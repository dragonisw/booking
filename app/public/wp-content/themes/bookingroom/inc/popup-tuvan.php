<?php
/**
 * Popup Tư Vấn - Consultation Popup
 * Hiển thị popup đăng ký tư vấn với hình ảnh và form liên hệ
 */

if (!defined('ABSPATH')) exit;

// ──────────────────────────────────────────────────────────────────────────────
// 1. ENQUEUE STYLES & SCRIPTS
// ──────────────────────────────────────────────────────────────────────────────
add_action('wp_enqueue_scripts', 'tuvan_popup_assets');
function tuvan_popup_assets() {
    // Google Font – Be Vietnam Pro
    wp_enqueue_style(
        'tuvan-bevietnam-font',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap',
        [],
        null
    );
    wp_enqueue_style(
        'tuvan-popup-css',
        get_stylesheet_directory_uri() . '/assets/css/popup-tuvan.css',
        ['tuvan-bevietnam-font'],
        '2.0.0'
    );
    wp_enqueue_script(
        'tuvan-popup-js',
        get_stylesheet_directory_uri() . '/assets/js/popup-tuvan.js',
        ['jquery'],
        '2.0.0',
        true
    );
    wp_localize_script('tuvan-popup-js', 'tuvan_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tuvan_popup_nonce'),
    ]);
}

// ──────────────────────────────────────────────────────────────────────────────
// 2. RENDER POPUP HTML VÀO FOOTER
// ──────────────────────────────────────────────────────────────────────────────
add_action('wp_footer', 'tuvan_popup_html', 5);
function tuvan_popup_html() {
    // Lấy ảnh từ Customizer hoặc dùng ảnh mặc định của theme
    $popup_image = get_theme_mod('tuvan_popup_image', '');
    if (empty($popup_image)) {
        // Dùng ảnh featured của trang chủ nếu có, hoặc ảnh đại diện khách sạn
        $front_page_id = get_option('page_on_front');
        if ($front_page_id && has_post_thumbnail($front_page_id)) {
            $popup_image = get_the_post_thumbnail_url($front_page_id, 'large');
        } else {
            $popup_image = get_stylesheet_directory_uri() . '/screenshot.png';
        }
    }

    $hotel_name  = get_bloginfo('name');
    $hotel_phone = get_theme_mod('footer_phone', '1900 2663');
    ?>
    <!-- ===== POPUP TƯ VẤN ===== -->
    <?php $popup_delay = absint(get_theme_mod('tuvan_popup_delay', 5)); ?>
    <div id="tuvan-popup-overlay" class="tuvan-overlay" role="dialog" aria-modal="true" aria-labelledby="tuvan-popup-title" aria-hidden="true" data-delay="<?php echo esc_attr($popup_delay); ?>">
        <div class="tuvan-popup" id="tuvan-popup-box">

            <!-- Nút đóng -->
            <button class="tuvan-close" id="tuvan-close-btn" aria-label="Đóng popup tư vấn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>

            <!-- Cột trái: Ảnh -->
            <div class="tuvan-image-col">
                <img src="<?php echo esc_url($popup_image); ?>" alt="<?php echo esc_attr($hotel_name); ?>" class="tuvan-image" loading="lazy"/>
                <div class="tuvan-image-overlay">
                    <div class="tuvan-image-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                        <span>Dịch vụ 5 sao</span>
                    </div>
                    <p class="tuvan-image-tagline">Trải nghiệm nghỉ dưỡng đẳng cấp cùng <?php echo esc_html($hotel_name); ?></p>
                </div>
            </div>

            <!-- Cột phải: Form -->
            <div class="tuvan-form-col">
                <div class="tuvan-form-header">
                    <div class="tuvan-form-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97z" clip-rule="evenodd"/></svg>
                    </div>
                    <h2 class="tuvan-title" id="tuvan-popup-title">Đăng Ký Tư Vấn</h2>
                    <p class="tuvan-subtitle">Để lại thông tin, chúng tôi sẽ liên hệ ngay!</p>
                </div>

                <form id="tuvan-popup-form" class="tuvan-form" novalidate>
                    <?php wp_nonce_field('tuvan_popup_nonce', 'tuvan_nonce_field'); ?>

                    <div class="tuvan-row">
                        <div class="tuvan-field">
                            <label for="tuvan-name" class="tuvan-label">Họ Tên <span class="tuvan-required">(*)</span></label>
                            <input type="text" id="tuvan-name" name="tuvan_name" class="tuvan-input" placeholder="Nguyễn Văn A" required autocomplete="name"/>
                        </div>
                        <div class="tuvan-field">
                            <label for="tuvan-phone" class="tuvan-label">Điện Thoại <span class="tuvan-required">(*)</span></label>
                            <input type="tel" id="tuvan-phone" name="tuvan_phone" class="tuvan-input" placeholder="0901 234 567" required autocomplete="tel"/>
                        </div>
                    </div>

                    <div class="tuvan-field tuvan-field--full">
                        <label for="tuvan-email" class="tuvan-label">Email</label>
                        <input type="email" id="tuvan-email" name="tuvan_email" class="tuvan-input" placeholder="email@example.com" autocomplete="email"/>
                    </div>

                    <div class="tuvan-field tuvan-field--full">
                        <label for="tuvan-message" class="tuvan-label">Lời Nhắn</label>
                        <textarea id="tuvan-message" name="tuvan_message" class="tuvan-input tuvan-textarea" placeholder="Tôi muốn tư vấn về..." rows="3"></textarea>
                    </div>

                    <!-- Thông báo kết quả -->
                    <div id="tuvan-result" class="tuvan-result" aria-live="polite" hidden></div>

                    <button type="submit" id="tuvan-submit-btn" class="tuvan-submit">
                        <span class="tuvan-submit-text">Gửi Đăng Ký</span>
                        <span class="tuvan-submit-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                        <span class="tuvan-submit-loading" hidden>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18" class="tuvan-spinner"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            Đang gửi...
                        </span>
                    </button>

                    <p class="tuvan-phone-note">
                        Hoặc gọi ngay:
                        <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $hotel_phone)); ?>" class="tuvan-phone-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/></svg>
                            <?php echo esc_html($hotel_phone); ?>
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- NÚT MỞ POPUP (nút cố định góc phải) -->
    <button id="tuvan-trigger-btn" class="tuvan-trigger" aria-label="Mở form đăng ký tư vấn" title="Đăng ký tư vấn">
        <span class="tuvan-trigger-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97z" clip-rule="evenodd"/></svg>
        </span>
        <span class="tuvan-trigger-label">TƯ VẤN</span>
        <span class="tuvan-trigger-pulse"></span>
    </button>
    <!-- ===== END POPUP TƯ VẤN ===== -->
    <?php
}

// ──────────────────────────────────────────────────────────────────────────────
// 3. XỬ LÝ AJAX GỬI FORM
// ──────────────────────────────────────────────────────────────────────────────
add_action('wp_ajax_tuvan_submit',        'tuvan_handle_submission');
add_action('wp_ajax_nopriv_tuvan_submit', 'tuvan_handle_submission');

function tuvan_handle_submission() {
    // Verify nonce
    if (!check_ajax_referer('tuvan_popup_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Yêu cầu không hợp lệ.'], 403);
    }

    // Validate fields
    $name    = sanitize_text_field($_POST['tuvan_name']   ?? '');
    $phone   = sanitize_text_field($_POST['tuvan_phone']  ?? '');
    $email   = sanitize_email($_POST['tuvan_email']       ?? '');
    $message = sanitize_textarea_field($_POST['tuvan_message'] ?? '');

    if (empty($name)) {
        wp_send_json_error(['message' => 'Vui lòng nhập họ tên.']);
    }
    if (empty($phone)) {
        wp_send_json_error(['message' => 'Vui lòng nhập số điện thoại.']);
    }
    if (!preg_match('/^[\d\s\+\-\(\)]{9,15}$/', $phone)) {
        wp_send_json_error(['message' => 'Số điện thoại không hợp lệ.']);
    }

    // Ghi vào database (post type tùy chỉnh nếu có, hoặc options)
    $lead_id = wp_insert_post([
        'post_type'   => 'tuvan_lead',
        'post_title'  => $name . ' – ' . $phone,
        'post_status' => 'publish',
        'meta_input'  => [
            '_tuvan_name'    => $name,
            '_tuvan_phone'   => $phone,
            '_tuvan_email'   => $email,
            '_tuvan_message' => $message,
            '_tuvan_ip'      => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            '_tuvan_time'    => current_time('mysql'),
        ],
    ]);

    // Gửi email thông báo cho admin
    $admin_email = get_option('admin_email');
    $blog_name   = get_bloginfo('name');
    $subject     = "[{$blog_name}] Đăng ký tư vấn mới từ {$name}";
    $body = "
        <h2>Đăng ký tư vấn mới</h2>
        <table cellpadding='8' style='border-collapse:collapse;width:100%;'>
            <tr><td><strong>Họ tên:</strong></td><td>{$name}</td></tr>
            <tr><td><strong>Điện thoại:</strong></td><td>{$phone}</td></tr>
            <tr><td><strong>Email:</strong></td><td>{$email}</td></tr>
            <tr><td><strong>Lời nhắn:</strong></td><td>{$message}</td></tr>
            <tr><td><strong>Thời gian:</strong></td><td>" . current_time('d/m/Y H:i') . "</td></tr>
        </table>
    ";
    wp_mail($admin_email, $subject, $body, ['Content-Type: text/html; charset=UTF-8']);

    wp_send_json_success([
        'message' => 'Cảm ơn bạn! Chúng tôi sẽ liên hệ trong thời gian sớm nhất.',
    ]);
}

// ──────────────────────────────────────────────────────────────────────────────
// 4. ĐĂNG KÝ CUSTOM POST TYPE "tuvan_lead" ĐỂ LƯU LEADS
// ──────────────────────────────────────────────────────────────────────────────
add_action('init', 'tuvan_register_lead_cpt');
function tuvan_register_lead_cpt() {
    register_post_type('tuvan_lead', [
        'labels'       => [
            'name'          => 'Đăng ký tư vấn',
            'singular_name' => 'Lead tư vấn',
            'menu_name'     => '📋 Tư vấn Leads',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-format-chat',
        'supports'     => ['title', 'custom-fields'],
        'capabilities' => ['create_posts' => 'do_not_allow'],
        'map_meta_cap' => true,
    ]);
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. HIỂN THỊ META TRONG ADMIN
// ──────────────────────────────────────────────────────────────────────────────
add_action('add_meta_boxes', 'tuvan_lead_metabox');
function tuvan_lead_metabox() {
    add_meta_box(
        'tuvan_lead_info',
        'Thông tin tư vấn',
        'tuvan_lead_metabox_cb',
        'tuvan_lead',
        'normal',
        'high'
    );
}

function tuvan_lead_metabox_cb($post) {
    $fields = [
        '_tuvan_name'    => 'Họ tên',
        '_tuvan_phone'   => 'Điện thoại',
        '_tuvan_email'   => 'Email',
        '_tuvan_message' => 'Lời nhắn',
        '_tuvan_time'    => 'Thời gian',
        '_tuvan_ip'      => 'IP',
    ];
    echo '<table class="form-table" style="width:100%">';
    foreach ($fields as $key => $label) {
        $val = get_post_meta($post->ID, $key, true);
        echo "<tr><th style='width:140px'><strong>{$label}</strong></th><td>" . esc_html($val) . '</td></tr>';
    }
    echo '</table>';
}

// ──────────────────────────────────────────────────────────────────────────────
// 6. CUSTOMIZER OPTIONS
// ──────────────────────────────────────────────────────────────────────────────
add_action('customize_register', 'tuvan_popup_customizer');
function tuvan_popup_customizer($wp_customize) {
    $wp_customize->add_section('tuvan_popup_section', [
        'title'    => '💬 Popup Tư Vấn',
        'priority' => 50,
    ]);

    // Ảnh popup
    $wp_customize->add_setting('tuvan_popup_image', ['default' => '', 'sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'tuvan_popup_image', [
        'label'   => 'Ảnh bên trái popup',
        'section' => 'tuvan_popup_section',
    ]));

    // Delay tự động mở (giây)
    $wp_customize->add_setting('tuvan_popup_delay', ['default' => '5', 'sanitize_callback' => 'absint']);
    $wp_customize->add_control('tuvan_popup_delay', [
        'label'       => 'Tự động mở sau (giây, 0 = tắt)',
        'section'     => 'tuvan_popup_section',
        'type'        => 'number',
        'input_attrs' => ['min' => 0, 'max' => 60, 'step' => 1],
    ]);

    // Màu chủ đạo
    $wp_customize->add_setting('tuvan_popup_color', ['default' => '#1e4d8c', 'sanitize_callback' => 'sanitize_hex_color']);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'tuvan_popup_color', [
        'label'   => 'Màu chủ đạo form',
        'section' => 'tuvan_popup_section',
    ]));
}
