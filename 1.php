<ul class="breadcrumb">
    <li>
        <a title="Xem Phim" href="/">
            <i class="fa fa-home"></i> Xem phim
        </a>
    </li>

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

        // Get the queried taxonomy and term name
        $taxonomy = get_queried_object()->taxonomy;
        $term_name = single_tag_title('', false);
        $prefix = $taxonomies[$taxonomy] ?? '';

        echo '<li><span class="breadcrumb_cur">' . esc_html($prefix . $term_name) . '</span></li>';
    } 
    elseif (is_archive()) {
        echo '<li><span class="breadcrumb_cur">Kho Phim Mới</span></li>';
    } 
    elseif (is_search()) {
        echo '<li><span class="breadcrumb_cur">Kết quả tìm kiếm: ' . esc_html(get_search_query()) . '</span></li>';
    } 
    else {
        $categories = get_the_terms(get_the_ID(), 'ophim_categories');
        if (!empty($categories) && !is_wp_error($categories)) {
            $term = reset($categories);
            echo '<li><a href="' . esc_url(home_url('/') . (get_option('ophim_slug_categories', 'categories')) . '/' . $term->slug . '/') . '" title="' . esc_attr($term->name) . '">
                    <span>' . esc_html($term->name) . '</span>
                </a></li>';
        }

        if (isEpisode()) {
            echo '<li><a href="' . esc_url(get_the_permalink()) . '" title="' . esc_attr(get_the_title()) . '">
                    <span>' . esc_html(get_the_title()) . '</span>
                </a></li>';
            echo '<li><span class="breadcrumb_cur">Tập ' . esc_html(episodeName()) . '</span></li>';
        } 
        // If it's a single movie page
        elseif (is_single() && get_post_type() === 'ophim') {
            echo '<li><span class="breadcrumb_cur">' . esc_html(get_the_title()) . '</span></li>';
        }
    }
    ?>
</ul>

<style>
    .breadcrumb {
        display: flex;
        list-style: none;
        padding: 1rem;
        margin: 1rem 0;
        font-size: 14px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        font-weight: normal;
        flex-wrap: wrap;
        row-gap: 2rem;
        align-items: center;
        background: rgba(0, 0, 0, .5);
    }

    .breadcrumb li {
        display: flex;
        align-items: center;
    }

    .breadcrumb li + li::before {
        content: "»";
        margin: 0 8px;
        color: #da966e;
        font-size: 18px;
        font-family: "Font Awesome 5 Free";
    }

    .breadcrumb a {
        text-decoration: none;
        color: #da966e !important;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: #ddd !important;
    }

    .breadcrumb .breadcrumb_cur {
        color: #ddd !important;
    }

    .breadcrumb i {
        margin-right: 5px;
        color: var(--color);
    }

    @media (max-width: 768px) {
        .breadcrumb-pm {
            font-size: 14px;
        }

        .breadcrumb-pm li + li::before {
            margin: 0 5px;
        }
    }
</style>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "@id": "https://<?php echo esc_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>#breadcrumb",
    "itemListElement": [
        {
            "@type": "ListItem", 
            "position": 1, 
            "name": "MotPhim", 
            "item": "<?= esc_url(home_url()); ?>"
        },
        <?php
        // Check if it's a taxonomy page
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
            echo '{
                "@type": "ListItem", 
                "position": 2, 
                "name": "' . esc_html($prefix . $term_name) . '", 
                "item": "' . esc_url(get_term_link(get_queried_object())) . '"
            }';
        } 
        // Check if it's an archive page
        elseif (is_archive()) {
            echo '{
                "@type": "ListItem", 
                "position": 2, 
                "name": "Kho Phim Mới", 
                "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"
            }';
        } 
        // Check if it's a search results page
        elseif (is_search()) {
            echo '{
                "@type": "ListItem", 
                "position": 2, 
                "name": "Kết quả tìm kiếm ' . esc_html(get_search_query()) . '", 
                "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_REFERER'] . '"
            }';
        } 
        else {
            // If it's a single movie page, get the movie's category
            $categories = get_the_terms(get_the_ID(), 'ophim_categories');
            if (!empty($categories) && !is_wp_error($categories)) {
                $term = reset($categories);
                echo '{
                    "@type": "ListItem", 
                    "position": 2, 
                    "name": "' . esc_html($term->name) . '", 
                    "item": "' . esc_url(home_url('/') . (get_option('ophim_slug_categories', 'categories')) . '/' . $term->slug . '/') . '"
                }';
            }

            // If it's an episode, add the episode breadcrumb
            if (isEpisode()) {
                echo ',{
                    "@type": "ListItem", 
                    "position": 3, 
                    "name": "' . esc_html(get_the_title()) . '", 
                    "item": "' . esc_url(get_the_permalink()) . '"
                }';
                echo ',{
                    "@type": "ListItem", 
                    "position": 4, 
                    "name": "Tập ' . esc_html(episodeName()) . '", 
                    "item": "https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"
                }';
            } 
            // If it's a single movie page
            elseif (is_single() && get_post_type() === 'ophim') {
                echo ',{
                    "@type": "ListItem", 
                    "position": 3, 
                    "name": "' . esc_html(get_the_title()) . '", 
                    "item": "' . esc_url(get_permalink(get_the_ID())) . '"
                }';
            }
        }
        ?>
    ]
}
</script>
