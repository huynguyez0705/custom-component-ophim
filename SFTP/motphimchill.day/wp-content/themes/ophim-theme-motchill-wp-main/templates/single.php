<div class="container">
	<div class="clear"></div>

	<div class="breadcrumbs-rankmath"><?php include( get_template_directory() . '/breadcrumb.php' ); ?></div>

	<div class="left-content" id="page-info">
		<div class="blockbody">
			<div class="info">
				<div class="poster">
					<a class="adspruce-streamlink" href="<?= watchUrl() ?>" title="<?php the_title(); ?>">
						<img src="<?= op_get_thumb_url() ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" /></a>
					<img class="hidden" itemprop="thumbnailUrl" src="<?= op_get_thumb_url() ?>">
					<div class="buttons btn-stream-link">
						<a class="btn-see btn " href="<?= watchUrl() ?>"title="Xem phim <?php the_title(); ?> Vietsub Motphim"> XEMPHIM</a>
					</div>
					<div class="clear"></div>
				</div>

				<div class="text" >
					<div class="name-info">
						<h1><span class="title" itemprop="name"><?php the_title(); ?></span></h1>
						<h2><span class="real-name"><?= op_get_original_title() ?> (<?= preg_match('/\d{4}/', op_get_year(), $matches) ? $matches[0] : '' ?>)</span></h2>
					</div>
					<div class="info-flim dinfo col">
						<ul class="film-info">
							<li><strong><?php the_title(); ?> - <?= op_get_original_title() ?> (<?= preg_match('/\d{4}/', op_get_year(), $matches) ? $matches[0] : '' ?>)</strong></li>
							<li><strong>Trạng thái: </strong><span class="badge badge-info" style="border-radius: 0px;"><?= op_get_episode() ?> <?= op_get_lang() ?></span></li>
							<li itemscope itemprop="director" itemtype="https://schema.org/Person">
								<strong>Đạo diễn: </strong>
								<span itemprop="name">
									<?= function_exists('op_get_directors') && op_get_directors(10, ', ') ? trim(op_get_directors(10, ', '), ', ') : 'Đang cập nhật'; ?>
								</span>
							</li>
							<li><strong>Thời lượng: </strong><span><?= op_get_runtime() ?></span></li>
							<li><strong>Tập hiện tại: </strong><span  class="badge badge-info" style="border-radius: 0px;background: #c58560;"><?= op_get_episode() ?></span></li>
							<li><strong>Số tập: </strong><span><?= op_get_total_episode() ?> Tập</span></li>
							<li><strong>Tình trạng: </strong><span><?= op_get_status() ?></span></li>
							<li><strong>Ngôn ngữ: </strong><span><?= op_get_lang() ?></span></li>
							<li><strong>Năm sản xuất: </strong><span>(<?= preg_match('/\d{4}/', op_get_year(), $matches) ? $matches[0] : 'Đang cập nhật'; ?>)</span></li>
							<meta itemprop="dateCreated" content="<?= preg_match('/\d{4}/', op_get_year(), $matches) ? $matches[0] : 'Đang cập nhật'; ?>">
							<li><strong>Quốc gia: </strong><span><?= trim(op_get_regions(', '),', ') ?></span></li>
							<li><strong>Thể loại: </strong><span><?= trim(op_get_genres(', '),', ') ?></span></li>
							<li><strong>Diễn viên: </strong><span><?= function_exists('op_get_actors') && op_get_actors(10, ', ') ? trim(op_get_actors(10, ', '),', ') : 'Đang cập nhật'; ?></span></li>
							<li><strong>Lượt xem: </strong><span class="badge badge-info" style="border-radius: 0px;"><?= op_get_post_view(); ?> Lượt</span></li>
						</ul>
						<div class="box-rating">
							<div id="star" data-score="<?= op_get_rating() ?>" style="cursor: pointer;"></div>
							<div id="div_average" style="float: left; line-height: 16px; margin: 0 5px;">
								<span id="hint"></span> 
								( <span class="average" id="average" >
								<?= op_get_rating() ?>
								</span> điểm / 
								<span id="rate_count">
									<?= op_get_rating_count() ?>
								</span> lượt)
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if (op_get_series()) { ?>
			<div class="series-movies"><p class="series_title">Series: <?= get_the_title() ?> </p><p class="series_list"><?= op_get_series() ?></p></div>
			<?php } ?>
			<?php if (op_get_notify()) { ?>
			<div class="film-note"><p class="">Ghi chú: <span><?= op_get_notify() ?></span></p></div>
			<?php } ?>
			<?php if (op_get_showtime_movies()) { ?>
			<div class="film-note "><p class="">Lịch chiếu: <span><?= op_get_showtime_movies() ?></span></p></div> 
			<?php } ?>
			
			<?php 
			$title = get_the_title(); 
			$title_with_plus = str_replace(' ', '+', $title); 
			?>
			<div class="notice note-rophim success" style="margin-bottom: 2em;">
				<p> <span class="icon-left">👉</span> Bấm vào đây để xem tập mới nhất trên server
					<strong><a href="https://www.google.com/search?q=<?php echo $title_with_plus; ?>+motphimchill.day" target="_blank" rel="nofollow" style="color:#dd003f">Phim <?php the_title(); ?> Motphimchill</a></strong>
					<span class="icon-right">👈</span>
				</p>
			</div>
			<?php include( get_template_directory() . '/notice.php' ); ?>

			<div class="info episode-list" style="margin-top:2rem">
				<?php foreach (episodeList() as $key => $server) { ?>
				<div class="control-box clear">
					<div class="server-episode-block">
						<i class="fa fa-film"></i> <?= $server['server_name'] ?>:
					</div>

					<div class="episodes">
						<div class="heading title_newepisode">Tập Mới Nhất :</div>
						<div class="list-episode">
							<?php
	// Sắp xếp danh sách tập theo thứ tự giảm dần
	usort($server['server_data'], function ($a, $b) {
		return intval($b['name']) - intval($a['name']);
	});
																  $limitedEpisodes = array_slice($server['server_data'], 0, 5);
																  foreach ($limitedEpisodes as $list) {
																	  $current = '';
																	  if (slugify($list['name']) == episodeName() && episodeSV() == $key) {
																		  $current = 'current';
																	  }
																	  echo '<a class="' . $current . '" href="' . hrefEpisode($list["name"], $key) . '">' . $list['name'] . '</a> '; 
																  }
							?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			

			<div class="clear"></div>

			<div class="detail">
				<div class="heading"> Nội dung phim </div>

				<div class="tabs-content" id="info-film" style="text-align: justify;" itemprop="description">
					<h2 class="heading-info"><?php the_title(); ?> - <?= op_get_original_title() ?> (<?= op_get_year()?>) - <?= op_get_lang() ?></h2>
					<?php the_content();?>
					<p>👉 Tham gia <a href="<?= home_url() ?>">MotPhimChill</a>, để xem phim <a href="<?= get_permalink() ?>"> <?= get_the_title()?> MotPhimChill Vietsub</a> + Thuyết Minh Full HD</p>
				</div>
				<img itemprop="image" src="<?= op_get_poster_url() ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
				<ul class="tags">
					<li class="caption"><span>Keywords </span><i class="fa fa-caret-right"></i></li>
					<li class="tag-item" style="color:#fff">
						<p><?= trim(op_get_tags(' | '),'| ') ?></p>
						<p><?= trim(op_get_tags(' Vietsub | '), ' | ') ?></p>
						<p><?= trim(op_get_tags(' Motphim | '), ' | ') ?></p>
						<p><?= trim(op_get_tags(' Motphimchill | '), ' | ') ?></p>
					</li>
				</ul>
				<div class="clear"></div>
				<div class="keywords">
					<p>xem phim <?php the_title(); ?> vietsub, phim <?= op_get_original_title() ?> vietsub, xem
						<?php the_title(); ?> vietsub online tap 1, tap 2,
						tap
						3, tap 4, phim <?= op_get_original_title() ?> ep 5, ep 6, ep 7, ep 8, ep 9, ep 10, xem
						<?php the_title(); ?> tập 11, tập 12, tập
						13,
						tập 14, tập 15, phim <?php the_title(); ?> tap 16, tap 17, tap 18, tap 19, tap 20, xem phim
						<?php the_title(); ?> tập
						21,
						23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47,
						48,
						49, 50, <?php the_title(); ?> tap cuoi, <?= op_get_original_title() ?> vietsub tron bo,
						review <?php the_title(); ?> netflix, <?php the_title(); ?>
						wetv, <?php the_title(); ?> phimmoi, <?php the_title(); ?> youtube,
						<?php the_title(); ?> dongphym, <?php the_title(); ?> vieon, phim
						keeng,
						bilutv, biphim, hdvip, hayghe, motphim, tvhay, zingtv, fptplay, phim1080, luotphim, fimfast,
						dongphim,
						fullphim, phephim, vtvgiaitri <?php the_title(); ?> full, <?= op_get_original_title() ?>
						online, <?php the_title(); ?> Thuyết Minh, <?php the_title(); ?>Vietsub,
						<?php the_title(); ?> Lồng Tiếng</p>
				</div>
			</div>
			<div id="comment-tab">
				<div class="box-comment" id="tabs-facebook" style="width: 100%; background-color: #fff">
					<div class="fb-comments w-full" data-href="<?= getCurrentUrl() ?>" data-width="100%"
						 data-numposts="5" data-colorscheme="light" data-lazy="true">
					</div>
				</div>
			</div>
			<div class="bottom-content">
				<div class="list-films film-hot">
					<h2 class="title-box">
						<i class="fa fa-star-o"></i>
						<a href="javascript:void(0)">Phim đề cử</a>
					</h2>
					<ul id="film_related" >
						<?php
	$postType = 'ophim';
						 $taxonomyName = 'ophim_genres';
						 $taxonomy = get_the_terms(get_the_id(), $taxonomyName);
						 if ($taxonomy) {
							 $category_ids = array();
							 foreach ($taxonomy as $individual_category) $category_ids[] = $individual_category->term_id;
							 $args = array('post_type' => $postType, 'post__not_in' => array(get_the_id()), 'posts_per_page' => 12, 'tax_query' => array(array('taxonomy' => $taxonomyName, 'field' => 'term_id', 'terms' => $category_ids,),));
							 $my_query = new wp_query($args);

							 if ($my_query->have_posts()):
							 while ($my_query->have_posts()):$my_query->the_post(); ?>
						<li class="item" title="<?php the_title(); ?>">
							<span class="label"><?= op_get_episode() ?> <?= op_get_lang() ?></span>
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
								<img class="img-film" title="<?php the_title(); ?>" alt="<?php the_title(); ?>"
									 src="<?= op_get_thumb_url() ?>" />
								<i class="icon-play"></i>
							</a>
							<div class="name" title="<?php the_title(); ?> - <?= op_get_original_title(); ?>"> 
								<div class="name-1 split-1"><?php the_title(); ?></div>
								<div class="name-2 split-1"><?= op_get_original_title(); ?></div>
							</div>
						</li>
						<?php
							 endwhile;
							 endif;
							 wp_reset_query();
						 }
						?> 

					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="right-content">
		<?php get_sidebar('ophim'); ?>
	</div>
	<div class="clear"></div>
</div>

<?php
add_action('wp_footer', function (){?>
<script type="text/javascript">
	const URL_POST_RATING = '<?php echo admin_url('admin-ajax.php')?>';
	const postid = '<?= get_the_ID()?>';
</script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/filmdetail.js?v=1.3"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/owl.carousel.min.js"></script>



<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Movie",
    "name": "<?= esc_html(get_the_title()); ?>",
    "description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
    "image": "<?= esc_url(op_get_thumb_url()); ?>",
    "datePublished": "<?= esc_html(op_get_year()); ?>",
    "dateCreated": "<?= get_the_date('Y-m-d'); ?>",
    "director": [
        <?php $directors=get_the_terms(get_the_ID(),"ophim_directors")?:[];foreach($directors as $key=>$director){echo($key>0?',':'').'{"@type":"Person","name":"'.(!empty($director->name)?esc_html($director->name):'Đang cập nhật').'","url":"'.(!empty(get_term_link($director))?esc_url(get_term_link($director)):'Đang cập nhật').'"}';}if(empty($directors))echo'{"@type":"Person","name":"Đang cập nhật","url":"Đang cập nhật"}';?>
    ],
    "actor": [
        <?php $actors=get_the_terms(get_the_ID(),"ophim_actors")?:[];foreach($actors as $key=>$actor){echo($key>0?',':'').'{"@type":"Person","name":"'.(!empty($actor->name)?esc_html($actor->name):'Đang cập nhật').'","url":"'.(!empty(get_term_link($actor))?esc_url(get_term_link($actor)):'Đang cập nhật').'"}';}if(empty($actors))echo'{"@type":"Person","name":"Đang cập nhật","url":"Đang cập nhật"}';?>
    ],
    "genre": "<?= esc_html(implode(', ', wp_list_pluck(get_the_terms(get_the_ID(), 'ophim_genres') ?: [], 'name')) ?: 'Đang cập nhật'); ?>",
    "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= op_get_rating() ?>",
    "ratingCount": "<?= op_get_rating_count() ?>",
    "bestRating": "10",
    "worstRating": "1"
}
}
</script>

<?php }) ?>