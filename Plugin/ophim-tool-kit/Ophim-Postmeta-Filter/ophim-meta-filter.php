<?php

/**
 * Plugin Name: Ophim Postmeta Filter (Module) - Final Version
 * Description: Lọc bài viết theo taxonomy + nhiều meta (AND). Sử dụng AJAX và hỗ trợ phân trang.
 * Version: 3.0.0
 * Author: Your Name & Gemini AI
 */

if (!defined('ABSPATH')) exit;

class Ophim_Postmeta_Filter_Module
{
  const CPT = 'ophim';
  const TAX = 'ophim_categories';
  const SLUG = 'ophim-meta-filter';
  const AJAX_ACTION = 'ophim_meta_filter_search';

  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_menu']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

    // Thêm Hook cho AJAX (Dành cho người dùng đăng nhập)
    add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'handle_ajax_search']);
  }

  // ----------------------------------------------------
  // WP SETUP & ASSETS
  // ----------------------------------------------------

  public function add_menu()
  {
    add_submenu_page(
      'edit.php?post_type=' . self::CPT,
      'Lọc & Quản lý Meta',
      'Lọc & Quản lý Meta',
      'manage_options',
      self::SLUG,
      [$this, 'render_page']
    );
  }

  public function enqueue_assets($hook)
  {
    $screen = get_current_screen();
    $is_me = ($screen && $screen->id === self::CPT . '_page_' . self::SLUG);
    if (!$is_me) return;

    $css_path = plugin_dir_path(__FILE__) . 'filter.css';
    $js_path = plugin_dir_path(__FILE__) . 'filter.js';

    // 1. CSS: Sử dụng filemtime để chống cache
    wp_enqueue_style(
      'ophim-filter-css',
      plugins_url('filter.css', __FILE__),
      [],
      // Dùng filemtime() để lấy thời gian sửa đổi cuối cùng làm version
      file_exists($css_path) ? filemtime($css_path) : '1.0'
    );

    // 2. JS: Sử dụng filemtime để chống cache
    wp_enqueue_script('jquery');
    wp_enqueue_script(
      'ophim-filter-js',
      plugins_url('filter.js', __FILE__),
      ['jquery'],
      file_exists($js_path) ? filemtime($js_path) : '1.0',
      true
    );

    // Data cho JS
    wp_localize_script('ophim-filter-js', 'OPhimFilter', [
      'ajaxurl' => admin_url('admin-ajax.php'),
      'ajaxAction' => self::AJAX_ACTION,
      'defaultLimit' => 200,
    ]);
  }

  // ----------------------------------------------------
  // DATA HELPERS
  // ----------------------------------------------------

  private function get_terms_all()
  {
    return get_terms(['taxonomy' => self::TAX, 'hide_empty' => false]);
  }

  private function get_all_meta_keys()
  {
    global $wpdb;
    $keys = $wpdb->get_col($wpdb->prepare(
      "SELECT DISTINCT pm.meta_key
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE p.post_type = %s AND pm.meta_key NOT LIKE '\_%'
             ORDER BY pm.meta_key ASC
             LIMIT 1000",
      self::CPT
    ));
    $keys = array_map('sanitize_text_field', (array)$keys);
    sort($keys, SORT_NATURAL | SORT_FLAG_CASE);
    return $keys;
  }

  private function normalize_pairs($keys, $vals)
  {
    $pairs = [];
    $len = max(count($keys), count($vals));
    for ($i = 0; $i < $len; $i++) {
      $k = isset($keys[$i]) ? trim((string)$keys[$i]) : '';
      $v = isset($vals[$i]) ? (string)$vals[$i] : '';
      if ($k !== '') $pairs[] = ['key' => $k, 'val' => $v];
    }
    return $pairs;
  }

  // ----------------------------------------------------
  // LÓGIC XỬ LÝ CHUNG (Hàm xử lý truy vấn)
  // ----------------------------------------------------

  public function handle_ajax_search()
  {
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => 'Bạn không có quyền thực hiện hành động này.']);
    }

    // Lấy và làm sạch dữ liệu từ $_POST (AJAX)
    $tax_term  = isset($_POST['tax_term']) ? sanitize_text_field($_POST['tax_term']) : '';
    $meta_keys = (isset($_POST['meta_key']) && is_array($_POST['meta_key'])) ? array_map('sanitize_text_field', $_POST['meta_key']) : [];
    $meta_vals = (isset($_POST['meta_val']) && is_array($_POST['meta_val'])) ? array_map('wp_unslash', $_POST['meta_val']) : [];
    $limit     = isset($_POST['limit']) ? max(1, min(1000, absint($_POST['limit']))) : 200; // Giới hạn tối đa 1000
    $paged     = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;

    // Bắt đầu buffer để lấy HTML
    ob_start();
    $this->execute_and_render_results($tax_term, $meta_keys, $meta_vals, $limit, $paged);
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
  }

  private function execute_and_render_results($tax_slug, $keys, $vals, $limit, $paged)
  {
    $tax_slug = trim((string)$tax_slug);
    $pairs = $this->normalize_pairs($keys, $vals);

    if (!$tax_slug && empty($pairs)) {
      echo '<p class="description" style="margin-top: 20px;">Chọn taxonomy hoặc thêm ít nhất 1 dòng META để lọc.</p>';
      return;
    }

    // 1. Xây dựng truy vấn
    $meta_query = ['relation' => 'AND'];
    global $wpdb;

    foreach ($pairs as $p) {
      if ($p['val'] === '') {
        $meta_query[] = ['key' => $p['key'], 'compare' => 'EXISTS'];
      } else {
        $safe_val = $wpdb->esc_like($p['val']);
        $meta_query[] = ['key' => $p['key'], 'value' => $safe_val, 'compare' => 'LIKE'];
      }
    }

    $tax_query = $tax_slug ? [[
      'taxonomy' => self::TAX,
      'field' => 'slug',
      'terms' => $tax_slug,
    ]] : [];

    // Lấy TỔNG SỐ bài viết trước (chỉ lấy ID, không giới hạn) để tính phân trang.
    $total_args = [
      'post_type'      => self::CPT,
      'posts_per_page' => -1, // Lấy hết
      'post_status'    => ['publish', 'draft', 'pending', 'future', 'private'],
      'tax_query'      => $tax_query,
      'meta_query'     => $meta_query,
      'fields'         => 'ids',
    ];

    $q_total = new WP_Query($total_args);
    $all_ids = $q_total->posts;

    // 2. Hậu kiểm (case-insensitive) trên TẤT CẢ IDs
    $matched_ids = [];
    if (!empty($all_ids)) {
      foreach ($all_ids as $pid) {
        $ok = true;
        foreach ($pairs as $p) {
          if ($p['val'] === '') continue;

          $vals = get_post_meta($pid, $p['key'], false);
          $found = false;
          $search_val = $p['val'];

          foreach ((array)$vals as $v) {
            $hay = is_scalar($v) ? (string)$v : wp_json_encode($v);
            if (stripos($hay, $search_val) !== false) {
              $found = true;
              break;
            }
          }
          if (!$found) {
            $ok = false;
            break;
          }
        }

        if ($ok) $matched_ids[] = $pid;
      }
    }

    $total_found = count($matched_ids);

    // 3. Thực hiện phân trang trên các ID đã hậu kiểm
    $total_pages = ceil($total_found / $limit);
    $paged = min($paged, $total_pages);

    $offset = ($paged - 1) * $limit;
    $paged_ids = array_slice($matched_ids, $offset, $limit);

    // 4. Hiển thị kết quả
    echo '<hr style="margin-top: 20px;"><h2>Kết quả Lọc</h2>';
    echo '<p class="ophim-muted">Tìm thấy <strong style="color: #007cba;">' . $total_found . '</strong> bài viết thỏa mãn điều kiện.';

    // Hiển thị Phân trang
    if ($total_pages > 1) {
      echo '<div class="tablenav-pages" style="float:right;">';
      $page_links = paginate_links([
        'base'      => '#%#%', // sẽ được JS thay thế
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $total_pages,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'type'      => 'array',
      ]);

      if (is_array($page_links)) {
        echo '<span class="pagination-links">';
        foreach ($page_links as $link) {
          // Thêm class để JS bắt sự kiện
          echo str_replace('page-numbers', 'page-numbers ophim-page-link', $link);
        }
        echo '</span>';
      }
      echo '</div>';
      echo '<br class="clear">';
    }

    echo '</p>';

    echo '<table class="widefat fixed striped"><thead><tr>';
    echo '<th width="80">ID</th><th>Tiêu đề</th>';
    foreach ($pairs as $p) {
      echo '<th>' . esc_html($p['key']) . '</th>';
    }
    echo '</tr></thead><tbody>';

    if ($total_found === 0) {
      echo '<tr><td colspan="' . (2 + count($pairs)) . '">Không có bài viết nào thỏa điều kiện.</td></tr>';
    } else {
      foreach ($paged_ids as $pid) {
        $title = get_the_title($pid);
        $edit  = get_edit_post_link($pid, '');
        echo '<tr>';
        echo '<td>#' . esc_html($pid) . '</td>';
        echo '<td><a href="' . esc_url($edit) . '">' . esc_html($title) . '</a></td>';
        foreach ($pairs as $p) {
          $vals = get_post_meta($pid, $p['key'], false);
          $preview = '<em>(Không có meta)</em>';
          if (!empty($vals)) {
            $first = is_scalar($vals[0]) ? (string)$vals[0] : wp_json_encode($vals[0]);
            $preview = function_exists('mb_substr') ? mb_substr($first, 0, 80) . (mb_strlen($first) > 80 ? '…' : '') : substr($first, 0, 80) . (strlen($first) > 80 ? '…' : '');
          }
          $cell_content = '<code class="result-meta-key">' . esc_html($preview) . '</code>';
          if ($p['val'] !== '') {
            $search_term = $p['val'];
            $cell_content = str_ireplace(
              esc_html($search_term),
              '<strong style="color:#d54e21;">' . esc_html($search_term) . '</strong>',
              $cell_content
            );
          }
          echo '<td>' . $cell_content . '</td>';
        }
        echo '</tr>';
      }
    }

    echo '</tbody></table>';
  }

  // ----------------------------------------------------
  // RENDER PAGE
  // ----------------------------------------------------

  public function render_page()
  {
    if (!current_user_can('manage_options')) return;

    $terms = $this->get_terms_all();
    $all_keys = $this->get_all_meta_keys();
    $datalist_id = 'ophim_meta_keys_list';

    // Giá trị mặc định cho form
    $tax_term = isset($_GET['tax_term']) ? sanitize_text_field($_GET['tax_term']) : '';
    $meta_keys = (isset($_GET['meta_key']) && is_array($_GET['meta_key'])) ? array_map('sanitize_text_field', $_GET['meta_key']) : [];
    $meta_vals = (isset($_GET['meta_val']) && is_array($_GET['meta_val'])) ? array_map('wp_unslash', $_GET['meta_val']) : [];
    $limit = isset($_GET['limit']) ? max(1, min(1000, absint($_GET['limit']))) : 200;

?>
    <div class="wrap">
      <h1 class="wp-heading-inline">Bộ Lọc Bài Viết Nâng Cao (AJAX & Phân Trang)</h1>
      <div class="ophim-form-wrap">
        <h2 class="ophim-title">Lọc theo Taxonomy & Nhiều Meta (AND)</h2>

        <form id="ophim-filter-form" class="ophim-form">
          <input type="hidden" name="post_type" value="<?php echo esc_attr(self::CPT); ?>">
          <input type="hidden" name="paged" value="1" id="paged-input">
          <div class="ophim-form-row">
            <label>Taxonomy (`<?php echo esc_html(self::TAX); ?>`)</label>
            <select name="tax_term" class="ophim-select">
              <option value="">— Chọn taxonomy —</option>
              <?php foreach ($terms as $t): ?>
                <option value="<?php echo esc_attr($t->slug); ?>" <?php selected($tax_term, $t->slug); ?>>
                  <?php echo esc_html($t->name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="ophim-form-row">
            <label>Số bài/trang (Tối đa 1000)</label>
            <input type="number" name="limit" min="1" max="1000" value="<?php echo esc_attr($limit); ?>" class="ophim-input-small">
          </div>

          <div class="ophim-form-row">
            <label>Điều kiện META (AND) — Để trống Value = chỉ cần key tồn tại</label>
            <div id="meta-rows">
              <?php
              $rows = max(1, count($meta_keys));
              for ($i = 0; $i < $rows; $i++):
                $k = isset($meta_keys[$i]) ? $meta_keys[$i] : '';
                $v = isset($meta_vals[$i]) ? $meta_vals[$i] : '';
              ?>
                <div class="meta-row">
                  <input type="text" name="meta_key[]" list="<?php echo esc_attr($datalist_id); ?>" value="<?php echo esc_attr($k); ?>" placeholder="Meta Key" class="ophim-combo">
                  <input type="text" name="meta_val[]" value="<?php echo esc_attr($v); ?>" placeholder="Giá trị cần tìm (LIKE)" class="ophim-input">
                  <button type="button" class="button remove-row">Xóa</button>
                </div>
              <?php endfor; ?>
            </div>
            <p><button type="button" id="add-row" class="button button-secondary">+ Thêm điều kiện</button></p>
          </div>

          <datalist id="<?php echo esc_attr($datalist_id); ?>">
            <?php foreach ($all_keys as $mk): ?>
              <option value="<?php echo esc_attr($mk); ?>"></option>
            <?php endforeach; ?>
          </datalist>

          <?php submit_button('Lọc bài viết', 'primary', 'submit', false); ?>
          <span class="spinner"></span>
        </form>
      </div>

      <div class="ophim-results" id="ophim-results-container">
      </div>
    </div>
<?php
  }
}

new Ophim_Postmeta_Filter_Module();
