<div class="top-text block">
    <div class="caption">
        <span class="uppercase"><?= $title; ?></span>
    </div>
    <div class="clear"></div>
    <ul class="list-film">
        <?php $loop = 0;
        while ($query->have_posts()) : $query->the_post();
            $loop++; ?>
            <li class="item">
                <div class="number-rank absolute">
                    <span><?= $loop ?></span>
                </div>
                <div class="info ">
                    <div class="name split-1"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
                    <div class="count_view"><?= op_get_post_view() ?> lượt xem</div>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>