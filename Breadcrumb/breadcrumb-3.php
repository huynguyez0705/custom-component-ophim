<?php
function breadcrumb_home() {
	$name = 'Motchill';
	$url = home_url();
?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<a itemprop="item" href='<?php echo esc_url($url); ?>'>
		<i class="fa fa-home"></i> 
		<span itemprop="name"><?php echo esc_html($name); ?></span>
	</a>
	<meta itemprop="position" content="1" />
	<i class="fa fa-long-arrow-alt-right"></i>
</li>
<?php } ?>

<?php
function breadcrumb_cate() {
	breadcrumb_home();
	if (isEpisode() || (is_single() && get_post_type() === 'ophim')) {
		$categories_episode = get_the_terms(get_the_ID(), 'ophim_categories');
		$name_cate_epis = $categories_episode && !is_wp_error($categories_episode) ? $categories_episode[0]->name : 'Đang cập nhật';
		$url_cate_epis = $categories_episode && !is_wp_error($categories_episode) ? get_term_link($categories_episode[0]) : '#';
?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<a itemprop="item" href='<?php echo esc_url($url_cate_epis); ?>'>
		<span itemprop="name"><?php echo esc_html($name_cate_epis); ?></span>
	</a>
	<meta itemprop="position" content="2" />
	<i class="fa fa-long-arrow-alt-right"></i>
</li>
<?php 
	} else {
		$taxonomies = ['ophim_categories'=>'','ophim_directors'=>'Đạo Diễn','ophim_years'=>'Năm Phát Hành','ophim_actors'=>'Diễn Viên','ophim_regions'=>'Quốc Gia','ophim_genres'=>'Thể Loại'];
		$cate = get_queried_object()->taxonomy;
		$cate_name = single_tag_title('', false);
		$prefix = $taxonomies[$cate] ?? '';
		$cate_url = get_term_link(get_queried_object());
		$paged = get_query_var('paged') ?: 1;
		$paged_url = add_query_arg('paged', $paged, $cate_url);
		$current_url = esc_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<meta itemprop="item" content='<?php echo esc_url($cate_url); ?>'>
	<span itemprop="name"><?php echo esc_html($cate_name); ?></span>
	<meta itemprop="position" content="2" />
	<?php if ($paged > 1) { ?> <i class="fa fa-long-arrow-alt-right"></i> <?php } ?>
</li>
<?php if ($paged > 1) { ?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<meta itemprop="item" content='<?php echo esc_url($current_url); ?>'>
	<span itemprop="name">Trang <?php echo $paged; ?></span>
	<meta itemprop="position" content="3" />
</li>
<?php }
	}
}
?>

<?php
function breadcrumb_single() { 
	breadcrumb_cate();
	global $post;
	$title = get_the_title($post->ID);
	$url_post = get_permalink($post->ID);
	if (isEpisode()) {
?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<a itemprop="item" href='<?php echo esc_url($url_post); ?>'>
		<span itemprop="name"><?php echo esc_html($title); ?></span>
	</a>
	<meta itemprop="position" content="3" />
	<i class="fa fa-long-arrow-alt-right"></i>
</li>
<?php } else { ?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	<meta itemprop="item" content='<?php echo esc_url($url_post); ?>'>
	<span itemprop="name"><?php echo esc_html($title); ?></span>
	<meta itemprop="position" content="3" />
</li>
<?php } } ?>

<?php
function breadcrumb_episode() {
    breadcrumb_single();
    global $post;
    $title_epis = 'Tập ' . episodeName();
    $url_epis = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
    <meta itemprop="item" content='<?php echo esc_url($url_epis); ?>'>
    <span itemprop="name"><?php echo esc_html($title_epis); ?></span>
    <meta itemprop="position" content="4" />
</li>
<?php } ?>
<!-- Render -->

<?php if (isEpisode()) { ?>
<!-- Trang Episode -->
<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php breadcrumb_episode(); ?>
</ul>
<?php } elseif (is_single() && get_post_type() === 'ophim' && !isEpisode()) { ?>
<!-- Trang Info -->
<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php breadcrumb_single(); ?>
</ul>
<?php } ?>

<!-- <?php $taxonomy_list = array('ophim_categories', 'ophim_actors', 'ophim_genres', 'ophim_regions', 'ophim_tags', 'ophim_years', 'ophim_directors'); ?>
 -->
<?php if (is_tax($taxonomy_list)) { 
    $url_archive = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!-- Trang Taxonomy -->
<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
	<?php breadcrumb_cate(); ?>
</ul>
<?php } ?>
<?php if (is_search()) { ?>
<!--  Trang Search -->
<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php breadcrumb_home(); ?>
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <meta itemprop="item" content='<?php echo esc_url(home_url('/?s=' . urlencode(get_search_query()))); ?>'>
        <span itemprop="name">Kết quả tìm kiếm: <?php echo esc_html(get_search_query()); ?></span>
        <meta itemprop="position" content="2" />
    </li>
</ul>
<?php } ?>
<?php if (is_archive() && !is_tax($taxonomy_list)) { 
$url_archive = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!--  Trang Archive -->
<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php breadcrumb_home(); ?>
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <meta itemprop="item" content='<?php echo esc_url($url_archive) ?>'>
        <span itemprop="name">Kho Phim MotPhimTV</span>
        <meta itemprop="position" content="2" />
    </li>
</ul>
<?php } ?>
<style>.breadcrumb {color:#fff;;display: flex;gap: 10px;padding: 1rem 0 2rem;}
.breadcrumb a {color:#c58560; margin-right:10px}</style>