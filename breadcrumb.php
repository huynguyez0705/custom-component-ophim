<?php include( get_template_directory() . '/breadcrumb.php' ); ?>

<ul class="breadcrumb">
    <li>
        <a class="" title="Xem Phim" href="/">
            <span class="">
                <i class="fa fa-home"></i> Motchill
            </span>
        </a>
    </li>
    <?php if (is_tax()) { ?>
    <li>
        <span class="breadcrumb_last">
            <?= single_tag_title(); ?>
        </span>
    </li>
    <?php } else if(is_archive()) { ?>
    <li>
        <span class="breadcrumb_last">
            Kho Phim Mới
        </span>
    </li>
    <?php } else { ?>
    <?php 
    $categories = get_the_terms(get_the_ID(), "ophim_categories");
    if (!empty($categories) && !is_wp_error($categories)) {
        $term = reset($categories); ?>
    <li>
        <a href="<?php echo home_url('/') . (get_option('ophim_slug_categories') ? get_option('ophim_slug_categories') : 'categories') . '/' . $term->slug . '/'; ?>" 
          title="<?php echo esc_attr($term->name); ?>">
            <span>
                <?php echo esc_html($term->name); ?>
            </span>
        </a>
    </li>
    <?php } ?>
    <?php if (isEpisode()) { ?>
    <li>
        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
            <span><?php the_title(); ?></span>
        </a>
    </li>
    <li>
        <span class="breadcrumb_last"><?php the_title(); ?> - Tập <?= episodeName() ?></span>
    </li>
    <?php } elseif (is_single() && get_post_type() == 'ophim') { ?>
    <li>
        <span class="breadcrumb_last"><?php the_title(); ?></span>
    </li>
    <?php } ?>
    <?php } ?>
</ul>

<style>
.breadcrumb { display:flex; list-style:none; padding: 1rem; margin:1rem 0; font-size:14px; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.1);font-weight:bold; flex-wrap: wrap;
        row-gap: 2rem;}
.breadcrumb li { display:flex; align-items:center; }
.breadcrumb li+li::before { content:"»"; margin:0 8px; color:#da966e;font-size:18px; }
.breadcrumb a { text-decoration:none; color:#d88a5f; transition:color 0.2s; }
.breadcrumb a:hover { color:#0056b3;}
.breadcrumb .breadcrumb_last { color:#ddd; }
.breadcrumb i { margin-right:5px; color:#d88a5f; }
@media(max-width:768px) {
  .breadcrumb { font-size:14px; } .breadcrumb li+li::before { margin:0 5px; }
} 

/* .breadcrumb li a{background:#da966e;color:#fff;padding:10px 1.2rem 10px 1.5rem;position:relative;margin-right:.3rem;font-size:14px;font-weight:bold;overflow:hidden}
.breadcrumb li a::before{content:'';position:absolute;top:50%;left:0;border-top:20px solid transparent;border-bottom:20px solid transparent;border-left:20px solid #151414;z-index:1;transform:translateY(-50%)}
.breadcrumb li a::after{content:'';position:absolute;top:50%;right:0;border-top:20px solid transparent;border-bottom:20px solid transparent;border-left:20px solid #da966e;z-index:2;transform:translate(90%,-50%)}

.breadcrumb .breadcrumb_last{background:#fff;color:#da966e;padding:10px 1.2rem 10px 1.5rem;font-size:14px;font-weight:bold;position:relative}
.breadcrumb .breadcrumb_last::before{content:'';position:absolute;top:50%;left:0;border-top:20px solid transparent;border-bottom:20px solid transparent;border-left:20px solid #151414;z-index:1;transform:translateY(-50%)}
.breadcrumb .breadcrumb_last::after{content:'';position:absolute;top:50%;right:0;border-top:20px solid transparent;border-bottom:20px solid transparent;border-left:20px solid #fff;z-index:2;transform:translate(90%,-50%)}

.breadcrumb li a:hover{background:#fff}
.breadcrumb li a:hover::after{border-left:20px solid #fff} */
</style>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Motchill",
      "item":  "<?= home_url(); ?>"
    },
    <?php if (is_tax()) { ?>
    {
      "@type": "ListItem",
      "position": 2,
      "name": "<?= single_tag_title('', false); ?>",
      "item": "<?= get_term_link(get_queried_object()); ?>"
    }
    <?php } else if (is_archive()) { ?>
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Kho Phim Mới",
      "item": "<?= get_term_link(get_queried_object()); ?>"
    }
    <?php } else { 
      $categories = get_the_terms(get_the_ID(), "ophim_categories");
      if (!empty($categories) && !is_wp_error($categories)) {
        $term = reset($categories);
    ?>
    {
      "@type": "ListItem",
      "position": 2,
      "name": "<?= esc_html($term->name); ?>",
      "item": "<?= home_url('/') . (get_option('ophim_slug_categories') ? get_option('ophim_slug_categories') : 'categories') . '/' . $term->slug . '/'; ?>"
    }
    <?php } ?>
    <?php if (isEpisode()) { ?>
    ,
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?php the_title(); ?>",
      "item": "<?php the_permalink(); ?>"
    },
    {
      "@type": "ListItem",
      "position": 4,
      "name": "<?php the_title(); ?> - Tập <?= episodeName() ?>"
    }
    <?php } elseif (is_single() && get_post_type() == 'ophim') { ?>
    ,
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?php the_title(); ?>",
      "item": "<?= get_permalink(get_the_ID()); ?>"
    }
    <?php } ?>
    <?php } ?>
  ]
}
</script>