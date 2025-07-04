<?php 
// Thay đổi URL của thẻ og:url do Rankmath  tự động tạo
add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
    if ( function_exists( 'isEpisode' ) && isEpisode() ) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    return $canonical;
});

add_filter( 'rank_math/opengraph/url', function( $url ) {
    return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});

// Thay đổi URL của thẻ og:url do Yoast SEO tự động tạo
add_filter('wpseo_opengraph_url', function($og_url) {
    return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});
add_filter('wpseo_canonical', function($canonical) {
        if ( ( is_singular( 'ophim' ) ) || ( function_exists( 'isEpisode' ) && isEpisode() ) ) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    return $canonical;
;
});

//prev and next
function custom_pagination_next_prev() {
    if (is_tax()) {
        global $wp_query;
        $paged = max(1, get_query_var('paged'));
        $max_page = $wp_query->max_num_pages;
        if ($paged < $max_page) echo '<link rel="next" href="' . esc_url(get_pagenum_link($paged + 1)) . '" />' . "\n";
        if ($paged > 1) echo '<link rel="prev" href="' . esc_url(get_pagenum_link($paged - 1)) . '" />' . "\n";
    }
}
add_action('wp_head', 'custom_pagination_next_prev', 2);