<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php
// Định nghĩa biến cho "Xem Phim"
$position_1_name = 'AnimeHay';
$position_1_url  = home_url('/');
$position_2_name = "Anime";
?>
<ul class="breadcrumb-pm">
	<li>
		<a title="<?= esc_attr($position_1_name) ?>" href="<?= esc_url($position_1_url) ?>">
			<i class="fa fa-home"></i><?= esc_html($position_1_name) ?>
		</a>
	</li>

	<?php
	if ( is_tax() ) {
		// Trang taxonomy
		$taxonomies = [
			'ophim_categories' => '',
			'ophim_directors'  => 'Đạo Diễn: ',
			'ophim_years'      => 'Năm Phát Hành: ',
			'ophim_actors'     => 'Diễn Viên: ',
			'ophim_regions'    => 'Quốc Gia: ',
			'ophim_genres'     => 'Thể Loại: '
		];
		$taxonomy  = get_queried_object()->taxonomy;
		$term_name = single_tag_title('', false);
		$prefix    = $taxonomies[ $taxonomy ] ?? '';
		echo '<li><span class="breadcrumb_last">' 
			. esc_html( $prefix . $term_name ) 
			. '</span></li>';
	}
		   elseif ( is_search() ) {
			   // Trang kết quả tìm kiếm
			   echo '<li><span class="breadcrumb_last">'
				   . 'Kết quả tìm kiếm: ' . esc_html( get_search_query() ) 
				   . '</span></li>';
		   }
		   elseif ( isEpisode() ) {
			   // Trang tập → Archive → phim → tập
			   echo '<li><a href="' 
				   . esc_url( get_post_type_archive_link('ophim') ) 
				   . '" title=" ' . esc_attr( $position_2_name ) . '">'
				   . '<span>' . esc_html( $position_2_name ) . '</span> </a></li>' ;
			   echo '<li><a href="' 
				   . esc_url( get_the_permalink() ) 
				   . '" title="' . esc_attr( get_the_title() ) . '">'
				   . '<span>' . esc_html( get_the_title() ) . '</span></a></li>';
			   echo '<li><span class="breadcrumb_last">'
				   . 'Tập ' . esc_html( episodeName() ) 
				   . '</span></li>';
		   }
		   elseif ( is_single() && get_post_type() === 'ophim' ) {
			   // Trang phim đơn → link về Archive → title
			   echo
				   '<li><a href="' 
				   . esc_url( get_post_type_archive_link('ophim') ) 
				   . '" title="' . esc_attr( $position_2_name ) . '">'
				   . '<span>' . esc_html( $position_2_name ) . '</span>'
				   . '</a></li>';
			   echo '<li><span class="breadcrumb_last">'
				   . esc_html( get_the_title() ) 
				   . '</span></li>';
		   }
		   elseif ( is_archive() ) {
			   // Chỉ Trang archive chung
			   echo '<li><span class="breadcrumb_last"> Kho Hoạt Hình Anime Hay </span></li>';
		   }
	?>
</ul>


<style>
	.breadcrumb-pm{display:flex;list-style:none;padding:8px 15px;margin:0 0 1rem;font-size:14px;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.3);flex-wrap:wrap;row-gap:.5em;align-items:center;background:rgba(0,0,0,1)}
	.breadcrumb-pm li{display:flex;align-items:center}
	.breadcrumb-pm li + li::before{content:"\f0da";margin:0 8px;font: normal normal normal 14px / 1 FontAwesome;}
	.breadcrumb-pm a{text-decoration:none;color:#cd3176!important;transition:color 0.2s}
	.breadcrumb-pm .breadcrumb_last{color:#ddd!important}
	.breadcrumb-pm i{margin-right:5px;color:var(--color)}
	.watching-movie	.breadcrumb-pm {padding:8px 5px}
	@media (max-width:768px){.breadcrumb-pm li + li::before{margin:0 5px}}
</style>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "@id": "https://<?= esc_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>#breadcrumb",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "<?= esc_html($position_1_name) ?>",
      "item": "<?= esc_url($position_1_url) ?>"
    }
    <?php if ( is_tax() ): ?>
    ,{
      "@type": "ListItem",
      "position": 2,
      "name": "<?= esc_html( $prefix . $term_name ) ?>",
      "item": "<?= esc_url( get_term_link(get_queried_object()) ) ?>"
    }
    <?php elseif ( is_search() ): ?>
    ,{
      "@type": "ListItem",
      "position": 2,
      "name": "Kết quả tìm kiếm <?= esc_html( get_search_query() ) ?>",
      "item": "<?= esc_url( get_search_link() ) ?>"
    }
    <?php elseif ( isEpisode() ): ?>
    ,{
      "@type": "ListItem",
      "position": 2,
      "name": "<?= esc_html($position_2_name) ?>",
      "item": "<?= esc_url( get_post_type_archive_link('ophim') ) ?>"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?= esc_html( get_the_title() ) ?>",
      "item": "<?= esc_url( get_the_permalink() ) ?>"
    },
    {
      "@type": "ListItem",
      "position": 4,
      "name": "Tập <?= esc_html( episodeName() ) ?>",
      "item": "https://<?= esc_html( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ?>"
    }
	<?php elseif ( is_single() && get_post_type() === 'ophim' ): ?>
    ,{
      "@type": "ListItem",
      "position": 2,
      "name": "<?= esc_html($position_2_name) ?>",
      "item": "<?= esc_url( get_post_type_archive_link('ophim') ) ?>"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?= esc_html( get_the_title() ) ?>",
      "item": "<?= esc_url( get_permalink() ) ?>"
    }
    <?php elseif ( is_archive() ): ?>
    ,{
      "@type": "ListItem",
      "position": 2,
      "name": "Kho Hoạt Hình Anime Hay",
      "item": "<?= esc_url( get_post_type_archive_link('ophim') ) ?>"
    }
    <?php endif; ?>
  ]
}
</script>
