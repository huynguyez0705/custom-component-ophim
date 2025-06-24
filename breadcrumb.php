//Remoview schema breadcrumb rankmath

add_filter( 'rank_math/json_ld', function( $data ) {
if ( isset( $data['breadcrumb'] ) ) {
unset( $data['breadcrumb'] ); // Remove breadcrumb schema
}
return $data;
});




<?php include( get_template_directory() . '/breadcrumb.php' ); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php
// Định nghĩa biến cho "Xem Phim"
$position_1_name = "Xem Phim";
$position_1_url = home_url(); // Giả sử URL là trang chủ
?>

<ul class="breadcrumb-pm">
    <li><a title="<?= $position_1_name ?>" href="<?= $position_1_url ?>"><i class="fa fa-home"></i>
            <?= $position_1_name ?></a></li>
    <?php 
    if (is_tax()) {
        $taxonomies = [
            'ophim_categories' => '',
            'ophim_directors' => 'Đạo Diễn: ',
            'ophim_years' => 'Năm Phát Hành: ',
            'ophim_actors' => 'Diễn Viên: ',
            'ophim_regions' => 'Quốc Gia: ',
            'ophim_genres' => 'Thể Loại: '
        ];
        $taxonomy = get_queried_object()->taxonomy;
        $term_name = single_tag_title('', false);
        $prefix = $taxonomies[$taxonomy] ?? '';
        echo '<li><span class="breadcrumb_last">' . esc_html($prefix . $term_name) . '</span></li>';
    } elseif (is_archive()) {
        echo '<li><span class="breadcrumb_last">Kho Phim Mới</span></li>';
    } elseif (is_search()) {
        echo '<li><span class="breadcrumb_last">Kết quả tìm kiếm: ' . esc_html(get_search_query()) . '</span></li>';
    } else {
        $categories = get_the_terms(get_the_ID(), 'ophim_categories');
        if (!empty($categories) && !is_wp_error($categories)) {
            $term = reset($categories);
            echo '<li><a href="' . esc_url(home_url('/') . (get_option('ophim_slug_categories', 'categories')) . '/' . $term->slug . '/') . '" title="' . esc_attr($term->name) . '"><span>' . esc_html($term->name) . '</span></a></li>';
        }
        if (isEpisode()) {
            echo '<li><a href="' . esc_url(get_the_permalink()) . '" title="' . esc_attr(get_the_title()) . '"><span>' . esc_html(get_the_title()) . '</span></a></li>';
            echo '<li><span class="breadcrumb_last">Tập ' . esc_html(episodeName()) . '</span></li>';
        } elseif (is_single() && get_post_type() === 'ophim') {
            echo '<li><span class="breadcrumb_last">' . esc_html(get_the_title()) . '</span></li>';
        }
    }
    ?>
</ul>

<style>
.breadcrumb-pm {
    display: flex;
    list-style: none;
    padding: 1rem;
    margin: 1rem 0;
    font-size: 14px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    font-weight: bold;
    flex-wrap: wrap;
    row-gap: 2rem;
    align-items: center;
    text-transform: uppercase;
    background: rgba(0, 0, 0, .5)
}

.breadcrumb-pm li {
    display: flex;
    align-items: center
}

.breadcrumb-pm li+li::before {
    content: "\f0da";
    margin: 0 8px;
    color: var(--color);
    font-size: 18px;
    font-family: "Font Awesome 5 Free"
}

.breadcrumb-pm a {
    text-decoration: none;
    color: #fff !important;
    transition: color 0.2s
}

.breadcrumb-pm a:hover {
    color: var(--hover) !important
}

.breadcrumb-pm .breadcrumb_last {
    color: #ddd !important
}

.breadcrumb-pm i {
    margin-right: 5px;
    color: var(--color)
}

@media (max-width:768px) {
    .breadcrumb-pm {
        font-size: 14px
    }

    .breadcrumb-pm li+li::before {
        margin: 0 5px
    }
}
</style>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "@id": "https://<?php echo esc_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>#breadcrumb",
    "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "<?= $position_1_name ?>",
            "item": "<?= esc_url(home_url()); ?>"
        },
        <?php
        if (is_tax()) {
            $taxonomies = [
                'ophim_categories' => '',
                'ophim_directors' => 'Đạo Diễn: ',
                'ophim_years' => 'Năm Phát Hành: ',
                'ophim_actors' => 'Diễn Viên: ',
                'ophim_regions' => 'Quốc Gia: ',
                'ophim_genres' => 'Thể Loại: '
            ];
            $taxonomy = get_queried_object()->taxonomy;
            $term_name = single_tag_title('', false);
            $prefix = $taxonomies[$taxonomy] ?? '';
            echo '{"@type": "ListItem", "position": 2, "name": "' . esc_html($prefix . $term_name) . '", "item": "' . esc_url(get_term_link(get_queried_object())) . '"}';
        } elseif (is_archive()) {
            echo '{"@type": "ListItem", "position": 2, "name": "Kho Phim Mới", "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"}';
        } elseif (is_search()) {
            echo '{"@type": "ListItem", "position": 2, "name": "Kết quả tìm kiếm ' . esc_html(get_search_query()) . '", "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_REFERER'] . '"}';
        } else {
            $categories = get_the_terms(get_the_ID(), 'ophim_categories');
            if (!empty($categories) && !is_wp_error($categories)) {
                $term = reset($categories);
                echo '{"@type": "ListItem", "position": 2, "name": "' . esc_html($term->name) . '", "item": "' . esc_url(home_url('/') . (get_option('ophim_slug_categories', 'categories')) . '/' . $term->slug . '/') . '"}';
            }
            if (isEpisode()) {
                echo ',{"@type": "ListItem", "position": 3, "name": "' . esc_html(get_the_title()) . '", "item": "' . esc_url(get_the_permalink()) . '"}';
                echo ',{"@type": "ListItem", "position": 4, "name": "Tập ' . esc_html(episodeName()) . '", "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"}';
            } elseif (is_single() && get_post_type() === 'ophim') {
                echo ',{"@type": "ListItem", "position": 3, "name": "' . esc_html(get_the_title()) . '", "item": "' . esc_url(get_permalink(get_the_ID())) . '"}';
            }
        }
        ?>
    ]
}
</script>