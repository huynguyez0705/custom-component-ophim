<?php
$current_url = home_url($_SERVER['REQUEST_URI']);
$trailer_url = op_get_trailer_url();
$trailer_embed_url = $trailer_url && ($video_id = (parse_str(parse_url($trailer_url, PHP_URL_QUERY), $vars) ?: $vars['v'] ?? null)) ? "https://www.youtube.com/embed/{$video_id}" : null;

?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
 "@type": "<?php 
    $meta_formality = get_post_meta(get_the_ID(), 'ophim_movie_formality', true);
    $categories = get_the_terms(get_the_ID(), 'ophim_categories');
    $is_phim_bo = $categories && in_array('Phim bộ', wp_list_pluck($categories, 'name'));
    
    if ($meta_formality === 'tv_series' && $is_phim_bo && get_post_type() === 'ophim') {
        echo 'TVSeries';
    } else {
        echo 'Movie';
    }
?>",
  "name": "Phim <?= esc_html(get_the_title()); ?> - <?= op_get_original_title() ?> <?= op_get_year() ?> <?= op_get_lang() ?> <?= op_get_quality() ?> - Tập <?= episodeName(); ?> - SubNhanh",
"description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
  "image": "<?= esc_url(home_url(op_get_thumb_url())); ?>",
  "datePublished": "<?= esc_html(op_get_year()); ?>",
  "dateCreated": "<?= get_the_date('Y-m-d'); ?>",
  "url": "<?= esc_html($current_url); ?>",
  "director": [<?= implode(',', array_map(fn($d) => '{"@type": "Person", "name": "' . esc_html($d->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($d) ?: 'Đang cập nhật') . '"}', get_the_terms(get_the_ID(), 'ophim_directors') ?: [new stdClass()])) ?>],
  "actor": [<?= implode(',', array_map(fn($a) => '{"@type": "Person", "name": "' . esc_html($a->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($a) ?: 'Đang cập nhật') . '"}', get_the_terms(get_the_ID(), 'ophim_actors') ?: [new stdClass()])) ?>],
  "genre": [<?= !empty($genres = get_the_terms(get_the_ID(), 'ophim_genres')) ? '"' . implode('","', array_map('esc_html', wp_list_pluck($genres, 'name'))) . '"' : '"Đang cập nhật"' ?>],
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= op_get_rating(); ?>",
    "ratingCount": "<?= op_get_rating_count(); ?>",
    "bestRating": "10",
    "worstRating": "1"
  }<?php if ($trailer_url && $trailer_embed_url) : ?>,
  "trailer": {
    "@type": "VideoObject",
    "name": "Trailer <?= esc_html(get_the_title()); ?>",
  "description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
    "thumbnailUrl": "<?= esc_url(home_url(op_get_poster_url())); ?>",
    "uploadDate": "<?= get_the_date('c'); ?>",
    "contentUrl": "<?= esc_url($trailer_url); ?>",
    "embedUrl": "<?= esc_url($trailer_embed_url); ?>"
  }<?php endif; ?>,
  "video": {
    "@type": "VideoObject",
    "name": "<?= esc_html(get_the_title()); ?> - Tập <?= episodeName(); ?>",
    "description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
    "thumbnailUrl": "<?= esc_url(home_url(op_get_poster_url())); ?>",
    "uploadDate": "<?= get_the_date('c'); ?>",
    "contentUrl": "<?= m3u8EpisodeUrl(); ?>",
    "embedUrl": "<?= embedEpisodeUrl(); ?>",
    "interactionStatistic": {
      "@type": "InteractionCounter",
      "interactionType": "https://schema.org/WatchAction",
      "userInteractionCount": "<?= esc_html(op_get_rating_count()); ?>"
    }
  }
}
</script>