<?php

//ajax search
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

//search fim
function mySearchFilter($query) {
    if ($query->is_search) {
        if (!isset($_GET['filter'])){
            $_GET['filter']['categories'] ='';
            $_GET['filter']['genres'] ='';
            $_GET['filter']['regions'] ='';
            $_GET['filter']['years'] ='';
            $_GET['filter']['status'] = '';
            
        }
        $categories = $_GET['filter']['categories'];
        $years = $_GET['filter']['years'];
        $genres = $_GET['filter']['genres'];
        $regions = $_GET['filter']['regions'];
        $status     = $_GET['filter']['status'];
        $query->set('post_type', 'ophim');
        $args = array();
        if($categories) {
            $args[] = array(
                'taxonomy' => 'ophim_categories',
                'field' => 'slug',
                'terms' => $categories,
            );
        }
        if($years) {
            $args[] = array(
                'taxonomy' => 'ophim_years',
                'field' => 'slug',
                'terms' => $years,
            );
        }
        if($genres) {
            $args[] = array(
                'taxonomy' => 'ophim_genres',
                'field' => 'slug',
                'terms' => $genres,
            );
        }
        if($regions) {
            $args[] = array(
                'taxonomy' => 'ophim_regions',
                'field' => 'slug',
                'terms' => $regions,
            );
        }
        if($args) {
            $query->set('tax_query',$args);
        }
        if ($status) {
            $meta = [
                'key'     => 'ophim_movie_status',
                'value'   => $status,
                'compare' => '=',
            ];
            $query->set('meta_query', [$meta]);
        }
    };
    return $query;
};

add_filter('pre_get_posts','mySearchFilter');