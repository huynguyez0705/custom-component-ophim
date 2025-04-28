<?php


function custom_seo_title($title) {
    if (is_admin()) return $title;  
    if (isEpisode()) {
        $post_title = get_the_title(get_the_ID());
        $year = op_get_year(get_the_ID());
        $lang = op_get_lang(get_the_ID());
        $quality = op_get_quality(get_the_ID());
        $episode = episodeName();
        $ori_title = op_get_original_title();
        return "Xem Phim {$post_title} - {$ori_title} - {$lang} {$quality} - Tập {$episode} - Phimmoi";
    }
    return $title;
}

function custom_seo_description($desc) {
    if (function_exists('isEpisode') && isEpisode()) {
        $post_title = get_the_title(get_the_ID());
        $episode = function_exists('episodeName') ? episodeName() : '';
        $ex = get_the_excerpt();
        $new_des = "Xem Phim {$post_title} Tập {$episode} - MotPhim. {$ex} ";
        $new_des = mb_substr($new_des, 0, 130, 'UTF-8') . '...';
    }
    return $desc;
}
//yoast seo
add_filter('wpseo_title', 'custom_seo_title', 10, 1);
add_filter('wpseo_opengraph_title', 'custom_seo_title', 10, 1);
add_filter('wpseo_metadesc', 'custom_seo_description', 10, 1);
add_filter('wpseo_opengraph_desc', 'custom_seo_description', 10, 1);
//rankmath
add_filter('rank_math/frontend/title', 'custom_seo_title', 10, 1);
add_filter('rank_math/frontend/description', 'custom_seo_description', 10, 1);


// keywords
add_action("wp_head", function() {
    $keywords = '';
    $title = get_the_title(get_the_ID());
    $ori_title = function_exists('op_get_original_title') ? op_get_original_title() : '';
    $episode = function_exists('episodeName') ? episodeName() : '';
    $domain ="Phimmoi";
    if(isEpisode()){$keywords = "{$title} - Tập {$episode} {$domain}, {$ori_title} - Tập {$episode} {$domain}";}
    elseif (is_singular('ophim')) {      
        $keywords = "{$title}  {$domain}, {$ori_title}  {$domain}";
    } elseif (is_front_page()) {
        $keywords = "Phimmoi, Phim Mới, Phimmoi net, Phim Trung Quốc, Phim Hàn Quốc, Phim chiếu rạp, Phim hành động, Phim kinh di, Phim hài, Phim hoạt hình, Phim Mỹ, Phim Võ Thuật, Phim bộ hay nhất, Xem phim Online";
    } elseif (is_tax(['ophim_categories', 'ophim_directors', 'ophim_years', 'ophim_actors', 'ophim_regions', 'ophim_genres'])) { 
        $keywords = single_tag_title('', false) . "  {$domain}";
    }elseif (is_archive()) {
        $keywords = "Kho phim mới Phimmoi";
    } 
    
    if ($keywords) {
        echo "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
    }
}, 2);

?>