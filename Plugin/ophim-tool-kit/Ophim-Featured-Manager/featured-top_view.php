<?php

/**
 * File: featured-top_view.php
 * Dùng trong Ophim Tool Kit – Quản Lý Phim Hot + Nổi Bật
 */

if (!defined('ABSPATH')) exit;

// Thêm submenu vào Ophim Tool Kit
add_action('admin_menu', 'ophim_featured_add_menu');
function ophim_featured_add_menu()
{
  add_submenu_page(
    'ophim-toolkit',
    'Quản Lý Phim Hot',
    'Quản Lý Phim Hot',
    'manage_options',
    'top-viewed-movies',
    'top_viewed_movies_display_page'
  );
}

// Hàm hiển thị trang chính
function top_viewed_movies_display_page()
{
  // Lấy giá trị từ URL (sắp xếp)
  $featured_orderby = sanitize_text_field($_GET['featured_orderby'] ?? 'views');
  $featured_order   = sanitize_text_field($_GET['featured_order'] ?? 'desc');
  $top_orderby      = sanitize_text_field($_GET['top_orderby'] ?? 'views');
  $top_order        = sanitize_text_field($_GET['top_order'] ?? 'desc');
  $posts_per_page   = isset($_POST['posts_per_page']) ? max(1, (int)$_POST['posts_per_page']) : 50;
  $paged            = max(1, get_query_var('paged') ?: (int)($_GET['paged'] ?? 1));
?>
  <div class="wrap">
    <h1>Quản Lý Phim Hot & Nổi Bật</h1>

    <!-- PHIM NỔI BẬT -->
    <h2>Phim Nổi Bật (Đã bật)</h2>
    <table class="widefat fixed">
      <thead>
        <tr>
          <th>#</th>
          <th><a href="<?= add_query_arg(['featured_orderby' => 'title', 'featured_order' => ($featured_orderby === 'title' && $featured_order === 'asc' ? 'desc' : 'asc')]) ?>">Tiêu đề</a></th>
          <th><a href="<?= add_query_arg(['featured_orderby' => 'views', 'featured_order' => ($featured_orderby === 'views' && $featured_order === 'asc' ? 'desc' : 'asc')]) ?>">Lượt xem</a></th>
          <th>Danh mục</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody><?= ophim_display_featured_movies($featured_orderby, $featured_order) ?></tbody>
    </table>

    <hr style="margin:40px 0">

    <!-- TOP LƯỢT XEM -->
    <h2>Top Phim Có Lượt Xem Cao Nhất</h2>
    <div class="posts_per_page" style="margin:15px 0;">
      <label>Số phim hiển thị: </label>
      <input type="number" id="posts_per_page_input" value="<?= esc_attr($posts_per_page) ?>" min="1" style="width:80px;">
      <button id="update_movies_button" class="button button-primary">Cập nhật</button>
    </div>

    <table class="widefat fixed">
      <thead>
        <tr>
          <th>Xếp hạng</th>
          <th><a href="<?= add_query_arg(['top_orderby' => 'title', 'top_order' => ($top_orderby === 'title' && $top_order === 'asc' ? 'desc' : 'asc')]) ?>">Tiêu đề</a></th>
          <th><a href="<?= add_query_arg(['top_orderby' => 'views', 'top_order' => ($top_orderby === 'views' && $top_order === 'asc' ? 'desc' : 'asc')]) ?>">Lượt xem</a></th>
          <th>Danh mục</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody id="top-viewed-movies">
        <?= ophim_top_viewed_movies_list($posts_per_page, $top_orderby, $top_order, $paged) ?>
      </tbody>
    </table>
  </div>

  <script>
    jQuery(function($) {
      // Cập nhật số lượng phim
      $('#update_movies_button').on('click', function() {
        let num = $('#posts_per_page_input').val();
        $.post(ajaxurl, {
          action: 'ophim_update_top_movies',
          posts_per_page: num,
          top_orderby: '<?= esc_js($top_orderby) ?>',
          top_order: '<?= esc_js($top_order) ?>'
        }, function(html) {
          $('#top-viewed-movies').html(html);
        });
      });

      // Thêm/Xóa nổi bật
      $(document).on('click', '.ophim-toggle-featured', function() {
        let btn = $(this);
        let id = btn.data('id');
        let add = btn.hasClass('add');
        $.post(ajaxurl, {
          action: 'ophim_toggle_featured',
          postid: id,
          status: add ? 1 : 0
        }, function() {
          location.reload();
        });
      });
    });
  </script>
<?php
}

// Danh sách phim nổi bật
function ophim_display_featured_movies($orderby = 'views', $order = 'desc')
{
  $args = [
    'post_type'      => 'ophim',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'ophim_featured_post', 'value' => 1]],
    'meta_key'       => 'ophim_view',
    'orderby'        => $orderby === 'title' ? 'title' : 'meta_value_num',
    'order'          => strtoupper($order)
  ];
  $q = new WP_Query($args);
  $html = '';
  if ($q->have_posts()) {
    $i = 1;
    while ($q->have_posts()) {
      $q->the_post();
      $views = (int)get_post_meta(get_the_ID(), 'ophim_view', true);
      $cats = wp_get_post_terms(get_the_ID(), 'ophim_categories', ['fields' => 'names']);
      $cat_str = $cats ? implode(', ', $cats) : '-';
      $html .= "<tr>
                <td>$i</td>
                <td><a href='" . get_edit_post_link() . "'>" . get_the_title() . "</a></td>
                <td>$views</td>
                <td>$cat_str</td>
                <td><button class='button ophim-toggle-featured remove' data-id='" . get_the_ID() . "'>Xóa Nổi Bật</button></td>
            </tr>";
      $i++;
    }
  } else {
    $html .= '<tr><td colspan="5">Chưa có phim nào được bật nổi bật</td></tr>';
  }
  wp_reset_postdata();
  return $html;
}

// Top lượt xem
function ophim_top_viewed_movies_list($num = 50, $orderby = 'views', $order = 'desc', $paged = 1)
{
  $args = [
    'post_type'      => 'ophim',
    'posts_per_page' => $num,
    'paged'          => $paged,
    'meta_key'       => 'ophim_view',
    'orderby'        => $orderby === 'title' ? 'title' : 'meta_value_num',
    'order'          => strtoupper($order)
  ];
  $q = new WP_Query($args);
  $html = '';
  if ($q->have_posts()) {
    $rank = ($paged - 1) * $num + 1;
    while ($q->have_posts()) {
      $q->the_post();
      $views = (int)get_post_meta(get_the_ID(), 'ophim_view', true);
      $featured = get_post_meta(get_the_ID(), 'ophim_featured_post', true);
      $cats = wp_get_post_terms(get_the_ID(), 'ophim_categories', ['fields' => 'names']);
      $cat_str = $cats ? implode(', ', $cats) : '-';
      $btn = $featured
        ? "<button class='button ophim-toggle-featured remove' data-id='" . get_the_ID() . "'>Xóa Nổi Bật</button>"
        : "<button class='button button-primary ophim-toggle-featured add' data-id='" . get_the_ID() . "'>Thêm Nổi Bật</button>";

      $html .= "<tr>
                <td>$rank</td>
                <td><a href='" . get_edit_post_link() . "'>" . get_the_title() . "</a></td>
                <td>$views</td>
                <td>$cat_str</td>
                <td>$btn</td>
            </tr>";
      $rank++;
    }
    // Phân trang
    $big = 999999;
    $html .= '<tr><td colspan="5" style="text-align:center">';
    $html .= paginate_links([
      'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
      'format'  => '?paged=%#%',
      'current' => $paged,
      'total'   => $q->max_num_pages,
      'prev_text' => 'Trước',
      'next_text' => 'Sau'
    ]);
    $html .= '</td></tr>';
  } else {
    $html .= '<tr><td colspan="5">Không có phim nào</td></tr>';
  }
  wp_reset_postdata();
  return $html;
}

// AJAX: Cập nhật danh sách top
add_action('wp_ajax_ophim_update_top_movies', function () {
  $num = max(1, (int)($_POST['posts_per_page'] ?? 50));
  $orderby = sanitize_text_field($_POST['top_orderby'] ?? 'views');
  $order   = sanitize_text_field($_POST['top_order'] ?? 'desc');
  echo ophim_top_viewed_movies_list($num, $orderby, $order);
  wp_die();
});

// AJAX: Bật/Tắt nổi bật
add_action('wp_ajax_ophim_toggle_featured', function () {
  $id = (int)($_POST['postid'] ?? 0);
  $status = (int)($_POST['status'] ?? 0);
  if ($id && get_post($id)) {
    update_post_meta($id, 'ophim_featured_post', $status);
    wp_send_json_success();
  }
  wp_die();
});

// CSS đẹp
add_action('admin_head', function () {
  if (!isset($_GET['page']) || $_GET['page'] !== 'top-viewed-movies') return;
  echo '<style>
        .ophim-toggle-featured.add{background:#2271b1;color:#fff}
        .ophim-toggle-featured.remove{background:#d63638;color:#fff}
        .posts_per_page input{width:80px;padding:5px}
        .pagination-links{margin:20px 0;text-align:center}
        .page-numbers{padding:8px 12px;margin:0 4px;border:1px solid #ccc;border-radius:4px}
        .page-numbers.current{background:#2271b1;color:#fff;border-color:#2271b1}
    </style>';
});
