<?php
/**
 * Temporary debug/cache-clear script
 * Truy cập: http://localhost/wp-content/themes/bookingroom/clear-cache.php
 * XÓA file này sau khi dùng xong!
 */

// Bootstrap WordPress
$wp_path = dirname(dirname(dirname(dirname(__FILE__))));
define('ABSPATH', $wp_path . '/');
define('WPINC', 'wp-includes');

// Load wp-load.php
require_once($wp_path . '/wp-load.php');

echo '<pre>';
echo "=== WordPress Theme Templates ===\n";

// Xóa theme transients
$deleted = 0;
global $wpdb;
$rows = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_theme_%' OR option_name LIKE '_transient_timeout_theme_%'");
echo "Cleared $rows theme transients\n\n";

// Hiện tại theme nào đang active
$theme = wp_get_theme();
echo "Active theme: " . $theme->get('Name') . "\n";
echo "Theme directory: " . get_stylesheet_directory() . "\n";
echo "Parent template: " . $theme->get('Template') . "\n\n";

// Danh sách templates được đăng ký
echo "=== Page Templates ===\n";
$templates = get_page_templates();
if (empty($templates)) {
    echo "KHÔNG CÓ template nào được đăng ký!\n";
    echo "=> Kiểm tra theme header trong style.css\n";
} else {
    foreach ($templates as $name => $file) {
        echo "- $name => $file\n";
    }
}

echo "\n=== Theme style.css header (10 dòng đầu) ===\n";
$css = file(get_stylesheet_directory() . '/style.css');
for ($i = 0; $i < min(15, count($css)); $i++) {
    echo htmlspecialchars($css[$i]);
}

echo "\n=== Trang có template page-destination.php không? ===\n";
$pages = get_pages(array(
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-destination.php',
));
if ($pages) {
    foreach ($pages as $p) {
        echo "Tìm thấy: [{$p->ID}] {$p->post_title} - URL: " . get_permalink($p->ID) . "\n";
    }
} else {
    echo "Không có trang nào đang dùng template page-destination.php\n";
    echo "=> Vào WordPress Admin > Pages > chọn trang Destination > đổi Template sang 'destinations'\n";
}

echo '</pre>';
echo '<p style="color:red;font-weight:bold;">XÓA FILE NÀY SAU KHI DÙNG! (/wp-content/themes/bookingroom/clear-cache.php)</p>';
