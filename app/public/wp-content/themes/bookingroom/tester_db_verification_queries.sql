-- ==============================================================================
-- SQL VERIFICATION & SYSTEM AUDIT QUERIES FOR DATABASE TESTER (WORDPRESS / BOOKING ROOM)
-- Dành riêng cho QA / Database Tester kiểm thử dữ liệu trực tiếp trong CSDL WordPress
-- ==============================================================================

SET NAMES utf8mb4;

-- ------------------------------------------------------------------------------
-- QUERY 1: KIỂM TRA TRÙNG LỊCH PHÒNG (OVERBOOKING / DATE OVERLAP CONFLICTS)
-- Mục tiêu: Phát hiện các đơn đặt phòng bị trùng lặp thời gian trên cùng 1 phòng vật lý.
-- Thuật toán giao thoa: (Check-in A < Check-out B) AND (Check-out A > Check-in B)
-- ------------------------------------------------------------------------------
SELECT 
    b1.ID AS `Booking_ID_1`,
    m_name1.meta_value AS `Khach_1`,
    m_room1.meta_value AS `Room_ID`,
    p_room.post_title AS `Ten_Phong`,
    m_in1.meta_value AS `CheckIn_1`,
    m_out1.meta_value AS `CheckOut_1`,
    b2.ID AS `Booking_ID_2`,
    m_name2.meta_value AS `Khach_2`,
    m_in2.meta_value AS `CheckIn_2`,
    m_out2.meta_value AS `CheckOut_2`
FROM wp_posts b1
INNER JOIN wp_posts b2 ON b1.ID < b2.ID AND b1.post_type = 'booking' AND b2.post_type = 'booking'
INNER JOIN wp_postmeta m_room1 ON b1.ID = m_room1.post_id AND m_room1.meta_key = '_room_id'
INNER JOIN wp_postmeta m_room2 ON b2.ID = m_room2.post_id AND m_room2.meta_key = '_room_id' AND m_room1.meta_value = m_room2.meta_value
INNER JOIN wp_postmeta m_in1   ON b1.ID = m_in1.post_id   AND m_in1.meta_key   = '_check_in'
INNER JOIN wp_postmeta m_out1  ON b1.ID = m_out1.post_id  AND m_out1.meta_key  = '_check_out'
INNER JOIN wp_postmeta m_in2   ON b2.ID = m_in2.post_id   AND m_in2.meta_key   = '_check_in'
INNER JOIN wp_postmeta m_out2  ON b2.ID = m_out2.post_id  AND m_out2.meta_key  = '_check_out'
LEFT JOIN wp_postmeta m_name1  ON b1.ID = m_name1.post_id AND m_name1.meta_key = '_customer_name'
LEFT JOIN wp_postmeta m_name2  ON b2.ID = m_name2.post_id AND m_name2.meta_key = '_customer_name'
LEFT JOIN wp_posts p_room      ON m_room1.meta_value = p_room.ID
WHERE b1.post_status IN ('publish', 'confirmed', 'pending')
  AND b2.post_status IN ('publish', 'confirmed', 'pending')
  AND (m_in1.meta_value < m_out2.meta_value AND m_out1.meta_value > m_in2.meta_value);


-- ------------------------------------------------------------------------------
-- QUERY 2: AUDIT DỮ LIỆU THIẾU (ORPHAN / MISSING BOOKING META DATA)
-- Mục tiêu: Tìm các đơn đặt phòng thiếu meta bắt buộc (_check_in, _check_out, _room_id, _customer_phone)
-- ------------------------------------------------------------------------------
SELECT 
    p.ID AS `Booking_ID`,
    p.post_title AS `Tieu_De_Booking`,
    p.post_date AS `Ngay_Tao`,
    m_in.meta_value AS `Check_In`,
    m_out.meta_value AS `Check_Out`,
    m_room.meta_value AS `Room_ID`,
    m_phone.meta_value AS `Phone`
FROM wp_posts p
LEFT JOIN wp_postmeta m_in    ON p.ID = m_in.post_id    AND m_in.meta_key    = '_check_in'
LEFT JOIN wp_postmeta m_out   ON p.ID = m_out.post_id   AND m_out.meta_key   = '_check_out'
LEFT JOIN wp_postmeta m_room  ON p.ID = m_room.post_id  AND m_room.meta_key  = '_room_id'
LEFT JOIN wp_postmeta m_phone ON p.ID = m_phone.post_id AND m_phone.meta_key = '_customer_phone'
WHERE p.post_type = 'booking'
  AND (m_in.meta_value IS NULL 
    OR m_out.meta_value IS NULL 
    OR m_room.meta_value IS NULL 
    OR m_phone.meta_value IS NULL);


-- ------------------------------------------------------------------------------
-- QUERY 3: KIỂM TRA SỐ ĐIỆN THOẠI KHÔNG CHUẨN HOẶC DỰ THỪA KHOẢNG TRẮNG
-- Mục tiêu: Phát hiện SĐT bị dán thừa khoảng trắng hoặc chứa ký tự đặc biệt gây lỗi tra cứu.
-- ------------------------------------------------------------------------------
SELECT 
    p.ID AS `Booking_ID`,
    m_name.meta_value AS `Ten_Khach`,
    m_phone.meta_value AS `SDT_Goc`,
    TRIM(m_phone.meta_value) AS `SDT_Da_Trim`,
    CASE 
        WHEN m_phone.meta_value LIKE ' %' OR m_phone.meta_value LIKE '% ' THEN 'Có khoảng trắng thừa'
        WHEN m_phone.meta_value REGEXP '[^0-9\+\-\. ]' THEN 'Chứa ký tự không hợp lệ'
        WHEN LENGTH(REGEXP_REPLACE(m_phone.meta_value, '[^0-9]', '')) NOT BETWEEN 9 AND 15 THEN 'Độ dài SĐT sai'
        ELSE 'Hợp lệ'
    END AS `Trang_Thai_Kiem_Tra`
FROM wp_posts p
INNER JOIN wp_postmeta m_phone ON p.ID = m_phone.post_id AND m_phone.meta_key IN ('_customer_phone', '_phone')
LEFT JOIN wp_postmeta m_name   ON p.ID = m_name.post_id  AND m_name.meta_key  IN ('_customer_name', '_name')
WHERE p.post_type = 'booking';


-- ------------------------------------------------------------------------------
-- QUERY 4: KIỂM TRA SỨC CHỨA VƯỢT QUÁ GIỚI HẠN (ROOM CAPACITY EXCEEDED)
-- Mục tiêu: Tìm các booking có tổng số người lớn + trẻ em lớn hơn sức chứa phòng (_capacity)
-- ------------------------------------------------------------------------------
SELECT 
    b.ID AS `Booking_ID`,
    p_room.post_title AS `Ten_Phong`,
    CAST(COALESCE(m_cap.meta_value, 2) AS UNSIGNED) AS `Suc_Chua_Max`,
    CAST(COALESCE(m_adults.meta_value, 1) AS UNSIGNED) AS `Nguoi_Lon`,
    CAST(COALESCE(m_children.meta_value, 0) AS UNSIGNED) AS `Tre_Em`,
    (CAST(COALESCE(m_adults.meta_value, 1) AS UNSIGNED) + CAST(COALESCE(m_children.meta_value, 0) AS UNSIGNED)) AS `Tong_So_Khach`
FROM wp_posts b
INNER JOIN wp_postmeta m_room     ON b.ID = m_room.post_id     AND m_room.meta_key = '_room_id'
LEFT JOIN wp_posts p_room         ON m_room.meta_value = p_room.ID
LEFT JOIN wp_postmeta m_cap       ON p_room.ID = m_cap.post_id AND m_cap.meta_key  = '_capacity'
LEFT JOIN wp_postmeta m_adults    ON b.ID = m_adults.post_id   AND m_adults.meta_key = '_adults'
LEFT JOIN wp_postmeta m_children  ON b.ID = m_children.post_id AND m_children.meta_key = '_children'
WHERE b.post_type = 'booking'
HAVING `Tong_So_Khach` > `Suc_Chua_Max`;


-- ------------------------------------------------------------------------------
-- QUERY 5: TRUY VẤN TÍNH PHÒNG TRỐNG THỜI GIAN THỰC (REAL-TIME AVAILABILITY QUERY)
-- Tham số giả định: Check-in = '2026-08-20', Check-out = '2026-08-22'
-- ------------------------------------------------------------------------------
SELECT 
    r.ID AS `Room_ID`,
    r.post_title AS `Ten_Phong`,
    CAST(COALESCE(m_units.meta_value, 1) AS UNSIGNED) AS `Tong_So_Phong_Vat_Ly`,
    COALESCE(booked.total_booked, 0) AS `Số_Phong_Da_Dat`,
    (CAST(COALESCE(m_units.meta_value, 1) AS UNSIGNED) - COALESCE(booked.total_booked, 0)) AS `Số_Phong_Con_Trong`
FROM wp_posts r
LEFT JOIN wp_postmeta m_units ON r.ID = m_units.post_id AND m_units.meta_key = '_total_units'
LEFT JOIN (
    SELECT 
        m_r.meta_value AS room_id,
        COUNT(DISTINCT b.ID) AS total_booked
    FROM wp_posts b
    INNER JOIN wp_postmeta m_r  ON b.ID = m_r.post_id  AND m_r.meta_key  = '_room_id'
    INNER JOIN wp_postmeta m_in ON b.ID = m_in.post_id AND m_in.meta_key = '_check_in'
    INNER JOIN wp_postmeta m_out ON b.ID = m_out.post_id AND m_out.meta_key = '_check_out'
    WHERE b.post_type = 'booking'
      AND b.post_status IN ('publish', 'confirmed', 'pending')
      AND (m_in.meta_value < '2026-08-22' AND m_out.meta_value > '2026-08-20')
    GROUP BY m_r.meta_value
) booked ON r.ID = booked.room_id
WHERE r.post_type IN ('room', 'hb_room')
  AND r.post_status = 'publish';


-- ------------------------------------------------------------------------------
-- QUERY 6: THỐNG KÊ DOANH THU & TRẠNG THÁI BOOKING DÀNH CHO REPORT
-- ------------------------------------------------------------------------------
SELECT 
    b.post_status AS `Trang_Thai_Booking`,
    COUNT(b.ID) AS `Tong_So_Don`,
    SUM(CAST(COALESCE(m_price.meta_value, 0) AS DECIMAL(12,2))) AS `Tong_Gia_Tri_VND`
FROM wp_posts b
LEFT JOIN wp_postmeta m_price ON b.ID = m_price.post_id AND m_price.meta_key IN ('_total_price', '_price')
WHERE b.post_type = 'booking'
GROUP BY b.post_status;


-- ------------------------------------------------------------------------------
-- QUERY 7: TRA CỨU BOOKING THEO SỐ ĐIỆN THOẠI (TEST SIMULATION HÀM TRA CỨU)
-- Nhập SĐT test: '0901234567'
-- ------------------------------------------------------------------------------
SELECT 
    b.ID AS `Ma_Dat_Phong`,
    m_name.meta_value AS `Khach_Hang`,
    m_phone.meta_value AS `So_Dien_Thoai`,
    p_room.post_title AS `Phong_Da_Dat`,
    m_in.meta_value AS `Ngay_Nhan_Phong`,
    m_out.meta_value AS `Ngay_Tra_Phong`,
    m_total.meta_value AS `Tong_Tien_VND`,
    b.post_status AS `Trang_Thai`
FROM wp_posts b
INNER JOIN wp_postmeta m_phone ON b.ID = m_phone.post_id AND m_phone.meta_key IN ('_customer_phone', '_phone')
LEFT JOIN wp_postmeta m_name  ON b.ID = m_name.post_id  AND m_name.meta_key  IN ('_customer_name', '_name')
LEFT JOIN wp_postmeta m_room  ON b.ID = m_room.post_id  AND m_room.meta_key  = '_room_id'
LEFT JOIN wp_posts p_room     ON m_room.meta_value = p_room.ID
LEFT JOIN wp_postmeta m_in    ON b.ID = m_in.post_id    AND m_in.meta_key    = '_check_in'
LEFT JOIN wp_postmeta m_out   ON b.ID = m_out.post_id   AND m_out.meta_key   = '_check_out'
LEFT JOIN wp_postmeta m_total ON b.ID = m_total.post_id AND m_total.meta_key = '_total_price'
WHERE b.post_type = 'booking'
  AND REGEXP_REPLACE(m_phone.meta_value, '[^0-9]', '') = '0901234567';


-- ------------------------------------------------------------------------------
-- QUERY 8: DỌN DẸP DỮ LIỆU KIỂM THỬ (CLEANUP TEST BOOKINGS AFTER TESTING)
-- Cảnh báo: Chỉ chạy trong môi trường STAGING / TESTING!
-- ------------------------------------------------------------------------------
-- DELETE p, pm FROM wp_posts p 
-- LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id 
-- WHERE p.post_type = 'booking' AND p.post_title LIKE '%Test%';
