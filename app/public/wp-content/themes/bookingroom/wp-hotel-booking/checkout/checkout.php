<?php
/**
 * Template Override: WP Hotel Booking Checkout
 * Override trang thanh toán của plugin theo giao diện theme bookingroom.
 *
 * Đặt file tại: themes/bookingroom/wp-hotel-booking/checkout/checkout.php
 * Plugin sẽ tự động dùng file này.
 *
 * @package BookingRoom
 */

// Đảm bảo plugin đang active
if ( ! function_exists( 'tp_hotel_booking' ) && ! class_exists( 'WP_Hotel_Booking' ) ) {
    wp_redirect( home_url() );
    exit;
}

get_header();

// ── Lấy thông tin giỏ hàng / booking hiện tại ────────────────────────────
$cart = null;
if ( function_exists( 'hb_get_cart' ) ) {
    $cart = hb_get_cart();
} elseif ( isset( $GLOBALS['hotel_booking_cart'] ) ) {
    $cart = $GLOBALS['hotel_booking_cart'];
}

$cart_items   = [];
$subtotal     = 0;
$total        = 0;
$tax_amount   = 0;
$discount     = 0;

if ( $cart ) {
    if ( method_exists( $cart, 'getItems' ) ) {
        $cart_items = $cart->getItems();
    } elseif ( isset( $cart->items ) ) {
        $cart_items = $cart->items;
    }
    $subtotal   = method_exists( $cart, 'getSubTotal' )   ? $cart->getSubTotal()   : ( $cart->sub_total   ?? 0 );
    $total      = method_exists( $cart, 'getTotal' )      ? $cart->getTotal()      : ( $cart->total       ?? 0 );
    $tax_amount = method_exists( $cart, 'getTaxAmount' )  ? $cart->getTaxAmount()  : ( $cart->tax_amount  ?? 0 );
    $discount   = method_exists( $cart, 'getDiscount' )   ? $cart->getDiscount()   : ( $cart->discount    ?? 0 );
}

// Lấy check-in/check-out từ session/cart
$check_in  = '';
$check_out = '';
if ( ! empty( $cart_items ) ) {
    $first = reset( $cart_items );
    $check_in  = isset( $first->check_in_date )  ? $first->check_in_date  : '';
    $check_out = isset( $first->check_out_date ) ? $first->check_out_date : '';
}
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <!-- ── Breadcrumbs ── -->
    <div class="bg-white border-b border-slate-200">
        <div class="container mx-auto px-4 py-4 flex items-center text-sm text-slate-500">
            <a href="<?php echo home_url(); ?>" class="hover:text-[#d35400] transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="<?php echo esc_url( bookingroom_get_hbroom_archive_url() ); ?>" class="hover:text-[#d35400] transition-colors">Phòng nghỉ</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 font-medium">Thanh toán</span>
        </div>
    </div>

    <!-- ── Progress Steps ── -->
    <div class="bg-white border-b border-slate-100 py-5">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-0 max-w-lg mx-auto">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-green-600">Chọn phòng</span>
                </div>
                <div class="h-px w-12 bg-slate-300 mx-3"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-[#d35400] flex items-center justify-center text-white text-sm font-bold">2</div>
                    <span class="text-sm font-bold text-[#d35400]">Thanh toán</span>
                </div>
                <div class="h-px w-12 bg-slate-200 mx-3"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-400 text-sm font-bold">3</div>
                    <span class="text-sm text-slate-400">Xác nhận</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-6xl mx-auto">

            <!-- ── Form Thanh toán (2/3) ── -->
            <div class="lg:col-span-2">
                <h1 class="text-2xl font-bold text-slate-900 mb-8">Thông tin thanh toán</h1>

                <?php
                /**
                 * Hook chính của WP Hotel Booking để hiển thị form checkout.
                 * Plugin sẽ render form vào đây.
                 */
                ?>

                <div id="hb-checkout-wrapper" class="space-y-8">

                    <?php
                    // Render form checkout của plugin
                    if ( function_exists( 'tp_hotel_booking_checkout' ) ) {
                        tp_hotel_booking_checkout();
                    } elseif ( has_action( 'tp_hotel_booking_checkout_form' ) ) {
                        do_action( 'tp_hotel_booking_checkout_form' );
                    } else {
                        // Fallback: Render form tùy chỉnh
                        ?>
                        <!-- Thông tin khách hàng -->
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                            <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                                <span class="w-7 h-7 bg-[#d35400] text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                                Thông tin cá nhân
                            </h2>
                            <div id="wphb-checkout-form">
                                <?php
                                // Trigger plugin's checkout form action
                                do_action( 'hotel_booking_checkout_form' );
                                do_action( 'hb_checkout_form' );
                                ?>
                            </div>

                            <?php if ( empty( did_action( 'hotel_booking_checkout_form' ) ) && empty( did_action( 'hb_checkout_form' ) ) ) : ?>
                            <!-- Hiển thị form thuần nếu plugin chưa render -->
                            <form id="hb-checkout-form" method="post" action="" class="space-y-5">
                                <?php wp_nonce_field( 'hb_checkout', 'hb_checkout_nonce' ); ?>
                                <input type="hidden" name="action" value="hb_checkout">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                                        <input type="text" name="customer_firstname" placeholder="Nguyễn Văn A" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Họ <span class="text-red-500">*</span></label>
                                        <input type="text" name="customer_lastname" placeholder="Nguyễn" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="customer_email" placeholder="example@email.com" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                        <input type="tel" name="customer_phone" placeholder="0901234567" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Địa chỉ</label>
                                    <input type="text" name="customer_address" placeholder="Số nhà, đường, phường/xã..."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú đặc biệt</label>
                                    <textarea name="customer_note" rows="3" placeholder="Yêu cầu đặc biệt, giờ check-in dự kiến..."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-[#d35400] outline-none transition-all resize-none"></textarea>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                            <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                                <span class="w-7 h-7 bg-[#d35400] text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                                Phương thức thanh toán
                            </h2>

                            <?php
                            // Hook thanh toán của plugin
                            do_action( 'tp_hotel_booking_payment_methods' );
                            do_action( 'hb_payment_methods' );
                            ?>

                            <div class="space-y-3">
                                <!-- Chuyển khoản ngân hàng -->
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all payment-option border-[#d35400] bg-orange-50" id="pay-transfer-label">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="mt-1 accent-[#d35400]" checked>
                                    <div>
                                        <div class="font-bold text-slate-900 mb-1">Chuyển khoản ngân hàng</div>
                                        <p class="text-sm text-slate-500">Chuyển khoản trước khi nhận phòng. Xác nhận trong vòng 30 phút.</p>
                                        <div class="mt-3 text-xs bg-white border border-orange-200 rounded-xl p-3 text-slate-600 space-y-1">
                                            <div><strong>Ngân hàng:</strong> Vietcombank</div>
                                            <div><strong>Số tài khoản:</strong> <?php echo esc_html( get_theme_mod( 'bookingroom_bank_account', '1234567890' ) ); ?></div>
                                            <div><strong>Chủ tài khoản:</strong> <?php echo esc_html( get_theme_mod( 'bookingroom_bank_name', 'CONG TY TNHH RESORT' ) ); ?></div>
                                            <div><strong>Nội dung CK:</strong> [Họ tên] - [Số phòng]</div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Thanh toán tại quầy -->
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-slate-200 cursor-pointer transition-all hover:border-slate-300 payment-option" id="pay-cash-label">
                                    <input type="radio" name="payment_method" value="cash" class="mt-1 accent-[#d35400]">
                                    <div>
                                        <div class="font-bold text-slate-900 mb-1">Thanh toán tại quầy</div>
                                        <p class="text-sm text-slate-500">Thanh toán tiền mặt hoặc thẻ khi nhận phòng.</p>
                                    </div>
                                </label>

                                <?php do_action( 'tp_hotel_booking_extra_payment_methods' ); ?>
                            </div>
                        </div>

                        <!-- Nút xác nhận đặt phòng -->
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                            <div class="flex items-start gap-3 mb-6">
                                <input type="checkbox" id="agree-terms" class="mt-1 accent-[#d35400] w-4 h-4 cursor-pointer">
                                <label for="agree-terms" class="text-sm text-slate-600 cursor-pointer">
                                    Tôi đồng ý với <a href="#" class="text-[#d35400] hover:underline font-medium">Điều khoản dịch vụ</a> và
                                    <a href="#" class="text-[#d35400] hover:underline font-medium">Chính sách hủy phòng</a> của resort.
                                </label>
                            </div>
                            <button type="button" id="hb-confirm-booking"
                                class="w-full bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-4.5 py-[18px] rounded-2xl transition-all shadow-xl hover:shadow-orange-200/60 text-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                                disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Xác nhận đặt phòng
                            </button>
                            <p class="text-center text-xs text-slate-400 mt-3 flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Thông tin thanh toán được mã hóa SSL 256-bit
                            </p>
                        </div>
                    <?php } // end fallback ?>

                </div>
            </div>

            <!-- ── Tóm tắt đơn đặt phòng (1/3) ── -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 sticky top-28 overflow-hidden">
                    <div class="bg-slate-900 p-6 text-white">
                        <h3 class="text-lg font-bold">Tóm tắt đặt phòng</h3>
                        <?php if ( $check_in && $check_out ) : ?>
                            <p class="text-slate-300 text-xs mt-1">
                                <?php echo date( 'd/m/Y', strtotime( $check_in ) ); ?> →
                                <?php echo date( 'd/m/Y', strtotime( $check_out ) ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="p-6">
                        <!-- Danh sách phòng trong giỏ -->
                        <?php if ( ! empty( $cart_items ) ) : ?>
                            <div class="space-y-4 mb-6">
                                <?php foreach ( $cart_items as $item ) :
                                    $item_room_id = isset( $item->room_id ) ? $item->room_id : ( isset( $item->product_id ) ? $item->product_id : 0 );
                                    $item_qty     = isset( $item->qty ) ? intval( $item->qty ) : 1;
                                    $item_total   = isset( $item->total_price ) ? floatval( $item->total_price ) : 0;
                                    $item_ci      = isset( $item->check_in_date )  ? $item->check_in_date  : $check_in;
                                    $item_co      = isset( $item->check_out_date ) ? $item->check_out_date : $check_out;
                                ?>
                                    <div class="flex gap-4">
                                        <div class="w-20 h-16 rounded-xl overflow-hidden flex-shrink-0">
                                            <?php if ( $item_room_id && has_post_thumbnail( $item_room_id ) ) : ?>
                                                <?php echo get_the_post_thumbnail( $item_room_id, 'thumbnail', [ 'class' => 'w-full h-full object-cover' ] ); ?>
                                            <?php else : ?>
                                                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=200"
                                                     class="w-full h-full object-cover" alt="">
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-900 text-sm truncate"><?php echo $item_room_id ? get_the_title( $item_room_id ) : __( 'Phòng', 'bookingroom' ); ?></p>
                                            <?php if ( $item_ci && $item_co ) : ?>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    <?php echo date( 'd/m', strtotime( $item_ci ) ); ?> – <?php echo date( 'd/m/Y', strtotime( $item_co ) ); ?>
                                                </p>
                                            <?php endif; ?>
                                            <p class="text-xs text-slate-500">Số lượng: <?php echo $item_qty; ?></p>
                                            <p class="text-sm font-bold text-[#d35400] mt-1"><?php echo number_format( $item_total ); ?>đ</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <hr class="border-slate-100 mb-5">
                        <?php else : ?>
                            <div class="text-center py-6 text-slate-400 text-sm mb-4">
                                <p>Giỏ hàng trống.</p>
                                <a href="<?php echo esc_url( bookingroom_get_hbroom_archive_url() ); ?>"
                                   class="text-[#d35400] font-medium hover:underline mt-1 inline-block">Chọn phòng →</a>
                            </div>
                        <?php endif; ?>

                        <!-- Tổng giá -->
                        <div class="space-y-3 text-sm">
                            <?php if ( $subtotal ) : ?>
                            <div class="flex justify-between text-slate-600">
                                <span>Tạm tính</span>
                                <span class="font-medium"><?php echo number_format( $subtotal ); ?>đ</span>
                            </div>
                            <?php endif; ?>
                            <?php if ( $tax_amount ) : ?>
                            <div class="flex justify-between text-slate-600">
                                <span>Thuế & phí</span>
                                <span class="font-medium"><?php echo number_format( $tax_amount ); ?>đ</span>
                            </div>
                            <?php endif; ?>
                            <?php if ( $discount ) : ?>
                            <div class="flex justify-between text-green-600">
                                <span>Giảm giá</span>
                                <span class="font-medium">-<?php echo number_format( $discount ); ?>đ</span>
                            </div>
                            <?php endif; ?>
                            <?php if ( $total ) : ?>
                            <div class="flex justify-between items-end pt-3 border-t border-slate-200">
                                <span class="font-bold text-slate-900 text-base">Tổng tiền</span>
                                <span class="text-2xl font-bold text-[#d35400]"><?php echo number_format( $total ); ?>đ</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Trust badges -->
                        <div class="mt-6 pt-5 border-t border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Thanh toán bảo mật SSL
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Xác nhận tức thì qua Email
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Hỗ trợ 24/7: <?php echo esc_html( get_theme_mod( 'bookingroom_hotline', '0123 456 789' ) ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Payment method toggle style
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const paymentLabels  = document.querySelectorAll('.payment-option');
    paymentOptions.forEach((radio) => {
        radio.addEventListener('change', function () {
            paymentLabels.forEach(lbl => {
                lbl.classList.remove('border-[#d35400]', 'bg-orange-50');
                lbl.classList.add('border-slate-200');
            });
            this.closest('.payment-option')?.classList.remove('border-slate-200');
            this.closest('.payment-option')?.classList.add('border-[#d35400]', 'bg-orange-50');
        });
    });

    // Terms checkbox → enable confirm button
    const agreeCheckbox = document.getElementById('agree-terms');
    const confirmBtn    = document.getElementById('hb-confirm-booking');
    if (agreeCheckbox && confirmBtn) {
        agreeCheckbox.addEventListener('change', function () {
            confirmBtn.disabled = ! this.checked;
        });
        confirmBtn.addEventListener('click', function () {
            if (this.disabled) return;
            // Submit form plugin
            const form = document.getElementById('hb-checkout-form');
            if (form) {
                this.disabled = true;
                this.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Đang xử lý...';
                form.submit();
            }
        });
    }
});
</script>

<?php get_footer(); ?>
