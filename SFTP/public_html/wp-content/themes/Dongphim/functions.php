<?php

define('THEME_URL', get_stylesheet_directory());
define('CORE', THEME_URL . '/core');
define('WIDGET', THEME_URL . '/widget');
define('SIDEBARTEMPLADE', THEME_URL . '/templates/rightbar');
define('THEMETEMPLADE', THEME_URL . '/templates');


require_once(CORE . '/init.php');
require_once(THEME_URL . '/inc/demo.php');
require_once(THEME_URL . '/inc/register_sidebar.php');
require_once(THEME_URL . '/inc/ajax.php');
require_once(THEME_URL . '/inc/front.php');
require_once(WIDGET . '/wg_ophim_categories.php');
require(WIDGET . '/wg_ophim_tabbed_categories.php');
require_once(WIDGET . '/wg_ophim_sidebar.php');
require_once(WIDGET . '/wg_ophim_slide_poster.php');
require_once(WIDGET . '/wg_ophim_footer.php');

// Thay đổi URL của thẻ canonical do Yoast SEO tự động tạo
add_filter('wpseo_canonical', function($canonical) {
	// Thay 'https://example.com/your-custom-url' bằng URL mong muốn
	return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});

// Thay đổi URL của thẻ og:url do Yoast SEO tự động tạo
add_filter('wpseo_opengraph_url', function($og_url) {
	// Thay 'https://example.com/your-custom-url' bằng URL mong muốn
	return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});
// Thay đổi tiêu đề và meta description do Yoast SEO tự động thêm vào
add_action('wp_head', function() {
	if (strpos(home_url(add_query_arg(null, null)), '/tap-') !== false && !is_404()) {
		ob_start(function($buffer) {
			// Tạo các biến trước
			$title = get_the_title();
			$episode = episodeName();
			$lang = op_get_lang();
			$original_title = op_get_original_title();
			$excerpt = mb_substr(get_the_excerpt(), 0, 130); // Giới hạn excerpt 130 ký tự

			// Sử dụng sprintf để tạo tiêu đề và mô tả meta
			$new_title = sprintf('Xem Phim %s - Tập %s %s - Dongphim - %s', $title, $episode, $lang, $original_title);
			$meta_description = sprintf('Xem %s - Tập %s. %s', $title, $episode, $excerpt);

			// Thay đổi <title>
			$buffer = preg_replace('/<title>.*<\/title>/', sprintf('<title>%s</title>', $new_title), $buffer);

			// Thay đổi <meta name="description">
			$buffer = preg_replace(
				'/<meta name="description" content=".*?"\s*\/?>/',
				sprintf('<meta name="description" content="%s" />', esc_attr($meta_description)),
				$buffer
			);

			return $buffer;
		});
	}
}, 1);

// Thêm thẻ meta keywords vào Yoast SEO với các tag từ ophim_tags
add_action('wpseo_head', function() {
	$keywords = '';
	$title = get_the_title(get_the_ID());
	$ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
	$episode = function_exists('episodeName') ? episodeName() : '';
	$domain ="Dongphim © Động Phim Hay";
	if(isEpisode()){$keywords = "{$title} - Tập {$episode} {$domain}, {$ori_title} - Tập {$episode} {$domain}";}
	elseif (is_singular('ophim')) {      
		$keywords = "{$title}  {$domain}, {$ori_title}  {$domain}";
	} elseif (is_front_page()) {
		$keywords = "Dongphim, Động Phim, Động Phim Hay 2025, Phim Mới, Phim HD, Xem Phim Online";
	} elseif (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) { 
		$keywords = single_tag_title('', false) . "  {$domain}";
	}elseif (is_archive()) {
		$keywords = "Kho phim mới Dongphim";
	} 

	if ($keywords) {
		echo "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
	}

});
remove_action( 'template_redirect', 'wp_old_slug_redirect');
remove_action( 'post_updated', 'wp_check_for_changed_slugs', 12, 3 );
remove_filter('template_redirect', 'redirect_canonical');
