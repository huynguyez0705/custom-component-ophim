<?php
$current_url = home_url($_SERVER['REQUEST_URI']);
$video_embed_url = embedEpisodeUrl(); // URL video embed
$video_m3u8_url = m3u8EpisodeUrl(); // URL m3u8 video
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Movie",
  "name": "<?php the_title(); ?> - Tập <?= episodeName(); ?>",
  "description": "<?php echo wp_strip_all_tags(get_the_excerpt()); ?>"
  "image": "<?= esc_url(op_get_poster_url()); ?>",
  "datePublished": "<?= esc_html(op_get_year()); ?>",
  "dateCreated": "<?= get_the_date('Y-m-d'); ?>",
  "url": "<?= esc_html($current_url); ?>",
  "director": [
    <?php
    $directors = get_the_terms(get_the_ID(), "ophim_directors") ?: [];
    foreach ($directors as $key => $director) {
        echo ($key > 0 ? ',' : '') . '{
          "@type": "Person",
          "name": "' . (!empty($director->name) ? esc_html($director->name) : 'Đang cập nhật') . '",
          "url": "' . (!empty(get_term_link($director)) ? esc_url(get_term_link($director)) : 'Đang cập nhật') . '"
        }';
    }
    if (empty($directors)) echo '{"@type": "Person", "name": "Đang cập nhật", "url": "Đang cập nhật"}';
    ?>
  ],
	"actor": [
		<?php
		$actors = get_the_terms(get_the_ID(), "ophim_actors") ?: [];
		foreach ($actors as $key => $actor) {
			echo ($key > 0 ? ',' : '') . '{
			  "@type": "Person",
			  "name": "' . (!empty($actor->name) ? esc_html($actor->name) : 'Đang cập nhật') . '",
			  "url": "' . (!empty(get_term_link($actor)) ? esc_url(get_term_link($actor)) : 'Đang cập nhật') . '"
			}';
		}
		if (empty($actors)) echo '{"@type": "Person", "name": "Đang cập nhật", "url": "Đang cập nhật"}';
		?>
	  ],
"genre": "<?= esc_html(implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), 'ophim_genres') ?: [], 'name')) ?: 'Đang cập nhật'); ?>",
 "aggregateRating": {
    "@type": "AggregateRating",
   "ratingValue": "<?= (op_get_rating() > 0) ? esc_html(op_get_rating()) : '10'; ?>",
    "ratingCount": "<?= (op_get_rating_count() > 0) ? esc_html(op_get_rating_count()) : '10'; ?>",
    "bestRating": "10",
    "worstRating": "1"
  }
  },
  "video": {
    "@type": "VideoObject",
    "name": "<?php the_title(); ?> - Tập <?= episodeName(); ?>",
     "description": "<?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>",
    "thumbnailUrl": "<?= esc_url(op_get_thumb_url()); ?>",
    "uploadDate": "<?= get_the_date('c'); ?>",
    "contentUrl": "<?= esc_url($video_m3u8_url); ?>",
    "embedUrl": "<?= esc_url($video_embed_url); ?>",
    "interactionStatistic": {
      "@type": "InteractionCounter",
      "interactionType": "https://schema.org/WatchAction",
      "userInteractionCount": "<?= (op_get_rating_count() > 0) ? esc_html(op_get_rating_count()) : '10'; ?>"
    }
  }
}
</script>