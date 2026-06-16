<?php
/**
 * Template Name: Tìm phòng trống
 * Trang tìm kiếm phòng trống theo ngày, số khách, loại phòng.
 *
 * @package BookingRoom
 */

get_header();

// Đọc params từ URL (nếu có, dùng để pre-fill form)
$pre_ci       = isset( $_GET['check_in'] )  ? sanitize_text_field( $_GET['check_in'] )  : '';
$pre_co       = isset( $_GET['check_out'] ) ? sanitize_text_field( $_GET['check_out'] ) : '';
$pre_adults   = isset( $_GET['adults'] )    ? absint( $_GET['adults'] )    : 2;
$pre_children = isset( $_GET['children'] )  ? absint( $_GET['children'] )  : 0;
$pre_type     = isset( $_GET['room_type'] ) ? sanitize_text_field( $_GET['room_type'] ) : '';
$pre_nights   = 0;
if ( $pre_ci && $pre_co ) {
    $ts1 = strtotime( $pre_ci );
    $ts2 = strtotime( $pre_co );
    if ( $ts2 > $ts1 ) $pre_nights = round( ( $ts2 - $ts1 ) / DAY_IN_SECONDS );
}

$use_hb    = function_exists( 'bookingroom_is_wphb_active' ) && bookingroom_is_wphb_active();
$taxonomy  = $use_hb ? 'hb_room_type' : 'room_category';
$room_types = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
?>

<main class="bg-slate-50 min-h-screen">

    <!-- ── Hero ── -->
    <section class="relative bg-slate-900 overflow-hidden py-20">
        <div class="absolute inset-0 opacity-30">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=2000"
                 class="w-full h-full object-cover" alt="">
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full text-white text-sm font-semibold mb-4">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <?php echo t('Kiểm tra phòng trống theo ngày', 'Check room availability by date'); ?>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
                    <?php echo t('Tìm phòng nghỉ<br>', 'Find your perfect<br>'); ?>
                    <span class="text-orange-400"><?php echo t('hoàn hảo cho bạn', 'stay'); ?></span>
                </h1>
                <p class="text-slate-300 text-base"><?php echo t('Nhập ngày và số khách để xem ngay danh sách phòng còn trống cùng giá chi tiết.', 'Enter dates and guests to instantly see available rooms and detailed prices.'); ?></p>
            </div>
        </div>
    </section>

    <!-- ── Search Panel ── -->
    <div class="container mx-auto px-4 -mt-10 relative z-20 pb-20">

        <!-- Sticky Search Form -->
        <div id="avail-search-panel"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 md:p-10 mb-10 transition-all duration-300">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-black">🔍</span>
                    <?php echo t('Kiểm tra phòng trống', 'Check Availability'); ?>
                </h2>
                <?php if ($pre_ci && $pre_co) : ?>
                <span class="text-sm text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg font-medium">
                    <?php echo date('d/m/Y', strtotime($pre_ci)); ?> → <?php echo date('d/m/Y', strtotime($pre_co)); ?>
                    · <?php echo $pre_nights; ?> <?php echo t('đêm', 'nights'); ?>
                </span>
                <?php endif; ?>
            </div>

            <form id="br-avail-form" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4" novalidate>
                <?php wp_nonce_field( 'booking_nonce', 'br_avail_nonce', false ); ?>

                <!-- Check-in -->
                <div class="xl:col-span-1">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
                        📅 <?php echo t('Ngày nhận phòng', 'Check-in Date'); ?>
                    </label>
                    <input type="date" id="br_check_in" name="check_in" required
                        value="<?php echo esc_attr($pre_ci); ?>"
                        min="<?php echo date('Y-m-d'); ?>"
                        class="avail-input w-full bg-slate-50 border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-semibold text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all cursor-pointer">
                </div>

                <!-- Check-out -->
                <div class="xl:col-span-1">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
                        📅 <?php echo t('Ngày trả phòng', 'Check-out Date'); ?>
                    </label>
                    <input type="date" id="br_check_out" name="check_out" required
                        value="<?php echo esc_attr($pre_co); ?>"
                        class="avail-input w-full bg-slate-50 border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-semibold text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all cursor-pointer">
                </div>

                <!-- Guests -->
                <div class="xl:col-span-1">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
                        👥 <?php echo t('Số khách', 'Guests'); ?>
                    </label>
                    <div class="avail-input bg-slate-50 border-2 border-slate-200 rounded-2xl px-4 py-2 flex items-center gap-3 focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-200 transition-all">
                        <div class="flex flex-col items-center gap-0 flex-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase"><?php echo t('Người lớn', 'Adults'); ?></span>
                            <div class="flex items-center gap-2">
                                <button type="button" class="br-counter-btn w-6 h-6 text-base font-black text-slate-500 hover:text-orange-500 transition-colors leading-none flex items-center justify-center" data-target="br_adults" data-action="minus">−</button>
                                <input type="number" id="br_adults" name="adults" value="<?php echo $pre_adults; ?>" min="1" max="10" readonly class="w-8 text-center font-black text-slate-800 text-base border-none bg-transparent outline-none" style="-moz-appearance:textfield">
                                <button type="button" class="br-counter-btn w-6 h-6 text-base font-black text-slate-500 hover:text-orange-500 transition-colors leading-none flex items-center justify-center" data-target="br_adults" data-action="plus">+</button>
                            </div>
                        </div>
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="flex flex-col items-center gap-0 flex-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase"><?php echo t('Trẻ em', 'Children'); ?></span>
                            <div class="flex items-center gap-2">
                                <button type="button" class="br-counter-btn w-6 h-6 text-base font-black text-slate-500 hover:text-orange-500 transition-colors leading-none flex items-center justify-center" data-target="br_children" data-action="minus">−</button>
                                <input type="number" id="br_children" name="children" value="<?php echo $pre_children; ?>" min="0" max="10" readonly class="w-8 text-center font-black text-slate-800 text-base border-none bg-transparent outline-none" style="-moz-appearance:textfield">
                                <button type="button" class="br-counter-btn w-6 h-6 text-base font-black text-slate-500 hover:text-orange-500 transition-colors leading-none flex items-center justify-center" data-target="br_children" data-action="plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Type -->
                <div class="xl:col-span-1">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
                        🛏️ <?php echo t('Loại phòng', 'Room Type'); ?>
                    </label>
                    <select id="br_room_type" name="room_type"
                        class="avail-input w-full bg-slate-50 border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-semibold text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all cursor-pointer appearance-none">
                        <option value=""><?php echo t('Tất cả loại phòng', 'All room types'); ?></option>
                        <?php
                        if ( ! is_wp_error( $room_types ) && ! empty( $room_types ) ) {
                            foreach ( $room_types as $rt ) {
                                $sel = selected( $pre_type, $rt->slug, false );
                                echo '<option value="' . esc_attr($rt->slug) . '"' . $sel . '>' . esc_html($rt->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Submit -->
                <div class="xl:col-span-1 flex items-end">
                    <button type="submit" id="br-avail-submit-btn"
                        class="w-full bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-3.5 px-6 rounded-2xl transition-all shadow-lg hover:shadow-orange-200/60 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2.5"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2.5"/>
                        </svg>
                        <span><?php echo t('Tìm phòng trống', 'Find available rooms'); ?></span>
                    </button>
                </div>
            </form>

            <!-- Quick date presets -->
            <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-slate-100">
                <span class="text-xs text-slate-400 font-medium self-center"><?php echo t('Chọn nhanh:', 'Quick select:'); ?></span>
                <button type="button" class="preset-btn text-xs px-3 py-1.5 bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 rounded-lg font-semibold transition-all" data-nights="1"><?php echo t('Tối nay (1 đêm)', 'Tonight (1 night)'); ?></button>
                <button type="button" class="preset-btn text-xs px-3 py-1.5 bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 rounded-lg font-semibold transition-all" data-nights="2"><?php echo t('2 đêm', '2 nights'); ?></button>
                <button type="button" class="preset-btn text-xs px-3 py-1.5 bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 rounded-lg font-semibold transition-all" data-nights="3"><?php echo t('3 đêm', '3 nights'); ?></button>
                <button type="button" class="preset-btn text-xs px-3 py-1.5 bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 rounded-lg font-semibold transition-all" data-nights="7"><?php echo t('1 tuần', '1 week'); ?></button>
                <button type="button" class="preset-btn text-xs px-3 py-1.5 bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 rounded-lg font-semibold transition-all" data-type="weekend"><?php echo t('Cuối tuần này', 'This weekend'); ?></button>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="br-avail-loading" class="hidden">
            <div class="flex flex-col items-center py-20 gap-4 text-slate-400">
                <div class="w-12 h-12 border-4 border-slate-200 border-t-orange-500 rounded-full animate-spin"></div>
                <p class="font-semibold"><?php echo t('Đang tìm phòng trống...', 'Finding available rooms...'); ?></p>
            </div>
        </div>

        <!-- Results -->
        <div id="br-avail-results">
            <?php
            // Nếu đã có params trên URL → tự động tìm kiếm (server-side render)
            if ( $pre_ci && $pre_co && function_exists( 'bookingroom_find_available_rooms' ) ) :
                $rooms = bookingroom_find_available_rooms( $pre_ci, $pre_co, $pre_adults, $pre_children, $pre_type );
                $ci_ts = strtotime( $pre_ci );
                $co_ts = strtotime( $pre_co );
                $available_count = count( array_filter( $rooms, fn($r) => $r['free_units'] > 0 ) );
                $days = [ t('Chủ nhật', 'Sunday'), t('Thứ 2', 'Monday'), t('Thứ 3', 'Tuesday'), t('Thứ 4', 'Wednesday'), t('Thứ 5', 'Thursday'), t('Thứ 6', 'Friday'), t('Thứ 7', 'Saturday') ];
            ?>
                <!-- Summary bar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-[#d35400] to-[#b84300] text-white rounded-2xl p-5 mb-6 shadow-lg">
                    <div>
                        <div class="text-2xl font-black">
                            <?php echo $available_count > 0 ? sprintf(t('%d loại phòng còn trống', '%d room types available'), $available_count) : t('Không có phòng trống', 'No rooms available'); ?>
                        </div>
                        <div class="text-orange-200 text-sm mt-1">
                            <?php echo $pre_nights; ?> <?php echo t('đêm', 'nights'); ?> · <?php echo $pre_adults; ?> <?php echo t('người lớn', 'adults'); ?><?php echo $pre_children ? " + {$pre_children} " . t('trẻ em', 'children') : ''; ?>
                        </div>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <div class="bg-white/15 backdrop-blur px-4 py-2 rounded-xl text-sm font-semibold">
                            📅 <?php echo $days[date('w', $ci_ts)]; ?>, <?php echo date('d/m/Y', $ci_ts); ?>
                        </div>
                        <div class="bg-white/15 backdrop-blur px-4 py-2 rounded-xl text-sm font-semibold">
                            📅 <?php echo $days[date('w', $co_ts)]; ?>, <?php echo date('d/m/Y', $co_ts); ?>
                        </div>
                    </div>
                </div>

                <?php if ( empty( $rooms ) ) : ?>
                    <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo t('Không có phòng trống', 'No rooms available'); ?></h3>
                        <p class="text-slate-500"><?php echo t('Vui lòng thử chọn ngày khác hoặc loại phòng khác.', 'Please try choosing different dates or room types.'); ?></p>
                    </div>
                <?php else : ?>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ( $rooms as $room ) :
                        $pb         = $room['price_breakdown'];
                        $is_avail   = $room['free_units'] > 0;
                        $book_url   = add_query_arg([
                            'check_in'  => $pre_ci,
                            'check_out' => $pre_co,
                            'adults'    => $pre_adults,
                            'children'  => $pre_children,
                            'step'      => 'book',
                        ], $room['permalink']);
                    ?>
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl border-2 <?php echo $is_avail ? 'border-slate-100 hover:border-orange-300' : 'border-slate-100 opacity-70'; ?> transition-all duration-300 group flex flex-col">

                        <!-- Image -->
                        <div class="relative h-52 overflow-hidden bg-slate-200">
                            <img src="<?php echo esc_url($room['thumbnail']); ?>"
                                 alt="<?php echo esc_attr($room['title']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <?php if ($room['type_name']) : ?>
                            <div class="absolute top-3 left-3 bg-[#d35400] text-white text-[10px] font-black uppercase px-3 py-1 rounded-full">
                                <?php echo esc_html($room['type_name']); ?>
                            </div>
                            <?php endif; ?>

                            <div class="absolute top-3 right-3">
                                <?php if ($is_avail) : ?>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    ✓ <?php echo $room['free_units']; ?> <?php echo t('phòng trống', 'rooms available'); ?>
                                </span>
                                <?php else : ?>
                                <span class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1 rounded-full">
                                    ✗ <?php echo t('Hết phòng', 'Fully booked'); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur rounded-xl px-3 py-2 shadow">
                                <div class="text-[#d35400] font-black text-sm"><?php echo number_format($room['price']); ?>đ</div>
                                <div class="text-slate-400 text-[9px] font-medium">/ <?php echo t('đêm', 'night'); ?></div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6 flex flex-col flex-1 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-[#d35400] transition-colors">
                                    <?php echo esc_html($room['title']); ?>
                                </h3>
                                <?php if ($room['excerpt']) : ?>
                                <p class="text-slate-500 text-sm leading-relaxed"><?php echo esc_html($room['excerpt']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Meta -->
                            <div class="flex items-center gap-5 text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <?php echo $room['capacity']; ?> <?php echo t('khách', 'guests'); ?>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <?php echo $pre_nights; ?> <?php echo t('đêm', 'nights'); ?>
                                </span>
                                <span class="flex items-center gap-1.5 text-<?php echo $is_avail ? 'blue' : 'red'; ?>-600 font-semibold">
                                    <?php echo $is_avail ? $room['free_units'] . '/' . $room['total_units'] . ' ' . t('phòng', 'rooms') : t('Hết phòng', 'Fully booked'); ?>
                                </span>
                            </div>

                            <!-- Price breakdown -->
                            <?php if ($pb && $pb['total_nights'] > 0) : ?>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-sm space-y-2">
                                <?php if ($pb['weekday_nights'] > 0 && $pb['weekend_nights'] > 0) : ?>
                                <div class="flex justify-between text-slate-500">
                                    <span><?php echo t('Trong tuần', 'Weekdays'); ?> (<?php echo $pb['weekday_nights']; ?> <?php echo t('đêm', 'nights'); ?>)</span>
                                    <span class="font-semibold"><?php echo number_format($pb['weekday_price']); ?>đ</span>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span><?php echo t('Cuối tuần', 'Weekend'); ?> (<?php echo $pb['weekend_nights']; ?> <?php echo t('đêm', 'nights'); ?>)</span>
                                    <span class="font-semibold"><?php echo number_format($pb['weekend_price']); ?>đ</span>
                                </div>
                                <?php else : ?>
                                <div class="flex justify-between text-slate-500">
                                    <span><?php echo $pb['total_nights']; ?> <?php echo t('đêm', 'nights'); ?> × <?php echo number_format($room['price']); ?>đ</span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                    <span class="font-bold text-slate-900"><?php echo t('Tổng cộng', 'Total'); ?></span>
                                    <span class="text-xl font-black text-[#d35400]"><?php echo number_format($pb['subtotal']); ?>đ</span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- CTA -->
                            <div class="mt-auto pt-2">
                                <?php if ($is_avail) : ?>
                                <a href="<?php echo esc_url($book_url); ?>"
                                   class="block w-full text-center bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-3.5 rounded-2xl transition-all shadow-md hover:shadow-lg hover:shadow-orange-200/50">
                                    <?php echo t('Đặt phòng ngay →', 'Book Now →'); ?>
                                </a>
                                <?php else : ?>
                                <a href="<?php echo get_permalink($room['id']); ?>"
                                   class="block w-full text-center bg-slate-100 text-slate-500 font-bold py-3.5 rounded-2xl">
                                    <?php echo t('Xem chi tiết phòng', 'View room details'); ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; // endif rooms ?>

            <?php else : ?>
                <!-- Placeholder khi chưa tìm -->
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo t('Chọn ngày để tìm phòng', 'Select dates to find rooms'); ?></h3>
                    <p class="text-slate-500 max-w-sm mx-auto"><?php echo t('Nhập ngày nhận phòng và trả phòng phía trên để xem danh sách phòng còn trống.', 'Enter check-in and check-out dates above to see available rooms.'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
/* Tailwind-style utilities (fallback nếu chưa load) */
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }

/* Custom avail input focus ring */
.avail-input:focus-within { border-color: #d35400 !important; }

/* Browser webkit date picker fix */
input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var ajaxUrl = typeof booking_ajax !== 'undefined' ? booking_ajax.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>';
    var nonce   = typeof booking_ajax !== 'undefined' ? booking_ajax.nonce : '';

    // ── Counter Buttons ────────────────────────────────────────────────
    document.querySelectorAll('.br-counter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target  = this.dataset.target;
            var action  = this.dataset.action;
            var input   = document.getElementById(target);
            if (!input) return;
            var cur = parseInt(input.value) || 0;
            var min = parseInt(input.min) || 0;
            var max = parseInt(input.max) || 99;
            if (action === 'plus'  && cur < max) input.value = cur + 1;
            if (action === 'minus' && cur > min) input.value = cur - 1;
        });
    });

    // ── Date Validation ────────────────────────────────────────────────
    var ciInput = document.getElementById('br_check_in');
    var coInput = document.getElementById('br_check_out');

    if (ciInput && coInput) {
        var today = new Date().toLocaleDateString('sv-SE');
        ciInput.min = today;
        coInput.min = today;

        ciInput.addEventListener('change', function() {
            var ci   = new Date(this.value);
            var next = new Date(ci);
            next.setDate(next.getDate() + 1);
            var nextStr = next.toLocaleDateString('sv-SE');
            coInput.min = nextStr;
            if (coInput.value && coInput.value <= this.value) {
                coInput.value = nextStr;
            }
        });
    }

    // ── Quick Presets ──────────────────────────────────────────────────
    document.querySelectorAll('.preset-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var nights = parseInt(this.dataset.nights) || 0;
            var type   = this.dataset.type || '';

            var today = new Date();
            var ci, co;

            if (type === 'weekend') {
                // Tìm thứ 6 tới
                var day = today.getDay();
                var daysToFri = (5 - day + 7) % 7 || 7;
                ci = new Date(today);
                ci.setDate(today.getDate() + daysToFri);
                co = new Date(ci);
                co.setDate(ci.getDate() + 2); // Thứ 6 + 2 = Chủ nhật
            } else {
                ci = new Date(today);
                co = new Date(today);
                co.setDate(today.getDate() + nights);
            }

            if (ciInput) ciInput.value = ci.toLocaleDateString('sv-SE');
            if (coInput) {
                coInput.min = ci.toLocaleDateString('sv-SE');
                coInput.value = co.toLocaleDateString('sv-SE');
            }

            // Highlight button
            document.querySelectorAll('.preset-btn').forEach(function(b) {
                b.classList.remove('bg-orange-50', 'border-orange-400', 'text-orange-700');
            });
            btn.classList.add('bg-orange-50', 'border-orange-400', 'text-orange-700');
        });
    });

    // ── AJAX Form Submit ───────────────────────────────────────────────
    var form     = document.getElementById('br-avail-form');
    var loading  = document.getElementById('br-avail-loading');
    var results  = document.getElementById('br-avail-results');
    var submitBtn = document.getElementById('br-avail-submit-btn');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var checkIn   = document.getElementById('br_check_in')?.value || '';
        var checkOut  = document.getElementById('br_check_out')?.value || '';
        var adults    = parseInt(document.getElementById('br_adults')?.value) || 1;
        var children  = parseInt(document.getElementById('br_children')?.value) || 0;
        var roomType  = document.getElementById('br_room_type')?.value || '';
        var nonceVal  = document.querySelector('[name="br_avail_nonce"]')?.value || nonce;

        if (!checkIn || !checkOut) {
            alert('<?php echo t('Vui lòng chọn ngày nhận phòng và trả phòng.', 'Please select check-in and check-out dates.'); ?>');
            return;
        }

        // UI
        submitBtn.disabled = true;
        submitBtn.querySelector('span').textContent = '<?php echo t('Đang tìm...', 'Searching...'); ?>';
        if (loading)  loading.classList.remove('hidden');
        if (results)  results.innerHTML = '';

        // Update URL silently
        var url = new URL(window.location.href);
        url.searchParams.set('check_in',  checkIn);
        url.searchParams.set('check_out', checkOut);
        url.searchParams.set('adults',    adults);
        url.searchParams.set('children',  children);
        if (roomType) url.searchParams.set('room_type', roomType);
        else url.searchParams.delete('room_type');
        window.history.replaceState(null, '', url);

        // AJAX
        var fd = new FormData();
        fd.append('action',    'bookingroom_check_availability');
        fd.append('nonce',     nonceVal);
        fd.append('check_in',  checkIn);
        fd.append('check_out', checkOut);
        fd.append('adults',    adults);
        fd.append('children',  children);
        fd.append('room_type', roomType);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (loading) loading.classList.add('hidden');
                if (!res.success) {
                    results.innerHTML = buildError(res.data?.message || '<?php echo t('Có lỗi xảy ra.', 'An error occurred.'); ?>');
                    return;
                }
                results.innerHTML = buildResults(res.data, checkIn, checkOut, adults, children);
                results.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function() {
                if (loading) loading.classList.add('hidden');
                results.innerHTML = buildError('<?php echo t('Kết nối thất bại. Vui lòng thử lại.', 'Connection failed. Please try again.'); ?>');
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.querySelector('span').textContent = '<?php echo t('Tìm phòng trống', 'Find available rooms'); ?>';
            });
    });

    // ── Build Results HTML (client-side) ──────────────────────────────
    function fmt(n) { return new Intl.NumberFormat('vi-VN').format(n); }
    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    var DAYS = ['<?php echo t("Chủ nhật", "Sunday"); ?>','<?php echo t("Thứ 2", "Monday"); ?>','<?php echo t("Thứ 3", "Tuesday"); ?>','<?php echo t("Thứ 4", "Wednesday"); ?>','<?php echo t("Thứ 5", "Thursday"); ?>','<?php echo t("Thứ 6", "Friday"); ?>','<?php echo t("Thứ 7", "Saturday"); ?>'];

    function buildResults(d, ci, co, adults, children) {
        var html = '';
        var availMsg = d.available_count > 0
            ? '<span class="text-2xl font-black">' + d.available_count + ' <?php echo t("loại phòng còn trống", "room types available"); ?></span>'
            : '<span class="text-2xl font-black"><?php echo t("Không có phòng trống", "No rooms available"); ?></span>';

        html += '<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-[#d35400] to-[#b84300] text-white rounded-2xl p-5 mb-6 shadow-lg">';
        html += '<div>' + availMsg + '<div class="text-orange-200 text-sm mt-1">' + d.nights + ' <?php echo t("đêm", "nights"); ?> · ' + adults + ' <?php echo t("người lớn", "adults"); ?>' + (children ? ' + ' + children + ' <?php echo t("trẻ em", "children"); ?>' : '') + '</div></div>';
        html += '<div class="flex gap-3 flex-wrap">';
        html += '<div class="bg-white/15 backdrop-blur px-4 py-2 rounded-xl text-sm font-semibold">📅 ' + d.ci_dow + ', ' + d.ci_display + '</div>';
        html += '<div class="bg-white/15 backdrop-blur px-4 py-2 rounded-xl text-sm font-semibold">📅 ' + d.co_dow + ', ' + d.co_display + '</div>';
        html += '</div></div>';

        if (!d.rooms || !d.rooms.length) {
            return html + buildNoResults();
        }

        html += '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">';
        d.rooms.forEach(function(room) {
            html += buildCard(room, d, ci, co, adults, children);
        });
        html += '</div>';
        return html;
    }

    function buildCard(room, d, ci, co, adults, children) {
        var isAvail  = room.free_units > 0;
        var pb       = room.price_breakdown;
        var bookUrl  = room.permalink + '?check_in=' + ci + '&check_out=' + co + '&adults=' + adults + '&children=' + children + '&step=book';

        var priceBreakdownHtml = '';
        if (pb && pb.total_nights > 0) {
            priceBreakdownHtml += '<div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-sm space-y-2">';
            if (pb.weekday_nights > 0 && pb.weekend_nights > 0) {
                priceBreakdownHtml += '<div class="flex justify-between text-slate-500"><span><?php echo t("Trong tuần", "Weekdays"); ?> (' + pb.weekday_nights + ' <?php echo t("đêm", "nights"); ?>)</span><span class="font-semibold">' + fmt(pb.weekday_price) + 'đ</span></div>';
                priceBreakdownHtml += '<div class="flex justify-between text-slate-500"><span><?php echo t("Cuối tuần", "Weekend"); ?> (' + pb.weekend_nights + ' <?php echo t("đêm", "nights"); ?>)</span><span class="font-semibold">' + fmt(pb.weekend_price) + 'đ</span></div>';
            } else {
                priceBreakdownHtml += '<div class="flex justify-between text-slate-500"><span>' + pb.total_nights + ' <?php echo t("đêm", "nights"); ?> × ' + fmt(room.price) + 'đ</span></div>';
            }
            priceBreakdownHtml += '<div class="flex justify-between items-center pt-2 border-t border-slate-200"><span class="font-bold text-slate-900"><?php echo t("Tổng cộng", "Total"); ?></span><span class="text-xl font-black text-[#d35400]">' + fmt(pb.subtotal) + 'đ</span></div>';
            priceBreakdownHtml += '</div>';
        }

        var ctaHtml = isAvail
            ? '<a href="' + esc(bookUrl) + '" class="block w-full text-center bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-3.5 rounded-2xl transition-all shadow-md hover:shadow-lg"><?php echo t("Đặt phòng ngay →", "Book Now →"); ?></a>'
            : '<a href="' + esc(room.permalink) + '" class="block w-full text-center bg-slate-100 text-slate-500 font-bold py-3.5 rounded-2xl"><?php echo t("Xem chi tiết phòng", "View room details"); ?></a>';

        return '<div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl border-2 ' + (isAvail ? 'border-slate-100 hover:border-orange-300' : 'border-slate-100 opacity-70') + ' transition-all duration-300 group flex flex-col">'
            + '<div class="relative h-52 overflow-hidden bg-slate-200">'
            + '<img src="' + esc(room.thumbnail) + '" alt="' + esc(room.title) + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">'
            + (room.type_name ? '<div class="absolute top-3 left-3 bg-[#d35400] text-white text-[10px] font-black uppercase px-3 py-1 rounded-full">' + esc(room.type_name) + '</div>' : '')
            + '<div class="absolute top-3 right-3"><span class="' + (isAvail ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600') + ' text-xs font-bold px-3 py-1 rounded-full">' + (isAvail ? '✓ ' + room.free_units + ' <?php echo t("phòng trống", "rooms available"); ?>' : '✗ <?php echo t("Hết phòng", "Fully booked"); ?>') + '</span></div>'
            + '<div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur rounded-xl px-3 py-2 shadow"><div class="text-[#d35400] font-black text-sm">' + fmt(room.price) + 'đ</div><div class="text-slate-400 text-[9px] font-medium">/ <?php echo t("đêm", "night"); ?></div></div>'
            + '</div>'
            + '<div class="p-6 flex flex-col flex-1 gap-4">'
            + '<div><h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-[#d35400] transition-colors">' + esc(room.title) + '</h3>'
            + (room.excerpt ? '<p class="text-slate-500 text-sm leading-relaxed">' + esc(room.excerpt) + '</p>' : '') + '</div>'
            + '<div class="flex items-center gap-5 text-sm text-slate-500">'
            + '<span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>' + room.capacity + ' <?php echo t("khách", "guests"); ?></span>'
            + '<span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' + d.nights + ' <?php echo t("đêm", "nights"); ?></span>'
            + '</div>'
            + priceBreakdownHtml
            + '<div class="mt-auto pt-2">' + ctaHtml + '</div>'
            + '</div></div>';
    }

    function buildNoResults() {
        return '<div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">'
            + '<div class="w-16 h-16 text-slate-300 mx-auto mb-4"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>'
            + '<h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo t("Không có phòng trống", "No rooms available"); ?></h3>'
            + '<p class="text-slate-500"><?php echo t("Vui lòng thử chọn ngày khác hoặc loại phòng khác.", "Please try choosing different dates or room types."); ?></p></div>';
    }

    function buildError(msg) {
        return '<div class="text-center py-12 bg-red-50 border-2 border-red-200 rounded-3xl">'
            + '<p class="text-red-600 font-bold text-lg">⚠ ' + esc(msg) + '</p></div>';
    }

    // Auto-submit nếu có params trong URL và chưa có nội dung server-side
    <?php if ( ! $pre_ci && ! $pre_co ) : ?>
    // Không có pre-search, đợi user submit
    <?php else : ?>
    // Server-side render đã xử lý – không cần auto AJAX
    <?php endif; ?>
});
</script>

<?php get_footer(); ?>
