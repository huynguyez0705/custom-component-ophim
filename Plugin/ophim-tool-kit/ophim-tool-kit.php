<?php
/*
Plugin Name: Ophim Tool Kit
Version: 3.5
Description: Bộ công cụ quản trị OPhim mạnh mẽ
*/

if (!defined('ABSPATH')) exit;

// 1. Tạo menu cha + trang chính đẹp
add_action('admin_menu', 'ophim_toolkit_main_menu');
function ophim_toolkit_main_menu()
{
  add_menu_page(
    'Ophim Tool Kit',           // Page title
    'Ophim Tool Kit',           // Menu title
    'manage_options',
    'ophim-toolkit',            // Slug → sẽ thành /wp-admin/ophim-toolkit
    'ophim_toolkit_dashboard', // ← Hàm hiển thị nội dung trang chính
    'dashicons-admin-tools',
    25
  );
}

// 2. Nội dung trang chính (dashboard tổng hợp)
function ophim_toolkit_dashboard()
{
?>
  <div class="wrap">
    <h1>Ophim Tool Kit <small style="font-size:14px;color:#666">v3.5</small></h1>
    <div style="background:#fff;padding:30px;border-left:4px solid #2271b1;margin:30px 0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1)">
      <h2>Chào mừng bạn đến với Ophim Tool Kit!</h2>
      <p>Đây là bộ công cụ mạnh mẽ giúp bạn quản lý phim OPhim nhanh hơn bao giờ hết.</p>

      <hr>
      <h3>Các công cụ hiện có:</h3>
      <ul style="font-size:16px;line-height:2">
        <li>Quản Lý Series (tạo series dài 50+ phần chỉ trong 10 giây)</li>
        <li>Quản Lý Phim Hot & Nổi Bật</li>
        <li>Quản Lý Postmeta</li>
        <li>Quản Lý Focus Keyword SEO</li>
        <li>Quản Lý Danh Mục</li>
      </ul>

      <p><a href="<?= admin_url('admin.php?page=ophim-series-pro') ?>" class="button button-primary button-large">Vào Quản Lý Series ngay</a></p>
    </div>
  </div>
<?php
}

// Include các plugin con (giữ nguyên)
$sub_plugins = [
  'Ophim-Series-Manager/ophim-series-manager.php',
  'Ophim-Featured-Manager/featured-top_view.php',
  'Ophim-Postmeta-Manager/ophim-update-postmeta.php',
  'Ophim-Focus-Keyword/ophim-focus-keyword.php',
  'Ophim-Categories-Manager/ophim-categories-plugin.php',
];

foreach ($sub_plugins as $file) {
  $path = __DIR__ . '/' . $file;
  if (file_exists($path)) {
    require_once $path;
  }
}
