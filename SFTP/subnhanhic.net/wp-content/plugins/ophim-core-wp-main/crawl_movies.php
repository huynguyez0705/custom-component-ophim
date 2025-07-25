<?php

function remove_image_sizes($sizes, $metadata)
{
	// Remove default image re-size wp
	return [];
}

add_filter('intermediate_image_sizes_advanced', 'remove_image_sizes', 10, 2);

function crawl_ophim_page_handle($url)
{
	$sourcePage = file_get_contents($url);
	$sourcePage = json_decode($sourcePage);
	$listMovies = [];
	if (isset($sourcePage->items)) {
		$sourcePage = $sourcePage->items;
	}
	if (isset($sourcePage->data->items)) {
		$sourcePage = $sourcePage->data->items;
	}

	if (count($sourcePage) > 0) {
		foreach ($sourcePage as $key => $item) {
			array_push($listMovies, get_option('my_custom_api_option','https://phimapi.com') . "/phim/{$item->slug}|{$item->_id}|{$item->modified->time}|{$item->name}|{$item->origin_name}|{$item->year}");
		}
		return join("\n", $listMovies);
	}
	return $listMovies;
}

add_action('wp_ajax_crawl_ophim_movies', 'crawl_ophim_movies');
function crawl_ophim_movies()
{
	$data_post = $_POST['url'];
	$url = explode('|', $data_post)[0];
	$ophim_id = explode('|', $data_post)[1];
	$ophim_update_time = explode('|', $data_post)[2];
	$title = explode('|', $data_post)[3];
	$org_title = explode('|', $data_post)[4];
	$year = explode('|', $data_post)[5];

	$filterType = oIsset($_POST, 'filterType', []);
	$filterCategory = oIsset($_POST, 'filterCategory', []);
	$filterCountry = oIsset($_POST, 'filterCountry', []);

	$result = crawl_ophim_movies_handle($url, $ophim_id, $ophim_update_time, $filterType, $filterCategory, $filterCountry);
	echo $result;
	die();
}

function crawl_ophim_movies_handle($url, $ophim_id, $ophim_update_time, $filterType, $filterCategory, $filterCountry)
{
	try {
		// Kiểm tra bài viết đã tồn tại
		$args = array(
			'post_type' => 'ophim',
			'meta_query' => array(
				array(
					'key' => 'ophim_fetch_ophim_id',
					'value' => $ophim_id,
					'compare' => '=',
				)
			)
		);
		$wp_query = new WP_Query($args);

		if ($wp_query->found_posts > 0) { 
			// Nếu bài viết tồn tại
			if ($wp_query->have_posts()) : 
			while ($wp_query->have_posts()) : 
			$wp_query->the_post();
			global $post;

			// Lấy thời gian cũ từ metadata
			$get_fetch_time = get_post_meta($post->ID, 'ophim_fetch_ophim_update_time', true);

			// Chuyển đổi thời gian cũ và mới sang timestamp để so sánh
			$old_time = strtotime($get_fetch_time);
			$new_time = strtotime($ophim_update_time);

			if ($new_time <= $old_time) {
				// Nếu thời gian mới không lớn hơn thời gian cũ, bỏ qua cập nhật
				return json_encode(array(
					'status' => true,
					'post_id' => $post->ID,
					'msg' => 'Nothing needs updating (time is not newer)!',
					'wait' => false,
					'schedule_code' => SCHEDULE_CRAWLER_TYPE_NOTHING
				));
			}

			// Lấy dữ liệu mới và cập nhật bài viết
			$sourcePage = json_decode(file_get_contents($url), true);
			$data = create_data($sourcePage, $url, $ophim_id, $ophim_update_time);
			$status = getStatus($data['status']);

			// Cập nhật thông tin bài viết
			$formality = ($data['type'] == 'tv_series') ? 'tv_series' : 'single_movies';
			$post_id = $post->ID;
			update_post_meta($post_id, 'ophim_movie_formality', $formality);
			update_post_meta($post_id, 'ophim_movie_status', getStatus($data['status']));
			update_post_meta($post_id, 'ophim_fetch_info_url', $data['fetch_url']);
			update_post_meta($post_id, 'ophim_fetch_ophim_id', $data['fetch_ophim_id']);
			update_post_meta($post_id, 'ophim_fetch_ophim_update_time', $data['fetch_ophim_update_time']);
			update_post_meta($post_id, 'ophim_trailer_url', $data['trailer_url']);
			update_post_meta($post_id, 'ophim_runtime', $data['duration']);
			update_post_meta($post_id, 'ophim_episode', $data['episode']);
			update_post_meta($post_id, 'ophim_total_episode', $data['total_episode']);
			update_post_meta($post_id, 'ophim_quality', $data['quality']);
			update_post_meta($post_id, 'ophim_lang', $data['lang']);
			update_post_meta($post_id, 'ophim_showtime_movies', $data['showtime']);
			$updatepost = array(
				'post_modified'  => date('Y-m-d H:i:s'),
				'post_modified_gmt'   => date('Y-m-d H:i:s'),
				'ID'          => $post_id, 
			);
			wp_update_post( $updatepost );

			// **Thay đổi xử lý thumbnail và poster theo API_DOMAIN**
			$crawl_settings = json_decode(get_option(CRAWL_OPHIM_OPTION_SETTINGS, false));
			$ophim_thumb_url = get_post_meta($post_id, 'ophim_thumb_url', true);
			$ophim_poster_url = get_post_meta($post_id, 'ophim_poster_url', true);

			// Kiểm tra và cập nhật thumbnail
			if (!file_exists(ABSPATH . $ophim_thumb_url)) {
				$thumb_image_url = download_resize_thumb($data, $post_id, $crawl_settings);
				if ($thumb_image_url != $ophim_thumb_url) {
					update_post_meta($post_id, 'ophim_thumb_url', $thumb_image_url);
				}
			}

			// Kiểm tra và cập nhật poster
			if (!file_exists(ABSPATH . $ophim_poster_url)) {
				$poster_image_url = download_resize_poster($data, $post_id, $crawl_settings);
				if ($poster_image_url != $ophim_poster_url) {
					update_post_meta($post_id, 'ophim_poster_url', $poster_image_url);
				}
			}

			// Cập nhật danh sách tập phim
			$list_episode = get_list_episode($sourcePage, $post->ID);
			return json_encode(array(
				'status' => true,
				'post_id' => $post->ID,
				'list_episode' => $list_episode,
				'msg' => 'Post updated successfully!',
				'wait' => true,
				'schedule_code' => SCHEDULE_CRAWLER_TYPE_UPDATE
			));

			endwhile;
			endif;
		} else { 
			// Nếu bài viết không tồn tại, tạo mới
			$sourcePage = file_get_contents($url);
			$sourcePage = json_decode($sourcePage, true);
			$data = create_data($sourcePage, $url, $ophim_id, $ophim_update_time, $filterType, $filterCategory, $filterCountry);

			// Tạo mới bài viết
			$post_id = add_posts($data);

			// Lấy danh sách tập phim
			$list_episode = get_list_episode($sourcePage, $post_id);
			return json_encode(array(
				'status' => true,
				'post_id' => $post_id,
				'data' => $url,
				'list_episode' => 'Add new',
				'wait' => true,
				'schedule_code' => SCHEDULE_CRAWLER_TYPE_INSERT
			));
		}
	} catch (Exception $e) {
		// Xử lý lỗi nếu có
		return json_encode(array(
			'status' => false,
			'post_id' => null,
			'data' => null,
			'list_episode' => null,
			'msg' => $e->getMessage(),
			'wait' => false,
			'schedule_code' => SCHEDULE_CRAWLER_TYPE_ERROR
		));
	}
}

function create_data($sourcePage, $url, $ophim_id, $ophim_update_time, $filterType = [], $filterCategory = [], $filterCountry = [])
{
	if (in_array($sourcePage["movie"]["type"], $filterType)) {
		return array(
			'crawl_filter' => true,
		);
	}
	$type = ($sourcePage["movie"]["type"] == "single") ? "single_movies" : "tv_series";
	
	$domain = parse_url($url, PHP_URL_HOST);
	if ($domain == 'ophim1.com') {
		$thumbnail = $sourcePage["movie"]["thumb_url"];
		$poster = $sourcePage["movie"]["poster_url"];
	} elseif ($domain == 'phimapi.com') {
		$thumbnail = $sourcePage["movie"]["poster_url"];
		$poster = $sourcePage["movie"]["thumb_url"];
	} else {
		throw new Exception("Unknown domain: $domain");
	}
	$arrCat = [];
	$arrGenres = [];
	foreach ($sourcePage["movie"]["category"] as $key => $value) {
		if (in_array($value["name"], $filterCategory)) {
			return array(
				'crawl_filter' => true,
			);
		}
		array_push($arrGenres, $value["name"]);
	}
	if ($sourcePage["movie"]["type"] == "single_movies") {
		array_push($arrCat, "Phim Lẻ");
	}
	if ($sourcePage["movie"]["type"] == "hoathinh") {
		array_push($arrCat, "Hoạt Hình");
		$type = (count(reset($sourcePage["episodes"])['server_data'] ?? []) > 1 ? 'series' : 'single');
	}
	if ($sourcePage["movie"]["type"] == "tvshows") {
		array_push($arrCat, "TV Shows");
	}
	if ($sourcePage["movie"]["type"] == "series") {
		array_push($arrCat, "Phim Bộ");
	}
	if ($sourcePage["movie"]["type"] == "tv_series") {
		array_push($arrCat, "Phim Bộ");
	}
	if ($sourcePage["movie"]["type"] == "single") {
		array_push($arrCat, "Phim Lẻ");
	}
	if ($sourcePage["movie"]["chieurap"] == true) {
		array_push($arrCat, "Phim Chiếu Rạp");
	}
	if (strpos(strtolower($sourcePage["movie"]["lang"]), "thuyết minh") !== false) {
		array_push($arrCat, "Phim Thuyết Minh");
	}
	$arrCountry = [];
	foreach ($sourcePage["movie"]["country"] as $key => $value) {
		if (in_array($value["name"], $filterCountry)) {
			return array(
				'crawl_filter' => true,
			);
		}
		array_push($arrCountry, $value["name"]);
	}

	$arrTags = [];
	array_push($arrTags, $sourcePage["movie"]["name"]);
	if ($sourcePage["movie"]["name"] != $sourcePage["movie"]["origin_name"])
		array_push($arrTags, $sourcePage["movie"]["origin_name"]);

	// Convert quality: FHD to HD, keep HD as is
	$quality = $sourcePage["movie"]["quality"];
	if (strtoupper($quality) === 'FHD') {
		$quality = 'HD';
	}

	$data = array(
		'crawl_filter' => false,
		'fetch_url' => $url,
		'fetch_ophim_id' => $ophim_id,
		'fetch_ophim_update_time' => $ophim_update_time,
		'title' => $sourcePage["movie"]["name"],
		'org_title' => $sourcePage["movie"]["origin_name"],
		'thumbnail' => $thumbnail,
		'poster' => $poster,
		'trailer_url' => $sourcePage["movie"]["trailer_url"],
		'episode' => $sourcePage["movie"]["episode_current"],
		'total_episode' => $sourcePage["movie"]["episode_total"],
		'tags' => $arrTags,
		'content' => strip_tags(preg_replace('/\\r?\\n/s', '', $sourcePage["movie"]["content"])),
		'actor' => implode(',', $sourcePage["movie"]["actor"]),
		'director' => implode(',', $sourcePage["movie"]["director"]),
		'country' => $arrCountry,
		'cat' => $arrCat,
		'genres' => $arrGenres,
		'type' => $type,
		'lang' => $sourcePage["movie"]["lang"],
		'showtime' => $sourcePage["movie"]["showtimes"],
		'year' => $sourcePage["movie"]["year"],
		'is_copyright' => $sourcePage["movie"]["is_copyright"],
		'status' => $sourcePage["movie"]["status"],
		'duration' => $sourcePage["movie"]["time"],
		'quality' => $quality
	);

	return $data;
}

function add_posts($data)
{
	$director = explode(',', sanitize_text_field($data['director']));
	$actor = explode(',', sanitize_text_field($data['actor']));

	$cat_id = array();
	foreach ($data['cat'] as $cat) {
		if (!term_exists($cat) && $cat != '') {
			wp_insert_term($cat, 'ophim_categories');
		}
	}
	foreach ($data['tags'] as $tag) {
		if (!term_exists($tag) && $tag != '') {
			wp_insert_term($tag, 'ophim_tags');
		}
	}
	$formality = ($data['type'] == 'tv_series') ? 'tv_series' : 'single_movies';
	$post_data = array(
		'post_title' => $data['title'],
		'post_content' => $data['content'],
		'post_status' => 'publish',
		'post_type' => 'ophim',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_author' => get_current_user_id()
	);
	$post_id = wp_insert_post($post_data);

	// Download & resize image
	$crawl_settings = json_decode(get_option(CRAWL_OPHIM_OPTION_SETTINGS, false));
	$thumb_image_url = download_resize_thumb($data, $post_id, $crawl_settings);
	$poster_image_url = download_resize_poster($data, $post_id, $crawl_settings);

	$status = getStatus($data['status']);
	//
	update_post_meta($post_id, 'ophim_movie_formality', $formality);
	update_post_meta($post_id, 'ophim_movie_status', $status);
	update_post_meta($post_id, 'ophim_fetch_info_url', $data['fetch_url']);
	update_post_meta($post_id, 'ophim_fetch_ophim_id', $data['fetch_ophim_id']);
	update_post_meta($post_id, 'ophim_fetch_ophim_update_time', $data['fetch_ophim_update_time']);
	update_post_meta($post_id, 'ophim_thumb_url', $thumb_image_url);
	update_post_meta($post_id, 'ophim_poster_url', $poster_image_url);
	update_post_meta($post_id, 'ophim_original_title', $data['org_title']);
	update_post_meta($post_id, 'ophim_trailer_url', $data['trailer_url']);
	update_post_meta($post_id, 'ophim_runtime', $data['duration']);
	update_post_meta($post_id, 'ophim_rating', '');
	update_post_meta($post_id, 'ophim_votes', '');
	update_post_meta($post_id, 'ophim_episode', $data['episode']);
	update_post_meta($post_id, 'ophim_total_episode', $data['total_episode']);
	update_post_meta($post_id, 'ophim_quality', $data['quality']);
	update_post_meta($post_id, 'ophim_lang', $data['lang']);
	update_post_meta($post_id, 'ophim_showtime_movies', $data['showtime']);
	update_post_meta($post_id, 'ophim_year', $data['year']);
	update_post_meta($post_id, 'ophim_is_copyright', $data['is_copyright']);

	// Check for active SEO plugins and set focus keyword
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
		update_post_meta( $post_id, '_yoast_wpseo_focuskw', sanitize_text_field( $data['title'] ) );
	}
	if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
		update_post_meta( $post_id, 'rank_math_focus_keyword', sanitize_text_field( $data['title'] ) );
	}

	//
	wp_set_object_terms($post_id, $status, 'status', false);
	wp_set_object_terms($post_id, $director, 'ophim_directors', false);
	wp_set_object_terms($post_id, $actor, 'ophim_actors', false);
	wp_set_object_terms($post_id, sanitize_text_field($data['year']), 'ophim_years', false);
	wp_set_object_terms($post_id, $data['country'], 'ophim_regions', false);
	wp_set_object_terms($post_id, $data['cat'], 'ophim_categories', false);
	wp_set_object_terms($post_id, $data['tags'], 'ophim_tags', false);
	wp_set_object_terms($post_id, $data['genres'], 'ophim_genres', false);

	return $post_id;
}

function download_resize_thumb($data, $post_id, $crawl_settings)
{
	$thumb_image_url = $data['thumbnail'];
	$convert_webp = oIsset($crawl_settings, 'crawl_convert_webp', 'off') == 'on' ? true : false;
	try {
		if (oIsset($crawl_settings, 'crawl_resize_size_thumb', 'off') == 'on') {
			$res_thumb = save_images(
				$data['thumbnail'],
				$post_id, $data['title'],
				'thumb',
				oIsset($crawl_settings, 'crawl_resize_size_thumb_w', 0),
				oIsset($crawl_settings, 'crawl_resize_size_thumb_h', 0),
				$convert_webp,
				true
			);
			$thumb_image_url = str_replace(get_site_url(), '', $res_thumb['url']);
		}
	} catch (Exception $e) {
	}
	return $thumb_image_url;
}

function download_resize_poster($data, $post_id, $crawl_settings)
{
	$poster_image_url = $data['poster'];
	$convert_webp = oIsset($crawl_settings, 'crawl_convert_webp', 'off') == 'on' ? true : false;
	try {
		if (oIsset($crawl_settings, 'crawl_resize_size_poster', 'off') == 'on') {
			if ($data['poster'] && $data['poster'] != "") {
				$res = save_images(
					$data['poster'],
					$post_id,
					$data['title'],
					'poster',
					oIsset($crawl_settings, 'crawl_resize_size_poster_w', 0),
					oIsset($crawl_settings, 'crawl_resize_size_poster_h', 0),
					$convert_webp
				);
				$poster_image_url = str_replace(get_site_url(), '', $res['url']);
			}
		}
	} catch (Exception $e) {
	}
	return $poster_image_url;
}

function save_images($image_url, $post_id, $posttitle, $suffix, $max_w, $max_h, $is_webp = false, $set_thumb = false)
{
	// Khởi tạo curl để tải về hình ảnh
	$ch = curl_init($image_url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36");
	$file = curl_exec($ch);
	curl_close($ch);

	$postname = sanitize_title($posttitle);
	$file_extension = $is_webp ? 'webp' : 'jpg';
	$im_name = "$postname-$post_id-$suffix.$file_extension";
	$res = wp_upload_bits($im_name, '', $file);
	if (!$res['error'] && $file) {
		$image_path = $res['file'];
		$editor = wp_get_image_editor($image_path);
		if (!is_wp_error($editor)) {
			$editor->resize($max_w, $max_h, false);
			$editor->save($image_path, $is_webp ? 'image/webp' : 'image/jpeg');
		}
		insert_attachment($res['file'], $post_id, $set_thumb);
	}
	return $res;
}

function insert_attachment($file, $post_id, $set_thumb)
{
	$dirs = wp_upload_dir();
	$filetype = wp_check_filetype($file);
	$attachment = array(
		'guid' => $dirs['baseurl'] . '/' . _wp_relative_upload_path($file),
		'post_mime_type' => $filetype['type'],
		'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
		'post_content' => '',
		'post_status' => 'inherit'
	);
	$attach_id = wp_insert_attachment($attachment, $file, $post_id);
	$attach_data = wp_generate_attachment_metadata($attach_id, $file);
	wp_update_attachment_metadata($attach_id, $attach_data);
	if ($set_thumb != false) set_post_thumbnail($post_id, $attach_id);
	return $attach_id;
}

function get_list_episode($sourcePage, $post_id)
{
    if (isset($sourcePage["episodes"]) && is_array($sourcePage["episodes"])) {
        $existing_episodes = get_post_meta($post_id, 'ophim_episode_list', true);

        foreach ($sourcePage["episodes"] as $epIndex => &$episode) {
            if (isset($episode['server_name'])) {
                if ($episode['server_name'] === "#Hà Nội (Thuyết Minh)") {
                    $episode['server_name'] = "#Thuyết Minh";
                } elseif ($episode['server_name'] === "#Hà Nội (Vietsub)") {
                    $episode['server_name'] = "#Vietsub";
                } elseif ($episode['server_name'] === "#Hà Nội (Lồng Tiếng)") {
                    $episode['server_name'] = "#Lồng Tiếng";
                }
            }

            if (isset($episode['server_data']) && is_array($episode['server_data'])) {
                foreach ($episode['server_data'] as &$server_data) {
                    if (strpos($server_data['name'], 'Tập') !== false) {
                        $server_data['name'] = trim(str_replace('Tập', '', $server_data['name']));
                    }
                    if (strpos($server_data['slug'], 'tap-') !== false) {
                        $server_data['slug'] = str_replace('tap-', '', $server_data['slug']);
                    }
                    $server_data['name'] = ltrim($server_data['name'], '0');
                    $server_data['slug'] = ltrim($server_data['slug'], '0');
                    $server_data['link_ro'] = "on";
                }
            }

            if (!empty($existing_episodes) && is_array($existing_episodes) && isset($existing_episodes[$epIndex])) {
                $episode['ro_id'] = $existing_episodes[$epIndex]['ro_id'] ?? '';
                $episode['ro_session'] = $existing_episodes[$epIndex]['ro_session'] ?? '';
            } else {
                $episode['ro_id'] = '';
                $episode['ro_session'] = '';
            }

            $sourcePage["episodes"][$epIndex] = $episode;
        }

        update_post_meta($post_id, 'ophim_episode_list', $sourcePage["episodes"]);
        return json_encode($sourcePage["episodes"], JSON_PRETTY_PRINT);
    }

    return json_encode([], JSON_PRETTY_PRINT);
}

function slugify($str, $divider = '-')
{
	$str = trim(mb_strtolower($str));
	$str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
	$str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
	$str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
	$str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
	$str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
	$str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
	$str = preg_replace('/(đ)/', 'd', $str);
	$str = preg_replace('/[^a-z0-9-\s]/', '', $str);
	$str = preg_replace('/([\s]+)/', $divider, $str);
	return $str;
}

function getStatus($status)
{
	$hl_status = "completed";
	switch (strtolower($status)) {
		case 'ongoing':
			$hl_status = "ongoing";
			break;
		case 'completed':
			$hl_status = "completed";
			break;
		default:
			$hl_status = "trailer";
			break;
	}
	return $hl_status;
}

add_action('wp_ajax_update_api_option', 'handle_update_api_option');

function handle_update_api_option() {
    if (!isset($_POST['value'])) {
        wp_send_json_error('Invalid request.');
    }

    $value = sanitize_text_field($_POST['value']);
    update_option('my_custom_api_option', $value);
    wp_send_json_success('Option updated.');
}