// Thay đổi URL của thẻ og:url do Rankmath  tự động tạo
add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
	    return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});
add_filter( 'rank_math/opengraph/url', function( $url ) {
    return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});

// Thay đổi URL của thẻ og:url do Yoast SEO tự động tạo
add_filter('wpseo_opengraph_url', function($og_url) {
     return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});
add_filter('wpseo_canonical', function($canonical) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
});
