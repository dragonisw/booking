<?php
/**
 * Google Reviews Integration
 * Fetches and displays reviews from Google Places API
 */

// 1. Add Place ID to Customizer
function bookingroom_google_reviews_customizer($wp_customize) {
    $wp_customize->add_setting('google_place_id', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('google_place_id', array(
        'label' => __('Google Place ID', 'bookingroom'),
        'description' => __('Mã định danh địa điểm doanh nghiệp của bạn trên Google (dùng để lấy đánh giá).', 'bookingroom'),
        'section' => 'booking_engine_section',
        'type' => 'text',
    ));
}
add_action('customize_register', 'bookingroom_google_reviews_customizer');

// 2. Shortcode to display reviews
function bookingroom_google_reviews_shortcode($atts) {
    $atts = shortcode_atts(array(
        'place_id' => get_theme_mod('google_place_id', ''),
        'api_key' => get_theme_mod('google_maps_api_key', '')
    ), $atts, 'google_reviews');

    $place_id = $atts['place_id'];
    $api_key = $atts['api_key'];

    if (empty($place_id) || empty($api_key)) {
        if (current_user_can('manage_options')) {
            return '<p class="text-red-500 bg-red-50 p-4 rounded border border-red-200"><strong>Admin Notice:</strong> Vui lòng cấu hình Google Maps API Key và Place ID trong phần Giao diện -> Tùy biến -> Booking Engine Section.</p>';
        }
        return '';
    }

    $transient_key = 'gr_reviews_' . md5($place_id);
    $data = get_transient($transient_key);

    if (false === $data) {
        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$place_id}&fields=name,rating,reviews,user_ratings_total&key={$api_key}&language=vi";
        
        $response = wp_remote_get($url, array('timeout' => 15));
        
        if (is_wp_error($response)) {
            if (current_user_can('manage_options')) {
                return '<p class="text-red-500">Lỗi kết nối API: ' . esc_html($response->get_error_message()) . '</p>';
            }
            return '';
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['status']) && $data['status'] === 'OK') {
            set_transient($transient_key, $data, DAY_IN_SECONDS);
        } else {
            if (current_user_can('manage_options')) {
                return '<p class="text-red-500 bg-red-50 p-4 rounded border border-red-200">Lỗi từ Google API: ' . esc_html($data['status'] ?? 'Unknown Error') . '</p>';
            }
            return '';
        }
    }

    if (!isset($data['result']['reviews']) || empty($data['result']['reviews'])) {
        return '<p class="text-gray-500">Chưa có bài đánh giá nào.</p>';
    }

    $result = $data['result'];
    $reviews = $result['reviews'];
    $total_rating = isset($result['rating']) ? $result['rating'] : 0;
    $total_reviews = isset($result['user_ratings_total']) ? $result['user_ratings_total'] : 0;
    $place_name = isset($result['name']) ? $result['name'] : '';

    ob_start();
    ?>
    <div class="google-reviews-container bg-white p-8 rounded-2xl shadow-xl border border-gray-100 my-8">
        <div class="flex flex-col md:flex-row items-center justify-between border-b pb-6 mb-6">
            <div class="text-center md:text-left mb-4 md:mb-0">
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight"><?php echo esc_html($place_name); ?></h3>
                <div class="flex items-center justify-center md:justify-start mt-2">
                    <span class="text-2xl font-black text-yellow-500 mr-3"><?php echo esc_html($total_rating); ?></span>
                    <div class="flex text-yellow-400">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= round($total_rating)) {
                                echo '<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                            } else {
                                echo '<svg class="w-6 h-6 fill-current text-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                            }
                        }
                        ?>
                    </div>
                    <span class="text-sm font-medium text-gray-500 ml-3">(<?php echo esc_html($total_reviews); ?> đánh giá trên Google)</span>
                </div>
            </div>
            <div>
                <a href="https://search.google.com/local/writereview?placeid=<?php echo esc_attr($place_id); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M21.35,11.1h-9.17v2.73h5.39c-0.27,1.49-1.22,2.71-2.58,3.53v2.85h4.12C21.57,18.06,23,14.88,23,11.1 C23,10.43,22.92,9.75,22.8,9.1H21.35z"/><path d="M12.18,23c3.08,0,5.67-1.01,7.56-2.76l-4.12-2.85c-0.97,0.67-2.22,1.06-3.44,1.06c-2.75,0-5.07-1.83-5.91-4.32H2v2.96 C3.91,20.73,7.69,23,12.18,23z"/><path d="M6.27,14.13c-0.22-0.65-0.34-1.35-0.34-2.07s0.12-1.42,0.34-2.07V7.04H2C1.29,8.53,0.89,10.22,0.89,12 c0,1.78,0.4,3.47,1.11,4.96L6.27,14.13z"/><path d="M12.18,4.56c1.67,0,3.17,0.57,4.36,1.66l3.22-3.17C17.84,1.18,15.25,0.18,12.18,0.18C7.69,0.18,3.91,2.45,2,6.23l4.27,2.96 C7.11,6.7,9.43,4.87,12.18,4.56z"/></svg>
                    Viết đánh giá
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($reviews as $review) : ?>
                <div class="review-item bg-gray-50 p-6 rounded-xl border border-gray-100 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
                    <div>
                        <div class="flex items-center mb-4">
                            <img src="<?php echo esc_url($review['profile_photo_url']); ?>" alt="<?php echo esc_attr($review['author_name']); ?>" class="w-12 h-12 rounded-full mr-4 shadow-sm">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm"><?php echo esc_html($review['author_name']); ?></h4>
                                <div class="flex text-yellow-400 mt-1">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            echo '<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed mb-4 line-clamp-4">"<?php echo esc_html($review['text']); ?>"</p>
                    </div>
                    <span class="text-xs font-medium text-gray-400"><?php echo esc_html($review['relative_time_description']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('google_reviews', 'bookingroom_google_reviews_shortcode');
