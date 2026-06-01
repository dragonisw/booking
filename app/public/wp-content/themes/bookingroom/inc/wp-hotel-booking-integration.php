<?php
/**
 * WP Hotel Booking Integration
 * Kết nối theme bookingroom với plugin WP Hotel Booking (ThimPress)
 *
 * Cách dùng: file này được include từ functions.php
 *
 * @package BookingRoom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Kiểm tra plugin WP Hotel Booking có đang active không.
 *
 * @return bool
 */
function bookingroom_is_wphb_active() {
    return class_exists( 'WP_Hotel_Booking' ) || function_exists( 'tp_hotel_booking' );
}

/**
 * Đăng ký đường dẫn template override.
 * Plugin sẽ tìm template trong thư mục wp-hotel-booking/ của theme.
 */
function bookingroom_wphb_template_path( $template_path ) {
    return 'wp-hotel-booking/';
}
add_filter( 'tp_hotel_booking_template_path', 'bookingroom_wphb_template_path' );

/**
 * Thêm thư mục template của theme vào plugin's template loader.
 */
function bookingroom_wphb_add_template_dir( $dirs ) {
    $dirs[] = get_stylesheet_directory() . '/wp-hotel-booking/';
    return $dirs;
}
add_filter( 'tp_hotel_booking_template_dirs', 'bookingroom_wphb_add_template_dir' );

// ─── Helper Functions ────────────────────────────────────────────────────────

/**
 * Lấy giá phòng hb_room theo plugin WP Hotel Booking.
 * Plugin lưu giá theo meta _hb_room_price hoặc _tp_room_price.
 *
 * @param  int $post_id  ID của hb_room
 * @return float
 */
function bookingroom_get_hbroom_price( $post_id ) {
    // Thử các meta key phổ biến của plugin
    $price = get_post_meta( $post_id, '_hb_room_price', true );
    if ( ! $price ) {
        $price = get_post_meta( $post_id, '_tp_room_price', true );
    }
    if ( ! $price ) {
        $price = get_post_meta( $post_id, 'tp_room_price', true );
    }
    if ( ! $price && function_exists( 'hb_get_room_base_price' ) ) {
        $price = hb_get_room_base_price( $post_id );
    }
    return floatval( $price ) ?: 1500000;
}

/**
 * Lấy sức chứa phòng hb_room.
 *
 * @param  int $post_id
 * @return int
 */
function bookingroom_get_hbroom_capacity( $post_id ) {
    $capacity = get_post_meta( $post_id, '_hb_room_capacity', true );
    if ( ! $capacity ) {
        $capacity = get_post_meta( $post_id, 'tp_room_capacity', true );
    }
    if ( ! $capacity && function_exists( 'hb_room_max_child' ) ) {
        $adults   = absint( hb_room_max_child( $post_id, 'adult' ) );
        $children = absint( hb_room_max_child( $post_id, 'children' ) );
        $capacity = $adults + $children;
    }
    return absint( $capacity ) ?: 2;
}

/**
 * Lấy số phòng còn trống cho hb_room theo ngày.
 *
 * @param  int    $post_id
 * @param  string $check_in   YYYY-MM-DD
 * @param  string $check_out  YYYY-MM-DD
 * @return int
 */
function bookingroom_get_hbroom_available( $post_id, $check_in, $check_out ) {
    // Dùng API của plugin nếu có
    if ( function_exists( 'tp_hotel_booking_available_rooms' ) ) {
        return absint( tp_hotel_booking_available_rooms( $post_id, $check_in, $check_out ) );
    }
    if ( function_exists( 'hb_room_available' ) ) {
        return absint( hb_room_available( $post_id, $check_in, $check_out ) );
    }
    // Fallback: dùng hàm của theme cũ nếu tồn tại
    if ( function_exists( 'bookingroom_get_available_rooms' ) ) {
        return bookingroom_get_available_rooms( $post_id, $check_in, $check_out );
    }
    return 1; // Mặc định còn trống
}

/**
 * Lấy URL trang checkout của WP Hotel Booking.
 *
 * @return string
 */
function bookingroom_get_hb_checkout_url() {
    if ( function_exists( 'hb_get_checkout_url' ) ) {
        return hb_get_checkout_url();
    }
    $checkout_page = get_option( 'tp_hotel_booking_checkout_page' );
    if ( $checkout_page ) {
        return get_permalink( $checkout_page );
    }
    return home_url( '/checkout/' );
}

/**
 * Lấy URL trang giỏ hàng của WP Hotel Booking.
 *
 * @return string
 */
function bookingroom_get_hb_cart_url() {
    if ( function_exists( 'hb_get_cart_url' ) ) {
        return hb_get_cart_url();
    }
    $cart_page = get_option( 'tp_hotel_booking_cart_page' );
    if ( $cart_page ) {
        return get_permalink( $cart_page );
    }
    return home_url( '/cart/' );
}

/**
 * Lấy archive URL của hb_room.
 *
 * @return string
 */
function bookingroom_get_hbroom_archive_url() {
    $url = get_post_type_archive_link( 'hb_room' );
    return $url ?: get_post_type_archive_link( 'room' ) ?: home_url( '/hotel-room/' );
}

/**
 * Render form đặt phòng inline (search form).
 * Dùng trong page-booking.php và trang chủ.
 *
 * @param array $args
 */
function bookingroom_render_hb_search_form( $args = [] ) {
    $defaults = [
        'check_in'  => '',
        'check_out' => '',
        'adults'    => 2,
        'children'  => 0,
        'action'    => bookingroom_get_hbroom_archive_url(),
    ];
    $args = wp_parse_args( $args, $defaults );

    // Nếu plugin có shortcode search form → dùng luôn
    if ( shortcode_exists( 'hotel_booking_search_form' ) ) {
        echo do_shortcode( '[hotel_booking_search_form]' );
        return;
    }

    // Fallback: Custom form gửi GET đến archive hb_room
    ?>
    <form action="<?php echo esc_url( $args['action'] ); ?>" method="get" class="hb-search-form">
        <input type="hidden" name="post_type" value="hb_room">
        <input type="hidden" name="hb_search" value="1">
        <div class="hb-search-row">
            <div class="hb-search-field">
                <label><?php esc_html_e( 'Ngày nhận phòng', 'bookingroom' ); ?></label>
                <input type="date" name="check_in" value="<?php echo esc_attr( $args['check_in'] ); ?>" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="hb-search-field">
                <label><?php esc_html_e( 'Ngày trả phòng', 'bookingroom' ); ?></label>
                <input type="date" name="check_out" value="<?php echo esc_attr( $args['check_out'] ); ?>" required>
            </div>
            <div class="hb-search-field">
                <label><?php esc_html_e( 'Người lớn', 'bookingroom' ); ?></label>
                <select name="adults">
                    <?php for ( $i = 1; $i <= 6; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $args['adults'], $i ); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="hb-search-field">
                <label><?php esc_html_e( 'Trẻ em', 'bookingroom' ); ?></label>
                <select name="children">
                    <?php for ( $i = 0; $i <= 5; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $args['children'], $i ); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="hb-search-submit">
                <button type="submit"><?php esc_html_e( 'Kiểm tra phòng trống', 'bookingroom' ); ?></button>
            </div>
        </div>
    </form>
    <?php
}

/**
 * Thêm body class khi WP Hotel Booking active.
 */
function bookingroom_wphb_body_class( $classes ) {
    if ( bookingroom_is_wphb_active() ) {
        $classes[] = 'wphb-active';
    }
    return $classes;
}
add_filter( 'body_class', 'bookingroom_wphb_body_class' );

/**
 * Override styles của plugin – đảm bảo CSS theme được ưu tiên.
 */
function bookingroom_wphb_dequeue_plugin_styles() {
    // Bỏ comment nếu bạn muốn tắt CSS mặc định của plugin
    // wp_dequeue_style( 'tp-hotel-booking' );
    // wp_dequeue_style( 'hotel-booking' );
}
add_action( 'wp_enqueue_scripts', 'bookingroom_wphb_dequeue_plugin_styles', 100 );

// ─── Customizer ──────────────────────────────────────────────────────────────

/**
 * Thêm section Customizer cho WP Hotel Booking.
 */
function bookingroom_wphb_customizer( $wp_customize ) {
    $wp_customize->add_section( 'bookingroom_wphb', [
        'title'    => __( 'WP Hotel Booking', 'bookingroom' ),
        'priority' => 160,
    ] );

    // Bật/tắt integration
    $wp_customize->add_setting( 'bookingroom_wphb_enable', [
        'default'           => 'yes',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'bookingroom_wphb_enable', [
        'label'   => __( 'Kích hoạt tích hợp WP Hotel Booking', 'bookingroom' ),
        'section' => 'bookingroom_wphb',
        'type'    => 'select',
        'choices' => [
            'yes' => 'Có',
            'no'  => 'Không',
        ],
    ] );

    // Slug archive phòng
    $wp_customize->add_setting( 'bookingroom_hbroom_archive_slug', [
        'default'           => 'hotel-room',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'bookingroom_hbroom_archive_slug', [
        'label'       => __( 'Slug archive phòng (mặc định: hotel-room)', 'bookingroom' ),
        'section'     => 'bookingroom_wphb',
        'type'        => 'text',
    ] );
}
add_action( 'customize_register', 'bookingroom_wphb_customizer' );
