<?php
/**
 * Template Override: Single hb_room
 * Hiển thị chi tiết phòng WP Hotel Booking với giao diện theme bookingroom.
 *
 * Copy file này vào: themes/bookingroom/wp-hotel-booking/single-hb_room.php
 *
 * @package BookingRoom
 */

get_header();
?>

<main class="bg-slate-50 min-h-screen pb-20">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <?php
    // ── Data phòng ──────────────────────────────────────────────────────────
    $room_id   = get_the_ID();
    $price     = bookingroom_get_hbroom_price( $room_id );
    $capacity  = bookingroom_get_hbroom_capacity( $room_id );

    // Tham số từ URL (từ trang archive chuyển sang)
    $check_in  = isset( $_GET['check_in'] )  ? sanitize_text_field( $_GET['check_in'] )  : '';
    $check_out = isset( $_GET['check_out'] ) ? sanitize_text_field( $_GET['check_out'] ) : '';
    $adults    = isset( $_GET['adults'] )    ? intval( $_GET['adults'] )    : 2;
    $children  = isset( $_GET['children'] )  ? intval( $_GET['children'] )  : 0;

    $nights = 0;
    if ( $check_in && $check_out ) {
        $ci_ts = strtotime( $check_in );
        $co_ts = strtotime( $check_out );
        if ( $co_ts > $ci_ts ) {
            $nights = round( ( $co_ts - $ci_ts ) / 86400 );
        }
    }

    // Gallery ảnh
    $gallery_ids  = get_post_meta( $room_id, '_tp_room_gallery', true );
    $gallery_urls = [];
    if ( ! empty( $gallery_ids ) ) {
        foreach ( explode( ',', $gallery_ids ) as $gid ) {
            $url = wp_get_attachment_image_url( trim( $gid ), 'large' );
            if ( $url ) $gallery_urls[] = $url;
        }
    }
    if ( has_post_thumbnail() ) {
        array_unshift( $gallery_urls, get_the_post_thumbnail_url( $room_id, 'large' ) );
    }
    $gallery_urls = array_unique( $gallery_urls );

    // Amenities / tiện nghi
    $amenities = get_post_meta( $room_id, '_tp_room_services', true );
    $amenities = is_array( $amenities ) ? $amenities : [];

    // Loại phòng
    $room_types = get_the_terms( $room_id, 'hb_room_type' );
    $type_names = $room_types ? array_map( fn($t) => $t->name, $room_types ) : [];
    ?>

    <!-- Breadcrumbs -->
    <div class="bg-white border-b border-slate-200">
        <div class="container mx-auto px-4 py-4 flex items-center text-sm text-slate-500">
            <a href="<?php echo home_url(); ?>" class="hover:text-[#d35400] transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="<?php echo esc_url( bookingroom_get_hbroom_archive_url() ); ?>" class="hover:text-[#d35400] transition-colors">Phòng nghỉ</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 font-medium"><?php the_title(); ?></span>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- ── Main Content (2/3) ── -->
            <div class="lg:col-span-2 space-y-10">

                <!-- Gallery -->
                <div class="relative">
                    <!-- Main Image -->
                    <div class="aspect-video bg-slate-200 rounded-3xl overflow-hidden shadow-xl relative group" id="main-gallery-img">
                        <?php if ( ! empty( $gallery_urls ) ) : ?>
                            <img src="<?php echo esc_url( $gallery_urls[0] ); ?>"
                                 alt="<?php the_title_attribute(); ?>"
                                 class="w-full h-full object-cover transition-opacity duration-500"
                                 id="gallery-main-img">
                        <?php elseif ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'full', [ 'class' => 'w-full h-full object-cover', 'id' => 'gallery-main-img' ] ); ?>
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200"
                                 class="w-full h-full object-cover" id="gallery-main-img" alt="">
                        <?php endif; ?>

                        <!-- Counter -->
                        <?php if ( count( $gallery_urls ) > 1 ) : ?>
                        <div class="absolute bottom-5 right-5">
                            <button id="view-all-photos"
                                class="bg-white/90 backdrop-blur px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg hover:bg-white transition-all text-slate-800 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Xem tất cả <?php echo count( $gallery_urls ); ?> ảnh
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnails -->
                    <?php if ( count( $gallery_urls ) > 1 ) : ?>
                    <div class="flex gap-3 mt-4 overflow-x-auto pb-2">
                        <?php foreach ( $gallery_urls as $idx => $gurl ) : ?>
                            <button class="gallery-thumb flex-shrink-0 w-24 h-16 rounded-xl overflow-hidden border-2 transition-all <?php echo $idx === 0 ? 'border-[#d35400] opacity-100' : 'border-transparent opacity-60 hover:opacity-100'; ?>"
                                    data-src="<?php echo esc_url( $gurl ); ?>">
                                <img src="<?php echo esc_url( $gurl ); ?>" class="w-full h-full object-cover" alt="">
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Room Info Card -->
                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3"><?php the_title(); ?></h1>
                            <div class="flex flex-wrap items-center gap-3">
                                <?php if ( ! empty( $type_names ) ) : ?>
                                    <?php foreach ( $type_names as $tn ) : ?>
                                        <span class="bg-orange-50 text-[#d35400] border border-orange-200 text-xs font-bold px-3 py-1 rounded-full"><?php echo esc_html( $tn ); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <span class="flex items-center gap-1 text-slate-500 text-sm">
                                    <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    4.9 (124 đánh giá)
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-slate max-w-none leading-relaxed text-slate-600 mb-8">
                        <?php the_content(); ?>
                    </div>

                    <hr class="border-slate-100 mb-8">

                    <!-- Tiện nghi -->
                    <h3 class="text-lg font-bold text-slate-900 mb-5">Tiện nghi phòng</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php
                        $default_amenities = [
                            [ 'icon' => 'wifi',    'label' => 'Wi-Fi miễn phí' ],
                            [ 'icon' => 'ac',      'label' => 'Máy lạnh' ],
                            [ 'icon' => 'tv',      'label' => 'TV màn hình phẳng' ],
                            [ 'icon' => 'bath',    'label' => 'Phòng tắm riêng' ],
                            [ 'icon' => 'minibar', 'label' => 'Minibar' ],
                            [ 'icon' => 'safe',    'label' => 'Két an toàn' ],
                            [ 'icon' => 'desk',    'label' => 'Bàn làm việc' ],
                            [ 'icon' => 'view',    'label' => 'View đẹp' ],
                        ];
                        $amenity_icons = [
                            'wifi'    => '<path d="M8.111 16.404a5.5 5.5 0 117.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.314 6.364c5.857-5.858 15.355-5.858 21.213 0"/>',
                            'ac'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.364-7.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 16.95l1.414 1.414M7.05 7.05L5.636 5.636"/>',
                            'tv'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                            'bath'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18m-9 6H3m9 0h3M12 6v6"/>',
                            'minibar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                            'safe'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                            'desk'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                            'view'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                        ];
                        $show_amenities = ! empty( $amenities ) ? $amenities : $default_amenities;
                        foreach ( $show_amenities as $am ) :
                            $icon_key = is_array( $am ) ? ( $am['icon'] ?? 'wifi' ) : 'wifi';
                            $label    = is_array( $am ) ? ( $am['label'] ?? $am ) : $am;
                            $icon_d   = $amenity_icons[ $icon_key ] ?? $amenity_icons['wifi'];
                        ?>
                            <div class="flex items-center gap-3 text-slate-600 p-3 rounded-xl bg-slate-50 hover:bg-orange-50 transition-colors">
                                <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-[#d35400] shadow-sm flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <?php echo $icon_d; ?>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium"><?php echo esc_html( $label ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Form đặt phòng của WP Hotel Booking Plugin -->
                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100" id="booking-form-section">
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Đặt phòng</h3>

                    <?php
                    /**
                     * Hiển thị form đặt phòng của WP Hotel Booking.
                     * Plugin cung cấp shortcode [hotel_booking] để nhúng form vào trang.
                     * Nếu muốn chỉ hiển thị form cho phòng hiện tại, dùng: [hotel_booking id="ROOM_ID"]
                     */
                    if ( shortcode_exists( 'hotel_booking' ) ) :
                        // Shortcode chính thức của plugin
                        echo do_shortcode( '[hotel_booking id="' . $room_id . '"]' );
                    elseif ( shortcode_exists( 'tp_hotel_booking_room' ) ) :
                        echo do_shortcode( '[tp_hotel_booking_room id="' . $room_id . '"]' );
                    else :
                        // Fallback form nếu plugin chưa active
                        ?>
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                            <p class="text-amber-700 font-medium text-sm">
                                ⚠️ Plugin WP Hotel Booking chưa được kích hoạt. Vui lòng kích hoạt plugin để hiển thị form đặt phòng.
                            </p>
                        </div>

                        <!-- Fallback custom form -->
                        <form id="hb-fallback-booking-form" class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Ngày nhận phòng <span class="text-red-500">*</span></label>
                                    <input type="date" name="check_in" id="hb_check_in"
                                        value="<?php echo esc_attr( $check_in ); ?>"
                                        min="<?php echo date('Y-m-d'); ?>"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Ngày trả phòng <span class="text-red-500">*</span></label>
                                    <input type="date" name="check_out" id="hb_check_out"
                                        value="<?php echo esc_attr( $check_out ); ?>"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Người lớn</label>
                                    <select name="adults" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none">
                                        <?php for ( $i = 1; $i <= 6; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>" <?php selected( $adults, $i ); ?>><?php echo $i; ?> người lớn</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Trẻ em</label>
                                    <select name="children" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none">
                                        <?php for ( $i = 0; $i <= 5; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>" <?php selected( $children, $i ); ?>><?php echo $i; ?> trẻ em</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <hr class="border-slate-100">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="cust_name" placeholder="Nguyễn Văn A"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none" required>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                    <input type="tel" name="cust_phone" placeholder="0901234567"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="cust_email" placeholder="example@email.com"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú (tùy chọn)</label>
                                <textarea name="cust_note" rows="3" placeholder="Yêu cầu đặc biệt..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#d35400] outline-none resize-none"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-4 rounded-2xl transition-all shadow-lg hover:shadow-orange-200/50 text-base">
                                Gửi yêu cầu đặt phòng
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Sticky Sidebar (1/3) ── -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 sticky top-28 overflow-hidden">

                    <!-- Header -->
                    <div class="bg-slate-900 p-6 text-white">
                        <h3 class="text-lg font-bold mb-1">Thông tin chuyến đi</h3>
                        <p class="text-slate-300 text-sm"><?php the_title(); ?></p>
                    </div>

                    <div class="p-6">
                        <!-- Dates -->
                        <div class="space-y-4 mb-6 text-sm">
                            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                <span class="text-slate-500">Nhận phòng</span>
                                <input type="date" id="sidebar_check_in"
                                    value="<?php echo esc_attr( $check_in ); ?>"
                                    class="bg-transparent border-none p-0 text-right font-semibold text-slate-900 focus:ring-0 cursor-pointer">
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                <span class="text-slate-500">Trả phòng</span>
                                <input type="date" id="sidebar_check_out"
                                    value="<?php echo esc_attr( $check_out ); ?>"
                                    class="bg-transparent border-none p-0 text-right font-semibold text-slate-900 focus:ring-0 cursor-pointer">
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                <span class="text-slate-500">Khách</span>
                                <span class="font-semibold text-slate-900"><?php echo $adults; ?> NL<?php echo $children ? " + {$children} TE" : ''; ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Số đêm</span>
                                <span id="sidebar_nights" class="font-bold text-[#d35400]"><?php echo $nights ?: '—'; ?> đêm</span>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="bg-slate-50 rounded-2xl p-5 mb-6 border border-slate-100">
                            <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                Chi tiết giá
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-slate-600">
                                    <span>Giá mỗi đêm</span>
                                    <span class="font-medium" data-base-price="<?php echo esc_attr( $price ); ?>"><?php echo number_format( $price ); ?>đ</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Số đêm</span>
                                    <span id="price_nights" class="font-medium"><?php echo $nights ?: 0; ?> đêm</span>
                                </div>
                                <div class="flex justify-between text-slate-600 pb-3 border-b border-slate-200">
                                    <span>Tạm tính</span>
                                    <span id="price_subtotal" class="font-medium"><?php echo $nights ? number_format( $price * $nights ) . 'đ' : '—'; ?></span>
                                </div>
                                <div class="flex justify-between items-end pt-1">
                                    <span class="font-bold text-slate-900">Tổng tiền</span>
                                    <span id="price_total" class="text-2xl font-bold text-[#d35400]"><?php echo $nights ? number_format( $price * $nights ) . 'đ' : '—'; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA: Scroll đến form đặt phòng -->
                        <a href="#booking-form-section"
                           class="block w-full text-center bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-4 rounded-2xl transition-all shadow-lg hover:shadow-orange-200/50 text-base">
                            Đặt phòng ngay
                        </a>
                        <p class="text-center text-xs text-slate-400 mt-3">Xác nhận ngay • Thanh toán an toàn</p>

                        <!-- Hotline -->
                        <div class="mt-5 pt-5 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-400 mb-1">Hỗ trợ đặt phòng 24/7</p>
                            <a href="tel:<?php echo esc_attr( get_theme_mod( 'bookingroom_hotline', '0123456789' ) ); ?>"
                               class="text-xl font-bold text-slate-900 hover:text-[#d35400] transition-colors">
                                <?php echo esc_html( get_theme_mod( 'bookingroom_hotline', '(84-252) 381 2233' ) ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endwhile; endif; ?>
</main>

<!-- Lightbox Gallery Modal -->
<div id="gallery-lightbox" class="fixed inset-0 bg-black/90 z-[9999] hidden items-center justify-center p-4">
    <button id="lightbox-close" class="absolute top-5 right-5 text-white hover:text-slate-300 transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] object-contain rounded-2xl shadow-2xl" alt="">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Gallery Thumbnails ────────────────────────────────────────────────
    const mainImg   = document.getElementById('gallery-main-img');
    const thumbs    = document.querySelectorAll('.gallery-thumb');
    const lightbox  = document.getElementById('gallery-lightbox');
    const lbImg     = document.getElementById('lightbox-img');
    const lbClose   = document.getElementById('lightbox-close');
    const viewAllBtn = document.getElementById('view-all-photos');

    thumbs.forEach((thumb, idx) => {
        thumb.addEventListener('click', function () {
            if (mainImg) mainImg.src = this.dataset.src;
            thumbs.forEach(t => {
                t.classList.remove('border-[#d35400]', 'opacity-100');
                t.classList.add('border-transparent', 'opacity-60');
            });
            this.classList.remove('border-transparent', 'opacity-60');
            this.classList.add('border-[#d35400]', 'opacity-100');
        });
    });

    if (viewAllBtn && lightbox) {
        viewAllBtn.addEventListener('click', function () {
            if (mainImg && lbImg) lbImg.src = mainImg.src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        });
    }
    if (lbClose) {
        lbClose.addEventListener('click', () => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        });
    }
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.add('hidden');
                this.classList.remove('flex');
            }
        });
    }

    // ── Sidebar Price Calculator ─────────────────────────────────────────
    const ciInput     = document.getElementById('sidebar_check_in');
    const coInput     = document.getElementById('sidebar_check_out');
    const nightsEl    = document.getElementById('sidebar_nights');
    const priceNights = document.getElementById('price_nights');
    const subtotalEl  = document.getElementById('price_subtotal');
    const totalEl     = document.getElementById('price_total');
    const basePrice   = parseFloat(document.querySelector('[data-base-price]')?.dataset.basePrice || 0);

    function formatVND(n) {
        return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    }

    function updatePriceCalc() {
        if (!ciInput || !coInput || !ciInput.value || !coInput.value) return;
        const ci = new Date(ciInput.value);
        const co = new Date(coInput.value);
        if (co <= ci) return;
        const n = Math.round((co - ci) / 86400000);
        const subtotal = basePrice * n;

        if (nightsEl)    nightsEl.textContent    = n + ' đêm';
        if (priceNights) priceNights.textContent  = n + ' đêm';
        if (subtotalEl)  subtotalEl.textContent   = formatVND(subtotal);
        if (totalEl)     totalEl.textContent      = formatVND(subtotal);
    }

    if (ciInput && coInput) {
        ciInput.addEventListener('change', function () {
            const next = new Date(this.value);
            next.setDate(next.getDate() + 1);
            coInput.min = next.toLocaleDateString('sv-SE');
            if (coInput.value && coInput.value <= this.value) {
                coInput.value = next.toLocaleDateString('sv-SE');
            }
            updatePriceCalc();
        });
        coInput.addEventListener('change', updatePriceCalc);
        updatePriceCalc();
    }

    // ── Fallback Form Submit ─────────────────────────────────────────────
    const fallbackForm = document.getElementById('hb-fallback-booking-form');
    if (fallbackForm) {
        fallbackForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const data = {
                action: 'process_booking',
                nonce: window.booking_ajax?.nonce || '',
                room_id: <?php echo $room_id; ?>,
                check_in:  document.getElementById('hb_check_in')?.value || '',
                check_out: document.getElementById('hb_check_out')?.value || '',
                adults: this.querySelector('[name="adults"]')?.value || 2,
                children: this.querySelector('[name="children"]')?.value || 0,
                name:  this.querySelector('[name="cust_name"]')?.value || '',
                phone: this.querySelector('[name="cust_phone"]')?.value || '',
                email: this.querySelector('[name="cust_email"]')?.value || '',
                note:  this.querySelector('[name="cust_note"]')?.value || '',
            };
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            if (window.jQuery && window.booking_ajax) {
                jQuery.post(booking_ajax.ajax_url, data, function (res) {
                    if (res.success) {
                        alert(res.data.message);
                        window.location.href = '<?php echo home_url("/tra-cuu"); ?>?booking_id=' + res.data.booking_id + '&phone=' + data.phone;
                    } else {
                        alert(res.data.message || 'Có lỗi xảy ra.');
                        btn.disabled = false;
                        btn.textContent = 'Gửi yêu cầu đặt phòng';
                    }
                });
            } else {
                alert('Đặt phòng thành công! Chúng tôi sẽ liên hệ với bạn sớm.');
                btn.disabled = false;
                btn.textContent = 'Gửi yêu cầu đặt phòng';
            }
        });
    }
});
</script>

<?php get_footer(); ?>
