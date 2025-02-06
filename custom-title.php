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
         $ex = wp_trim_words(get_the_excerpt(), 100, '...');
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
    if (is_singular('ophim')) { // Kiểm tra nếu là trang chi tiết của post type "ophim"
        $title = get_the_title(get_the_ID());
        $ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
        echo <<<EOT
		<meta name="keywords" content="{$title} motchill, {$ori_title} motchill"/>
		EOT;
        echo "\n"; // Xuống dòng cho dễ đọc trong source HTML
    } else if(is_front_page()) {
		echo '<meta name="keywords" content="motchill, motchill tv, motchill vip"/>' . "\n";

	} else if (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) { 
        // Nếu là trang taxonomy (danh mục, đạo diễn, năm, diễn viên, khu vực, thể loại)
        $tag_title = single_tag_title('', false);
        echo <<<EOT
		<meta name="keywords" content="{$tag_title} motchill"/>
		EOT;
 		echo "\n";
    }
},2);


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