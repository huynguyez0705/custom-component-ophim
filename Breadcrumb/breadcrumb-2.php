<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php
$position_1_name = 'SubNhanh';
$position_1_url  = home_url('/');
$position_2_name = "SubNhanh";
$taxonomies = [
    'ophim_categories' => '',
    'ophim_directors'  => 'Đạo Diễn: ',
    'ophim_years'      => 'Năm Phát Hành: ',
    'ophim_actors'     => 'Diễn Viên: ',
    'ophim_regions'    => 'Quốc Gia: ',
    'ophim_genres'     => 'Thể Loại: '
];

// Retrieve terms for single post or episode
$terms = [];
if (is_single() || isEpisode()) {
    foreach (['ophim_categories', 'ophim_regions'] as $tax) {
        $terms[$tax] = get_the_terms(get_the_ID(), $tax);
        $terms[$tax] = !empty($terms[$tax]) && !is_wp_error($terms[$tax]) && isset($terms[$tax][0]) ? [
            'name' => esc_html($terms[$tax][0]->name),
            'url' => esc_url(get_term_link($terms[$tax][0], $tax))
        ] : ['name' => '', 'url' => '#'];
    }
}
$category_name = $terms['ophim_categories']['name'] ?? '';
$category_url = $terms['ophim_categories']['url'] ?? '#';
$region_name = $terms['ophim_regions']['name'] ?? '';
$region_url = $terms['ophim_regions']['url'] ?? '#';
?>
<ul class="breadcrumb">
    <li>
        <a title="<?= esc_attr($position_1_name) ?>" href="<?= esc_url($position_1_url) ?>">
            <i class="fa fa-home"></i><?= esc_html($position_1_name) ?>
        </a>
    </li>

    <?php if (is_tax()):
        $taxonomy  = get_queried_object()->taxonomy;
        $term_name = single_tag_title('', false);
        $prefix    = $taxonomies[$taxonomy] ?? '';
    ?>
        <li>
            <span class="breadcrumb_last">
                <?= esc_html($prefix . $term_name) ?>
            </span>
        </li>
    <?php elseif (is_search()): ?>
        <li>
            <span class="breadcrumb_last">
                Kết quả tìm kiếm: <?= esc_html(get_search_query()) ?>
            </span>
        </li>
    <?php elseif (isEpisode()): ?>
        <!-- <li>
<a href="<?= esc_url(get_post_type_archive_link('ophim')) ?>" title="<?= esc_attr($position_2_name) ?>">
<span><?= esc_html($position_2_name) ?></span>
</a>
</li> -->
        <?php if ($category_name): ?>
            <li>
                <a href="<?= esc_url($category_url) ?>" title="<?= esc_attr($category_name) ?>">
                    <span><?= esc_html($category_name) ?></span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($region_name): ?>
            <li>
                <a href="<?= esc_url($region_url) ?>" title="<?= esc_attr($region_name) ?>">
                    <span><?= esc_html($region_name) ?></span>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="<?= esc_url(get_the_permalink()) ?>" title="<?= esc_attr(get_the_title()) ?>">
                <span><?= esc_html(get_the_title()) ?></span>
            </a>
        </li>
        <li>
            <span class="breadcrumb_last">
                Tập <?= esc_html(episodeName()) ?>
            </span>
        </li>
    <?php elseif (is_single() && get_post_type() === 'ophim'): ?>
        <!-- <li>
<a href="<?= esc_url(get_post_type_archive_link('ophim')) ?>" title="<?= esc_attr($position_2_name) ?>">
<span><?= esc_html($position_2_name) ?></span>
</a>
</li> -->
        <?php if ($category_name): ?>
            <li>
                <a href="<?= esc_url($category_url) ?>" title="<?= esc_attr($category_name) ?>">
                    <span><?= esc_html($category_name) ?></span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($region_name): ?>
            <li>
                <a href="<?= esc_url($region_url) ?>" title="<?= esc_attr($region_name) ?>">
                    <span><?= esc_html($region_name) ?></span>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <span class="breadcrumb_last"><?= esc_html(get_the_title()) ?></span>
        </li>
    <?php elseif (is_archive()): ?>
        <li>
            <span class="breadcrumb_last">Kho Hoạt Hình Anime Hay</span>
        </li>
    <?php endif; ?>
</ul>

<style>
    .breadcrumb {
        display: flex;
        list-style: none;
        padding: 10px 15px;
        margin: 1rem 0;
        font-size: 16px;
        background: #1a1a20;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .breadcrumb li {
        display: flex;
        align-items: center;
    }

    .breadcrumb li+li::before {
        content: "/";
        margin: 0 8px;
        color: #f1b722;
    }

    .breadcrumb a {
        text-decoration: none;
        color: #f1b722;
        transition: color 0.2s;
    }

    .breadcrumb a:hover,
    .breadcrumb a:hover i {
        color: #0056b3;
        text-decoration: underline;
    }

    .breadcrumb .breadcrumb_last {
        color: #ddd;
    }

    .breadcrumb i {
        margin-right: 5px;
        color: #f1b722;
    }

    @media (max-width: 768px) {
        .breadcrumb {
            font-size: 14px;
        }

        .breadcrumb li+li::before {
            margin: 0 5px;
        }
    }
</style>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "@id": "https://<?php echo esc_js($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>#breadcrumb",
        "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "<?php echo esc_js($position_1_name); ?>",
                "item": "<?php echo esc_url($position_1_url); ?>"
            }
            <?php if (is_tax()):
                $taxonomy = get_queried_object()->taxonomy;
                $term_name = single_tag_title('', false);
                $prefix = $taxonomies[$taxonomy] ?? '';
            ?>, {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "<?php echo esc_js($prefix . $term_name); ?>",
                    "item": "<?php echo esc_url(get_term_link(get_queried_object())); ?>"
                }
            <?php elseif (is_search()): ?>, {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Kết quả tìm kiếm: <?php echo esc_js(get_search_query()); ?>",
                    "item": "<?php echo esc_url(home_url('/?s=' . urlencode(get_search_query()))); ?>"
                }
            <?php elseif (isEpisode()): ?>
                <?php if ($category_name): ?>, {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "<?php echo esc_js($category_name); ?>",
                        "item": "<?php echo esc_url($category_url); ?>"
                    }
                <?php endif; ?>
                <?php if ($region_name): ?>, {
                        "@type": "ListItem",
                        "position": <?php echo $category_name ? 3 : 2; ?>,
                        "name": "<?php echo esc_js($region_name); ?>",
                        "item": "<?php echo esc_url($region_url); ?>"
                    }
                <?php endif; ?>, {
                    "@type": "ListItem",
                    "position": <?php echo ($category_name && $region_name) ? 4 : ($category_name || $region_name) ? 3 : 2; ?>,
                    "name": "<?php echo esc_js(get_the_title()); ?>",
                    "item": "<?php echo esc_url(get_the_permalink()); ?>"
                }, {
                    "@type": "ListItem",
                    "position": <?php echo ($category_name && $region_name) ? 5 : ($category_name || $region_name) ? 4 : 3; ?>,
                    "name": "Tập <?php echo esc_js(episodeName()); ?>",
                    "item": "<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>"
                }
            <?php elseif (is_single() && get_post_type() === 'ophim'): ?>
                <?php if ($category_name): ?>, {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "<?php echo esc_js($category_name); ?>",
                        "item": "<?php echo esc_url($category_url); ?>"
                    }
                <?php endif; ?>
                <?php if ($region_name): ?>, {
                        "@type": "ListItem",
                        "position": <?php echo $category_name ? 3 : 2; ?>,
                        "name": "<?php echo esc_js($region_name); ?>",
                        "item": "<?php echo esc_url($region_url); ?>"
                    }
                <?php endif; ?>, {
                    "@type": "ListItem",
                    "position": <?php echo ($category_name && $region_name) ? 4 : ($category_name || $region_name) ? 3 : 2; ?>,
                    "name": "<?php echo esc_js(get_the_title()); ?>",
                    "item": "<?php echo esc_url(get_the_permalink()); ?>"
                }
            <?php elseif (is_archive()): ?>, {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Kho Hoạt Hình Anime Hay",
                    "item": "<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>"
                }
            <?php endif; ?>
        ]
    }
</script>