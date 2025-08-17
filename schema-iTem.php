<?php
	$term = get_queried_object();
	$movies = new WP_Query([
		'post_type' => 'ophim',
		'tax_query' => [['taxonomy' => $term->taxonomy, 'field' => 'slug', 'terms' => $term->slug]],
	]);

	if ($movies->have_posts()) :
	?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "@id": "<?=  esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>#ItemList",
    "name": "<?= esc_html($term->name); ?>",
    "description": "<?= esc_html($term->description); ?>",
    "url": "<?=  esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>",
    "itemListOrder": "Descending",
    "numberOfItems": "<?= $movies->post_count; ?>",
    "itemListElement": [
        <?php
$index = 1;
while ($movies->have_posts()) : $movies->the_post();
$movie = [
    'name' => esc_html(get_the_title()),
    'url' => esc_url(get_permalink()),
    'image' => esc_url(function_exists('op_get_thumb_url') ? op_get_thumb_url() : ''),
    'description' => esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))),
    'datePublished' => esc_html(op_get_year()),
    'dateCreated' => get_the_date('Y-m-d'),
    'director' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_directors") ?: [], 'name')) ?: 'Đang cập nhật',
    'actor' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_actors") ?: [], 'name')) ?: 'Đang cập nhật',
    'genre' => implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), "ophim_genres") ?: [], 'name')) ?: 'Đang cập nhật',
    'ratingValue' => (op_get_rating() > 0) ? op_get_rating() : '10',
    'ratingCount' => (op_get_rating_count() > 0) ? op_get_rating_count() : rand(5, 100),
];
    ?> {
            "@type": "ListItem",
            "position": <?= $index; ?>,
            "item": {
                "@type": "Movie",
                "name": "<?= $movie['name']; ?>",
                "url": "<?= $movie['url']; ?>",
                "image": "<?= $movie['image']; ?>",
                "description": "<?= $movie['description']; ?>",
                "dateCreated": "<?= $movie['dateCreated']; ?>",
                "genre": "<?= $movie['genre']; ?>",
                "director": {
                    "@type": "Person",
                    "name": "<?= $movie['director']; ?>"
                },
                "actor": [
                    <?= implode(',', array_map(fn($actor) => '{"@type": "Person", "name": "' . esc_html($actor) . '"}', explode(', ', $movie['actor']))); ?>
                ]
            }
        }
        <?= $movies->current_post + 1 < $movies->post_count ? ',' : ''; ?>
        <?php $index++; endwhile; wp_reset_postdata(); ?>
    ]
}
</script>
<?php endif; ?>