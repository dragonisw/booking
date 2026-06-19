<?php
// Simple patch script to globally replace get_post_meta with bookingroom_get_meta_lang
$files = [
    __DIR__ . '/page-trang-chu.php',
    __DIR__ . '/page-about.php',
    __DIR__ . '/single-room.php',
    __DIR__ . '/archive-room.php',
    __DIR__ . '/single-destination.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file<br>";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // We only replace get_post_meta calls that have 3 arguments and the third is 'true'.
    // Actually, simply str_replace 'get_post_meta(get_the_ID(),' is very safe.
    
    $content = str_replace(
        ['get_post_meta(get_the_ID()', 'get_post_meta( get_the_ID()'],
        'bookingroom_get_meta_lang(get_the_ID()',
        $content
    );

    $content = str_replace(
        ['get_post_meta($pid', 'get_post_meta( $pid'],
        'bookingroom_get_meta_lang($pid',
        $content
    );

    $content = str_replace(
        ['get_post_meta($dest_id', 'get_post_meta( $dest_id'],
        'bookingroom_get_meta_lang($dest_id',
        $content
    );
    
    $content = str_replace(
        ['get_post_meta($hotel->ID', 'get_post_meta( $hotel->ID'],
        'bookingroom_get_meta_lang($hotel->ID',
        $content
    );

    $content = str_replace(
        ['get_post_meta($rel->ID', 'get_post_meta( $rel->ID'],
        'bookingroom_get_meta_lang($rel->ID',
        $content
    );

    file_put_contents($file, $content);
    echo "Updated $file<br>";
}
echo "Done.";
