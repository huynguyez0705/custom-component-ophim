<div class="top-thumb block">
  <div class="caption">
    <span class="uppercase"><?= $title; ?></span>
  </div>
  <div class="list-film card-v">
    <?php $loop = 0;
		while ($query->have_posts()) : $query->the_post();
			$loop++; ?>
    <div class="film-item-ver">
      <a href="<?php the_permalink(); ?>" title="Phim <?php the_title(); ?>" class="thumb-v">
        <img class="img-film" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" src="<?= op_get_thumb_url() ?>" />
      </a>
      <div class="info">
        <h3 class="name split-1">
          <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?> <?= op_get_year() ?>"><?php the_title(); ?></a>
        </h3>
        <div class="real-name"><?= op_get_year() ?></div>
        <div class="count_view"><?= op_get_post_view() ?> lượt xem</div>
        <p class="top-star" data-rating="<?= op_get_rating() * 10 ?>"></p>
      </div>

    </div>
    <?php endwhile; ?>
  </div>
</div>