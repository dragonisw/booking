<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */

add_action('wp_enqueue_scripts', 'bookingroom_style');
function bookingroom_style()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array(), time());
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), time());

    // Add Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    // Enqueue Booking Script
    wp_enqueue_script('booking-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
    wp_localize_script('booking-script', 'booking_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('booking_nonce')
    ));

    // Enqueue API Booking Script
    wp_enqueue_script('api-booking-script', get_stylesheet_directory_uri() . '/assets/js/api-booking.js', array(), '1.0', true);

    // Google Maps API
    $api_key = get_theme_mod('google_maps_api_key', '');
    if (!empty($api_key)) {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places', array(), null, true);
    }

    // Swiper.js for sliders
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
}

/**
 * Include Room Pricing Helper (giá cuối tuần / trong tuần)
 */
require_once get_stylesheet_directory() . '/inc/room-pricing.php';

/**
 * Customer & Booking Manager
 * Quản lý thông tin khách hàng, xuất CSV, email thông báo
 */
require_once get_stylesheet_directory() . '/inc/customer-manager.php';

/**
 * Destination Custom Post Type
 * Điểm đến du lịch – quản lý trực tiếp trong Admin
 */
require_once get_stylesheet_directory() . '/inc/destination-cpt.php';

/**
 * Customer Review Custom Post Type
 * Đánh giá khách hàng
 */
require_once get_stylesheet_directory() . '/inc/review-cpt.php';

/**
 * WP Hotel Booking Integration (ThimPress)
 * Kết nối theme với plugin WP Hotel Booking – template overrides, helper functions
 * Plugin cần được cài và kích hoạt qua: Plugins → Add New → "WP Hotel Booking"
 */
require_once get_stylesheet_directory() . '/inc/wp-hotel-booking-integration.php';

/**
 * Room Availability System
 * Tìm phòng trống theo ngày: AJAX endpoint, price breakdown, shortcode [room_availability_form]
 */
require_once get_stylesheet_directory() . '/inc/room-availability.php';

/**
 * Room Admin Columns, Overview Page & Dashboard Widget
 * Tổng số phòng theo từng loại, lịch trạng thái, xuất CSV
 */
require_once get_stylesheet_directory() . '/inc/room-admin-columns.php';

/**
 * SMS Manager
 * Twilio SMS API integration & Customizer settings
 */
require_once get_stylesheet_directory() . '/inc/sms-manager.php';

/**
 * Multilingual System (No Plugin)
 * Handles custom language switching, content and string translation.
 */
require_once get_stylesheet_directory() . '/inc/multilingual.php';

/**
 * Google Reviews Integration
 * Fetches and displays reviews from Google Places API
 */
require_once get_stylesheet_directory() . '/inc/google-reviews.php';

/**
 * Location Page Meta Boxes
 * Cho phép chỉnh sửa toàn bộ nội dung trang Vị trí qua WordPress Editor
 */
require_once get_stylesheet_directory() . '/inc/location-metabox.php';

/**
 * 3rd-party Booking API Integration
 * Kết nối với hệ thống Booking Engine / OTA / Channel Manager
 */
require_once get_stylesheet_directory() . '/inc/api-integration/class-booking-api-client.php';
require_once get_stylesheet_directory() . '/inc/api-integration/class-booking-rest-controller.php';

// Khởi tạo REST Controller
add_action( 'rest_api_init', function() {
    $controller = new BookingRoom_REST_Controller();
    $controller->register_routes();
} );

// Đăng ký Customizer Settings cho Booking API
add_action( 'customize_register', function( $wp_customize ) {
    $wp_customize->add_section( 'bookingroom_api_section', array(
        'title'       => __( 'Booking API Integration', 'bookingroom' ),
        'description' => __( 'Cấu hình kết nối hệ thống Booking Engine (Channel Manager).', 'bookingroom' ),
        'priority'    => 35,
    ) );

    // API Endpoint
    $wp_customize->add_setting( 'booking_api_endpoint', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'booking_api_endpoint', array(
        'label'       => __( 'API Endpoint URL', 'bookingroom' ),
        'description' => __( 'Ví dụ: https://api.cloudbeds.com/api/v1.1/', 'bookingroom' ),
        'section'     => 'bookingroom_api_section',
        'type'        => 'url',
    ) );

    // API Key
    $wp_customize->add_setting( 'booking_api_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'booking_api_key', array(
        'label'       => __( 'API Key / Access Token', 'bookingroom' ),
        'section'     => 'bookingroom_api_section',
        'type'        => 'text',
    ) );
} );


/**
 * Enqueue Admin Scripts for Media Uploader
 */
function bookingroom_admin_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('bookingroom-admin-js', get_stylesheet_directory_uri() . '/assets/js/admin.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'bookingroom_admin_scripts');

/**
 * Register navigation menus
 */
function bookingroom_register_menus()
{
    register_nav_menus(
        array(
            'primary-menu'           => __('Primary Menu', 'bookingroom'),
            'primary-menu-en'        => __('Primary Menu (English)', 'bookingroom'),
            'footer-explore'         => __('Footer – Khám phá', 'bookingroom'),
            'footer-explore-en'      => __('Footer – Khám phá (English)', 'bookingroom'),
            'footer-support'         => __('Footer – Hỗ trợ', 'bookingroom'),
            'footer-support-en'      => __('Footer – Hỗ trợ (English)', 'bookingroom'),
            'footer-destination'     => __('Footer – Điểm đến', 'bookingroom'),
            'footer-destination-en'  => __('Footer – Điểm đến (English)', 'bookingroom'),
        )
    );
}
add_action('init', 'bookingroom_register_menus');

add_theme_support('title-tag');
add_theme_support('custom-logo', array(
    'height'      => 100,
    'width'       => 400,
    'flex-height' => true,
    'flex-width'  => true,
    'header-text' => array('site-title', 'site-description'),
));

/**
 * Register Custom Post Types
 */
function bookingroom_register_cpts()
{
    // Hotel Post Type
    register_post_type('hotel', array(
        'labels' => array(
            'name' => __('Khách sạn', 'bookingroom'),
            'singular_name' => __('Khách sạn', 'bookingroom'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-building',
        'rewrite' => array('slug' => 'hotels'),
    ));

    // Room Post Type
    register_post_type('room', array(
        'labels' => array(
            'name' => __('Phòng', 'bookingroom'),
            'singular_name' => __('Phòng', 'bookingroom'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-admin-home',
        'rewrite' => array('slug' => 'rooms'),
    ));

    // Booking Post Type
    register_post_type('booking', array(
        'labels' => array(
            'name' => __('Đặt phòng', 'bookingroom'),
            'singular_name' => __('Đặt phòng', 'bookingroom'),
        ),
        'public' => false,
        'show_ui' => true,
        'supports' => array('title', 'custom-fields'),
        'menu_icon' => 'dashicons-calendar-alt',
    ));

    // Room Category Taxonomy
    register_taxonomy('room_category', 'room', array(
        'labels' => array(
            'name' => __('Loại phòng', 'bookingroom'),
            'singular_name' => __('Loại phòng', 'bookingroom'),
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'room-type'),
    ));
}
add_action('init', 'bookingroom_register_cpts');

/**
 * AJAX Handler for Bookings
 */
function bookingroom_process_booking()
{
    check_ajax_referer('booking_nonce', 'nonce');

    $room_id = intval($_POST['room_id']);
    $check_in = sanitize_text_field($_POST['check_in']);
    $check_out = sanitize_text_field($_POST['check_out']);
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 1;
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $user_id = get_current_user_id();

    if (!$room_id || !$check_in || !$check_out || !$phone) {
        wp_send_json_error(array('message' => 'Dữ liệu không hợp lệ. Vui lòng điền đầy đủ các thông tin bắt buộc.'));
    }

    // Create Booking Post
    $booking_id = wp_insert_post(array(
        'post_type' => 'booking',
        'post_status' => 'publish',
        'post_title' => 'Đặt phòng #' . time() . ' - ' . $name,
        'post_author' => $user_id ?: 1, // Default to admin if guest
    ));

    if (!is_wp_error($booking_id)) {
        update_post_meta($booking_id, '_room_id', $room_id);
        update_post_meta($booking_id, '_check_in', $check_in);
        update_post_meta($booking_id, '_check_out', $check_out);
        update_post_meta($booking_id, '_guests', $guests);
        update_post_meta($booking_id, '_customer_name', $name);
        update_post_meta($booking_id, '_phone', $phone);
        update_post_meta($booking_id, '_email', $email);
        update_post_meta($booking_id, '_status', 'pending');
        if (isset($_POST['selected_rooms'])) {
            update_post_meta($booking_id, '_selected_rooms', sanitize_text_field($_POST['selected_rooms']));
        }

        wp_send_json_success(array(
            'message' => 'Đặt phòng thành công! Mã đặt phòng của bạn là: ' . $booking_id,
            'booking_id' => $booking_id
        ));
    } else {
        wp_send_json_error(array('message' => 'Lỗi hệ thống, vui lòng thử lại.'));
    }
}
add_action('wp_ajax_process_booking', 'bookingroom_process_booking');
add_action('wp_ajax_nopriv_process_booking', 'bookingroom_process_booking');

/**
 * AJAX Handler for Booking Lookup
 */
function bookingroom_lookup_booking()
{
    check_ajax_referer('booking_nonce', 'nonce');

    $phone = sanitize_text_field($_POST['phone']);
    $booking_id = intval($_POST['booking_id']);

    if (!$phone || !$booking_id) {
        wp_send_json_error(array('message' => 'Vui lòng nhập đầy đủ thông tin.'));
    }

    $booking = get_post($booking_id);

    if (!$booking || $booking->post_type !== 'booking') {
        wp_send_json_error(array('message' => 'Không tìm thấy mã đặt phòng này.'));
    }

    $stored_phone = get_post_meta($booking_id, '_phone', true);

    if ($stored_phone !== $phone) {
        wp_send_json_error(array('message' => 'Thông tin số điện thoại không khớp với mã đặt phòng.'));
    }

    $room_id = get_post_meta($booking_id, '_room_id', true);
    $check_in = get_post_meta($booking_id, '_check_in', true);
    $check_out = get_post_meta($booking_id, '_check_out', true);
    $status = get_post_meta($booking_id, '_status', true) ?: $booking->post_status;
    $customer_name = get_post_meta($booking_id, '_customer_name', true) ?: 'Khách hàng';

    wp_send_json_success(array(
        'booking_id' => $booking_id,
        'customer_name' => $customer_name,
        'room_title' => get_the_title($room_id),
        'check_in' => $check_in,
        'check_out' => $check_out,
        'status' => $status
    ));
}
add_action('wp_ajax_lookup_booking', 'bookingroom_lookup_booking');
add_action('wp_ajax_nopriv_lookup_booking', 'bookingroom_lookup_booking');

/**
 * Add Room Meta Boxes
 */
function bookingroom_add_room_meta_boxes()
{
    // Meta box chính – Thông tin & Giá phòng
    add_meta_box(
        'room_details',
        '🛏️ Thông tin & Giá phòng',
        'bookingroom_room_details_callback',
        'room',
        'normal',
        'high'
    );

    // Meta box bên – Số lượng & Số hiệu phòng
    add_meta_box(
        'room_inventory',
        '🔢 Số lượng & Số hiệu phòng',
        'bookingroom_room_inventory_callback',
        'room',
        'side',
        'high'
    );

    // Meta box bên – Lịch trạng thái phòng
    add_meta_box(
        'room_availability_calendar',
        '📅 Tình trạng đặt phòng (30 ngày tới)',
        'bookingroom_room_availability_cb',
        'room',
        'normal',
        'default'
    );

    // Tiện nghi & Chính sách phòng
    add_meta_box(
        'room_features',
        '✨ Tiện nghi & Chính sách phòng',
        'bookingroom_room_features_callback',
        'room',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bookingroom_add_room_meta_boxes');

function bookingroom_room_details_callback( $post ) {
    wp_nonce_field( 'bookingroom_save_room_details', 'bookingroom_room_details_nonce' );

    $price          = get_post_meta( $post->ID, '_price',          true );
    $weekend_price  = get_post_meta( $post->ID, '_weekend_price',  true );
    $capacity       = get_post_meta( $post->ID, '_capacity',       true );
    $room_label     = get_post_meta( $post->ID, '_room_label',     true );
    $engine_room_id = get_post_meta( $post->ID, '_engine_room_id', true );

    $s_card  = 'background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 18px;margin-bottom:14px;';
    $s_label = 'font-weight:700;display:block;margin-bottom:5px;font-size:13px;color:#1e293b;';
    $s_input = 'width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;font-family:inherit;background:#fff;box-sizing:border-box;';
    $s_desc  = 'display:block;color:#94a3b8;font-size:11.5px;margin-top:4px;';
    $s_grid  = 'display:grid;grid-template-columns:1fr 1fr;gap:14px;';
    ?>

    <style>
    #room_details .inside input[type=number]:focus,
    #room_details .inside input[type=text]:focus,
    #room_details .inside textarea:focus {
        border-color:#d35400!important;
        outline:none;
        box-shadow:0 0 0 3px rgba(211,84,0,0.12);
    }
    .room-section-title {
        font-size:12px;font-weight:800;text-transform:uppercase;
        letter-spacing:.07em;color:#d35400;
        margin:18px 0 10px;padding-bottom:6px;
        border-bottom:2px solid #ffedd5;
    }
    </style>

    <!-- Giá phòng -->
    <div style="<?php echo $s_card; ?>">
        <div class="room-section-title">💰 Cài đặt giá</div>
        <div style="<?php echo $s_grid; ?>">
            <div>
                <label style="<?php echo $s_label; ?>">Giá trong tuần (T2–T5) <span style="color:red">*</span></label>
                <input type="number" name="room_price" id="room_price"
                    value="<?php echo esc_attr( $price ); ?>"
                    style="<?php echo $s_input; ?>" placeholder="VD: 1500000" min="0">
                <span style="<?php echo $s_desc; ?>">đ / đêm · Thứ 2, 3, 4, 5</span>
            </div>
            <div>
                <label style="<?php echo $s_label; ?>">Giá cuối tuần (T6–CN)</label>
                <input type="number" name="room_weekend_price" id="room_weekend_price"
                    value="<?php echo esc_attr( $weekend_price ); ?>"
                    style="<?php echo $s_input; ?>" placeholder="Để trống = giống giá trong tuần" min="0">
                <span style="<?php echo $s_desc; ?>">đ / đêm · Thứ 6, 7, Chủ nhật</span>
            </div>
        </div>
        <?php if ( $price ) : ?>
        <div style="margin-top:10px;padding:10px 14px;background:linear-gradient(135deg,#fff7ed,#ffedd5);border-radius:8px;font-size:12px;color:#92400e;">
            💡 Giá trong tuần: <strong><?php echo number_format( $price ); ?>đ</strong>
            <?php if ( $weekend_price ) : ?> · Cuối tuần: <strong><?php echo number_format( $weekend_price ); ?>đ</strong><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Thông tin cơ bản -->
    <div style="<?php echo $s_card; ?>">
        <div class="room-section-title">📋 Thông tin cơ bản</div>
        <div style="<?php echo $s_grid; ?>">
            <div>
                <label style="<?php echo $s_label; ?>">Sức chứa tối đa (người)</label>
                <input type="number" name="room_capacity" id="room_capacity"
                    value="<?php echo esc_attr( $capacity ); ?>"
                    style="<?php echo $s_input; ?>" placeholder="VD: 2" min="1">
            </div>
            <div>
                <label style="<?php echo $s_label; ?>">Nhãn loại phòng</label>
                <input type="text" name="room_label" id="room_label"
                    value="<?php echo esc_attr( $room_label ); ?>"
                    style="<?php echo $s_input; ?>" placeholder="VD: Deluxe Room, Suite...">
            </div>
        </div>
    </div>

    <!-- Booking Engine -->
    <div style="<?php echo $s_card; ?>">
        <div class="room-section-title">🔗 Booking Engine bên ngoài</div>
        <label style="<?php echo $s_label; ?>">ID Phòng trên Booking Engine (ezCloud, Cloudbeds...)</label>
        <input type="text" name="engine_room_id" id="engine_room_id"
            value="<?php echo esc_attr( $engine_room_id ); ?>"
            style="<?php echo $s_input; ?>" placeholder="Để trống nếu dùng hệ thống nội bộ">
        <span style="<?php echo $s_desc; ?>">Dùng để link thẳng đến loại phòng này trên engine ngoài.</span>
    </div>
    <?php
}

// ── Inventory Meta Box (Sidebar) ──────────────────────────────────────────
function bookingroom_room_inventory_callback( $post ) {
    $room_numbers = get_post_meta( $post->ID, '_room_numbers', true );
    $room_quantity = get_post_meta( $post->ID, '_room_quantity', true );

    // Tính số lượng từ _room_numbers nếu có
    $numbers_arr = [];
    if ( $room_numbers ) {
        $numbers_arr = array_filter( array_map( 'trim', explode( ',', $room_numbers ) ) );
    }
    $auto_count = count( $numbers_arr );

    // Tổng hiệu lực
    $total_units = $auto_count > 0 ? $auto_count : ( (int) $room_quantity ?: 10 );

    // Đặt phòng hiện tại (đang trong khoảng hôm nay)
    $today      = date( 'Y-m-d' );
    $active_bks = new WP_Query([
        'post_type'      => 'booking',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'AND',
            [ 'key' => '_room_id', 'value' => $post->ID, 'compare' => '=' ],
            [ 'key' => '_check_in',  'value' => $today, 'compare' => '<=', 'type' => 'DATE' ],
            [ 'key' => '_check_out', 'value' => $today, 'compare' => '>',  'type' => 'DATE' ],
            [ 'key' => '_status', 'value' => ['cancelled'], 'compare' => 'NOT IN' ],
        ],
    ]);
    $occupied_now = $active_bks->found_posts;
    $free_now     = max( 0, $total_units - $occupied_now );

    $s_input = 'width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;box-sizing:border-box;background:#fff;';
    ?>

    <style>
    #room_inventory .inside { padding:10px 12px; }
    #room_inventory input:focus, #room_inventory textarea:focus {
        border-color:#d35400!important;outline:none;
        box-shadow:0 0 0 3px rgba(211,84,0,0.1);
    }
    .br-inv-stat {
        display:flex;align-items:center;justify-content:space-between;
        padding:8px 10px;border-radius:8px;margin-bottom:6px;font-size:12px;
    }
    .br-tag { display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700; }
    .br-number-tag {
        display:inline-flex;align-items:center;justify-content:center;
        width:34px;height:26px;background:#f1f5f9;border:1.5px solid #e2e8f0;
        border-radius:6px;font-size:12px;font-weight:700;color:#1e293b;
        cursor:pointer;transition:all .15s;
        text-decoration:none;
    }
    .br-number-tag:hover { background:#ffedd5;border-color:#d35400;color:#d35400; }
    .br-number-tag.occupied { background:#fee2e2;border-color:#fca5a5;color:#dc2626; }
    .br-number-tag.available { background:#dcfce7;border-color:#86efac;color:#16a34a; }
    #br-room-numbers-preview { display:flex;flex-wrap:wrap;gap:5px;margin-top:8px; }
    </style>

    <!-- Tổng số phòng -->
    <p style="margin:0 0 12px;">
        <label style="font-weight:700;font-size:12px;display:block;margin-bottom:5px;color:#475569;text-transform:uppercase;letter-spacing:.05em;">
            📦 Tổng số phòng vật lý
        </label>
        <input type="number" name="room_quantity" id="room_quantity"
            value="<?php echo esc_attr( $room_quantity ?: $auto_count ?: 10 ); ?>"
            min="1" max="9999" style="<?php echo $s_input; ?>">
        <span style="display:block;color:#94a3b8;font-size:11px;margin-top:3px;">
            ⚠ Nếu nhập danh sách số hiệu bên dưới, hệ thống sẽ dùng số lượng từ danh sách đó.
        </span>
    </p>

    <!-- Tình trạng hiện tại -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;">
        <div class="br-inv-stat" style="background:#dcfce7;">
            <span style="color:#15803d;font-weight:700;font-size:18px;"><?php echo $free_now; ?></span>
            <span style="color:#16a34a;font-size:11px;font-weight:600;">Trống hôm nay</span>
        </div>
        <div class="br-inv-stat" style="background:#fee2e2;">
            <span style="color:#b91c1c;font-weight:700;font-size:18px;"><?php echo $occupied_now; ?></span>
            <span style="color:#dc2626;font-size:11px;font-weight:600;">Đang có khách</span>
        </div>
    </div>

    <hr style="margin:0 0 12px;border:none;border-top:1px solid #f1f5f9;">

    <!-- Số hiệu phòng -->
    <label style="font-weight:700;font-size:12px;display:block;margin-bottom:5px;color:#475569;text-transform:uppercase;letter-spacing:.05em;">
        🔑 Danh sách số hiệu phòng
    </label>
    <textarea name="room_numbers" id="room_numbers" rows="4"
        style="<?php echo $s_input; ?>resize:vertical;font-family:monospace;"
        placeholder="VD: 101, 102, 103, 201, 202"><?php echo esc_textarea( $room_numbers ); ?></textarea>
    <span style="display:block;color:#94a3b8;font-size:11px;margin-top:3px;">
        Cách nhau bởi dấu phẩy. Hệ thống dùng để hiển thị chọn phòng khi đặt.
    </span>

    <!-- Tự tạo số hiệu -->
    <div style="margin-top:12px;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
        <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;">⚡ Tự tạo nhanh số hiệu</div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <div style="font-size:10px;color:#94a3b8;margin-bottom:3px;">Từ số</div>
                <input type="number" id="gen_from" value="101" min="1" style="width:62px;padding:5px 7px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;">
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;margin-bottom:3px;">Đến số</div>
                <input type="number" id="gen_to" value="<?php echo esc_attr( $room_quantity ? 100 + $room_quantity : 110 ); ?>" min="1" style="width:62px;padding:5px 7px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;">
            </div>
            <div>
                <div style="font-size:10px;color:#94a3b8;margin-bottom:3px;">Tiền tố</div>
                <input type="text" id="gen_prefix" value="" placeholder="VD: P, R" style="width:50px;padding:5px 7px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;">
            </div>
            <button type="button" id="gen-room-numbers-btn"
                style="padding:6px 12px;background:#d35400;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                Tạo
            </button>
        </div>
    </div>

    <!-- Preview tags -->
    <?php if ( ! empty( $numbers_arr ) ) : ?>
    <div style="margin-top:12px;">
        <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">📌 Số hiệu hiện tại (<?php echo count($numbers_arr); ?> phòng)</div>
        <div id="br-room-numbers-preview">
            <?php
            // Lấy các phòng đang có booking hôm nay
            $booked_rooms_raw = [];
            if ( $active_bks->found_posts > 0 ) {
                foreach ( $active_bks->posts as $bid ) {
                    $sel = get_post_meta( $bid, '_selected_rooms', true );
                    if ( $sel ) {
                        foreach ( array_map( 'trim', explode( ',', $sel ) ) as $rn ) {
                            $booked_rooms_raw[] = $rn;
                        }
                    }
                }
            }
            foreach ( $numbers_arr as $rnum ) :
                $is_occ = in_array( $rnum, $booked_rooms_raw );
                $cls    = $is_occ ? 'occupied' : 'available';
                $title  = $is_occ ? 'Đang có khách' : 'Còn trống';
            ?>
                <span class="br-number-tag <?php echo $cls; ?>" title="<?php echo $title; ?>"><?php echo esc_html( $rnum ); ?></span>
            <?php endforeach; ?>
        </div>
        <div style="font-size:10px;color:#94a3b8;margin-top:6px;">
            <span style="background:#fee2e2;border:1px solid #fca5a5;padding:1px 5px;border-radius:4px;color:#dc2626;">Đỏ</span> = đang có khách &nbsp;
            <span style="background:#dcfce7;border:1px solid #86efac;padding:1px 5px;border-radius:4px;color:#16a34a;">Xanh</span> = còn trống
        </div>
    </div>
    <?php endif; ?>

    <script>
    (function() {
        // Tự tạo số hiệu phòng
        document.getElementById('gen-room-numbers-btn').addEventListener('click', function() {
            var from   = parseInt(document.getElementById('gen_from').value) || 1;
            var to     = parseInt(document.getElementById('gen_to').value)   || 1;
            var prefix = document.getElementById('gen_prefix').value.trim();
            if (to < from) { alert('Số cuối phải lớn hơn số đầu.'); return; }
            if (to - from > 499) { alert('Tối đa 500 số hiệu.'); return; }
            var nums = [];
            for (var i = from; i <= to; i++) nums.push(prefix + i);
            var ta = document.getElementById('room_numbers');
            var existing = ta.value.trim();
            ta.value = existing ? existing + ', ' + nums.join(', ') : nums.join(', ');
            // Cập nhật input tổng số phòng
            var allNums = ta.value.split(',').filter(function(s){return s.trim();});
            document.getElementById('room_quantity').value = allNums.length;
            ta.focus();
        });

        // Đồng bộ số lượng khi sửa textarea
        document.getElementById('room_numbers').addEventListener('input', function() {
            var nums = this.value.split(',').filter(function(s){return s.trim();});
            document.getElementById('room_quantity').value = nums.length > 0 ? nums.length : '';
        });
    })();
    </script>
    <?php
}

// ── Lịch tình trạng phòng (30 ngày tới) ─────────────────────────────────
function bookingroom_room_availability_cb( $post ) {
    $room_id   = $post->ID;
    $today     = new DateTime( 'today' );
    $end       = new DateTime( '+30 days' );

    // Lấy tất cả booking đang active trong 30 ngày tới
    $bookings = get_posts([
        'post_type'      => 'booking',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            'relation' => 'AND',
            [ 'key' => '_room_id',  'value' => $room_id,               'compare' => '=' ],
            [ 'key' => '_check_in',  'value' => $end->format('Y-m-d'), 'compare' => '<',  'type' => 'DATE' ],
            [ 'key' => '_check_out', 'value' => $today->format('Y-m-d'), 'compare' => '>', 'type' => 'DATE' ],
            [ 'key' => '_status',   'value' => ['cancelled'],           'compare' => 'NOT IN' ],
        ],
    ]);

    // Xây dựng map ngày → danh sách booking
    $day_map = [];
    foreach ( $bookings as $b ) {
        $ci = new DateTime( get_post_meta( $b->ID, '_check_in',  true ) );
        $co = new DateTime( get_post_meta( $b->ID, '_check_out', true ) );
        $st = get_post_meta( $b->ID, '_status', true ) ?: 'pending';
        $nm = get_post_meta( $b->ID, '_customer_name', true ) ?: '—';
        $cur = clone $ci;
        while ( $cur < $co ) {
            $dk = $cur->format( 'Y-m-d' );
            if ( ! isset( $day_map[$dk] ) ) $day_map[$dk] = [];
            $day_map[$dk][] = [ 'id' => $b->ID, 'name' => $nm, 'status' => $st ];
            $cur->modify( '+1 day' );
        }
    }

    $status_colors = [
        'pending'    => [ 'bg' => '#fef3c7', 'dot' => '#f59e0b', 'txt' => '#92400e' ],
        'confirmed'  => [ 'bg' => '#d1fae5', 'dot' => '#10b981', 'txt' => '#065f46' ],
        'checked_in' => [ 'bg' => '#dbeafe', 'dot' => '#3b82f6', 'txt' => '#1e40af' ],
        'checked_out'=> [ 'bg' => '#ede9fe', 'dot' => '#8b5cf6', 'txt' => '#4c1d95' ],
        'cancelled'  => [ 'bg' => '#fee2e2', 'dot' => '#ef4444', 'txt' => '#7f1d1d' ],
    ];
    $status_labels = [
        'pending'    => 'Chờ XN',
        'confirmed'  => 'Đã XN',
        'checked_in' => 'Nhận phòng',
        'checked_out'=> 'Trả phòng',
        'cancelled'  => 'Đã huỷ',
    ];
    ?>

    <style>
    .br-cal-grid {
        display:grid;
        grid-template-columns:repeat(7,1fr);
        gap:4px;
        margin-top:12px;
    }
    .br-cal-head {
        text-align:center;font-size:11px;font-weight:700;
        color:#94a3b8;padding:4px 0;text-transform:uppercase;
    }
    .br-cal-day {
        border:1.5px solid #f1f5f9;border-radius:8px;
        padding:6px 4px;min-height:60px;
        font-size:11px;position:relative;
        background:#fff;transition:border-color .15s;
    }
    .br-cal-day:hover { border-color:#d35400; }
    .br-cal-day.today { border-color:#d35400;background:#fff7ed; }
    .br-cal-day.has-booking { background:#f0fdf4; }
    .br-cal-day.full { background:#fef2f2;border-color:#fca5a5; }
    .br-cal-day__num {
        font-weight:800;font-size:13px;color:#1e293b;
        display:block;margin-bottom:3px;
    }
    .br-cal-day.today .br-cal-day__num { color:#d35400; }
    .br-cal-booking {
        display:block;font-size:9px;font-weight:700;
        padding:1.5px 4px;border-radius:3px;
        margin-bottom:2px;white-space:nowrap;
        overflow:hidden;text-overflow:ellipsis;
        max-width:100%;cursor:pointer;
    }
    .br-cal-legend { display:flex;flex-wrap:wrap;gap:8px;margin-top:12px; }
    .br-cal-legend-item { display:flex;align-items:center;gap:4px;font-size:11px;color:#64748b; }
    .br-cal-legend-dot { width:8px;height:8px;border-radius:50%; }
    .br-avail-bar {
        display:flex;align-items:center;gap:8px;
        padding:10px 14px;background:#f8fafc;
        border-radius:8px;margin-bottom:14px;
        font-size:12px;border:1px solid #e2e8f0;
    }
    </style>

    <?php
    // Thống kê tổng quan
    $total_units = function_exists( 'bookingroom_get_room_total_units' )
        ? bookingroom_get_room_total_units( $room_id )
        : ( (int) get_post_meta( $room_id, '_room_quantity', true ) ?: 10 );
    $booked_days = count( $day_map );
    ?>

    <div class="br-avail-bar">
        <div style="width:10px;height:10px;border-radius:50%;background:#10b981;"></div>
        <span>Tổng <?php echo $total_units; ?> phòng ·
            <strong style="color:#16a34a;"><?php echo max(0, $booked_days); ?> ngày</strong> có booking trong 30 ngày tới
        </span>
        <a href="<?php echo admin_url('edit.php?post_type=booking&room_filter=' . $room_id); ?>"
           style="margin-left:auto;color:#2563eb;font-weight:600;text-decoration:none;font-size:11px;">
            Xem tất cả →
        </a>
    </div>

    <?php
    // Render lịch
    $dow_labels = [ 'CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7' ];
    $cal_start  = clone $today;
    // Đưa về đầu tuần (CN)
    $start_dow  = (int) $cal_start->format( 'w' );
    $cal_start->modify( '-' . $start_dow . ' days' );

    echo '<div class="br-cal-grid">';
    foreach ( $dow_labels as $dl ) {
        echo '<div class="br-cal-head">' . $dl . '</div>';
    }

    $cur = clone $cal_start;
    $today_str = $today->format( 'Y-m-d' );
    $end_str   = $end->format( 'Y-m-d' );

    while ( $cur <= $end ) {
        $dk      = $cur->format( 'Y-m-d' );
        $day_num = $cur->format( 'j' );
        $month   = $cur->format( 'n' );
        $bks     = $day_map[$dk] ?? [];
        $is_today = $dk === $today_str;
        $in_range = $dk >= $today_str && $dk <= $end_str;
        $n_booked = count( $bks );
        $n_free   = max( 0, $total_units - $n_booked );

        $cls = 'br-cal-day';
        if ( $is_today ) $cls .= ' today';
        if ( $n_booked > 0 && $n_free > 0 ) $cls .= ' has-booking';
        if ( $n_free === 0 && $n_booked > 0 ) $cls .= ' full';
        if ( ! $in_range ) $cls .= '' ; // past days lighter

        echo '<div class="' . $cls . '">';
        echo '<span class="br-cal-day__num">' . $day_num;
        if ( $month != date('n') ) echo '<small style="color:#94a3b8;font-weight:400;font-size:9px;">/' . $month . '</small>';
        echo '</span>';

        // Show bookings
        $shown = 0;
        foreach ( $bks as $bk ) {
            if ( $shown >= 2 ) {
                echo '<span class="br-cal-booking" style="background:#f1f5f9;color:#64748b;">+' . ( count($bks) - 2 ) . ' nữa</span>';
                break;
            }
            $sc = $status_colors[$bk['status']] ?? $status_colors['pending'];
            $link = admin_url( 'post.php?post=' . $bk['id'] . '&action=edit' );
            echo '<a href="' . esc_url($link) . '" class="br-cal-booking" ';
            echo 'style="background:' . $sc['bg'] . ';color:' . $sc['txt'] . ';" ';
            echo 'title="#' . $bk['id'] . ' · ' . esc_attr($bk['name']) . '">';
            echo esc_html( mb_strimwidth( $bk['name'], 0, 8, '…' ) );
            echo '</a>';
            $shown++;
        }

        // Free rooms count
        if ( $in_range ) {
            $free_color = $n_free === 0 ? '#dc2626' : '#16a34a';
            echo '<span style="position:absolute;bottom:3px;right:4px;font-size:9px;font-weight:800;color:' . $free_color . ';">';
            echo $n_free . '/' . $total_units;
            echo '</span>';
        }

        echo '</div>';
        $cur->modify( '+1 day' );
    }
    echo '</div>';
    ?>

    <!-- Legend -->
    <div class="br-cal-legend">
        <?php foreach ( $status_colors as $sk => $sv ) : ?>
        <div class="br-cal-legend-item">
            <div class="br-cal-legend-dot" style="background:<?php echo $sv['dot']; ?>;"></div>
            <span><?php echo $status_labels[$sk] ?? $sk; ?></span>
        </div>
        <?php endforeach; ?>
        <div class="br-cal-legend-item" style="margin-left:auto;">
            <span style="font-size:10px;color:#94a3b8;">Số góc = <strong>trống/tổng</strong></span>
        </div>
    </div>
    <?php
}

function bookingroom_room_features_callback( $post ) {
    $amenities = get_post_meta( $post->ID, '_room_amenities', true ) ?: [];
    $policies = get_post_meta( $post->ID, '_room_policies', true );
    
    $common_amenities = [
        'wifi' => 'Wi-Fi tốc độ cao',
        'ac' => 'Điều hòa nhiệt độ',
        'tv' => 'Smart TV 4K',
        'bathtub' => 'Bồn tắm riêng',
        'safe' => 'Két an toàn',
        'minibar' => 'Minibar & Trà/Cà phê',
        'phone' => 'Điện thoại phòng',
        'balcony' => 'Ban công/Cửa sổ lớn',
        'laundry' => 'Dịch vụ giặt là'
    ];
    ?>
    <div style="padding:10px 0;">
        <p style="font-weight:700;margin-bottom:10px;color:#1e293b;">Tiện nghi phòng</p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;background:#f8fafc;padding:15px;border:1px solid #e2e8f0;border-radius:8px;">
            <?php foreach ( $common_amenities as $key => $label ) : ?>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                    <input type="checkbox" name="room_amenities[]" value="<?php echo esc_attr($key); ?>" <?php checked( in_array( $key, $amenities ) ); ?> style="margin:0;">
                    <span style="font-size:13px;"><?php echo esc_html($label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        
        <p style="font-weight:700;margin-bottom:10px;color:#1e293b;">Chính sách phòng</p>
        <?php 
        wp_editor( $policies, 'room_policies', array(
            'textarea_name' => 'room_policies',
            'media_buttons' => true,
            'textarea_rows' => 6,
            'teeny'         => true,
            'editor_css'    => '<style>.wp-editor-area{font-family:inherit;}</style>'
        ) );
        ?>
        <p style="color:#94a3b8;font-size:11px;margin-top:6px;">Chính sách hủy, yêu cầu nhận phòng... có thể gõ nội dung tùy ý ở đây.</p>
    </div>
    <?php
}

function bookingroom_save_room_details( $post_id ) {
    if ( ! isset( $_POST['bookingroom_room_details_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['bookingroom_room_details_nonce'], 'bookingroom_save_room_details' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Giá
    if ( isset( $_POST['room_price'] ) ) {
        update_post_meta( $post_id, '_price', absint( $_POST['room_price'] ) );
    }
    if ( isset( $_POST['room_weekend_price'] ) ) {
        $wp = trim( $_POST['room_weekend_price'] );
        $wp !== '' ? update_post_meta( $post_id, '_weekend_price', absint( $wp ) ) : delete_post_meta( $post_id, '_weekend_price' );
    }

    // Cơ bản
    $text_fields = [
        'room_capacity'   => '_capacity',
        'room_label'      => '_room_label',
        'engine_room_id'  => '_engine_room_id',
    ];
    foreach ( $text_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[$post_key] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[$post_key] ) );
        }
    }

    // Số lượng phòng
    if ( isset( $_POST['room_quantity'] ) ) {
        $qty = absint( $_POST['room_quantity'] );
        $qty > 0 ? update_post_meta( $post_id, '_room_quantity', $qty ) : delete_post_meta( $post_id, '_room_quantity' );
    }

    // Số hiệu phòng – làm sạch và chuẩn hóa
    if ( isset( $_POST['room_numbers'] ) ) {
        $raw     = sanitize_textarea_field( $_POST['room_numbers'] );
        $nums    = array_unique( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
        $cleaned = implode( ', ', $nums );
        $cleaned ? update_post_meta( $post_id, '_room_numbers', $cleaned ) : delete_post_meta( $post_id, '_room_numbers' );

        // Đồng bộ _room_quantity với số lượng thực tế
        if ( ! empty( $nums ) ) {
            update_post_meta( $post_id, '_room_quantity', count( $nums ) );
        }
    }

    // Tiện nghi & Chính sách
    if ( isset( $_POST['room_amenities'] ) && is_array( $_POST['room_amenities'] ) ) {
        $amenities = array_map( 'sanitize_text_field', $_POST['room_amenities'] );
        update_post_meta( $post_id, '_room_amenities', $amenities );
    } else {
        delete_post_meta( $post_id, '_room_amenities' );
    }

    if ( isset( $_POST['room_policies'] ) ) {
        update_post_meta( $post_id, '_room_policies', wp_kses_post( wp_unslash( $_POST['room_policies'] ) ) );
    }
}
add_action( 'save_post', 'bookingroom_save_room_details' );

/**
 * Add Home Page Meta Boxes (Classic Editor Style)
 */
function bookingroom_add_home_meta_boxes()
{
    $post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : false);
    if (!$post_id)
        return;

    $template = get_post_meta($post_id, '_wp_page_template', true);
    if ($template == 'page-trang-chu.php' || $template == 'page-destination.php') {
        if ($template == 'page-trang-chu.php') {
            add_meta_box('home_hero_section', '🎯 Thông tin Hero (Đầu trang)', 'bookingroom_home_hero_callback', 'page', 'normal', 'high');
            add_meta_box('home_about_section', '🏨 Section Giới thiệu (About)', 'bookingroom_home_about_callback', 'page', 'normal', 'high');
            add_meta_box('home_why_us_section', 'Thông tin Tại sao chọn chúng tôi', 'bookingroom_home_why_us_callback', 'page', 'normal', 'high');
        }
        add_meta_box('home_destinations_section', 'Danh sách Điểm đến (Chọn từ Gallery)', 'bookingroom_home_destinations_callback', 'page', 'normal', 'high');
    }

    // ── About Page Meta Boxes ────────────────────────────────────────────────
    if ($template == 'page-about.php') {
        add_meta_box('ab_hero_box',       '🖼️ Hero – Ảnh nền & Tiêu đề',        'bookingroom_about_hero_cb',       'page', 'normal', 'high');
        add_meta_box('ab_stats_box',      '📊 Thống kê (4 con số)',               'bookingroom_about_stats_cb',      'page', 'normal', 'high');
        add_meta_box('ab_story_box',      '📖 Câu chuyện của chúng tôi',          'bookingroom_about_story_cb',      'page', 'normal', 'high');
        add_meta_box('ab_features_box',   '✅ 3 Điểm nổi bật (trong section Story)', 'bookingroom_about_features_cb', 'page', 'normal', 'high');
        add_meta_box('ab_values_box',     '💎 Giá trị cốt lõi (3 thẻ)',          'bookingroom_about_values_cb',     'page', 'normal', 'high');
        add_meta_box('ab_milestones_box', '⏳ Cột mốc phát triển (4 mốc)',        'bookingroom_about_milestones_cb', 'page', 'normal', 'high');
        add_meta_box('ab_team_box',       '👥 Đội ngũ (3 thành viên)',            'bookingroom_about_team_cb',       'page', 'normal', 'high');
        add_meta_box('ab_testi_box',      '💬 Nhận xét khách hàng',               'bookingroom_about_testi_cb',      'page', 'normal', 'high');
        add_meta_box('ab_cta_box',        '🚀 CTA Banner cuối trang',             'bookingroom_about_cta_cb',        'page', 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'bookingroom_add_home_meta_boxes');

/* ── About page meta box helpers ── */

function _abp_input_row( $label, $name, $value, $type = 'text', $placeholder = '' ) {
    $s_input = 'width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-family:inherit;';
    echo '<p><label style="font-weight:700;display:block;margin-bottom:4px;font-size:13px;">' . esc_html($label) . '</label>';
    if ($type === 'textarea') {
        echo '<textarea name="' . esc_attr($name) . '" rows="4" style="' . $s_input . 'resize:vertical;">' . esc_textarea($value) . '</textarea>';
    } else {
        echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" style="' . $s_input . '">';
    }
    echo '</p>';
}

function _abp_section_title( $label ) {
    echo '<h3 style="margin:18px 0 10px;font-size:14px;color:#2563eb;border-bottom:2px solid #bfdbfe;padding-bottom:7px;">' . esc_html($label) . '</h3>';
}

function _abp_img_picker( $label, $name, $post_id ) {
    $id  = get_post_meta($post_id, $name, true);
    $url = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
    echo '<p><label style="font-weight:700;display:block;margin-bottom:6px;font-size:13px;">' . esc_html($label) . '</label>';
    echo '<div style="display:flex;align-items:center;gap:12px;">';
    if ($url) echo '<img src="' . esc_url($url) . '" style="width:80px;height:60px;object-fit:cover;border-radius:6px;border:2px solid #e2e8f0;">';
    echo '<div>';
    echo '<input type="hidden" name="' . esc_attr($name) . '" id="abp_' . esc_attr($name) . '" value="' . esc_attr($id) . '">';
    echo '<button type="button" class="button abp-media-btn" data-target="abp_' . esc_attr($name) . '">Chọn ảnh</button>';
    if ($id) echo ' <button type="button" class="button abp-media-remove" data-target="abp_' . esc_attr($name) . '" style="color:red;">✕ Xóa</button>';
    echo '</div></div></p>';
}

/* Hero callback */
function bookingroom_about_hero_cb($post) {
    wp_nonce_field('abp_save_meta', 'abp_nonce');
    $pid = $post->ID;
    _abp_section_title('Ảnh nền Hero');
    echo '<p><label style="font-weight:700;font-size:13px;">URL ảnh nền (hoặc upload bên dưới):</label><br>';
    echo '<input type="text" name="_about_hero_bg_url" value="' . esc_attr(get_post_meta($pid,'_about_hero_bg_url',true)) . '" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;" placeholder="https://..."></p>';
    _abp_img_picker('Hoặc chọn từ thư viện media', '_about_hero_bg_id', $pid);
    _abp_section_title('Nội dung');
    _abp_input_row('Tiêu đề (có thể dùng <span class="ab-blue">...</span> để đổi màu)', '_about_hero_title', get_post_meta($pid,'_about_hero_title',true), 'text', 'Về <span class="ab-blue">Chúng Tôi</span>');
    _abp_input_row('Mô tả ngắn', '_about_hero_subtitle', get_post_meta($pid,'_about_hero_subtitle',true), 'textarea');
    echo bookingroom_about_media_js();
}

/* Stats callback */
function bookingroom_about_stats_cb($post) {
    $pid = $post->ID;
    $style_row = 'display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;';
    echo '<div style="' . $style_row . '">';
    for ($i = 1; $i <= 4; $i++) {
        echo '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;">';
        echo '<div style="font-weight:700;font-size:12px;color:#2563eb;margin-bottom:8px;">Thống kê ' . $i . '</div>';
        _abp_input_row('Số / Giá trị', "_about_stat{$i}_num", get_post_meta($pid, "_about_stat{$i}_num", true), 'text', '500+');
        _abp_input_row('Nhãn',         "_about_stat{$i}_lbl", get_post_meta($pid, "_about_stat{$i}_lbl", true), 'text', 'Khách sạn');
        echo '</div>';
    }
    echo '</div>';
}

/* Story callback */
function bookingroom_about_story_cb($post) {
    $pid = $post->ID;
    _abp_section_title('Hình ảnh');
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">';
    _abp_img_picker('Ảnh chính (trái)', '_about_story_img1_id', $pid);
    echo '<p><label style="font-weight:700;font-size:13px;">URL ảnh chính:</label><br><input type="text" name="_about_story_img1" value="' . esc_attr(get_post_meta($pid,'_about_story_img1',true)) . '" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;"></p>';
    echo '</div>';
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">';
    _abp_img_picker('Ảnh phụ (góc phải)', '_about_story_img2_id', $pid);
    echo '<p><label style="font-weight:700;font-size:13px;">URL ảnh phụ:</label><br><input type="text" name="_about_story_img2" value="' . esc_attr(get_post_meta($pid,'_about_story_img2',true)) . '" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;"></p>';
    echo '</div>';
    _abp_section_title('Nội dung');
    _abp_input_row('Tiêu đề section (có thể dùng <span class="ab-accent">...</span> để gradient)', '_about_story_heading', get_post_meta($pid,'_about_story_heading',true), 'text');
    _abp_input_row('Nội dung câu chuyện (HTML đơn giản)', '_about_story_text', get_post_meta($pid,'_about_story_text',true), 'textarea');
    echo '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">';
    _abp_input_row('Số nổi bật (badge)', '_about_story_badge_num', get_post_meta($pid,'_about_story_badge_num',true), 'text', '10+');
    _abp_input_row('Nhãn badge', '_about_story_badge_lbl', get_post_meta($pid,'_about_story_badge_lbl',true), 'text', 'Năm kinh nghiệm');
    _abp_input_row('Nút CTA – văn bản', '_about_story_cta_text', get_post_meta($pid,'_about_story_cta_text',true), 'text', 'Xem phòng nghỉ');
    _abp_input_row('Nút CTA – URL', '_about_story_cta_url', get_post_meta($pid,'_about_story_cta_url',true), 'url');
    echo '</div>';
    echo bookingroom_about_media_js();
}

/* Features callback */
function bookingroom_about_features_cb($post) {
    $pid = $post->ID;
    for ($i = 1; $i <= 3; $i++) {
        echo '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        echo '<div style="font-weight:700;font-size:12px;color:#7c3aed;margin-bottom:8px;">Điểm nổi bật ' . $i . '</div>';
        echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">';
        _abp_input_row('Tiêu đề', "_about_feat{$i}_title", get_post_meta($pid, "_about_feat{$i}_title", true), 'text');
        _abp_input_row('Mô tả',   "_about_feat{$i}_desc",  get_post_meta($pid, "_about_feat{$i}_desc",  true), 'text');
        echo '</div></div>';
    }
}

/* Values callback */
function bookingroom_about_values_cb($post) {
    $pid = $post->ID;
    for ($i = 1; $i <= 3; $i++) {
        echo '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        echo '<div style="font-weight:700;font-size:12px;color:#2563eb;margin-bottom:8px;">Giá trị ' . $i . '</div>';
        echo '<div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;">';
        _abp_input_row('Tiêu đề', "_about_val{$i}_title", get_post_meta($pid, "_about_val{$i}_title", true));
        _abp_input_row('Mô tả',   "_about_val{$i}_desc",  get_post_meta($pid, "_about_val{$i}_desc",  true));
        echo '</div></div>';
    }
}

/* Milestones callback */
function bookingroom_about_milestones_cb($post) {
    $pid = $post->ID;
    for ($i = 1; $i <= 4; $i++) {
        echo '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        echo '<div style="font-weight:700;font-size:12px;color:#0284c7;margin-bottom:8px;">Cột mốc ' . $i . '</div>';
        echo '<div style="display:grid;grid-template-columns:80px 1fr 2fr;gap:12px;align-items:start;">';
        _abp_input_row('Năm', "_about_ms{$i}_year",  get_post_meta($pid, "_about_ms{$i}_year",  true), 'text', '2015');
        _abp_input_row('Tiêu đề', "_about_ms{$i}_title", get_post_meta($pid, "_about_ms{$i}_title", true));
        _abp_input_row('Mô tả',   "_about_ms{$i}_desc",  get_post_meta($pid, "_about_ms{$i}_desc",  true));
        echo '</div></div>';
    }
}

/* Team callback */
function bookingroom_about_team_cb($post) {
    $pid = $post->ID;
    for ($i = 1; $i <= 3; $i++) {
        echo '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        echo '<div style="font-weight:700;font-size:12px;color:#0369a1;margin-bottom:8px;">Thành viên ' . $i . '</div>';
        echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">';
        _abp_input_row('Họ tên',  "_about_team{$i}_name", get_post_meta($pid, "_about_team{$i}_name", true));
        _abp_input_row('Chức vụ', "_about_team{$i}_role", get_post_meta($pid, "_about_team{$i}_role", true));
        echo '</div>';
        _abp_img_picker('Ảnh đại diện', "_about_team{$i}_img_id", $pid);
        echo '<p><label style="font-weight:700;font-size:13px;">Hoặc nhập URL ảnh:</label><br>';
        echo '<input type="text" name="_about_team' . $i . '_img" value="' . esc_attr(get_post_meta($pid, "_about_team{$i}_img", true)) . '" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;"></p>';
        echo '</div>';
    }
    echo bookingroom_about_media_js();
}

/* Testimonial callback */
function bookingroom_about_testi_cb($post) {
    $pid = $post->ID;
    _abp_input_row('Nội dung trích dẫn', '_about_testi_quote', get_post_meta($pid,'_about_testi_quote',true), 'textarea');
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">';
    _abp_input_row('Tên khách hàng', '_about_testi_name', get_post_meta($pid,'_about_testi_name',true));
    _abp_input_row('Chức vụ / Công ty', '_about_testi_role', get_post_meta($pid,'_about_testi_role',true));
    echo '</div>';
    _abp_img_picker('Ảnh avatar', '_about_testi_img_id', $pid);
    echo '<p><label style="font-weight:700;font-size:13px;">Hoặc URL avatar:</label><br>';
    echo '<input type="text" name="_about_testi_img" value="' . esc_attr(get_post_meta($pid,'_about_testi_img',true)) . '" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;"></p>';
    echo bookingroom_about_media_js();
}

/* CTA callback */
function bookingroom_about_cta_cb($post) {
    $pid = $post->ID;
    _abp_input_row('Tiêu đề CTA', '_about_cta_title', get_post_meta($pid,'_about_cta_title',true), 'text', 'Sẵn sàng cho chuyến nghỉ dưỡng hoàn hảo?');
    _abp_input_row('Mô tả phụ',   '_about_cta_sub',   get_post_meta($pid,'_about_cta_sub',true), 'text');
    echo '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">';
    _abp_input_row('Nút 1 – văn bản', '_about_cta_btn1',     get_post_meta($pid,'_about_cta_btn1',true), 'text', 'Đặt phòng ngay');
    _abp_input_row('Nút 1 – URL',     '_about_cta_btn1_url', get_post_meta($pid,'_about_cta_btn1_url',true), 'url');
    _abp_input_row('Nút 2 – văn bản', '_about_cta_btn2',     get_post_meta($pid,'_about_cta_btn2',true), 'text', 'Liên hệ chúng tôi');
    _abp_input_row('Nút 2 – URL',     '_about_cta_btn2_url', get_post_meta($pid,'_about_cta_btn2_url',true), 'url');
    echo '</div>';
}

/* Media picker JS (enqueued once per page) */
function bookingroom_about_media_js() {
    static $printed = false;
    if ($printed) return '';
    $printed = true;
    return '<script>
    jQuery(function($){
        $(".abp-media-btn").on("click", function(){
            var targetId = $(this).data("target");
            var frame = wp.media({ title:"Chọn ảnh", button:{text:"Sử dụng ảnh này"}, multiple:false });
            frame.on("select", function(){
                var att = frame.state().get("selection").first().toJSON();
                $("#" + targetId).val(att.id).trigger("change");
                if (!$("#" + targetId).siblings("img").length) {
                    $("#" + targetId).before(\'<img src="\' + att.url + \'" style="width:80px;height:60px;object-fit:cover;border-radius:6px;border:2px solid #e2e8f0;margin-right:10px;">\');
                } else {
                    $("#" + targetId).siblings("img").attr("src", att.url);
                }
            });
            frame.open();
        });
        $(".abp-media-remove").on("click", function(){
            var targetId = $(this).data("target");
            $("#" + targetId).val("").trigger("change");
            $("#" + targetId).siblings("img").remove();
        });
    });
    </script>';
}

/* Save all about page meta */
function bookingroom_save_about_meta($post_id) {
    if (!isset($_POST['abp_nonce']) || !wp_verify_nonce($_POST['abp_nonce'], 'abp_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // All simple text/url/textarea fields
    $text_fields = [
        '_about_hero_title', '_about_hero_subtitle', '_about_hero_bg_url',
        '_about_stat1_num', '_about_stat1_lbl', '_about_stat2_num', '_about_stat2_lbl',
        '_about_stat3_num', '_about_stat3_lbl', '_about_stat4_num', '_about_stat4_lbl',
        '_about_story_heading', '_about_story_text', '_about_story_img1', '_about_story_img2',
        '_about_story_badge_num', '_about_story_badge_lbl', '_about_story_cta_text', '_about_story_cta_url',
        '_about_feat1_title', '_about_feat1_desc', '_about_feat2_title', '_about_feat2_desc',
        '_about_feat3_title', '_about_feat3_desc',
        '_about_val1_title', '_about_val1_desc', '_about_val2_title', '_about_val2_desc',
        '_about_val3_title', '_about_val3_desc',
        '_about_ms1_year', '_about_ms1_title', '_about_ms1_desc',
        '_about_ms2_year', '_about_ms2_title', '_about_ms2_desc',
        '_about_ms3_year', '_about_ms3_title', '_about_ms3_desc',
        '_about_ms4_year', '_about_ms4_title', '_about_ms4_desc',
        '_about_team1_name', '_about_team1_role', '_about_team1_img',
        '_about_team2_name', '_about_team2_role', '_about_team2_img',
        '_about_team3_name', '_about_team3_role', '_about_team3_img',
        '_about_testi_quote', '_about_testi_name', '_about_testi_role', '_about_testi_img',
        '_about_cta_title', '_about_cta_sub',
        '_about_cta_btn1', '_about_cta_btn1_url', '_about_cta_btn2', '_about_cta_btn2_url',
    ];
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, wp_kses_post(wp_unslash($_POST[$field])));
        }
    }

    // Integer (media IDs) fields
    $id_fields = [
        '_about_hero_bg_id', '_about_story_img1_id', '_about_story_img2_id',
        '_about_testi_img_id',
        '_about_team1_img_id', '_about_team2_img_id', '_about_team3_img_id',
    ];
    foreach ($id_fields as $field) {
        if (isset($_POST[$field])) {
            $val = absint($_POST[$field]);
            if ($val) update_post_meta($post_id, $field, $val);
            else delete_post_meta($post_id, $field);
        }
    }

    // If media ID is set, derive URL automatically
    $img_map = [
        '_about_hero_bg_id'    => '_about_hero_bg_url',
        '_about_story_img1_id' => '_about_story_img1',
        '_about_story_img2_id' => '_about_story_img2',
        '_about_testi_img_id'  => '_about_testi_img',
        '_about_team1_img_id'  => '_about_team1_img',
        '_about_team2_img_id'  => '_about_team2_img',
        '_about_team3_img_id'  => '_about_team3_img',
    ];
    foreach ($img_map as $id_field => $url_field) {
        $id = absint(get_post_meta($post_id, $id_field, true));
        if ($id) {
            $url = wp_get_attachment_image_url($id, 'full');
            if ($url) update_post_meta($post_id, $url_field, $url);
        }
    }
}
add_action('save_post', 'bookingroom_save_about_meta');


function bookingroom_home_hero_callback($post)
{
    wp_nonce_field('bookingroom_save_home_meta', 'bookingroom_home_meta_nonce');
    $title = get_post_meta($post->ID, '_home_hero_title', true);
    $subtitle = get_post_meta($post->ID, '_home_hero_subtitle', true);
    ?>
    <p><strong>Tiêu đề lớn (Dùng <span class="text-blue-400">...</span> để đổi màu):</strong></p>
    <?php wp_editor($title, 'home_hero_title', array('textarea_name' => 'home_hero_title', 'media_buttons' => false, 'textarea_rows' => 3)); ?>
    <p style="margin-top: 15px;"><strong>Mô tả ngắn:</strong></p>
    <?php wp_editor($subtitle, 'home_hero_subtitle', array('textarea_name' => 'home_hero_subtitle', 'media_buttons' => false, 'textarea_rows' => 5)); ?>
    <hr>
    <p style="margin-top: 15px;"><strong>Ảnh Banner (Có thể chọn nhiều ảnh để tạo banner động/slider):</strong></p>
    <?php $banner_ids = get_post_meta($post->ID, '_home_banner_ids', true); ?>
    <div id="banner-images-container" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
        <?php 
        if ($banner_ids) {
            $ids = explode(',', $banner_ids);
            foreach ($ids as $id) {
                $url = wp_get_attachment_image_url($id, 'thumbnail');
                if ($url) {
                    echo '<div class="banner-image-preview" data-id="' . $id . '" style="position: relative; border: 1px solid #ccc; padding: 2px;">';
                    echo '<img src="' . $url . '" style="width: 80px; height: 80px; object-fit: cover; display: block;">';
                    echo '<a href="#" class="remove-banner-img" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; text-align: center; line-height: 16px; text-decoration: none; font-size: 12px;">×</a>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    <input type="hidden" name="home_banner_ids" id="home_banner_ids" value="<?php echo esc_attr($banner_ids); ?>">
    <button type="button" class="button" id="upload-banner-btn">Chọn ảnh từ Gallery</button>
    <p class="description">Nếu chọn nhiều ảnh, banner sẽ tự động chuyển động (Slider).</p>
    <?php
}

function bookingroom_home_destinations_callback($post)
{
    $dest_ids = get_post_meta($post->ID, '_home_destination_ids', true);
    ?>
    <p><strong>Chọn các hình ảnh đại diện cho các Điểm đến (Tiêu đề ảnh sẽ là tên Điểm đến, Mô tả ảnh là số lượng khách sạn):</strong></p>
    <div id="destination-images-container" style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
        <?php 
        if ($dest_ids) {
            $ids = explode(',', $dest_ids);
            foreach ($ids as $id) {
                $url = wp_get_attachment_image_url($id, 'thumbnail');
                $title = get_the_title($id);
                if ($url) {
                    echo '<div class="destination-image-preview" data-id="' . $id . '" style="position: relative; border: 1px solid #ccc; padding: 5px; width: 100px; text-align: center; background: #f9f9f9;">';
                    echo '<img src="' . $url . '" style="width: 80px; height: 80px; object-fit: cover; display: block; margin: 0 auto 5px;">';
                    echo '<span style="font-size: 10px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . esc_html($title) . '</span>';
                    echo '<a href="#" class="remove-dest-img" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; text-align: center; line-height: 16px; text-decoration: none; font-size: 12px;">×</a>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    <input type="hidden" name="home_destination_ids" id="home_destination_ids" value="<?php echo esc_attr($dest_ids); ?>">
    <button type="button" class="button" id="upload-dest-btn">Chọn Điểm đến từ Gallery</button>
    <p class="description">Lưu ý: Để đổi tên điểm đến, hãy chỉnh sửa <b>Tiêu đề (Title)</b> của ảnh trong Thư viện media. Để đổi số lượng khách sạn, hãy chỉnh sửa <b>Mô tả (Caption)</b> của ảnh.</p>
    <?php
}

/**
 * Meta box callback: Section Giới thiệu (About)
 */
function bookingroom_home_about_callback($post) {
    $pid = $post->ID;
    // Ảnh
    $img_id   = get_post_meta($pid, '_home_about_img',   true);
    $img2_id  = get_post_meta($pid, '_home_about_img2',  true);
    $img_url  = $img_id  ? wp_get_attachment_image_url($img_id,  'thumbnail') : '';
    $img2_url = $img2_id ? wp_get_attachment_image_url($img2_id, 'thumbnail') : '';
    // Nội dung
    $eyebrow   = get_post_meta($pid, '_home_about_eyebrow',   true) ?: 'Về chúng tôi';
    $title     = get_post_meta($pid, '_home_about_title',     true) ?: 'Điểm đến <span class="text-accent">nghỉ dưỡng đẳng cấp</span> hàng đầu Việt Nam';
    $desc      = get_post_meta($pid, '_home_about_desc',      true) ?: 'Chúng tôi mang đến trải nghiệm lưu trú tuyệt vời với hệ thống khách sạn, resort sang trọng trải dài khắp Việt Nam.';
    $badge_num = get_post_meta($pid, '_home_about_badge_num', true) ?: '10+';
    $badge_lbl = get_post_meta($pid, '_home_about_badge_label', true) ?: 'Năm kinh nghiệm';
    // Features
    $feat1_title = get_post_meta($pid, '_home_about_feat1_title', true) ?: 'Không gian sang trọng, đẳng cấp 5 sao';
    $feat1_desc  = get_post_meta($pid, '_home_about_feat1_desc',  true) ?: 'Thiết kế tinh tế, nội thất cao cấp mang lại cảm giác thư thái tuyệt đối.';
    $feat2_title = get_post_meta($pid, '_home_about_feat2_title', true) ?: 'Đặt phòng nhanh chóng, xác nhận tức thì';
    $feat2_desc  = get_post_meta($pid, '_home_about_feat2_desc',  true) ?: 'Quy trình đặt phòng đơn giản, xác nhận ngay lập tức qua email và SMS.';
    $feat3_title = get_post_meta($pid, '_home_about_feat3_title', true) ?: 'Cam kết giá tốt nhất, hoàn tiền 100%';
    $feat3_desc  = get_post_meta($pid, '_home_about_feat3_desc',  true) ?: 'Giá ưu đãi độc quyền, chính sách hoàn tiền minh bạch khi có thay đổi.';
    // Stats
    $stat1_num   = get_post_meta($pid, '_home_stat1_num',   true) ?: '500+';
    $stat1_label = get_post_meta($pid, '_home_stat1_label', true) ?: 'Khách sạn';
    $stat2_num   = get_post_meta($pid, '_home_stat2_num',   true) ?: '50K+';
    $stat2_label = get_post_meta($pid, '_home_stat2_label', true) ?: 'Lượt đặt phòng';
    $stat3_num   = get_post_meta($pid, '_home_stat3_num',   true) ?: '4.9★';
    $stat3_label = get_post_meta($pid, '_home_stat3_label', true) ?: 'Đánh giá TB';
    // CTA
    $cta_text = get_post_meta($pid, '_home_about_cta_text', true) ?: 'Tìm hiểu thêm về chúng tôi';
    $cta_url  = get_post_meta($pid, '_home_about_cta_url',  true) ?: home_url('/about');

    $style_box   = 'background:#f0f6ff;border:1px solid #c7d9f0;border-radius:8px;padding:16px 18px;margin-bottom:18px;';
    $style_row   = 'display:flex;gap:16px;margin-bottom:12px;flex-wrap:wrap;';
    $style_col   = 'flex:1;min-width:200px;';
    $style_label = 'font-weight:700;display:block;margin-bottom:5px;color:#1e293b;font-size:13px;';
    $style_input = 'width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;';
    $style_h3    = 'margin:0 0 14px;font-size:14px;color:#2563eb;border-bottom:2px solid #bfdbfe;padding-bottom:8px;';
    ?>
    <style>
        #home_about_section .about-img-pick { display:flex; align-items:center; gap:14px; }
        #home_about_section .about-img-pick img { width:90px; height:70px; object-fit:cover; border-radius:6px; border:2px solid #e2e8f0; }
        #home_about_section .about-img-pick .no-img { width:90px; height:70px; background:#f1f5f9; border:2px dashed #cbd5e1; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:11px; text-align:center; }
    </style>

    <div style="<?php echo $style_box; ?>">
        <h3 style="<?php echo $style_h3; ?>">🖼️ Hình ảnh</h3>
        <div style="<?php echo $style_row; ?>">
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Ảnh chính (bên trái)</label>
                <div class="about-img-pick">
                    <?php if ($img_url): ?>
                        <img src="<?php echo esc_url($img_url); ?>" id="about-img-preview" alt="">
                    <?php else: ?>
                        <div class="no-img" id="about-img-preview">Chưa có ảnh</div>
                    <?php endif; ?>
                    <div>
                        <input type="hidden" name="home_about_img" id="home_about_img" value="<?php echo esc_attr($img_id); ?>">
                        <button type="button" class="button" id="about-img-btn">Chọn ảnh chính</button>
                        <?php if ($img_id): ?>
                            <br><button type="button" class="button" id="about-img-remove" style="margin-top:6px;color:red;">✕ Xóa</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Ảnh phụ (góc phải nhỏ)</label>
                <div class="about-img-pick">
                    <?php if ($img2_url): ?>
                        <img src="<?php echo esc_url($img2_url); ?>" id="about-img2-preview" alt="">
                    <?php else: ?>
                        <div class="no-img" id="about-img2-preview">Chưa có ảnh</div>
                    <?php endif; ?>
                    <div>
                        <input type="hidden" name="home_about_img2" id="home_about_img2" value="<?php echo esc_attr($img2_id); ?>">
                        <button type="button" class="button" id="about-img2-btn">Chọn ảnh phụ</button>
                        <?php if ($img2_id): ?>
                            <br><button type="button" class="button" id="about-img2-remove" style="margin-top:6px;color:red;">✕ Xóa</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="<?php echo $style_box; ?>">
        <h3 style="<?php echo $style_h3; ?>">📝 Nội dung chính</h3>
        <div style="<?php echo $style_row; ?>">
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Nhãn nhỏ phía trên tiêu đề (Eyebrow)</label>
                <input type="text" name="home_about_eyebrow" value="<?php echo esc_attr($eyebrow); ?>" style="<?php echo $style_input; ?>" placeholder="VD: Về chúng tôi">
            </div>
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Badge nổi trên ảnh (số)</label>
                <input type="text" name="home_about_badge_num" value="<?php echo esc_attr($badge_num); ?>" style="<?php echo $style_input; ?>" placeholder="VD: 10+">
            </div>
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Badge nổi trên ảnh (nhãn)</label>
                <input type="text" name="home_about_badge_label" value="<?php echo esc_attr($badge_lbl); ?>" style="<?php echo $style_input; ?>" placeholder="VD: Năm kinh nghiệm">
            </div>
        </div>
        <div style="margin-bottom:12px;">
            <label style="<?php echo $style_label; ?>">Tiêu đề section (có thể dùng thẻ &lt;span class="text-accent"&gt;...&lt;/span&gt; để tô màu gradient)</label>
            <input type="text" name="home_about_title" value="<?php echo esc_attr($title); ?>" style="<?php echo $style_input; ?>" placeholder="VD: Điểm đến <span class=&quot;text-accent&quot;>nghỉ dưỡng đẳng cấp</span> hàng đầu Việt Nam">
        </div>
        <div>
            <label style="<?php echo $style_label; ?>">Mô tả ngắn</label>
            <textarea name="home_about_desc" rows="3" style="<?php echo $style_input; ?>" placeholder="Mô tả về khách sạn/dịch vụ..."><?php echo esc_textarea($desc); ?></textarea>
        </div>
    </div>

    <div style="<?php echo $style_box; ?>">
        <h3 style="<?php echo $style_h3; ?>">✅ Danh sách ưu điểm (Features)</h3>
        <?php foreach ([[1,$feat1_title,$feat1_desc],[2,$feat2_title,$feat2_desc],[3,$feat3_title,$feat3_desc]] as [$n,$ft,$fd]): ?>
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;margin-bottom:10px;">
            <div style="font-size:12px;font-weight:700;color:#7c3aed;margin-bottom:8px;">Ưu điểm <?php echo $n; ?></div>
            <div style="<?php echo $style_row; ?>">
                <div style="flex:2;min-width:200px;">
                    <label style="<?php echo $style_label; ?>">Tiêu đề</label>
                    <input type="text" name="home_about_feat<?php echo $n; ?>_title" value="<?php echo esc_attr($ft); ?>" style="<?php echo $style_input; ?>">
                </div>
                <div style="flex:3;min-width:240px;">
                    <label style="<?php echo $style_label; ?>">Mô tả</label>
                    <input type="text" name="home_about_feat<?php echo $n; ?>_desc" value="<?php echo esc_attr($fd); ?>" style="<?php echo $style_input; ?>">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="<?php echo $style_box; ?>">
        <h3 style="<?php echo $style_h3; ?>">📊 Thống kê (3 ô số liệu)</h3>
        <div style="<?php echo $style_row; ?>">
            <?php foreach ([
                [1,$stat1_num,$stat1_label],
                [2,$stat2_num,$stat2_label],
                [3,$stat3_num,$stat3_label]
            ] as [$n,$sn,$sl]): ?>
            <div style="flex:1;min-width:140px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:12px;">
                <div style="font-size:11px;font-weight:700;color:#2563eb;margin-bottom:8px;">Thống kê <?php echo $n; ?></div>
                <label style="<?php echo $style_label; ?>">Số / Giá trị</label>
                <input type="text" name="home_stat<?php echo $n; ?>_num" value="<?php echo esc_attr($sn); ?>" style="<?php echo $style_input; ?>;margin-bottom:8px;" placeholder="VD: 500+">
                <label style="<?php echo $style_label; ?>">Nhãn</label>
                <input type="text" name="home_stat<?php echo $n; ?>_label" value="<?php echo esc_attr($sl); ?>" style="<?php echo $style_input; ?>" placeholder="VD: Khách sạn">
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="<?php echo $style_box; ?>">
        <h3 style="<?php echo $style_h3; ?>">🔗 Nút CTA</h3>
        <div style="<?php echo $style_row; ?>">
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Văn bản nút</label>
                <input type="text" name="home_about_cta_text" value="<?php echo esc_attr($cta_text); ?>" style="<?php echo $style_input; ?>" placeholder="VD: Tìm hiểu thêm về chúng tôi">
            </div>
            <div style="<?php echo $style_col; ?>">
                <label style="<?php echo $style_label; ?>">Đường dẫn URL</label>
                <input type="url" name="home_about_cta_url" value="<?php echo esc_attr($cta_url); ?>" style="<?php echo $style_input; ?>" placeholder="<?php echo home_url('/about'); ?>">
            </div>
        </div>
    </div>
    <?php
}

function bookingroom_home_why_us_callback($post)
{
    $content = get_post_meta($post->ID, '_home_why_us_content', true);
    ?>
        <p><strong>Nội dung phần "Tại sao chọn chúng tôi" (Nhập dưới dạng danh sách hoặc các khối văn bản):</strong></p>
        <?php wp_editor($content, 'home_why_us_content', array('textarea_name' => 'home_why_us_content', 'textarea_rows' => 10)); ?>
<?php
}

function bookingroom_save_home_meta($post_id)
{
    if (!isset($_POST['bookingroom_home_meta_nonce']) || !wp_verify_nonce($_POST['bookingroom_home_meta_nonce'], 'bookingroom_save_home_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    if (isset($_POST['home_hero_title'])) {
        update_post_meta($post_id, '_home_hero_title', $_POST['home_hero_title']);
    }
    if (isset($_POST['home_hero_subtitle'])) {
        update_post_meta($post_id, '_home_hero_subtitle', $_POST['home_hero_subtitle']);
    }
    if (isset($_POST['home_banner_ids'])) {
        update_post_meta($post_id, '_home_banner_ids', sanitize_text_field($_POST['home_banner_ids']));
    }
    if (isset($_POST['home_destination_ids'])) {
        update_post_meta($post_id, '_home_destination_ids', sanitize_text_field($_POST['home_destination_ids']));
    }
    if (isset($_POST['home_why_us_content'])) {
        update_post_meta($post_id, '_home_why_us_content', $_POST['home_why_us_content']);
    }

    // ── About / Intro Section ──────────────────────────────────────────────
    $about_text_fields = array(
        'home_about_eyebrow', 'home_about_badge_num', 'home_about_badge_label',
        'home_about_desc', 'home_about_cta_text', 'home_about_cta_url',
        'home_about_feat1_title', 'home_about_feat1_desc',
        'home_about_feat2_title', 'home_about_feat2_desc',
        'home_about_feat3_title', 'home_about_feat3_desc',
        'home_stat1_num', 'home_stat1_label',
        'home_stat2_num', 'home_stat2_label',
        'home_stat3_num', 'home_stat3_label',
    );
    foreach ($about_text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    // Tiêu đề cho phép HTML tag span
    if (isset($_POST['home_about_title'])) {
        update_post_meta($post_id, '_home_about_title', wp_kses($_POST['home_about_title'], array(
            'span' => array('class' => array(), 'style' => array()),
            'br'   => array(),
            'b'    => array(),
            'strong' => array(),
        )));
    }
    // Ảnh
    if (isset($_POST['home_about_img'])) {
        $img_id = absint($_POST['home_about_img']);
        if ($img_id) update_post_meta($post_id, '_home_about_img', $img_id);
        else delete_post_meta($post_id, '_home_about_img');
    }
    if (isset($_POST['home_about_img2'])) {
        $img2_id = absint($_POST['home_about_img2']);
        if ($img2_id) update_post_meta($post_id, '_home_about_img2', $img2_id);
        else delete_post_meta($post_id, '_home_about_img2');
    }
}
add_action('save_post', 'bookingroom_save_home_meta');

/**
 * Your code goes below.
 */

/**
 * Booking Engine Settings in Customizer
 */
function bookingroom_customize_register($wp_customize)
{
    // Section
    $wp_customize->add_section('booking_engine_section', array(
        'title' => __('Cấu hình Booking Engine & API', 'bookingroom'),
        'priority' => 30,
    ));

    // Setting: Enable External Engine
    $wp_customize->add_setting('use_external_booking', array(
        'default' => 'no',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('use_external_booking', array(
        'label' => __('Sử dụng Booking Engine bên ngoài', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'select',
        'choices' => array(
            'no' => __('Không (Dùng hệ thống nội bộ)', 'bookingroom'),
            'yes' => __('Có (Dùng link bên ngoài)', 'bookingroom'),
        ),
    ));

    // Setting: Booking Engine URL
    $wp_customize->add_setting('booking_engine_url', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('booking_engine_url', array(
        'label' => __('Link Booking Engine (ezCloud, Cloudbeds...)', 'bookingroom'),
        'description' => __('VD: https://booking.ezcloud.vn/your-hotel-id', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'url',
    ));

    // Setting: Hotline/Phone Number
    $wp_customize->add_setting('bookingroom_hotline', array(
        'default' => '(84-252) 381 2233',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('bookingroom_hotline', array(
        'label' => __('Số điện thoại hỗ trợ đặt phòng (Hotline)', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'text',
    ));

    // Setting: Booking Email
    $wp_customize->add_setting('bookingroom_email', array(
        'default' => 'reservation@poshanuresort.com.vn',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('bookingroom_email', array(
        'label' => __('Email hỗ trợ đặt phòng', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'email',
    ));

    // Setting: Google Maps API Key
    $wp_customize->add_setting('google_maps_api_key', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('google_maps_api_key', array(
        'label' => __('Google Maps API Key', 'bookingroom'),
        'description' => __('Dùng cho tính năng tự động gợi ý địa điểm (Autocomplete).', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'text',
    ));

    // Add Logo Height setting to Site Identity
    $wp_customize->add_setting('logo_height', array(
        'default' => 100,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('logo_height', array(
        'label' => __('Chiều cao Logo (px)', 'bookingroom'),
        'section' => 'title_tagline',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 20,
            'max' => 1000,
            'step' => 5,
        ),
    ));

    // ── Footer Contact & Social ──────────────────────────────────────────
    $wp_customize->add_section('bookingroom_footer_section', array(
        'title'    => __('Footer – Liên hệ & Mạng xã hội', 'bookingroom'),
        'priority' => 35,
    ));

    $footer_fields = array(
        'footer_address'    => array('text',  __('Địa chỉ', 'bookingroom'),      '123 Đường Lê Lợi, Q.1, TP.HCM'),
        'footer_phone'      => array('text',  __('Số điện thoại', 'bookingroom'), '+84 90 123 4567'),
        'footer_email'      => array('email', __('Email liên hệ', 'bookingroom'), 'info@travelrooms.vn'),
        'social_facebook'   => array('url',   __('Facebook URL', 'bookingroom'),  '#'),
        'social_instagram'  => array('url',   __('Instagram URL', 'bookingroom'), '#'),
        'social_youtube'    => array('url',   __('YouTube URL', 'bookingroom'),   '#'),
    );

    foreach ($footer_fields as $key => $cfg) {
        $wp_customize->add_setting($key, array(
            'default'           => $cfg[2],
            'transport'         => 'refresh',
            'sanitize_callback' => $cfg[0] === 'email' ? 'sanitize_email' : 'sanitize_text_field',
        ));
        $wp_customize->add_control($key, array(
            'label'   => $cfg[1],
            'section' => 'bookingroom_footer_section',
            'type'    => $cfg[0],
        ));
    }

    // ── Trang Liên hệ – Contact Page ────────────────────────────────────
    $wp_customize->add_section('bookingroom_contact_section', array(
        'title'    => __('Trang Liên hệ (Contact)', 'bookingroom'),
        'priority' => 36,
    ));

    // --- Hero ---
    $wp_customize->add_setting('contact_hero_title', array(
        'default'           => 'Liên hệ với Chúng tôi',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_hero_title', array(
        'label'   => __('Hero – Tiêu đề', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_hero_desc', array(
        'default'           => 'Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để lại lời nhắn hoặc liên hệ trực tiếp qua hotline.',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_hero_desc', array(
        'label'   => __('Hero – Mô tả', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'textarea',
    ));

    // --- Quick Info Card 1: Giờ làm việc ---
    $wp_customize->add_setting('contact_card1_title', array(
        'default'           => 'Giờ làm việc',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card1_title', array(
        'label'   => __('Card 1 – Tiêu đề (Giờ làm việc)', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_card1_desc', array(
        'default'           => 'Thứ 2 – Thứ 6: 8:00 – 18:00<br>Thứ 7 – CN: 9:00 – 17:00',
        'transport'         => 'refresh',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('contact_card1_desc', array(
        'label'       => __('Card 1 – Nội dung', 'bookingroom'),
        'description' => __('Dùng &lt;br&gt; để xuống dòng', 'bookingroom'),
        'section'     => 'bookingroom_contact_section',
        'type'        => 'textarea',
    ));

    // --- Quick Info Card 2: Hotline ---
    $wp_customize->add_setting('contact_card2_title', array(
        'default'           => 'Hotline hỗ trợ',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card2_title', array(
        'label'   => __('Card 2 – Tiêu đề (Hotline)', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_card2_phone', array(
        'default'           => '0123 456 789',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card2_phone', array(
        'label'   => __('Card 2 – Số hotline', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_card2_phone_link', array(
        'default'           => 'tel:01234567890',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('contact_card2_phone_link', array(
        'label'       => __('Card 2 – Link gọi điện', 'bookingroom'),
        'description' => __('VD: tel:01234567890', 'bookingroom'),
        'section'     => 'bookingroom_contact_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('contact_card2_desc', array(
        'default'           => 'Hỗ trợ 24/7 mọi ngày',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card2_desc', array(
        'label'   => __('Card 2 – Mô tả phụ', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    // --- Quick Info Card 3: Email ---
    $wp_customize->add_setting('contact_card3_title', array(
        'default'           => 'Phản hồi email',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card3_title', array(
        'label'   => __('Card 3 – Tiêu đề (Email)', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_card3_desc', array(
        'default'           => 'Trong vòng 2–4 giờ làm việc',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_card3_desc', array(
        'label'   => __('Card 3 – Mô tả', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_card3_email', array(
        'default'           => 'support@bookingroom.com',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('contact_card3_email', array(
        'label'   => __('Card 3 – Địa chỉ email', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'email',
    ));

    // --- Info Panel: Địa chỉ ---
    $wp_customize->add_setting('contact_info_address', array(
        'default'           => '123 Đường Trần Hưng Đạo<br>Quận 1, TP. Hồ Chí Minh',
        'transport'         => 'refresh',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('contact_info_address', array(
        'label'       => __('Info Panel – Địa chỉ', 'bookingroom'),
        'description' => __('Dùng &lt;br&gt; để xuống dòng', 'bookingroom'),
        'section'     => 'bookingroom_contact_section',
        'type'        => 'textarea',
    ));

    // --- Info Panel: Điện thoại ---
    $wp_customize->add_setting('contact_info_phone1', array(
        'default'           => '0123 456 789',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_info_phone1', array(
        'label'   => __('Info Panel – Số điện thoại 1', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_info_phone1_link', array(
        'default'           => 'tel:01234567890',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('contact_info_phone1_link', array(
        'label'   => __('Info Panel – Link gọi SĐT 1', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_info_phone2', array(
        'default'           => '1900 1234',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_info_phone2', array(
        'label'   => __('Info Panel – Số điện thoại 2', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('contact_info_phone2_link', array(
        'default'           => 'tel:19001234',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('contact_info_phone2_link', array(
        'label'   => __('Info Panel – Link gọi SĐT 2', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'text',
    ));

    // --- Info Panel: Email ---
    $wp_customize->add_setting('contact_info_email1', array(
        'default'           => 'support@bookingroom.com',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('contact_info_email1', array(
        'label'   => __('Info Panel – Email 1', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'email',
    ));

    $wp_customize->add_setting('contact_info_email2', array(
        'default'           => 'info@bookingroom.com',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('contact_info_email2', array(
        'label'   => __('Info Panel – Email 2', 'bookingroom'),
        'section' => 'bookingroom_contact_section',
        'type'    => 'email',
    ));

    // --- Info Panel: Social links ---
    $contact_social = array(
        'contact_social_facebook'  => array('url', __('Facebook URL', 'bookingroom'),  '#'),
        'contact_social_instagram' => array('url', __('Instagram URL', 'bookingroom'), '#'),
        'contact_social_youtube'   => array('url', __('YouTube URL', 'bookingroom'),   '#'),
        'contact_social_zalo'      => array('url', __('Zalo URL', 'bookingroom'),      '#'),
    );
    foreach ($contact_social as $key => $cfg) {
        $wp_customize->add_setting($key, array(
            'default'           => $cfg[2],
            'transport'         => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($key, array(
            'label'   => $cfg[1],
            'section' => 'bookingroom_contact_section',
            'type'    => $cfg[0],
        ));
    }

    // --- Google Maps embed address ---
    $wp_customize->add_setting('contact_map_query', array(
        'default'           => '123+Tran+Hung+Dao,+Ho+Chi+Minh+City',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_map_query', array(
        'label'       => __('Google Maps – Địa chỉ tìm kiếm', 'bookingroom'),
        'description' => __('Dùng dấu + thay khoảng trắng. VD: 123+Tran+Hung+Dao', 'bookingroom'),
        'section'     => 'bookingroom_contact_section',
        'type'        => 'text',
    ));
    // ── Trang Vị trí – Location Page ─────────────────────────────────────
    $wp_customize->add_section('bookingroom_location_section', array(
        'title'    => __('Trang Vị trí (Location)', 'bookingroom'),
        'priority' => 37,
    ));

    // Hero
    $wp_customize->add_setting('loc_hero_title', array(
        'default'           => 'Vị trí của chúng tôi',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loc_hero_title', array(
        'label'   => __('Hero – Tiêu đề', 'bookingroom'),
        'section' => 'bookingroom_location_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('loc_hero_desc', array(
        'default'           => 'Toạ lạc tại trung tâm thành phố, Sonata dễ dàng tiếp cận từ mọi hướng.',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loc_hero_desc', array(
        'label'   => __('Hero – Mô tả', 'bookingroom'),
        'section' => 'bookingroom_location_section',
        'type'    => 'textarea',
    ));

    $wp_customize->add_setting('loc_hero_image', array(
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'loc_hero_image', array(
        'label'   => __('Hero – Ảnh nền', 'bookingroom'),
        'section' => 'bookingroom_location_section',
    )));

    // Contact details
    $wp_customize->add_setting('loc_address_full', array(
        'default'           => '123 Đường Trần Hưng Đạo, Phường Nguyễn Cư Trinh, Quận 1, TP. Hồ Chí Minh',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loc_address_full', array(
        'label'   => __('Địa chỉ đầy đủ', 'bookingroom'),
        'section' => 'bookingroom_location_section',
        'type'    => 'textarea',
    ));

    $wp_customize->add_setting('loc_phone', array(
        'default'           => '0123 456 789',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loc_phone', array(
        'label'   => __('Số điện thoại', 'bookingroom'),
        'section' => 'bookingroom_location_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('loc_phone_link', array(
        'default'           => 'tel:0123456789',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('loc_phone_link', array(
        'label'       => __('Link gọi điện', 'bookingroom'),
        'description' => __('VD: tel:0123456789', 'bookingroom'),
        'section'     => 'bookingroom_location_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('loc_email', array(
        'default'           => 'info@sonata.vn',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('loc_email', array(
        'label'   => __('Email', 'bookingroom'),
        'section' => 'bookingroom_location_section',
        'type'    => 'email',
    ));

    // Map settings
    $wp_customize->add_setting('loc_map_query', array(
        'default'           => '123+Tran+Hung+Dao,+Ho+Chi+Minh+City',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loc_map_query', array(
        'label'       => __('Google Maps – Địa chỉ tìm kiếm', 'bookingroom'),
        'description' => __('Dùng dấu + thay khoảng trắng. VD: 123+Tran+Hung+Dao', 'bookingroom'),
        'section'     => 'bookingroom_location_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('loc_map_embed', array(
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('loc_map_embed', array(
        'label'       => __('Google Maps – Embed HTML (tuỳ chọn)', 'bookingroom'),
        'description' => __('Dán toàn bộ thẻ &lt;iframe&gt; từ Google Maps → Share → Embed a map. Nếu để trống sẽ dùng API Key hoặc bản đồ mặc định.', 'bookingroom'),
        'section'     => 'bookingroom_location_section',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'bookingroom_customize_register');


/**
 * Get Booking URL based on settings
 */
function bookingroom_get_booking_url($params = array())
{
    $use_external = get_theme_mod('use_external_booking', 'no');
    $external_url = get_theme_mod('booking_engine_url', '');

    if ($use_external === 'yes' && !empty($external_url)) {
        // If room_id is passed, try to get the room-specific engine ID
        if (isset($params['room_id'])) {
            $engine_room_id = get_post_meta($params['room_id'], '_engine_room_id', true);
            if (!empty($engine_room_id)) {
                $params['room_type'] = $engine_room_id; // Standard param for many engines
            }
            unset($params['room_id']);
        }

        return add_query_arg($params, $external_url);
    }

    // Default local rooms page
    return home_url('/rooms');
}


/**
 * Override front page template to use page-trang-chu.php
 */
add_filter('template_include', function ($template) {
    if (is_front_page() || is_home()) {
        $new_template = locate_template(array('page-trang-chu.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
});

/**
 * Track Post Views
 */
function bookingroom_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count == ''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function bookingroom_track_post_views($post_id) {
    if (!is_single()) return;
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;    
    }
    bookingroom_set_post_views($post_id);
}
add_action('wp_head', 'bookingroom_track_post_views');



/**
 * Get the number of available rooms for a specific room type during a date range
 */
function bookingroom_get_available_rooms($room_id, $check_in, $check_out) {
    // 1. Get total rooms of this room type
    $room_numbers_meta = get_post_meta($room_id, '_room_numbers', true);
    if (!empty($room_numbers_meta)) {
        $all_rooms = array_map('trim', explode(',', $room_numbers_meta));
        $total_rooms = count($all_rooms);
    } else {
        $total_rooms = 10; // Default to 10 if not defined
    }

    // 2. Query bookings that overlap with the selected dates
    // Booking overlaps if: booking._check_in < select.check_out AND booking._check_out > select.check_in
    $args = array(
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_room_id',
                'value' => $room_id,
                'compare' => '='
            ),
            array(
                'key' => '_check_in',
                'value' => $check_out,
                'compare' => '<',
                'type' => 'DATE'
            ),
            array(
                'key' => '_check_out',
                'value' => $check_in,
                'compare' => '>',
                'type' => 'DATE'
            ),
            array(
                'key' => '_status',
                'value' => 'cancelled',
                'compare' => '!=',
            )
        )
    );

    $bookings_query = new WP_Query($args);
    $booked_count = $bookings_query->post_count;

    $available_rooms = $total_rooms - $booked_count;
    if ($available_rooms < 0) {
        $available_rooms = 0;
    }

    return $available_rooms;
}



/**
 * Route room custom post type search results to archive-room.php template
 */
function bookingroom_room_search_template($template) {
    if (is_search() && get_query_var('post_type') === 'room') {
        $new_template = locate_template(array('archive-room.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'bookingroom_room_search_template');

/**
 * Điều hướng template phòng:
 *  - Mặc định (click ảnh) → single-thongtin.php (trang thông tin phòng)
 *  - Có ?step=book         → single-room.php    (trang đặt phòng / thanh toán)
 */
function bookingroom_single_room_template($template) {
    if (is_singular('room')) {
        $step = isset($_GET['step']) ? sanitize_key($_GET['step']) : '';
        if ($step === 'book') {
            // Trang đặt phòng / thanh toán
            $booking_template = locate_template(array('single-room.php'));
            if (!empty($booking_template)) {
                return $booking_template;
            }
        } else {
            // Trang thông tin phòng (mặc định)
            $info_template = locate_template(array('single-thongtin.php'));
            if (!empty($info_template)) {
                return $info_template;
            }
        }
    }
    return $template;
}
add_filter('template_include', 'bookingroom_single_room_template', 20);

// ==========================================
// AUTO-ADD BTN-MENU CLASS TO LAST MENU ITEM
// ==========================================
add_filter('wp_nav_menu_objects', 'bookingroom_auto_btn_last_menu_item', 10, 2);
function bookingroom_auto_btn_last_menu_item($items, $args) {
    if (($args->theme_location == 'primary' || $args->theme_location == 'primary-menu') && !empty($items)) {
        // Tìm item cuối cùng ở cấp độ cao nhất (menu parent)
        $last_item_key = null;
        foreach ($items as $key => $item) {
            if ($item->menu_item_parent == 0) {
                $last_item_key = $key;
            }
        }
        if ($last_item_key !== null) {
            $items[$last_item_key]->classes[] = 'btn-menu';
        }
    }
    return $items;
}





// Hide admin bar on frontend
add_filter('show_admin_bar', '__return_false');
