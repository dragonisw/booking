<?php
/**
 * SMS Manager
 * Quản lý gửi tin nhắn SMS thông qua Twilio API.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Thêm cài đặt Twilio API vào WordPress Customizer
 */
function bookingroom_sms_customizer_settings( $wp_customize ) {
    $wp_customize->add_section( 'bookingroom_sms_section', array(
        'title'       => __( 'Cài đặt SMS (Twilio)', 'bookingroom' ),
        'priority'    => 130,
        'description' => __( 'Cấu hình thông tin kết nối API Twilio để gửi tin nhắn SMS.', 'bookingroom' ),
    ) );

    // Account SID
    $wp_customize->add_setting( 'twilio_account_sid', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'twilio_account_sid', array(
        'label'   => __( 'Twilio Account SID', 'bookingroom' ),
        'section' => 'bookingroom_sms_section',
        'type'    => 'text',
    ) );

    // Auth Token
    $wp_customize->add_setting( 'twilio_auth_token', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'twilio_auth_token', array(
        'label'   => __( 'Twilio Auth Token', 'bookingroom' ),
        'section' => 'bookingroom_sms_section',
        'type'    => 'password',
    ) );

    // From Number
    $wp_customize->add_setting( 'twilio_from_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'description'       => __( 'Số điện thoại gửi đi của Twilio (VD: +1234567890)', 'bookingroom' ),
    ) );
    $wp_customize->add_control( 'twilio_from_number', array(
        'label'   => __( 'Twilio From Number', 'bookingroom' ),
        'section' => 'bookingroom_sms_section',
        'type'    => 'text',
    ) );
}
add_action( 'customize_register', 'bookingroom_sms_customizer_settings' );

/**
 * Hàm gửi tin nhắn SMS qua Twilio
 * 
 * @param string $to_phone Số điện thoại người nhận
 * @param string $message Nội dung tin nhắn
 * @return bool|WP_Error Trả về true nếu thành công, hoặc WP_Error nếu thất bại
 */
function bookingroom_send_sms( $to_phone, $message ) {
    $account_sid = get_theme_mod( 'twilio_account_sid', '' );
    $auth_token  = get_theme_mod( 'twilio_auth_token', '' );
    $from_number = get_theme_mod( 'twilio_from_number', '' );

    if ( empty( $account_sid ) || empty( $auth_token ) || empty( $from_number ) ) {
        return new WP_Error( 'missing_credentials', 'Thông tin API Twilio chưa được cấu hình.' );
    }

    // Xử lý số điện thoại: Twilio yêu cầu định dạng E.164 (VD: +84901234567)
    // Nếu số bắt đầu bằng '0', thay bằng '+84'
    $to_phone = preg_replace('/^0/', '+84', trim($to_phone));
    
    // Đảm bảo số điện thoại bắt đầu bằng dấu '+'
    if ( strpos($to_phone, '+') !== 0 ) {
        $to_phone = '+' . $to_phone;
    }

    $twilio_api_url = "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json";

    $args = array(
        'method'      => 'POST',
        'timeout'     => 15,
        'headers'     => array(
            'Authorization' => 'Basic ' . base64_encode( "{$account_sid}:{$auth_token}" ),
        ),
        'body'        => array(
            'To'   => $to_phone,
            'From' => $from_number,
            'Body' => $message,
        ),
    );

    $response = wp_remote_post( $twilio_api_url, $args );

    if ( is_wp_error( $response ) ) {
        return $response; // Lỗi từ WP HTTP API
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );
    $data = json_decode( $response_body, true );

    if ( $response_code >= 200 && $response_code < 300 ) {
        return true; // Gửi thành công
    } else {
        $error_message = isset($data['message']) ? $data['message'] : 'Không rõ lỗi';
        return new WP_Error( 'twilio_api_error', "Lỗi Twilio: {$error_message}" );
    }
}
