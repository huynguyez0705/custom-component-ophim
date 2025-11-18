<?php
// Add an admin menu item under ophim post type
add_action('admin_menu', 'add_seo_keyword_menu');
function add_seo_keyword_menu()
{
  add_submenu_page(
    'ophim-toolkit',
    'Quản Lý Keyword',
    'Quản Lý Keyword',
    'manage_options',
    'manage-seo-keyword',
    'render_seo_keyword_page'
  );
}

function check_and_update_seo_focus_keyword($custom_prefix = '') {
    global $wpdb;

    // Use custom prefix if provided, otherwise use WordPress default prefix
    $prefix = !empty($custom_prefix) ? sanitize_text_field($custom_prefix) : $wpdb->prefix;
    $postmeta_table = $prefix . 'postmeta';
    $posts_table = $prefix . 'posts';

    // Check active SEO plugin
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $meta_key = '';
    $plugin_name = '';
    if (is_plugin_active('seo-by-rank-math/rank-math.php')) {
        $meta_key = 'rank_math_focus_keyword';
        $plugin_name = 'Rank Math';
    } elseif (is_plugin_active('wordpress-seo/wp-seo.php')) {
        $meta_key = '_yoast_wpseo_focuskw';
        $plugin_name = 'Yoast SEO';
    } else {
        return array(
            'processed' => 0,
            'updated' => array(),
            'added' => array(),
            'skipped' => array(),
            'existing' => array(),
            'error' => 'Không có plugin SEO nào (Rank Math hoặc Yoast SEO) đang active.'
        );
    }

    // Query to get all published posts
    $query = $wpdb->prepare(
        "SELECT p.ID, p.post_title
        FROM $posts_table p
        WHERE p.post_status = %s
        ORDER BY p.post_title ASC",
        'publish'
    );

    $posts = $wpdb->get_results($query);
    $results = array(
        'processed' => 0,
        'updated' => array(),
        'added' => array(),
        'skipped' => array(),
        'existing' => array(),
        'error' => ''
    );

    // Process each post
    foreach ($posts as $post) {
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $edit_link = get_edit_post_link($post_id, 'display');

        // Skip posts without a valid edit link
        if (!$edit_link) {
            $results['skipped'][] = array(
                'post_id' => $post_id,
                'post_title' => $post_title,
                'reason' => 'Không có link chỉnh sửa hợp lệ'
            );
            continue;
        }

        // Check if the focus keyword meta key exists for this post
        $meta_value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value
                FROM $postmeta_table
                WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if ($meta_value !== null) {
            if (trim($meta_value) === '') {
                $results['updated'][] = array(
                    'post_id' => $post_id,
                    'post_title' => $post_title,
                    'edit_link' => $edit_link,
                    'meta_value' => $post_title,
                    'meta_key' => $meta_key
                );
            } else {
                $results['existing'][] = array(
                    'post_id' => $post_id,
                    'post_title' => $post_title,
                    'edit_link' => $edit_link,
                    'meta_value' => $meta_value,
                    'meta_key' => $meta_key
                );
            }
        } else {
            $results['added'][] = array(
                'post_id' => $post_id,
                'post_title' => $post_title,
                'edit_link' => $edit_link,
                'meta_value' => $post_title,
                'meta_key' => $meta_key
                );
        }

        $results['processed']++;
    }

    return $results;
}

function update_seo_focus_keyword($custom_prefix = '') {
    global $wpdb;

    // Use custom prefix if provided, otherwise use WordPress default prefix
    $prefix = !empty($custom_prefix) ? sanitize_text_field($custom_prefix) : $wpdb->prefix;
    $postmeta_table = $prefix . 'postmeta';
    $posts_table = $prefix . 'posts';

    // Check active SEO plugin
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $meta_key = '';
    $plugin_name = '';
    if (is_plugin_active('seo-by-rank-math/rank-math.php')) {
        $meta_key = 'rank_math_focus_keyword';
        $plugin_name = 'Rank Math';
    } elseif (is_plugin_active('wordpress-seo/wp-seo.php')) {
        $meta_key = '_yoast_wpseo_focuskw';
        $plugin_name = 'Yoast SEO';
    } else {
        return array(
            'processed' => 0,
            'updated' => array(),
            'added' => array(),
            'skipped' => array(),
            'error' => 'Không có plugin SEO nào (Rank Math hoặc Yoast SEO) đang active.'
        );
    }

    // Query to get all published posts
    $query = $wpdb->prepare(
        "SELECT p.ID, p.post_title
        FROM $posts_table p
        WHERE p.post_status = %s
        ORDER BY p.post_title ASC",
        'publish'
    );

    $posts = $wpdb->get_results($query);
    $results = array(
        'processed' => 0,
        'updated' => array(),
        'added' => array(),
        'skipped' => array(),
        'error' => ''
    );

    // Process each post
    foreach ($posts as $post) {
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $edit_link = get_edit_post_link($post_id, 'display');

        // Skip posts without a valid edit link
        if (!$edit_link) {
            $results['skipped'][] = array(
                'post_id' => $post_id,
                'post_title' => $post_title,
                'reason' => 'Không có link chỉnh sửa hợp lệ'
            );
            continue;
        }

        // Check if the focus keyword meta key exists for this post
        $meta_value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value
                FROM $postmeta_table
                WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if ($meta_value !== null) {
            // Meta key exists, check if meta_value is empty
            if (trim($meta_value) === '') {
                // Update empty meta_value with post title
                $wpdb->update(
                    $postmeta_table,
                    array('meta_value' => sanitize_text_field($post_title)),
                    array(
                        'post_id' => $post_id,
                        'meta_key' => $meta_key
                    ),
                    array('%s'),
                    array('%d', '%s')
                );
                $results['updated'][] = array(
                    'post_id' => $post_id,
                    'post_title' => $post_title,
                    'edit_link' => $edit_link,
                    'meta_value' => $post_title,
                    'meta_key' => $meta_key
                );
            }
        } else {
            // Meta key does not exist, add it with post title as meta_value
            $wpdb->insert(
                $postmeta_table,
                array(
                    'post_id' => $post_id,
                    'meta_key' => $meta_key,
                    'meta_value' => sanitize_text_field($post_title)
                ),
                array('%d', '%s', '%s')
            );
            $results['added'][] = array(
                'post_id' => $post_id,
                'post_title' => $post_title,
                'edit_link' => $edit_link,
                'meta_value' => $post_title,
                'meta_key' => $meta_key
            );
        }

        $results['processed']++;
    }

    // Format the log message
    $log = "Thêm/Cập nhật Focus Keyword của $plugin_name (Tạo lúc: " . date('Y-m-d H:i:s') . "):\n\n";
    $log .= "Tiền tố bảng: $prefix\n";
    $log .= "Tổng số bài viết đã xử lý: {$results['processed']}\n\n";

    $log .= "Bài viết đã cập nhật Focus Keyword (trống thành tiêu đề):\n";
    if (!empty($results['updated'])) {
        foreach ($results['updated'] as $post) {
            $log .= "Post ID: {$post['post_id']} - Tiêu đề: {$post['post_title']} - Meta Key: {$post['meta_key']} - Giá trị được đặt: {$post['meta_value']} - Link chỉnh sửa: {$post['edit_link']}\n";
        }
    } else {
        $log .= "Không có bài viết nào có giá trị Focus Keyword trống để cập nhật.\n";
    }

    $log .= "\nBài viết đã thêm Focus Keyword mới:\n";
    if (!empty($results['added'])) {
        foreach ($results['added'] as $post) {
            $log .= "Post ID: {$post['post_id']} - Tiêu đề: {$post['post_title']} - Meta Key: {$post['meta_key']} - Giá trị được đặt: {$post['meta_value']} - Link chỉnh sửa: {$post['edit_link']}\n";
        }
    } else {
        $log .= "Không có bài viết nào cần thêm Focus Keyword mới.\n";
    }

    $log .= "\nBài viết bị bỏ qua (không có link chỉnh sửa hợp lệ):\n";
    if (!empty($results['skipped'])) {
        foreach ($results['skipped'] as $post) {
            $log .= "Post ID: {$post['post_id']} - Tiêu đề: {$post['post_title']} - Lý do: {$post['reason']}\n";
        }
    } else {
        $log .= "Không có bài viết nào bị bỏ qua.\n";
    }

    if (!empty($results['error'])) {
        $log .= "\nLỗi: {$results['error']}\n";
    }

    // Save the log to a file
    file_put_contents(WP_CONTENT_DIR . '/seo_focus_keyword_update_log.txt', $log);

    return $results;
}

// Hook to check focus keyword via AJAX
add_action('wp_ajax_check_seo_keyword', 'run_check_seo_keyword');
function run_check_seo_keyword() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Không đủ quyền truy cập.');
    }

    $custom_prefix = isset($_POST['prefix']) ? sanitize_text_field($_POST['prefix']) : '';
    $results = check_and_update_seo_focus_keyword($custom_prefix);

    if (!empty($results['error'])) {
        wp_send_json_error($results['error']);
    }

    $response = '<div class="seo-keyword-results">';
    $response .= '<h3>Tổng số bài viết đã kiểm tra: ' . esc_html($results['processed']) . '</h3>';

    $response .= '<h4>Bài viết đã có Focus Keyword:</h4>';
    if (!empty($results['existing'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Meta Key</th><th>Meta Value</th><th>Link Edit</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['existing'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_key']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_value']) . '</td>';
            $response .= '<td><a href="' . esc_url($post['edit_link']) . '" target="_blank">Chỉnh sửa</a></td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào đã có Focus Keyword.</p>';
    }

    $response .= '<h4>Bài viết cần cập nhật Focus Keyword (trống):</h4>';
    if (!empty($results['updated'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Meta Key</th><th>Meta Value</th><th>Link Edit</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['updated'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_key']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_value']) . '</td>';
            $response .= '<td><a href="' . esc_url($post['edit_link']) . '" target="_blank">Chỉnh sửa</a></td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào có giá trị Focus Keyword trống.</p>';
    }

    $response .= '<h4>Bài viết cần thêm Focus Keyword mới:</h4>';
    if (!empty($results['added'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Meta Key</th><th>Meta Value</th><th>Link Edit</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['added'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_key']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_value']) . '</td>';
            $response .= '<td><a href="' . esc_url($post['edit_link']) . '" target="_blank">Chỉnh sửa</a></td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào cần thêm Focus Keyword mới.</p>';
    }

    $response .= '<h4>Bài viết bị bỏ qua (không có link chỉnh sửa hợp lệ):</h4>';
    if (!empty($results['skipped'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Lý do</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['skipped'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['reason']) . '</td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào bị bỏ qua.</p>';
    }
    $response .= '</div>';

    wp_send_json_success($response);
}

// Hook to update focus keyword via AJAX
add_action('wp_ajax_update_seo_keyword', 'run_update_seo_keyword');
function run_update_seo_keyword() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Không đủ quyền truy cập.');
    }

    $custom_prefix = isset($_POST['prefix']) ? sanitize_text_field($_POST['prefix']) : '';
    $results = update_seo_focus_keyword($custom_prefix);

    if (!empty($results['error'])) {
        wp_send_json_error($results['error']);
    }

    $response = '<div class="seo-keyword-results">';
    $response .= '<h3>Tổng số bài viết đã xử lý: ' . esc_html($results['processed']) . '</h3>';

    $response .= '<h4>Bài viết đã cập nhật Focus Keyword (trống thành tiêu đề):</h4>';
    if (!empty($results['updated'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Meta Key</th><th>Meta Value</th><th>Link Edit</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['updated'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_key']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_value']) . '</td>';
            $response .= '<td><a href="' . esc_url($post['edit_link']) . '" target="_blank">Chỉnh sửa</a></td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào có giá trị Focus Keyword trống để cập nhật.</p>';
    }

    $response .= '<h4>Bài viết đã thêm Focus Keyword mới:</h4>';
    if (!empty($results['added'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Meta Key</th><th>Meta Value</th><th>Link Edit</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['added'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_key']) . '</td>';
            $response .= '<td>' . esc_html($post['meta_value']) . '</td>';
            $response .= '<td><a href="' . esc_url($post['edit_link']) . '" target="_blank">Chỉnh sửa</a></td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào cần thêm Focus Keyword mới.</p>';
    }

    $response .= '<h4>Bài viết bị bỏ qua (không có link chỉnh sửa hợp lệ):</h4>';
    if (!empty($results['skipped'])) {
        $response .= '<table class="wp-list-table widefat fixed striped">';
        $response .= '<thead><tr>';
        $response .= '<th>Post ID</th><th>Tiêu đề</th><th>Lý do</th>';
        $response .= '</tr></thead><tbody>';
        foreach ($results['skipped'] as $post) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($post['post_id']) . '</td>';
            $response .= '<td>' . esc_html($post['post_title']) . '</td>';
            $response .= '<td>' . esc_html($post['reason']) . '</td>';
            $response .= '</tr>';
        }
        $response .= '</tbody></table>';
    } else {
        $response .= '<p>Không có bài viết nào bị bỏ qua.</p>';
    }
    $response .= '</div>';

    wp_send_json_success($response);
}



// Enqueue custom CSS for better UX/UI
add_action('admin_enqueue_scripts', 'seo_keyword_admin_styles');
function seo_keyword_admin_styles($hook) {
    if ($hook !== 'ophim_page_manage-seo-keyword') {
        return;
    }
    wp_enqueue_style('seo-keyword-admin-style', plugin_dir_url(__FILE__) . 'seo-keyword.css', array(), '1.1');
}

// Render admin page
function render_seo_keyword_page() {
    global $wpdb;
    $default_prefix = $wpdb->prefix;
    ?>
    <div class="wrap">
        <h1>Quản Lý Keyword</h1>
        <p>Nhập tiền tố bảng database (mặc định: <?php echo esc_html($default_prefix); ?>) và sử dụng các nút dưới đây để kiểm tra hoặc thêm/cập nhật Focus Keyword cho plugin SEO đang active (Rank Math hoặc Yoast SEO).</p>
        <form id="seo-keyword-form" class="seo-keyword-form">
            <label for="db-prefix">Tiền tố bảng:</label>
            <input type="text" id="db-prefix" name="db-prefix" value="<?php echo esc_attr($default_prefix); ?>" placeholder="Nhập tiền tố (ví dụ: wp_, gfNxY_)">
            <br><br>
            <button type="button" id="check-keyword-button" class="button button-primary">Kiểm tra Focus Keyword</button>
            <button type="button" id="update-keyword-button" class="button button-secondary">Chèn Focus Keyword</button>
        </form>
        <div id="keyword-result" class="seo-keyword-results"></div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#check-keyword-button').click(function() {
                var prefix = $('#db-prefix').val();
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'check_seo_keyword',
                        prefix: prefix
                    },
                    success: function(response) {
                        $('#keyword-result').html(response.data);
                    },
                    error: function(response) {
                        $('#keyword-result').html('<p class="error">Lỗi: ' + response.responseJSON.data + '</p>');
                    }
                });
            });

            $('#update-keyword-button').click(function() {
                if (confirm('Bạn có chắc chắn muốn thêm/cập nhật Focus Keyword cho các bài viết?')) {
                    var prefix = $('#db-prefix').val();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'update_seo_keyword',
                            prefix: prefix
                        },
                        success: function(response) {
                            $('#keyword-result').html(response.data);
                        },
                        error: function(response) {
                            $('#keyword-result').html('<p class="error">Lỗi: ' + response.responseJSON.data + '</p>');
                        }
                    });
                }
            });
        });
    </script>
    <?php
}