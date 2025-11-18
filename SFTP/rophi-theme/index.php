<?php
get_header();
?>

<div class="container">
	<?php if (is_active_sidebar('widget-slider-poster')) {
		dynamic_sidebar('widget-slider-poster');
	} else {
		_e('This is widget poster. Go to Appearance -> Widgets to add some widgets.', 'ophim');
	}
	?>
	<div class="container">
		<div class="top-add">
			<a href="https://rophim.com" target="_blank" rel="nofollow"><img class="img-rophim" alt="Banner RổPhim" src="<?php echo get_template_directory_uri(); ?>/assets/images/banner-rophim.gif" loading="lazy"></a>
		</div>
	</div>

	<div class="content-box">
		<div class="left-content">
			<?php if (is_active_sidebar('widget-area')) {
				dynamic_sidebar('widget-area');
			} else {
				_e(' Go to Appearance -> Widgets to add some widgets.', 'ophim');
			}
			?>
		</div>
		<div class="right-content">
			<?php get_sidebar('ophim'); ?>
		</div>
	</div>
</div>

<?php
get_footer();
?>