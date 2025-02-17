<?php

function custom_opengraph_meta() {
    global $post;

    // Kiểm tra nếu là bài viết đơn
    if (is_single()) {
        // Lấy URL ảnh đại diện hoặc ảnh mặc định
        $img_src = op_get_poster_url($post->ID);

        // Lấy tiêu đề và mô tả bài viết
        $title = get_the_title($post->ID);
        $description = wp_strip_all_tags(get_the_content());

        // Nếu Yoast SEO được kích hoạt
        if (defined('WPSEO_VERSION')) {
            add_filter('wpseo_opengraph_title', fn() => $title);
            add_filter('wpseo_opengraph_desc', fn() => $description);
            add_filter('wpseo_opengraph_image', fn() => $img_src);
        }

        // Nếu Rank Math được kích hoạt
        elseif (defined('RANK_MATH_VERSION')) {
            foreach (['facebook', 'twitter'] as $network) {
                add_filter("rank_math/opengraph/{$network}/title", fn() => $title);
                add_filter("rank_math/opengraph/{$network}/description", fn() => $description);
                add_filter("rank_math/opengraph/{$network}/image", fn() => $img_src);
            }
        }

        // Nếu không sử dụng plugin SEO
        else {
            echo '<meta property="og:title" content="' . esc_attr($title) . '"/>';
            echo '<meta property="og:description" content="' . esc_attr($description) . '"/>';
            echo '<meta property="og:type" content="movie"/>';
            echo '<meta property="og:url" content="' . esc_url(get_permalink($post->ID)) . '"/>';
            echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '"/>';
            echo '<meta property="og:image" content="' . esc_url($img_src) . '"/>';
        }
    }
}
add_action('wp_head', 'custom_opengraph_meta', 1);

add_filter( 'document_title', 'mod_browser_tab_title');
function mod_browser_tab_title( $title )
{
    if(episodeName()){
        $title = $title.' - Tập '.episodeName();
    }
    return $title;
}

function new_rewrite_rule()
{
    $getslug = get_option('ophim_watch_urls');
    if($getslug){
        $slug = $getslug;
    }else{
        $slug = 'xem-phim';
    }
    add_rewrite_rule($slug.'/([^/]*)/([^/]*)', 'index.php?ophim=$matches[1]', 'top');
}

add_action('init', 'new_rewrite_rule');
//classic widget and editer
add_filter('use_block_editor_for_post', '__return_false');
add_filter( 'use_widgets_block_editor', '__return_false' );
//filter feature
function wisdom_filter_tracked_plugins()
{
    global $typenow;
    if ($typenow == 'ophim') {
        $current_plugin = oIsset($_GET,'featured');?>
<select name="featured" id="featured">
    <option value="all" <?php selected('all', $current_plugin); ?>>All</option>
    <option value="1" <?php selected('1', $current_plugin); ?>>Featured</option>
</select>
<?php }
}

add_action('restrict_manage_posts', 'wisdom_filter_tracked_plugins');
// end filter
function wisdom_sort_plugins_by_slug($query)
{
    global $pagenow;
    $post_type = oIsset($_GET,'post_type');
    if (is_admin() && $pagenow == 'edit.php' && $post_type == 'ophim' && isset($_GET['featured']) && $_GET['featured'] == '1') {
        $query->query_vars['meta_key'] = 'ophim_featured_post';
        $query->query_vars['meta_value'] = $_GET['featured'];
        $query->query_vars['meta_compare'] = '=';
    }
}

add_filter('parse_query', 'wisdom_sort_plugins_by_slug');

//custom columm phim
function filter_movies($defaults)
{
    $defaults['rating'] = 'Rating';
    $defaults['vote'] = 'Vote';
    $defaults['cviews'] = 'Views';
    $defaults['featur'] = 'Featured';
    $defaults['thumb'] = 'Thumb';
    $defaults['poster'] = 'Poster';
    return $defaults;
}

add_filter('manage_ophim_posts_columns', 'filter_movies');
add_action('manage_ophim_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key == 'rating') {
        echo op_get_rating();
    }
    if ($column_key == 'vote') {
        echo op_get_rating_count();
    }
    if ($column_key == 'cviews') {
        echo op_get_post_view();
    }
    if ($column_key == 'thumb') {
        op_the_thumbnail();
    }
    if ($column_key == 'poster') {
        op_the_poster();
    }
    if ($column_key == 'featur') {
        $hideA = (1 == op_get_meta('featured_post')) ? 'style="display:none"' : '';
        $hideB = (1 != op_get_meta('featured_post')) ? 'style="display:none"' : '';
        echo '<a id="feature-add-' . $post_id . '" class="button add-to-featured button-primary" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('dt-featured-' . $post_id) . '"  ' . $hideA . '>' . __('Add') . '</a>';
        echo '<a id="feature-del-' . $post_id . '" class="button del-of-featured" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('dt-featured-' . $post_id) . '" ' . $hideB . '>' . __('Remove') . '</a>';
    }
}, 10, 2);
//end cusstom columm fim


// add includejscss
function ophim_include_myuploadscript()
{

    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }
    wp_enqueue_style('admin_csss', OFIM_PUBLIC_URL . '/css/admin.style.min.css?ver=2.5.5', false, '');
    wp_enqueue_script('admin_js', OFIM_JS_URL . '/script.js', array('jquery'), null, false);
}

add_action('admin_enqueue_scripts', 'ophim_include_myuploadscript');


//end upload


//add meta box
add_action('init', 'framework_core', 0);
function ophim_meta_box()
{
    global $typenow;
    if ($typenow == 'ophim') {
        add_meta_box('info_phim', 'Thông tin', 'info_phim', 'ophim');
        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            add_meta_box('link_custom_box_html', 'Tập phim', 'link_custom_box_html', 'ophim');
        }
    }
}

add_action('add_meta_boxes', 'ophim_meta_box');

function info_phim($post)
{
    include_once OFIM_TEMPLADE_PATCH . '/backend/metabox_info.php';
}

function ophim_thongtin_save($post_id)
{
    if (isset($_POST['ophim'])) {
        $post = $_POST['ophim'];
        $post['ophim_is_copyright'] = oIsset($post,'ophim_is_copyright');
        foreach ($post as $key => $p) {
            update_post_meta($post_id, $key, $p);
        }
        return $post_id;
    }
}

add_action('save_post', 'ophim_thongtin_save');
add_action('save_post', 'savephim');


function savephim($post_id)
{
    if (isset($_POST['episode'])) {
        $episode = $_POST['episode'];
        update_post_meta($post_id, 'ophim_episode_list', $episode);
    }
}


function link_custom_box_html($post)
{
    $postmneta = get_post_meta($post->ID, 'ophim_episode_list', true);
    $postid = $post->ID;
    include_once OFIM_TEMPLADE_PATCH . '/backend/episode.php';
}

//end custom box


//add menu tax admin

function framework_core()
{
    framework_create_post_type('ophim', 'OPhim', 'OPhim', get_option('ophim_slug_movies') ? get_option('ophim_slug_movies') : 'movie', ['title', 'editor'], 'dashicons-format-video');
    framework_create_taxonomies('ophim_directors', 'ophim', 'Đạo diễn', 'Đạo diễn', get_option('ophim_slug_directors') ? get_option('ophim_slug_directors') : 'directors');
    framework_create_taxonomies_cat('ophim_categories', 'ophim', 'Danh mục', 'Danh mục', get_option('ophim_slug_categories') ? get_option('ophim_slug_categories') : 'categories');
    framework_create_taxonomies('ophim_actors', 'ophim', 'Diễn viên', 'Diễn viên', get_option('ophim_slug_actors') ? get_option('ophim_slug_actors') : 'actors');
    framework_create_taxonomies('ophim_genres', 'ophim', 'Thể loại', 'Thể loại', get_option('ophim_slug_genres') ? get_option('ophim_slug_genres') : 'genres');
    framework_create_taxonomies('ophim_regions', 'ophim', 'Quốc gia', 'Quốc gia', get_option('ophim_slug_regions') ? get_option('ophim_slug_regions') : 'regions');
    framework_create_taxonomies('ophim_tags', 'ophim', 'Tags', 'Tags', get_option('ophim_slug_tags') ? get_option('ophim_slug_tags') : 'tags');
    framework_create_taxonomies('ophim_years', 'ophim', 'Năm', 'Năm', get_option('ophim_slug_years') ? get_option('ophim_slug_years') : 'years');
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_post_type($post_type, $singular_name, $plural_name, $slug, $support, $menu_icon)
{
    $labels = array('name' => _x($plural_name, 'post type general name', 'Ophim'), 'singular_name' => _x($singular_name, 'post type singular name', 'Ophim'), 'menu_name' => _x($plural_name, 'admin menu', 'Ophim'), 'name_admin_bar' => _x($singular_name, 'add new on admin bar', 'Ophim'), 'add_new' => _x('Thêm phim', $post_type, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item' => __('New ' . $singular_name, 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'view_item' => __('View ' . $singular_name, 'Ophim'), 'all_items' => __('Danh sách ', 'Ophim'), 'search_items' => __('Search ' . $singular_name . 's', 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'not_found' => __('No ' . $singular_name . ' found.', 'Ophim'), 'not_found_in_trash' => __('No ' . $singular_name . ' found in Trash.', 'Ophim'),);
    $args = array('labels' => $labels, 'description' => __('Description.', 'Ophim'), 'public' => ($post_type === 'process') ? false : true, 'publicly_queryable' => true, 'show_ui' => true, 'show_in_menu' => true, 'query_var' => true, 'rewrite' => ($post_type === 'process') ? false : array('slug' => $slug), 'capability_type' => 'post', 'has_archive' => true, 'hierarchical' => false, 'menu_position' => null, 'menu_icon' => $menu_icon, 'supports' => $support,);
    register_post_type($post_type, $args);
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_taxonomies($taxonomy_name, $post_type, $singular_name, $plural_name, $slug)
{
    $labels = array('name' => _x($plural_name, 'taxonomy general name', 'Ophim'), 'singular_name' => _x($singular_name, 'taxonomy singular name', 'Ophim'), 'search_items' => __('Search ' . $plural_name, 'Ophim'), 'all_items' => __('All ' . $plural_name, 'Ophim'), 'parent_item' => __('Parent ' . $singular_name, 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'update_item' => __('Update ' . $singular_name, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item_name' => __('New ' . $singular_name . ' Name', 'Ophim'), 'menu_name' => __($singular_name, 'Ophim'),);
    $args = array('hierarchical' => false, 'labels' => $labels, 'show_ui' => true, 'show_admin_column' => true, 'query_var' => true, 'rewrite' => array('slug' => $slug),);
    register_taxonomy($taxonomy_name, array($post_type), $args);
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_taxonomies_cat($taxonomy_name, $post_type, $singular_name, $plural_name, $slug)
{
    $labels = array('name' => _x($plural_name, 'taxonomy general name', 'Ophim'), 'singular_name' => _x($singular_name, 'taxonomy singular name', 'Ophim'), 'search_items' => __('Search ' . $plural_name, 'Ophim'), 'all_items' => __('All ' . $plural_name, 'Ophim'), 'parent_item' => __('Parent ' . $singular_name, 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'update_item' => __('Update ' . $singular_name, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item_name' => __('New ' . $singular_name . ' Name', 'Ophim'), 'menu_name' => __($singular_name, 'Ophim'),);
    $args = array('hierarchical' => true, 'labels' => $labels, 'show_ui' => true, 'show_admin_column' => true, 'query_var' => true, 'rewrite' => array('slug' => $slug),);
    register_taxonomy($taxonomy_name, array($post_type), $args);
}

//end add tax