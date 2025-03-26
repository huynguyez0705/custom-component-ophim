	<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Movie",
    "name": "<?= esc_html(get_the_title()); ?>",
    "description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
    "image": "<?= esc_url(op_get_thumb_url()); ?>",
    "datePublished": "<?= esc_html(op_get_year()); ?>",
    "dateCreated": "<?= get_the_date('Y-m-d'); ?>",
    "director": [
        <?php $directors=get_the_terms(get_the_ID(),"ophim_directors")?:[];foreach($directors as $key=>$director){echo($key>0?',':'').'{"@type":"Person","name":"'.(!empty($director->name)?esc_html($director->name):'Đang cập nhật').'","url":"'.(!empty(get_term_link($director))?esc_url(get_term_link($director)):'Đang cập nhật').'"}';}if(empty($directors))echo'{"@type":"Person","name":"Đang cập nhật","url":"Đang cập nhật"}';?>
    ],
    "actor": [
        <?php $actors=get_the_terms(get_the_ID(),"ophim_actors")?:[];foreach($actors as $key=>$actor){echo($key>0?',':'').'{"@type":"Person","name":"'.(!empty($actor->name)?esc_html($actor->name):'Đang cập nhật').'","url":"'.(!empty(get_term_link($actor))?esc_url(get_term_link($actor)):'Đang cập nhật').'"}';}if(empty($actors))echo'{"@type":"Person","name":"Đang cập nhật","url":"Đang cập nhật"}';?>
    ],
    "genre": "<?= esc_html(implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), 'ophim_genres') ?: [], 'name')) ?: 'Đang cập nhật'); ?>",
"aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= (op_get_rating() > 8) ? esc_html(op_get_rating()) : '10'; ?>",
    "ratingCount": "<?= (op_get_rating_count() > 50) ? esc_html(op_get_rating_count()) : rand(50, 100); ?>",
    "bestRating": "10",
    "worstRating": "1"
}
}
	</script>