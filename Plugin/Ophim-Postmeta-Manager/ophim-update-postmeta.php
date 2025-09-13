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
        <style>
.ophim-wrap { 
    background: #f9f9f9; 
    padding: 20px; 
    border-radius: 8px; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    overflow: hidden; 
}

.ophim-title { 
    color: #2c3e50; 
    margin-bottom: 20px; 
}

.ophim-input-group { 
    margin-bottom: 20px; 
}

.ophim-input-group label { 
    display: block; 
    font-weight: 500; 
    margin-bottom: 5px; 
    color: #34495e; 
}

.ophim-input-group input { 
    padding: 8px; 
    width: 200px; 
    border: 1px solid #ddd; 
    border-radius: 4px; 
    font-size: 14px; 
}

.ophim-btn { 
    padding: 8px 15px; 
    background: #3498db; 
    color: white; 
    border: none; 
    border-radius: 4px; 
    cursor: pointer; 
    margin-left: 10px; 
}

.ophim-btn:hover { 
    background: #2980b9; 
}

.ophim-table-container {  
    max-width: 100%; 
    overflow-x: auto; 
}

.ophim-table { 
    display: flex; 
    flex-direction: column; 
    max-width: 100%;  
    overflow-x: auto;
}

 .ophim-table-cell:nth-child(1) { 
    width: 25%; 
}

 .ophim-table-cell:nth-child(2) { 
    width: 60%; 
}

 .ophim-table-cell:nth-child(3) { 
    width: 15%; 
    text-align: center; 
}

.ophim-table-header { 
    display: flex;
    width: 100%;
}

.ophim-table-header .ophim-table-cell { 
    background: #3498db; 
    color: white; 
    font-weight: bold; 
    padding: 12px; 
    text-align: center; 

    border:2px solid;
}

.ophim-table-body { 
    display: flex;
    flex-direction: column;
}

.ophim-table-body .ophim-table-row { 
    display: flex; 
    width: 100%; 
}

.ophim-table-body .ophim-table-cell { 
    padding: 12px; 
    border:2px solid #fff;

}

.ophim-table-body .ophim-table-cell:nth-child(3) { 
    text-align: center; 
}

.ophim-table-cell input { 
    padding: 6px; 
    max-width: 100%; 
    border: 1px solid #ddd; 
    border-radius: 4px; 
    overflow: scroll;
}

.scroll { 
    max-height: 100px; 
    overflow-y: auto; 
    display: block; 
}

textarea { 
    width: 100%; 
    height: 100px; 
    padding: 6px; 
    border: 1px solid #ddd; 
    border-radius: 4px; 
    resize: vertical; 
}

        </style>
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

                $.ajax({
                    url: '<?php echo admin_url('admin-post.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'update_postmeta',
                        post_id: post_id,
                        meta_key: metaKey,
                        meta_value: metaValue
                    },
                    success: function() {
                        var valueCell = (metaKey === 'ophim_episode_list') 
                            ? `<div class="meta-value scroll">${metaValue}</div>` 
                            : `<div class="meta-value">${metaValue}</div>`;
                        $row.html(`
                            <div class="ophim-table-cell meta-key">${metaKey}</div>
                            <div class="ophim-table-cell">${valueCell}</div>
                            <div class="ophim-table-cell"><button class="ophim-edit-btn ophim-btn">Edit</button></div>
                        `);
                    }
                });
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
            wp_die('Thiếu thông tin yêu cầu!');
        }

        $post_id = intval($_POST['post_id']);
        $meta_key = sanitize_text_field($_POST['meta_key']);
        $meta_value = $_POST['meta_value']; // Không sanitize vì ophim_episode_list có thể chứa serialized data

        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'postmeta',
            ['meta_value' => $meta_value],
            ['post_id' => $post_id, 'meta_key' => $meta_key]
        );

        wp_die();
    }
}

new Ophim_Update_Postmeta_Plugin();
?>