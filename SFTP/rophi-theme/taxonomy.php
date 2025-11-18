<?php
get_header();
?>
<div class="container">
  <?php include(get_template_directory() . '/breadcrumb.php'); ?>
  <?php include(get_template_directory() . '/filter.php'); ?>
  <div class="content-box">
    <div class="left-content flex-1" id="page-info">
      <?php
      $taxonomies = [
        'ophim_categories' => '',
        'ophim_directors' => 'Đạo Diễn ',
        'ophim_years' => 'Phim Năm ',
        'ophim_actors' => 'Diễn Viên ',
        'ophim_regions' => 'Phim ',
        'ophim_genres' => 'Phim '
      ];
      $current_tax = get_queried_object()->taxonomy ?? '';
      $term_name = single_tag_title('', false);
      $prefix = $taxonomies[$current_tax] ?? 'Phim ';
      $title = esc_html($prefix . $term_name);
      ?>
      <div class="box-header">
        <h1 class="box-title"><?php echo $title; ?></h1>
      </div>
      <div class="list-films film-new">
        <div class="card-h wide">
          <?php $key = 0;
          if (have_posts()) {
            while (have_posts()) {
              the_post();
              $xClass = 'item';
              include THEMETEMPLADE . '/section/section_thumb_item.php';
            }
            wp_reset_postdata();
          } else { ?>
            <p>Rất tiếc, không có nội dung nào trùng khớp yêu cầu</p>
          <?php } ?>
        </div>
        <div class="pagination">
          <?php ophim_pagination(); ?>
        </div>
      </div>
    </div>
    <div class="right-content hide">
      <?php get_sidebar('ophim'); ?>
    </div>
  </div>
</div>
<?php
$term = get_queried_object();
$movies = new WP_Query([
  'post_type' => 'ophim',
  'tax_query' => [['taxonomy' => $term->taxonomy, 'field' => 'slug', 'terms' => $term->slug]],
  'paged' => get_query_var('paged') ? get_query_var('paged') : 1,

]);

if ($movies->have_posts()) :
?>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "@id": "<?= esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>#ItemList",
      "name": "<?= esc_html($term->name); ?>",
      "description": "<?= esc_html($term->name); ?> 2025 tại MotPhimTV. Xem Phim Không Quảng Cáo",
      "url": "<?= esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>",
      "itemListOrder": "Descending",
      "numberOfItems": "<?= $movies->post_count; ?>",
      "itemListElement": [
        <?php
        $index = 1;
        while ($movies->have_posts()) : $movies->the_post();
          $movie = [
            'name' => esc_html(get_the_title()),
            'url' => esc_url(get_permalink()),
            'image' => esc_url(function_exists('op_get_thumb_url') ? op_get_thumb_url() : ''),
            'description' => esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))),
            'datePublished' => esc_html(op_get_year()),
            'dateCreated' => get_the_date('Y-m-d'),
            'director' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_directors") ?: [], 'name')) ?: 'Đang cập nhật',
            'actor' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_actors") ?: [], 'name')) ?: 'Đang cập nhật',
            'genre' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_genres") ?: [], 'name')) ?: 'Đang cập nhật',
            'ratingValue' => (op_get_rating() > 0) ? op_get_rating() : '10',
            'ratingCount' => (op_get_rating_count() > 0) ? op_get_rating_count() : rand(5, 100),
          ];
        ?> {
            "@type": "ListItem",
            "position": <?= $index; ?>,
            "item": {
              "@type": "Movie",
              "name": "<?= $movie['name']; ?>",
              "url": "<?= $movie['url']; ?>",
              "image": "<?= $movie['image']; ?>",
              "description": "<?= $movie['description']; ?>",
              "dateCreated": "<?= $movie['dateCreated']; ?>",
              "genre": "<?= $movie['genre']; ?>",
              "director": {
                "@type": "Person",
                "name": "<?= $movie['director']; ?>"
              },
              "actor": [
                <?= implode(',', array_map(fn($actor) => '{"@type": "Person", "name": "' . esc_html($actor) . '"}', explode(', ', $movie['actor']))); ?>
              ]
            }
          }
          <?= $movies->current_post + 1 < $movies->post_count ? ',' : ''; ?>
        <?php $index++;
        endwhile;
        wp_reset_postdata(); ?>
      ]
    }
  </script>
<?php endif; ?>
<?php
get_footer();
?>