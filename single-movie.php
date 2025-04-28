<?php
get_header(); // Lấy header của theme
?>
<section class="section" id="post-<?php the_ID(); ?>">
  <div class="section-bg fill"> </div>
  <div class="section-content relative">
    <div class="row">
      <div id="col col-img" class="col medium-4 small-12 large-4">
        <div class="col-inner">
          <?php if (has_post_thumbnail()) : ?>
            <p>
              <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>" alt="<?php the_title(); ?>" class="movie-thumbnail">
            </p>
          <?php else : ?>
            <p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/default.jpg" alt="Default Thumbnail" class="movie-thumbnail">
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
</section>