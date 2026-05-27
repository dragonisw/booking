<?php
/**
 * Room Pricing Helper Functions
 * Xét điều kiện giá phòng cuối tuần (Thứ 6, Thứ 7, Chủ Nhật) và trong tuần
 *
 * Cuối tuần = Thứ 6 (Friday), Thứ 7 (Saturday), Chủ Nhật (Sunday)
 * Trong tuần = Thứ 2 - Thứ 5 (Monday - Thursday)
 *
 * @package BookingRoom
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Kiểm tra một ngày có phải là cuối tuần không.
 * Cuối tuần: Thứ 6 (5), Thứ 7 (6), Chủ Nhật (0)
 *
 * @param  string|DateTime $date  Ngày cần kiểm tra (YYYY-MM-DD hoặc DateTime)
 * @return bool  true nếu là cuối tuần
 */
function bookingroom_is_weekend($date) {
    if (!($date instanceof DateTime)) {
        $date = new DateTime($date);
    }
    // 0 = Sunday, 5 = Friday, 6 = Saturday
    $dow = (int) $date->format('w');
    return in_array($dow, [0, 5, 6], true);
}

/**
 * Lấy giá phòng đúng theo ngày (cuối tuần hoặc trong tuần).
 *
 * @param  int    $post_id      ID của post phòng
 * @param  string $date         Ngày check-in (YYYY-MM-DD). Nếu null → trả về giá trong tuần mặc định.
 * @param  bool   $use_checkin  Nếu true → dùng ngày check-in để xác định loại giá.
 * @return int    Giá phòng theo đêm (VNĐ)
 */
function bookingroom_get_room_price_for_date($post_id, $date = null) {
    $weekday_price  = (int) get_post_meta($post_id, '_price', true);
    $weekend_price  = (int) get_post_meta($post_id, '_weekend_price', true);

    // Nếu chưa có giá cuối tuần → dùng giá trong tuần
    if (!$weekend_price) {
        return $weekday_price ?: 1500000;
    }

    if ($date && bookingroom_is_weekend($date)) {
        return $weekend_price;
    }

    return $weekday_price ?: 1500000;
}

/**
 * Tính tổng tiền phòng cho một khoảng thời gian,
 * phân biệt đêm cuối tuần và đêm trong tuần.
 *
 * @param  int    $post_id      ID phòng
 * @param  string $check_in     Ngày nhận phòng (YYYY-MM-DD)
 * @param  string $check_out    Ngày trả phòng (YYYY-MM-DD)
 * @return array {
 *     @type int   weekday_nights    Số đêm trong tuần
 *     @type int   weekend_nights    Số đêm cuối tuần
 *     @type int   weekday_price     Giá mỗi đêm trong tuần
 *     @type int   weekend_price     Giá mỗi đêm cuối tuần
 *     @type int   subtotal          Tiền phòng trước phí
 *     @type int   total_nights      Tổng số đêm
 * }
 */
function bookingroom_calculate_stay_price($post_id, $check_in, $check_out) {
    $weekday_price = (int) get_post_meta($post_id, '_price', true) ?: 1500000;
    $weekend_price = (int) get_post_meta($post_id, '_weekend_price', true);

    // Nếu chưa cài giá cuối tuần → cả kỳ nghỉ tính giá trong tuần
    if (!$weekend_price) {
        $weekend_price = $weekday_price;
    }

    $ci = new DateTime($check_in);
    $co = new DateTime($check_out);

    if ($ci >= $co) {
        return [
            'weekday_nights' => 0,
            'weekend_nights' => 0,
            'weekday_price'  => $weekday_price,
            'weekend_price'  => $weekend_price,
            'subtotal'       => 0,
            'total_nights'   => 0,
        ];
    }

    $weekday_nights = 0;
    $weekend_nights = 0;

    $current = clone $ci;
    while ($current < $co) {
        if (bookingroom_is_weekend($current)) {
            $weekend_nights++;
        } else {
            $weekday_nights++;
        }
        $current->modify('+1 day');
    }

    $subtotal    = ($weekday_nights * $weekday_price) + ($weekend_nights * $weekend_price);
    $total_nights = $weekday_nights + $weekend_nights;

    return [
        'weekday_nights' => $weekday_nights,
        'weekend_nights' => $weekend_nights,
        'weekday_price'  => $weekday_price,
        'weekend_price'  => $weekend_price,
        'subtotal'       => $subtotal,
        'total_nights'   => $total_nights,
    ];
}

/**
 * Lấy giá hiển thị "từ" cho danh sách phòng.
 * Ưu tiên giá trong tuần (thấp hơn) để hiển thị "Từ X đ/đêm".
 *
 * @param  int $post_id  ID phòng
 * @return int
 */
function bookingroom_get_display_price($post_id) {
    $weekday_price = (int) get_post_meta($post_id, '_price', true) ?: 1500000;
    $weekend_price = (int) get_post_meta($post_id, '_weekend_price', true);

    if ($weekend_price && $weekend_price < $weekday_price) {
        return $weekend_price;
    }

    return $weekday_price;
}

/**
 * Render badge giá cuối tuần/trong tuần để hiển thị trong card phòng.
 *
 * @param  int  $post_id    ID phòng
 * @param  bool $echo       Nếu true → echo ra, nếu false → return string
 * @return string|void
 */
function bookingroom_render_price_badge($post_id, $echo = true) {
    $weekday_price = (int) get_post_meta($post_id, '_price', true) ?: 0;
    $weekend_price = (int) get_post_meta($post_id, '_weekend_price', true);

    $html = '<div class="room-price-info">';

    if ($weekday_price) {
        $html .= '<span class="price-weekday">';
        $html .= '<span class="price-label">Trong tuần</span>';
        $html .= '<span class="price-value">' . number_format($weekday_price) . 'đ</span>';
        $html .= '</span>';
    }

    if ($weekend_price && $weekend_price !== $weekday_price) {
        $html .= '<span class="price-weekend">';
        $html .= '<span class="price-label">Cuối tuần</span>';
        $html .= '<span class="price-value">' . number_format($weekend_price) . 'đ</span>';
        $html .= '</span>';
    }

    $html .= '</div>';

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}
