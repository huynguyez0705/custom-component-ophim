<?php
get_header();
?>
<div id="main-content">
    <?php if(get_option('ophim_is_ads') == 'on') { ?>
        <div id=top_addd></div>
    <?php } ?>

    <div id="content">
        <div class="container">
            <div class="container filter-page">
                <div class="block">
                    <div class="text" style="margin: 0 0 10px 0;overflow: hidden;padding: 5px 10px;list-style: none;background-color: #302e2e;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">
                        <?php include( get_template_directory() . '/breadcrumb.php' ); ?>
                    </div>
                    <div class="clear"></div>
                    <h1 class="caption">Kết quả tìm kiếm : <?php echo "$s"; ?></h1>
                    <div id="binlist">
                        <ul class="list-film horizontal">
                            <?php
                            if (have_posts()) {
                                while (have_posts()) {
                                    the_post(); ?>
                                    <li class="item small">
                                        <span class="label">
                                            <div class="status"><?= op_get_total_episode() ?> | <?= op_get_lang() ?>| <?= op_get_quality() ?></div>
                                        </span>
                                        <a title="<?php the_title(); ?> - <?= op_get_original_title() ?>" href="<?php the_permalink(); ?>"
                                           style="height: 133.875px;">
                                            <img alt="<?php the_title(); ?> - <?= op_get_original_title() ?>"
                                                 src="<?= op_get_poster_url() ?>">
                                            <h3><?php the_title(); ?></h3> <i class="icon-play"></i>
                                        </a>
                                    </li>
                                <?php }
                                wp_reset_postdata();
                            } ?>

                        </ul>
                        <?php if (!have_posts()) { ?>
                            <p>Không có phim nào cho mục này...</p>
                        <?php } ?>
                        <div class="clear"></div>
                        <div class="pagination">
                            <?php ophim_pagination(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>

