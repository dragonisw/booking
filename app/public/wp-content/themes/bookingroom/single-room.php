<?php get_header(); ?>

<main class="bg-slate-50 min-h-screen pb-20">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <!-- Breadcrumbs -->
        <div class="bg-white border-b border-slate-200">
            <div class="container mx-auto px-4 py-4 flex items-center text-sm text-slate-500">
                <a href="<?php echo home_url(); ?>" class="hover:text-blue-600"><?php echo t('Trang chủ', 'Home'); ?></a>
                <span class="mx-2">/</span>
                <a href="<?php echo get_post_type_archive_link('room'); ?>" class="hover:text-blue-600"><?php echo t('Phòng', 'Rooms'); ?></a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 font-medium"><?php the_title(); ?></span>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Main Content (2/3) -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Gallery Placeholder -->
                    <div class="aspect-video bg-slate-200 rounded-3xl overflow-hidden shadow-lg relative group">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover')); ?>
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200" alt="Room" class="w-full h-full object-cover">
                        <?php endif; ?>
                        <div class="absolute bottom-6 right-6">
                            <button class="bg-white/90 backdrop-blur px-6 py-3 rounded-xl font-bold flex items-center shadow-lg hover:bg-white transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <?php echo t('Xem tất cả hình ảnh', 'View all images'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-start justify-between mb-8">
                            <div>
                                <h1 class="text-4xl font-bold text-slate-900 mb-2"><?php the_title(); ?></h1>
                                <div class="flex items-center text-slate-500 gap-4">
                                    <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg> Quận 1, TP. HCM</span>
                                    <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg> 4.9 <?php echo t('(124 đánh giá)', '(124 reviews)'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-slate max-w-none mb-10 leading-relaxed text-slate-600">
                            <?php the_content(); ?>
                        </div>

                        <hr class="border-slate-100 mb-10">

                        <!-- Amenities -->
                        <?php 
                        $amenities = get_post_meta(get_the_ID(), '_room_amenities', true) ?: []; 
                        $policies = get_post_meta(get_the_ID(), '_room_policies', true);
                        ?>
                        
                        <?php if ( ! empty( $amenities ) ) : ?>
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            <?php echo t('Tiện nghi phòng', 'Room Amenities'); ?>
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                            <?php 
                            $amenity_icons = [
                                'wifi' => '<path d="M8.111 16.404a5.5 5.5 0 117.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.314 6.364c5.857-5.858 15.355-5.858 21.213 0"></path>',
                                'ac' => '<path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.364-7.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 16.95l1.414 1.414M7.05 7.05L5.636 5.636"></path>',
                                'tv' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
                                'bathtub' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>',
                                'safe' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>',
                                'minibar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path>',
                                'phone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>',
                                'balcony' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                                'laundry' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>',
                            ];
                            $common_amenities = [
                                'wifi' => t('Wi-Fi tốc độ cao', 'High-speed Wi-Fi'),
                                'ac' => t('Điều hòa nhiệt độ', 'Air conditioning'),
                                'tv' => t('Smart TV 4K', 'Smart TV 4K'),
                                'bathtub' => t('Bồn tắm riêng', 'Private bathtub'),
                                'safe' => t('Két an toàn', 'Safe box'),
                                'minibar' => t('Minibar & Trà/Cà phê', 'Minibar & Tea/Coffee'),
                                'phone' => t('Điện thoại phòng', 'Room phone'),
                                'balcony' => t('Ban công/Cửa sổ lớn', 'Balcony/Large window'),
                                'laundry' => t('Dịch vụ giặt là', 'Laundry service')
                            ];
                            ?>
                            <?php foreach ( $amenities as $amn ) : 
                                if (!isset($common_amenities[$amn])) continue;
                                $svg_path = isset($amenity_icons[$amn]) ? $amenity_icons[$amn] : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                            ?>
                                <div class="flex items-center gap-3 text-slate-600">
                                    <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $svg_path; ?></svg>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo esc_html($common_amenities[$amn]); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Policies -->
                        <?php if ( ! empty( $policies ) ) : ?>
                            <hr class="border-slate-100 my-10">
                            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php echo t('Chính sách phòng', 'Room Policies'); ?>
                            </h3>
                            <div class="prose prose-slate max-w-none text-sm text-slate-600 mb-10">
                                <?php echo wp_kses_post( wpautop( $policies ) ); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Room Selection -->
                    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-xl font-bold mb-6"><?php echo t('Chọn phòng', 'Select Room'); ?></h3>
                        
                        <div class="flex items-center gap-6 mb-8 text-sm">
                            <div class="flex items-center gap-2"><div class="w-6 h-6 rounded border border-slate-200 bg-white"></div><span class="text-slate-600"><?php echo t('Còn trống', 'Available'); ?></span></div>
                            <div class="flex items-center gap-2"><div class="w-6 h-6 rounded border border-blue-600 bg-blue-50 text-blue-600 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div><span class="text-slate-600"><?php echo t('Đang chọn', 'Selected'); ?></span></div>
                            <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-slate-200 cursor-not-allowed"></div><span class="text-slate-600"><?php echo t('Đã đặt', 'Booked'); ?></span></div>
                        </div>

                        <?php
                        // Lấy danh sách phòng từ trường tùy chỉnh (Custom field), nếu không có thì dùng mảng mặc định
                        $room_numbers_meta = get_post_meta(get_the_ID(), '_room_numbers', true);
                        if (!empty($room_numbers_meta)) {
                            // Dữ liệu nhập vào dạng: "101, 102, 103, 201, 202, 203"
                            $all_rooms = array_map('trim', explode(',', $room_numbers_meta));
                        } else {
                            // Tạo mảng tự động làm mặc định
                            $all_rooms = array();
                            for ($floor = 1; $floor <= 2; $floor++) {
                                for ($r = 1; $r <= 6; $r++) {
                                    $all_rooms[] = $floor . sprintf('%02d', $r); // Tạo 101, 102... 201, 202...
                                }
                            }
                        }

                        // Giả lập danh sách phòng đã được đặt trước (sau này có thể query từ hệ thống booking)
                        $booked_rooms = array('103', '106', '204'); 

                        // Nhóm các phòng theo tầng (dựa vào số đầu tiên)
                        $floors = array();
                        foreach ($all_rooms as $rm) {
                            $floor_num = substr($rm, 0, 1);
                            if (!isset($floors[$floor_num])) $floors[$floor_num] = array();
                            $floors[$floor_num][] = $rm;
                        }
                        ksort($floors);
                        ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <?php foreach ($floors as $floor_num => $rooms_in_floor): ?>
                                <div>
                                    <h4 class="font-bold text-slate-700 mb-4 text-center bg-slate-50 py-2 rounded-lg"><?php echo t('Tầng', 'Floor'); ?> <?php echo esc_html($floor_num); ?></h4>
                                    <div class="grid grid-cols-3 gap-3">
                                        <?php foreach ($rooms_in_floor as $rm): 
                                            $is_booked = in_array($rm, $booked_rooms);
                                        ?>
                                            <?php if ($is_booked): ?>
                                                <button type="button" class="p-3 rounded-xl bg-slate-100 text-slate-400 font-medium cursor-not-allowed border border-transparent" disabled><span><?php echo esc_html($rm); ?></span></button>
                                            <?php else: ?>
                                                <button type="button" class="room-slot p-3 rounded-xl border border-slate-200 bg-white text-slate-600 font-medium hover:border-blue-600 hover:text-blue-600 transition-all focus:outline-none flex flex-col items-center justify-center gap-1" data-room="<?php echo esc_attr($rm); ?>"><span><?php echo esc_html($rm); ?></span></button>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-xl font-bold mb-2"><?php echo t('Thông tin khách hàng', 'Customer Information'); ?></h3>
                        <p class="text-sm text-slate-500 mb-6"><?php echo t('Vui lòng điền thông tin để chúng tôi có thể liên hệ với bạn.', 'Please fill in the information so we can contact you.'); ?></p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700"><?php echo t('Họ và tên', 'Full Name'); ?> <span class="text-red-500">*</span></label>
                                <input type="text" id="cust_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all placeholder:text-slate-400" placeholder="<?php echo esc_attr(t('VD: Nguyễn Văn A', 'Ex: John Doe')); ?>">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700"><?php echo t('Số điện thoại', 'Phone Number'); ?> <span class="text-red-500">*</span></label>
                                <input type="tel" id="cust_phone" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all placeholder:text-slate-400" placeholder="0901234567">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="cust_email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all placeholder:text-slate-400" placeholder="example@email.com">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700"><?php echo t('Ghi chú (Tùy chọn)', 'Note (Optional)'); ?></label>
                                <textarea id="cust_note" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all placeholder:text-slate-400" placeholder="<?php echo esc_attr(t('Yêu cầu đặc biệt...', 'Special requests...')); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Sidebar (1/3) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 sticky top-28 overflow-hidden">
                        
                        <!-- Header / Title -->
                        <div class="bg-slate-900 p-6 text-white">
                            <h3 class="text-lg font-bold mb-1"><?php echo t('Thông tin chuyến đi', 'Trip Information'); ?></h3>
                            <p class="text-slate-300 text-sm"><?php the_title(); ?></p>
                        </div>

                        <div class="p-6">
                            <?php 
                            $use_external = get_theme_mod('use_external_booking', 'no');
                            $engine_url = get_theme_mod('booking_engine_url', '');
                            $price = get_post_meta(get_the_ID(), '_price', true) ?: 1500000;
                            $capacity = get_post_meta(get_the_ID(), '_capacity', true) ?: 2;
                            ?>

                            
                            <!-- Booking Details -->
                            <div class="space-y-4 mb-6 text-sm">
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-500"><?php echo t('Nhận phòng', 'Check-in'); ?></span>
                                    <input type="date" id="check_in_date" class="bg-transparent border-none p-0 text-right font-medium text-slate-900 focus:ring-0 cursor-pointer min-w-[120px]">
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-500"><?php echo t('Trả phòng', 'Check-out'); ?></span>
                                    <input type="date" id="check_out_date" class="bg-transparent border-none p-0 text-right font-medium text-slate-900 focus:ring-0 cursor-pointer min-w-[120px]">
                                </div>
                                <div class="flex justify-between items-start pb-4 border-b border-slate-100">
                                    <span class="text-slate-500"><?php echo t('Phòng đã chọn', 'Selected Rooms'); ?></span>
                                    <div class="text-right">
                                        <span id="selected_rooms_count" class="font-bold text-blue-600">0 phòng</span>
                                        <div id="selected_rooms_list" class="text-xs text-slate-500 mt-2 flex flex-wrap gap-1.5 justify-end max-w-[150px]">
                                            <!-- e.g., <span class="bg-slate-100 px-2 py-1 rounded">101</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Details -->
                            <div class="bg-slate-50 rounded-2xl p-5 mb-6 border border-slate-100">
                                <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <?php echo t('Chi tiết giá', 'Price Details'); ?>
                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                </h4>
                                
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between text-slate-600">
                                        <span><?php echo t('Giá mỗi phòng/đêm', 'Price per room/night'); ?></span>
                                        <span class="font-medium" data-base-price="<?php echo esc_attr($price); ?>"><?php echo number_format($price); ?>đ</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600">
                                        <span><?php echo t('Số đêm', 'Nights'); ?></span>
                                        <span id="nights_count" class="font-medium">1 <?php echo t('đêm', 'night'); ?></span>
                                    </div>
                                    <div class="flex justify-between text-slate-600">
                                        <span><?php echo t('Tạm tính', 'Subtotal'); ?></span>
                                        <span id="subtotal_price" class="font-medium">0đ</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600 pb-4 border-b border-slate-200">
                                        <span><?php echo t('Phí thanh toán (5%)', 'Payment fee (5%)'); ?></span>
                                        <span id="fee_price" class="font-medium">0đ</span>
                                    </div>
                                    <div class="flex justify-between items-end pt-2">
                                        <span class="font-bold text-slate-900 text-base"><?php echo t('Tổng tiền', 'Total'); ?></span>
                                        <span id="total_price" class="text-2xl font-bold text-orange-500">0đ</span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($use_external === 'yes' && !empty($engine_url)): ?>
                                <a href="<?php echo esc_url(bookingroom_get_booking_url(array('room_id' => get_the_ID(), 'checkin' => date('Y-m-d'), 'checkout' => date('Y-m-d', strtotime('+1 day'))))); ?>" 
                                   class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-200/50 flex items-center justify-center text-base">
                                    <?php echo t('Đặt ngay trên hệ thống', 'Book now'); ?>
                                </a>
                                <p class="text-center text-xs text-slate-400 mt-4"><?php echo t('Bạn sẽ được chuyển đến trang đặt phòng an toàn', 'You will be redirected to a secure booking page'); ?></p>
                            <?php else: ?>
                                <button type="button" id="submit_booking" disabled class="w-full bg-orange-500 text-white font-bold py-4 rounded-xl hover:bg-orange-600 transition-all shadow-lg hover:shadow-orange-200/50 flex items-center justify-center text-base disabled:opacity-50 disabled:cursor-not-allowed">
                                    <?php echo t('Tiếp tục thanh toán', 'Continue to payment'); ?>
                                </button>
                                <p class="text-center text-xs text-slate-400 mt-4"><?php echo t('Bạn sẽ chưa bị trừ tiền ngay lúc này', 'You will not be charged right now'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    <?php endwhile; endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSlots = document.querySelectorAll('.room-slot');
    const selectedRoomsCountEl = document.getElementById('selected_rooms_count');
    const selectedRoomsListEl = document.getElementById('selected_rooms_list');
    const subtotalPriceEl = document.getElementById('subtotal_price');
    const feePriceEl = document.getElementById('fee_price');
    const totalPriceEl = document.getElementById('total_price');
    const nightsCountEl = document.getElementById('nights_count');
    const checkInInput = document.getElementById('check_in_date');
    const checkOutInput = document.getElementById('check_out_date');
    const submitBtn = document.getElementById('submit_booking');
    
    let selectedRooms = [];
    const basePrice = parseInt(document.querySelector('[data-base-price]').getAttribute('data-base-price')) || 1500000;
    let nights = 1;

    // Initialize dates from URL params or leave empty
    const urlParams = new URLSearchParams(window.location.search);
    const paramCheckIn = urlParams.get('check_in');
    const paramCheckOut = urlParams.get('check_out');

    if (paramCheckIn) {
        checkInInput.value = paramCheckIn;
    } else {
        checkInInput.value = '';
    }

    if (paramCheckOut) {
        checkOutInput.value = paramCheckOut;
    } else {
        checkOutInput.value = '';
    }

    // Set minimum date to today
    const todayStr = new Date().toLocaleDateString('sv-SE'); // YYYY-MM-DD
    checkInInput.min = todayStr;
    checkOutInput.min = todayStr;

    // Update checkout min when checkin changes
    checkInInput.addEventListener('change', function() {
        if (this.value) {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutInput.min = nextDay.toLocaleDateString('sv-SE');
            // If checkout is before new checkin, reset it
            if (checkOutInput.value && checkOutInput.value <= this.value) {
                checkOutInput.value = nextDay.toLocaleDateString('sv-SE');
            }
        }
    }, true);

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    function calculateNights() {
        const start = new Date(checkInInput.value);
        const end = new Date(checkOutInput.value);
        if (start && end && end > start && !isNaN(start.getTime()) && !isNaN(end.getTime())) {
            const diffTime = Math.abs(end - start);
            nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        } else {
            nights = 0;
        }
        nightsCountEl.textContent = nights + ' <?php echo t('đêm', 'night(s)'); ?>';
        updatePrice();
    }

    function updatePrice() {
        const count = selectedRooms.length;
        const subtotal = basePrice * count * nights;
        const fee = subtotal * 0.05; // 5% fee
        const total = subtotal + fee;

        subtotalPriceEl.textContent = formatMoney(subtotal);
        feePriceEl.textContent = formatMoney(fee);
        totalPriceEl.textContent = formatMoney(total);

        if (count > 0) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'true');
        }
    }

    function updateSidebar() {
        selectedRoomsCountEl.textContent = selectedRooms.length + ' <?php echo t('phòng', 'rooms'); ?>';
        
        selectedRoomsListEl.innerHTML = '';
        if (selectedRooms.length === 0) {
            selectedRoomsListEl.innerHTML = '<span class="italic text-slate-400"><?php echo t('Chưa chọn phòng', 'No rooms selected'); ?></span>';
        } else {
            selectedRooms.forEach(room => {
                const span = document.createElement('span');
                span.className = 'bg-blue-50 text-blue-600 border border-blue-200 px-2 py-1 rounded font-medium shadow-sm';
                span.textContent = room;
                selectedRoomsListEl.appendChild(span);
            });
        }
        
        updatePrice();
    }

    roomSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            const roomNumber = this.getAttribute('data-room');
            const isSelected = this.classList.contains('bg-blue-50');

            if (isSelected) {
                // Deselect
                this.classList.remove('bg-blue-50', 'border-blue-600', 'text-blue-600');
                this.classList.add('bg-white', 'border-slate-200', 'text-slate-600');
                
                // Remove SVG checkmark
                const checkmark = this.querySelector('svg');
                if (checkmark) checkmark.remove();
                
                selectedRooms = selectedRooms.filter(r => r !== roomNumber);
            } else {
                // Select
                this.classList.remove('bg-white', 'border-slate-200', 'text-slate-600');
                this.classList.add('bg-blue-50', 'border-blue-600', 'text-blue-600');
                
                // Add SVG checkmark
                this.insertAdjacentHTML('beforeend', '<svg class="w-4 h-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>');
                
                selectedRooms.push(roomNumber);
                // Sort numerically
                selectedRooms.sort((a, b) => parseInt(a) - parseInt(b));
            }
            
            updateSidebar();
        });
    });

    checkInInput.addEventListener('change', calculateNights);
    checkOutInput.addEventListener('change', calculateNights);

    submitBtn.addEventListener('click', function() {
        const name = document.getElementById('cust_name').value;
        const phone = document.getElementById('cust_phone').value;
        const email = document.getElementById('cust_email').value;
        const note = document.getElementById('cust_note').value;
        
        if (!name || !phone || !email || selectedRooms.length === 0) {
            alert('<?php echo t('Vui lòng điền đầy đủ thông tin và chọn phòng.', 'Please fill in all information and select a room.'); ?>');
            return;
        }

        const data = {
            action: 'process_booking',
            nonce: booking_ajax.nonce,
            room_id: <?php echo get_the_ID(); ?>,
            check_in: checkInInput.value,
            check_out: checkOutInput.value,
            guests: <?php echo $capacity; ?>,
            name: name,
            phone: phone,
            email: email,
            note: note,
            selected_rooms: selectedRooms.join(', ')
        };

        submitBtn.disabled = true;
        submitBtn.textContent = '<?php echo t('Đang xử lý...', 'Processing...'); ?>';

        jQuery.ajax({
            url: booking_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = '<?php echo home_url('/tra-cuu'); ?>?booking_id=' + response.data.booking_id + '&phone=' + phone;
                } else {
                    alert(response.data.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '<?php echo t('Tiếp tục thanh toán', 'Continue to payment'); ?>';
                }
            },
            error: function() {
                alert('<?php echo t('Có lỗi xảy ra, vui lòng thử lại.', 'An error occurred, please try again.'); ?>');
                submitBtn.disabled = false;
                submitBtn.textContent = '<?php echo t('Tiếp tục thanh toán', 'Continue to payment'); ?>';
            }
        });
    });

    // Initial calculation
    calculateNights();
    updateSidebar();
});
</script>

<?php get_footer(); ?>
