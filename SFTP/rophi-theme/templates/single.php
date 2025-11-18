<div class="container">
  <?php include(get_template_directory() . '/breadcrumb.php'); ?>
  <div class="content-box">
    <div class="left-content" id="page-info">
      <div class="blockbody">
        <div class="box-info" itemscope itemtype="https://schema.org/TVSeries">
          <div class="poster">
            <a class="adspruce-streamlink thumb-v" href="<?= watchUrl() ?>" title="<?php the_title(); ?>">
              <img class="img-film" src="<?= op_get_thumb_url() ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
              <span class="icon-play"></span>
            </a>
            <div class="button-watch two-button">
              <a class="btn-stream-link uppercase" href="<?= watchUrl() ?>" title="Xem phim <?php the_title(); ?>"> Xem phim
              </a>
              <!-- <a class="btn-stream-link uppercase custom-button" href="https://rophim.me" title="Xem phim <?php the_title(); ?>"> Xem phim
            </a> -->
            </div>
          </div>
          <div class="text">
            <h1>
              <span class="title" itemprop="name"><?php the_title(); ?></span>
            </h1>
            <h2>
              <span class="real-name"><?= op_get_original_title() ?>
                (<?= op_get_year() ?>)</span>
            </h2>
            <div class="dinfo">
              <div class="col">
                <div class="film-detail">
                  <span class="detail-label">Trạng thái:</span>
                  <span class="film-status">
                    <span class="badge-info"><?= op_get_episode() ?> <?= op_get_lang() ?></span>
                  </span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Thời lượng:</span>
                  <span><?= op_get_runtime() ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Số tập:</span>
                  <span><?= op_get_total_episode() ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Tình trạng:</span>
                  <span><?= op_get_status() ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Ngôn ngữ:</span>
                  <span><?= op_get_lang() ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Năm sản xuất:</span>
                  <span><?= op_get_year() ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Quốc gia:</span>
                  <span><?= op_get_regions(', ') ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Thể loại:</span>
                  <span><?= op_get_genres(', ') ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Đạo diễn:</span>
                  <span><?= op_get_directors(10, ', ') ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Diễn viên:</span>
                  <span><?= op_get_actors(10, ', ') ?></span>
                </div>
                <div class="film-detail">
                  <span class="detail-label">Lượt xem:</span>
                  <span class="badge-view"><?= op_get_post_view() ?></span>
                </div>
              </div>
            </div>

            <div class="btn-groups">
              <div class="box-btn">
                <!-- Facebook Share Button -->
                <div class="fb-share-button" data-href="<?php the_permalink(); ?>" data-layout="button_count" data-size="small"></div>

                <!-- Pinterest Share Button -->
                <a href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo urlencode(op_get_thumb_url()); ?>&description=<?php echo urlencode(mb_substr(get_the_excerpt(), 0, 130)); ?>"
                  target="_blank" class="btn-pinterest">
                  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pinterest-logo.png" alt="Chia sẻ trên Pinterest" width="11" height="11">
                  Pinterest
                </a>

                <!-- X (Twitter) Share Button -->
                <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank" class="btn-twitter">
                  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/twitter-logo.png" alt="Chia sẻ trên X">
                  Twitter
                </a>
              </div>

              <div class="box-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <div id="star" data-score=" <?= op_get_rating() ?>" style="cursor: pointer;"></div>
                <div>
                  <div id="div_average" style="float: left; line-height: 16px; margin: 0 5px; ">
                    <span id="hint"></span> ( <span class="average" id="average" itemprop="ratingValue"> <?= op_get_rating() ?></span>
                    điểm / <span id="rate_count" itemprop="ratingCount"><?= op_get_rating_count() ?></span>
                    lượt)
                  </div>
                  <meta itemprop="bestRating" content="10" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="box-episodes">
          <div class="box-episode-header">
            <h2 class="box-title uppercase">Tập Mới</h2>
            <div class="episodes-tab">
              <?php foreach (episodeList() as $key => $server): ?>
                <div class="tab-version <?= $key == array_key_first(episodeList()) ? 'active' : '' ?>" data-server="episodes-<?= $key ?>">
                  <?= htmlspecialchars($server['server_name']) ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="box-episode-bottom">
            <div class="notification">
              <div class="toast-server"></div>
            </div>
            <?php foreach (episodeList() as $key => $server): ?>
              <div class="episode-block <?= $key == array_key_first(episodeList()) ? 'active' : '' ?>" id="episodes-<?= $key ?>">
                <div class="episodes">
                  <div class="list-episode ">
                    <?php foreach (array_reverse($server['server_data']) as $list): ?>
                      <a class="epi-<?= htmlspecialchars($list['name']) ?>" href="<?= hrefEpisode($list['name'], $key) ?>">
                        <?= htmlspecialchars($list['name']) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if (op_get_notify()) { ?>
          <div class="film-note">
            <h4 class="hidden">Ghi chú</h4>GHI CHÚ: <?= op_get_notify() ?>
          </div>
        <?php } ?>

        <?php if (op_get_showtime_movies()) { ?>
          <div class="film-note">
            <h4 class="hidden">Lịch chiếu</h4>LỊCH CHIẾU: <?= op_get_showtime_movies() ?>
          </div>
        <?php } ?>

        <div class="box-content" id="info-film">
          <div class="tabs-content">
            <div class="tabs-content-header">
              <h2 class="box-title uppercase"> Nội dung chi tiết </h2>
              <h3 class="heading"><?php the_title(); ?></h3>
            </div>

            <div class="tabs-content-body relative scroll-y">
              <div class="read-more">Xem Thêm</div>
              <div class="tab-body-content">
                <p><?php the_content(); ?></p>
              </div>
              <div class="tabs-body-img relative">
                <img class="img-film" src="<?= op_get_poster_url() ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
                <div class="caption">Phim <?= the_title() ?></div>
              </div>
              <div class="tabs-body-trailer">
                <?php
                if ($url = op_get_trailer_url()):
                  $id = substr($url, strrpos($url, '=') + 1);
                ?>
                  <iframe style="aspect-ratio: 16/9;" src="https://www.youtube.com/embed/<?php echo $id; ?>" frameborder="0" allowfullscreen></iframe>
                <?php
                endif;
                ?>
              </div>
              <script>
                document.querySelector('.tabs-content-body .read-more').addEventListener('click', function() {
                  this.parentElement.classList.toggle('open');
                  this.textContent = this.parentElement.classList.contains('open') ? 'Thu Gọn' : 'Xem Thêm';
                });
              </script>
            </div>
          </div>
          <ul class="tags">
            <li class="caption">
              <span>Tags</span>
              <i class="fa fa-caret-right"></i>
            </li>
            <li class="tag-item">
              <div>
                <?= op_get_tags(' | ') ?>
              </div>
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
              <?php the_title(); ?> Lồng Tiếng
            </p>
          </div>
        </div>

        <div class="top-add">
          <a href="https://rophim.com" target="_blank" rel="nofollow"><img class="img-rophim" alt="Banner RổPhim" src="<?php echo get_template_directory_uri(); ?>/assets/images/banner-rophim.gif" loading="lazy"></a>
        </div>
        
        <div id="comment-tab">
          <div class="box-comment" id="tabs-facebook" style="width: 100%; background-color: #fff">
            <div class="fb-comments w-full" data-href="<?= getCurrentUrl() ?>" data-width="100%" data-numposts="5" data-colorscheme="light" data-lazy="true">
            </div>
          </div>
        </div>

        <div class="box-related">
          <div class="list-films film-hot">
            <h2 class="box-title">
              <i class="fa fa-film"></i>
              <span>Phim Đề Cử</span>
            </h2>
            <div id="film_related" class="relative">
              <?php
              $postType = 'ophim';
              $taxonomyName = 'ophim_genres';
              $taxonomy = get_the_terms(get_the_id(), $taxonomyName);
              if ($taxonomy) {
                $category_ids = array();
                foreach ($taxonomy as $individual_category)
                  $category_ids[] = $individual_category->term_id;
                $args = array('post_type' => $postType, 'post__not_in' => array(get_the_id()), 'posts_per_page' => 10, 'tax_query' => array(
                  array(
                    'taxonomy' => $taxonomyName,
                    'field' => 'term_id',
                    'terms' => $category_ids,
                  ),
                ));
                $my_query = new wp_query($args);

                if ($my_query->have_posts()):
                  while ($my_query->have_posts()):
                    $my_query->the_post();
              ?>
                    <div class="item" title="<?php the_title(); ?>">
                      <a href="<?php the_permalink(); ?>" title="Phim <?php the_title(); ?> " class="thumb-v">
                        <img class="img-film" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" src="<?= op_get_thumb_url() ?>" />
                        <i class="icon-play"></i>
                        <span class="label"><?= op_get_episode() ?> <?= op_get_lang() ?></span>
                      </a>
                      <div class="info">
                        <h3 class="name split-1"><?php the_title(); ?></h3>
                        <div class="name-real split-1"><?= op_get_original_title() ?></div>
                      </div>
                    </div>
              <?php
                  endwhile;
                endif;
                wp_reset_query();
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-content">
      <?php get_sidebar('ophim'); ?>
    </div>
  </div>
</div>
<?php
if (op_get_trailer_url()) {
  parse_str(parse_url($trailer_url, PHP_URL_QUERY), $my_array_of_vars);
  $video_id = $my_array_of_vars['v'];
  $trailer_embed_url = "https://www.youtube.com/embed/" . $video_id;
}
?>
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Movie",
    "name": "Phim <?= esc_html(get_the_title()); ?>",
    "description": "<?= esc_html(wp_strip_all_tags(wp_trim_words(get_the_content(), 20))); ?>",
    "image": "<?= esc_url(home_url(op_get_thumb_url())); ?>",
    "datePublished": "<?= esc_html(op_get_year()); ?>",
    "dateCreated": "<?= get_the_date('Y-m-d'); ?>",
    "director": [
      <?= implode(',', array_map(fn($d) => '{"@type": "Person", "name": "' . esc_html($d->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($d) ?: 'Đang cập nhật') . '"}',   get_the_terms(get_the_ID(), 'ophim_directors') ?: [new stdClass()])) ?>
    ],
    "actor": [
      <?= implode(',', array_map(fn($a) => '{"@type": "Person", "name": "' . esc_html($a->name ?: 'Đang cập nhật') . '", "url": "' . esc_url(get_term_link($a) ?: 'Đang cập nhật') . '"}', get_the_terms(get_the_ID(), 'ophim_actors') ?: [new stdClass()])) ?>
    ],
    "genre": [
      <?= !empty($genres = get_the_terms(get_the_ID(), 'ophim_genres')) ? '"' . implode('","', array_map('esc_html', wp_list_pluck($genres, 'name'))) . '"' : '"Đang cập nhật"' ?>
    ],
    "keywords": [
      <?php $tags = get_the_terms(get_the_ID(), 'ophim_tags');
      if (is_array($tags) && !empty($tags)) {
        $tag_names = array_unique(array_map(fn($tag) => '"' . esc_html($tag->name) . ' ' . esc_html($domain) . '"', $tags));
        echo implode(',', $tag_names);
      } else {
        echo '"Đang cập nhật"';
      } ?>
    ],
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "<?= op_get_rating(); ?>",
      "ratingCount": "<?= op_get_rating_count(); ?>",
      "bestRating": "10",
      "worstRating": "1"
    }
    <?php if (!empty(op_get_trailer_url())) : ?>,
      "trailer": {
        "@type": "VideoObject",
        "name": "Trailer <?= esc_html(get_the_title()); ?>?>",
        "description": "Trailer của <?= esc_html(get_the_title()); ?>",
        "thumbnailUrl": "<?= esc_url(home_url(op_get_poster_url())); ?>",
        "uploadDate": "<?= get_the_date('c'); ?>",
        "contentUrl": "<?= esc_url($trailer_url); ?>",
        "embedUrl": "<?= esc_url($trailer_embed_url); ?>"
      }
    <?php endif; ?>
  }
</script>
<?php
add_action('wp_footer', function () {
?>
  <script type="text/javascript">
    const URL_POST_RATING = '<?php echo admin_url('admin-ajax.php') ?>';
    const postid = '<?= get_the_ID() ?>';
  </script>

  <script>
    //Tính lại chiều cao cho các ảnh bị lệch nhau trên mobile
    // var first_img_w = $(".img-film").eq(0).width();
    // var first_img_h = first_img_w * (1.25); // Chiều cao bằng chiều rộng x 1.42
    // $(".img-film").height(first_img_h);

    $(function() {
      $('.dinfo').slimScroll({
        height: '250px'
      });
    });
  </script>

<?php }) ?>