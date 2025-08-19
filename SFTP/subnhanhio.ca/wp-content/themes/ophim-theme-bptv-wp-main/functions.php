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
require_once(WIDGET . '/wg_ophim_sidebar.php');
require_once(WIDGET . '/wg_ophim_slide_poster.php');
require_once(WIDGET . '/wg_ophim_footer.php');



add_action('wp_head', function() {
if (strpos(home_url(add_query_arg(null, null)), '/tap-') !== false && !is_404()) {
    ob_start(function($buffer) {
        // Tạo các biến trước
        $title = get_the_title();
        $episode = episodeName();
        $lang = op_get_lang();
		$quality = op_get_quality(get_the_ID());
        $original_title = op_get_original_title();
        $excerpt = mb_substr(get_the_excerpt(), 0, 130); // Giới hạn excerpt 130 ký tự

        // Sử dụng sprintf để tạo tiêu đề và mô tả meta
        $new_title = sprintf('Xem Phim %s - Tập %s - %s %s - Subnhanh', $title, $episode, $quality, $lang);
        $meta_description = sprintf('Xem %s Tập %s Tại Subnhanh. %s', $title, $episode, $excerpt);

        // Thay đổi <title>
        $buffer = preg_replace('/<title>.*<\/title>/', sprintf('<title>%s</title>', $new_title), $buffer);

        // Thay đổi <meta name="description">
        $buffer = preg_replace('/<meta name="description" content=".*?"\s*\/?>/',sprintf('<meta name="description" content="%s" />', esc_attr($meta_description)),$buffer);

        return $buffer;
        }); 
    }
}, 1);


add_filter( 'wpseo_canonical', function( $canonical ) {
    if (function_exists( 'isEpisode' ) && isEpisode()) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    return $canonical;
});
// keywords
add_action("wp_head", function () {
	$title = get_the_title(get_the_ID());
	$ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
	$episode = function_exists('episodeName') ? episodeName() : '';
	if (isEpisode()) {
		echo <<<EOT
		<meta name="keywords" content="{$title} - Tập {$episode} SubNhanh, {$ori_title} - Tập {$episode} SubNhanh, Phim {$title} Tập {$episode} Vietsub"/>
		EOT;
		echo "\n";
	} else if (is_singular('ophim')) {
		echo <<<EOT
		<meta name="keywords" content="Phim {$title} SubNhanh, Phim {$ori_title} SubNhanh, Phim {$title} Vietsub"/>
		EOT;
		echo "\n";
	} else if (is_front_page()) {
		echo '<meta name="keywords" content="SubNhanh, SubNhanh id, SubNhanh bio, SubNhanh vip, Xem phim online, phim mới"/>' . "\n";
	} else if (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) {
		$tag_title = single_tag_title('', false);
		$prefix = '';
		if (is_tax('ophim_directors')) {
			$prefix = 'Đạo diễn ';
		} elseif (is_tax('ophim_actors')) {
			$prefix = 'Diễn viên ';
		} elseif (is_tax('ophim_regions') || is_tax('ophim_genres')) {
			$prefix = 'Phim ';
		}
		$title_taxonomy = $prefix . $tag_title;
		// Nếu là ophim_categories thì không thêm prefix
		echo <<<EOT
		<meta name="keywords" content="{$title_taxonomy} SubNhanh,"/>
		EOT;
		echo "\n";
	} elseif (is_search()) {
		$keywords = get_search_query();
		echo <<<EOT
		<meta name="keywords" content="{$keywords} SubNhanh"/>
		EOT;
		echo "\n";
	}
}, 2);
add_filter('wpseo_enable_404_redirect', '__return_false');
remove_action('template_redirect', 'wp_old_slug_redirect');
remove_action('post_updated', 'wp_check_for_changed_slugs', 12, 3);
remove_filter('template_redirect', 'redirect_canonical');
