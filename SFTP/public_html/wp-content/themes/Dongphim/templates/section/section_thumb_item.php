<li>
	<a class="movie-item m-block" href="<?php the_permalink(); ?>"
	  title="<?php the_title(); ?> - <?= op_get_original_title() ?> (<?= op_get_year() ?>)">
		<div class="block-wrapper">
      <img clas="public-film-item-thumb ratio-content" 
            src=" <?= op_get_thumb_url();?>"
            alt="<?php the_title(); ?> - <?= op_get_original_title() ?> (<?= op_get_year() ?>)" >
			<div class="movie-meta">
				<div class="movie-title-1"><?php the_title(); ?></div>
				<span class="movie-title-2"><?= op_get_original_title() ?>
					(<?= op_get_year() ?>)
				</span>

<?php
  $fmt    = op_get_meta('movie_formality');
  $status = op_get_meta('movie_status');
?>
<span class="ribbon">
	<?php if($status == "ongoing" && (($fmt == 'tv_series' || $fmt == 'series'))): ?>
			<?= op_get_episode() ?> / <?= op_get_total_episode() ?>
	<?php elseif ($status == "completed" && (($fmt == 'tv_series' || $fmt == 'series'))): ?>
			<?= op_get_episode() ?>
		<?php else:  ?>
			<?= op_get_quality() ?> <?= op_get_lang() ?>
  <?php endif; ?>
</span>

			</div>
		</div>
	</a>
</li>