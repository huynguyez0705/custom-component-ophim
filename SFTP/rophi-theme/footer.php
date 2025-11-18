</div>
</div>
<?php
if (is_active_sidebar('widget-footer')) {
  dynamic_sidebar('widget-footer');
} else {
  _e('This is widget footer. Go to Appearance -> Widgets to add some widgets.', 'ophim');
}
?>
</div>
<script>
$(document).ready(function() {
  $("#film_related").owlCarousel({
    items: 4,
    itemsTablet: [700, 3],
    itemsMobile: [479, 2],
    navigation: true, // Show next and prev buttons
    slideSpeed: 300,
    paginationSpeed: 400,
    stopOnHover: true,
    pagination: false,
    navigationText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
  });
})
</script>
<script>
$(document).ready(function() {
  $("img.lazy").lazyload({
    effect: "fadeIn"
  });
});
</script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v23.0&appId=APP_ID"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/filmdetail.js?v=<?= filemtime(get_template_directory() . '/assets/js/filmdetail.js') ?>"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/owl.carousel.min.js?v=<?= filemtime(get_template_directory() . '/assets/js/owl.carousel.min.js') ?>"></script>

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

<?php wp_footer(); ?>

</html>