<?php
/*
Plugin Name: Ophim Tool Kit
Version: 1.0
Author: Dương Quá
*/

// Đảm bảo chạy trong WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Include các plugin con
$plugin_files = [
    __DIR__ . '/Ophim-Postmeta-Manager/ophim-update-postmeta.php',
    __DIR__ . '/Ophim-Categories-Manager/ophim-categories-plugin.php',
    __DIR__ . '/Ophim-Featured-Manager/featured-top_view.php',
    __DIR__ . '/Ophim-Focus-Keyword/ophim-focus-keyword.php',
    __DIR__ . '/Ophim-Custom-Meta/ophim-custom-meta.php',
];

foreach ($plugin_files as $file) {
    if (file_exists($file)) {
        include_once $file;
    } else {
        error_log('[Ophim All Plugins] Không tìm thấy file: ' . $file);
    }
}

// Đăng ký CSS cho plugin Ophim-Focus-Keyword (vì file CSS không tự động được nạp)
add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'ophim_page_manage-seo-keyword') !== false) {
        wp_enqueue_style(
            'seo-keyword-admin-style',
            plugin_dir_url(__FILE__) . 'Ophim-Focus-Keyword/seo-keyword.css',
            [],
            '1.1'
        );
    }
});
?>