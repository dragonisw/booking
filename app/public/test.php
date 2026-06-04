<?php
require_once('wp-load.php');

$args = array(
    'post_type' => 'room',
    's' => '',
    'posts_per_page' => -1,
);
$query = new WP_Query($args);

echo "Found posts: " . $query->post_count . "\n";
foreach ($query->posts as $post) {
    echo "- " . $post->post_title . "\n";
}

$check_in = '2026-05-05';
$check_out = '2026-06-06';
if ($query->post_count > 0) {
    $room_id = $query->posts[0]->ID;
    echo "\nTesting bookingroom_get_available_rooms for room ID $room_id:\n";
    $avail = bookingroom_get_available_rooms($room_id, $check_in, $check_out);
    echo "Available: $avail\n";
}
