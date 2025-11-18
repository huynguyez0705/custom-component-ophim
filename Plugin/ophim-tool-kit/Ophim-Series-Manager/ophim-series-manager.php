<?php

/**
 * Plugin Name: OPhim Series Manager Pro
 * Version: 5.2
 */


if (!defined('ABSPATH')) exit;
define('OSM_PATH', plugin_dir_path(__FILE__));
define('OSM_URL', plugin_dir_url(__FILE__));

class OPhim_Series_Manager
{
  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_submenu']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    add_action('wp_ajax_ophim_search_film_pro', [$this, 'ajax_search']);
    add_action('wp_ajax_ophim_save_series_group', [$this, 'ajax_save']);
  }

  public function add_submenu()
  {
    add_submenu_page(
      'ophim-toolkit',
      'Quản Lý Series',
      'Quản Lý Series',
      'edit_others_posts',
      'ophim-series-pro',
      [$this, 'render_page']
    );
  }

  public function enqueue_assets($hook)
  {
    // CHẤP NHẬN CẢ 2 TRƯỜNG HỢP HOOK → CHẠY 100%
    if (strpos($hook, 'ophim-series-pro') === false) return;

    // 1. Enqueue CSS từ file assets/style.css
    wp_enqueue_style('osm-admin-style', OSM_URL . 'assets/style.css', [], '7');

    // 2. Enqueue CSS Font Awesome (bên ngoài)
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], '6.5.0');

    // 3. Enqueue Script
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('osm-admin', OSM_URL . 'assets/ophim-series.js', ['jquery', 'jquery-ui-sortable'], '7', true);
    wp_localize_script('osm-admin', 'osm_ajax', ['ajaxurl' => admin_url('admin-ajax.php')]);
  }

  public function render_page()
  {
    if (!current_user_can('edit_others_posts')) wp_die('Không có quyền.');
    echo '<style>' . file_get_contents(OSM_PATH . 'assets/style.css') . '</style>';
    require_once OSM_PATH . 'templates/admin-page.php';
  }

  public function ajax_search()
  {
    $q = sanitize_text_field($_POST['q'] ?? '');
    $args = [
      'post_type'      => 'ophim',
      'posts_per_page' => 50,
      'post_status'    => 'any'
    ];
    if (is_numeric($q)) $args['p'] = intval($q);
    else $args['s'] = $q;

    $posts = get_posts($args);
    $out = [];
    foreach ($posts as $p) {
      $series_ids = get_post_meta($p->ID, 'ophim_series_ids', true);
      $in_series = !empty($series_ids);

      $out[] = [
        'id'        => $p->ID,
        'title'     => $p->post_title,
        'in_series' => $in_series
      ];
    }
    wp_send_json_success($out);
  }

  public function ajax_save()
  {
    $ids = array_filter(array_map('intval', explode(',', $_POST['ids'] ?? '')));
    if (count($ids) < 2) wp_send_json_error();
    $str = implode(',', $ids);
    foreach ($ids as $id) update_post_meta($id, 'ophim_series_ids', $str);
    wp_send_json_success();
  }
}
new OPhim_Series_Manager();

if (!function_exists('op_get_render_series')) {
  function op_get_render_series()
  {
    /** @var WP_Post|null $post */
    global $post;
    if (!$post instanceof WP_Post || $post->post_type !== 'ophim') {
      return '';
    }

    if (!$post || $post->post_type !== 'ophim') return '';
    $ids_str = get_post_meta($post->ID, 'ophim_series_ids', true);
    if (empty($ids_str)) return '';
    $ids = array_filter(array_map('intval', explode(',', $ids_str)));
    $series = get_posts(['post_type' => 'ophim', 'post__in' => $ids, 'posts_per_page' => -1, 'orderby' => 'post__in', 'post_status' => 'publish']);
    if (count($series) <= 1) return '';

    preg_match('/Phần\s*(\d+)/i', $post->post_title, $m);
    $current_part = $m[1] ?? '1';

    ob_start(); ?>
    <div class="season-dropdown dropdown">
      <div class="line-center dropdown" data-bs-toggle="dropdown" style="cursor:pointer;">
        <i class="fa-solid fa-bars-staggered text-primary"></i>
        Phần <?= esc_html($current_part) ?> <i class="fa-solid fa-caret-down"></i>
      </div>
      <div class="dropdown-menu v-dropdown-menu">
        <div class="dropdown-blank w-100"><span>Danh sách phần</span></div>
        <div class="droplist">
          <?php foreach ($series as $s):
            preg_match('/Phần\s*(\d+)/i', $s->post_title, $m2);
            $part = $m2[1] ?? '1';
            $active = ($s->ID === $post->ID) ? ' active' : '';
          ?>
            <a href="<?= get_permalink($s) ?>" class="dropdown-item<?= $active ?>">
              <strong>Phần <?= esc_html($part) ?></strong>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
<?php
    return ob_get_clean();
  }
}
