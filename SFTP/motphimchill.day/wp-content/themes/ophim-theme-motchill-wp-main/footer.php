</div>
</div>
<?php
if ( is_active_sidebar('widget-footer') ) {
	dynamic_sidebar( 'widget-footer' );
} else {
	_e('This is widget footer. Go to Appearance -> Widgets to add some widgets.', 'ophim');
}
?>
</div>
<script>
	$(document).ready(function() {
		$("img.lazy").lazyload({
			effect: "fadeIn"
		});
	});
</script>

<script src="<?= get_template_directory_uri() ?>/assets/js/jquery.raty.js"></script>
<script>
	$(document).ready(function() {
		$('.top-star').raty({
			readOnly: true,
			numberMax: 5,
			half: true,
			score: function() {
				return $(this).attr('data-rating');
			},
			hints: ["bad", "poor", "regular", "good", "gorgeous"],
			space: false
		});
	})
</script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v22.0"></script>
<!--<script type="text/javascript" src="/ads.js"></script>-->
<script type="text/javascript" src="/blocks.js"></script>
<?php wp_footer(); ?>
</html>