<?php
/**
 * Header Customizer – Tùy chọn sắp xếp Header
 *
 * Đăng ký toàn bộ settings & controls cho phần Header
 * trong WordPress Customizer (Appearance → Customize → Header).
 *
 * Các nhóm tùy chọn:
 *  1. Layout  – Bố cục (logo trái/giữa/phải, nav vị trí, chiều cao)
 *  2. Style   – Kiểu dáng (nền, màu text, glassmorphism, border)
 *  3. Logo    – Kích thước logo, hiển thị tagline
 *  4. Nút CTA – Text, URL, màu nền, hiển thị/ẩn
 *  5. Sticky  – Sticky / Transparent / Scroll effect
 *  6. Mobile  – Kiểu menu mobile (Drawer / Fullscreen)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* =========================================================
   REGISTER: add_action → customize_register
   ========================================================= */
add_action( 'customize_register', 'bookingroom_header_customizer_register' );

function bookingroom_header_customizer_register( $wp_customize ) {

    /* ---------------------------------------------------------
       PANEL: Header Settings
       --------------------------------------------------------- */
    $wp_customize->add_panel( 'bookingroom_header_panel', array(
        'title'       => __( '🏠 Header – Tùy chọn sắp xếp', 'bookingroom' ),
        'description' => __( 'Toàn bộ tùy chọn bố cục, màu sắc, logo và hành vi của header.', 'bookingroom' ),
        'priority'    => 25,
    ) );

    /* =========================================================
       SECTION 1: Layout – Bố cục Header
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_layout', array(
        'title'    => __( '📐 Bố cục Header', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 10,
    ) );

    // --- Kiểu bố cục tổng thể ---
    $wp_customize->add_setting( 'header_layout', array(
        'default'           => 'logo-left-nav-center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_layout', array(
        'label'       => __( 'Kiểu bố cục', 'bookingroom' ),
        'description' => __( 'Chọn cách sắp xếp Logo, Nav và Actions trên header.', 'bookingroom' ),
        'section'     => 'bookingroom_header_layout',
        'type'        => 'select',
        'choices'     => array(
            'logo-left-nav-center'  => __( 'Logo trái · Nav giữa · Actions phải', 'bookingroom' ),
            'logo-left-nav-right'   => __( 'Logo trái · Nav + Actions phải', 'bookingroom' ),
            'logo-center-nav-split' => __( 'Logo giữa · Nav chia 2 bên', 'bookingroom' ),
            'logo-center-nav-below' => __( 'Logo giữa · Nav bên dưới (2 hàng)', 'bookingroom' ),
            'logo-right-nav-left'   => __( 'Logo phải · Nav trái', 'bookingroom' ),
            'minimal-logo-only'     => __( 'Tối giản – Chỉ Logo + Menu icon', 'bookingroom' ),
        ),
    ) );

    // --- Chiều cao header (desktop) ---
    $wp_customize->add_setting( 'header_height', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_height', array(
        'label'       => __( 'Chiều cao Header (px)', 'bookingroom' ),
        'description' => __( 'Desktop. Tối thiểu 50px, tối đa 140px.', 'bookingroom' ),
        'section'     => 'bookingroom_header_layout',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 50, 'max' => 140, 'step' => 2 ),
    ) );

    // --- Chiều cao header khi scroll ---
    $wp_customize->add_setting( 'header_height_scrolled', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_height_scrolled', array(
        'label'       => __( 'Chiều cao khi cuộn (px)', 'bookingroom' ),
        'section'     => 'bookingroom_header_layout',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 40, 'max' => 120, 'step' => 2 ),
    ) );

    // --- Padding ngang ---
    $wp_customize->add_setting( 'header_padding_x', array(
        'default'           => 'medium',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_padding_x', array(
        'label'   => __( 'Khoảng cách 2 bên (Padding)', 'bookingroom' ),
        'section' => 'bookingroom_header_layout',
        'type'    => 'select',
        'choices' => array(
            'small'   => __( 'Nhỏ (1.5rem)', 'bookingroom' ),
            'medium'  => __( 'Vừa (3rem)', 'bookingroom' ),
            'large'   => __( '📐 Rộng (5rem)', 'bookingroom' ),
            'full'    => __( 'Full width (0.5rem)', 'bookingroom' ),
        ),
    ) );

    // --- Hiển thị Language Switcher ---
    $wp_customize->add_setting( 'header_show_lang', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_show_lang', array(
        'label'   => __( 'Hiển thị Language Switcher (VI/EN)', 'bookingroom' ),
        'section' => 'bookingroom_header_layout',
        'type'    => 'checkbox',
    ) );

    // --- Hiển thị Icon Bar (Fullscreen Menu) ---
    $wp_customize->add_setting( 'header_show_iconbar', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_show_iconbar', array(
        'label'   => __( 'Hiển thị nút Icon Bar Menu', 'bookingroom' ),
        'section' => 'bookingroom_header_layout',
        'type'    => 'checkbox',
    ) );

    // --- Hiển thị Desktop Nav ---
    $wp_customize->add_setting( 'header_show_desktop_nav', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_show_desktop_nav', array(
        'label'   => __( 'Hiển thị Navigation desktop', 'bookingroom' ),
        'section' => 'bookingroom_header_layout',
        'type'    => 'checkbox',
    ) );

    /* =========================================================
       SECTION 2: Style – Màu sắc & Kiểu dáng
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_style', array(
        'title'    => __( '🎨 Màu sắc & Kiểu dáng', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 20,
    ) );

    // --- Màu nền header ---
    $wp_customize->add_setting( 'header_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_bg_color', array(
        'label'   => __( 'Màu nền Header', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
    ) ) );

    // --- Độ trong suốt nền ---
    $wp_customize->add_setting( 'header_bg_opacity', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_bg_opacity', array(
        'label'       => __( 'Độ mờ nền (%) – 100 = đặc, 0 = trong suốt', 'bookingroom' ),
        'section'     => 'bookingroom_header_style',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    // --- Màu text nav link ---
    $wp_customize->add_setting( 'header_nav_color', array(
        'default'           => '#475569',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_nav_color', array(
        'label'   => __( 'Màu chữ Nav link', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
    ) ) );

    // --- Màu text nav hover/active ---
    $wp_customize->add_setting( 'header_nav_hover_color', array(
        'default'           => '#2563eb',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_nav_hover_color', array(
        'label'   => __( 'Màu chữ Nav Hover / Active', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
    ) ) );

    // --- Kiểu underline nav ---
    $wp_customize->add_setting( 'header_nav_underline', array(
        'default'           => 'slide',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_nav_underline', array(
        'label'   => __( 'Hiệu ứng Nav Underline', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
        'type'    => 'select',
        'choices' => array(
            'slide'   => __( '→ Trượt từ trung tâm', 'bookingroom' ),
            'left'    => __( '← Trượt từ trái', 'bookingroom' ),
            'none'    => __( '✗ Không có', 'bookingroom' ),
            'dot'     => __( '• Chấm bên dưới', 'bookingroom' ),
            'bg'      => __( '▬ Nền highlight', 'bookingroom' ),
        ),
    ) );

    // --- Kiểu box-shadow ---
    $wp_customize->add_setting( 'header_shadow', array(
        'default'           => 'soft',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_shadow', array(
        'label'   => __( 'Bóng đổ (Shadow)', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
        'type'    => 'select',
        'choices' => array(
            'none'   => __( 'Không có', 'bookingroom' ),
            'soft'   => __( 'Mềm (mặc định)', 'bookingroom' ),
            'medium' => __( 'Vừa', 'bookingroom' ),
            'strong' => __( 'Mạnh', 'bookingroom' ),
        ),
    ) );

    // --- Border bottom ---
    $wp_customize->add_setting( 'header_border_bottom', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_border_bottom', array(
        'label'   => __( 'Hiện border dưới header', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
        'type'    => 'checkbox',
    ) );

    // --- Màu border bottom ---
    $wp_customize->add_setting( 'header_border_color', array(
        'default'           => '#e2e8f0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_border_color', array(
        'label'   => __( 'Màu border dưới', 'bookingroom' ),
        'section' => 'bookingroom_header_style',
    ) ) );

    /* =========================================================
       SECTION 3: Logo
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_logo', array(
        'title'    => __( '🖼 Logo', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 30,
    ) );

    // --- Chiều cao logo ---
    $wp_customize->add_setting( 'logo_height', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'logo_height', array(
        'label'       => __( 'Chiều cao Logo (px)', 'bookingroom' ),
        'description' => __( 'Desktop. Mobile tự co lại.', 'bookingroom' ),
        'section'     => 'bookingroom_header_logo',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 30, 'max' => 120, 'step' => 2 ),
    ) );

    // --- Hiển thị tagline ---
    $wp_customize->add_setting( 'header_show_tagline', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_show_tagline', array(
        'label'   => __( 'Hiển thị Tagline bên dưới tên site', 'bookingroom' ),
        'section' => 'bookingroom_header_logo',
        'type'    => 'checkbox',
    ) );

    // --- Logo trắng trên trang chủ ---
    $wp_customize->add_setting( 'header_logo_invert_home', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_logo_invert_home', array(
        'label'       => __( 'Đảo màu Logo thành trắng trên trang chủ', 'bookingroom' ),
        'description' => __( 'Áp dụng filter CSS invert, phù hợp logo đen trên nền tối.', 'bookingroom' ),
        'section'     => 'bookingroom_header_logo',
        'type'        => 'checkbox',
    ) );

    /* =========================================================
       SECTION 4: Nút CTA (Call-to-Action)
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_cta', array(
        'title'    => __( '🔘 Nút CTA (Đặt phòng)', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 40,
    ) );

    // --- Text nút ---
    $wp_customize->add_setting( 'header_btn_text', array(
        'default'           => 'Đặt phòng ngay',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_btn_text', array(
        'label'   => __( 'Text nút CTA', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
        'type'    => 'text',
    ) );

    // --- URL nút ---
    $wp_customize->add_setting( 'header_btn_url', array(
        'default'           => '/booking',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'header_btn_url', array(
        'label'   => __( 'URL nút CTA', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
        'type'    => 'url',
    ) );

    // --- Màu nền nút ---
    $wp_customize->add_setting( 'header_btn_bg', array(
        'default'           => '#2563eb',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_btn_bg', array(
        'label'   => __( 'Màu nền nút CTA', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
    ) ) );

    // --- Màu chữ nút ---
    $wp_customize->add_setting( 'header_btn_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_btn_text_color', array(
        'label'   => __( 'Màu chữ nút CTA', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
    ) ) );

    // --- Kiểu nút ---
    $wp_customize->add_setting( 'header_btn_style', array(
        'default'           => 'rounded',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_btn_style', array(
        'label'   => __( 'Kiểu nút', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
        'type'    => 'select',
        'choices' => array(
            'rounded' => __( 'Pill (bo tròn đầy đủ)', 'bookingroom' ),
            'soft'    => __( 'Bo góc mềm (8px)', 'bookingroom' ),
            'square'  => __( 'Vuông góc', 'bookingroom' ),
            'outline' => __( 'Viền ngoài (Outline)', 'bookingroom' ),
            'ghost'   => __( 'Ghost (nền trong suốt)', 'bookingroom' ),
        ),
    ) );

    // --- Hiển thị nút trên mobile ---
    $wp_customize->add_setting( 'header_btn_show_mobile', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_btn_show_mobile', array(
        'label'   => __( 'Hiển thị nút CTA trên Mobile', 'bookingroom' ),
        'section' => 'bookingroom_header_cta',
        'type'    => 'checkbox',
    ) );

    /* =========================================================
       SECTION 5: Sticky & Scroll Behavior
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_sticky', array(
        'title'    => __( '📌 Sticky & Scroll Behavior', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 50,
    ) );

    // --- Kiểu sticky ---
    $wp_customize->add_setting( 'header_sticky_type', array(
        'default'           => 'sticky',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'header_sticky_type', array(
        'label'   => __( 'Kiểu cố định Header', 'bookingroom' ),
        'section' => 'bookingroom_header_sticky',
        'type'    => 'select',
        'choices' => array(
            'static'          => __( 'Không cố định (Static)', 'bookingroom' ),
            'sticky'          => __( 'Sticky (dán đầu trang khi scroll)', 'bookingroom' ),
            'fixed-always'    => __( 'Fixed – Luôn hiển thị trên cùng', 'bookingroom' ),
            'hide-on-scroll'  => __( 'Ẩn khi scroll xuống, hiện khi scroll lên', 'bookingroom' ),
        ),
    ) );

    // --- Transparent trên trang chủ ---
    $wp_customize->add_setting( 'header_transparent_home', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'header_transparent_home', array(
        'label'       => __( 'Trong suốt trên trang chủ (đè lên Hero)', 'bookingroom' ),
        'description' => __( 'Header sẽ không có nền, hiển thị đè lên ảnh hero.', 'bookingroom' ),
        'section'     => 'bookingroom_header_sticky',
        'type'        => 'checkbox',
    ) );

    // --- Glassmorphism khi scroll ---
    $wp_customize->add_setting( 'header_glassmorphism', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'header_glassmorphism', array(
        'label'       => __( 'Glassmorphism khi cuộn trang', 'bookingroom' ),
        'description' => __( 'Nền mờ + blur khi scroll xuống.', 'bookingroom' ),
        'section'     => 'bookingroom_header_sticky',
        'type'        => 'checkbox',
    ) );

    // --- Scroll threshold ---
    $wp_customize->add_setting( 'header_scroll_threshold', array(
        'default'           => 20,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'header_scroll_threshold', array(
        'label'       => __( 'Ngưỡng scroll kích hoạt hiệu ứng (px)', 'bookingroom' ),
        'section'     => 'bookingroom_header_sticky',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 0, 'max' => 200, 'step' => 10 ),
    ) );

    /* =========================================================
       SECTION 6: Mobile Menu
       ========================================================= */
    $wp_customize->add_section( 'bookingroom_header_mobile', array(
        'title'    => __( '📱 Menu Mobile', 'bookingroom' ),
        'panel'    => 'bookingroom_header_panel',
        'priority' => 60,
    ) );

    // --- Kiểu menu mobile ---
    $wp_customize->add_setting( 'header_mobile_menu_type', array(
        'default'           => 'drawer',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'header_mobile_menu_type', array(
        'label'   => __( 'Kiểu menu mobile', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'select',
        'choices' => array(
            'drawer'     => __( 'Drawer (kéo từ phải)', 'bookingroom' ),
            'fullscreen' => __( 'Fullscreen Overlay', 'bookingroom' ),
            'dropdown'   => __( 'Dropdown bên dưới', 'bookingroom' ),
        ),
    ) );

    // --- Vị trí drawer ---
    $wp_customize->add_setting( 'header_drawer_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'header_drawer_position', array(
        'label'   => __( 'Vị trí Drawer (khi chọn Drawer)', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'select',
        'choices' => array(
            'right' => __( 'Từ phải', 'bookingroom' ),
            'left'  => __( 'Từ trái', 'bookingroom' ),
        ),
    ) );

    // --- Màu nền fullscreen overlay ---
    $wp_customize->add_setting( 'header_fs_menu_bg', array(
        'default'           => '#0a2618',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_fs_menu_bg', array(
        'label'       => __( 'Màu nền Fullscreen Menu', 'bookingroom' ),
        'description' => __( 'Màu nền khi mở menu toàn màn hình.', 'bookingroom' ),
        'section'     => 'bookingroom_header_mobile',
    ) ) );

    // --- Social: Facebook ---
    $wp_customize->add_setting( 'social_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'social_facebook', array(
        'label'   => __( 'Facebook URL', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'url',
    ) );

    // --- Social: YouTube ---
    $wp_customize->add_setting( 'social_youtube', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'social_youtube', array(
        'label'   => __( 'YouTube URL', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'url',
    ) );

    // --- Social: TikTok ---
    $wp_customize->add_setting( 'social_tiktok', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'social_tiktok', array(
        'label'   => __( 'TikTok URL', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'url',
    ) );

    // --- Social: Instagram ---
    $wp_customize->add_setting( 'social_instagram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'social_instagram', array(
        'label'   => __( 'Instagram URL', 'bookingroom' ),
        'section' => 'bookingroom_header_mobile',
        'type'    => 'url',
    ) );
}

/* =========================================================
   OUTPUT: In CSS động từ Customizer settings
   ========================================================= */
add_action( 'wp_head', 'bookingroom_header_customizer_css', 99 );

function bookingroom_header_customizer_css() {
    // Lấy các giá trị
    $height          = absint( get_theme_mod( 'header_height', 80 ) );
    $height_s        = absint( get_theme_mod( 'header_height_scrolled', 64 ) );
    $bg_color        = sanitize_hex_color( get_theme_mod( 'header_bg_color', '#ffffff' ) );
    $bg_opacity      = min( 100, max( 0, absint( get_theme_mod( 'header_bg_opacity', 100 ) ) ) );
    $nav_color       = sanitize_hex_color( get_theme_mod( 'header_nav_color', '#475569' ) );
    $nav_hover       = sanitize_hex_color( get_theme_mod( 'header_nav_hover_color', '#2563eb' ) );
    $btn_bg          = sanitize_hex_color( get_theme_mod( 'header_btn_bg', '#2563eb' ) );
    $btn_color       = sanitize_hex_color( get_theme_mod( 'header_btn_text_color', '#ffffff' ) );
    $border_color    = sanitize_hex_color( get_theme_mod( 'header_border_color', '#e2e8f0' ) );
    $shadow          = sanitize_text_field( get_theme_mod( 'header_shadow', 'soft' ) );
    $btn_style       = sanitize_text_field( get_theme_mod( 'header_btn_style', 'rounded' ) );
    $underline       = sanitize_text_field( get_theme_mod( 'header_nav_underline', 'slide' ) );
    $padding_x       = sanitize_text_field( get_theme_mod( 'header_padding_x', 'medium' ) );
    $fs_bg           = sanitize_hex_color( get_theme_mod( 'header_fs_menu_bg', '#0a2618' ) );
    $show_border     = (bool) get_theme_mod( 'header_border_bottom', false );
    $glassmorphism   = (bool) get_theme_mod( 'header_glassmorphism', true );
    $logo_invert     = (bool) get_theme_mod( 'header_logo_invert_home', false );
    $btn_mobile      = (bool) get_theme_mod( 'header_btn_show_mobile', false );
    $show_lang       = (bool) get_theme_mod( 'header_show_lang', true );
    $show_iconbar    = (bool) get_theme_mod( 'header_show_iconbar', true );
    $show_nav        = (bool) get_theme_mod( 'header_show_desktop_nav', true );
    $layout          = sanitize_text_field( get_theme_mod( 'header_layout', 'logo-left-nav-center' ) );
    $show_tagline    = (bool) get_theme_mod( 'header_show_tagline', false );

    // Padding map
    $padding_map = array(
        'small'  => '1.5rem',
        'medium' => '3rem',
        'large'  => '5rem',
        'full'   => '0.5rem',
    );
    $px = isset( $padding_map[ $padding_x ] ) ? $padding_map[ $padding_x ] : '3rem';

    // Shadow map
    $shadow_map = array(
        'none'   => 'none',
        'soft'   => '0 1px 4px rgba(0,0,0,0.08)',
        'medium' => '0 4px 16px rgba(0,0,0,0.12)',
        'strong' => '0 8px 32px rgba(0,0,0,0.18)',
    );
    $box_shadow = isset( $shadow_map[ $shadow ] ) ? $shadow_map[ $shadow ] : $shadow_map['soft'];

    // Btn border-radius
    $btn_radius_map = array(
        'rounded' => '9999px',
        'soft'    => '8px',
        'square'  => '0',
        'outline' => '9999px',
        'ghost'   => '9999px',
    );
    $btn_radius = isset( $btn_radius_map[ $btn_style ] ) ? $btn_radius_map[ $btn_style ] : '9999px';

    // Convert hex to rgb for rgba usage
    $rgb = bookingroom_hex2rgb( $bg_color );
    $rgba_bg = $rgb ? "rgba({$rgb[0]},{$rgb[1]},{$rgb[2]}," . ( $bg_opacity / 100 ) . ')' : $bg_color;

    // Nav position theo layout
    $nav_position = 'center'; // default
    if ( in_array( $layout, array( 'logo-left-nav-right', 'logo-right-nav-left' ), true ) ) {
        $nav_position = 'right-group';
    } elseif ( $layout === 'logo-center-nav-split' ) {
        $nav_position = 'hidden'; // handled by split layout
    } elseif ( $layout === 'minimal-logo-only' ) {
        $nav_position = 'hidden';
    }

    ob_start();
    ?>
    <style id="bookingroom-header-customizer-css">
    /* ── Generated by Header Customizer ── */

    /* Chiều cao & padding */
    .header-wrap {
        padding: 0 <?php echo esc_attr( $px ); ?>;
        height: <?php echo esc_attr( $height ); ?>px;
    }
    #masthead.is-scrolled .header-wrap {
        height: <?php echo esc_attr( $height_s ); ?>px;
    }

    /* Màu nền */
    #masthead {
        background: <?php echo esc_attr( $rgba_bg ); ?>;
        box-shadow: <?php echo esc_attr( $box_shadow ); ?>;
        <?php if ( $show_border ) : ?>
        border-bottom: 1px solid <?php echo esc_attr( $border_color ); ?>;
        <?php endif; ?>
    }

    <?php if ( $glassmorphism ) : ?>
    #masthead.is-scrolled {
        background: rgba(<?php echo esc_attr( $rgb ? implode(',', $rgb) : '255,255,255' ); ?>, 0.88) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-shadow: 0 2px 20px rgba(0,0,0,0.08) !important;
        border-bottom: 1px solid rgba(226,232,240,0.7);
    }
    <?php else : ?>
    #masthead.is-scrolled {
        background: <?php echo esc_attr( $rgba_bg ); ?> !important;
        backdrop-filter: none;
    }
    <?php endif; ?>

    /* Nav link colors */
    #primary-menu-list > li > a {
        color: <?php echo esc_attr( $nav_color ); ?>;
    }
    #primary-menu-list > li > a:hover,
    #primary-menu-list > li.current-menu-item > a,
    #primary-menu-list > li.current-menu-ancestor > a {
        color: <?php echo esc_attr( $nav_hover ); ?>;
    }
    #primary-menu-list > li > a::after {
        background: <?php echo esc_attr( $nav_hover ); ?>;
    }

    <?php // Underline style
    if ( $underline === 'none' ) : ?>
    #primary-menu-list > li > a::after { display: none; }
    <?php elseif ( $underline === 'left' ) : ?>
    #primary-menu-list > li > a::after { transform-origin: left !important; }
    <?php elseif ( $underline === 'dot' ) : ?>
    #primary-menu-list > li > a::after {
        width: 6px; height: 6px;
        border-radius: 50%;
        left: 50%; transform: translateX(-50%) scaleX(0);
        right: auto; bottom: 4px;
    }
    #primary-menu-list > li > a:hover::after,
    #primary-menu-list > li.current-menu-item > a::after {
        transform: translateX(-50%) scaleX(1);
    }
    <?php elseif ( $underline === 'bg' ) : ?>
    #primary-menu-list > li > a::after { display: none; }
    #primary-menu-list > li > a:hover,
    #primary-menu-list > li.current-menu-item > a {
        background: rgba(<?php echo esc_attr( $rgb ? implode(',', bookingroom_hex2rgb($nav_hover)) : '37,99,235' ); ?>, 0.08);
        border-radius: 8px;
    }
    <?php endif; ?>

    /* Nút CTA */
    .btn-booking {
        background: <?php echo esc_attr( $btn_bg ); ?>;
        color: <?php echo esc_attr( $btn_color ); ?> !important;
        border-radius: <?php echo esc_attr( $btn_radius ); ?>;
        <?php if ( $btn_style === 'outline' ) : ?>
        background: transparent !important;
        border: 2px solid <?php echo esc_attr( $btn_bg ); ?>;
        color: <?php echo esc_attr( $btn_bg ); ?> !important;
        box-shadow: none;
        <?php elseif ( $btn_style === 'ghost' ) : ?>
        background: transparent !important;
        border: 1px solid rgba(0,0,0,0.15);
        box-shadow: none;
        <?php endif; ?>
    }
    .btn-booking:hover {
        <?php if ( $btn_style === 'outline' ) : ?>
        background: <?php echo esc_attr( $btn_bg ); ?> !important;
        color: <?php echo esc_attr( $btn_color ); ?> !important;
        <?php endif; ?>
    }

    <?php if ( $btn_mobile ) : ?>
    @media (max-width: 767px) {
        .btn-booking { display: inline-block !important; font-size: 0.8rem; padding: 0.45rem 1rem; }
    }
    <?php endif; ?>

    /* Ẩn/hiện các phần tử */
    <?php if ( ! $show_lang ) : ?>
    .lang-switcher, .fs-menu-lang { display: none !important; }
    <?php endif; ?>

    <?php if ( ! $show_iconbar ) : ?>
    .btn-iconbar-menu { display: none !important; }
    <?php endif; ?>

    <?php if ( ! $show_nav ) : ?>
    #site-navigation { display: none !important; }
    <?php endif; ?>

    <?php if ( ! $show_tagline ) : ?>
    .site-description { display: none !important; }
    <?php endif; ?>

    /* Logo invert trên trang chủ */
    <?php if ( $logo_invert ) : ?>
    .is-home-style #masthead:not(.is-scrolled) .custom-logo-container img {
        filter: brightness(0) invert(1);
    }
    <?php endif; ?>

    /* Fullscreen menu background */
    #fs-menu-bg {
        background: linear-gradient(160deg, <?php echo esc_attr( $fs_bg ); ?> 0%, <?php echo esc_attr( bookingroom_darken_hex( $fs_bg, 15 ) ); ?> 40%, <?php echo esc_attr( $fs_bg ); ?> 70%, <?php echo esc_attr( bookingroom_darken_hex( $fs_bg, 25 ) ); ?> 100%);
    }

    /* Layout: Logo giữa */
    <?php if ( $layout === 'logo-center-nav-split' || $layout === 'logo-center-nav-below' ) : ?>
    .header-logo {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        flex: none;
    }
    #site-navigation {
        position: static;
        transform: none;
        left: auto;
    }
    <?php endif; ?>

    <?php if ( $layout === 'logo-right-nav-left' ) : ?>
    .header-logo { order: 3; }
    #site-navigation { order: 1; position: static; transform: none; left: auto; }
    .header-actions { order: 2; flex: 1; justify-content: center; }
    <?php endif; ?>

    <?php if ( $layout === 'logo-center-nav-below' ) : ?>
    #masthead .header-wrap { flex-wrap: wrap; height: auto; min-height: <?php echo esc_attr( $height ); ?>px; padding-bottom: 0; }
    .header-logo { width: 100%; justify-content: center; padding-top: 1rem; position: static; transform: none; left: auto; }
    #site-navigation { width: 100%; justify-content: center; position: static; transform: none; left: auto; border-top: 1px solid rgba(0,0,0,0.06); padding: 0.6rem 0; }
    .header-actions { order: 2; }
    <?php endif; ?>

    <?php if ( $layout === 'minimal-logo-only' ) : ?>
    #site-navigation { display: none !important; }
    .lang-switcher { display: none !important; }
    <?php endif; ?>
    </style>
    <?php
    echo ob_get_clean();
}

/* =========================================================
   LIVE PREVIEW: postMessage transport (Customizer preview)
   ========================================================= */
add_action( 'customize_preview_init', 'bookingroom_header_customizer_preview' );

function bookingroom_header_customizer_preview() {
    wp_add_inline_script( 'customize-preview', "
    (function($) {
        // Chiều cao header
        wp.customize('header_height', function(v) {
            v.bind(function(val) {
                document.querySelector('#bookingroom-header-customizer-css').disabled = true;
                document.getElementById('masthead').querySelector('.header-wrap').style.height = val + 'px';
            });
        });

        // Màu nav
        wp.customize('header_nav_color', function(v) {
            v.bind(function(val) {
                var links = document.querySelectorAll('#primary-menu-list > li > a');
                links.forEach(function(l) { l.style.color = val; });
            });
        });

        // Màu nav hover (thông qua CSS variable)
        wp.customize('header_nav_hover_color', function(v) {
            v.bind(function(val) {
                document.documentElement.style.setProperty('--nav-hover', val);
            });
        });

        // Màu nền nút
        wp.customize('header_btn_bg', function(v) {
            v.bind(function(val) {
                var btn = document.querySelector('.btn-booking');
                if (btn) btn.style.background = val;
            });
        });

        // Text nút
        wp.customize('header_btn_text', function(v) {
            v.bind(function(val) {
                var btn = document.querySelector('.btn-booking');
                if (btn) btn.textContent = val;
            });
        });
    })(jQuery);
    " );
}

/* =========================================================
   HELPER: Chuyển hex → [r,g,b]
   ========================================================= */
function bookingroom_hex2rgb( $hex ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    if ( strlen( $hex ) !== 6 ) return null;
    return array(
        hexdec( substr( $hex, 0, 2 ) ),
        hexdec( substr( $hex, 2, 2 ) ),
        hexdec( substr( $hex, 4, 2 ) ),
    );
}

/* =========================================================
   HELPER: Làm tối màu hex
   ========================================================= */
function bookingroom_darken_hex( $hex, $amount = 20 ) {
    $rgb = bookingroom_hex2rgb( $hex );
    if ( ! $rgb ) return $hex;
    $r = max( 0, $rgb[0] - $amount );
    $g = max( 0, $rgb[1] - $amount );
    $b = max( 0, $rgb[2] - $amount );
    return sprintf( '#%02x%02x%02x', $r, $g, $b );
}
