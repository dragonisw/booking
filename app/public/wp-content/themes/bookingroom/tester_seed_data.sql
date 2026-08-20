-- ==============================================================================
-- TEST DATA SEEDING SCRIPT FOR STAGING / TESTING ENVIRONMENT
-- Giúp Tester nhanh chóng khởi tạo dữ liệu mẫu (Phòng, Loại phòng, Booking mẫu)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tạo dữ liệu phòng kiểm thử mẫu (Test Rooms)
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_name`, `post_type`)
VALUES
(9001, 1, NOW(), NOW(), 'Phòng Deluxe Suite view biển tuyệt đẹp cho 2 người lớn.', 'Phòng Deluxe Suite View Biển', 'Phòng Deluxe Suite 35m2', 'publish', 'closed', 'closed', 'phong-deluxe-suite', 'room'),
(9002, 1, NOW(), NOW(), 'Phòng Executive King sang trọng hiện đại.', 'Phòng Executive King Bed', 'Phòng Executive 45m2', 'publish', 'closed', 'closed', 'phong-executive-king', 'room'),
(9003, 1, NOW(), NOW(), 'Phòng Family Connecting dành cho gia đình 4 người.', 'Phòng Family Suite Gia Đình', 'Phòng Family 60m2', 'publish', 'closed', 'closed', 'phong-family-suite', 'room')
ON DUPLICATE KEY UPDATE `post_title` = VALUES(`post_title`);

-- 2. Tạo Meta Data cho các Phòng mẫu
INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
-- Room 9001
(9001, '_price', '1500000'),
(9001, '_weekend_price', '1800000'),
(9001, '_capacity', '2'),
(9001, '_total_units', '5'),

-- Room 9002
(9002, '_price', '2200000'),
(9002, '_weekend_price', '2600000'),
(9002, '_capacity', '2'),
(9002, '_total_units', '3'),

-- Room 9003
(9003, '_price', '3500000'),
(9003, '_weekend_price', '4000000'),
(9003, '_capacity', '4'),
(9003, '_total_units', '2')
ON DUPLICATE KEY UPDATE `meta_value` = VALUES(`meta_value`);

-- 3. Tạo Booking mẫu để Tester kiểm thử giao thoa thời gian (Overbooking Test)
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_status`, `post_name`, `post_type`)
VALUES
(9101, 1, NOW(), NOW(), 'Đơn đặt phòng test 1', 'Booking #9101 - Nguyễn Văn A', 'publish', 'booking-9101', 'booking'),
(9102, 1, NOW(), NOW(), 'Đơn đặt phòng test 2', 'Booking #9102 - Trần Thị B', 'publish', 'booking-9102', 'booking')
ON DUPLICATE KEY UPDATE `post_title` = VALUES(`post_title`);

-- Meta data cho Booking 9101 (2026-08-20 đến 2026-08-22)
INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(9101, '_room_id', '9001'),
(9101, '_check_in', '2026-08-20'),
(9101, '_check_out', '2026-08-22'),
(9101, '_customer_name', 'Nguyễn Văn A'),
(9101, '_customer_phone', '0901234567'),
(9101, '_customer_email', 'nguyenvana@gmail.com'),
(9101, '_adults', '2'),
(9101, '_children', '0'),
(9101, '_total_price', '3000000'),

-- Meta data cho Booking 9102 (2026-08-21 đến 2026-08-23) -> Trùng 1 đêm ngày 21/08 với 9101
(9102, '_room_id', '9001'),
(9102, '_check_in', '2026-08-21'),
(9102, '_check_out', '2026-08-23'),
(9102, '_customer_name', 'Trần Thị B'),
(9102, '_customer_phone', '0987654321'),
(9102, '_customer_email', 'tranthib@gmail.com'),
(9102, '_adults', '2'),
(9102, '_children', '1'),
(9102, '_total_price', '3300000')
ON DUPLICATE KEY UPDATE `meta_value` = VALUES(`meta_value`);

SET FOREIGN_KEY_CHECKS = 1;
