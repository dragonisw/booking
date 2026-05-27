<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */

add_action('wp_enqueue_scripts', 'bookingroom_style');
function bookingroom_style()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));

    // Add Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    // Enqueue Booking Script
    wp_enqueue_script('booking-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
    wp_localize_script('booking-script', 'booking_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('booking_nonce')
    ));

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
            'primary-menu'   => __('Primary Menu', 'bookingroom'),
            'footer-explore' => __('Footer – Khám phá', 'bookingroom'),
            'footer-support' => __('Footer – Hỗ trợ', 'bookingroom'),
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
    add_meta_box(
        'room_details',
        __('Thông tin phòng', 'bookingroom'),
        'bookingroom_room_details_callback',
        'room',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bookingroom_add_room_meta_boxes');

function bookingroom_room_details_callback($post)
{
    wp_nonce_field('bookingroom_save_room_details', 'bookingroom_room_details_nonce');

    $price = get_post_meta($post->ID, '_price', true);
    $capacity = get_post_meta($post->ID, '_capacity', true);
    $room_label = get_post_meta($post->ID, '_room_label', true);
    $engine_room_id = get_post_meta($post->ID, '_engine_room_id', true);

    ?>
    <?php
    $weekend_price = get_post_meta($post->ID, '_weekend_price', true);
    ?>
    <p>
        <label for="room_price"><strong><?php _e('Giá phòng trong tuần (T2–T5) (VNĐ):', 'bookingroom'); ?></strong></label>
        <input type="number" id="room_price" name="room_price" value="<?php echo esc_attr($price); ?>" class="widefat" placeholder="VD: 1500000" />
        <span class="description">Áp dụng: Thứ 2, Thứ 3, Thứ 4, Thứ 5</span>
    </p>
    <p>
        <label for="room_weekend_price"><strong><?php _e('Giá phòng cuối tuần (T6–CN) (VNĐ):', 'bookingroom'); ?></strong></label>
        <input type="number" id="room_weekend_price" name="room_weekend_price" value="<?php echo esc_attr($weekend_price); ?>" class="widefat" placeholder="VD: 2000000 (để trống = giống giá trong tuần)" />
        <span class="description">Áp dụng: Thứ 6, Thứ 7, Chủ Nhật. Để trống nếu giá bằng với trong tuần.</span>
    </p>
    <p>
        <label for="room_capacity"><?php _e('Sức chứa (người):', 'bookingroom'); ?></label>
        <input type="text" id="room_capacity" name="room_capacity" value="<?php echo esc_attr($capacity); ?>"
            class="widefat" />
    </p>
    <p>
        <label for="room_label"><?php _e('Nhãn hiển thị (ví dụ: Lux Room):', 'bookingroom'); ?></label>
        <input type="text" id="room_label" name="room_label" value="<?php echo esc_attr($room_label); ?>" class="widefat" />
    </p>
    <hr>
    <p>
        <label for="engine_room_id"><?php _e('ID Phòng trên Booking Engine (nếu có):', 'bookingroom'); ?></label>
        <input type="text" id="engine_room_id" name="engine_room_id" value="<?php echo esc_attr($engine_room_id); ?>"
            class="widefat" />
        <span class="description">Dùng để trỏ trực tiếp đến loại phòng này trên ezCloud/Cloudbeds...</span>
    </p>
    <?php

}

function bookingroom_save_room_details($post_id)
{
    if (!isset($_POST['bookingroom_room_details_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['bookingroom_room_details_nonce'], 'bookingroom_save_room_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['room_price'])) {
        update_post_meta($post_id, '_price', sanitize_text_field($_POST['room_price']));
    }

    if (isset($_POST['room_weekend_price'])) {
        $wp = sanitize_text_field($_POST['room_weekend_price']);
        if ($wp !== '') {
            update_post_meta($post_id, '_weekend_price', $wp);
        } else {
            delete_post_meta($post_id, '_weekend_price');
        }
    }

    if (isset($_POST['room_capacity'])) {
        update_post_meta($post_id, '_capacity', sanitize_text_field($_POST['room_capacity']));
    }

    if (isset($_POST['room_label'])) {
        update_post_meta($post_id, '_room_label', sanitize_text_field($_POST['room_label']));
    }

    if (isset($_POST['engine_room_id'])) {
        update_post_meta($post_id, '_engine_room_id', sanitize_text_field($_POST['engine_room_id']));
    }
}

add_action('save_post', 'bookingroom_save_room_details');

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
}
add_action('add_meta_boxes', 'bookingroom_add_home_meta_boxes');

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
 * Add About Page Meta Boxes
 */
function bookingroom_add_about_meta_boxes() {
    $post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : false);
    if (!$post_id) return;
    if (get_post_meta($post_id, '_wp_page_template', true) == 'page-about.php') {
        add_meta_box('about_hero', 'Khối Hero (Đầu trang)', 'bookingroom_about_hero_cb', 'page', 'normal', 'high');
        add_meta_box('about_story', 'Khối Câu chuyện', 'bookingroom_about_story_cb', 'page', 'normal', 'high');
        add_meta_box('about_values', 'Khối Giá trị Cốt lõi', 'bookingroom_about_values_cb', 'page', 'normal', 'high');
        add_meta_box('about_testimonial', 'Khối Khách hàng đánh giá', 'bookingroom_about_testimonial_cb', 'page', 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'bookingroom_add_about_meta_boxes');

function bookingroom_about_hero_cb($post) {
    wp_nonce_field('save_about_meta', 'about_meta_nonce');
    $title = get_post_meta($post->ID, '_about_hero_title', true) ?: 'Về <span class="text-blue-500">Sonata</span> Travel';
    $subtitle = get_post_meta($post->ID, '_about_hero_subtitle', true) ?: 'Hành trình của chúng tôi bắt đầu từ niềm đam mê khám phá và khát khao mang lại những trải nghiệm nghỉ dưỡng đẳng cấp nhất cho mỗi khách hàng.';
    echo '<p><strong>Tiêu đề:</strong></p><input type="text" name="about_hero_title" class="widefat" value="'.esc_attr($title).'">';
    echo '<p><strong>Mô tả:</strong></p><textarea name="about_hero_subtitle" class="widefat" rows="3">'.esc_textarea($subtitle).'</textarea>';
}

function bookingroom_about_story_cb($post) {
    $heading = get_post_meta($post->ID, '_about_story_heading', true) ?: 'Kiến tạo những kỷ niệm <br> nghỉ dưỡng vô giá';
    $content = get_post_meta($post->ID, '_about_story_content', true) ?: '<p>Được thành lập với sứ mệnh nâng tầm trải nghiệm du lịch Việt, **Sonata Travel** không chỉ đơn thuần là một đại lý đặt phòng. Chúng tôi là người bạn đồng hành, giúp bạn tìm kiếm những không gian sống đẳng cấp và tinh tế nhất.</p><p>Mỗi khách sạn, mỗi căn phòng trong hệ thống của chúng tôi đều được lựa chọn kỹ lưỡng dựa trên tiêu chuẩn khắt khe về chất lượng dịch vụ, phong cách kiến trúc và sự tiện nghi.</p>';
    $stat1_num = get_post_meta($post->ID, '_about_stat1_num', true) ?: '500+';
    $stat1_label = get_post_meta($post->ID, '_about_stat1_label', true) ?: 'Điểm đến cao cấp';
    $stat2_num = get_post_meta($post->ID, '_about_stat2_num', true) ?: '15k+';
    $stat2_label = get_post_meta($post->ID, '_about_stat2_label', true) ?: 'Khách hàng hài lòng';
    
    echo '<p><strong>Tiêu đề:</strong></p><input type="text" name="about_story_heading" class="widefat" value="'.esc_attr($heading).'">';
    echo '<p><strong>Nội dung:</strong></p>';
    wp_editor($content, 'about_story_content', ['textarea_rows'=>5]);
    
    echo '<div style="display:flex; gap:20px; margin-top:20px;">';
    echo '<div><p><strong>Thống kê 1 (Số):</strong></p><input type="text" name="about_stat1_num" value="'.esc_attr($stat1_num).'"></div>';
    echo '<div><p><strong>Thống kê 1 (Nhãn):</strong></p><input type="text" name="about_stat1_label" value="'.esc_attr($stat1_label).'"></div>';
    echo '</div>';
    
    echo '<div style="display:flex; gap:20px; margin-top:10px;">';
    echo '<div><p><strong>Thống kê 2 (Số):</strong></p><input type="text" name="about_stat2_num" value="'.esc_attr($stat2_num).'"></div>';
    echo '<div><p><strong>Thống kê 2 (Nhãn):</strong></p><input type="text" name="about_stat2_label" value="'.esc_attr($stat2_label).'"></div>';
    echo '</div>';
}

function bookingroom_about_testimonial_cb($post) {
    $quote = get_post_meta($post->ID, '_about_testi_quote', true) ?: '"Dịch vụ của Sonata Travel thật sự vượt ngoài mong đợi. Họ không chỉ tìm cho tôi một căn phòng đẹp, mà còn tư vấn những điểm ăn uống, vui chơi rất tinh tế. Chắc chắn tôi sẽ quay lại."';
    $name = get_post_meta($post->ID, '_about_testi_name', true) ?: 'Anh Minh Nguyễn';
    $role = get_post_meta($post->ID, '_about_testi_role', true) ?: 'Giám đốc Điều hành, TechCorp';
    
    echo '<p><strong>Nội dung đánh giá:</strong></p><textarea name="about_testi_quote" class="widefat" rows="4">'.esc_textarea($quote).'</textarea>';
    echo '<p><strong>Tên khách hàng:</strong></p><input type="text" name="about_testi_name" class="widefat" value="'.esc_attr($name).'">';
    echo '<p><strong>Chức vụ / Công ty:</strong></p><input type="text" name="about_testi_role" class="widefat" value="'.esc_attr($role).'">';
}

function bookingroom_about_values_cb($post) {
    for($i=1; $i<=3; $i++) {
        $title = get_post_meta($post->ID, '_about_val'.$i.'_title', true);
        $desc = get_post_meta($post->ID, '_about_val'.$i.'_desc', true);
        
        if($title === '') {
            $defaults = ['Chất lượng hàng đầu', 'Giá tốt nhất', 'Hỗ trợ 24/7'];
            $title = $defaults[$i-1];
        }
        if($desc === '') {
            $default_desc = [
                'Chúng tôi chỉ hợp tác với các đối tác khách sạn đạt tiêu chuẩn 4-5 sao quốc tế.',
                'Nhờ mạng lưới đối tác rộng lớn, chúng tôi luôn có mức giá ưu đãi đặc quyền cho khách hàng.',
                'Đội ngũ chuyên viên của Sonata luôn sẵn sàng hỗ trợ bạn bất kể thời gian nào trong ngày.'
            ];
            $desc = $default_desc[$i-1];
        }

        echo '<div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">';
        echo '<h4>Giá trị '.$i.'</h4>';
        echo '<p><strong>Tiêu đề:</strong></p><input type="text" name="about_val'.$i.'_title" class="widefat" value="'.esc_attr($title).'">';
        echo '<p><strong>Mô tả:</strong></p><textarea name="about_val'.$i.'_desc" class="widefat" rows="2">'.esc_textarea($desc).'</textarea>';
        echo '</div>';
    }
}

function bookingroom_save_about_meta($post_id) {
    if (!isset($_POST['about_meta_nonce']) || !wp_verify_nonce($_POST['about_meta_nonce'], 'save_about_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'about_hero_title', 'about_hero_subtitle',
        'about_story_heading', 'about_story_content', 'about_stat1_num', 'about_stat1_label', 'about_stat2_num', 'about_stat2_label',
        'about_testi_quote', 'about_testi_name', 'about_testi_role',
        'about_val1_title', 'about_val1_desc', 'about_val2_title', 'about_val2_desc', 'about_val3_title', 'about_val3_desc'
    ];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = ($field === 'about_story_content' || $field === 'about_hero_title' || $field === 'about_hero_subtitle' || $field === 'about_story_heading') ? wp_kses_post($_POST[$field]) : sanitize_textarea_field($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'bookingroom_save_about_meta');


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


