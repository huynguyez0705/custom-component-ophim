<?php
/**
 * Plugin Name: Ophim Update Postmeta
 * Description: Cập nhật giá trị trong bảng postmeta
 * Version: 1.0
 * Author: Bạn
 */

class Ophim_Update_Postmeta_Plugin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_update_postmeta', [$this, 'update_postmeta']);
        add_action('wp_ajax_get_meta_data', [$this, 'ajax_get_meta_data']);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ophim',
            'Quản Lý Postmeta',
            'Quản Lý Postmeta',
            'manage_options',
            'ophim-update-postmeta',
            [$this, 'admin_page']
        );
    }

    public function admin_page() {
        ?>
        <div class="wrap ophim-wrap">
            <h1 class="ophim-title">Quản Lý Postmeta</h1>
            <div class="ophim-input-group">
                <label for="post_id">Post ID</label>
                <input type="text" id="post_id" value="" required>
                <button id="load-meta" class="ophim-btn">Load Meta Data</button>
            </div>
            <div id="meta-table-container" class="ophim-table-container">
                <div id="meta-table" class="ophim-table">
                    <div class="ophim-table-header">
                        <div class="ophim-table-cell">Meta Key</div>
                        <div class="ophim-table-cell">Meta Value</div>
                        <div class="ophim-table-cell">Action</div>
                    </div>
                    <div id="meta-table-body" class="ophim-table-body"></div>
                </div>
            </div>
        </div>
        <link rel="stylesheet" href="<?php echo plugins_url('post-meta.css', __FILE__); ?>">
        <script>
        jQuery(document).ready(function($) {
            $('#load-meta').on('click', function() {
                var post_id = $('#post_id').val();
                if (post_id) {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: { action: 'get_meta_data', post_id: post_id },
                        success: function(response) {
                            $('#meta-table-body').html(response);
                        },
                        error: function() {
                            $('#meta-table-body').html('<div class="ophim-table-row"><div class="ophim-table-cell" style="grid-column: span 3;">Lỗi khi tải dữ liệu</div></div>');
                        }
                    });
                }
            });

            $(document).on('click', '.ophim-edit-btn', function() {
                var $row = $(this).closest('.ophim-table-row');
                var metaKey = $row.find('.meta-key').text();
                var metaValue = $row.find('.meta-value').text();
                if (confirm('Bạn có muốn sửa giá trị này?')) {
                    if (metaKey === 'ophim_episode_list') {
                        $row.html(`
                            <div class="ophim-table-cell meta-key">${metaKey}</div>
                            <div class="ophim-table-cell"><textarea class="edit-value">${metaValue}</textarea></div>
                            <div class="ophim-table-cell"><button class="ophim-save-btn ophim-btn">Save</button></div>
                        `);
                    } else {
                        $row.html(`
                            <div class="ophim-table-cell meta-key">${metaKey}</div>
                            <div class="ophim-table-cell"><input type="text" class="edit-value" value="${metaValue}"></div>
                            <div class="ophim-table-cell"><button class="ophim-save-btn ophim-btn">Save</button></div>
                        `);
                    }
                }
            });

            $(document).on('click', '.ophim-save-btn', function() {
                var $row = $(this).closest('.ophim-table-row');
                var post_id = $('#post_id').val();
                var metaKey = $row.find('.meta-key').text();
                var metaValue = $row.find('.edit-value').val() || $row.find('textarea.edit-value').val();

                if (confirm('Bạn có muốn lưu giá trị này?')) {
                    $.ajax({
                        url: '<?php echo admin_url('admin-post.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'update_postmeta',
                            post_id: post_id,
                            meta_key: metaKey,
                            meta_value: metaValue
                        },
                        success: function(response) {
                            var valueCell = (metaKey === 'ophim_episode_list') 
                                ? `<div class="meta-value scroll">${metaValue}</div>` 
                                : `<div class="meta-value">${metaValue}</div>`;
                            $row.html(`
                                <div class="ophim-table-cell meta-key">${metaKey}</div>
                                <div class="ophim-table-cell">${valueCell}</div>
                                <div class="ophim-table-cell"><button class="ophim-edit-btn ophim-btn">Edit</button></div>
                            `);
                            $('#meta-table-container').before('<div class="ophim-message">Lưu thành công!</div>');
                            setTimeout(function() { $('.ophim-message').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', xhr.responseText);
                            $('#meta-table-container').before('<div class="ophim-message error">Lỗi khi lưu dữ liệu: ' + error + '</div>');
                            setTimeout(function() { $('.ophim-message').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }

    public function ajax_get_meta_data() {
        if (!isset($_POST['post_id'])) {
            echo '<div class="ophim-table-row"><div class="ophim-table-cell" style="grid-column: span 3;">Không có post_id</div></div>';
            wp_die();
        }

        $post_id = intval($_POST['post_id']);
        if (!current_user_can('edit_post', $post_id)) {
            echo '<div class="ophim-table-row"><div class="ophim-table-cell" style="grid-column: span 3;">Bạn không có quyền truy cập</div></div>';
            wp_die();
        }

        global $wpdb;
        $meta_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d",
                $post_id
            )
        );

        $output = '';
        if ($meta_data) {
            foreach ($meta_data as $data) {
                $output .= '<div class="ophim-table-row">';
                $output .= '<div class="ophim-table-cell meta-key">' . esc_html($data->meta_key) . '</div>';
                if ($data->meta_key === 'ophim_episode_list') {
                    $output .= '<div class="ophim-table-cell"><div class="meta-value scroll">' . esc_html($data->meta_value) . '</div></div>';
                } else {
                    $output .= '<div class="ophim-table-cell"><div class="meta-value">' . esc_html($data->meta_value) . '</div></div>';
                }
                $output .= '<div class="ophim-table-cell"><button class="ophim-edit-btn ophim-btn">Edit</button></div>';
                $output .= '</div>';
            }
        } else {
            $output .= '<div class="ophim-table-row"><div class="ophim-table-cell" style="grid-column: span 3;">Không có meta data</div></div>';
        }

        echo $output;
        wp_die();
    }

    public function update_postmeta() {
        if (!isset($_POST['post_id'], $_POST['meta_key'], $_POST['meta_value'])) {
            wp_send_json_error('Thiếu thông tin yêu cầu!');
        }

        $post_id = intval($_POST['post_id']);
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Bạn không có quyền chỉnh sửa!');
        }

        $meta_key = sanitize_text_field($_POST['meta_key']);
        $meta_value = $_POST['meta_value']; // Không sanitize vì ophim_episode_list có thể chứa serialized data

        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'postmeta',
            ['meta_value' => $meta_value],
            ['post_id' => $post_id, 'meta_key' => $meta_key]
        );

        if ($result !== false) {
            wp_send_json_success('Lưu thành công!');
        } else {
            wp_send_json_error('Lỗi khi lưu dữ liệu!');
        }
    }
}

new Ophim_Update_Postmeta_Plugin();
?>