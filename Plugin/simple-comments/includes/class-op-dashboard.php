<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OP_Comment_Dashboard {

    public function __construct() {
        // Constructor left empty as per design
    }

    /** Dashboard: Quản lý bình luận */
    public function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $this->handle_actions();

        $post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

        echo '<div class="wrap">';
        if ( $post_id ) {
            $this->render_comment_detail_view( $post_id );
        } else {
            $this->render_post_list_view();
        }
        echo '</div>';
    }

    /** Xử lý hành động (Approve/Trash/Pin/Create...) */
    protected function handle_actions() {
        // Handle comment creation
        if ( isset( $_POST['create_comment'], $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'op_create_comment' ) ) {
            $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
            $content = isset( $_POST['comment_content'] ) ? wp_kses_post( $_POST['comment_content'] ) : '';
            $is_pinned = isset( $_POST['is_pinned'] ) && $_POST['is_pinned'] === '1';

            if ( $post_id && $content ) {
                $current_user = wp_get_current_user();
                $commentdata = [
                    'comment_post_ID'      => $post_id,
                    'comment_author'       => $current_user->display_name,
                    'comment_author_email' => $current_user->user_email,
                    'comment_author_url'   => $current_user->user_url,
                    'comment_content'      => $content,
                    'comment_approved'     => 1,
                    'user_id'              => $current_user->ID,
                ];

                $comment_id = wp_insert_comment( $commentdata );
                if ( $comment_id && $is_pinned ) {
                    add_comment_meta( $comment_id, 'op_pinned', 1 );
                }

                if ( $comment_id ) {
                    echo '<div class="notice notice-success"><p>Bình luận đã được tạo thành công!</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>Không thể tạo bình luận.</p></div>';
                }
            }
        }

        // Handle other actions
        if ( ! isset( $_GET['action'], $_GET['comment_id'], $_GET['_wpnonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'op_action_' . $_GET['comment_id'] ) ) {
            echo '<div class="notice notice-error"><p>Invalid Nonce.</p></div>';
            return;
        }

        $comment_id = absint( $_GET['comment_id'] );
        $action     = sanitize_text_field( $_GET['action'] );

        switch ( $action ) {
            case 'approve':
                wp_set_comment_status( $comment_id, 'approve' );
                echo '<div class="notice notice-success"><p>Đã duyệt bình luận.</p></div>';
                break;
            case 'unapprove':
                wp_set_comment_status( $comment_id, 'hold' );
                echo '<div class="notice notice-success"><p>Đã bỏ duyệt bình luận.</p></div>';
                break;
            case 'trash':
                wp_trash_comment( $comment_id );
                echo '<div class="notice notice-success"><p>Đã xóa bình luận.</p></div>';
                break;
            case 'pin':
                update_comment_meta( $comment_id, 'op_pinned', 1 );
                echo '<div class="notice notice-success"><p>Đã ghim bình luận.</p></div>';
                break;
            case 'unpin':
                delete_comment_meta( $comment_id, 'op_pinned' );
                echo '<div class="notice notice-success"><p>Đã bỏ ghim bình luận.</p></div>';
                break;
        }
    }

    /** View 1: Danh sách bài viết có bình luận */
    protected function render_post_list_view() {
        echo '<h1 class="wp-heading-inline">Quản lý bình luận theo bài viết</h1>';
        
        // Lấy tất cả bài viết có bình luận
        global $wpdb;
        $posts = $wpdb->get_results( "
            SELECT ID, post_title, comment_count 
            FROM $wpdb->posts 
            WHERE comment_count > 0 
            AND post_status = 'publish' 
            ORDER BY comment_count DESC
        " );

        if ( empty( $posts ) ) {
            echo '<p>Chưa có bài viết nào có bình luận.</p>';
            return;
        }

        // Lấy danh sách pending comments count cho tất cả bài viết trong 1 query
        $pending_counts = $wpdb->get_results( "
            SELECT comment_post_ID, COUNT(*) as count
            FROM $wpdb->comments
            WHERE comment_approved = '0'
            GROUP BY comment_post_ID
        ", OBJECT_K );

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>
                <th>Bài viết</th>
                <th style="width:100px">Tổng</th>
                <th style="width:100px">Chờ duyệt</th>
                <th style="width:150px">Hành động</th>
              </tr></thead>';
        echo '<tbody>';

        foreach ( $posts as $p ) {
            // Lấy pending từ kết quả đã query
            $pending = isset( $pending_counts[ $p->ID ] ) ? $pending_counts[ $p->ID ]->count : 0;

            $manage_url = admin_url( 'admin.php?page=op-manage-comments&post_id=' . $p->ID );
            $view_url   = get_permalink( $p->ID );

            echo '<tr>';
            echo '<td><strong><a href="' . esc_url( $manage_url ) . '">' . esc_html( $p->post_title ) . '</a></strong></td>';
            echo '<td>' . number_format_i18n( $p->comment_count ) . '</td>';
            echo '<td style="' . ( $pending > 0 ? 'color:orange;font-weight:bold;' : '' ) . '">' . number_format_i18n( $pending ) . '</td>';
            echo '<td>
                    <a href="' . esc_url( $manage_url ) . '" class="button">Xem bình luận</a>
                    <a href="' . esc_url( $view_url ) . '" class="button" target="_blank">Xem bài</a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    /** View 2: Chi tiết bình luận của 1 bài */
    protected function render_comment_detail_view( int $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            echo '<p>Bài viết không tồn tại.</p>';
            return;
        }

        $back_url = admin_url( 'admin.php?page=op-manage-comments' );
        echo '<h1>Bình luận: ' . esc_html( $post->post_title ) . ' <a href="' . esc_url( $back_url ) . '" class="page-title-action">Quay lại</a></h1>';

        // Add comment creation form
        ?>
        <div class="card" style="max-width: 800px; margin-bottom: 20px;">
            <h2>Tạo bình luận mới (Admin)</h2>
            <form method="post" action="">
                <?php wp_nonce_field( 'op_create_comment', '_wpnonce' ); ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="comment_content">Nội dung bình luận</label></th>
                        <td>
                            <textarea name="comment_content" id="comment_content" rows="5" class="large-text" required></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Ghim bình luận</th>
                        <td>
                            <label>
                                <input type="checkbox" name="is_pinned" value="1">
                                Ghim bình luận này lên đầu
                            </label>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="create_comment" class="button button-primary" value="Tạo bình luận">
                </p>
            </form>
        </div>
        <?php

        $per_page = 20;
        $page     = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
        $offset   = ( $page - 1 ) * $per_page;

        $comments = get_comments( [
            'post_id' => $post_id,
            'status'  => 'all',
            'order'   => 'DESC',
            'number'  => $per_page,
            'offset'  => $offset,
        ] );

        $total_comments = get_comments( [
            'post_id' => $post_id,
            'status'  => 'all',
            'count'   => true,
        ] );

        $total_pages = ceil( $total_comments / $per_page );

        if ( empty( $comments ) ) {
            echo '<p>Không có bình luận nào.</p>';
            return;
        }

        // Pagination links
        if ( $total_pages > 1 ) {
            $page_links = paginate_links( [
                'base'    => add_query_arg( 'paged', '%#%' ),
                'format'  => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total'   => $total_pages,
                'current' => $page,
            ] );
            echo '<div class="tablenav top"><div class="tablenav-pages">' . $page_links . '</div></div>';
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>
                <th style="width:150px">Tác giả</th>
                <th>Nội dung</th>
                <th style="width:150px">Thời gian</th>
                <th style="width:100px">Trạng thái</th>
                <th style="width:200px">Hành động</th>
              </tr></thead>';
        echo '<tbody>';

        foreach ( $comments as $c ) {
            $approve_url = wp_nonce_url( admin_url( 'admin.php?page=op-manage-comments&post_id=' . $post_id . '&action=approve&comment_id=' . $c->comment_ID ), 'op_action_' . $c->comment_ID );
            $unapprove_url = wp_nonce_url( admin_url( 'admin.php?page=op-manage-comments&post_id=' . $post_id . '&action=unapprove&comment_id=' . $c->comment_ID ), 'op_action_' . $c->comment_ID );
            $trash_url = wp_nonce_url( admin_url( 'admin.php?page=op-manage-comments&post_id=' . $post_id . '&action=trash&comment_id=' . $c->comment_ID ), 'op_action_' . $c->comment_ID );
            $pin_url = wp_nonce_url( admin_url( 'admin.php?page=op-manage-comments&post_id=' . $post_id . '&action=pin&comment_id=' . $c->comment_ID ), 'op_action_' . $c->comment_ID );
            $unpin_url = wp_nonce_url( admin_url( 'admin.php?page=op-manage-comments&post_id=' . $post_id . '&action=unpin&comment_id=' . $c->comment_ID ), 'op_action_' . $c->comment_ID );

            $is_pinned = get_comment_meta( $c->comment_ID, 'op_pinned', true );

            $status_label = 'Đã duyệt';
            $row_class = '';
            if ( $c->comment_approved == '0' ) {
                $status_label = '<span style="color:orange;font-weight:bold;">Chờ duyệt</span>';
                $row_class = ' style="background:#fff8e5;"';
            } elseif ( $c->comment_approved == 'spam' ) {
                $status_label = 'Spam';
            } elseif ( $c->comment_approved == 'trash' ) {
                $status_label = 'Thùng rác';
            }

            if ( $is_pinned ) {
                $status_label .= ' <span style="color:green;">📌 Pinned</span>';
            }

            echo '<tr' . $row_class . '>';
            echo '<td>
                    <strong>' . esc_html( $c->comment_author ) . '</strong><br>
                    <small>' . esc_html( $c->comment_author_email ) . '</small>
                  </td>';
            echo '<td>' . wp_kses_post( $c->comment_content ) . '</td>';
            echo '<td>' . get_comment_date( 'd/m/Y H:i', $c ) . '</td>';
            echo '<td>' . $status_label . '</td>';
            echo '<td>';
            if ( $c->comment_approved == '0' ) {
                echo '<a href="' . esc_url( $approve_url ) . '" class="button button-primary">Duyệt</a> ';
            } else {
                echo '<a href="' . esc_url( $unapprove_url ) . '" class="button">Bỏ duyệt</a> ';
            }
            
            if ( $is_pinned ) {
                echo '<a href="' . esc_url( $unpin_url ) . '" class="button">Bỏ ghim</a> ';
            } else {
                echo '<a href="' . esc_url( $pin_url ) . '" class="button">Ghim</a> ';
            }
            
            echo '<a href="' . esc_url( $trash_url ) . '" class="button button-link-delete" onclick="return confirm(\'Bạn chắc chắn muốn xóa?\')">Xóa</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        if ( $total_pages > 1 && isset( $page_links ) ) {
            echo '<div class="tablenav bottom"><div class="tablenav-pages">' . $page_links . '</div></div>';
        }
    }
}
