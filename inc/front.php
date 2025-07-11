<?php

// Ajax search
add_action( 'wp_footer', 'ajax_fetch' );
function ajax_fetch() {
    ?>
<script type="text/javascript">
function fetch() {
    $("#result").html('');
    key = jQuery('#search').val();
    if (!key) {
        $("#result").html('');
        return;
    }
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: {
            action: 'search_film',
            keyword: key,
            limit: 5
        },
        success: function(res) {
            $("#result").html('');
            let data = JSON.parse(res);
            $.each(data, function(key, value) {
                $('#result').append('<div class=""> <a href="' + value.slug + '"><img src="' + value
                    .image + '" height="40"/> ' + value.title + ' | ' + value.original_title +
                    ' | ' + value.year + '</a><div><hr>')
            });
        }
    });
    document.body.addEventListener("click", function(event) {
        $("#result").html('');
    });
}
</script>
<?php
}

// Search filter
function mySearchFilter($query) {
    if ($query->is_search) {
        if (!isset($_GET['filter'])) {
            $_GET['filter']['categories'] = '';
            $_GET['filter']['genres'] = '';
            $_GET['filter']['regions'] = '';
            $_GET['filter']['years'] = '';
            $_GET['filter']['status'] = '';
            $_GET['filter']['sort'] = '';
            $_GET['filter']['lang'] = '';
        }
        $categories = $_GET['filter']['categories'];
        $years = $_GET['filter']['years'];
        $genres = $_GET['filter']['genres'];
        $regions = $_GET['filter']['regions'];
        $status = $_GET['filter']['status'];
        $sort = $_GET['filter']['sort'];
        $lang = $_GET['filter']['lang'];

        $query->set('post_type', 'ophim');
        $args = array();
        if ($categories) {
            $args[] = array(
                'taxonomy' => 'ophim_categories',
                'field' => 'slug',
                'terms' => $categories,
            );
        }
        if ($years) {
            $args[] = array(
                'taxonomy' => 'ophim_years',
                'field' => 'slug',
                'terms' => $years,
            );
        }
        if ($genres) {
            $args[] = array(
                'taxonomy' => 'ophim_genres',
                'field' => 'slug',
                'terms' => $genres,
            );
        }
        if ($regions) {
            $args[] = array(
                'taxonomy' => 'ophim_regions',
                'field' => 'slug',
                'terms' => $regions,
            );
        }
        if ($args) {
            $query->set('tax_query', $args);
        }
        if ($status) {
            $meta_query[] = [
                'key' => 'ophim_movie_status',
                'value' => $status,
                'compare' => '=',
            ];
        }
        if ($lang) {
            $meta_query[] = [
                'key' => 'ophim_lang',
                'value' => $lang,
                'compare' => 'LIKE',
            ];
        }
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }

        // Xử lý sắp xếp
        if ($sort) {
            switch ($sort) {
                case 'newest':
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
                case 'updated':
                    $query->set('meta_key', 'ophim_fetch_ophim_update_time');
                    $query->set('orderby', 'meta_value');
                    $query->set('order', 'DESC');
                    break;
                case 'views':
                    $query->set('meta_key', 'ophim_view');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'rating':
                    $query->set('meta_query', [
                        'relation' => 'OR',
                        [
                            'key' => 'ophim_votes',
                            'value' => 8,
                            'compare' => '>=',
                            'type' => 'NUMERIC',
                        ],
                        [
                            'key' => 'ophim_random_votes',
                            'compare' => 'EXISTS',
                        ],
                    ]);
                    $query->set('orderby', [
                        'ophim_votes' => 'DESC',
                        'ophim_random_votes' => 'DESC',
                    ]);
                    break;
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
    return $query;
};

add_filter('pre_get_posts', 'mySearchFilter');