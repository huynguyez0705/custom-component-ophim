<?php

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
	<div id="ophim-popup" class="ophim-popup">
		<div class="ophim-popup-content">
			<button id="popup-close" class="ophim-close-btn">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
					<path fill="#fff" d="M12 10.586l5.707-5.707a1 1 0 0 1 1.414 1.414L13.414 12l5.707 5.707a1 1 0 0 1-1.414 1.414L12 13.414l-5.707 5.707a1 1 0 0 1-1.414-1.414L10.586 12 4.879 6.293a1 1 0 0 1 1.414-1.414L12 10.586z"/>
				</svg>
			</button>
			<h3 id="popup-title"></h3>
			<p id="popup-message"></p>
			<div class="ophim-popup-buttons">
				<button id="popup-confirm" class="ophim-btn">✅ Xác nhận</button>
				<button id="popup-cancel" class="ophim-btn">❌ Hủy</button>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="<?php echo plugins_url('post-meta.css', __FILE__) . '?v=' . filemtime(plugin_dir_path(__FILE__) . 'post-meta.css'); ?>">
<script>
	jQuery(document).ready(function($) {
		let currentRow, currentMetaKey, currentMetaValue;

		function showPopup(title, message, onConfirm) {
			$('#popup-title').text(title);
			$('#popup-message').text(message);
			$('#ophim-popup').addClass('show');
			$('#popup-confirm').off('click').on('click', function() {
				$('#ophim-popup').removeClass('show');
				onConfirm();
			});
			$('#popup-cancel, #popup-close').off('click').on('click', function() {
				$('#ophim-popup').removeClass('show');
			});
		}

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
			currentRow = $(this).closest('.ophim-table-row');
			currentMetaKey = currentRow.find('.meta-key').text();
			currentMetaValue = currentRow.find('.meta-value').text();
			showPopup(
				'Xác nhận chỉnh sửa',
				'Bạn có muốn sửa giá trị cho meta key "' + currentMetaKey + '"?',
				function() {
					if (currentMetaKey === 'ophim_episode_list') {
						currentRow.html(`
<div class="ophim-table-cell meta-key">${currentMetaKey}</div>
<div class="ophim-table-cell"><textarea class="edit-value">${currentMetaValue}</textarea></div>
<div class="ophim-table-cell"><button class="ophim-save-btn ophim-btn">Save</button></div>
`);
					} else {
						currentRow.html(`
<div class="ophim-table-cell meta-key">${currentMetaKey}</div>
<div class="ophim-table-cell"><input type="text" class="edit-value" value="${currentMetaValue}"></div>
<div class="ophim-table-cell"><button class="ophim-save-btn ophim-btn">Save</button></div>
`);
					}
				}
			);
		});

		$(document).on('click', '.ophim-save-btn', function() {
			currentRow = $(this).closest('.ophim-table-row');
			var post_id = $('#post_id').val();
			currentMetaKey = currentRow.find('.meta-key').text();
			currentMetaValue = currentRow.find('.edit-value').val() || currentRow.find('textarea.edit-value').val();
			showPopup(
				'Xác nhận lưu',
				'Bạn có muốn lưu giá trị mới cho meta key "' + currentMetaKey + '"?',
				function() {
					$.ajax({
						url: '<?php echo admin_url('admin-post.php'); ?>',
						type: 'POST',
						data: {
							action: 'update_postmeta',
							post_id: post_id,
							meta_key: currentMetaKey,
							meta_value: currentMetaValue
						},
						success: function(response) {
							var valueCell = (currentMetaKey === 'ophim_episode_list') 
							? `<div class="meta-value scroll">${currentMetaValue}</div>` 
							: `<div class="meta-value">${currentMetaValue}</div>`;
							currentRow.html(`
<div class="ophim-table-cell meta-key">${currentMetaKey}</div>
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
			);
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