<?php

// Giả lập hàm get_post_meta() để test mà không cần WordPress
function get_post_meta($post_id, $key, $single) {
    $fake_data = [
        123 => ['rating' => 7.8, 'votes' => 45],  // Votes < 50 → Random rating
        124 => ['rating' => 8.5, 'votes' => 60],  // Votes > 50 → Dùng rating thật
        125 => ['rating' => 9.3, 'votes' => 120], // Votes > 50 → Dùng rating thật
        126 => ['rating' => null, 'votes' => 30], // Không có rating, votes < 50 → Random rating
    ];
    
    return isset($fake_data[$post_id][$key]) ? $fake_data[$post_id][$key] : 0;
}

// Giả lập get_the_ID() để test
function get_the_ID() {
    global $current_post_id;
    return $current_post_id;
}

// Hàm lấy rating
function op_get_rating()
{
    $count = get_post_meta(get_the_ID(), 'rating', true);
    return ($count && round($count, 1) > 8) ? round($count, 1) : round(mt_rand(80, 100) / 10, 1);
}

// Hàm lấy số votes + tự động tạo random rating nếu votes < 50
function op_get_rating_count()
{
    $count = get_post_meta(get_the_ID(), 'votes', true);
    
    // Nếu votes > 50 → dùng rating thật, ngược lại → random từ 5.0 đến 10.0
    return ($count > 50) ? op_get_rating() : round(mt_rand(50, 100) / 10, 1);
}

// Test nhiều trường hợp khác nhau
$test_cases = [123, 124, 125, 126];

foreach ($test_cases as $post_id) {
    $current_post_id = $post_id; // Giả lập get_the_ID()
    echo "Post ID: $post_id → Votes: " . get_post_meta($post_id, 'votes', true) . " → Final Rating: " . op_get_rating_count() . "\n";
}