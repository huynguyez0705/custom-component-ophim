function disable_redirect_guess_404_permalink( $redirect_url ) {
    if ( is_404() ) {
        return false;
    }
    return $redirect_url;
}
add_filter( 'redirect_canonical', 'disable_redirect_guess_404_permalink' );
add_filter( 'wpseo_enable_404_redirect', '__return_false' );
add_filter( 'rank_math/redirection/enable_auto_redirect', '__return_false' );
add_filter( 'rank_math/redirection/do_redirect', function( $do_redirect, $request ) {
    if ( is_404() ) {
        return false;
    }
    return $do_redirect;
}, 10, 2 );
