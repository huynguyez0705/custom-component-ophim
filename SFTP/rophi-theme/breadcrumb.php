<?php
function breadcrumb_home(&$json_breadcrumb)
{
  global $name;
  $name = 'Motchill';
  $url = home_url();
  $json_breadcrumb[] = [
    '@type' => 'ListItem',
    'position' => 1,
    'name' => 'Trang chủ',
    'item' => esc_url($url)
  ];
?>
  <li>
    <a href="<?php echo esc_url($url); ?>">
      <i class="fa fa-home"></i> <span><?php echo esc_html($name); ?></span>
    </a>
    <i class="fa fa-caret-right"></i>
  </li>
  <?php
}

function breadcrumb_cate(&$json_breadcrumb)
{
  breadcrumb_home($json_breadcrumb);
  if (isEpisode() || (is_single() && get_post_type() === 'ophim')) {
    $categories = get_the_terms(get_the_ID(), 'ophim_categories');
    $name_cate = $categories && !is_wp_error($categories) ? $categories[0]->name : 'Đang cập nhật';
    $url_cate = $categories && !is_wp_error($categories) ? get_term_link($categories[0]) : '#';
    $json_breadcrumb[] = [
      '@type' => 'ListItem',
      'position' => count($json_breadcrumb) + 1,
      'name' => esc_html($name_cate),
      'item' => esc_url($url_cate)
    ];
  ?>
    <li>
      <a href="<?php echo esc_url($url_cate); ?>">
        <span><?php echo esc_html($name_cate); ?></span>
      </a>
      <i class="fa fa-caret-right"></i>
    </li>
  <?php
  } else {
    $cate_name = single_tag_title('', false);
    $cate_url = get_term_link(get_queried_object());
    $paged = get_query_var('paged') ?: 1;
    $json_breadcrumb[] = [
      '@type' => 'ListItem',
      'position' => count($json_breadcrumb) + 1,
      'name' => esc_html($cate_name),
      'item' => esc_url($cate_url)
    ];
  ?>
    <li>
      <?php if ($paged == 1) { ?>
        <span class="breadcrumb_last"><?php echo esc_html($cate_name); ?></span>
      <?php } else { ?>
        <a href="<?php echo esc_url($cate_url); ?>">
          <span><?php echo esc_html($cate_name); ?></span>
        </a>
        <i class="fa fa-caret-right"></i>
      <?php } ?>
    </li>
    <?php if ($paged > 1) { ?>
      <li>
        <span class="breadcrumb_last">Trang <?php echo $paged; ?></span>
      </li>
    <?php
      $json_breadcrumb[] = [
        '@type' => 'ListItem',
        'position' => count($json_breadcrumb) + 1,
        'name' => 'Trang ' . $paged,
        'item' => esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])
      ];
    }
    ?>
  <?php
  }
}

function breadcrumb_single(&$json_breadcrumb)
{
  breadcrumb_cate($json_breadcrumb);
  global $post;
  $title = get_the_title($post->ID);
  $url_post = get_permalink($post->ID);
  $json_breadcrumb[] = [
    '@type' => 'ListItem',
    'position' => count($json_breadcrumb) + 1,
    'name' => esc_html($title),
    'item' => esc_url($url_post)
  ];
  ?>
  <li>
    <?php if (isEpisode()) { ?>
      <a href="<?php echo esc_url($url_post); ?>">
        <span><?php echo esc_html($title); ?></span>
      </a>
      <i class="fa fa-caret-right"></i>
    <?php } else { ?>
      <span class="breadcrumb_last"><?php echo esc_html($title); ?></span>
    <?php } ?>
  </li>
<?php
}

function breadcrumb_episode(&$json_breadcrumb)
{
  breadcrumb_single($json_breadcrumb);
  $title_epis = 'Tập ' . episodeName();
  $url_epis = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $json_breadcrumb[] = [
    '@type' => 'ListItem',
    'position' => count($json_breadcrumb) + 1,
    'name' => esc_html($title_epis),
    'item' => esc_url($url_epis)
  ];
?>
  <li>
    <span class="breadcrumb_last"><?php echo esc_html($title_epis); ?></span>
  </li>
<?php
}

// Render
$current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$taxonomy_list = ['ophim_categories', 'ophim_actors', 'ophim_genres', 'ophim_regions', 'ophim_tags', 'ophim_years', 'ophim_directors'];
$json_breadcrumb = [];
?>

<?php if (isEpisode()) { ?>
  <ul class="breadcrumb">
    <?php breadcrumb_episode($json_breadcrumb); ?>
  </ul>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_url($current_url); ?>#breadcrumb",
      "itemListElement": <?php echo json_encode($json_breadcrumb, JSON_UNESCAPED_SLASHES); ?>
    }
  </script>
<?php } elseif (is_single() && get_post_type() === 'ophim' && !isEpisode()) { ?>
  <ul class="breadcrumb">
    <?php breadcrumb_single($json_breadcrumb); ?>
  </ul>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_url($current_url); ?>#breadcrumb",
      "itemListElement": <?php echo json_encode($json_breadcrumb, JSON_UNESCAPED_SLASHES); ?>
    }
  </script>
<?php } elseif (is_tax($taxonomy_list)) { ?>
  <ul class="breadcrumb">
    <?php breadcrumb_cate($json_breadcrumb); ?>
  </ul>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_url($current_url); ?>#breadcrumb",
      "itemListElement": <?php echo json_encode($json_breadcrumb, JSON_UNESCAPED_SLASHES); ?>
    }
  </script>
<?php } elseif (is_search()) { ?>
  <ul class="breadcrumb">
    <?php breadcrumb_home($json_breadcrumb); ?>
    <li>
      <span>Tìm kiếm: <?php echo esc_html(get_search_query()); ?></span>
    </li>
  </ul>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_url($current_url); ?>#breadcrumb",
      "itemListElement": [
        <?php echo json_encode($json_breadcrumb[0], JSON_UNESCAPED_SLASHES); ?>,
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Tìm kiếm: <?php echo esc_html(get_search_query()); ?>",
          "item": "<?php echo esc_url(home_url('/?s=' . urlencode(get_search_query()))); ?>"
        }
      ]
    }
  </script>
<?php } elseif (is_archive() && !is_tax($taxonomy_list)) { ?>
  <ul class="breadcrumb">
    <?php breadcrumb_home($json_breadcrumb); ?>
    <li>
      <span>Kho Phim <?php echo htmlspecialchars($name); ?></span>
    </li>
  </ul>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_url($current_url); ?>#breadcrumb",
      "itemListElement": [
        <?php echo json_encode($json_breadcrumb[0], JSON_UNESCAPED_SLASHES); ?>,
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Kho Phim <?php echo htmlspecialchars($name); ?>",
          "item": "<?php echo esc_url($current_url); ?>"
        }
      ]
    }
  </script>
<?php } ?>

<style>
  :root {
    --color: #da966e;
    --text-base: #abb7c4;
  }

  .breadcrumb {
    display: flex;
    gap: 8px;
    padding: 1rem;
    background: #181818;
    font-size: 14px;
    margin: 1rem 0;
    color: var(--text-base);
  }

  .breadcrumb i {
    color: var(--color);
  }

  .breadcrumb a {
    color: var(--color);
    margin-right: 8px;
  }



  .breadcrumb>li+li:before {
    content: none;
  }

  .breadcrumb .breadcrumb_last {
    color: #fff;
  }
</style>