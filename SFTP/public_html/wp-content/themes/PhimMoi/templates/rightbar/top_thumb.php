<div class="right-box top-film-week">
    <h2 class="right-box-header star-icon"><span><?= $title; ?></span></h2>
    <div class="right-box-content">
        <ul class="list-top-movie" id="list-top-film-week">
            <?php $loop = 0; while ($query->have_posts()) : $query->the_post(); $loop++; ?>
                <li class="list-top-movie-item movie-item-<?= $loop; ?>" id="list-top-movie-item-1">
                    <a class="list-top-movie-link link-movie-<?= $loop; ?>" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                       <img src="<?= ($loop == 1) ? op_get_poster_url() : op_get_thumb_url() ?>" alt="Phim <?php the_title(); ?>">
                        <div class="list-top-movie-item-info">
                            <span class="list-top-movie-item-vn"><?php the_title(); ?></span>
                            <span class="list-top-movie-item-en"><?= op_get_original_title() ?></span>
                            <span class="list-top-movie-item-view"><?= op_get_post_view() ?> lượt xem</span>
                            <span class="rate-vote rate-vote-<?= number_format(op_get_rating() ?? 0, 0)?>"></span>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>
<div class="clear"></div>