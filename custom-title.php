<?php

// rankmath

add_filter('rank_math/frontend/title', function($title) {
    if (isEpisode()) {
        return sprintf(
            'Xem phim %s (%s)- %s %s - Tập %s | HHKUNGFU',
            get_the_title(get_the_ID()),
	        op_get_year(get_the_ID()),
            op_get_lang(get_the_ID()),
            op_get_year(get_the_ID()),
            op_get_quality(get_the_ID()),
            episodeName()
        );
    }
    return $title;
}, 10, 1);
add_filter( 'rank_math/frontend/description', function( $des ) {
    if ( function_exists('isEpisode') && isEpisode() ) {
        $post_title = get_the_title(get_the_ID());
        $episode = function_exists('episodeName') ? episodeName() : '';
        $ex = get_the_excerpt();
        $new_des = "Xem {$post_title} Tập {$episode}. {$ex} ";
        return $new_des;
    }
    return $des;
}, 10, 1 );



// Yoast seo

add_filter('wpseo_title', function($title) {
    if (isEpisode()) {
        $post_title = get_the_title(get_the_ID());
        $year = op_get_year(get_the_ID());
        $lang = op_get_lang(get_the_ID());
        $quality = op_get_quality(get_the_ID());
        $episode = episodeName();
	$ori_title = op_get_original_title();

        // Tạo tiêu đề bằng nội suy chuỗi
        $custom_title = "Xem phim {$post_title} ({$year}) - {$lang} {$quality} - Tập {$episode} | HHKUNGFU";

        return $custom_title;
    }
    return $title;
}, 10, 1);

// keywords
add_action("wp_head", function() {
    $title = get_the_title(get_the_ID());
    $ori_title = op_get_original_title();
    echo <<<EOT
<meta name="keywords" content="{$title}, {$ori_title}"/>
EOT;
});


// op_get_year
function op_get_year($end ='')
{
    $html = "";
    $years = get_the_terms(get_the_ID(), "ophim_years");
    if (is_array($years)) {
        foreach ($years as $y) {
            if (preg_match('/^\d{4}$/', $y->name)) {
                $html .= $y->name . $end; // Nối năm với $end nếu có
            }
        }
    }
    return $html;
}
?>