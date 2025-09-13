<div class="container">
	<div class="clear"></div>

	<div class="breadcrumbs-rankmath"><?php include( get_template_directory() . '/breadcrumb.php' ); ?></div>
	<div class="box-player" id="box-player">
		<div class="clear"></div>
		<div id="player" class="embed-responsive embed-responsive-16by9"></div>
		<div class="loading-container">
			<div class="loading-player"></div>
		</div>
	</div>
	<div class="div-control" style="margin-bottom:80px;">
		<div class="film-note tip-change-server" style="margin:0; float:left;border: 1px dashed #e25ddb;padding: 5px">
			<span>Đổi Server nếu như không xem được <i class="fa fa-arrow-right"></i></span>
			<?php
			$episodeList = episodeList();
			$episodeData = isset($episodeList[0]) && !empty($episodeList[0]) ? $episodeList[0] : (isset($episodeList[1]) && !empty($episodeList[1]) ? $episodeList[1] : null);
			if ((empty($episodeData['server_data'][intval(episodeName()) - 1]["link_m3u8"])  && !empty($episodeData['server_data'][intval(episodeName()) - 1]["link_embed_new"])) || count($episodeData['server_data']) == 1 && !empty($episodeData['server_data'][0]["link_embed_new"])) {  ?>
			<a data-id="<?= episodeName() ?>" data-link="<?= embedNewEpisodeUrl() ?>" data-type="embed"
			   onclick="chooseStreamingServer(this)" class="streaming-server btn-sv btn-hls btn btn-primary">VIP #1</a>
			<?php
} else if ($episodeData) {
			?>
			<a data-id="<?= episodeName() ?>"
			   data-link="<?= m3u8EpisodeUrl() ?>"
			   data-type="m3u8" onclick="chooseStreamingServer(this)"
			   class="streaming-server btn-sv btn-hls btn btn-primary">VIP #1</a>

			<?php }?>

			<?php echo do_shortcode('[custom_button]') ?>
		</div>

		<span class="video-btn" id="btn_lightbulb" title="Tắt đèn">
			<i class="fa fa-lightbulb-o"></i>
		</span>
	</div>
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
		<p> <span class="icon-left">👉</span>  Bấm vào đây để xem tập <?= episodeName() ?> mới nhất nếu có lỗi:
			<strong><a href="https://www.google.com/search?q=<?php echo $title_with_plus; ?>+Tập+<?= episodeName() ?>+Motphimchill.day" target="_blank" rel="nofollow" style="color:#dd003f">Phim <?php the_title(); ?> Tập <?= episodeName() ?> Motphimchill</a></strong>
			<span class="icon-right">👈</span>
		</p>
	</div>
	<?php include( get_template_directory() . '/notice.php' ); ?>
	<div class="left-content" id="page-stream">
		<div class="left-content-player" id="player-video">
			<?php foreach (episodeList() as $key => $server) { ?>
			<div class="control-box clear">
				<div class="server-episode-block">
					<i class="fa fa-film"></i> <?= $server['server_name'] ?>:
				</div>
				<div class="episodes">
					<div class="list-episode">
						<?php foreach ($server['server_data'] as $list) {
	$current = '';
	if (slugify($list['name']) == episodeName()&& episodeSV() == $key) {
		$current = 'current';
	}
	echo '<a class="'.$current.'"  href="' . hrefEpisode($list["name"],$key) . '">
																							' . $list['name'] . '
																					</a> 
																			';
} ?>
					</div>
				</div>
			</div>

			<?php } ?>

			<div class="details">
				<div class="clear"></div>
				<div href="<?php the_permalink(); ?>" class="name">
					<h1>
						<a href="<?php the_permalink(); ?>"
						   title="Xem phim <?php the_title(); ?>"><?php the_title(); ?></a>
						<span>&nbsp;-&nbsp;</span>
						<span class="chapter-name"> Tập <?= episodeName() ?></span>
					</h1>
					<div class="clear"></div>
					<h2 class="real-name">
						<a href="<?php the_permalink(); ?>"><?= op_get_original_title() ?>
							(<?= op_get_year() ?>)</a>
					</h2>
				</div>
				<div class="clear"></div>
				<p class="short-description"
				   style="padding: 5px 8px;margin: 5px 0 20px 0;line-height: 26px;font-size: 12px;color: #BBB;background: #222;">
					<?php the_excerpt() ?>
					[ <a style="color: #fff;" href="<?php the_permalink(); ?>"
						 title="<?php the_title(); ?> - <?= op_get_original_title() ?> (<?= op_get_year() ?>)">Xem
					thêm</a>] </p>
				<div class="clear"></div>
				<div class="social-icon">
					<div class="fb-like" data-href="<?php the_permalink(); ?>" data-layout="button_count"
						 data-action="like" data-show-faces="false" data-share="true"></div>
					<div class="gg-like"></div>
				</div>
				<div class="box-rating">
					<?php $get_rating = (op_get_rating() > 8) ? op_get_rating() : round(mt_rand(80, 100) / 10, 1);
					$random_rating_count = (op_get_rating_count() > 50) ? op_get_rating_count() : rand(50, 100)						
					?>
					<div id="star" data-score="<?= $get_rating ?>" style="cursor: pointer;"></div>
					<div id="div_average" style="float: left; line-height: 16px; margin: 0 5px;">
						<span id="hint"></span> 
						( <span class="average" id="average">
						<?= $get_rating ?>
						</span> điểm / 
						<span id="rate_count">
							<?= $random_rating_count ?>
						</span> lượt)
					</div>
				</div>
				<div class="clear"></div>
				<div id="comment-tab">
					<div class="box-comment" id="tabs-facebook" style="width: 100%; background-color: #fff">
						<div class="fb-comments w-full" data-href="<?= getCurrentUrl() ?>" data-width="100%"
							 data-numposts="5" data-colorscheme="light" data-lazy="true">
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="bottom-content">
			<div class="list-films film-hot">
				<h2 class="title-box">
					<i class="fa fa-star-o"></i>
					<a href="javascript:void(0)">Có thể bạn muốn xem?</a>
				</h2>
				<ul id="film_related" class="relative">
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
	<div class="right-content">
		<?php get_sidebar('ophim'); ?>
	</div>
	<div class="clear"></div>
</div>

<?php
add_action('wp_footer', function () {?>
<script src="<?= get_template_directory_uri() ?>/assets/player/js/p2p-media-loader-core.min.js"></script>
<script src="<?= get_template_directory_uri() ?>/assets/player/js/p2p-media-loader-hlsjs.min.js"></script>
<?php op_jwpayer_js(); ?>
<?php $current_url = home_url($_SERVER['REQUEST_URI']);
									 $trailer_url = op_get_trailer_url();
									 $trailer_embed_url = $trailer_url && ($video_id = (parse_str(parse_url($trailer_url, PHP_URL_QUERY), $vars) ?: $vars['v'] ?? null)) ? "https://www.youtube.com/embed/{$video_id}" : null;
									 $description = wp_strip_all_tags(wp_trim_words(get_the_content(), 20)) ?: 'Xem Phim ' . esc_html(get_the_title()) . ' (' . esc_html(op_get_year()) . ') [' . esc_html(op_get_quality()) . ' ' . esc_html(op_get_lang()) . '] - Tập ' . esc_html(op_get_episode()) . ' - ' . esc_html(get_bloginfo('name'));?>


<script type="application/ld+json">
{
"@context": "https://schema.org",
"@type": "Movie",
"name": "Phim <?= esc_html(get_the_title()); ?> - <?= op_get_original_title() ?> <?= op_get_year() ?> <?= op_get_lang() ?> <?= op_get_quality() ?> - Tập <?= episodeName(); ?> - SubNhanh",
"description": "<?= esc_html($description); ?>",
"image": "<?= esc_url(home_url(op_get_thumb_url())); ?>",
"datePublished": "<?= esc_html(op_get_year()); ?>",
"dateCreated": "<?= get_the_date('Y-m-d'); ?>",
"url": "<?= esc_html($current_url); ?>",
"director": [<?= implode(',', array_map(fn($d) => '{"@type": "Person", "name": "' . esc_html($d->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($d) ?: 'Đang cập nhật') . '"}', get_the_terms(get_the_ID(), 'ophim_directors') ?: [new stdClass()])) ?>],
"actor": [<?= implode(',', array_map(fn($a) => '{"@type": "Person", "name": "' . esc_html($a->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($a) ?: 'Đang cập nhật') . '"}', get_the_terms(get_the_ID(), 'ophim_actors') ?: [new stdClass()])) ?>],
"genre": [<?= !empty($genres = get_the_terms(get_the_ID(), 'ophim_genres')) ? '"' . implode('","', array_map('esc_html', wp_list_pluck($genres, 'name'))) . '"' : '"Đang cập nhật"' ?>],
"aggregateRating": {
	"@type": "AggregateRating",
	"ratingValue": "<?= op_get_rating(); ?>",
	"ratingCount": "<?= op_get_rating_count(); ?>",
	"bestRating": "10",
	"worstRating": "1"
}<?php if ($trailer_url && $trailer_embed_url) : ?>,
"trailer": {
	"@type": "VideoObject",
	"name": "Trailer <?= esc_html(get_the_title()); ?>",
	"description": "<?= esc_html($description); ?>",
	"thumbnailUrl": "<?= esc_url(home_url(op_get_poster_url())); ?>",
	"uploadDate": "<?= get_the_date('c'); ?>",
	"contentUrl": "<?= esc_url($trailer_url); ?>",
	"embedUrl": "<?= esc_url($trailer_embed_url); ?>"
}
<?php endif; ?>
<?php if (embedEpisodeUrl() || embedNewEpisodeUrl()) : ?>,
"video": {
	"@type": "VideoObject",
	"name": "<?= esc_html(get_the_title()); ?> - Tập <?= episodeName(); ?>",
	"description": "<?= esc_html($description); ?>",
	"thumbnailUrl": "<?= esc_url(home_url(op_get_poster_url())); ?>",
	"uploadDate": "<?= get_the_date('c'); ?>",
	"contentUrl": "<?= esc_url(m3u8EpisodeUrl() ?: $trailer_url); ?>",
	"embedUrl": "<?= esc_url(embedEpisodeUrl() ?: (embedNewEpisodeUrl() ?: $trailer_embed_url)); ?>",
	"interactionStatistic": {
		"@type": "InteractionCounter",
		"interactionType": "https://schema.org/WatchAction",
		"userInteractionCount": "<?= op_get_rating_count(); ?>"
	}
}
<?php endif; ?>
}

</script>
<script>
	var episode_id = '<?= episodeName() ?>';
	const wrapper = document.getElementById('player');
	const vastAds = "<?= get_option('ophim_jwplayer_advertising_file') ?>";

	function chooseStreamingServer(el) {
		const type = el.dataset.type;
		const link = el.dataset.link.replace(/^http:\/\//i, 'https://');
		const id = el.dataset.id;

		episode_id = id;


		Array.from(document.getElementsByClassName('streaming-server')).forEach(server => {
			server.classList.remove('btn-success');
		})
		el.classList.add('btn-success');

		renderPlayer(type, link, id);
	}

	function renderPlayer(type, link, id) {
		if (type == 'embed') {
			if (vastAds) {
				wrapper.innerHTML = `<div id="fake_jwplayer"></div>`;
				const fake_player = jwplayer("fake_jwplayer");
				const objSetupFake = {
					key: "<?= get_option('ophim_jwplayer_license', 'ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc=') ?>",
					aspectratio: "16:9",
					width: "100%",
					file: "<?= get_template_directory_uri() ?>/assets/player/app-ro-phim.mp4",
					volume: 100,
					mute: false,
					autostart: true,
					advertising: {
						tag: "<?= get_option('ophim_jwplayer_advertising_file') ?>",
						client: "vast",
						vpaidmode: "insecure",
						skipoffset: <?= get_option('ophim_jwplayer_advertising_skipoffset') ?:  5 ?>, // Bỏ qua quảng cáo trong vòng 5 giây
						skipmessage: "Bỏ qua sau xx giây",
						skiptext: "Bỏ qua"
					}
				};
				fake_player.setup(objSetupFake);
				fake_player.on('complete', function(event) {
					$("#fake_jwplayer").remove();
					wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
allowfullscreen="" allow='autoplay'></iframe>`
					fake_player.remove();
				});

				fake_player.on('adSkipped', function(event) {
					$("#fake_jwplayer").remove();
					wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
allowfullscreen="" allow='autoplay'></iframe>`
					fake_player.remove();
				});

				fake_player.on('adComplete', function(event) {
					$("#fake_jwplayer").remove();
					wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
allowfullscreen="" allow='autoplay'></iframe>`
					fake_player.remove();
				});
			} else {
				if (wrapper) {
					wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
allowfullscreen="" allow='autoplay'></iframe>`
				}
			}
			return;
		}

		if (type == 'm3u8' || type == 'mp4') {
			wrapper.innerHTML = `<div id="jwplayer"></div>`;
			const player = jwplayer("jwplayer");
			const objSetup = {
				key: "<?= get_option('ophim_jwplayer_license', 'ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc=') ?>",
				aspectratio: "16:9",
				width: "100%",
				image: "<?= op_get_poster_url() ?>",
				file: link,
				playbackRateControls: true,
				playbackRates: [0.25, 0.75, 1, 1.25],
				sharing: {
					sites: [
						"reddit",
						"facebook",
						"twitter",
						"googleplus",
						"email",
						"linkedin",
					],
				},
				volume: 100,
				mute: false,
				autostart: true,
				logo: {
					file: "<?= get_option('ophim_jwplayer_logo_file') ?>",
					link: "<?= get_option('ophim_jwplayer_logo_link') ?>",
					position: "<?= get_option('ophim_jwplayer_logo_position') ?>",
				},
				advertising: {
					tag: "<?= get_option('ophim_jwplayer_advertising_file') ?>",
					client: "vast",
					vpaidmode: "insecure",
					skipoffset: <?= get_option('ophim_jwplayer_advertising_skipoffset') ?:  5 ?>, // Bỏ qua quảng cáo trong vòng 5 giây
					skipmessage: "Bỏ qua sau xx giây",
					skiptext: "Bỏ qua"
				}
			};

			if (type == 'm3u8') {
				const segments_in_queue = 50;

				var engine_config = {
					debug: !1,
					segments: {
						forwardSegmentCount: 50,
					},
					loader: {
						cachedSegmentExpiration: 864e5,
						cachedSegmentsCount: 1e3,
						requiredSegmentsPriority: segments_in_queue,
						httpDownloadMaxPriority: 9,
						httpDownloadProbability: 0.06,
						httpDownloadProbabilityInterval: 1e3,
						httpDownloadProbabilitySkipIfNoPeers: !0,
						p2pDownloadMaxPriority: 50,
						httpFailedSegmentTimeout: 500,
						simultaneousP2PDownloads: 20,
						simultaneousHttpDownloads: 2,
						// httpDownloadInitialTimeout: 12e4,
						// httpDownloadInitialTimeoutPerSegment: 17e3,
						httpDownloadInitialTimeout: 0,
						httpDownloadInitialTimeoutPerSegment: 17e3,
						httpUseRanges: !0,
						maxBufferLength: 300,
						// useP2P: false,
					},
				};
				if (Hls.isSupported() && p2pml.hlsjs.Engine.isSupported()) {
					var engine = new p2pml.hlsjs.Engine(engine_config);
					player.setup(objSetup);
					jwplayer_hls_provider.attach();
					p2pml.hlsjs.initJwPlayer(player, {
						liveSyncDurationCount: segments_in_queue, // To have at least 7 segments in queue
						maxBufferLength: 300,
						loader: engine.createLoaderClass(),
					});
				} else {
					player.setup(objSetup);
				}
			} else {
				player.setup(objSetup);
			}


			const resumeData = 'OPCMS-PlayerPosition-' + id;
			player.on('ready', function() {
				if (typeof(Storage) !== 'undefined') {
					if (localStorage[resumeData] == '' || localStorage[resumeData] == 'undefined') {
						console.log("No cookie for position found");
						var currentPosition = 0;
					} else {
						if (localStorage[resumeData] == "null") {
							localStorage[resumeData] = 0;
						} else {
							var currentPosition = localStorage[resumeData];
						}
						console.log("Position cookie found: " + localStorage[resumeData]);
					}
					player.once('play', function() {
						console.log('Checking position cookie!');
						console.log(Math.abs(player.getDuration() - currentPosition));
						if (currentPosition > 180 && Math.abs(player.getDuration() - currentPosition) >
							5) {
							player.seek(currentPosition);
						}
					});
					window.onunload = function() {
						localStorage[resumeData] = player.getPosition();
					}
				} else {
					console.log('Your browser is too old!');
				}
			});

			player.on('complete', function() {
				<?php if(nextEpisodeUrl()){ ?>
				window.location.href = "<?= nextEpisodeUrl() ?>";
				<?php }?>
				if (typeof(Storage) !== 'undefined') {
					localStorage.removeItem(resumeData);
				} else {
					console.log('Your browser is too old!');
				}
			})

			function formatSeconds(seconds) {
				var date = new Date(1970, 0, 1);
				date.setSeconds(seconds);
				return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
			}
		}
	}
</script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const episode = '<?= episodeName() ?>';
		let playing = document.querySelector(`[data-id="${episode}"]`);
		if (playing) {
			playing.click();
			return;
		}

		const servers = document.getElementsByClassName('streaming-server');
		if (servers[0]) {
			servers[0].click();
		}
	});
</script>
<script type="text/javascript">
	const URL_POST_RATING = '<?php echo admin_url('admin-ajax.php')?>';
	const postid = '<?= get_the_ID()?>';
</script>
<script type="text/javascript" src="<?= get_template_directory_uri() ?>/assets/js/filmdetail.js?v=1.2.2"></script>
<?php }) ?>