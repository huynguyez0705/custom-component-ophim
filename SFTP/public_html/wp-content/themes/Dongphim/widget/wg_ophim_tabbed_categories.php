<?php
class WG_oPhim_Tabbed_Categories extends WP_Widget {

    function __construct() {
        parent::__construct(
            'wg_tabbed_homepage',
            __('Ophim Tabbed Homepage', 'ophim'),
            array('description' => __('Danh sách phim với tab', 'ophim'))
        );
    }

    public function widget($args, $instance) {
        global $post;
        extract($args);
        $title = $instance['title'];
        $slug = $instance['slug'];
        $postnum = $instance['postnum'];
        $orderby = $instance['orderby'];
        $status = $instance['status'];
        $featured = $instance['featured'];

        echo $before_widget;
        ob_start();

        // Define tab categories
        $tabs = [
            'phim-le-moi' => ['label' => 'Phim lẻ mới', 'category' => 'phim-le'],
            'phim-bo-moi' => ['label' => 'Phim bộ mới', 'category' => 'phim-bo'],
            'phim-bo-full' => ['label' => 'Phim bộ full', 'category' => 'phim-bo', 'status' => 'completed']
        ];

        ?>
        <div class="tabbed-content">
            <ul class="nav nav-tabs nav-uppercase nav-size-normal nav-left" role="tablist">
                <?php $first = true; foreach ($tabs as $tab_id => $tab): ?>
                    <li id="tab-<?= $tab_id ?>" class="tab has-icon <?= $first ? 'active' : '' ?>" role="presentation">
                        <a href="#tab_<?= $tab_id ?>" role="tab" aria-selected="<?= $first ? 'true' : 'false' ?>" aria-controls="tab_<?= $tab_id ?>" tabindex="<?= $first ? '0' : '-1' ?>">
                            <span><?= $tab['label'] ?></span>
                        </a>
                    </li>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </ul>
            <div class="tab-panels">
                <?php $first = true; foreach ($tabs as $tab_id => $tab): ?>
                    <div id="tab_<?= $tab_id ?>" class="panel entry-content <?= $first ? 'active' : '' ?>" role="tabpanel" aria-labelledby="tab-<?= $tab_id ?>">
                        <div class="movies-tab">
                            <?php
                            $query_args = [
                                'post_type' => 'ophim',
                                'post_status' => 'publish',
                                'posts_per_page' => $postnum,
                            ];

                            if ($tab['category'] != 'all') {
                                $query_args['tax_query'][] = [
                                    'taxonomy' => 'ophim_categories',
                                    'field' => 'slug',
                                    'terms' => $tab['category'],
                                ];
                            }

                            if (isset($tab['status']) && $tab['status'] != 'all') {
                                $query_args['meta_query'][] = [
                                    'key' => 'ophim_movie_status',
                                    'value' => $tab['status']
                                ];
                            }

                            if ($status != 'all') {
                                $query_args['meta_query'][] = [
                                    'key' => 'ophim_movie_status',
                                    'value' => $status
                                ];
                            }

                            if ($featured == 'on') {
                                $query_args['meta_query'][] = [
                                    'key' => 'ophim_featured_post',
                                    'value' => '1'
                                ];
                            }

                            if ($orderby == 'update') {
                                $query_args['orderby'] = 'modified';
                            } elseif ($orderby == 'new') {
                                $query_args['orderby'] = 'publish_date';
                                $query_args['order'] = 'DESC';
                            } elseif ($orderby == 'view') {
                                $query_args['meta_key'] = 'ophim_view';
                                $query_args['orderby'] = 'meta_value_num';
                                $query_args['order'] = 'DESC';
                            } elseif ($orderby == 'rate') {
                                $query_args['meta_key'] = 'ophim_rating';
                                $query_args['orderby'] = 'meta_value_num';
                                $query_args['order'] = 'DESC';
                            } elseif ($orderby == 'rand') {
                                $query_args['orderby'] = 'rand';
                            }

                            $query = new WP_Query($query_args);
                            include THEME_URL . '/templates/section/tabbed_section_thumb.php';
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        echo $after_widget;
        $html = ob_get_contents();
        ob_end_clean();
        echo $html;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, [
            'title' => '',
            'slug' => '#',
            'postnum' => 8,
            'status' => 'all',
            'orderby' => 'new',
            'featured' => ''
        ]);
        extract($instance);
        ?>
        <p>
            <label for="<?= $this->get_field_id('title'); ?>"><?php _e('Tiêu đề', 'ophim') ?></label>
            <input class="widefat" type="text" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" value="<?= $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?= $this->get_field_id('slug'); ?>"><?php _e('Đường dẫn tĩnh', 'ophim') ?></label>
            <input class="widefat" type="text" id="<?= $this->get_field_id('slug'); ?>" name="<?= $this->get_field_name('slug'); ?>" value="<?= $instance['slug']; ?>" />
        </p>
        <p>
            <label><?php _e('Nổi bật', 'ophim') ?></label>
            <input type="checkbox" name="<?= $this->get_field_name('featured'); ?>" <?= $featured == 'on' ? 'checked' : '' ?> />
        </p>
        <p>
            <label><?php _e('Trạng thái', 'ophim') ?></label><br>
            <?php
            $statuses = ['all' => __('Tất cả', 'ophim'), 'trailer' => __('Sắp chiếu', 'ophim'), 'ongoing' => __('Đang chiếu', 'ophim'), 'completed' => __('Hoàn thành', 'ophim')];
            foreach ($statuses as $x => $n) { ?>
                <label for="<?= $this->get_field_id('status'); ?>_<?= $x ?>">
                    <input id="<?= $this->get_field_id('status'); ?>_<?= $x ?>" name="<?= $this->get_field_name('status'); ?>" type="radio" value="<?= $x ?>" <?= checked($x, $status, false) ?> /> <?= $n ?>
                </label>
            <?php } ?>
        </p>
        <p>
            <label><?php _e('Sắp xếp', 'ophim') ?></label><br>
            <?php
            $orders = ['new' => __('Mới tạo', 'ophim'), 'update' => __('Mới cập nhật', 'ophim'), 'view' => __('Lượt xem', 'ophim'), 'rate' => __('Đánh giá', 'ophim'), 'rand' => __('Random', 'ophim')];
            foreach ($orders as $x => $n) { ?>
                <label for="<?= $this->get_field_id('orderby'); ?>_<?= $x ?>">
                    <input id="<?= $this->get_field_id('orderby'); ?>_<?= $x ?>" name="<?= $this->get_field_name('orderby'); ?>" type="radio" value="<?= $x ?>" <?= checked($x, $orderby, false) ?> /> <?= $n ?>
                </label>
            <?php } ?>
        </p>
        <p>
            <label for="<?= $this->get_field_id('postnum'); ?>"><?php _e('Số bài đăng hiển thị', 'ophim') ?></label>
            <input type="number" class="widefat" style="width: 60px;" id="<?= $this->get_field_id('postnum'); ?>" name="<?= $this->get_field_name('postnum'); ?>" value="<?= $instance['postnum']; ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['slug'] = !empty($new_instance['slug']) ? strip_tags($new_instance['slug']) : '';
        $instance['postnum'] = $new_instance['postnum'];
        $instance['featured'] = $new_instance['featured'] ?? null;
        $instance['status'] = $new_instance['status'];
        $instance['orderby'] = $new_instance['orderby'];
        return $instance;
    }
}
function register_new_widget_tabbed_categories() {
    register_widget('WG_oPhim_Tabbed_Categories');
}
add_action('widgets_init', 'register_new_widget_tabbed_categories');
?>