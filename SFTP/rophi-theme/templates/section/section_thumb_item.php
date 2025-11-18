<div class="<?= $xClass ?>">
	<a class ="thumb-v" href="<?php the_permalink(); ?>" title="Phim <?php the_title(); ?>">
		<img class="img-film lazy" data-original="<?= op_get_thumb_url() ?>" title="Phim <?php the_title(); ?>" alt="Phim <?php the_title(); ?>" />
		<span class="icon-play"></span>
		<span class="label"><?= op_get_episode() ?> <?= op_get_lang() ?></span>
	</a>
	<div class="info">
		<h3  class="name split-1"><?php the_title(); ?></h3>
		<div class="name-real split-1"><?= op_get_original_title() ?></div>
	</div>
</div>
