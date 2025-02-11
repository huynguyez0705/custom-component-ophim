<?php 
		$term = get_queried_object(); // Lấy đối tượng term hiện tại
		if ( isset( $term->term_id ) ) {
			$description = term_description( $term->term_id, $term->taxonomy ); // Lấy mô tả của taxonomy
			if ( !empty( $description ) ) { ?>
<div class="myui-panel active myui-panel-bg clearfix">
    <div class="myui-panel-box clearfix">
        <div class="myui-panel_bd">
            <?php echo $description; ?>
        </div>
    </div>
</div>
<?php } else {
				$term_name = $term->name;
				$term_link = get_term_link( $term );
		?>
<div class="myui-panel active myui-panel-bg clearfix">
    <div class="myui-panel-box clearfix">
        <div class="myui-panel_bd">
            Phim <a href="<?php echo esc_url( $term_link ); ?>"><strong>Hoạt Hình
                    <?php echo esc_html( $term_name ); ?></strong></a> mới và hot nhất trên <a
                href="<?php echo get_site_url() ?>"><strong>HHKungfu</strong></a>. Tuyển chọn 1000+ bộ phim hoạt hình
            hot, thuyết minh tiếng Việt, lồng tiếng Việt, FullHD - Vietsub. Tốc độ load cực nhanh. Phim được yêu thích
            nhất tháng <?= date_i18n('n') ?> năm <?= date_i18n('Y') ?>.
        </div>
    </div>
</div>
<?php } } ?>