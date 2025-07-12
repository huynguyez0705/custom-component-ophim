<?php
/*
Plugin Name: Ophim Category Manager Plugin
Description: Manages 'ophim' posts with meta_key 'ophim_lang' containing 'thuyết minh' or 'lồng tiếng' and meta_key 'ophim_featured_post' with value 1, allowing assignment of multiple categories.
Version:     1.6
Author:      Cô Cô
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class Ophim_Category_Manager_Plugin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_head', [ $this, 'add_admin_styles' ] );
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

    public function add_admin_styles() {
        ?>
        <style>
            .ophim-table-wrapper {
                max-height: 600px;
                overflow-y: auto;
                margin-top: 20px;
            }
            .ophim-table-wrapper table {
                width: 100%;
            }
        </style>
        <?php
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
            $type = isset( $_POST['otm_type'] ) ? sanitize_text_field( $_POST['otm_type'] ) : 'thuyet_minh';
            if ( empty( $selected ) ) {
                echo '<div class="notice notice-error"><p>Vui lòng chọn ít nhất một danh mục để gán.</p></div>';
            } else {
                $found_ids = $this->scan_posts( $type );
                if ( empty( $found_ids ) ) {
                    $type_label = $type === 'thuyet_minh' ? 'thuyết minh' : ( $type === 'long_tieng' ? 'lồng tiếng' : 'phim hot' );
                    echo '<div class="notice notice-warning"><p>Không có bài viết “' . esc_html( $type_label ) . '” để gán.</p></div>';
                } else {
                    foreach ( $found_ids as $post_id ) {
                        wp_set_post_terms( $post_id, $selected, 'ophim_categories', true );
                    }
                    $term_names = [];
                    foreach ( $selected as $term_id ) {
                        $term = get_term( $term_id, 'ophim_categories' );
                        if ( $term && ! is_wp_error( $term ) ) {
                            $term_names[] = $term->name;
                        }
                    }
                    $count = count( $found_ids );
                    $type_label = $type === 'thuyet_minh' ? 'thuyết minh' : ( $type === 'long_tieng' ? 'lồng tiếng' : 'phim hot' );
                    echo '<div class="notice notice-success"><p>Đã gán danh mục <strong>' 
                         . esc_html( implode( ', ', $term_names ) ) 
                         . '</strong> cho <strong>' . $count . '</strong> bài viết “' 
                         . esc_html( $type_label ) . '”.</p></div>';
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
            <h1>Quản lý Danh Mục cho Ophim</h1>

            <!-- Form Gán và Kiểm tra danh mục -->
            <form method="post" style="margin-bottom:20px;">
                <?php wp_nonce_field( 'otm_nonce_action', 'otm_nonce_field' ); ?>
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

                <h3>Loại bài viết:</h3>
                <label><input type="radio" name="otm_type" value="thuyet_minh" checked> Thuyết Minh</label>
                <label style="margin-left:20px;"><input type="radio" name="otm_type" value="long_tieng"> Lồng Tiếng</label>
                <label style="margin-left:20px;"><input type="radio" name="otm_type" value="hot"> Phim Hot</label>

                <div style="margin-top:20px;">
                    <input type="hidden" name="otm_action" value="assign">
                    <?php submit_button( 'Thêm vào danh mục', 'secondary', 'submit', false ); ?>
                    <input type="hidden" name="otm_action" value="check">
                    <?php submit_button( 'Kiểm tra bài viết', 'primary', 'submit_check', false ); ?>
                </div>
            </form>

            <!-- Kết quả kiểm tra -->
            <?php
            if ( isset( $_POST['otm_action'] ) 
                 && $_POST['otm_action'] === 'check' 
                 && check_admin_referer( 'otm_nonce_action', 'otm_nonce_field' ) 
            ) {
                $type = isset( $_POST['otm_type'] ) ? sanitize_text_field( $_POST['otm_type'] ) : 'thuyet_minh';
                $found_ids = $this->scan_posts( $type );
                if ( empty( $found_ids ) ) {
                    $type_label = $type === 'thuyet_minh' ? 'Thuyết Minh' : ( $type === 'long_tieng' ? 'Lồng Tiếng' : 'Phim Hot' );
                    echo '<div class="notice notice-warning"><p>Không tìm thấy bài viết “' . esc_html( $type_label ) . '”.</p></div>';
                } else {
                    $total = count( $found_ids );
                    $type_label = $type === 'thuyet_minh' ? 'Thuyết Minh' : ( $type === 'long_tieng' ? 'Lồng Tiếng' : 'Phim Hot' );
                    echo '<h2>Tổng số bài viết “' . esc_html( $type_label ) . '”: ' . $total . '</h2>';
                    echo '<div class="ophim-table-wrapper">';
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
                    echo '</div>';
                }
            }
            ?>
        </div>
        <?php
    }

    /**
     * Quét bài viết theo loại: 'thuyet_minh', 'long_tieng' hoặc 'hot'.
     *
     * @param string $type Loại quét: 'thuyet_minh', 'long_tieng' hoặc 'hot'
     * @return int[] Mảng các post ID
     */
    private function scan_posts( $type = 'thuyet_minh' ) {
        $ids = [];
        if ( $type === 'thuyet_minh' || $type === 'long_tieng' ) {
            $query_args = [
                'post_type'      => 'ophim',
                'posts_per_page' => -1,
                'meta_key'       => 'ophim_lang',
                'fields'         => 'ids',
            ];
            $query = new WP_Query( $query_args );
            if ( $query->have_posts() ) {
                $search_term = $type === 'thuyet_minh' ? 'thuyết minh' : 'lồng tiếng';
                foreach ( $query->posts as $post_id ) {
                    $lang  = get_post_meta( $post_id, 'ophim_lang', true );
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
            ];
            $query = new WP_Query( $query_args );
            if ( $query->have_posts() ) {
                $ids = $query->posts;
            }
            wp_reset_postdata();
        }
        return $ids;
    }
}

new Ophim_Category_Manager_Plugin();