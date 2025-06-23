<?php
$taxonomies = [
    'ophim_categories' => '',
    'ophim_directors' => 'Phim của đạo diễn ',
    'ophim_years' => 'Phim năm ',
    'ophim_actors' => 'Phim của diễn viên ',
    'ophim_regions' => 'Phim quốc gia ',
    'ophim_genres' => 'Phim thể loại '
];
$current_tax = get_queried_object()->taxonomy ?? '';
$term_name = single_tag_title('', false);
$prefix = $taxonomies[$current_tax] ?? 'Phim ';
$title = esc_html($prefix . $term_name);
?>

<h1><?php echo $title; ?></h1>
