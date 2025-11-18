<?php
get_header();
?>
<div class="container">
  <?php include(get_template_directory() . '/breadcrumb.php'); ?>
  <?php include(get_template_directory() . '/filter.php'); ?>
  <div class="content-box">
    <div class="left-content flex-1" id="page-info">
      <div class="list-films film-new">
        <div class="card-h">
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
    <div class="right-content">
      <?php get_sidebar('ophim'); ?>
    </div>
  </div>

</div>
<?php
get_footer();
?>