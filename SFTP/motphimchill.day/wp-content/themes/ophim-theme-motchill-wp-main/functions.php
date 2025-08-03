<?php

define('THEME_URL', get_stylesheet_directory());
define('CORE', THEME_URL . '/core');
define('WIDGET', THEME_URL . '/widget');
define('SIDEBARTEMPLADE', THEME_URL . '/templates/rightbar');
define('THEMETEMPLADE', THEME_URL . '/templates');


// Include các file cần thiết
require_once(CORE . '/init.php');
require_once(THEME_URL . '/inc/demo.php');
require_once(THEME_URL . '/inc/register_sidebar.php');
require_once(THEME_URL . '/inc/ajax.php');
require_once(THEME_URL . '/inc/front.php');
require_once(WIDGET . '/wg_ophim_categories.php');
require_once(WIDGET . '/wg_ophim_sidebar.php');
require_once(WIDGET . '/wg_ophim_slide_poster.php');
require_once(WIDGET . '/wg_ophim_footer.php');

// Thêm meta keywords
add_action('wp_head', function () {
    if (is_singular('ophim')) {
        $title = get_the_title(get_the_ID());
        $ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
        printf('<meta name="keywords" content="%s Motphimchill, %s Motphimchill"/>', esc_attr($title), esc_attr($ori_title));
        echo "\n";
    } elseif (is_front_page()) {
        echo '<meta name="keywords" content="motphim, motphimchill, motphim tv, motphim.chill, mọt phim, motphimtv"/>' . "\n";
    } elseif (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) {
        $tag_title = single_tag_title('', false);
        printf('<meta name="keywords" content="%s motphimchill"/>', esc_attr($tag_title));
        echo "\n";
    }
}, 2);

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
        $new_title = sprintf('Xem %s - %s %s - Tập %s - MotPhimChill |  Phim %s', $title, $lang, $quality, $episode, $original_title );
        $meta_description = sprintf('Xem %s - Tập %s - Motphimchill. %s', $title, $episode, $excerpt);

        // Thay đổi <title>
        $buffer = preg_replace('/<title>.*<\/title>/', sprintf('<title>%s</title>', $new_title), $buffer);

        // Thay đổi <meta name="description">
        $buffer = preg_replace('/<meta name="description" content=".*?"\s*\/?>/',sprintf('<meta name="description" content="%s" />', esc_attr($meta_description)),$buffer);

        return $buffer;
        }); 
    }
}, 1);
add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
    if ( function_exists( 'isEpisode' ) && isEpisode() ) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    return $canonical;
});
