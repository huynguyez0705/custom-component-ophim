<?php
/*
Plugin Name: Ophim Cate Manager
Description: Quản lý các bài ‘ophim’ có meta_key = ‘ophim_lang’ chứa “thuyết minh” và cho phép gán nhiều danh mục.
Version:     1.2
Author:      Your Name
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class Ophim_Thuyet_Minh_Plugin {
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
        if ( isset( $_POST['otm_action'] ) 
             && $_POST['otm_action'] === 'assign' 
             && check_admin_referer( 'otm_nonce_action', 'otm_nonce_field' ) 
        ) {
            $selected = isset( $_POST['otm_categories'] ) 
                        ? array_map( 'intval', (array) $_POST['otm_categories'] ) 
                        : [];
            if ( empty( $selected ) ) {
                echo '<div class="notice notice-error"><p>Vui lòng chọn ít nhất một danh mục để gán.</p></div>';
            } else {
                $found_ids = $this->scan_posts();
                if ( empty( $found_ids ) ) {
                    echo '<div class="notice notice-warning"><p>Không có bài viết “thuyết minh” để gán.</p></div>';
                } else {
                    foreach ( $found_ids as $post_id ) {
                        wp_set_post_terms( $post_id, $selected, 'ophim_categories', true );
                    }
                    // Lấy tên term đã gán để thông báo
                    $term_names = [];
                    foreach ( $selected as $term_id ) {
                        $term = get_term( $term_id, 'ophim_categories' );
                        if ( $term && ! is_wp_error( $term ) ) {
                            $term_names[] = $term->name;
                        }
                    }
                    $count = count( $found_ids );
                    echo '<div class="notice notice-success"><p>Đã gán danh mục <strong>' 
                         . esc_html( implode( ', ', $term_names ) ) 
                         . '</strong> cho <strong>' . $count . '</strong> bài viết.</p></div>';
                }
            }
        }

        // Lấy tất cả term để hiển thị trong multi-select
        $terms = get_terms([
            'taxonomy'   => 'ophim_categories',
            'hide_empty' => false,
        ]);
        ?>
        <div class="wrap">
            <h1>Quản lý Thuyết Minh cho Ophim</h1>

            <!-- Form Gán danh mục (đặt ở trên cùng) -->
            <form method="post" style="margin-bottom:20px;">
                <?php wp_nonce_field( 'otm_nonce_action', 'otm_nonce_field' ); ?>
                <input type="hidden" name="otm_action" value="assign">
                <label for="otm_categories"><strong>Chọn danh mục để gán:</strong></label><br>
                <select name="otm_categories[]" id="otm_categories" multiple
                        style="min-width:300px; height:150px;">
                    <?php foreach ( $terms as $term ): ?>
                        <option value="<?php echo esc_attr( $term->term_id ); ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Giữ Ctrl (Cmd) để chọn nhiều danh mục cùng lúc.</p>
                <?php submit_button( 'Thêm vào danh mục', 'secondary' ); ?>
            </form>

            <!-- Form Kiểm tra -->
            <form method="post" style="margin-bottom:20px;">
                <?php wp_nonce_field( 'otm_nonce_action', 'otm_nonce_field' ); ?>
                <input type="hidden" name="otm_action" value="check">
                <?php submit_button( 'Kiểm tra bài viết Thuyết Minh', 'primary' ); ?>
            </form>

        <?php
        // Nếu bấm Check, hiển thị bảng kết quả
        if ( isset( $_POST['otm_action'] ) 
             && $_POST['otm_action'] === 'check' 
             && check_admin_referer( 'otm_nonce_action', 'otm_nonce_field' ) 
        ) {
            $found_ids = $this->scan_posts();
            if ( empty( $found_ids ) ) {
                echo '<div class="notice notice-warning"><p>Không tìm thấy bài viết “thuyết minh”.</p></div>';
            } else {
                $total = count( $found_ids );
                echo '<h2>Tổng số bài viết có “thuyết minh”: ' . $total . '</h2>';
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr><th scope="col" style="width:80px;">ID</th><th scope="col">Tiêu đề</th></tr></thead>';
                echo '<tbody>';
                foreach ( $found_ids as $post_id ) {
                    $title     = get_the_title( $post_id );
                    $edit_link = get_edit_post_link( $post_id );
                    echo '<tr>';
                    echo '<td>' . esc_html( $post_id ) . '</td>';
                    echo '<td><a href="' . esc_url( $edit_link ) . '" target="_blank">'
                         . esc_html( $title ) . '</a></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
        }
        ?>
        </div>
        <?php
    }

    /**
     * Quét toàn bộ post_type=ophim có meta_key=ophim_lang,
     * tìm meta_value chứa “thuyết minh” (bỏ dấu +, không phân biệt hoa/thường).
     *
     * @return int[] Mảng các post ID
     */
    private function scan_posts() {
        $ids = [];
        $query = new WP_Query([
            'post_type'      => 'ophim',
            'posts_per_page' => -1,
            'meta_key'       => 'ophim_lang',
            'fields'         => 'ids',
        ]);
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $post_id ) {
                $lang  = get_post_meta( $post_id, 'ophim_lang', true );
                $clean = mb_strtolower( str_replace( '+', ' ', $lang ), 'UTF-8' );
                if ( strpos( $clean, 'thuyết minh' ) !== false ) {
                    $ids[] = $post_id;
                }
            }
        }
        wp_reset_postdata();
        return $ids;
    }
}

new Ophim_Thuyet_Minh_Plugin();
