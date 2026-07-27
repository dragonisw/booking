<?php
/**
 * Lớp REST Controller để đăng ký các Custom API Endpoints cho Frontend gọi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BookingRoom_REST_Controller {

    private $api_client;
    private $namespace = 'bookingroom/v1';

    public function __construct() {
        // Tải class API Client nếu chưa có
        if ( ! class_exists( 'BookingRoom_API_Client' ) ) {
            require_once get_template_directory() . '/inc/api-integration/class-booking-api-client.php';
        }
        $this->api_client = new BookingRoom_API_Client();
    }

    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        // Endpoint: GET /wp-json/bookingroom/v1/search
        register_rest_route( $this->namespace, '/search', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'search_rooms' ),
            'permission_callback' => '__return_true', // Public endpoint
            'args'                => array(
                'check_in'  => array(
                    'required'          => true,
                    'validate_callback' => function($param, $request, $key) {
                        return (bool) strtotime($param);
                    }
                ),
                'check_out' => array(
                    'required'          => true,
                    'validate_callback' => function($param, $request, $key) {
                        return (bool) strtotime($param);
                    }
                ),
                'adults'    => array(
                    'default'           => 2,
                    'sanitize_callback' => 'absint'
                ),
                'children'  => array(
                    'default'           => 0,
                    'sanitize_callback' => 'absint'
                )
            )
        ) );

        // Endpoint: POST /wp-json/bookingroom/v1/book
        register_rest_route( $this->namespace, '/book', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'create_booking' ),
            'permission_callback' => '__return_true', // Tùy thuộc vào yêu cầu, có thể cần nonce hoặc auth
        ) );
    }

    /**
     * Callback xử lý tìm kiếm phòng trống
     */
    public function search_rooms( WP_REST_Request $request ) {
        $check_in  = $request->get_param( 'check_in' );
        $check_out = $request->get_param( 'check_out' );
        $adults    = $request->get_param( 'adults' );
        $children  = $request->get_param( 'children' );

        // 1. Gọi hệ thống Booking Engine API thông qua API Client
        $params = array(
            'check_in'  => $check_in,
            'check_out' => $check_out,
            'adults'    => $adults,
            'children'  => $children,
        );
        
        // Tùy thuộc vào cấu trúc của 3rd-party API, endpoint có thể là 'availability', 'search', v.v.
        $api_response = $this->api_client->get( 'availability', $params );

        if ( is_wp_error( $api_response ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => $api_response->get_error_message(),
                'code'    => $api_response->get_error_code()
            ), 400 );
        }

        // 2. Format lại dữ liệu trả về cho Frontend (nếu cần thiết)
        // Ví dụ: map dữ liệu từ 3rd-party sang định dạng chung của theme

        return new WP_REST_Response( array(
            'success' => true,
            'data'    => $api_response
        ), 200 );
    }

    /**
     * Callback xử lý tạo Booking mới
     */
    public function create_booking( WP_REST_Request $request ) {
        $data = $request->get_json_params();

        if ( empty( $data['room_id'] ) || empty( $data['customer_info'] ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Thiếu thông tin đặt phòng.'
            ), 400 );
        }

        // Gọi API của bên thứ 3 để tạo booking
        // Endpoint có thể là 'bookings', 'reservations', v.v.
        $api_response = $this->api_client->post( 'bookings', $data );

        if ( is_wp_error( $api_response ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => $api_response->get_error_message(),
                'code'    => $api_response->get_error_code()
            ), 400 );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Đặt phòng thành công.',
            'data'    => $api_response
        ), 200 );
    }
}
