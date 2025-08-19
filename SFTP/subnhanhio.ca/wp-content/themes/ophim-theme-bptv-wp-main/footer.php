</div>
</div>
</div>
<footer class="Footer">
	<div class="Container">
		<?php
		if ( is_active_sidebar('widget-footer') ) {
			dynamic_sidebar( 'widget-footer' );
		} else {
			_e('This is widget footer. Go to Appearance -> Widgets to add some widgets.', 'ophim');
		}
		?>
	</div>
</footer>


</div>



<script type="text/javascript">
	function JS_Load(u) {
		var d = document,
			p = d.getElementsByTagName('HEAD')[0],
			c = d.createElement('script');
		c.type = 'text/javascript';
		c.src = u;
		p.appendChild(c);
	}
</script>

<script type="text/javascript">
	JS_Load('<?= get_template_directory_uri() ?>/assets/js/default.include-footer1.js');
</script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v21.0"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/fx/util.js"></script>
<script type="text/javascript" src="/block.js?ver=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/block.js') ?>"></script>
<script>
	jQuery(document).ready(function(t) {
		$(".AAIco-arrow_upward").click(function() {
			$("html, body").animate({
				scrollTop: 0
			}, "slow");
			return false;
		});
	})

	// Lazyload image
	document.addEventListener("DOMContentLoaded", function() {
		var lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));

		if ("IntersectionObserver" in window) {
			let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
				entries.forEach(function(entry) {
					if (entry.isIntersecting) {
						let lazyImage = entry.target;
						lazyImage.src = lazyImage.dataset.src;
						lazyImage.classList.remove("lazy");
						lazyImageObserver.unobserve(lazyImage);
					}
				});
			});

			lazyImages.forEach(function(lazyImage) {
				lazyImageObserver.observe(lazyImage);
			});
		} else {
			// Possibly fall back to event handlers here
		}
	});
	// End lazy load image
</script>
<?php wp_footer(); ?>
</html>