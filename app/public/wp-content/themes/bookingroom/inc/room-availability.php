<?php
/**
 * Room Availability System
 * Hệ thống tìm phòng trống thời gian thực cho theme bookingroom
 *
 * Tính năng:
 *  - AJAX endpoint: bookingroom_check_availability
 *  - Kiểm tra xung đột booking dựa trên post type 'booking' + meta _check_in/_check_out
 *  - Hỗ trợ WP Hotel Booking plugin (hb_room) nếu active
 *  - Shortcode [room_availability_form] để nhúng form bất kỳ đâu
 *
 * @package BookingRoom
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ═══════════════════════════════════════════════════════════════════════
// 1. HÀM KIỂM TRA PHÒNG TRỐNG (Core Logic)
// ═══════════════════════════════════════════════════════════════════════

/**
 * Lấy danh sách phòng còn trống theo ngày check-in/check-out.
 *
 * @param string $check_in   YYYY-MM-DD
 * @param string $check_out  YYYY-MM-DD
 * @param int    $adults     Số người lớn
 * @param int    $children   Số trẻ em
 * @param string $room_type  Slug loại phòng (tuỳ chọn)
 * @return array             Mảng thông tin phòng trống
 */
function bookingroom_find_available_rooms( $check_in, $check_out, $adults = 1, $children = 0, $room_type = '' ) {
    // ── Xác định post type (WP Hotel Booking hay nội bộ) ────────────────
    $use_hb      = bookingroom_is_wphb_active();
    $post_type   = $use_hb ? 'hb_room' : 'room';
    $taxonomy    = $use_hb ? 'hb_room_type' : 'room_category';

    // ── Query phòng ────────────────────────────────────────────────────
    $tax_query = [];
    if ( $room_type ) {
        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $room_type ),
        ];
    }

    $query_args = [
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ];
    if ( ! empty( $tax_query ) ) {
        $query_args['tax_query'] = $tax_query;
    }

    $rooms_query = new WP_Query( $query_args );
    $available   = [];

    if ( ! $rooms_query->have_posts() ) {
        return $available;
    }

    while ( $rooms_query->have_posts() ) {
        $rooms_query->the_post();
        $room_id = get_the_ID();

        // Lấy số phòng vật lý (physical units) của loại phòng này
        $total_units = bookingroom_get_room_total_units( $room_id, $post_type );

        // Kiểm tra đã đặt bao nhiêu phòng trong khoảng thời gian này
        $booked_units = bookingroom_get_booked_units( $room_id, $check_in, $check_out, $post_type );

        $free_units = max( 0, $total_units - $booked_units );

        // Kiểm tra sức chứa
        $capacity = $use_hb
            ? bookingroom_get_hbroom_capacity( $room_id )
            : (int) ( get_post_meta( $room_id, '_capacity', true ) ?: 2 );

        // Lấy giá
        $price = $use_hb
            ? bookingroom_get_hbroom_price( $room_id )
            : (float) ( get_post_meta( $room_id, '_price', true ) ?: 1500000 );

        // Giá cuối tuần (chỉ dùng cho post type 'room' nội bộ)
        $weekend_price = ! $use_hb
            ? (float) ( get_post_meta( $room_id, '_weekend_price', true ) ?: $price )
            : $price;

        // Tính tổng tiền theo ngày
        $price_breakdown = bookingroom_calc_price_breakdown( $price, $weekend_price, $check_in, $check_out );

        // Ảnh đại diện
        $thumb_url = has_post_thumbnail( $room_id )
            ? get_the_post_thumbnail_url( $room_id, 'medium' )
            : 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=400';

        // Loại phòng (taxonomy terms)
        $terms     = get_the_terms( $room_id, $taxonomy );
        $type_name = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';

        $available[] = [
            'id'             => $room_id,
            'title'          => get_the_title(),
            'permalink'      => get_permalink(),
            'thumbnail'      => $thumb_url,
            'price'          => $price,
            'weekend_price'  => $weekend_price,
            'capacity'       => $capacity,
            'free_units'     => $free_units,
            'total_units'    => $total_units,
            'type_name'      => $type_name,
            'price_breakdown'=> $price_breakdown,
            'label'          => get_post_meta( $room_id, '_room_label', true ) ?: $type_name,
            'excerpt'        => wp_trim_words( get_the_excerpt(), 15, '...' ),
        ];
    }

    wp_reset_postdata();

    // Sắp xếp: phòng còn trống lên đầu, sau đó theo giá tăng dần
    usort( $available, function( $a, $b ) {
        if ( $a['free_units'] > 0 && $b['free_units'] === 0 ) return -1;
        if ( $a['free_units'] === 0 && $b['free_units'] > 0 ) return 1;
        return $a['price'] <=> $b['price'];
    } );

    return $available;
}

/**
 * Lấy tổng số phòng vật lý (units) của một loại phòng.
 */
function bookingroom_get_room_total_units( $room_id, $post_type = 'room' ) {
    // Ưu tiên meta _room_numbers (VD: "101, 102, 103")
    $room_numbers = get_post_meta( $room_id, '_room_numbers', true );
    if ( ! empty( $room_numbers ) ) {
        return count( array_filter( array_map( 'trim', explode( ',', $room_numbers ) ) ) );
    }

    // Nếu dùng WP Hotel Booking, lấy từ meta của plugin
    if ( $post_type === 'hb_room' ) {
        $qty = (int) get_post_meta( $room_id, '_tp_room_count', true );
        if ( $qty > 0 ) return $qty;
        $qty = (int) get_post_meta( $room_id, '_hb_room_count', true );
        if ( $qty > 0 ) return $qty;
    }

    // Fallback meta tổng quát
    $qty = (int) get_post_meta( $room_id, '_room_quantity', true );
    if ( $qty > 0 ) return $qty;

    return 10; // Mặc định
}

/**
 * Đếm số booking đang overlap trong khoảng ngày cho một loại phòng.
 */
function bookingroom_get_booked_units( $room_id, $check_in, $check_out, $post_type = 'room' ) {
    // Thử dùng API của WP Hotel Booking plugin trước
    if ( $post_type === 'hb_room' ) {
        if ( function_exists( 'hb_room_available' ) ) {
            $total = bookingroom_get_room_total_units( $room_id, $post_type );
            $avail = (int) hb_room_available( $room_id, $check_in, $check_out );
            return max( 0, $total - $avail );
        }
    }

    // Query booking nội bộ
    $meta_room_key = $post_type === 'hb_room' ? '_hb_room_id' : '_room_id';

    $args = [
        'post_type'      => 'booking',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'     => $meta_room_key,
                'value'   => $room_id,
                'compare' => '=',
            ],
            // check_in của booking < check_out của search
            [
                'key'     => '_check_in',
                'value'   => $check_out,
                'compare' => '<',
                'type'    => 'DATE',
            ],
            // check_out của booking > check_in của search
            [
                'key'     => '_check_out',
                'value'   => $check_in,
                'compare' => '>',
                'type'    => 'DATE',
            ],
            // Không tính booking đã huỷ
            [
                'key'     => '_status',
                'value'   => [ 'cancelled' ],
                'compare' => 'NOT IN',
            ],
        ],
    ];

    // Thử cả hai key nếu post type là hb_room
    if ( $post_type === 'hb_room' ) {
        $args2          = $args;
        $args2['meta_query'][0]['key'] = '_room_id';
        $q2             = new WP_Query( $args2 );
        $booked_with_room_key = $q2->found_posts;
        $q1             = new WP_Query( $args );
        return max( $q1->found_posts, $booked_with_room_key );
    }

    $bookings_query = new WP_Query( $args );
    return $bookings_query->found_posts;
}

/**
 * Tính chi tiết giá theo từng đêm (cuối tuần / trong tuần).
 */
function bookingroom_calc_price_breakdown( $weekday_price, $weekend_price, $check_in, $check_out ) {
    $ci = new DateTime( $check_in );
    $co = new DateTime( $check_out );

    if ( $ci >= $co ) {
        return [ 'total_nights' => 0, 'weekday_nights' => 0, 'weekend_nights' => 0, 'subtotal' => 0 ];
    }

    $weekday_nights = 0;
    $weekend_nights = 0;
    $current = clone $ci;

    while ( $current < $co ) {
        $dow = (int) $current->format( 'w' ); // 0=Sun, 5=Fri, 6=Sat
        if ( in_array( $dow, [ 0, 5, 6 ] ) ) {
            $weekend_nights++;
        } else {
            $weekday_nights++;
        }
        $current->modify( '+1 day' );
    }

    $subtotal = ( $weekday_nights * $weekday_price ) + ( $weekend_nights * $weekend_price );

    return [
        'total_nights'   => $weekday_nights + $weekend_nights,
        'weekday_nights' => $weekday_nights,
        'weekend_nights' => $weekend_nights,
        'weekday_price'  => $weekday_price,
        'weekend_price'  => $weekend_price,
        'subtotal'       => $subtotal,
    ];
}

// ═══════════════════════════════════════════════════════════════════════
// 2. AJAX HANDLER
// ═══════════════════════════════════════════════════════════════════════

add_action( 'wp_ajax_bookingroom_check_availability',        'bookingroom_ajax_check_availability' );
add_action( 'wp_ajax_nopriv_bookingroom_check_availability', 'bookingroom_ajax_check_availability' );

function bookingroom_ajax_check_availability() {
    check_ajax_referer( 'booking_nonce', 'nonce' );

    $check_in   = sanitize_text_field( $_POST['check_in']   ?? '' );
    $check_out  = sanitize_text_field( $_POST['check_out']  ?? '' );
    $adults     = absint( $_POST['adults']    ?? 1 );
    $children   = absint( $_POST['children'] ?? 0 );
    $room_type  = sanitize_text_field( $_POST['room_type']  ?? '' );

    // Validation
    if ( ! $check_in || ! $check_out ) {
        wp_send_json_error( [ 'message' => 'Vui lòng chọn ngày nhận phòng và trả phòng.' ] );
    }

    $ci_ts = strtotime( $check_in );
    $co_ts = strtotime( $check_out );

    if ( ! $ci_ts || ! $co_ts ) {
        wp_send_json_error( [ 'message' => 'Ngày không hợp lệ.' ] );
    }

    if ( $co_ts <= $ci_ts ) {
        wp_send_json_error( [ 'message' => 'Ngày trả phòng phải sau ngày nhận phòng.' ] );
    }

    if ( $ci_ts < strtotime( 'today' ) ) {
        wp_send_json_error( [ 'message' => 'Ngày nhận phòng không thể trong quá khứ.' ] );
    }

    $nights = round( ( $co_ts - $ci_ts ) / DAY_IN_SECONDS );

    $rooms = bookingroom_find_available_rooms( $check_in, $check_out, $adults, $children, $room_type );

    $available_count = count( array_filter( $rooms, fn($r) => $r['free_units'] > 0 ) );

    wp_send_json_success( [
        'rooms'           => $rooms,
        'nights'          => $nights,
        'check_in'        => $check_in,
        'check_out'       => $check_out,
        'adults'          => $adults,
        'children'        => $children,
        'available_count' => $available_count,
        'ci_display'      => date_i18n( 'd/m/Y', $ci_ts ),
        'co_display'      => date_i18n( 'd/m/Y', $co_ts ),
        'ci_dow'          => bookingroom_vn_day_of_week( $ci_ts ),
        'co_dow'          => bookingroom_vn_day_of_week( $co_ts ),
    ] );
}

/**
 * Trả về tên thứ trong tuần bằng tiếng Việt.
 */
function bookingroom_vn_day_of_week( $timestamp ) {
    $days = [ 'Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7' ];
    return $days[ (int) date( 'w', $timestamp ) ];
}

// ═══════════════════════════════════════════════════════════════════════
// 3. SHORTCODE [room_availability_form]
// ═══════════════════════════════════════════════════════════════════════

add_shortcode( 'room_availability_form', 'bookingroom_availability_shortcode' );

function bookingroom_availability_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'title'     => t('Tìm phòng trống', 'Find available rooms'),
        'subtitle'  => t('Kiểm tra phòng trống theo ngày', 'Check availability by date'),
        'style'     => 'card', // 'card' | 'inline' | 'hero'
    ], $atts );

    ob_start();
    bookingroom_render_availability_form( $atts );
    return ob_get_clean();
}

/**
 * Render form tìm phòng trống.
 */
function bookingroom_render_availability_form( $args = [] ) {
    $title    = $args['title']    ?? t('Tìm phòng trống', 'Find available rooms');
    $subtitle = $args['subtitle'] ?? t('Kiểm tra phòng trống theo ngày', 'Check availability by date');
    $style    = $args['style']    ?? 'card';

    $use_hb   = bookingroom_is_wphb_active();
    $taxonomy = $use_hb ? 'hb_room_type' : 'room_category';
    $room_types = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
    ?>
    <div class="br-avail-widget br-avail-style-<?php echo esc_attr($style); ?>" id="br-availability-widget">

        <?php if ( $style !== 'inline' ) : ?>
        <div class="br-avail-header">
            <h3><?php echo esc_html($title); ?></h3>
            <?php if ($subtitle) : ?><p><?php echo esc_html($subtitle); ?></p><?php endif; ?>
        </div>
        <?php endif; ?>

        <form id="br-avail-form" class="br-avail-form" novalidate>
            <?php wp_nonce_field( 'booking_nonce', 'br_avail_nonce', false ); ?>

            <div class="br-avail-fields">
                <div class="br-avail-field">
                    <label for="br_check_in">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?php echo t('Nhận phòng', 'Check-in'); ?>
                    </label>
                    <input type="date" id="br_check_in" name="check_in" required
                        min="<?php echo date('Y-m-d'); ?>"
                        placeholder="dd/mm/yyyy">
                </div>

                <div class="br-avail-field">
                    <label for="br_check_out">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?php echo t('Trả phòng', 'Check-out'); ?>
                    </label>
                    <input type="date" id="br_check_out" name="check_out" required
                        placeholder="dd/mm/yyyy">
                </div>

                <div class="br-avail-field br-avail-guests">
                    <label>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <?php echo t('Khách', 'Guests'); ?>
                    </label>
                    <div class="br-guest-pickers">
                        <div class="br-guest-picker">
                            <span><?php echo t('Người lớn', 'Adults'); ?></span>
                            <div class="br-counter">
                                <button type="button" class="br-counter-btn" data-target="br_adults" data-action="minus">−</button>
                                <input type="number" id="br_adults" name="adults" value="2" min="1" max="10" readonly>
                                <button type="button" class="br-counter-btn" data-target="br_adults" data-action="plus">+</button>
                            </div>
                        </div>
                        <div class="br-guest-picker">
                            <span><?php echo t('Trẻ em', 'Children'); ?></span>
                            <div class="br-counter">
                                <button type="button" class="br-counter-btn" data-target="br_children" data-action="minus">−</button>
                                <input type="number" id="br_children" name="children" value="0" min="0" max="10" readonly>
                                <button type="button" class="br-counter-btn" data-target="br_children" data-action="plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ( ! is_wp_error( $room_types ) && ! empty( $room_types ) ) : ?>
                <div class="br-avail-field">
                    <label for="br_room_type">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <?php echo t('Loại phòng', 'Room type'); ?>
                    </label>
                    <select id="br_room_type" name="room_type">
                        <option value=""><?php echo t('Tất cả loại phòng', 'All room types'); ?></option>
                        <?php foreach ( $room_types as $rt ) : ?>
                            <option value="<?php echo esc_attr($rt->slug); ?>"><?php echo esc_html($rt->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="br-avail-submit">
                    <button type="submit" id="br-avail-submit-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span><?php echo t('Kiểm tra phòng trống', 'Check availability'); ?></span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Loading -->
        <div id="br-avail-loading" class="br-avail-loading hidden">
            <div class="br-avail-spinner"></div>
            <p><?php echo t('Đang tìm phòng trống...', 'Searching for available rooms...'); ?></p>
        </div>

        <!-- Results -->
        <div id="br-avail-results" class="br-avail-results hidden"></div>
    </div>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════
// 4. ENQUEUE ASSETS
// ═══════════════════════════════════════════════════════════════════════

add_action( 'wp_enqueue_scripts', 'bookingroom_availability_assets' );
function bookingroom_availability_assets() {
    // Inline CSS
    $css = bookingroom_availability_css();
    wp_add_inline_style( 'child-style', $css );

    // Inline JS (phụ thuộc vào booking-script đã đăng ký trong functions.php)
    $js = bookingroom_availability_js();
    wp_add_inline_script( 'booking-script', $js );
}

function bookingroom_availability_css() {
    return '
/* ─── Availability Widget Base ─── */
.br-avail-widget {
    --br-primary: #d35400;
    --br-primary-dark: #b84300;
    --br-primary-light: #fff3ee;
    --br-success: #16a34a;
    --br-error: #dc2626;
    --br-text: #1e293b;
    --br-muted: #64748b;
    --br-border: #e2e8f0;
    --br-bg: #f8fafc;
    --br-white: #ffffff;
    --br-radius: 16px;
    --br-radius-sm: 10px;
    --br-shadow: 0 4px 24px rgba(0,0,0,0.08);
    font-family: "Inter", -apple-system, sans-serif;
    color: var(--br-text);
}

/* Header */
.br-avail-header { margin-bottom: 20px; }
.br-avail-header h3 {
    font-size: 20px; font-weight: 800; margin: 0 0 6px;
    color: var(--br-text);
}
.br-avail-header p { margin: 0; color: var(--br-muted); font-size: 14px; }

/* Form */
.br-avail-form { width: 100%; }
.br-avail-fields {
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.br-avail-field { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 160px; }
.br-avail-field label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: var(--br-muted);
    text-transform: uppercase; letter-spacing: 0.06em;
}
.br-avail-field label svg { width: 14px; height: 14px; }
.br-avail-field input[type="date"],
.br-avail-field select {
    background: var(--br-white); border: 2px solid var(--br-border);
    border-radius: var(--br-radius-sm); padding: 12px 14px;
    font-size: 15px; font-weight: 600; color: var(--br-text);
    outline: none; transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%; box-sizing: border-box; cursor: pointer;
}
.br-avail-field input[type="date"]:focus,
.br-avail-field select:focus {
    border-color: var(--br-primary);
    box-shadow: 0 0 0 3px rgba(211,84,0,0.12);
}

/* Guest Pickers */
.br-guest-pickers {
    background: var(--br-white); border: 2px solid var(--br-border);
    border-radius: var(--br-radius-sm); padding: 10px 14px;
    display: flex; gap: 16px; flex-wrap: wrap;
}
.br-guest-picker { display: flex; align-items: center; gap: 10px; }
.br-guest-picker span { font-size: 13px; font-weight: 600; color: var(--br-muted); white-space: nowrap; }
.br-counter { display: flex; align-items: center; gap: 6px; }
.br-counter-btn {
    width: 30px; height: 30px; border-radius: 50%;
    border: 2px solid var(--br-border); background: var(--br-bg);
    font-size: 18px; font-weight: 700; line-height: 1;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center;
    color: var(--br-text);
}
.br-counter-btn:hover { border-color: var(--br-primary); color: var(--br-primary); background: var(--br-primary-light); }
.br-counter input[type="number"] {
    width: 38px; text-align: center; font-size: 15px; font-weight: 800;
    border: none; background: transparent; outline: none; color: var(--br-text);
    -moz-appearance: textfield; pointer-events: none;
}
.br-counter input::-webkit-outer-spin-button,
.br-counter input::-webkit-inner-spin-button { -webkit-appearance: none; }

/* Submit */
.br-avail-submit { display: flex; align-items: flex-end; }
.br-avail-submit button {
    background: var(--br-primary); color: #fff; border: none;
    border-radius: var(--br-radius-sm); padding: 13px 24px;
    font-size: 15px; font-weight: 700; cursor: pointer;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    display: flex; align-items: center; gap: 8px; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(211,84,0,0.25);
}
.br-avail-submit button svg { width: 18px; height: 18px; }
.br-avail-submit button:hover { background: var(--br-primary-dark); box-shadow: 0 8px 20px rgba(211,84,0,0.35); transform: translateY(-1px); }
.br-avail-submit button:active { transform: translateY(0); }
.br-avail-submit button:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* Loading */
.br-avail-loading {
    display: flex; flex-direction: column; align-items: center;
    padding: 48px 24px; gap: 16px; color: var(--br-muted);
}
.br-avail-spinner {
    width: 40px; height: 40px; border: 3px solid var(--br-border);
    border-top-color: var(--br-primary); border-radius: 50%;
    animation: br-spin 0.8s linear infinite;
}
@keyframes br-spin { to { transform: rotate(360deg); } }

/* Results Summary */
.br-result-summary {
    margin-top: 24px; padding: 16px 20px;
    background: linear-gradient(135deg, var(--br-primary), var(--br-primary-dark));
    border-radius: var(--br-radius); color: #fff;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.br-result-summary strong { font-size: 18px; font-weight: 800; }
.br-result-summary span { font-size: 13px; opacity: 0.85; }
.br-result-dates { display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; }
.br-result-dates .br-date-tag {
    background: rgba(255,255,255,0.18); border-radius: 8px;
    padding: 4px 12px; font-weight: 600;
}

/* Room Cards */
.br-rooms-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px; margin-top: 20px;
}
.br-room-card {
    background: var(--br-white); border-radius: var(--br-radius);
    overflow: hidden; border: 2px solid var(--br-border);
    transition: box-shadow 0.25s, border-color 0.25s, transform 0.25s;
    display: flex; flex-direction: column;
}
.br-room-card:hover { box-shadow: var(--br-shadow); border-color: var(--br-primary); transform: translateY(-3px); }
.br-room-card.br-sold-out { opacity: 0.65; }
.br-room-card.br-sold-out:hover { transform: none; border-color: var(--br-border); box-shadow: none; }

.br-room-card__img {
    position: relative; height: 200px; overflow: hidden; background: #e2e8f0;
}
.br-room-card__img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.br-room-card:hover .br-room-card__img img { transform: scale(1.06); }

.br-room-badge {
    position: absolute; top: 12px; left: 12px;
    background: var(--br-primary); color: #fff;
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.05em; padding: 4px 10px; border-radius: 20px;
}
.br-avail-badge {
    position: absolute; top: 12px; right: 12px;
    font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px;
}
.br-avail-badge.avail { background: #dcfce7; color: #16a34a; }
.br-avail-badge.sold  { background: #fee2e2; color: #dc2626; }

.br-price-float {
    position: absolute; bottom: 12px; right: 12px;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(8px);
    border-radius: 10px; padding: 6px 12px; text-align: right;
    box-shadow: 0 2px 10px rgba(0,0,0,0.12);
}
.br-price-float .br-price-main { font-size: 16px; font-weight: 800; color: var(--br-primary); }
.br-price-float .br-price-unit { font-size: 10px; color: var(--br-muted); }

.br-room-card__body { padding: 18px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
.br-room-card__title { font-size: 17px; font-weight: 800; color: var(--br-text); margin: 0; line-height: 1.3; }
.br-room-card__excerpt { font-size: 13px; color: var(--br-muted); margin: 0; line-height: 1.5; }
.br-room-card__meta { display: flex; gap: 16px; flex-wrap: wrap; }
.br-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--br-muted); font-weight: 600; }
.br-meta-item svg { width: 14px; height: 14px; color: var(--br-primary); }

.br-price-breakdown {
    background: var(--br-bg); border-radius: 10px; padding: 12px 14px;
    font-size: 12px; display: flex; flex-direction: column; gap: 4px;
}
.br-price-row { display: flex; justify-content: space-between; color: var(--br-muted); }
.br-price-row.br-total { font-weight: 800; color: var(--br-text); font-size: 14px; margin-top: 4px; border-top: 1px solid var(--br-border); padding-top: 6px; }
.br-price-row.br-total .br-price-value { color: var(--br-primary); font-size: 16px; }

.br-room-card__footer { padding: 0 18px 18px; }
.br-book-btn {
    display: block; width: 100%; text-align: center;
    background: var(--br-primary); color: #fff;
    padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px;
    text-decoration: none; transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(211,84,0,0.2);
}
.br-book-btn:hover { background: var(--br-primary-dark); box-shadow: 0 6px 18px rgba(211,84,0,0.35); color: #fff; }
.br-book-btn.disabled {
    background: #cbd5e1; color: #94a3b8; cursor: not-allowed;
    box-shadow: none; pointer-events: none;
}

/* No results */
.br-no-results {
    text-align: center; padding: 60px 24px; color: var(--br-muted);
    border: 2px dashed var(--br-border); border-radius: var(--br-radius);
    margin-top: 20px;
}
.br-no-results svg { width: 48px; height: 48px; margin: 0 auto 16px; display: block; opacity: 0.35; }
.br-no-results strong { display: block; font-size: 18px; color: var(--br-text); margin-bottom: 8px; }

/* Utility */
.hidden { display: none !important; }

/* Card style wrapper */
.br-avail-style-card {
    background: var(--br-white); border-radius: 24px;
    padding: 28px 32px; box-shadow: var(--br-shadow);
    border: 1px solid var(--br-border);
}

/* Responsive */
@media (max-width: 640px) {
    .br-avail-fields { flex-direction: column; }
    .br-avail-field { min-width: 100%; }
    .br-avail-submit button { width: 100%; justify-content: center; }
    .br-avail-style-card { padding: 20px 16px; }
    .br-rooms-grid { grid-template-columns: 1fr; }
    .br-result-summary { flex-direction: column; }
}
';
}

function bookingroom_availability_js() {
    $archive_url = esc_js( get_post_type_archive_link( bookingroom_is_wphb_active() ? 'hb_room' : 'room' ) ?: home_url('/rooms') );

    return <<<JS
(function($){
    'use strict';

    // ── Counter buttons ────────────────────────────────────────────────
    $(document).on('click', '.br-counter-btn', function(){
        var target  = $(this).data('target');
        var action  = $(this).data('action');
        var \$input  = $('#' + target);
        var current = parseInt(\$input.val()) || 0;
        var min     = parseInt(\$input.attr('min')) || 0;
        var max     = parseInt(\$input.attr('max')) || 99;
        if (action === 'plus'  && current < max) \$input.val(current + 1);
        if (action === 'minus' && current > min) \$input.val(current - 1);
    });

    // ── Date validation ────────────────────────────────────────────────
    $(document).on('change', '#br_check_in', function(){
        var ci   = new Date(this.value);
        var next = new Date(ci);
        next.setDate(next.getDate() + 1);
        var nextStr = next.toLocaleDateString('sv-SE');
        var \$co = $('#br_check_out');
        \$co.attr('min', nextStr);
        if (\$co.val() && \$co.val() <= this.value) {
            \$co.val(nextStr);
        }
    });

    // ── Form submit ────────────────────────────────────────────────────
    $(document).on('submit', '#br-avail-form', function(e){
        e.preventDefault();
        var \$form    = $(this);
        var \$btn     = $('#br-avail-submit-btn');
        var \$loading = $('#br-avail-loading');
        var \$results = $('#br-avail-results');

        var checkIn   = \$form.find('[name="check_in"]').val();
        var checkOut  = \$form.find('[name="check_out"]').val();
        var adults    = parseInt(\$form.find('[name="adults"]').val()) || 1;
        var children  = parseInt(\$form.find('[name="children"]').val()) || 0;
        var roomType  = \$form.find('[name="room_type"]').val() || '';
        var nonce     = \$form.find('[name="br_avail_nonce"]').val() || (booking_ajax ? booking_ajax.nonce : '');

        if (!checkIn || !checkOut) {
            alert('<?php echo esc_js(t('Vui lòng chọn ngày nhận phòng và trả phòng.', 'Please select check-in and check-out dates.')); ?>');
            return;
        }

        // UI: show loading
        \$btn.prop('disabled', true).find('span').text('<?php echo esc_js(t('Đang tìm...', 'Searching...')); ?>');
        \$loading.removeClass('hidden');
        \$results.addClass('hidden').html('');

        $.ajax({
            url:  booking_ajax.ajax_url,
            type: 'POST',
            data: {
                action:    'bookingroom_check_availability',
                nonce:     nonce,
                check_in:  checkIn,
                check_out: checkOut,
                adults:    adults,
                children:  children,
                room_type: roomType,
            },
            success: function(res) {
                if (!res.success) {
                    \$results.removeClass('hidden').html(brErrorHtml(res.data.message || '<?php echo esc_js(t('Có lỗi xảy ra.', 'An error occurred.')); ?>'));
                    return;
                }
                var d = res.data;
                \$results.removeClass('hidden').html(brBuildResults(d, checkIn, checkOut, adults, children));

                // Scroll to results smoothly
                var offset = \$results.offset().top - 80;
                \$('html, body').animate({ scrollTop: offset }, 400);
            },
            error: function() {
                \$results.removeClass('hidden').html(brErrorHtml('<?php echo esc_js(t('Kết nối thất bại. Vui lòng thử lại.', 'Connection failed. Please try again.')); ?>'));
            },
            complete: function() {
                \$btn.prop('disabled', false).find('span').text('<?php echo esc_js(t('Kiểm tra phòng trống', 'Check availability')); ?>');
                \$loading.addClass('hidden');
            }
        });
    });

    // ── Build HTML ─────────────────────────────────────────────────────
    function fmt(n) {
        return new Intl.NumberFormat('vi-VN').format(n);
    }

    function brBuildResults(d, ci, co, adults, children) {
        var html = '';

        // Summary bar
        var msg = d.available_count > 0
            ? '<strong>' + d.available_count + ' <?php echo esc_js(t('loại phòng còn trống', 'available room types')); ?></strong>'
            : '<strong><?php echo esc_js(t('Không có phòng trống', 'No available rooms')); ?></strong>';
        html += '<div class="br-result-summary">';
        html += '<div>' + msg + '<br><span>' + d.nights + ' <?php echo esc_js(t('đêm', 'nights')); ?> &middot; ' + adults + ' <?php echo esc_js(t('NL', 'Adults')); ?>' + (children ? ' + ' + children + ' <?php echo esc_js(t('TE', 'Children')); ?>' : '') + '</span></div>';
        html += '<div class="br-result-dates">';
        html += '<div class="br-date-tag">📅 ' + d.ci_dow + ', ' + d.ci_display + '</div>';
        html += '<div class="br-date-tag">📅 ' + d.co_dow + ', ' + d.co_display + '</div>';
        html += '</div></div>';

        if (!d.rooms || d.rooms.length === 0) {
            return html + brNoResults();
        }

        html += '<div class="br-rooms-grid">';
        d.rooms.forEach(function(room) {
            html += brRoomCard(room, d, ci, co, adults, children);
        });
        html += '</div>';

        return html;
    }

    function brRoomCard(room, d, ci, co, adults, children) {
        var isSoldOut  = room.free_units <= 0;
        var cardClass  = 'br-room-card' + (isSoldOut ? ' br-sold-out' : '');
        var availClass = isSoldOut ? 'sold' : 'avail';
        var availText  = isSoldOut ? '<?php echo esc_js(t('Hết phòng', 'Sold out')); ?>' : room.free_units + ' <?php echo esc_js(t('phòng trống', 'available rooms')); ?>';

        // Book URL
        var bookUrl = room.permalink + '?check_in=' + ci + '&check_out=' + co
            + '&adults=' + adults + '&children=' + children + '&step=book';

        // Price breakdown
        var pb   = room.price_breakdown;
        var priceHtml = '';
        if (pb && pb.total_nights > 0) {
            priceHtml += '<div class="br-price-breakdown">';
            if (pb.weekday_nights > 0 && pb.weekend_nights > 0) {
                priceHtml += '<div class="br-price-row"><span><?php echo esc_js(t('Trong tuần', 'Weekday')); ?> (' + pb.weekday_nights + ' <?php echo esc_js(t('đêm', 'nights')); ?>)</span><span>' + fmt(pb.weekday_price) + 'đ/<?php echo esc_js(t('đêm', 'night')); ?></span></div>';
                priceHtml += '<div class="br-price-row"><span><?php echo esc_js(t('Cuối tuần', 'Weekend')); ?> (' + pb.weekend_nights + ' <?php echo esc_js(t('đêm', 'nights')); ?>)</span><span>' + fmt(pb.weekend_price) + 'đ/<?php echo esc_js(t('đêm', 'night')); ?></span></div>';
            } else {
                priceHtml += '<div class="br-price-row"><span>' + pb.total_nights + ' <?php echo esc_js(t('đêm', 'nights')); ?> &times; ' + fmt(room.price) + 'đ</span></div>';
            }
            priceHtml += '<div class="br-price-row br-total"><span><?php echo esc_js(t('Tổng cộng', 'Total')); ?></span><span class="br-price-value">' + fmt(pb.subtotal) + 'đ</span></div>';
            priceHtml += '</div>';
        }

        var bookBtn = isSoldOut
            ? '<span class="br-book-btn disabled"><?php echo esc_js(t('Hết phòng', 'Sold out')); ?></span>'
            : '<a href="' + escHtml(bookUrl) + '" class="br-book-btn"><?php echo esc_js(t('Đặt phòng ngay', 'Book now')); ?> &rarr;</a>';

        return '<div class="' + cardClass + '">'
            + '<div class="br-room-card__img">'
            + '<img src="' + escHtml(room.thumbnail) + '" alt="' + escHtml(room.title) + '" loading="lazy">'
            + (room.type_name ? '<div class="br-room-badge">' + escHtml(room.type_name) + '</div>' : '')
            + '<div class="br-avail-badge ' + availClass + '">' + availText + '</div>'
            + '<div class="br-price-float"><div class="br-price-main">' + fmt(room.price) + 'đ</div><div class="br-price-unit">/ <?php echo esc_js(t('đêm', 'night')); ?></div></div>'
            + '</div>'
            + '<div class="br-room-card__body">'
            + '<h3 class="br-room-card__title">' + escHtml(room.title) + '</h3>'
            + (room.excerpt ? '<p class="br-room-card__excerpt">' + escHtml(room.excerpt) + '</p>' : '')
            + '<div class="br-room-card__meta">'
            + '<div class="br-meta-item"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>' + room.capacity + ' <?php echo esc_js(t('khách', 'guests')); ?></div>'
            + '<div class="br-meta-item"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' + d.nights + ' <?php echo esc_js(t('đêm', 'nights')); ?></div>'
            + '</div>'
            + priceHtml
            + '</div>'
            + '<div class="br-room-card__footer">' + bookBtn + '</div>'
            + '</div>';
    }

    function brNoResults() {
        return '<div class="br-no-results">'
            + '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            + '<strong><?php echo esc_js(t('Không có phòng trống', 'No available rooms')); ?></strong>'
            + '<p><?php echo esc_js(t('Thử thay đổi ngày hoặc loại phòng khác.', 'Try changing dates or another room type.')); ?></p>'
            + '</div>';
    }

    function brErrorHtml(msg) {
        return '<div class="br-no-results" style="border-color:#fca5a5;background:#fff5f5;">'
            + '<strong style="color:#dc2626;">⚠ ' + escHtml(msg) + '</strong>'
            + '</div>';
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

})(jQuery);
JS;
}
