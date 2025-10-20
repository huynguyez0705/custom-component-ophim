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
    __DIR__ . '/Ophim-Postmeta-Filter/ophim-meta-filter.php',
];

foreach ($plugin_files as $file) {
    if (file_exists($file)) {
        include_once $file;
    } else {
        error_log('[Ophim All Plugins] Không tìm thấy file: ' . $file);
    }
}
