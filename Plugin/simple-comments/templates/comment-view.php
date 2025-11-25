<?php
/**
 * Template Name: Comment View by Date
 * Description: View comments filtered by day, month, year
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Get date parameters
$day = isset( $_GET['day'] ) ? absint( $_GET['day'] ) : 0;
$month = isset( $_GET['month'] ) ? absint( $_GET['month'] ) : 0;
$year = isset( $_GET['year'] ) ? absint( $_GET['year'] ) : date( 'Y' );

// Build date query
$date_query = [];
if ( $year ) {
    $date_query['year'] = $year;
}
if ( $month ) {
    $date_query['month'] = $month;
}
if ( $day ) {
    $date_query['day'] = $day;
}

// Get comments
$args = [
    'status' => 'approve',
    'orderby' => 'comment_date_gmt',
    'order' => 'DESC',
];

if ( ! empty( $date_query ) ) {
    $args['date_query'] = [ $date_query ];
}

$comments = get_comments( $args );
$total_comments = count( $comments );

// Build title
$title = 'Tất cả bình luận';
if ( $day && $month && $year ) {
    $title = sprintf( 'Bình luận ngày %02d/%02d/%d', $day, $month, $year );
} elseif ( $month && $year ) {
    $title = sprintf( 'Bình luận tháng %02d/%d', $month, $year );
} elseif ( $year ) {
    $title = sprintf( 'Bình luận năm %d', $year );
}
?>

<div class="op-comment-view-wrapper" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <div class="op-comment-view-header" style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;"><?php echo esc_html( $title ); ?></h1>
        <p style="color: #666; font-size: 16px;">Tổng số: <?php echo number_format_i18n( $total_comments ); ?> bình luận</p>
        
        <!-- Date Filter Form -->
        <form method="get" style="margin-top: 20px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Ngày:</label>
                    <input type="number" name="day" min="1" max="31" value="<?php echo $day ? $day : ''; ?>" placeholder="DD" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Tháng:</label>
                    <input type="number" name="month" min="1" max="12" value="<?php echo $month ? $month : ''; ?>" placeholder="MM" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Năm:</label>
                    <input type="number" name="year" min="2020" max="2030" value="<?php echo $year; ?>" placeholder="YYYY" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
            <button type="submit" style="padding: 10px 24px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Lọc bình luận</button>
            <a href="<?php echo esc_url( remove_query_arg( ['day', 'month', 'year'] ) ); ?>" style="margin-left: 10px; padding: 10px 24px; background: #666; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">Xóa bộ lọc</a>
        </form>
    </div>

    <div class="op-comment-list" style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php if ( $comments ) : ?>
            <?php foreach ( $comments as $comment ) : 
                $post = get_post( $comment->comment_post_ID );
                $is_admin = user_can( $comment->user_id, 'manage_options' );
                $is_pinned = get_comment_meta( $comment->comment_ID, 'op_pinned', true );
            ?>
            <div class="op-comment-item" style="padding: 20px; border-bottom: 1px solid #eee;">
                <div style="display: flex; gap: 15px;">
                    <div class="op-comment-avatar" style="flex-shrink: 0;">
                        <?php 
                        $avatar_id = get_comment_meta( $comment->comment_ID, 'op_avatar_id', true );
                        $admin_avatar = get_site_icon_url( 48 );
                        
                        if ( $is_admin && $admin_avatar ) {
                            echo '<img src="' . esc_url( $admin_avatar ) . '" alt="Admin" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #ffc107;">';
                        } elseif ( $avatar_id ) {
                            $avatar_url = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . esc_attr( $avatar_id );
                            echo '<img src="' . esc_url( $avatar_url ) . '" alt="Avatar" style="width: 48px; height: 48px; border-radius: 50%;">';
                        } else {
                            echo get_avatar( $comment, 48, '', '', ['class' => 'op-avatar'] );
                        }
                        ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="margin-bottom: 8px;">
                            <strong style="font-size: 16px; <?php echo $is_admin ? 'color: #ffc107;' : ''; ?>">
                                <?php echo esc_html( $comment->comment_author ); ?>
                            </strong>
                            <?php if ( $is_admin ) : ?>
                                <span style="background: linear-gradient(90deg, #fbbf24, #f59e0b); color: #111; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; margin-left: 8px;">Admin</span>
                            <?php endif; ?>
                            <?php if ( $is_pinned ) : ?>
                                <span style="margin-left: 8px; font-size: 14px;" title="Pinned">📌</span>
                            <?php endif; ?>
                            <span style="color: #999; font-size: 13px; margin-left: 10px;">
                                <?php echo get_comment_date( 'd/m/Y H:i', $comment ); ?>
                            </span>
                        </div>
                        <div style="margin-bottom: 10px; color: #333; line-height: 1.6;">
                            <?php echo wpautop( $comment->comment_content ); ?>
                        </div>
                        <div style="font-size: 13px; color: #666;">
                            Trên bài: <a href="<?php echo get_permalink( $post ); ?>#comment-<?php echo $comment->comment_ID; ?>" style="color: #0073aa; text-decoration: none;">
                                <?php echo esc_html( $post->post_title ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div style="padding: 40px; text-align: center; color: #999;">
                <p style="font-size: 18px;">Không có bình luận nào trong khoảng thời gian này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
