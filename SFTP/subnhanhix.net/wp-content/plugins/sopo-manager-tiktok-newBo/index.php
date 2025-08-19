<?php
/*
Plugin Name: Sope Manager
Description: Tiktok ft Shopee
Version: 1.5
Author: Cô Văn Nal
*/

add_action('admin_menu', 'gsr_add_admin_menu');
function gsr_add_admin_menu() {
    add_menu_page('Sope Manager', 'Sope Manager', 'manage_options', 'gs-reader', 'sgsr_admin_page');
}

function sgsr_admin_page() {
    if (isset($_POST['sgsr_submit'])) {
        $sheet_tabs_raw = sanitize_text_field($_POST['sgsr_sheet_tabs']);

        $sheet_tabs_range = sanitize_text_field($_POST['sgsr_sheet_range']);
        $sope_replay_time = sanitize_text_field($_POST['sope_replay_time']);

        $priority_range = sanitize_text_field($_POST['priority_range']);
        $priority_time = sanitize_text_field($_POST['priority_time']);
    
        update_option('sgsr_sheet_tabs', $sheet_tabs_raw);
        update_option('sgsr_sheet_range', $sheet_tabs_range);
        update_option('sope_replay_time', $sope_replay_time);

        update_option('priority_range', $priority_range);
        update_option('priority_time', $priority_time);

        update_option('sope_popup_mode', $_POST['sope_popup_mode'] === 'random' ? 'random' : 'sequential');

        echo '<div class="updated"><p>Đã lưu thiết lập.</p></div>';
    }

    $sheet_tabs_raw = get_option('sgsr_sheet_tabs', '');
    $sheet_tabs_range = get_option('sgsr_sheet_range', '');
    $sope_replay_time = get_option('sope_replay_time', '');

    $priority_range = get_option('priority_range', '');
    $priority_time = get_option('priority_time', '');

    $sheet_tabs = array_filter(array_map('trim', explode(',', $sheet_tabs_raw)));    
    $mode = get_option('sope_popup_mode', 'sequential');


    echo "<div class='wrap'><h1>Google Sheets Reader</h1>";

    echo "<form method='post'>";
    echo "<table class='form-table'>";
    echo "<tr><th scope='row'>Tên sheet: TiTi-HKT, BoBo, Rua-Kini, Payer, Docdac</th><td><input type='text' name='sgsr_sheet_tabs' value='" . esc_attr($sheet_tabs_raw) . "' class='regular-text' required></td></tr>";
    echo "<tr><th scope='row'>Vùng dữ liệu ưu tiên</th><td><input type='text' name='priority_range' value='" . esc_attr($priority_range) . "' class='regular-text' required></td></tr>";
    echo "<tr><th scope='row'>Thời gian lặp ưu tiên(phút)</th><td><input type='text' name='priority_time' value='" . esc_attr($priority_time) . "' class='regular-text' required></td></tr>";
    echo "<tr><th scope='row'>Vùng dữ liệu</th><td><input type='text' name='sgsr_sheet_range' value='" . esc_attr($sheet_tabs_range) . "' class='regular-text' required></td></tr>";
    echo "<tr><th scope='row'>Thời gian lặp (phút)</th><td><input type='text' name='sope_replay_time' value='" . esc_attr($sope_replay_time) . "' class='regular-text' required></td></tr>";
    echo "<tr><th scope='row'>Chế độ mở link</th><td>
                <select name='sope_popup_mode'>
                    <option value='sequential' " . selected($mode, 'sequential', false) . ">Tuần tự</option>
                    <option value='random' " . selected($mode, 'random', false) . ">Ngẫu nhiên</option>
                </select>
            </td>
        </tr>";
    echo "</table>";
    submit_button('Lưu cấu hình', 'primary', 'sgsr_submit');
    echo "</form>";

if (!empty($sheet_tabs)) {
    echo "<hr><h2>Dữ liệu từ Sheets</h2>";

    $all_targets = [];
    $priority_targets = [];
    $priority_range = get_option('priority_range', '');

    foreach ($sheet_tabs as $sheet) {
        echo "<h3>" . esc_html($sheet) . "</h3>";

        $sheet_data = sgsr_fetch_sheet($sheet, $sheet_tabs_range);

        if (is_null($sheet_data)) {
            echo "<p style='color:red'>Không thể đọc dữ liệu sheet: " . esc_html($sheet) . "</p>";
            continue;
        }

        $sheet_urls = [];
        if (!empty($sheet_data)) {
            foreach ($sheet_data as $row) {
                $candidate = isset($row[0]) ? trim($row[0]) : '';
                if (!empty($candidate) && wp_http_validate_url($candidate)) {
                    $sheet_urls[] = esc_url_raw($candidate);
                }
            }
        }

        // Hiển thị bảng dữ liệu sheet (nếu có)
        if (!empty($sheet_data)) {
            echo "<table class='widefat striped'><tbody>";
            foreach ($sheet_data as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . esc_html($cell) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "<p><strong>Số link hợp lệ trong sheet này:</strong> " . esc_html(count($sheet_urls)) . "</p><br>";
        } else {
            echo "<p>Không có dữ liệu trong vùng {$sheet_tabs_range} cho sheet này.</p>";
        }

        // Gom link thường
        if (!empty($sheet_urls)) {
            $all_targets = array_merge($all_targets, $sheet_urls);
        }

        // Nếu có vùng ưu tiên, lấy và hiển thị
        if (!empty($priority_range)) {
            $priority_data = sgsr_fetch_sheet($sheet, $priority_range);

            if (is_null($priority_data)) {
                echo "<p style='color:orange'>Không thể đọc vùng ưu tiên ({$priority_range}) cho sheet: " . esc_html($sheet) . "</p>";
            } else {
                $sheet_priority_urls = [];
                foreach ($priority_data as $row) {
                    $candidate = isset($row[0]) ? trim($row[0]) : '';
                    if (!empty($candidate) && wp_http_validate_url($candidate)) {
                        $sheet_priority_urls[] = esc_url_raw($candidate);
                    }
                }

                if (!empty($sheet_priority_urls)) {
                    $priority_targets = array_merge($priority_targets, $sheet_priority_urls);

                    echo "<p><strong>Priority links (sheet này):</strong><br>";
                    foreach ($sheet_priority_urls as $pl) {
                        echo esc_html($pl) . "<br>";
                    }
                    echo "</p>";
                } else {
                    echo "<p>Không có link ưu tiên trong vùng {$priority_range} cho sheet này.</p>";
                }
            }
        }
        echo "<br>";
    } // end foreach sheets

    $all_targets = array_values(array_unique($all_targets));
	$priority_targets = array_values(array_unique($priority_targets));

	// Nếu rỗng thì giữ lại dữ liệu cũ
	if (empty($all_targets)) {
		$all_targets = get_option('cocosim', []);
	}
	if (empty($priority_targets)) {
		$priority_targets = get_option('sope_priority_links', []);
	}

	update_option('cocosim', $all_targets);
	update_option('sope_priority_links', $priority_targets); // lưu mảng ưu tiên

    echo "<p><strong>Tổng link thường:</strong> " . esc_html(count($all_targets)) . " — <strong>Tổng link ưu tiên:</strong> " . esc_html(count($priority_targets)) . "</p>";
}

    echo "</div>";
}

function sgsr_fetch_sheet($sheet, $sheet_tabs_range) {
    $url = "https://sheets.googleapis.com/v4/spreadsheets/1can4F9Q0vu8BZTEGbRcf6j7P-rrsoM5Dof0fH4XoIrU/values/$sheet!$sheet_tabs_range?key=AIzaSyA6d0PX3Bq7N-bscRkQtoTo6UEICC9pSxM";
    $response = wp_remote_get($url);
    if (is_wp_error($response)) return [];

    $csv = wp_remote_retrieve_body($response);
    $data = json_decode($csv, true);

    if (!empty($data) && array_key_exists('values',$data) == true)  {
        return $data['values'];
    }
    return null;
}



add_filter('cron_schedules', function($schedules) {
    if (!isset($schedules['sope_every_hour'])) {
        $schedules['sope_every_hour'] = [
            'interval' => 3600,
            'display'  => __('Every Hour')
        ];
    }
    return $schedules;
});

add_action('init', function() {
    if (!wp_next_scheduled('sope_update_sheet_data')) {
        wp_schedule_event(time(), 'twicedaily', 'sope_update_sheet_data');
    }
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('sope_update_sheet_data');
});

add_action('sope_update_sheet_data', 'sope_fetch_and_cache_data');

function sope_fetch_and_cache_data() {
    $mode            = get_option('sope_popup_mode', 'sequential');
    $sheet_tabs_raw  = get_option('sgsr_sheet_tabs', '');
    $sheet_tabs_range = get_option('sgsr_sheet_range', '');
    $priority_range  = get_option('priority_range', '');

    $sheet_tabs = array_filter(array_map('trim', explode(',', $sheet_tabs_raw)));

    $all_target_urls = [];
    $priority_urls = [];

    foreach ($sheet_tabs as $sheet) {
        // Link thường
        $sheet_data = sgsr_fetch_sheet($sheet, $sheet_tabs_range);
        if (!is_null($sheet_data) && !empty($sheet_data)) {
            foreach ($sheet_data as $row) {
                $candidate = isset($row[0]) ? trim($row[0]) : '';
                if (!empty($candidate) && wp_http_validate_url($candidate)) {
                    $all_target_urls[] = esc_url_raw($candidate);
                }
            }
        }

        // Link ưu tiên
        if (!empty($priority_range)) {
            $priority_data = sgsr_fetch_sheet($sheet, $priority_range);
            if (!is_null($priority_data) && !empty($priority_data)) {
                foreach ($priority_data as $row) {
                    $candidate = isset($row[0]) ? trim($row[0]) : '';
                    if (!empty($candidate) && wp_http_validate_url($candidate)) {
                        $priority_urls[] = esc_url_raw($candidate);
                    }
                }
            }
        }
    }

    // Nếu fetch rỗng → giữ nguyên dữ liệu cũ
    if (empty($all_target_urls)) {
        $all_target_urls = get_option('cocosim', []);
    }
    if (empty($priority_urls)) {
        $priority_urls = get_option('sope_priority_links', []);
    }

    // Lưu vào option
    update_option('cocosim', array_values(array_unique($all_target_urls)));
    update_option('sope_priority_links', array_values(array_unique($priority_urls)));
}


add_action('wp_enqueue_scripts', 'enqueue_popup_script');
function enqueue_popup_script() {
    $serialized_links = get_option('cocosim', []);
    $priority_links   = get_option('sope_priority_links', []);

    $mode = get_option('sope_popup_mode', 'sequential');
    $link = array_values(array_filter((array) $serialized_links));
    $priority_links = array_values(array_filter((array) $priority_links));

    $replay_time = (float) get_option("sope_replay_time", 10);
    if ($replay_time <= 0) $replay_time = 10;

    $priority_time = (float) get_option("priority_time", 5);
    if ($priority_time < 0) $priority_time = 5;

    wp_enqueue_script('popup-js', plugin_dir_url(__FILE__) . 'asset/script.js', [], "1.5" , true);
    $localized_data = [
        'links' => $link,
        'priority_links' => $priority_links,
        'replay_time'  => $replay_time,
        'priority_time' => $priority_time,
        'mode' => $mode,
    ];

    wp_localize_script('popup-js', 'popupLinkData', $localized_data);
}
add_action('wp_enqueue_scripts', 'enqueue_popup_script');

