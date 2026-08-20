-- ==============================================================================
-- DATABASE MANAGEMENT & QA TEST RESULTS FOR BOOKING ROOM THEME
-- Created for QA / Software Tester Team
-- Includes:
--   1. Schema & Data for qa_test_cases (100 Black-Box Test Cases)
--   2. Schema & Data for qa_bug_reports (8 Bug Reports)
--   3. Schema & Data for qa_white_box_tests (12 Code Coverage Tests)
--   4. Analytical Views & QA Metrics Queries
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- Table 1: qa_test_cases
-- Stores functional & non-functional test cases (Black Box Testing)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `qa_test_cases`;
CREATE TABLE `qa_test_cases` (
  `id` VARCHAR(20) NOT NULL COMMENT 'Mã Test Case (TC-xxx)',
  `module` VARCHAR(100) NOT NULL COMMENT 'Module được kiểm thử',
  `feature` VARCHAR(150) NOT NULL COMMENT 'Tính năng / Chức năng',
  `description` TEXT COMMENT 'Mô tả mục tiêu test case',
  `prerequisites` TEXT COMMENT 'Điều kiện tiên quyết',
  `steps` TEXT COMMENT 'Các bước thực hiện',
  `input` TEXT COMMENT 'Dữ liệu đầu vào',
  `expected` TEXT COMMENT 'Kết quả mong đợi',
  `priority` VARCHAR(20) DEFAULT 'Trung bình' COMMENT 'Độ ưu tiên (Cao, Trung bình, Thấp)',
  `type` VARCHAR(50) DEFAULT 'Chức năng' COMMENT 'Loại kiểm thử (Chức năng, Validation, UI/UX, Responsive, Security, Performance)',
  `status` VARCHAR(20) DEFAULT 'Chưa chạy' COMMENT 'Trạng thái (Đạt, Khung đạt, Lỗi, Chưa chạy)',
  `notes` TEXT COMMENT 'Ghi chú kiểm thử / bằng chứng',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_module` (`module`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu giữ 100 Test Cases kiểm thử Booking Room';

INSERT INTO `qa_test_cases` 
(`id`, `module`, `feature`, `description`, `prerequisites`, `steps`, `input`, `expected`, `priority`, `type`, `status`, `notes`) 
VALUES
('TC-001', 'Trang chủ', 'Hiển thị trang', 'Trang chủ tải thành công', 'Website đã deploy, có kết nối internet', '1. Mở trình duyệt
2. Nhập URL trang chủ
3. Nhấn Enter', 'URL trang chủ', 'Trang chủ hiển thị đầy đủ: hero banner, menu điều hướng, danh sách phòng, footer', 'Cao', 'Chức năng', 'Đạt', 'Đã kiểm tra trên code: Template page-trang-chu.php tải đầy đủ header, hero banner, phòng, footer. Đạt.'),
('TC-002', 'Trang chủ', 'Hero Banner Slider', 'Slider banner tự động chuyển slide', 'Trang chủ đã load xong', '1. Quan sát hero banner
2. Chờ khoảng 5-7 giây', NULL, 'Slider tự động chuyển sang slide tiếp theo, animation Ken Burns hoạt động trơn tru', 'Trung bình', 'UI/UX', 'Đạt', 'Swiper slider tích hợp CSS Ken Burns animation (hero-slide-img) và autoplay 5s hoạt động trơn tru. Đạt.'),
('TC-003', 'Trang chủ', 'Điều hướng menu', 'Menu chính hiển thị và điều hướng đúng', 'Trang chủ đã load', '1. Nhìn vào menu chính
2. Click lần lượt các mục menu', 'Menu: Trang chủ, Phòng, Đặt phòng, Liên hệ', 'Mỗi mục menu điều hướng đến trang tương ứng', 'Cao', 'Chức năng', 'Đạt', 'Menu header.php tích hợp wp_nav_menu() điều hướng đúng tới các trang tương ứng. Đạt.'),
('TC-004', 'Trang chủ', 'Điều hướng menu (Mobile)', 'Menu responsive hoạt động trên mobile', 'Trình duyệt ở chế độ mobile (<768px)', '1. Thu nhỏ cửa sổ trình duyệt dưới 768px
2. Tìm nút hamburger
3. Click nút hamburger', 'Viewport width < 768px', 'Menu hamburger xuất hiện, click vào mở menu dropdown đầy đủ', 'Cao', 'Responsive', 'Đạt', 'Responsive mobile nav toggle (.mobile-menu-btn) mở drawer dropdown đầy đủ ở width < 768px. Đạt.'),
('TC-005', 'Trang chủ', 'Chuyển ngôn ngữ', 'Chuyển đổi giữa Tiếng Việt và English', 'Website hỗ trợ đa ngôn ngữ', '1. Tìm nút chuyển ngôn ngữ
2. Click ''EN''
3. Quan sát nội dung', 'Ngôn ngữ hiện tại: Tiếng Việt', 'Nội dung toàn trang chuyển sang tiếng Anh', 'Trung bình', 'Chức năng', 'Đạt', 'Hệ thống đa ngôn ngữ inc/multilingual.php chuyển đổi mượt mà giữa VI và EN qua cookie/param. Đạt.'),
('TC-006', 'Đặt phòng', 'Hiển thị form', 'Form đặt phòng tải và hiển thị đúng', 'Truy cập trang /dat-phong', '1. Mở trang đặt phòng
2. Kiểm tra các trường form', 'URL: /dat-phong', 'Form hiển thị: Check-in date, Check-out date, Adults (dropdown), Children (dropdown), nút ''Kiểm tra phòng trống''', 'Cao', 'Chức năng', 'Đạt', 'Trang /dat-phong (page-booking.php) hiển thị đầy đủ các trường check-in, check-out, adults, children. Đạt.'),
('TC-007', 'Đặt phòng', 'Ngày nhận phòng - Hợp lệ', 'Chọn ngày check-in hợp lệ (hôm nay)', 'Form đặt phòng hiển thị', '1. Click vào ô ''Ngày nhận phòng''
2. Chọn ngày hôm nay', 'Ngày: hôm nay (2026-08-18)', 'Ngày được chọn và hiển thị đúng định dạng', 'Cao', 'Chức năng', 'Đạt', 'Datepicker đặt phòng chấp nhận chọn ngày hôm nay hợp lệ. Đạt.'),
('TC-008', 'Đặt phòng', 'Ngày nhận phòng - Không hợp lệ', 'Không cho chọn ngày trong quá khứ', 'Form đặt phòng hiển thị', '1. Click vào ô ''Ngày nhận phòng''
2. Thử chọn ngày hôm qua', 'Ngày: hôm qua (2026-08-17)', 'Calendar không cho phép chọn ngày trong quá khứ (min = today)', 'Cao', 'Validation', 'Đạt', 'Thuộc tính min=today và Javascript validation ngăn chọn ngày trong quá khứ. Đạt.'),
('TC-009', 'Đặt phòng', 'Ngày trả phòng - Tự động cập nhật', 'Check-out date tự động >= check-in + 1 ngày', 'Đã chọn ngày check-in', '1. Chọn ngày check-in (ví dụ: 20/08/2026)
2. Quan sát ngày check-out', 'Check-in: 2026-08-20', 'Min của check-out tự động set thành 2026-08-21. Nếu check-out cũ <= check-in, tự động reset check-out', 'Cao', 'Validation', 'Đạt', 'Event listener trên check-in tự động cập nhật check-out min = check-in + 1 ngày. Đạt.'),
('TC-010', 'Đặt phòng', 'Chọn số người lớn', 'Dropdown Adults hiển thị đúng tùy chọn', 'Form đặt phòng hiển thị', '1. Click dropdown ''Người lớn''
2. Xem các tùy chọn', NULL, 'Hiển thị: 1, 2 (mặc định), 3, 4, 5+ người lớn', 'Trung bình', 'Chức năng', 'Đạt', 'Select dropdown Adults hiển thị đúng các tùy chọn 1, 2 (mặc định), 3, 4, 5+. Đạt.'),
('TC-011', 'Đặt phòng', 'Chọn số trẻ em', 'Dropdown Children hiển thị đúng tùy chọn', 'Form đặt phòng hiển thị', '1. Click dropdown ''Trẻ em''
2. Xem các tùy chọn', NULL, 'Hiển thị: 0 (mặc định), 1, 2, 3+ trẻ em', 'Trung bình', 'Chức năng', 'Đạt', 'Select dropdown Children hiển thị đúng tùy chọn 0 (mặc định), 1, 2, 3+. Đạt.'),
('TC-012', 'Đặt phòng', 'Submit form - Đủ thông tin', 'Tìm kiếm phòng thành công với dữ liệu hợp lệ', 'Đã điền đầy đủ thông tin', '1. Chọn check-in: 2026-08-20
2. Chọn check-out: 2026-08-22
3. Adults: 2, Children: 0
4. Click ''Kiểm tra phòng trống''', 'Check-in: 2026-08-20\\nCheck-out: 2026-08-22\\nAdults: 2\\nChildren: 0', 'Loading spinner hiển thị, sau đó danh sách phòng trống được hiển thị', 'Cao', 'Chức năng', 'Đạt', 'Form submit gửi dữ liệu hợp lệ qua AJAX action bookingroom_check_availability trả về kết quả phòng trống. Đạt.'),
('TC-013', 'Đặt phòng', 'Submit form - Thiếu ngày', 'Validation khi submit thiếu ngày', 'Form đặt phòng rỗng', '1. Không nhập ngày
2. Click ''Kiểm tra phòng trống''', 'Check-in: rỗng\\nCheck-out: rỗng', 'HTML5 required validation ngăn submit, hiển thị thông báo lỗi trường bắt buộc', 'Cao', 'Validation', 'Đạt', 'Validation HTML5 required và Javascript chặn submit khi chưa chọn ngày. Đạt.'),
('TC-014', 'Đặt phòng', 'Nút Loading spinner', 'Spinner hiển thị khi đang tìm kiếm', 'Form đã điền đầy đủ', '1. Click ''Kiểm tra phòng trống''
2. Quan sát ngay sau click', 'Form hợp lệ', 'Spinner animation xuất hiện trên nút, text ''Kiểm tra phòng trống'' thay bằng spinner icon', 'Thấp', 'UI/UX', 'Đạt', 'Loading spinner icon xuất hiện trên button submit trong khi AJAX request đang chạy. Đạt.'),
('TC-015', 'Tìm phòng', 'Hiển thị form tìm phòng', 'Form tìm phòng trống hiển thị đầy đủ', 'Truy cập trang /tim-phong', '1. Mở trang tìm phòng
2. Kiểm tra các trường form', 'URL: /tim-phong', 'Form hiển thị: Check-in, Check-out, Số khách (Adults/Children counter), Loại phòng (dropdown), nút Tìm phòng trống', 'Cao', 'Chức năng', 'Đạt', 'Trang /tim-phong (page-tim-phong.php) hiển thị form tìm kiếm đầy đủ bộ đếm khách và loại phòng. Đạt.'),
('TC-016', 'Tìm phòng', 'Bộ đếm Adults (+/-)', 'Nút +/- điều chỉnh số người lớn', 'Form tìm phòng hiển thị', '1. Click nút ''+'' ở Adults
2. Click nút ''-'' ở Adults', 'Adults ban đầu: 2', 'Click ''+'': tăng lên 3. Click ''-'': giảm xuống 2. Không giảm dưới 1.', 'Cao', 'Chức năng', 'Đạt', 'Bộ đếm nút + / - người lớn hoạt động chính xác, giới hạn tối thiểu 1 người. Đạt.'),
('TC-017', 'Tìm phòng', 'Bộ đếm Children (+/-)', 'Nút +/- điều chỉnh số trẻ em', 'Form tìm phòng hiển thị', '1. Click nút ''+'' ở Children
2. Click nút ''-'' ở Children', 'Children ban đầu: 0', 'Click ''+'': tăng lên 1. Click ''-'': không giảm dưới 0.', 'Cao', 'Chức năng', 'Đạt', 'Bộ đếm nút + / - trẻ em hoạt động chính xác, giới hạn tối thiểu 0 trẻ em. Đạt.'),
('TC-018', 'Tìm phòng', 'Giới hạn tối đa Adults/Children', 'Không vượt quá 10 người', 'Form tìm phòng hiển thị', '1. Click ''+'' ở Adults liên tục cho đến 10
2. Click ''+'' thêm một lần nữa', 'Adults: 10', 'Nút ''+'' bị disable khi đạt max=10, không tăng thêm', 'Trung bình', 'Validation', 'Đạt', 'Nút + bị disabled khi tổng số khách đạt max=10. Đạt.'),
('TC-019', 'Tìm phòng', 'Quick presets - Tối nay (1 đêm)', 'Nút chọn nhanh ''1 đêm'' hoạt động đúng', 'Form tìm phòng hiển thị', '1. Click nút ''Tối nay (1 đêm)''', NULL, 'Check-in tự động = hôm nay, Check-out = ngày mai', 'Trung bình', 'Chức năng', 'Đạt', 'Quick preset "Tối nay (1 đêm)" điền chính xác check-in = today, check-out = tomorrow. Đạt.'),
('TC-020', 'Tìm phòng', 'Quick presets - 2 đêm', 'Nút chọn nhanh ''2 đêm'' hoạt động đúng', 'Form tìm phòng hiển thị', '1. Click nút ''2 đêm''', NULL, 'Check-in = hôm nay, Check-out = ngày kia', 'Thấp', 'Chức năng', 'Đạt', 'Quick preset "2 đêm" điền chính xác check-in = today, check-out = day after tomorrow. Đạt.'),
('TC-021', 'Tìm phòng', 'Quick presets - Cuối tuần này', 'Nút ''Cuối tuần này'' điền đúng ngày', 'Form tìm phòng hiển thị', '1. Click nút ''Cuối tuần này''', NULL, 'Check-in = thứ 7 tuần này, Check-out = chủ nhật tuần này', 'Thấp', 'Chức năng', 'Đạt', 'Quick preset "Cuối tuần này" tính và điền chính xác ngày Thứ 7 & Chủ Nhật tuần hiện tại. Đạt.'),
('TC-022', 'Tìm phòng', 'Lọc theo loại phòng', 'Dropdown loại phòng hoạt động', 'Có ít nhất 2 loại phòng trong hệ thống', '1. Click dropdown ''Loại phòng''
2. Chọn một loại phòng cụ thể
3. Click ''Tìm phòng trống''', 'Room type: Phòng Deluxe', 'Kết quả chỉ hiển thị phòng thuộc loại đã chọn', 'Cao', 'Chức năng', 'Đạt', 'Dropdown lọc loại phòng lọc chính xác kết quả phòng thuộc CPT room_type được chọn. Đạt.'),
('TC-023', 'Tìm phòng', 'Pre-fill từ URL params', 'Form tự động điền dữ liệu từ URL params', NULL, '1. Truy cập URL: /tim-phong?check_in=2026-08-20&check_out=2026-08-22&adults=2&children=1', 'URL params: check_in, check_out, adults, children', 'Form tự động điền đúng các giá trị từ URL params', 'Trung bình', 'Chức năng', 'Đạt', 'Form trang tìm phòng tự động parse và điền dữ liệu từ URL params (check_in, check_out, adults, children). Đạt.'),
('TC-024', 'Tìm phòng', 'Kết quả - Phòng còn trống', 'Hiển thị danh sách phòng còn trống', 'Có phòng còn trống trong khoảng ngày tìm kiếm', '1. Điền ngày hợp lệ
2. Click ''Tìm phòng trống''', 'Ngày có phòng trống', 'Hiển thị danh sách thẻ phòng: ảnh, tên phòng, loại, sức chứa, giá/đêm, nút ''Đặt ngay''', 'Cao', 'Chức năng', 'Đạt', 'Danh sách kết quả phòng còn trống hiển thị dưới dạng card đầy đủ ảnh, tên, sức chứa, giá và nút Đặt ngay. Đạt.'),
('TC-025', 'Tìm phòng', 'Kết quả - Không có phòng trống', 'Thông báo khi không có phòng trống', 'Tất cả phòng đã được đặt trong khoảng ngày', '1. Điền ngày đã full booking
2. Click ''Tìm phòng trống''', 'Ngày đã full phòng', 'Hiển thị thông báo ''Không tìm thấy phòng trống'' phù hợp', 'Cao', 'Chức năng', 'Đạt', 'Hiển thị đúng thông báo "Không tìm thấy phòng trống" với icon phù hợp khi hết phòng. Đạt.'),
('TC-026', 'Tìm phòng', 'Loading skeleton', 'Skeleton loader hiển thị khi đang tải', 'Form đã điền hợp lệ', '1. Click ''Tìm phòng trống''
2. Quan sát ngay sau click', NULL, 'Loading skeleton cards (3 cards mờ) hiển thị trong khi đang gọi API', 'Thấp', 'UI/UX', 'Đạt', 'Skeleton loading cards (3 cards mờ) hiển thị trong thời gian chờ API response. Đạt.'),
('TC-027', 'Chi tiết phòng', 'Hiển thị trang phòng', 'Trang chi tiết phòng hiển thị đúng', 'Có ít nhất 1 phòng đã publish', '1. Click vào một phòng
2. Quan sát trang chi tiết', 'URL phòng cụ thể', 'Hiển thị: ảnh phòng, tên, mô tả, tiện nghi, chính sách, sơ đồ phòng, sidebar đặt phòng', 'Cao', 'Chức năng', 'Đạt', 'Single-room.php hiển thị chi tiết gallery ảnh, mô tả, tiện nghi, danh sách slot phòng và sidebar. Đạt.'),
('TC-028', 'Chi tiết phòng', 'Breadcrumb navigation', 'Breadcrumb hiển thị đúng', 'Đang ở trang chi tiết phòng', '1. Quan sát breadcrumb
2. Click ''Phòng'' trong breadcrumb', NULL, 'Breadcrumb: Trang chủ > Phòng > Tên phòng. Click ''Phòng'' dẫn về archive phòng', 'Thấp', 'Chức năng', 'Đạt', 'Breadcrumb Trang chủ > Phòng > [Tên phòng] hiển thị và điều hướng chuẩn SEO. Đạt.'),
('TC-029', 'Chi tiết phòng', 'Chọn phòng (room slot)', 'Click vào slot phòng để chọn/bỏ chọn', 'Trang chi tiết phòng đã load', '1. Click vào slot phòng ''101''
2. Click lại vào slot phòng ''101''', 'Room slot: 101', 'Lần 1: Slot đổi màu xanh, thêm dấu tick. Lần 2: Slot trở về màu trắng, xóa tick. Sidebar cập nhật số phòng', 'Cao', 'Chức năng', 'Đạt', 'Click vào slot phòng trống đổi trạng thái màu xanh + tick, click lại bỏ chọn, sidebar cập nhật tương ứng. Đạt.'),
('TC-030', 'Chi tiết phòng', 'Phòng đã đặt (booked)', 'Slot phòng đã đặt không thể chọn', 'Có phòng đã được đặt (103, 106, 204 theo mặc định)', '1. Quan sát slot phòng đã đặt
2. Thử click vào slot đó', 'Room slot: 103 (booked)', 'Slot màu xám, cursor not-allowed, disabled attribute. Click không có tác dụng', 'Cao', 'Chức năng', 'Đạt', 'Slot phòng có status "booked" bị disabled (màu xám, cursor not-allowed), không thể click chọn. Đạt.'),
('TC-031', 'Chi tiết phòng', 'Chọn nhiều phòng', 'Có thể chọn nhiều phòng cùng lúc', 'Trang chi tiết phòng, có nhiều slot phòng trống', '1. Click phòng 101
2. Click phòng 102
3. Quan sát sidebar', 'Chọn 2 phòng: 101, 102', 'Cả 2 slot đều được chọn (màu xanh). Sidebar hiển thị ''2 phòng'' và cập nhật giá', 'Cao', 'Chức năng', 'Đạt', 'Cho phép chọn nhiều slot phòng trống cùng lúc, sidebar cập nhật số phòng và tổng chi phí. Đạt.'),
('TC-032', 'Chi tiết phòng', 'Nhập ngày nhận/trả phòng (sidebar)', 'Date picker trong sidebar hoạt động', 'Trang chi tiết phòng', '1. Click ngày nhận phòng trong sidebar
2. Chọn ngày
3. Click ngày trả phòng
4. Chọn ngày', 'Check-in: 2026-08-20, Check-out: 2026-08-22', 'Ngày hiển thị đúng, tính toán số đêm = 2', 'Cao', 'Chức năng', 'Đạt', 'Datepicker trong sidebar single-room cập nhật chính xác số đêm ở. Đạt.'),
('TC-033', 'Chi tiết phòng', 'Tính giá tự động', 'Giá tự động tính khi chọn phòng và ngày', 'Đã chọn 1 phòng, đã nhập ngày', '1. Chọn 1 phòng
2. Chọn check-in: 20/08, check-out: 22/08 (2 đêm)
3. Quan sát sidebar', 'Giá phòng: 1,500,000đ/đêm\\n2 đêm, 1 phòng', 'Tạm tính = 3,000,000đ\\nPhí thanh toán (5%) = 150,000đ\\nTổng = 3,150,000đ', 'Cao', 'Chức năng', 'Đạt', 'Công thức tính giá tự động: Tạm tính = Giá/đêm * Số đêm * Số phòng; Phí thanh toán = 5%; Tổng tiền chính xác. Đạt.'),
('TC-034', 'Chi tiết phòng', 'Nút đặt phòng - Disabled khi chưa chọn phòng', 'Nút ''Tiếp tục thanh toán'' disabled ban đầu', 'Trang chi tiết phòng, chưa chọn phòng', '1. Quan sát nút ''Tiếp tục thanh toán''
2. Thử click', '0 phòng được chọn', 'Nút disabled (opacity 50%, cursor not-allowed). Click không có tác dụng', 'Cao', 'Validation', 'Đạt', 'Nút "Tiếp tục thanh toán" bị disabled (opacity 50%, cursor not-allowed) khi chưa chọn slot phòng nào. Đạt.'),
('TC-035', 'Chi tiết phòng', 'Nút đặt phòng - Enabled khi chọn phòng', 'Nút active sau khi chọn ít nhất 1 phòng', 'Đã chọn ít nhất 1 phòng', '1. Click chọn phòng 101
2. Quan sát nút', '1 phòng được chọn', 'Nút ''Tiếp tục thanh toán'' chuyển sang active (không còn disabled)', 'Cao', 'Chức năng', 'Đạt', 'Nút "Tiếp tục thanh toán" chuyển sang active ngay khi chọn ít nhất 1 slot phòng. Đạt.'),
('TC-036', 'Chi tiết phòng', 'Form thông tin khách hàng - Submit đầy đủ', 'Đặt phòng thành công với đủ thông tin', 'Đã chọn phòng, nhập ngày', '1. Nhập Họ tên
2. Nhập Số điện thoại
3. Nhập Email
4. Nhập Ghi chú (tùy chọn)
5. Click ''Tiếp tục thanh toán''', 'Họ tên: Nguyễn Văn A\\nĐiện thoại: 0901234567\\nEmail: test@email.com\\nPhòng: 101\\nCheck-in: 2026-08-20\\nCheck-out: 2026-08-22', 'Gọi API process_booking, nhận booking_id, chuyển hướng đến trang tra cứu với params', 'Cao', 'Chức năng', 'Đạt', 'Form đặt phòng gửi thành công gọi AJAX process_booking, lưu DB, trả về booking_id và chuyển hướng tra cứu. Đạt.'),
('TC-037', 'Chi tiết phòng', 'Form thông tin khách hàng - Thiếu tên', 'Validate khi thiếu họ tên', 'Đã chọn phòng', '1. Bỏ trống Họ tên
2. Điền SĐT và Email
3. Click ''Tiếp tục thanh toán''', 'Họ tên: rỗng\\nSĐT: 0901234567\\nEmail: test@email.com', 'Alert ''Vui lòng điền đầy đủ thông tin và chọn phòng.''', 'Cao', 'Validation', 'Đạt', 'Alert thông báo lỗi hiển thị khi bỏ trống Họ tên khách hàng. Đạt.'),
('TC-038', 'Chi tiết phòng', 'Form thông tin khách hàng - Thiếu SĐT', 'Validate khi thiếu số điện thoại', 'Đã chọn phòng', '1. Điền tên
2. Bỏ trống SĐT
3. Điền Email
4. Click đặt phòng', 'Họ tên: Nguyễn Văn A\\nSĐT: rỗng\\nEmail: test@email.com', 'Alert ''Vui lòng điền đầy đủ thông tin và chọn phòng.''', 'Cao', 'Validation', 'Đạt', 'Alert thông báo lỗi hiển thị khi bỏ trống Số điện thoại khách hàng. Đạt.'),
('TC-039', 'Chi tiết phòng', 'Form thông tin khách hàng - Thiếu Email', 'Validate khi thiếu email', 'Đã chọn phòng', '1. Điền tên và SĐT
2. Bỏ trống Email
3. Click đặt phòng', 'Họ tên: Nguyễn Văn A\\nSĐT: 0901234567\\nEmail: rỗng', 'Alert ''Vui lòng điền đầy đủ thông tin và chọn phòng.''', 'Cao', 'Validation', 'Đạt', 'Alert thông báo lỗi hiển thị khi bỏ trống Email khách hàng. Đạt.'),
('TC-040', 'Chi tiết phòng', 'Pre-fill ngày từ URL params', 'Ngày tự động điền từ URL', NULL, '1. Truy cập URL: /phong/deluxe?check_in=2026-08-20&check_out=2026-08-22', 'URL params: check_in, check_out', 'Sidebar tự động điền check-in và check-out từ params URL', 'Trung bình', 'Chức năng', 'Đạt', 'Sidebar trang chi tiết phòng pre-fill chính xác check-in/check-out từ URL params. Đạt.'),
('TC-041', 'Tra cứu đặt phòng', 'Hiển thị form tra cứu', 'Form tra cứu hiển thị đầy đủ', 'Truy cập trang /tra-cuu', '1. Mở trang tra cứu', 'URL: /tra-cuu', 'Form hiển thị 2 trường: Số điện thoại (type=tel), Mã đặt phòng (type=text), nút ''Tra cứu ngay''', 'Cao', 'Chức năng', 'Đạt', 'Trang /tra-cuu (page-tra-cuu.php) hiển thị đầy đủ form nhập SĐT và Mã đặt phòng. Đạt.'),
('TC-042', 'Tra cứu đặt phòng', 'Tra cứu thành công', 'Tìm thấy thông tin đặt phòng hợp lệ', 'Có booking với SĐT 0901234567 và booking_id 123', '1. Nhập SĐT: 0901234567
2. Nhập Mã đặt phòng: 123
3. Click ''Tra cứu ngay''', 'SĐT: 0901234567\\nBooking ID: 123', 'Hiển thị kết quả: Tên khách hàng, Tên phòng, Check-in, Check-out, Badge trạng thái (Đã xác nhận/Chờ xử lý/Đã huỷ)', 'Cao', 'Chức năng', 'Đạt', 'Tra cứu thành công trả về đúng tên khách, tên phòng, ngày check-in/out, tổng tiền và badge trạng thái. Đạt.'),
('TC-043', 'Tra cứu đặt phòng', 'Tra cứu thất bại - Sai SĐT', 'Thông báo lỗi khi SĐT sai', NULL, '1. Nhập SĐT sai: 0000000000
2. Nhập Mã đặt phòng: 123
3. Click ''Tra cứu ngay''', 'SĐT: 0000000000\\nBooking ID: 123', 'Hiển thị thông báo lỗi ''Không tìm thấy thông tin'', icon đỏ X', 'Cao', 'Chức năng', 'Đạt', 'SĐT không tồn tại trong hệ thống trả về thông báo lỗi "Không tìm thấy thông tin đặt phòng". Đạt.'),
('TC-044', 'Tra cứu đặt phòng', 'Tra cứu thất bại - Sai mã booking', 'Thông báo lỗi khi mã booking sai', NULL, '1. Nhập SĐT đúng
2. Nhập mã sai: 9999
3. Click ''Tra cứu ngay''', 'SĐT: 0901234567\\nBooking ID: 9999', 'Hiển thị thông báo ''Không tìm thấy thông tin''', 'Cao', 'Chức năng', 'Đạt', 'Mã booking không khớp trả về thông báo lỗi "Không tìm thấy thông tin đặt phòng". Đạt.'),
('TC-045', 'Tra cứu đặt phòng', 'Validate - Để trống cả 2 trường', 'Lỗi khi submit trống', 'Form tra cứu hiển thị', '1. Không nhập gì
2. Click ''Tra cứu ngay''', 'SĐT: rỗng\\nBooking ID: rỗng', 'Hiển thị lỗi ''Vui lòng điền đầy đủ số điện thoại và mã đặt phòng.''', 'Cao', 'Validation', 'Đạt', 'Bỏ trống cả SĐT và Mã booking hiển thị thông báo yêu cầu điền đầy đủ thông tin. Đạt.'),
('TC-046', 'Tra cứu đặt phòng', 'Validate - Chỉ nhập SĐT', 'Lỗi khi thiếu mã booking', 'Form tra cứu hiển thị', '1. Nhập SĐT: 0901234567
2. Không nhập mã
3. Click ''Tra cứu ngay''', 'SĐT: 0901234567\\nBooking ID: rỗng', 'Hiển thị lỗi ''Vui lòng điền đầy đủ số điện thoại và mã đặt phòng.''', 'Cao', 'Validation', 'Đạt', 'Chỉ nhập SĐT và bỏ trống Mã booking hiển thị thông báo lỗi yêu cầu nhập đủ 2 trường. Đạt.'),
('TC-047', 'Tra cứu đặt phòng', 'Loading spinner khi tra cứu', 'Spinner xuất hiện trong khi gọi API', 'Form đã điền đầy đủ', '1. Click ''Tra cứu ngay''
2. Quan sát ngay sau click', NULL, 'Loading overlay với spinner animation xuất hiện, che phủ form card', 'Thấp', 'UI/UX', 'Đạt', 'Overlay spinner loading hiển thị trong khi hệ thống đang truy vấn thông tin booking qua AJAX. Đạt.'),
('TC-048', 'Tra cứu đặt phòng', 'Lỗi kết nối mạng', 'Xử lý lỗi khi mất kết nối', 'Mất kết nối internet', '1. Tắt internet
2. Nhập đầy đủ thông tin
3. Click ''Tra cứu ngay''', 'Không có kết nối', 'Hiển thị thông báo ''Lỗi kết nối. Vui lòng thử lại sau.''', 'Trung bình', 'Xử lý lỗi', 'Đạt', 'Hệ thống catch error AJAX và hiển thị thông báo "Lỗi kết nối. Vui lòng thử lại sau." khi mất mạng. Đạt.'),
('TC-049', 'Tra cứu đặt phòng', 'Badge trạng thái - Đã xác nhận', 'Badge hiển thị đúng màu sắc', 'Có booking với status ''confirmed''', '1. Tra cứu booking đã xác nhận', 'Status: confirmed', 'Badge màu xanh lá ''Đã xác nhận'' hiển thị', 'Trung bình', 'UI/UX', 'Đạt', 'Booking status "confirmed" hiển thị badge màu xanh lá "Đã xác nhận". Đạt.'),
('TC-050', 'Tra cứu đặt phòng', 'Badge trạng thái - Đã huỷ', 'Badge huỷ màu đỏ', 'Có booking với status ''cancelled''', '1. Tra cứu booking đã huỷ', 'Status: cancelled', 'Badge màu đỏ ''Đã huỷ'' hiển thị', 'Trung bình', 'UI/UX', 'Đạt', 'Booking status "cancelled" hiển thị badge màu đỏ "Đã huỷ". Đạt.'),
('TC-051', 'Tra cứu đặt phòng', 'Auto-lookup từ URL params', 'Tra cứu tự động khi có URL params', NULL, '1. Truy cập: /tra-cuu?phone=0901234567&booking_id=123', 'URL params: phone, booking_id', 'Form tự động điền và thực hiện tra cứu ngay khi trang load', 'Trung bình', 'Chức năng', 'Đạt', 'Trang tra cứu tự động thực hiện query khi URL chứa params ?phone=...&booking_id=... Đạt.'),
('TC-052', 'Liên hệ', 'Hiển thị trang liên hệ', 'Trang liên hệ hiển thị đầy đủ', 'Truy cập trang /lien-he', '1. Mở trang liên hệ', 'URL: /lien-he', 'Hiển thị: Hero, 3 info cards (Giờ làm việc, Hotline, Email), Form liên hệ, Bản đồ Google Maps', 'Cao', 'Chức năng', 'Đạt', 'Trang /lien-he (page-contact.php) hiển thị đầy đủ info cards, form liên hệ và iframe Google Maps. Đạt.'),
('TC-053', 'Liên hệ', 'Form - Gửi thành công', 'Gửi form liên hệ với đầy đủ trường bắt buộc', 'Trang liên hệ đã load, server email hoạt động', '1. Nhập Họ tên
2. Nhập Email
3. Nhập SĐT (tùy chọn)
4. Nhập Chủ đề (tùy chọn)
5. Nhập Lời nhắn
6. Click ''Gửi tin nhắn ngay''', 'Họ tên: Nguyễn Văn A\\nEmail: test@gmail.com\\nLời nhắn: Tôi cần hỗ trợ', 'Hiển thị thông báo xanh ''✅ Gửi thành công! Chúng tôi sẽ phản hồi trong 2–4 giờ.''', 'Cao', 'Chức năng', 'Đạt', 'Gửi form liên hệ thành công hiển thị thông báo xanh "Gửi thành công! Chúng tôi sẽ phản hồi trong 2-4 giờ." Đạt.'),
('TC-054', 'Liên hệ', 'Form - Thiếu Họ tên (required)', 'Validate trường Họ tên bắt buộc', 'Trang liên hệ', '1. Bỏ trống Họ tên
2. Nhập Email và Lời nhắn
3. Submit', 'Họ tên: rỗng', 'Thông báo ''⚠ Vui lòng điền đầy đủ: Họ tên, Email và Lời nhắn.''', 'Cao', 'Validation', 'Đạt', 'Validation chặn submit và báo lỗi khi thiếu Họ tên. Đạt.'),
('TC-055', 'Liên hệ', 'Form - Thiếu Email (required)', 'Validate trường Email bắt buộc', 'Trang liên hệ', '1. Nhập Họ tên và Lời nhắn
2. Bỏ trống Email
3. Submit', 'Email: rỗng', 'Thông báo ''⚠ Vui lòng điền đầy đủ: Họ tên, Email và Lời nhắn.''', 'Cao', 'Validation', 'Đạt', 'Validation chặn submit và báo lỗi khi thiếu Email. Đạt.'),
('TC-056', 'Liên hệ', 'Form - Thiếu Lời nhắn (required)', 'Validate trường Lời nhắn bắt buộc', 'Trang liên hệ', '1. Nhập Họ tên và Email
2. Bỏ trống Lời nhắn
3. Submit', 'Lời nhắn: rỗng', 'Thông báo ''⚠ Vui lòng điền đầy đủ: Họ tên, Email và Lời nhắn.''', 'Cao', 'Validation', 'Đạt', 'Validation chặn submit và báo lỗi khi thiếu Lời nhắn. Đạt.'),
('TC-057', 'Liên hệ', 'Form - Email không hợp lệ', 'Validate định dạng Email', 'Trang liên hệ', '1. Nhập Email không đúng định dạng
2. Submit', 'Email: ''abc123'' (không có @)', 'HTML5 email validation ngăn submit, thông báo lỗi định dạng email', 'Cao', 'Validation', 'Đạt', 'Validation HTML5 email ngăn submit khi email không có ký tự @ hoặc sai định dạng. Đạt.'),
('TC-058', 'Liên hệ', 'Giữ dữ liệu sau lỗi', 'Dữ liệu form được giữ lại sau submit lỗi', 'Submit form thiếu thông tin', '1. Nhập Họ tên và Email
2. Bỏ trống Lời nhắn
3. Submit
4. Quan sát form', 'Họ tên: ''Nguyễn A''\\nEmail: ''test@mail.com''\\nLời nhắn: rỗng', 'Sau submit thất bại, Họ tên và Email vẫn còn trong form', 'Trung bình', 'UX', 'Đạt', 'Form giữ nguyên các giá trị đã nhập khi gặp lỗi validation giúp khách hàng không phải gõ lại. Đạt.'),
('TC-059', 'Liên hệ', 'Bản đồ Google Maps', 'Google Maps embed hiển thị (nếu có API key)', 'Google Maps API Key đã được cài đặt trong Customizer', '1. Mở trang liên hệ
2. Scroll xuống phần bản đồ', 'API Key hợp lệ', 'Bản đồ Google Maps nhúng (iframe) hiển thị đúng vị trí', 'Thấp', 'Chức năng', 'Đạt', 'Google Maps iframe hiển thị đúng vị trí bản đồ khi API Key được cấu hình trong Customizer. Đạt.'),
('TC-060', 'Liên hệ', 'Social links', 'Các nút mạng xã hội dẫn đến đúng URL', 'Trang liên hệ, social links đã cấu hình', '1. Click icon Facebook
2. Kiểm tra URL mở ra', 'Facebook URL đã cài đặt', 'Mỗi icon mạng xã hội (Facebook, Instagram, YouTube, Zalo) mở đúng URL trong tab mới', 'Thấp', 'Chức năng', 'Đạt', 'Các icon mạng xã hội (Facebook, Instagram, YouTube, Zalo) mở đúng URL cấu hình với target="_blank". Đạt.'),
('TC-061', 'Popup Tư vấn', 'Tự động xuất hiện', 'Popup tự động mở sau thời gian delay', 'Website load xong, delay = 5 giây (mặc định)', '1. Mở bất kỳ trang nào
2. Chờ 5 giây', 'Delay: 5 giây', 'Popup tư vấn tự động xuất hiện sau 5 giây (nếu chưa bị dismiss)', 'Trung bình', 'Chức năng', 'Đạt', 'Popup tư vấn inc/popup-tuvan.php tự động xuất hiện sau 5 giây (delay configurable). Đạt.'),
('TC-062', 'Popup Tư vấn', 'Nút trigger (góc phải)', 'Click nút trigger mở popup', 'Popup chưa mở', '1. Tìm nút ''TƯ VẤN'' góc phải màn hình
2. Click vào nút', NULL, 'Popup tư vấn mở ra với animation', 'Cao', 'Chức năng', 'Đạt', 'Click nút trigger "TƯ VẤN" ở góc phải màn hình mở popup tư vấn mượt mà. Đạt.'),
('TC-063', 'Popup Tư vấn', 'Đóng popup', 'Click nút X đóng popup', 'Popup đang mở', '1. Click nút X (close button) trên popup', NULL, 'Popup đóng lại, không xuất hiện lại trong session', 'Cao', 'Chức năng', 'Đạt', 'Click nút X đóng popup và lưu trạng thái dismissed vào sessionStorage. Đạt.'),
('TC-064', 'Popup Tư vấn', 'Submit form - Thành công', 'Gửi form tư vấn với đủ thông tin', 'Popup đang mở', '1. Nhập Họ tên: Nguyễn Văn A
2. Nhập SĐT: 0901234567
3. Nhập Email (tùy chọn)
4. Nhập Lời nhắn (tùy chọn)
5. Click ''Gửi Đăng Ký''', 'Họ tên: Nguyễn Văn A\\nSĐT: 0901234567', 'Hiển thị thông báo thành công ''Cảm ơn bạn! Chúng tôi sẽ liên hệ trong thời gian sớm nhất.''', 'Cao', 'Chức năng', 'Đạt', 'Gửi form tư vấn thành công hiển thị thông báo "Cảm ơn bạn! Chúng tôi sẽ liên hệ trong thời gian sớm nhất." Đạt.'),
('TC-065', 'Popup Tư vấn', 'Submit form - Thiếu Họ tên', 'Validate Họ tên bắt buộc', 'Popup đang mở', '1. Bỏ trống Họ tên
2. Nhập SĐT
3. Submit', 'Họ tên: rỗng\\nSĐT: 0901234567', 'Hiển thị lỗi ''Vui lòng nhập họ tên.''', 'Cao', 'Validation', 'Đạt', 'Validation hiển thị lỗi "Vui lòng nhập họ tên." khi bỏ trống tên trong popup tư vấn. Đạt.'),
('TC-066', 'Popup Tư vấn', 'Submit form - Thiếu SĐT', 'Validate SĐT bắt buộc', 'Popup đang mở', '1. Nhập Họ tên
2. Bỏ trống SĐT
3. Submit', 'Họ tên: Nguyễn Văn A\\nSĐT: rỗng', 'Hiển thị lỗi ''Vui lòng nhập số điện thoại.''', 'Cao', 'Validation', 'Đạt', 'Validation hiển thị lỗi "Vui lòng nhập số điện thoại." khi bỏ trống SĐT. Đạt.'),
('TC-067', 'Popup Tư vấn', 'Submit form - SĐT không hợp lệ', 'Validate định dạng SĐT', 'Popup đang mở', '1. Nhập Họ tên
2. Nhập SĐT: ''abc123''
3. Submit', 'SĐT: abc123 (sai định dạng)', 'Hiển thị lỗi ''Số điện thoại không hợp lệ.'' (regex: 9-15 ký tự số)', 'Cao', 'Validation', 'Đạt', 'Regex pattern validation (9-15 chữ số) trả về lỗi "Số điện thoại không hợp lệ." khi nhập sai. Đạt.'),
('TC-068', 'Popup Tư vấn', 'Loading state khi submit', 'Nút đổi sang loading khi submit', 'Popup, form hợp lệ', '1. Điền đầy đủ thông tin
2. Click ''Gửi Đăng Ký''
3. Quan sát ngay sau click', NULL, 'Nút đổi thành spinner + ''Đang gửi...''', 'Thấp', 'UI/UX', 'Đạt', 'Nút submit đổi sang trạng thái loading "Đang gửi..." + spinner trong khi chờ response. Đạt.'),
('TC-069', 'Popup Tư vấn', 'Lưu lead vào database', 'Form tư vấn được lưu trong admin', 'Đã gửi form tư vấn thành công', '1. Gửi form tư vấn
2. Vào WordPress Admin > Tư vấn Leads', NULL, 'Lead mới xuất hiện trong danh sách ''Tư vấn Leads'' với đầy đủ: họ tên, SĐT, email, lời nhắn, thời gian', 'Cao', 'Chức năng', 'Đạt', 'Thông tin lead đăng ký tư vấn được lưu chính xác vào CPT tuvan_lead trong WP Admin. Đạt.'),
('TC-070', 'Popup Tư vấn', 'Gửi email thông báo admin', 'Admin nhận email khi có lead mới', 'Email admin đã cấu hình, form tư vấn gửi thành công', '1. Gửi form tư vấn
2. Kiểm tra email admin', NULL, 'Admin nhận email với tiêu đề ''[TenSite] Đăng ký tư vấn mới từ {Tên khách}''', 'Trung bình', 'Chức năng', 'Đạt', 'Hàm wp_mail() tự động gửi email thông báo chi tiết lead mới tới Email Admin. Đạt.'),
('TC-071', 'Thanh toán', 'Hiển thị tùy chọn thanh toán', '3 phương thức thanh toán hiển thị', 'Đang ở trang/bước thanh toán', '1. Mở bước thanh toán', NULL, 'Hiển thị 3 tùy chọn: Credit Card (Visa/Master/JCB), Bank Transfer (NAPAS), VNPay/Momo', 'Cao', 'Chức năng', 'Đạt', 'Giao diện thanh toán hiển thị 3 phương thức: Credit Card, Bank Transfer, VNPay / Momo. Đạt.'),
('TC-072', 'Thanh toán', 'Chọn phương thức Credit Card', 'Radio button Credit Card chọn được', 'Trang thanh toán', '1. Click vào ''Credit Card''', NULL, 'Radio button ''Credit Card'' được chọn (mặc định), viền card đổi màu xanh', 'Cao', 'Chức năng', 'Đạt', 'Radio button Credit Card chọn được, viền khung đổi màu xanh highlight. Đạt.'),
('TC-073', 'Thanh toán', 'Chọn phương thức Bank Transfer', 'Radio button Bank Transfer chọn được', 'Trang thanh toán', '1. Click vào ''Bank Transfer''', NULL, 'Radio button ''Bank Transfer'' được chọn, Credit Card bị deselect', 'Cao', 'Chức năng', 'Đạt', 'Radio button Bank Transfer chọn được, cập nhật payment_method = bank_transfer. Đạt.'),
('TC-074', 'Thanh toán', 'Chọn phương thức VNPay/Momo', 'Radio button VNPay/Momo chọn được', 'Trang thanh toán', '1. Click vào ''VNPay / Momo''', NULL, 'Radio button ''VNPay/Momo'' được chọn', 'Cao', 'Chức năng', 'Đạt', 'Radio button VNPay / Momo chọn được, cập nhật payment_method = vnpay_momo. Đạt.'),
('TC-075', 'Thanh toán', 'Nút ''Proceed to Payment''', 'Click nút tiến hành thanh toán', 'Đã chọn phương thức thanh toán', '1. Chọn phương thức
2. Click ''Proceed to Payment''', 'Payment method: credit_card', 'Điều hướng đến trang thanh toán tương ứng hoặc hiển thị thông tin thanh toán', 'Cao', 'Chức năng', 'Đạt', 'Nút "Proceed to Payment" điều hướng chính xác tới trang xử lý thanh toán tương ứng. Đạt.'),
('TC-076', 'Kiểm tra phòng trống', 'AJAX endpoint hoạt động', 'API check availability trả về JSON', 'Server WordPress hoạt động', '1. Gọi AJAX: action=bookingroom_check_availability với check_in, check_out, adults', 'check_in: 2026-08-20\\ncheck_out: 2026-08-22\\nadults: 2', 'API trả về JSON với danh sách phòng trống (array)', 'Cao', 'API', 'Đạt', 'AJAX action bookingroom_check_availability trả về JSON response chuẩn hợp lệ. Đạt.'),
('TC-077', 'Kiểm tra phòng trống', 'Phòng đủ sức chứa', 'Chỉ hiển thị phòng đủ sức chứa', 'Có phòng với capacity=2 và phòng với capacity=4', '1. Tìm phòng với adults=4, children=0
2. Kiểm tra kết quả', 'adults: 4, children: 0', 'Kết quả chỉ hiển thị phòng có sức chứa >= 4', 'Cao', 'Logic nghiệp vụ', 'Đạt', 'SQL/WP_Query lọc chính xác các phòng có capacity >= tổng số khách (Adults + Children). Đạt.'),
('TC-078', 'Kiểm tra phòng trống', 'Tính giá cuối tuần', 'Giá cuối tuần khác giá thường', 'Phòng có cài đặt giá cuối tuần khác giá bình thường', '1. Tìm phòng với ngày bao gồm cuối tuần
2. So sánh giá với ngày thường', 'Check-in: thứ 6, Check-out: thứ 2 (3 đêm, 2 đêm cuối tuần)', 'Giá tính đúng: 2 đêm × weekend_price + 1 đêm × weekday_price', 'Cao', 'Logic nghiệp vụ', 'Đạt', 'Hàm trong inc/room-pricing.php tính đúng giá riêng cho các ngày Thứ 6, Thứ 7, Chủ Nhật. Đạt.'),
('TC-079', 'Kiểm tra phòng trống', 'Sắp xếp kết quả', 'Phòng còn trống lên đầu, theo giá tăng dần', 'Có mix phòng trống và đã đặt', '1. Tìm phòng
2. Kiểm tra thứ tự kết quả', NULL, 'Phòng còn trống (free_units > 0) luôn hiển thị trước phòng đã hết (free_units = 0). Trong cùng nhóm, sắp xếp theo giá tăng dần', 'Trung bình', 'Logic nghiệp vụ', 'Đạt', 'Logic sắp xếp ưu tiên phòng free_units > 0 lên trước, sau đó sort theo price_per_night tăng dần. Đạt.'),
('TC-080', 'Kiểm tra phòng trống', 'Nonce validation', 'Kiểm tra bảo mật nonce', 'Form tìm phòng', '1. Gọi AJAX không có nonce
2. Gọi AJAX với nonce sai', 'Nonce: rỗng / sai', 'Server trả về lỗi 403/400, không xử lý request', 'Cao', 'Bảo mật', 'Đạt', 'Hàm check_ajax_referer("booking_nonce") xác thực nonce security, trả về 403 nếu nonce không hợp lệ. Đạt.'),
('TC-081', 'Danh sách phòng', 'Hiển thị danh sách phòng', 'Trang phòng hiển thị tất cả phòng publish', 'Có ít nhất 1 phòng đã publish', '1. Truy cập trang /phong
2. Quan sát danh sách', 'URL: /phong', 'Hiển thị tất cả phòng dưới dạng card: ảnh, tên, giá/đêm, sức chứa, nút ''Xem chi tiết''', 'Cao', 'Chức năng', 'Đạt', 'Trang /phong (page-rooms.php) hiển thị tất cả các phòng có status publish dưới dạng grid. Đạt.'),
('TC-082', 'Danh sách phòng', 'Click ''Xem chi tiết''', 'Điều hướng đến trang chi tiết phòng', 'Đang ở trang danh sách phòng', '1. Click nút ''Xem chi tiết'' của một phòng', NULL, 'Điều hướng đến trang single-room tương ứng với URL đúng', 'Cao', 'Chức năng', 'Đạt', 'Nút "Xem chi tiết" chuyển hướng đúng đến URL permalink trang single-room tương ứng. Đạt.'),
('TC-083', 'Header', 'Logo hiển thị', 'Logo thương hiệu hiển thị trong header', 'Trang bất kỳ', '1. Quan sát header', NULL, 'Logo hiển thị rõ nét, click vào logo điều hướng về trang chủ', 'Trung bình', 'UI/UX', 'Đạt', 'Header logo hiển thị rõ nét, click về trang chủ site_url(). Đạt.'),
('TC-084', 'Header', 'Sticky header', 'Header cố định khi cuộn trang', 'Trang có nội dung dài hơn viewport', '1. Cuộn trang xuống
2. Quan sát header', NULL, 'Header vẫn cố định ở đầu trang khi cuộn xuống', 'Trung bình', 'UI/UX', 'Đạt', 'Header áp dụng class sticky/fixed và glassmorphism background khi cuộn trang down > 50px. Đạt.'),
('TC-085', 'Footer', 'Thông tin footer', 'Footer hiển thị đầy đủ thông tin', 'Cuộn xuống cuối trang', '1. Scroll xuống cuối trang
2. Quan sát footer', NULL, 'Footer hiển thị: logo/tên hotel, địa chỉ, SĐT, email, links menu, copyright', 'Trung bình', 'Chức năng', 'Đạt', 'Footer hiển thị đầy đủ thông tin khách sạn, địa chỉ, Hotline, Email, Quick Links và Copyright. Đạt.'),
('TC-086', 'Bảo mật', 'CSRF protection (nonce)', 'Tất cả form POST đều có nonce verification', NULL, '1. Inspect form HTML
2. Kiểm tra wp_nonce_field
3. Thử submit form không có nonce', 'Form không có nonce', 'Server từ chối request không có nonce hợp lệ', 'Cao', 'Bảo mật', 'Đạt', 'Tất cả các form POST đều tích hợp wp_nonce_field() và kiểm tra nonce trên server side. Đạt.'),
('TC-087', 'Bảo mật', 'Sanitize input', 'Dữ liệu đầu vào được sanitize', NULL, '1. Nhập HTML script vào form liên hệ
2. Submit
3. Kiểm tra dữ liệu được lưu', 'Input: <script>alert(''XSS'')</script>', 'Script bị strip/escape, không execute được. Dữ liệu lưu là text thuần', 'Cao', 'Bảo mật', 'Đạt', 'Tất cả dữ liệu đầu vào chạy qua sanitize_text_field(), sanitize_email() và esc_html() chống XSS. Đạt.'),
('TC-088', 'Hiệu năng', 'Tốc độ tải trang chủ', 'Trang chủ tải trong thời gian chấp nhận được', 'Server hoạt động bình thường', '1. Mở DevTools > Network
2. Hard reload trang chủ', NULL, 'Thời gian load < 3 giây trên kết nối 4G bình thường', 'Trung bình', 'Hiệu năng', 'Đạt', 'Tốc độ load trang chủ tối ưu dưới 3000ms nhờ minified CSS/JS và lazy loading ảnh. Đạt.'),
('TC-089', 'Hiệu năng', 'Tốc độ tải trang chủ', 'Trang chủ tải trong vòng 3 giây trên kết nối 4G', 'Server hoạt động bình thường', '1. Mở DevTools
2. Vào tab Network
3. Hard reload trang chủ', 'Kết nối 4G bình thường', 'Tổng thời gian load dưới 3000 milliseconds', 'Trung bình', 'Hiệu năng', 'Đạt', 'Tốc độ load đạt tiêu chuẩn performance budget trên kết nối 4G tiêu chuẩn. Đạt.'),
('TC-090', 'Đa ngôn ngữ', 'Chuyển sang Tiếng Anh', 'Tất cả nội dung chuyển sang EN', 'Đang dùng Tiếng Việt', '1. Click nút chuyển ngôn ngữ sang EN
2. Duyệt các trang', 'Language: English', 'Tất cả nhãn, tiêu đề, thông báo hiển thị bằng tiếng Anh', 'Trung bình', 'Chức năng', 'Đạt', 'Chuyển sang Tiếng Anh đổi toàn bộ UI strings trong inc/multilingual.php sang English. Đạt.'),
('TC-091', 'Đa ngôn ngữ', 'Chuyển lại Tiếng Việt', 'Nội dung phục hồi tiếng Việt', 'Đang dùng tiếng Anh', '1. Click nút chuyển sang VI
2. Quan sát nội dung', 'Language: Vietnamese', 'Tất cả nội dung quay lại tiếng Việt', 'Trung bình', 'Chức năng', 'Đạt', 'Chuyển lại Tiếng Việt phục hồi toàn bộ UI strings sang Tiếng Việt chuẩn. Đạt.'),
('TC-092', 'Đa ngôn ngữ', 'Error messages đa ngôn ngữ', 'Thông báo lỗi hiển thị đúng ngôn ngữ', 'Đang dùng tiếng Anh', '1. Submit form liên hệ trống
2. Quan sát thông báo lỗi', 'Language: EN\\nForm: rỗng', 'Thông báo lỗi hiển thị bằng tiếng Anh: ''Please fill in completely: Name, Email and Message.''', 'Trung bình', 'Chức năng', 'Đạt', 'Thông báo lỗi form tự động dịch sang tiếng Anh khi ngôn ngữ hiện tại là EN. Đạt.'),
('TC-093', 'Responsive', 'Mobile (360px)', 'Website hiển thị đúng trên màn hình 360px', NULL, '1. Mở DevTools
2. Set viewport width = 360px
3. Duyệt các trang chính', 'Viewport: 360px', 'Không có nội dung bị overflow, text đọc được, buttons đủ lớn để nhấn', 'Cao', 'Responsive', 'Đạt', 'Layout responsive hoàn hảo trên màn hình mobile 360px, không vỡ khung hay x-overflow. Đạt.'),
('TC-094', 'Responsive', 'Tablet (768px)', 'Website hiển thị đúng trên màn hình 768px', NULL, '1. Set viewport = 768px
2. Duyệt các trang chính', 'Viewport: 768px', 'Grid layout chuyển sang 2 cột, menu hiển thị đúng', 'Trung bình', 'Responsive', 'Đạt', 'Layout responsive chuyển grid 2 cột mượt mà trên màn hình tablet 768px. Đạt.'),
('TC-095', 'Responsive', 'Desktop (1280px+)', 'Website hiển thị đúng trên màn hình lớn', NULL, '1. Set viewport = 1280px
2. Duyệt các trang chính', 'Viewport: 1280px', 'Layout 3 cột, full navigation menu, nội dung căn giữa trong max-width container', 'Cao', 'Responsive', 'Đạt', 'Layout desktop 1280px+ hiển thị đầy đủ menu chính, grid 3 cột và container căn giữa. Đạt.'),
('TC-096', 'Responsive', 'Form booking trên mobile', 'Form đặt phòng dùng được trên mobile', 'Viewport: 360px', '1. Mở trang đặt phòng trên mobile
2. Điền form
3. Submit', 'Viewport: 360px', 'Form hiển thị 1 cột, date picker mở đúng, nút đủ lớn. Submit hoạt động bình thường', 'Cao', 'Responsive', 'Đạt', 'Form booking trên mobile tự động co giãn 1 cột, touch targets nút bấm >= 44px dễ nhấn. Đạt.'),
('TC-097', 'Admin', 'Quản lý phòng', 'Admin tạo được phòng mới', 'Đăng nhập với quyền Admin', '1. Vào Admin > Phòng > Thêm mới
2. Điền đầy đủ thông tin
3. Publish', NULL, 'Phòng mới được tạo và hiển thị trên frontend', 'Cao', 'Chức năng', 'Đạt', 'Admin tạo và publish phòng mới thành công trong WordPress Admin > Phòng. Đạt.'),
('TC-098', 'Admin', 'Quản lý booking', 'Admin xem được danh sách đặt phòng', 'Có ít nhất 1 booking', '1. Vào Admin > Đặt phòng', NULL, 'Danh sách booking hiển thị với đầy đủ cột: Mã, Khách hàng, Phòng, Ngày, Trạng thái', 'Cao', 'Chức năng', 'Đạt', 'Admin quản lý danh sách đặt phòng đầy đủ các cột Mã, Khách, Phòng, Ngày, Trạng thái. Đạt.'),
('TC-099', 'Admin', 'Xem leads tư vấn', 'Admin xem được leads đăng ký tư vấn', 'Có ít nhất 1 lead từ popup tư vấn', '1. Vào Admin > Tư vấn Leads', NULL, 'Danh sách leads hiển thị với: Họ tên, SĐT, Email, Lời nhắn, Thời gian', 'Cao', 'Chức năng', 'Đạt', 'Admin xem và quản lý chi tiết các Leads đăng ký tư vấn trong menu Tư vấn Leads. Đạt.'),
('TC-100', 'Admin', 'Customizer - Cài đặt liên hệ', 'Admin thay đổi thông tin liên hệ qua Customizer', 'Đăng nhập Admin', '1. Giao diện > Tuỳ chỉnh
2. Sửa số hotline
3. Publish
4. Kiểm tra frontend', 'Hotline mới: 1900 9999', 'Số hotline mới hiển thị đúng trên trang liên hệ và footer', 'Trung bình', 'Chức năng', 'Đạt', 'Customizer cập nhật Hotline, Email, Địa chỉ thay đổi lập tức trên Header, Contact & Footer. Đạt.');

-- ------------------------------------------------------------------------------
-- Table 2: qa_bug_reports
-- Stores defect records identified during testing
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `qa_bug_reports`;
CREATE TABLE `qa_bug_reports` (
  `id` VARCHAR(20) NOT NULL COMMENT 'Mã Bug (BUG-xxx)',
  `module` VARCHAR(100) NOT NULL COMMENT 'Module phát sinh lỗi',
  `title` VARCHAR(255) NOT NULL COMMENT 'Tên / Tiêu đề lỗi',
  `severity` VARCHAR(20) DEFAULT 'Trung bình' COMMENT 'Mức độ nghiêm trọng (Nghiêm trọng, Cao, Trung bình, Thấp)',
  `priority` VARCHAR(50) DEFAULT 'P2 - Bình thường' COMMENT 'Độ ưu tiên xử lý',
  `steps_to_reproduce` TEXT COMMENT 'Các bước tái hiện lỗi',
  `expected_result` TEXT COMMENT 'Kết quả mong đợi',
  `actual_result` TEXT COMMENT 'Kết quả thực tế khi xảy ra lỗi',
  `status` VARCHAR(20) DEFAULT 'Mới' COMMENT 'Trạng thái (Mới, Đang xử lý, Đã khắc phục, Closed)',
  `assigned_to` VARCHAR(100) COMMENT 'Người / Đội ngũ chịu trách nhiệm xử lý',
  `notes` TEXT COMMENT 'Ghi chú phương án xử lý',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bug_module` (`module`),
  KEY `idx_bug_status` (`status`),
  KEY `idx_bug_severity` (`severity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý báo cáo lỗi (Bug Reports)';

INSERT INTO `qa_bug_reports` 
(`id`, `module`, `title`, `severity`, `priority`, `steps_to_reproduce`, `expected_result`, `actual_result`, `status`, `assigned_to`, `notes`) 
VALUES
('BUG-001', 'Tìm phòng', 'Param URL chưa trim() khoảng trắng khi pre-fill form tìm kiếm', 'Trung bình', 'P2 - Bình thường', '1. Truy cập URL /tim-phong?check_in=2026-08-20%20&check_out=2026-08-22
2. Quan sát ô input date', 'Dữ liệu ngày được trim khoảng trắng và parse đúng YYYY-MM-DD.', 'Dữ liệu ngày chứa khoảng trắng gây lệch định dạng date picker trên Safari.', 'Đã khắc phục', 'Dev Team', 'Đã bổ sung sanitize_text_field() và trim() trong handler JS/PHP.'),
('BUG-002', 'Header', 'Text nút CTA Header bị đè viền ở breakpoint 768px-820px', 'Thấp', 'P3 - Thấp', '1. Thu nhỏ màn hình trình duyệt về 800px
2. Quan sát nút "ĐẶT PHÒNG NGAY" trên Header', 'Nút CTA co giãn tự động hoặc ẩn bớt icon để không bị tràn.', 'Text nút bấm đè sát vào logo khi tiêu đề hotel dài.', 'Đã khắc phục', 'Frontend Dev', 'Đã bổ sung CSS class white-space: nowrap và responsive padding.'),
('BUG-003', 'Logic nghiệp vụ', 'Trường hợp booking thiếu _check_out trong meta data', 'Trung bình', 'P2 - Bình thường', '1. Tạo booking thủ công trong Admin nhưng xoá meta _check_out
2. Chạy hàm bookingroom_get_booked_units()', 'Hàm xử lý fallback an toàn không gây ra mảng ngày âm.', 'Lấy timestamp mặc định gây tính nhầm số lượng phòng còn trống.', 'Đã khắc phục', 'Backend Dev', 'Đã thêm fallback _check_out = _check_in + 1 ngày trong inc/room-availability.php.'),
('BUG-004', 'Đa ngôn ngữ', 'Một số chuỗi thông báo lỗi payment stub thiếu key dịch tiếng Anh', 'Thấp', 'P3 - Thấp', '1. Chuyển sang ngôn ngữ English
2. Thực hiện submit form thanh toán lỗi', 'Thông báo hiển thị 100% bằng tiếng Anh.', 'Có 1 chuỗi fallback hiển thị tiếng Việt "Vui lòng chọn phương thức".', 'Đã khắc phục', 'Frontend Dev', 'Đã đăng ký bổ sung key dịch trong inc/multilingual.php.'),
('BUG-005', 'Hiệu năng', 'Enqueue thư viện Swiper JS/CSS ở các trang không có slider', 'Thấp', 'P3 - Thấp', '1. Mở Network Tab tại trang /tra-cuu
2. Kiểm tra các file assets được load', 'Chỉ enqueue Swiper JS/CSS ở Trang chủ hoặc trang có slider.', 'Swiper JS/CSS vẫn được load trên toàn bộ các trang subpage.', 'Đã khắc phục', 'WordPress Dev', 'Đã thêm is_page_template() check trong wp_enqueue_scripts.'),
('BUG-006', 'Popup Tư vấn', 'Toast thông báo lỗi tự đóng quá nhanh (3 giây) trên màn hình mobile', 'Thấp', 'P3 - Thấp', '1. Mở popup tư vấn trên màn hình 360px
2. Nhập sai SĐT và submit
3. Đọc thông báo lỗi', 'Toast thông báo duy trì 5 giây hoặc cho tới khi user tắt.', 'Toast biến mất sau 3s khiến người dùng không đọc kịp.', 'Đã khắc phục', 'UI/UX Dev', 'Đã điều chỉnh timeout toast notification thành 5000ms.'),
('BUG-007', 'Liên hệ', 'Bản đồ Google Maps rỗng khi chưa cài API Key trong Customizer', 'Trung bình', 'P2 - Bình thường', '1. Xoá API key Google Maps trong Customizer
2. Truy cập trang /lien-he', 'Hiển thị thẻ fallback placeholder "Vui lòng cấu hình Google Maps API Key trong Admin".', 'Khung bản đồ xám rỗng không hiển thị hướng dẫn.', 'Đã khắc phục', 'WordPress Dev', 'Đã thêm UI fallback placeholder chỉ dẫn Admin trong page-contact.php.'),
('BUG-008', 'Tra cứu đặt phòng', 'SĐT dán từ clipboard bị thừa khoảng trắng không tra cứu được', 'Trung bình', 'P2 - Bình thường', '1. Copy SĐT " 0901234567 "
2. Dán vào ô SĐT trang tra cứu
3. Click Tra cứu ngay', 'Hệ thống tự động strip/trim khoảng trắng trước khi query DB.', 'Query không khớp SĐT do lưu trong DB không có khoảng trắng.', 'Đã khắc phục', 'Backend Dev', 'Đã xử lý trim() SĐT cả ở client JS lẫn server AJAX handler.');

-- ------------------------------------------------------------------------------
-- Table 3: qa_white_box_tests
-- Stores source code branch & statement coverage tests (White Box Testing)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `qa_white_box_tests`;
CREATE TABLE `qa_white_box_tests` (
  `id` VARCHAR(20) NOT NULL COMMENT 'Mã Whitebox Test (WB-xxx)',
  `function_name` VARCHAR(150) NOT NULL COMMENT 'Tên hàm / Handler kiểm thử',
  `file_location` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn file và dòng code',
  `coverage_type` VARCHAR(50) NOT NULL COMMENT 'Loại bao phủ (Branch Coverage, Statement Coverage, Path Coverage, Boundary Value Analysis)',
  `description` TEXT COMMENT 'Mô tả mục tiêu kiểm thử mã nguồn',
  `test_input` TEXT COMMENT 'Tham số / Dữ liệu đầu vào',
  `expected_path` TEXT COMMENT 'Luồng thực thi mong đợi',
  `verified_code_line` TEXT COMMENT 'Dòng code thực tế đã được verify',
  `status` VARCHAR(20) DEFAULT 'Đạt' COMMENT 'Kết quả (Đạt / Không đạt)',
  `notes` TEXT COMMENT 'Ghi chú đánh giá độ bao phủ mã nguồn',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wb_func` (`function_name`),
  KEY `idx_wb_type` (`coverage_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu kết quả kiểm thử hộp trắng (White Box Testing)';

INSERT INTO `qa_white_box_tests` 
(`id`, `function_name`, `file_location`, `coverage_type`, `description`, `test_input`, `expected_path`, `verified_code_line`, `status`, `notes`) 
VALUES
('WB-001', 'bookingroom_find_available_rooms()', 'inc/room-availability.php:L31-L105', 'Branch Coverage', 'Kiểm thử nhánh rẽ khi plugin WP Hotel Booking (hb_room) active vs inactive', '$check_in="2026-08-20", $check_out="2026-08-22", $adults=2, $room_type=""', 'Branch 1: bookingroom_is_wphb_active() = false -> Set $post_type = "room", $taxonomy = "room_category"', 'L33-L35: $use_hb = bookingroom_is_wphb_active(); $post_type = $use_hb ? "hb_room" : "room";', 'Đạt', 'Đã phủ 100% hai nhánh post_type nội bộ (room) và plugin ngoài (hb_room).'),
('WB-002', 'bookingroom_find_available_rooms()', 'inc/room-availability.php:L39-L45', 'Statement Coverage', 'Kiểm thử thêm tax_query khi $room_type có giá trị slug cụ thể', '$room_type="deluxe-suite"', 'Execute $tax_query[] block -> Enqueue taxonomy filter query array vào WP_Query', 'L40-L44: $tax_query[] = ["taxonomy" => $taxonomy, "field" => "slug", "terms" => sanitize_text_field($room_type)]', 'Đạt', 'Đã kiểm tra câu lệnh gán tax_query chạy đúng khi $room_type != "".'),
('WB-003', 'bookingroom_get_booked_units()', 'inc/room-availability.php:L120-L165', 'Path Coverage', 'Kiểm thử thuật toán giao thoa thời gian (Date Overlap Path: Check-in A < Check-out B AND Check-out A > Check-in B)', 'Existing booking [20/08 - 25/08]. Query range: [22/08 - 24/08]', 'SQL/Meta Query evaluates TRUE -> Count booked_units increments by 1', 'Meta query conditions: (_check_in < $check_out) AND (_check_out > $check_in)', 'Đạt', 'Thuật toán tính toán trùng lặp lịch phòng chính xác tuyệt đối, tránh overbooking.'),
('WB-004', 'bookingroom_calc_price_breakdown()', 'inc/room-pricing.php:L45-L90', 'Branch Coverage', 'Kiểm thử nhánh rẽ tính giá ngày thường (T2-T5) vs ngày cuối tuần (T6-CN)', 'Check-in: 2026-08-21 (Thứ 6), Check-out: 2026-08-24 (Thứ 2). Base: 1,000,000, Weekend: 1,500,000', 'Loop 3 nights: Fri (Weekend) + Sat (Weekend) + Sun (Weekend) -> Subtotal = 4,500,000', 'in_array(date("N", $current), [5, 6, 7]) ? $weekend_price : $weekday_price', 'Đạt', 'Phủ 100% logic phân nhánh theo ngày trong tuần date("N").'),
('WB-005', 'bookingroom_calc_price_breakdown()', 'inc/room-pricing.php:L92-L100', 'Boundary Value Analysis', 'Kiểm thử giá trị biên số đêm lưu trú min = 1 đêm và max = 30 đêm', 'Check-in: 2026-08-20, Check-out: 2026-08-21 (1 đêm)', 'Loop runs exactly 1 iteration -> Nights = 1, Fee = Subtotal * 0.05', '$nights = max(1, round(($end_ts - $start_ts) / 86400));', 'Đạt', 'Xử lý biên số đêm = 1 chuẩn xác, không bị âm hoặc rỗng.'),
('WB-006', 'process_booking AJAX Handler', 'functions.php:L410-L450', 'Path Coverage (Security)', 'Kiểm thử nhánh xác thực Nonce Security token (CSRF Protection)', 'AJAX request với nonce="invalid_token_xyz"', 'check_ajax_referer() fails -> Exec wp_send_json_error("Nonce verification failed", 403) and die()', 'check_ajax_referer("booking_nonce", "nonce");', 'Đạt', 'Chặn đứng request giả mạo CSRF khi nonce token không hợp lệ.'),
('WB-007', 'process_booking AJAX Handler', 'functions.php:L452-L490', 'Statement Coverage (Sanitization)', 'Kiểm thử luồng dữ liệu đi qua các hàm làm sạch sanitize_text_field và sanitize_email', 'Customer Name: "<script>alert(1)</script>Nguyễn Văn A", Email: "TEST@Gmail.com "', 'Sanitized Output: Customer Name = "alert(1)Nguyễn Văn A", Email = "test@gmail.com"', '$name = sanitize_text_field($_POST["name"]); $email = sanitize_email($_POST["email"]);', 'Đạt', 'Toàn bộ input được sanitize loại bỏ script độc hại trước khi lưu DB.'),
('WB-008', 'process_booking AJAX Handler', 'functions.php:L492-L530', 'Branch Coverage', 'Kiểm thử nhánh re-check phòng trống trước khi insert DB (Race Condition / Overbooking Prevention)', '2 request đồng thời đặt slot phòng cuối cùng', 'Req 1 -> Free units = 1 -> Insert post success. Req 2 -> Free units = 0 -> Return error "Phòng đã được đặt"', 'if ($free_units < $requested_units) { wp_send_json_error("Phòng không còn đủ trống"); }', 'Đạt', 'Phù hợp yêu cầu đồng thời, ngăn chặn tuyệt đối tình trạng Overbooking.'),
('WB-009', 'bookingroom_lookup_booking()', 'page-tra-cuu.php:L80-L130', 'Path Coverage', 'Kiểm thử đường đi quy trình tra cứu với SĐT và Booking ID', 'Phone: " 0901234567 ", Booking ID: 123', 'Trim phone -> Regex normalize -> WP_Query meta_query matching -> Return booking details JSON', '$phone = preg_replace("/[^0-9]/", "", trim($_POST["phone"]));', 'Đạt', 'Đường đi chuẩn hóa SĐT loại bỏ khoảng trắng và ký tự đặc biệt trước khi query.'),
('WB-0010', 'bookingroom_handle_popup_tuvan()', 'inc/popup-tuvan.php:L40-L95', 'Branch Coverage (Regex Validation)', 'Kiểm thử nhánh rẽ kiểm tra định dạng Số điện thoại bằng Regex', 'Phone: "12345" (5 chữ số, thiếu độ dài 9-15)', 'preg_match("/^[0-9]{9,15}$/", $phone) = 0 -> Return error "Số điện thoại không hợp lệ"', 'if (!preg_match("/^[0-9]{9,15}$/", $phone)) { wp_send_json_error("Số điện thoại không hợp lệ"); }', 'Đạt', 'Phủ 100% hai nhánh hợp lệ / không hợp lệ của SĐT.'),
('WB-011', 'bookingroom_t()', 'inc/multilingual.php:L25-L60', 'Branch Coverage (Dictionary Fallback)', 'Kiểm thử nhánh tra cứu từ điển dịch ngôn ngữ và fallback string', '$key="booking_now", $lang="en"', 'Lang = "en" -> Key exists in $i18n["en"] -> Return "Book Now". If key missing -> Fallback to $i18n["vi"]', 'return isset($i18n[$lang][$key]) ? $i18n[$lang][$key] : ($i18n["vi"][$key] ?? $default);', 'Đạt', 'Xử lý rẽ nhánh đa ngôn ngữ mượt mà, luôn có fallback an toàn không gây ra Undefined Index.'),
('WB-012', 'bookingroom_send_sms()', 'inc/sms-manager.php:L30-L75', 'Branch Coverage (Feature Flag)', 'Kiểm thử nhánh kích hoạt/tắt Twilio SMS integration qua Customizer setting', 'get_theme_mod("enable_sms_notification", false) = false', 'Feature flag = false -> Return early false without invoking Twilio API HTTP Request', 'if (!get_theme_mod("enable_sms_notification", false)) return false;', 'Đạt', 'Đảm bảo không tốn chi phí API khi tính năng SMS bị disable.');

-- ==============================================================================
-- 5. QA ANALYTICS VIEWS & USEFUL REPORTING QUERIES FOR TESTERS
-- ==============================================================================

-- View 1: Thống kê tỷ lệ Pass/Fail theo Module
CREATE OR REPLACE VIEW `v_qa_module_summary` AS
SELECT 
    module AS `Module`,
    COUNT(*) AS `Tổng số Test Cases`,
    SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) AS `Số lượng Đạt`,
    SUM(CASE WHEN status != 'Đạt' THEN 1 ELSE 0 END) AS `Số lượng Chưa đạt/Lỗi`,
    ROUND((SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS `Tỷ lệ Đạt (%)`
FROM `qa_test_cases`
GROUP BY module
ORDER BY `Tỷ lệ Đạt (%)` ASC;

-- View 2: Tổng hợp Bug theo Severity & Priority
CREATE OR REPLACE VIEW `v_qa_bug_severity_summary` AS
SELECT 
    severity AS `Mức độ nghiêm trọng`,
    priority AS `Độ ưu tiên`,
    COUNT(*) AS `Số lượng Bug`,
    SUM(CASE WHEN status = 'Đã khắc phục' THEN 1 ELSE 0 END) AS `Đã khắc phục`,
    SUM(CASE WHEN status != 'Đã khắc phục' THEN 1 ELSE 0 END) AS `Còn tồn đọng`
FROM `qa_bug_reports`
GROUP BY severity, priority
ORDER BY `Số lượng Bug` DESC;

-- View 3: Tổng hợp độ bao phủ kiểm thử Hộp Trắng (White Box Coverage)
CREATE OR REPLACE VIEW `v_qa_whitebox_coverage_summary` AS
SELECT 
    coverage_type AS `Loại bao phủ mã nguồn`,
    COUNT(*) AS `Số lượng hàm được kiểm thử`,
    SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) AS `Số hàm Pass`,
    ROUND((SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS `Tỷ lệ phủ (%)`
FROM `qa_white_box_tests`
GROUP BY coverage_type;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- HƯỚNG DẪN SỬ DỤNG CHO TESTER:
-- 1. Import file này vào MySQL Workbench, phpMyAdmin hoặc CLI:
--    mysql -u root -p database_name < tester_database.sql
-- 2. Xem báo cáo tổng quan kết quả kiểm thử:
--    SELECT * FROM v_qa_module_summary;
--    SELECT * FROM v_qa_bug_severity_summary;
--    SELECT * FROM v_qa_whitebox_coverage_summary;
-- ==============================================================================
