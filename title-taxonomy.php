<h1>
<?php
$taxonomies = array( 'ophim_categories' => '', 'ophim_directors' => 'Phim của đạo diễn ','ophim_years' => 'Phim năm ','ophim_actors' => 'Phim của diễn viên ','ophim_regions' => 'Phim quốc gia ','ophim_genres' => 'Phim thể loại ' );
$current_tax = get_queried_object()->taxonomy;
if (array_key_exists($current_tax, $taxonomies)) {
echo $taxonomies[$current_tax] . single_tag_title('', false);
} else {
echo 'Phim ' . single_tag_title('', false);
}
?>
</h1>