<?php
/**
 * Plugin Name: Top Viewed and Featured Movies
 * Description: A plugin to display the top movies with the most views and featured movies in the WordPress admin.
 * Version: Limited
 * Author: Ophim
 */

// Thêm mục menu trong Admin
add_action('admin_menu', 'top_viewed_movies_add_menu');
function top_viewed_movies_add_menu() {
    add_menu_page(
        'Movies Dashboard', 'Movies Dashboard', 'manage_options', 'top-viewed-movies', 
        'top_viewed_movies_display', 'dashicons-chart-bar', 20
    );
} 
// Hiển thị nội dung trang quản lý
// Hiển thị nội dung trang quản lý
function top_viewed_movies_display() {
    // Get sorting info from the URL, default to sorting by views in descending order
    $featured_orderby = $_GET['featured_orderby'] ?? 'views';  // Default to 'views'
    $featured_order = $_GET['featured_order'] ?? 'desc';  // Default to 'desc'
    $top_orderby = $_GET['top_orderby'] ?? 'views';  // Default to 'views'
    $top_order = $_GET['top_order'] ?? 'desc';  // Default to 'desc'
    $posts_per_page = isset($_POST['posts_per_page']) ? (int)$_POST['posts_per_page'] : 50;
    ?>
<div class="wrap">
    <h1>Movies Dashboard</h1>

    <h2>Featured Movies</h2>
    <table class="widefat fixed">
        <thead>
            <tr>
                <th>#</th>
                <th><a
                        href="<?php echo add_query_arg(['featured_orderby' => 'title', 'featured_order' => ($featured_order == 'asc' ? 'desc' : 'asc')]); ?>">Title</a>
                </th>
                <th><a
                        href="<?php echo add_query_arg(['featured_orderby' => 'views', 'featured_order' => ($featured_order == 'asc' ? 'desc' : 'asc')]); ?>">Views</a>
                </th>
                <th>Category</th>
                <th>Featured</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody><?php display_featured_movies($featured_orderby, $featured_order); ?></tbody>
    </table>

    <h2>Top Viewed Movies</h2>
    <div class="posts_per_page">
        <label for="posts_per_page">Number of movies to show:</label>
        <input type="number" id="posts_per_page_input" name="posts_per_page" value="<?php echo $posts_per_page; ?>"
            min="1" />
        <button id="update_movies_button" class="button button-primary">Update</button>
    </div>

    <table class="widefat fixed">
        <thead>
            <tr>
                <th>Rank</th>
                <th><a
                        href="<?php echo add_query_arg(['top_orderby' => 'title', 'top_order' => ($top_order == 'asc' ? 'desc' : 'asc')]); ?>">Title</a>
                </th>
                <th><a
                        href="<?php echo add_query_arg(['top_orderby' => 'views', 'top_order' => ($top_order == 'asc' ? 'desc' : 'asc')]); ?>">Views</a>
                </th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="top-viewed-movies"><?php top_viewed_movies_list($posts_per_page, $top_orderby, $top_order); ?>
        </tbody>
    </table>
</div>
<?php }

// Hiển thị danh sách phim nổi bật
// Hiển thị danh sách phim nổi bật
function display_featured_movies($orderby = 'meta_value_num', $order = 'desc') {
    $args = [
        'post_type' => 'ophim',
        'posts_per_page' => -1,
        'meta_key' => 'ophim_view', // Sắp xếp theo views
        'meta_query' => [
            [
                'key' => 'ophim_featured_post',
                'value' => 1,
                'compare' => '='
            ]
        ],
        'orderby' => ($orderby == 'title') ? 'title' : 'meta_value_num',
        'order' => $order,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $rank = 1;
        while ($query->have_posts()) {
            $query->the_post();
            $views = get_post_meta(get_the_ID(), 'ophim_view', true) ?: 0;
            $category_list = '';
            $terms = get_the_terms(get_the_ID(), 'ophim_categories');
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $category_list .= $term->name . ', ';
                }
                $category_list = rtrim($category_list, ', ');
            }

            echo '<tr>
                    <td>' . $rank . '</td>
                    <td><a href="' . get_edit_post_link() . '">' . get_the_title() . '</a></td>
                    <td>' . $views . '</td>
                    <td>' . $category_list . '</td>
                    <td>Yes</td>
                    <td>
                        <button class="button button-secondary remove-featured" data-postid="' . get_the_ID() . '">
                            ' . esc_html__('Remove Featured', 'top-viewed-movies') . '
                        </button>
                    </td>
                  </tr>';
            $rank++;
        }
    } else {
        echo '<tr><td colspan="6">No featured movies found.</td></tr>';
    }
    wp_reset_postdata();
}

// Hiển thị các bài viết có số lượt xem nhiều nhất
function top_viewed_movies_list($posts_per_page = 50, $orderby = 'views', $order = 'desc', $paged = 1) {
    $args = [
        'post_type' => 'ophim',
        'posts_per_page' => $posts_per_page,
        'meta_key' => 'ophim_view',
        'orderby' => ($orderby == 'title') ? 'title' : 'meta_value_num',
        'order' => $order,
        'paged' => $paged
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $rank = 1;
        while ($query->have_posts()) {
            $query->the_post();
            $views = get_post_meta(get_the_ID(), 'ophim_view', true) ?: 0;
            $is_featured = get_post_meta(get_the_ID(), 'ophim_featured_post', true);
            $categories = get_the_terms(get_the_ID(), 'ophim_categories');
            $category_list = '';
            foreach ($categories as $term) {
                $category_list .= $term->name . ', ';
            }
            $category_list = rtrim($category_list, ', ');

            echo '<tr>';
            echo '<td>' . $rank . '</td>';
            echo '<td><a href="' . get_edit_post_link() . '">' . get_the_title() . '</a></td>';
            echo '<td>' . $views . '</td>';
            echo '<td>' . $category_list . '</td>';

            if ($is_featured) {
                echo '<td><button class="button button-secondary del-featured" id="feature-del-' . get_the_ID() . '" data-postid="' . get_the_ID() . '">' . esc_html__('Remove Featured', 'top-viewed-movies') . '</button></td>';
            } else {
                echo '<td><button class="button button-primary add-featured" id="feature-add-' . get_the_ID() . '" data-postid="' . get_the_ID() . '">' . esc_html__('Add Featured', 'top-viewed-movies') . '</button></td>';
            }
            $rank++;
        }

        echo '<tr><td colspan="5" class="pagination">';
        echo paginate_links([
            'total' => $query->max_num_pages,
            'current' => $paged,
            'format' => '?paged=%#%',
            'prev_text' => __('Previous'),
            'next_text' => __('Next'),
        ]);
        echo '</td></tr>';
    } else {
        echo '<tr><td colspan="5">No movies found.</td></tr>';
    }
    wp_reset_postdata();
}



// Xử lý AJAX cập nhật số lượng phim
add_action('wp_ajax_update_posts_per_page', 'update_posts_per_page');
function update_posts_per_page() {
    $posts_per_page = isset($_POST['posts_per_page']) ? (int)$_POST['posts_per_page'] : 50;
    $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : 'views';
    $order = isset($_POST['order']) ? $_POST['order'] : 'desc';
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 1;
    ob_start();
    top_viewed_movies_list($posts_per_page, $orderby, $order, $paged);
    $output = ob_get_clean();
    echo $output;
    wp_die();
}

// Xóa phim khỏi mục Featured
if (!function_exists('dt_toggle_featured')) {
    function dt_toggle_featured() {
        $postid = isset($_REQUEST['postid']) ? $_REQUEST['postid'] : false;
        $featured_status = isset($_REQUEST['featured_status']) ? (int)$_REQUEST['featured_status'] : 0;
    
        if ($postid && get_post($postid)) {
            update_post_meta($postid, 'ophim_featured_post', $featured_status);
            echo $featured_status ? 'Added to Featured' : 'Removed from Featured';
        }
        die();
    }
}
add_action('wp_ajax_dt_toggle_featured', 'dt_toggle_featured');



add_action('admin_footer', 'top_viewed_movies_ajax_script');


function top_viewed_movies_ajax_script() { ?>
<script type="text/javascript">
jQuery(function($) {
    // Xử lý nút "Remove Featured"
    $('.remove-featured').on('click', function() {
        var postid = $(this).data('postid');
        var button = $(this);
        if (confirm(
                '<?php esc_html_e('Are you sure you want to remove this movie from featured?', 'top-viewed-movies'); ?>'
            )) {
            $.post(ajaxurl, {
                action: 'dt_toggle_featured',
                postid: postid
            }, function() {
                button.closest('tr').fadeOut();
                location.reload();
            });
        }
    });

    // Xử lý nút "Add Featured"
    $(document).on('click', '.add-featured', function() {
        var postid = $(this).data('postid');
        $.post(ajaxurl, {
                action: 'dt_toggle_featured',
                postid: postid,
                featured_status: 1
            },
            function(res) {
                $("#feature-add-" + postid).hide();
                var removeButton =
                    '<button class="button button-secondary del-featured" id="feature-del-' +
                    postid + '" data-postid="' + postid + '">' +
                    '<?php esc_html_e('Remove Featured', 'top-viewed-movies'); ?>' + '</button>';
                $('#feature-add-' + postid).parent().append(removeButton);


            })
    });

    $('.del-featured').on('click', function() {
        var postid = $(this).data('postid');
        if (confirm(
                '<?php esc_html_e('Are you sure you want to remove this movie from featured?', 'top-viewed-movies'); ?>'
            )) {
            $.post(ajaxurl, {
                action: 'dt_toggle_featured',
                postid: postid,
                featured_status: 0

            }, function(res) {
                // Ẩn nút "Remove Featured"
                $("#feature-del-" + postid).hide();

                // Tạo lại nút "Add Featured"
                var addButton =
                    '<button class="button button-primary add-featured" id="feature-add-' +
                    postid + '" data-postid="' + postid + '">' +
                    '<?php esc_html_e('Add Featured', 'top-viewed-movies'); ?>' + '</button>';

                // Thêm nút vào đúng vị trí
                $('#feature-del-' + postid).parent().append(addButton);
                // Reload lại trang để đảm bảo phân trang đúng
            });
        }
    });
    // Xử lý nút "Update"
    $('#update_movies_button').on('click', function() {
        var posts_per_page = $('#posts_per_page_input').val();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'update_posts_per_page',
                posts_per_page: posts_per_page,
                orderby: '<?php echo $top_orderby; ?>',
                order: '<?php echo $top_order; ?>',
                paged: 1 // Chuyển về trang đầu khi cập nhật số lượng phim
            },
            success: function(response) {
                $('#top-viewed-movies').html(response);
            }
        });
    });

    $(document).on('click', '.page-numbers', function(e) {
        e.preventDefault();
        var page = $(this).text();
        var posts_per_page = $('#posts_per_page_input').val();
        var pageUrl = window.location.href.split('?')[0];
        var newUrl = pageUrl + '?page=top-viewed-movies&page=' + page;
        history.pushState({
            path: newUrl
        }, '', newUrl);
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'update_posts_per_page',
                posts_per_page: posts_per_page,
                orderby: '<?php echo $top_orderby; ?>',
                order: '<?php echo $top_order; ?>',
                paged: page // Lấy trang cần hiển thị
            },
            success: function(response) {
                $('#top-viewed-movies').html(response);
            }
        });
    });
});
</script>
<?php }
// Thêm style CSS vào admin (minified)
add_action('admin_head', 'top_viewed_movies_admin_styles');
function top_viewed_movies_admin_styles() {
    echo '<style>
        button.button.del-featured,button.button.remove-featured{background:#f44336;color:#fff;border:1px solid #f44336;border-radius:3px;cursor:pointer;display:inline-block;vertical-align:middle}
button.remove-featured:hover{background:#f6f7f7;border-color:#f6f7f7}
/* Style for the pagination (align to the right) */
.pagination{text-align:right;padding: 2em 0 1em !important}
.pagination .page-numbers{background-color:#f1f1f1;border:1px solid #ddd;padding:5px 10px;margin:0 3px;border-radius:3px;text-decoration:none;color:#0073aa}
.pagination .page-numbers:hover{background-color:#0073aa;color:#fff}
.pagination .current{background-color:#0073aa;color:#fff;border:1px solid #0073aa}
.pagination .prev,.pagination .next{font-weight:bold}
.posts_per_page{margin:10px 0}
            
    </style>';
}



?>