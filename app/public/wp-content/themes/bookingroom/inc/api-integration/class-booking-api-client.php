<?php
/**
 * Lớp API Client để giao tiếp với hệ thống Booking Engine (OTA/Channel Manager) của bên thứ ba.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BookingRoom_API_Client {

    private $api_url;
    private $api_key;

    public function __construct() {
        // Lấy thông tin cấu hình từ Customizer (sẽ được thêm vào sau)
        $this->api_url = get_theme_mod( 'booking_api_endpoint', '' );
        $this->api_key = get_theme_mod( 'booking_api_key', '' );
    }

    /**
     * Lấy các Headers chung cho mỗi request.
     * Có thể tùy biến tùy theo yêu cầu của từng hệ thống (Bearer token, X-API-Key, etc.)
     */
    private function get_headers() {
        return array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            // 'X-API-Key'     => $this->api_key, // Tùy thuộc vào nhà cung cấp API
        );
    }

    /**
     * Hàm gọi GET request
     */
    public function get( $endpoint, $params = array() ) {
        if ( empty( $this->api_url ) ) {
            return new WP_Error( 'api_not_configured', 'API Endpoint chưa được cấu hình.' );
        }

        $url = rtrim( $this->api_url, '/' ) . '/' . ltrim( $endpoint, '/' );
        
        if ( ! empty( $params ) ) {
            $url = add_query_arg( $params, $url );
        }

        $args = array(
            'headers' => $this->get_headers(),
            'timeout' => 15,
        );

        $response = wp_remote_get( $url, $args );

        return $this->handle_response( $response );
    }

    /**
     * Hàm gọi POST request
     */
    public function post( $endpoint, $body = array() ) {
        if ( empty( $this->api_url ) ) {
            return new WP_Error( 'api_not_configured', 'API Endpoint chưa được cấu hình.' );
        }

        $url = rtrim( $this->api_url, '/' ) . '/' . ltrim( $endpoint, '/' );

        $args = array(
            'headers' => $this->get_headers(),
            'body'    => wp_json_encode( $body ),
            'timeout' => 30,
        );

        $response = wp_remote_post( $url, $args );

        return $this->handle_response( $response );
    }

    /**
     * Hàm xử lý response chung từ HTTP request
     */
    private function handle_response( $response ) {
        if ( is_wp_error( $response ) ) {
            // Có lỗi kết nối (timeout, dns, v.v.)
            error_log( 'Booking API Error: ' . $response->get_error_message() );
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $data        = json_decode( $body, true );

        if ( $status_code >= 200 && $status_code < 300 ) {
            return $data;
        } else {
            $error_message = isset( $data['message'] ) ? $data['message'] : 'Lỗi từ API hệ thống đặt phòng.';
            error_log( 'Booking API Failed (' . $status_code . '): ' . $body );
            return new WP_Error( 'api_error_' . $status_code, $error_message, $data );
        }
    }
}
