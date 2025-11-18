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

// Thêm meta keywords
add_action('wp_head', function () {
  if (is_singular('ophim')) {
    $title = get_the_title(get_the_ID());
    $ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
    printf('<meta name="keywords" content="%s Motphimchill, %s Motphimchill"/>', esc_attr($title), esc_attr($ori_title));
    echo "\n";
  } elseif (is_front_page()) {
    echo '<meta name="keywords" content="motphim, motphimchill, motphim tv, mọt phim, motphimtv, motphim chill"/>' . "\n";
  } elseif (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) {
    $tag_title = single_tag_title('', false);
    printf('<meta name="keywords" content="%s Motphimchill"/>', esc_attr($tag_title));
    echo "\n";
  }
}, 2);


add_action('wp_head', function () {
  if (strpos(home_url(add_query_arg(null, null)), '/tap-') !== false && !is_404()) {
    ob_start(function ($buffer) {
      global $post;
      if (!$post) return $buffer;

      // Tạo biến
      $title          = get_the_title();
      $episode        = function_exists('episodeName') ? episodeName() : '';
      $lang           = function_exists('op_get_lang') ? op_get_lang() : '';
      $quality        = function_exists('op_get_quality') ? op_get_quality(get_the_ID()) : '';
      $original_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
      $excerpt        = wp_trim_words(strip_tags(get_the_content($post->ID)),20);
      $img_src        = function_exists('op_get_poster_url') ? op_get_poster_url($post->ID) : '';

      // Tiêu đề & mô tả mới
      $new_title        = sprintf('Xem %s %s - Tập %s - Motphimchill - %s', $title, $lang, $episode, $original_title);
      $meta_description = sprintf('Xem %s - Tập %s - Motphimchill. %s', $title, $episode, $excerpt);

      // Các pattern cần thay thế
      $replacements = [
        '/<title>.*<\/title>/i' => sprintf('<title>%s</title>', esc_attr($new_title)),
        '/<meta name="description" content=".*?"\s*\/?>/i' => sprintf('<meta name="description" content="%s" />', esc_attr($meta_description)),
        '/<meta property="og:title" content=".*?"\s*\/?>/i' => sprintf('<meta property="og:title" content="%s" />', esc_attr($new_title)),
        '/<meta property="og:description" content=".*?"\s*\/?>/i' => sprintf('<meta property="og:description" content="%s" />', esc_attr($meta_description)),
        '/<meta name="twitter:title" content=".*?"\s*\/?>/i' => sprintf('<meta name="twitter:title" content="%s" />', esc_attr($new_title)),
        '/<meta name="twitter:description" content=".*?"\s*\/?>/i' => sprintf('<meta name="twitter:description" content="%s" />', esc_attr($meta_description)),
        '/<meta name="twitter:image" content=".*?"\s*\/?>/i' =>sprintf('<meta name="twitter:image" content="%s" />', esc_url($img_src)),
      ];

      // Thay thế theo mapping
      foreach ($replacements as $pattern => $replacement) {
        $buffer = preg_replace($pattern, $replacement, $buffer);
      }

      return $buffer;
    });
  }
}, 1);


// add_filter('wpseo_opengraph_url', function ($og_url) {
// return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// });
remove_action('template_redirect', 'wp_old_slug_redirect');
remove_action('post_updated', 'wp_check_for_changed_slugs', 12, 3);
remove_filter('template_redirect', 'redirect_canonical');


