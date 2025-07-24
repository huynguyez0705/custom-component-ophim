<?php
/*
Plugin Name: Ophim Category Manager Plugin
Description: Manages 'ophim' posts with meta_key 'ophim_lang' containing 'thuyết minh' or 'lồng tiếng' and meta_key 'ophim_featured_post' with value 1, allowing assignment of multiple categories.
Version:     2.2
Author:      Cô Cô
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ophim_Category_Manager_Plugin {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=ophim',
			'Quản lý Danh Mục',
			'Quản lý Danh Mục',
			'manage_options',
			'ophim-categories',
			[ $this, 'admin_page' ]
		);
	}

	public function admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Bạn không có quyền truy cập trang này.' );
		}

		// Xử lý form Gán danh mục
		if ( isset( $_POST['otm_action'] ) && $_POST['otm_action'] === 'assign' && check_admin_referer( 'otm_nonce_action_assign', 'otm_nonce_field' ) ) {
			$selected = isset( $_POST['otm_categories'] ) ? array_map( 'intval', (array) $_POST['otm_categories'] ) : [];
			$type = isset( $_POST['otm_type'] ) ? sanitize_text_field( $_POST['otm_type'] ) : 'thuyet_minh';
			$type_label = $type === 'thuyet_minh' ? 'thuyết minh' : ( $type === 'long_tieng' ? 'lồng tiếng' : 'phim hot' );

			error_log( '[Ophim Plugin] POST Data (Assign): ' . print_r( $_POST, true ) );

			if ( empty( $selected ) ) {
				echo '<div class="notice notice-error"><p>Vui lòng chọn ít nhất một danh mục để gán.</p></div>';
			} else {
				$found_ids = $this->scan_posts( $type );
				if ( empty( $found_ids ) ) {
					echo '<div class="notice notice-warning"><p>Không có bài viết “' . esc_html( $type_label ) . '” để gán.</p></div>';
				} else {
					$failed = 0;
					foreach ( $found_ids as $post_id ) {
						$result = wp_set_post_terms( $post_id, $selected, 'ophim_categories', true );
						if ( is_wp_error( $result ) ) {
							$failed++;
							error_log( '[Ophim Plugin] Error setting terms for post ID ' . $post_id . ': ' . $result->get_error_message() );
						}
					}
					$term_names = [];
					foreach ( $selected as $term_id ) {
						$term = get_term( $term_id, 'ophim_categories' );
						if ( $term && ! is_wp_error( $term ) ) {
							$term_names[] = $term->name;
						}
					}
					$count = count( $found_ids ) - $failed;
					if ( $count > 0 ) {
						echo '<div class="notice notice-success"><p>Đã gán danh mục <strong>' 
							. esc_html( implode( ', ', $term_names ) ) 
							. '</strong> cho <strong>' . $count . '</strong> bài viết “' 
							. esc_html( $type_label ) . '”.</p></div>';
						if ( $failed > 0 ) {
							echo '<div class="notice notice-error"><p>' . $failed . ' bài viết không thể gán danh mục do lỗi. Vui lòng kiểm tra log lỗi.</p></div>';
						}
					} else {
						echo '<div class="notice notice-error"><p>Không thể gán danh mục cho bất kỳ bài viết nào. Vui lòng kiểm tra log lỗi.</p></div>';
					}
					error_log( '[Ophim Plugin] Gán danh mục hoàn tất. Thành công: ' . $count . ', Lỗi: ' . $failed );
				}
			}
		}

		// Xử lý form Kiểm tra
		if ( isset( $_POST['otm_action'] ) && $_POST['otm_action'] === 'check' && check_admin_referer( 'otm_nonce_action_check', 'otm_nonce_field' ) ) {
			$type = isset( $_POST['otm_type'] ) ? sanitize_text_field( $_POST['otm_type'] ) : 'thuyet_minh';
			$type_label = $type === 'thuyet_minh' ? 'thuyết minh' : ( $type === 'long_tieng' ? 'lồng tiếng' : 'phim hot' );
			$found_ids = $this->scan_posts( $type );
			if ( empty( $found_ids ) ) {
				echo '<div class="notice notice-warning"><p>Không tìm thấy bài viết “' . esc_html( $type_label ) . '”.</p></div>';
			} else {
				$total = count( $found_ids );
				echo '<h2>Tổng số bài viết “' . esc_html( $type_label ) . '”: ' . $total . '</h2>';
				echo '<div class="ophim-table-wrapper">';
				echo '<table class="wp-list-table widefat fixed striped">';
				echo '<thead><tr><th scope="col" style="width:80px;">ID</th><th scope="col">Tiêu đề</th></tr></thead>';
				echo '<tbody>';
				foreach ( $found_ids as $post_id ) {
					$title = get_the_title( $post_id );
					$edit_link = get_edit_post_link( $post_id );
					echo '<tr>';
					echo '<td>' . esc_html( $post_id ) . '</td>';
					echo '<td><a href="' . esc_url( $edit_link ) . '" target="_blank">' . esc_html( $title ) . '</a></td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		}

		// Lấy tất cả term
		$terms = get_terms( [
			'taxonomy'   => 'ophim_categories',
			'hide_empty' => false,
		] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			echo '<div class="notice notice-error"><p>Không tìm thấy danh mục trong taxonomy <strong>ophim_categories</strong>. Vui lòng tạo danh mục tại <a href="' . admin_url( 'edit-tags.php?taxonomy=ophim_categories&post_type=ophim' ) . '">đây</a>.</p></div>';
			return;
		}
?>
<div class="wrap">
	<style>
		.ophim-title{font-size:2rem;font-weight:600;color:#1a1a1a;margin-bottom:2rem}
.ophim-form-container{background:#ffffff;border-radius:12px;padding:2rem;margin-bottom:2rem;box-shadow:0 4px 20px rgba(0,0,0,0.1);transition:transform 0.3s ease}
.otm-categories-select{width:100%;min-width:300px;height:150px;padding:0.75rem;border:2px solid #e5e7eb;border-radius:8px;background:#f9fafb;font-size:1rem;transition:border-color 0.3s ease,box-shadow 0.3s ease}
.otm-categories-select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.2);outline:none}
.otm-categories-select option{padding:0.5rem 0.75rem;font-size:0.95rem;color:#1f2937;background:#ffffff;transition:background-color 0.2s ease,color 0.2s ease}
.otm-categories-select option:hover,.otm-categories-select option:checked{background:#3b82f6;color:#ffffff}
.ophim-radio-group{display:flex;gap:1.5rem;margin:1.5rem 0}
.ophim-radio-group label{display:flex;align-items:center;gap:0.5rem;font-size:1rem;color:#374151;cursor:pointer}
.ophim-radio-group input[type="radio"]{accent-color:#3b82f6;width:1.25rem;height:1.25rem;transition:transform 0.2s ease}
.ophim-radio-group input[type="radio"]:hover{transform:scale(1.1)}
input[type=radio]:checked::before{width:.75rem;height:.75rem}
.button-primary,.button-secondary{padding:0.75rem 1.5rem;border-radius:8px;font-weight:500;transition:all 0.3s ease}
.button-primary{background:#3b82f6;border:none;color:white}
.button-primary:hover{background:#2563eb;transform:translateY(-2px)}
.button-secondary{background:#10b981;border:none;color:white}
.button-secondary:hover{background:#059669;transform:translateY(-2px)}
.ophim-table-wrapper{margin-top:2rem;max-height:400px;overflow-y:auto;overflow-x:auto;scrollbar-width:thin;scrollbar-color:#3b82f6 #f1f5f9}
.ophim-table-wrapper::-webkit-scrollbar{width:8px;height:8px}
.ophim-table-wrapper::-webkit-scrollbar-track{background:#f1f5f9;border-radius:4px}
.ophim-table-wrapper::-webkit-scrollbar-thumb{background:#3b82f6;border-radius:4px}
.ophim-table-wrapper::-webkit-scrollbar-thumb:hover{background:#2563eb}
.wp-list-table{border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
.wp-list-table th{background:#f1f5f9;color:#1f2937;font-weight:600;padding:1rem}
.wp-list-table td{padding:1rem;color:#374151}
.wp-list-table tr:hover{background:#f9fafb}
.wp-list-table a{color:#3b82f6;text-decoration:none;transition:color 0.2s ease}
.wp-list-table a:hover{color:#2563eb;text-decoration:underline}
.notice{border-radius:8px;padding:1rem;margin-bottom:1.5rem}
.notice-success{background:#ecfdf5;border-left:4px solid #10b981;color:#065f46}
.notice-error{background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b}
.notice-warning{background:#fffbeb;border-left:4px solid #f59e0b;color:#92400e}
.description{color:#6b7280;font-size:0.875rem;margin-top:0.5rem}
	</style>
	<h1 class="ophim-title">Quản lý Danh Mục cho Ophim</h1>

	<!-- Form Gán danh mục -->
	<div class="ophim-form-container" id="ophim-form-container">
		<form method="post">
			<?php wp_nonce_field( 'otm_nonce_action_assign', 'otm_nonce_field' ); ?>
			<input type="hidden" name="otm_action" value="assign">
			<label for="otm_categories"><strong>Chọn danh mục để gán:</strong></label><br>
			<select name="otm_categories[]" id="otm_categories" multiple class="otm-categories-select" style="min-width:300px; height:150px;">
				<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>">
					<?php echo esc_html( $term->name ); ?> (ID: <?php echo esc_attr( $term->term_id ); ?>)
				</option>
				<?php endforeach; ?>
			</select>
			<p class="description">Giữ Ctrl (Cmd) để chọn nhiều danh mục cùng lúc.</p>

			<h3>Loại bài viết:</h3>
			<div class="ophim-radio-group">
				<label><input type="radio" name="otm_type" value="thuyet_minh" checked> Thuyết Minh</label>
				<label><input type="radio" name="otm_type" value="long_tieng"> Lồng Tiếng</label>
				<label><input type="radio" name="otm_type" value="hot"> Phim Hot</label>
			</div>

			<?php submit_button( 'Thêm vào danh mục', 'secondary' ); ?>
		</form>
	</div>

	<!-- Form Kiểm tra -->
	<div class="ophim-form-container">
		<form method="post">
			<?php wp_nonce_field( 'otm_nonce_action_check', 'otm_nonce_field' ); ?>
			<input type="hidden" name="otm_action" value="check">
			<h3>Loại bài viết:</h3>
			<div class="ophim-radio-group">
				<label><input type="radio" name="otm_type" value="thuyet_minh" checked> Thuyết Minh</label>
				<label><input type="radio" name="otm_type" value="long_tieng"> Lồng Tiếng</label>
				<label><input type="radio" name="otm_type" value="hot"> Phim Hot</label>
			</div>
			<?php submit_button( 'Kiểm tra bài viết', 'primary' ); ?>
		</form>
	</div>
</div>
<?php
	}

	private function scan_posts( $type = 'thuyet_minh' ) {
		$ids = [];
		if ( $type === 'thuyet_minh' || $type === 'long_tieng' ) {
			$query_args = [
				'post_type'      => 'ophim',
				'posts_per_page' => -1,
				'meta_key'       => 'ophim_lang',
				'fields'         => 'ids',
				'post_status'    => 'publish',
			];
			$query = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				$search_term = $type === 'thuyet_minh' ? 'thuyết minh' : 'lồng tiếng';
				foreach ( $query->posts as $post_id ) {
					$lang = get_post_meta( $post_id, 'ophim_lang', true );
					$clean = mb_strtolower( str_replace( '+', ' ', $lang ), 'UTF-8' );
					if ( strpos( $clean, $search_term ) !== false ) {
						$ids[] = $post_id;
					}
				}
			}
			wp_reset_postdata();
		} elseif ( $type === 'hot' ) {
			$query_args = [
				'post_type'      => 'ophim',
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'     => 'ophim_featured_post',
						'value'   => '1',
						'compare' => '=',
					],
				],
				'fields'         => 'ids',
				'post_status'    => 'publish',
			];
			$query = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				$ids = $query->posts;
			}
			wp_reset_postdata();
		}
		error_log( '[Ophim Plugin] Scan posts for type ' . $type . ': Found ' . count( $ids ) . ' posts.' );
		return $ids;
	}
}

new Ophim_Category_Manager_Plugin();