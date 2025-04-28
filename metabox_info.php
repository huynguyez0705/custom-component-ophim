<?php

/**
 * Movie Information Meta Box
 * Handles movie info meta fields in Post Editor
 * Location: /flatsome-child/assets/inc/metabox_info.php
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Register meta fields
function movie_register_info_meta()
{
  $fields = [
    'movie_status',
    'movie_original_title',
    'movie_trailer_url',
    'movie_runtime',
    'movie_year',
    'movie_rating',
    'movie_episode',
    'movie_total_episode',
    'movie_quality',
    'movie_lang',
    'movie_notify'
  ];
  foreach ($fields as $field) {
    register_post_meta('post', $field, [
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string',
      'default' => '',
    ]);
  }
}
add_action('init', 'movie_register_info_meta');

// Add Movie Information meta box
function movie_add_info_meta_box()
{
  add_meta_box(
    'movie_info',
    __('Movie Information', 'flatsome'),
    'movie_render_info_meta_box',
    'post',
    'normal',
    'default'
  );
}
add_action('add_meta_boxes', 'movie_add_info_meta_box');

// Render Movie Information meta box
function movie_render_info_meta_box($post)
{
  $status = get_post_meta($post->ID, 'movie_status', true);
  $original_title = get_post_meta($post->ID, 'movie_original_title', true);
  $trailer_url = get_post_meta($post->ID, 'movie_trailer_url', true);
  $runtime = get_post_meta($post->ID, 'movie_runtime', true);
  $year = get_post_meta($post->ID, 'movie_year', true);
  $rating = get_post_meta($post->ID, 'movie_rating', true);
  $episode = get_post_meta($post->ID, 'movie_episode', true);
  $total_episode = get_post_meta($post->ID, 'movie_total_episode', true);
  $quality = get_post_meta($post->ID, 'movie_quality', true);
  $lang = get_post_meta($post->ID, 'movie_lang', true);
  $notify = get_post_meta($post->ID, 'movie_notify', true);
?>
  <style>
    #movie_info {
      width: 100%;
      margin-top: 20px;
    }

    .movie-info-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
    }

    .movie-info-table tr {
      border-bottom: 1px solid #e5e5e5;
    }

    .movie-info-table tr:first-child>td {
      border-top: 0 !important
    }

    .movie-info-table td {
      border-top: 1px solid #f0f0f0;
      padding: 15px 20px
    }

    .movie-info-table td.label {
      color: #333;
      width: 30%;
      vertical-align: middle;
      background: #fdfdfd;
      border-right: 1px solid #f0f0f0
    }

    .movie-info-table td.label label {
      font-weight: 400;
      display: block;
      text-align: right;
      font-size: 16px;
      color: #000
    }

    .movie-info-table td.field {
      border-top: 1px solid #f0f0f0;
      padding: 13px 15px;
      position: relative
    }


    .movie-info-table input[type="text"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 3px;
      box-sizing: border-box;
      font-size: 14px;
      height: 30px;
    }

    .movie-info-table input[type="radio"] {
      margin-right: 8px;
      vertical-align: middle;
    }

    #movie_notify {
      width: 100%;
      height: 120px;
    }
  </style>
  <table class="movie-info-table">
    <tbody>
      <!-- Trạng thái -->
      <tr>
        <td class="label"><label for="movie_status"><?php _e('Status', 'flatsome'); ?></label></td>
        <td class="field">
          <?php
          $statuses = [
            'trailer' => __('Coming Soon', 'flatsome'),
            'ongoing' => __('Ongoing', 'flatsome'),
            'completed' => __('Completed', 'flatsome'),
          ];
          foreach ($statuses as $value => $label) : ?>
            <label for="movie_status_<?php echo esc_attr($value); ?>">
              <input type="radio" name="movie_status" id="movie_status_<?php echo esc_attr($value); ?>" value="<?php echo esc_attr($value); ?>" <?php checked($value, $status); ?>>
              <?php echo esc_html($label); ?>
            </label>
          <?php endforeach; ?>
        </td>
      </tr>

      <!-- Tiêu đề gốc -->
      <tr>
        <td class="label"><label for="movie_original_title">Tiêu đề gốc</label></td>
        <td class="field">
          <input type="text" name="movie_original_title" id="movie_original_title" value="<?php echo esc_attr($original_title); ?>">
        </td>
      </tr>

      <!-- Đường dẫn trailer -->
      <tr>
        <td class="label"><label for="movie_trailer_url">Đường dẫn trailer</label></td>
        <td class="field">
          <input type="text" name="movie_trailer_url" id="movie_trailer_url" value="<?php echo esc_attr($trailer_url); ?>">
        </td>
      </tr>

      <!-- Thời lượng -->
      <tr>
        <td class="label"><label for="movie_runtime"><?php _e('Thời lượng', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_runtime" id="movie_runtime" value="<?php echo esc_attr($runtime); ?>">
        </td>
      </tr>

      <!-- Năm -->
      <tr>
        <td class="label"><label for="movie_year"><?php _e('Năm', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_year" id="movie_year" value="<?php echo esc_attr($year); ?>">
        </td>
      </tr>

      <!-- Đánh giá -->
      <tr>
        <td class="label"><label for="movie_rating"><?php _e('Đánh giá', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_rating" id="movie_rating" value="<?php echo esc_attr($rating); ?>">
        </td>
      </tr>

      <!-- Tập hiện tại -->
      <tr>
        <td class="label"><label for="movie_episode"><?php _e('Tập hiện tại', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_episode" id="movie_episode" value="<?php echo esc_attr($episode); ?>">
        </td>
      </tr>

      <!-- Tổng tập -->
      <tr>
        <td class="label"><label for="movie_total_episode"><?php _e('Tổng tập', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_total_episode" id="movie_total_episode" value="<?php echo esc_attr($total_episode); ?>">
        </td>
      </tr>

      <!-- Chất lượng -->
      <tr>
        <td class="label"><label for="movie_quality"><?php _e('Chất lượng', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_quality" id="movie_quality" value="<?php echo esc_attr($quality); ?>">
        </td>
      </tr>

      <!-- Ngôn ngữ -->
      <tr>
        <td class="label"><label for="movie_lang"><?php _e('Ngôn ngữ', 'flatsome'); ?></label></td>
        <td class="field">
          <input type="text" name="movie_lang" id="movie_lang" value="<?php echo esc_attr($lang); ?>">
        </td>
      </tr>

      <!-- Thông báo -->
      <tr>
        <td class="label"><label for="movie_notify"><?php _e('Notify', 'flatsome'); ?></label></td>
        <td class="field">
          <?php
          wp_editor($notify, 'movie_notify', [
            'textarea_name' => 'movie_notify',
            'textarea_rows' => 5,
            'media_buttons' => false,
            'tinymce' => true,
            'quicktags' => true,
          ]);
          ?>
        </td>
      </tr>
    </tbody>
  </table>
<?php
}

// Save meta fields
function movie_save_info_meta($post_id)
{
  $fields = [
    'movie_status',
    'movie_original_title',
    'movie_trailer_url',
    'movie_runtime',
    'movie_year',
    'movie_rating',
    'movie_episode',
    'movie_total_episode',
    'movie_quality',
    'movie_lang',
    'movie_notify'
  ];
  foreach ($fields as $field) {
    if (isset($_POST[$field])) {
      update_post_meta($post_id, $field, wp_kses_post($_POST[$field]));
    }
  }
}
add_action('save_post', 'movie_save_info_meta');
