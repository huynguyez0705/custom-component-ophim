<?php

// Filter Title
add_filter('rank_math/frontend/title', function($title) {
    if (isEpisode()) {
        $year = op_get_year(get_the_ID());
        $originalTitle = op_get_original_title();
        return "<strong>".get_the_title(get_the_ID())."</strong> - {(is_string($year)) ? $year : ''} - {(is_string($originalTitle)) ? $originalTitle : ''} | HHKUNGFU";
    }
    
    return $title;
}, 10, 1);

// Filter Description
add_filter('rank_math/frontend/description', function($des) {
    if (function_exists('isEpisode')) {
        $post_title = get_the_title(get_the_ID());
        $original_excerpt = op_get_excerpt();
        return "Xem phim {$post_title} | {$original_excerpt}";
    }
    
    return $des;
}, 10, 1);

// Filter Title using Yoast SEO
add_filter('wpseo_title', function($title) {
    if (isEpisode()) {
        $post_title = get_the_title(get_the_ID());
        $year = op_get_year(get_the_ID());
        $originalTitle = op_get_original_title();
        
        return "Xem phim {$post_title} ({$year}) - {$originalTitle} | HHKUNGFU";
    }
    
    return $title;
}, 10, 1);
?>