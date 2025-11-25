<?php

/**
 * Plugin Name: SignalTrue Ophim Comments
 * Description: Hệ thống bình luận AJAX, Turnstile, avatar chữ cái, hỗ trợ Admin/pinned cho phim (post_type = ophim).
 * Version:     2.3
 * Author:      Dương Quá
 */

if (!defined('ABSPATH')) {
	exit;
}

define('OP_SC_VERSION', '2.3');
define('OP_SC_DIR', plugin_dir_path(__FILE__));
define('OP_SC_URL', plugin_dir_url(__FILE__));

class OP_SignalTrue_Comments
{

	public $dashboard;

	public function __construct()
	{
		require_once OP_SC_DIR . 'includes/class-op-dashboard.php';
		$this->dashboard = new OP_Comment_Dashboard();

		add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

		add_shortcode('op_comments', [$this, 'shortcode']);
		// Keep old shortcode for compatibility if needed, or just use new one. User asked for "signaltrue", so maybe "op_comments" is better.
		add_shortcode('msc_comments', [$this, 'shortcode']);

		// Action hook cho theme: do_action('op_show_comments');
		add_action('op_show_comments', [$this, 'render_comments']);

		add_action('wp_ajax_op_load_comments', [$this, 'ajax_load_comments']);
		add_action('wp_ajax_nopriv_op_load_comments', [$this, 'ajax_load_comments']);

		add_action('wp_ajax_op_load_replies', [$this, 'ajax_load_replies']);
		add_action('wp_ajax_nopriv_op_load_replies', [$this, 'ajax_load_replies']);

		add_action('wp_ajax_op_submit_comment_ajax', [$this, 'ajax_submit_comment']);
		add_action('wp_ajax_nopriv_op_submit_comment_ajax', [$this, 'ajax_submit_comment']);

		add_action('admin_post_op_submit_comment', [$this, 'handle_non_ajax_submit']);
		add_action('admin_post_nopriv_op_submit_comment', [$this, 'handle_non_ajax_submit']);

		add_action('admin_menu', [$this, 'add_admin_menu']);
		add_action('admin_init', [$this, 'register_settings']);

		// Meta box for per-post comment toggle
		add_action('add_meta_boxes', [$this, 'add_comment_meta_box']);
		add_action('save_post', [$this, 'save_comment_meta_box']);
	}

	/** chỉ chạy trên single ophim */
	protected function is_target_screen(): bool
	{
		return is_singular('ophim');
	}

	/** Enqueue CSS + JS */
	public function enqueue_assets()
	{
		if (!$this->is_target_screen()) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'op-comments-style',
			OP_SC_URL . 'public/css/signaltrue.min.css',
			[],
			OP_SC_VERSION
		);

		// JS
		wp_enqueue_script(
			'op-comments-script',
			OP_SC_URL . 'public/js/signaltrue.min.js',
			['jquery'],
			OP_SC_VERSION,
			true
		);

		// Localize
		$nonce = wp_create_nonce('op_sc_nonce');
		$sitekey = get_option('op_turnstile_sitekey');
		$secret = get_option('op_turnstile_secret');
		$turnstile_enabled = $sitekey && $secret;

		if ($turnstile_enabled) {
			wp_enqueue_script('op-turnstile-api', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit', [], null, true);
		}

		wp_localize_script('op-comments-script', 'OP_AJAX', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => $nonce,
			'turnstile_enabled' => $turnstile_enabled,
			'turnstile_sitekey' => $sitekey,
		]);
	}

	/** Action callback: echo shortcode content */
	public function render_comments()
	{
		echo $this->shortcode([]);
	}

	/** Shortcode: HTML khung comment + form */
	public function shortcode($atts)
	{
		if (!$this->is_target_screen()) {
			return '';
		}

		// Check if comments are enabled globally
		if (get_option('op_comments_enabled', '1') !== '1') {
			return '<div class="op-wrap"><div class="op-card"><p style="text-align:center;color:#999;">Hệ thống bình luận tạm thời đóng.</p></div></div>';
		}

		global $post;
		if (!$post) {
			return '';
		}

		// Check if comments are enabled for this specific post
		$post_enabled = get_post_meta($post->ID, '_op_comments_enabled', true);
		$post_enabled = $post_enabled === '' ? '1' : $post_enabled; // Default enabled

		if ($post_enabled !== '1') {
			return '<div class="op-wrap"><div class="op-card"><p style="text-align:center;color:#999;">Bình luận đã tắt cho bài viết này.</p></div></div>';
		}

		$post_id = $post->ID;
		$nonce = wp_create_nonce('op_sc_submit_comment');
		$referer = esc_url($_SERVER['REQUEST_URI'] ?? '');
		$comment_count = get_comments_number($post_id);

		ob_start();
		?>
		<div id="comment-area" class="child-box child-discuss">
			<div class="op-wrap" data-post="<?php echo esc_attr($post_id); ?>">
				<div class="op-header">
					<div class="op-header-left">
						<div class="inc-icon"><svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21"
								fill="none">
								<g clip-path="url(#clip0_281_3026)">
									<path
										d="M14.499 0.5H6.50109C3.19363 0.5 0.502686 3.19095 0.502686 6.4984V11.1638C0.502686 14.3596 3.01468 16.9796 6.16784 17.1532V19.9338C6.16784 20.2461 6.42244 20.5 6.73536 20.5C6.88498 20.5 7.02661 20.4407 7.13358 20.3337L7.75875 19.7085C9.40031 18.0666 11.5834 17.1622 13.9054 17.1622H14.499C17.8064 17.1622 20.4974 14.4713 20.4974 11.1638V6.4984C20.4974 3.19095 17.8064 0.5 14.499 0.5ZM6.16784 10.1641C5.4327 10.1641 4.83486 9.56625 4.83486 8.83111C4.83486 8.09597 5.4327 7.49813 6.16784 7.49813C6.90298 7.49813 7.50082 8.09597 7.50082 8.83111C7.50082 9.56625 6.90265 10.1641 6.16784 10.1641ZM10.5 10.1641C9.76488 10.1641 9.16704 9.56625 9.16704 8.83111C9.16704 8.09597 9.76488 7.49813 10.5 7.49813C11.2352 7.49813 11.833 8.09597 11.833 8.83111C11.833 9.56625 11.2348 10.1641 10.5 10.1641ZM14.8322 10.1641C14.0971 10.1641 13.4992 9.56625 13.4992 8.83111C13.4992 8.09597 14.0971 7.49813 14.8322 7.49813C15.5673 7.49813 16.1652 8.09597 16.1652 8.83111C16.1652 9.56625 15.567 10.1641 14.8322 10.1641Z"
										fill="currentColor"></path>
								</g>
							</svg>
						</div>
						<span>Bình luận</span> <span class="count"><?php echo number_format_i18n($comment_count); ?></span>
					</div>
					<div class="op-tabs">
						<button class="op-tab-btn active" data-sort="newest">Mới nhất</button>
						<button class="op-tab-btn" data-sort="oldest">Cũ nhất</button>
						<div style="display:flex;align-items:center;gap:8px;margin-left:10px;">
							<span style="font-size:12px;opacity:0.7;">Ẩn/Hiện</span>
							<button class="op-toggle-switch" type="button" data-bs-toggle="collapse" data-bs-target="#opCommentFormBox"
								aria-expanded="true" aria-controls="opCommentFormBox"></button>
						</div>
					</div>
				</div>
				<div class="op-card">
					<div class="collapse show" id="opCommentFormBox">
						<div class="op-box">
							<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="op-form comment-form"
								data-ajax="1">

								<input type="hidden" id="op_nonce" name="op_nonce" value="<?php echo esc_attr($nonce); ?>">
								<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr($referer); ?>">
								<input type="hidden" name="action" value="op_submit_comment">
								<input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
								<input type="hidden" name="parent_id" value="0">
								<input type="text" name="website" class="op-hp" tabindex="-1" autocomplete="off">

								<div class="op-status" aria-live="polite" style="display:none;"></div>

								<div class="op-input-wrap">
									<textarea id="op-content" name="comment_content" required
										placeholder="Viết bình luận của bạn..."></textarea>
									<span class="counter">0/1000</span>
								</div>

								<?php if (!is_user_logged_in()): ?>
									<div class="op-grid">
										<div>
											<label class="op-label">Tên hiển thị</label>
											<input id="op-name" type="text" name="comment_author" placeholder="Nhập tên của bạn" required>
										</div>
										<div>
											<label class="op-label">Email (Bảo mật)</label>
											<input id="op-email" type="email" name="comment_author_email" placeholder="Nhập email" required>
										</div>
									</div>
								<?php else: ?>
									<input type="hidden" name="comment_author"
										value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
									<input type="hidden" name="comment_author_email"
										value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
								<?php endif; ?>

								<?php
								$sitekey = get_option('op_turnstile_sitekey');
								$turnstile_for_users = get_option('op_turnstile_for_users', '1');
								$show_turnstile = $sitekey && $turnstile_for_users === '1' && !current_user_can('manage_options');
								if ($show_turnstile): ?>
									<div class="op-turnstile-wrap">
										<div class="op-turnstile-container" data-sitekey="<?php echo esc_attr($sitekey); ?>" data-theme="dark"
											data-size="flexible"></div>
									</div>
								<?php endif; ?>

								<div class="op-footer">
									<div class="op-footer-left">
										<label class="reveal-toggle">
											<input type="checkbox" name="op_is_spoil" value="1">
											<div class="op-toggle-switch"></div>
											<span>Tiết lộ?</span>
										</label>

										<div class="avatar-picker">
											<div class="avatar-picker-btn">
												<div class="avatar-thumb"></div>
												<span>Chọn Avatar</span>
											</div>
											<input type="hidden" name="avatar_id" value="">
										</div>

										<?php if (current_user_can('manage_options')): ?>
											<label class="reveal-toggle op-pin-toggle" style="margin-left: 15px;">
												<input type="checkbox" name="is_pinned" value="1">
												<div class="op-toggle-switch"></div>
												<span>Ghim bình luận</span>
											</label>
										<?php endif; ?>
									</div>

									<button type="submit" class="btn-send">
										Gửi bình luận <span class="arrow"></span>
									</button>
								</div>
							</form>
						</div>
					</div> <!-- End collapse opCommentFormBox -->

					<div class="op-list-shell">
						<div class="op-list">
							<p class="op-empty">Đang tải bình luận...</p>
						</div>
						<button class="op-loadmore" style="display:none;">Xem thêm bình luận</button>
					</div>
				</div>
			</div>

			<!-- Avatar Modal -->
			<div class="op-avatar-modal">
				<div class="op-avatar-dialog">
					<button class="op-avatar-close" type="button">&times;</button>
					<h3>Chọn Avatar</h3>
					<p class="op-avatar-desc">Chọn một avatar để hiển thị bên cạnh bình luận của bạn.</p>

					<div class="op-avatar-grid">
						<?php
						// Tạo 9 avatar ngẫu nhiên
						for ($i = 0; $i < 9; $i++) {
							$seed = wp_generate_password(8, false);
							$colors = ['b6e3f4,c0aede', 'd1d4f9,b6e3f4', 'ffdfbf,ffd4d4', 'c0aede,ffdfbf', 'ffd4d4,d1d4f9'];
							$bg = $colors[array_rand($colors)];

							// Lưu value dạng seed|bg để gửi lên server
							$val = $seed . '|' . $bg;
							$url = 'https://api.dicebear.com/9.x/adventurer/svg?seed=' . $seed . '&backgroundType=gradientLinear&backgroundColor=' . $bg;
							?>
							<div class="op-avatar-option" data-id="<?php echo esc_attr($val); ?>">
								<img data-src="<?php echo esc_url($url); ?>"
									src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Avatar"
									loading="lazy">
							</div>
						<?php } ?>
					</div>

					<div class="op-avatar-actions">
						<button class="op-avatar-dismiss" type="button">Hủy</button>
						<button class="op-avatar-save" type="button">Lưu thay đổi</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/** Helper: tạo initials từ tên */
	protected function get_initials(string $name): string
	{
		$name = trim(wp_strip_all_tags($name));
		if ($name === '') {
			return '?';
		}
		$parts = preg_split('/\s+/', $name);
		$initials = '';
		foreach ($parts as $p) {
			if ($p === '') {
				continue;
			}
			$initials .= mb_strtoupper(mb_substr($p, 0, 1));
			if (mb_strlen($initials) >= 2) {
				break;
			}
		}
		return $initials ?: mb_strtoupper(mb_substr($name, 0, 1));
	}

	/** Helper: màu nền avatar chữ */
	protected function get_initial_bg_color(int $comment_id): string
	{
		$pal = [
			'#fb7185',
			'#f87171',
			'#34d399',
			'#a78bfa',
			'#facc15',
			'#38bdf8',
		];
		$idx = $comment_id % count($pal);
		return $pal[$idx];
	}

	/** Helper: filter blocked words */
	protected function filter_blocked_words(string $content): string
	{
		$blocked_words = get_option('op_blocked_words', '');
		if (empty($blocked_words)) {
			return $content;
		}

		$words = array_filter(array_map('trim', explode("\n", $blocked_words)));
		foreach ($words as $word) {
			if (empty($word))
				continue;
			$content = preg_replace('/\b' . preg_quote($word, '/') . '\b/iu', '***', $content);
		}
		return $content;
	}

	/** Render 1 comment item – giống HTML bạn gửi */
	protected function render_comment_item(WP_Comment $comment): string
	{
		static $admin_avatar_url = null;
		if ($admin_avatar_url === null) {
			$admin_avatar_url = get_site_icon_url(32);
		}

		$comment_id = $comment->comment_ID;
		$author = get_comment_author($comment);
		$content = apply_filters('comment_text', $comment->comment_content, $comment);
		$datetime = get_comment_date('d/m/Y H:i', $comment);
		$is_admin = user_can($comment->user_id, 'manage_options');
		$is_pinned = (bool) get_comment_meta($comment_id, 'op_pinned', true);
		$is_spoil = (bool) get_comment_meta($comment_id, 'op_is_spoil', true);

		// Avatar logic
		$avatar_html = '';
		$avatar_id = get_comment_meta($comment_id, 'op_avatar_id', true);

		if ($is_admin && $admin_avatar_url) {
			$avatar_html = '<img src="' . esc_url($admin_avatar_url) . '" alt="Admin" class="avatar">';
		} elseif ($avatar_id) {
			// Check format seed|bg
			if (strpos($avatar_id, '|') !== false) {
				list($seed, $bg) = explode('|', $avatar_id);
				$avatar_url = 'https://api.dicebear.com/9.x/adventurer/svg?seed=' . esc_attr($seed) . '&backgroundType=gradientLinear&backgroundColor=' . esc_attr($bg);
			} else {
				// Old format support
				$avatar_url = 'https://api.dicebear.com/9.x/adventurer/svg?seed=' . esc_attr($avatar_id) . '&backgroundType=gradientLinear';
			}
			$avatar_html = '<img src="' . esc_url($avatar_url) . '" alt="Avatar" class="avatar">';
		} else {
			$initials = $this->get_initials($author);
			$bg = $this->get_initial_bg_color($comment_id);
			$avatar_html = '<span class="op-initial-avatar avatar" style="background:' . esc_attr($bg) . ';">' . esc_html($initials) . '</span>';
		}

		ob_start();
		?>
		<div class="op-item<?php echo $is_admin ? ' op-item-admin' : ''; ?>" id="comment-<?php echo esc_attr($comment_id); ?>"
			data-comment="<?php echo esc_attr($comment_id); ?>">

			<div class="op-avatar"><?php echo $avatar_html; ?></div>

			<div class="op-body">
				<div class="op-head">
					<strong class="op-name<?php echo $is_admin ? ' op-name-admin' : ''; ?>"><?php echo esc_html($author); ?></strong>
					<?php if ($is_admin): ?>
						<span class="op-badge-admin" title="Quản trị viên">Admin</span>
					<?php endif; ?>
					<?php if ($is_pinned): ?>
						<span class="op-badge-pinned" title="Pinned">📌</span>
					<?php endif; ?>
					<span class="op-time"><?php echo esc_html($datetime); ?></span>
				</div>

				<div class="op-content<?php echo $is_spoil ? ' op-is-spoil' : ''; ?>">
					<?php if ($is_spoil): ?>
						<div class="op-spoil-overlay">
							<span>Nội dung có chứa tiết lộ nội dung phim.</span>
							<button type="button" class="op-spoil-btn">Xem</button>
						</div>
						<div class="op-spoil-text"><?php echo $content; ?></div>
					<?php else: ?>
						<?php echo $content; ?>
					<?php endif; ?>
				</div>

				<div class="op-actions">
					<button type="button" class="op-reply-toggle">Trả lời</button>
					<?php
					$reply_count = get_comments([
						'parent' => $comment_id,
						'count' => true,
						'status' => 'approve',
					]);
					if ($reply_count > 0): ?>
						<button type="button" class="op-replies-acc-btn op-view-replies">
							<span class="op-replies-acc-text">Xem trả lời (<?php echo (int) $reply_count; ?>)</span>
						</button>
					<?php endif; ?>
				</div>

				<div class="op-reply-form-wrap" style="display:none;"></div>

				<div class="op-replies-acc-panel" style="display:none;">
					<div class="op-replies" data-loaded="0"></div>
					<button type="button" class="op-loadmore-replies" style="display:none;">Xem thêm trả lời</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/** AJAX: load bình luận top-level */
	public function ajax_load_comments()
	{
		check_ajax_referer('op_sc_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;
		$sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'newest';
		$per_page = 10;

		if (!$post_id) {
			wp_send_json_error(['message' => 'Invalid post']);
		}

		$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

		$total = get_comments([
			'post_id' => $post_id,
			'parent' => 0,
			'status' => 'approve',
			'count' => true,
		]);

		$comments = get_comments([
			'post_id' => $post_id,
			'parent' => 0,
			'status' => 'approve',
			'number' => $per_page,
			'offset' => $offset,
			'orderby' => 'comment_date_gmt',
			'order' => $order,
		]);

		$html = '';
		foreach ($comments as $c) {
			$html .= $this->render_comment_item($c);
		}

		$loaded = $offset + count($comments);

		wp_send_json_success([
			'html' => $html,
			'total' => (int) $total,
			'loaded' => (int) $loaded,
		]);
	}

	/** AJAX: load replies cho 1 comment */
	public function ajax_load_replies()
	{
		check_ajax_referer('op_sc_nonce', 'nonce');

		$parent_id = isset($_POST['parent_id']) ? absint($_POST['parent_id']) : 0;
		$offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;
		$per_page = 10;

		if (!$parent_id) {
			wp_send_json_error(['message' => 'Invalid parent']);
		}

		$total = get_comments([
			'parent' => $parent_id,
			'status' => 'approve',
			'count' => true,
		]);

		$comments = get_comments([
			'parent' => $parent_id,
			'status' => 'approve',
			'number' => $per_page,
			'offset' => $offset,
			'orderby' => 'comment_date_gmt',
			'order' => 'ASC',
		]);

		$html = '';
		foreach ($comments as $c) {
			$html .= $this->render_comment_item($c);
		}

		$loaded = $offset + count($comments);

		wp_send_json_success([
			'html' => $html,
			'total' => (int) $total,
			'loaded' => (int) $loaded,
		]);
	}

	/** Verify Turnstile */
	protected function verify_turnstile(string $token): bool
	{
		$secret = get_option('op_turnstile_secret');
		if (!$secret) {
			return true; // không cấu hình thì coi như tắt
		}
		if ($token === '') {
			return false;
		}

		$response = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			[
				'body' => [
					'secret' => $secret,
					'response' => $token,
					'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
				],
				'timeout' => 10,
			]
		);

		if (is_wp_error($response)) {
			return false;
		}
		$body = json_decode(wp_remote_retrieve_body($response), true);
		return !empty($body['success']);
	}

	/** Helper: Xử lý dữ liệu comment chung cho cả AJAX và POST thường */
	protected function process_comment_data($data)
	{
		$post_id = isset($data['post_id']) ? absint($data['post_id']) : 0;
		$parent = isset($data['parent_id']) ? absint($data['parent_id']) : 0;
		$author = sanitize_text_field($data['comment_author'] ?? '');
		$email = sanitize_email($data['comment_author_email'] ?? '');
		$content = wp_kses_post($data['comment_content'] ?? '');
		$website = trim($data['website'] ?? '');
		$ts_token = sanitize_text_field($data['cf-turnstile-response'] ?? '');
		$avatar_id = sanitize_text_field($data['avatar_id'] ?? '');
		$is_pinned = isset($data['is_pinned']) && $data['is_pinned'] === '1';

		if ($website !== '') {
			return new WP_Error('spam', 'Spam detected.');
		}

		if (!$post_id || $author === '' || $content === '') {
			return new WP_Error('missing_data', 'Thiếu thông tin bắt buộc.');
		}

		// Skip turnstile for admins
		if (!current_user_can('manage_options') && !$this->verify_turnstile($ts_token)) {
			return new WP_Error('turnstile_fail', 'Xác minh bảo mật không hợp lệ.');
		}

		// Filter blocked words
		$content = $this->filter_blocked_words($content);

		$commentdata = [
			'comment_post_ID' => $post_id,
			'comment_parent' => $parent,
			'comment_author' => $author,
			'comment_author_email' => $email,
			'comment_content' => $content,
			'comment_approved' => 1,
		];

		$comment_id = wp_insert_comment($commentdata);
		if (!$comment_id) {
			return new WP_Error('insert_fail', 'Không thể lưu bình luận.');
		}

		if ($avatar_id) {
			add_comment_meta($comment_id, 'op_avatar_id', $avatar_id);
		}

		$is_spoil = isset($data['op_is_spoil']) && $data['op_is_spoil'] === '1';
		if ($is_spoil) {
			add_comment_meta($comment_id, 'op_is_spoil', 1);
		}

		if ($is_pinned && current_user_can('manage_options')) {
			add_comment_meta($comment_id, 'op_pinned', 1);
		}

		return $comment_id;
	}

	/** AJAX submit comment */
	public function ajax_submit_comment()
	{
		check_ajax_referer('op_sc_nonce', 'nonce');

		$result = $this->process_comment_data($_POST);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => $result->get_error_message()]);
		}

		$comment_id = $result;
		$comment = get_comment($comment_id);
		$html = $this->render_comment_item($comment);

		wp_send_json_success([
			'message' => 'Cảm ơn bạn! Bình luận đã được gửi.',
			'html' => $html,
		]);
	}

	/** Fallback submit không AJAX (admin-post.php) */
	public function handle_non_ajax_submit()
	{
		if (!isset($_POST['op_nonce']) || !wp_verify_nonce($_POST['op_nonce'], 'op_sc_submit_comment')) {
			wp_die('Invalid nonce');
		}

		$result = $this->process_comment_data($_POST);

		if (is_wp_error($result)) {
			wp_die($result->get_error_message());
		}

		$comment_id = $result;
		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$redirect = $post_id ? get_permalink($post_id) : home_url('/');

		wp_safe_redirect($redirect . '#comment-tab');
		exit;
	}
	public function add_admin_menu()
	{
		add_menu_page(
			'OP Comments',
			'OP Comments',
			'manage_options',
			'op-comments',
			[$this, 'settings_page_html'],
			'dashicons-format-chat',
			26
		);
		add_submenu_page(
			'op-comments',
			'Cấu hình',
			'Cấu hình',
			'manage_options',
			'op-comments',
			[$this, 'settings_page_html']
		);
		add_submenu_page(
			'op-comments',
			'Quản lý bình luận',
			'Quản lý bình luận',
			'manage_options',
			'op-manage-comments',
			[$this->dashboard, 'render']
		);
	}

	public function register_settings()
	{
		register_setting('op_simple_comments_group', 'op_turnstile_sitekey');
		register_setting('op_simple_comments_group', 'op_turnstile_secret');
		register_setting('op_simple_comments_group', 'op_turnstile_for_users');
		register_setting('op_simple_comments_group', 'op_blocked_words');
		register_setting('op_simple_comments_group', 'op_comments_enabled');
	}

	public function settings_page_html()
	{
		if (!current_user_can('manage_options')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields('op_simple_comments_group');
				do_settings_sections('op_simple_comments_group');
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Cloudflare Turnstile Sitekey</th>
						<td><input type="text" name="op_turnstile_sitekey"
								value="<?php echo esc_attr(get_option('op_turnstile_sitekey')); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Cloudflare Turnstile Secret</th>
						<td><input type="text" name="op_turnstile_secret"
								value="<?php echo esc_attr(get_option('op_turnstile_secret')); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Bật Turnstile cho người dùng</th>
						<td>
							<label>
								<input type="checkbox" name="op_turnstile_for_users" value="1" <?php checked(get_option('op_turnstile_for_users', '1'), '1'); ?> />
								Yêu cầu xác minh Turnstile cho người dùng thường (Admin luôn được miễn)
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Từ khóa bị chặn</th>
						<td>
							<textarea name="op_blocked_words" rows="5" class="large-text"
								placeholder="Nhập từng từ khóa trên mỗi dòng"><?php echo esc_textarea(get_option('op_blocked_words')); ?></textarea>
							<p class="description">Nhập mỗi từ khóa trên một dòng. Các từ này sẽ được thay thế bằng ***</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Bật/Tắt hệ thống bình luận</th>
						<td>
							<label>
								<input type="checkbox" name="op_comments_enabled" value="1" <?php checked(get_option('op_comments_enabled', '1'), '1'); ?> />
								Cho phép người dùng bình luận (Tắt để ẩn hoàn toàn form bình luận)
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/** Add meta box for comment toggle */
	public function add_comment_meta_box()
	{
		add_meta_box(
			'op_comment_toggle',
			'Bật/Tắt Bình Luận',
			[$this, 'render_comment_meta_box'],
			'ophim',
			'side',
			'high'
		);
	}

	/** Render meta box content */
	public function render_comment_meta_box($post)
	{
		wp_nonce_field('op_comment_toggle_nonce', 'op_comment_toggle_nonce');
		$enabled = get_post_meta($post->ID, '_op_comments_enabled', true);
		$enabled = $enabled === '' ? '1' : $enabled; // Default enabled
		?>
		<p>
			<label>
				<input type="checkbox" name="op_comments_enabled" value="1" <?php checked($enabled, '1'); ?> />
				<strong>Cho phép bình luận trên bài viết này</strong>
			</label>
		</p>
		<p class="description">
			Bỏ tick để tắt bình luận riêng cho bài viết này (không ảnh hưởng bài khác)
		</p>
		<?php
	}

	/** Save meta box data */
	public function save_comment_meta_box($post_id)
	{
		// Check nonce
		if (!isset($_POST['op_comment_toggle_nonce']) || !wp_verify_nonce($_POST['op_comment_toggle_nonce'], 'op_comment_toggle_nonce')) {
			return;
		}

		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// Check permissions
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		$enabled = isset($_POST['op_comments_enabled']) ? '1' : '0';
		update_post_meta($post_id, '_op_comments_enabled', $enabled);
	}
}

new OP_SignalTrue_Comments();
