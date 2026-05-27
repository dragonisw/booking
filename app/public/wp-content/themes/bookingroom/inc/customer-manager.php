<?php
/**
 * Customer & Booking Manager
 * Quản lý thông tin khách hàng và đặt phòng trong WordPress Admin
 */

// ============================================================
// 1. CUSTOM ADMIN COLUMNS cho Booking CPT
// ============================================================

add_filter('manage_booking_posts_columns', 'br_booking_columns');
function br_booking_columns($cols) {
    return array(
        'cb'            => $cols['cb'],
        'booking_id'    => '🔖 Mã ĐP',
        'customer_name' => '👤 Khách hàng',
        'contact'       => '📞 Liên hệ',
        'room_info'     => '🛏️ Phòng',
        'dates'         => '📅 Ngày ở',
        'nights'        => '🌙 Số đêm',
        'total_price'   => '💰 Tổng tiền',
        'status'        => '🚦 Trạng thái',
        'created'       => '🕐 Ngày đặt',
    );
}

add_action('manage_booking_posts_custom_column', 'br_booking_column_content', 10, 2);
function br_booking_column_content($col, $post_id) {
    $check_in    = get_post_meta($post_id, '_check_in', true);
    $check_out   = get_post_meta($post_id, '_check_out', true);
    $status      = get_post_meta($post_id, '_status', true) ?: 'pending';
    $name        = get_post_meta($post_id, '_customer_name', true) ?: '—';
    $phone       = get_post_meta($post_id, '_phone', true) ?: '—';
    $email       = get_post_meta($post_id, '_email', true) ?: '—';
    $room_id     = get_post_meta($post_id, '_room_id', true);
    $sel_rooms   = get_post_meta($post_id, '_selected_rooms', true) ?: '—';
    $price_meta  = get_post_meta($post_id, '_total_price', true);
    $note        = get_post_meta($post_id, '_note', true);

    // Tính tổng tiền nếu chưa có
    if (!$price_meta && $room_id && $check_in && $check_out) {
        $base  = get_post_meta($room_id, '_price', true) ?: 0;
        $d1    = new DateTime($check_in);
        $d2    = new DateTime($check_out);
        $nights_count = max(1, (int)$d2->diff($d1)->days);
        $rooms_count  = $sel_rooms !== '—' ? count(array_filter(explode(',', $sel_rooms))) : 1;
        $price_meta   = round($base * $nights_count * $rooms_count * 1.05);
    }

    $status_map = array(
        'pending'   => array('label' => 'Chờ xác nhận', 'color' => '#f59e0b', 'bg' => '#fef3c7'),
        'confirmed' => array('label' => 'Đã xác nhận',  'color' => '#10b981', 'bg' => '#d1fae5'),
        'checked_in'=> array('label' => 'Đã nhận phòng','color' => '#3b82f6', 'bg' => '#dbeafe'),
        'checked_out'=> array('label' => 'Đã trả phòng','color' => '#8b5cf6', 'bg' => '#ede9fe'),
        'cancelled' => array('label' => 'Đã huỷ',       'color' => '#ef4444', 'bg' => '#fee2e2'),
    );
    $st = $status_map[$status] ?? $status_map['pending'];

    switch ($col) {
        case 'booking_id':
            echo '<strong>#' . $post_id . '</strong>';
            break;
        case 'customer_name':
            echo '<strong>' . esc_html($name) . '</strong>';
            if ($note) echo '<br><span style="color:#94a3b8;font-size:11px;" title="' . esc_attr($note) . '">📝 ' . esc_html(mb_strimwidth($note, 0, 40, '...')) . '</span>';
            break;
        case 'contact':
            echo '<a href="tel:' . esc_attr($phone) . '" style="color:#2563eb;">' . esc_html($phone) . '</a>';
            echo '<br><a href="mailto:' . esc_attr($email) . '" style="color:#64748b;font-size:11px;">' . esc_html($email) . '</a>';
            break;
        case 'room_info':
            echo $room_id ? '<a href="' . get_edit_post_link($room_id) . '">' . esc_html(get_the_title($room_id)) . '</a>' : '—';
            if ($sel_rooms && $sel_rooms !== '—') echo '<br><span style="color:#64748b;font-size:11px;">Phòng: ' . esc_html($sel_rooms) . '</span>';
            break;
        case 'dates':
            $ci = $check_in  ? date_i18n('d/m/Y', strtotime($check_in))  : '—';
            $co = $check_out ? date_i18n('d/m/Y', strtotime($check_out)) : '—';
            echo '<span style="font-size:12px;">✈️ ' . $ci . '</span><br><span style="font-size:12px;">🏠 ' . $co . '</span>';
            break;
        case 'nights':
            if ($check_in && $check_out) {
                $d1 = new DateTime($check_in);
                $d2 = new DateTime($check_out);
                $n  = max(0, (int)$d2->diff($d1)->days);
                echo '<strong>' . $n . '</strong> đêm';
            } else { echo '—'; }
            break;
        case 'total_price':
            echo $price_meta ? '<strong style="color:#f97316;">' . number_format($price_meta) . 'đ</strong>' : '—';
            break;
        case 'status':
            echo '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;color:' . $st['color'] . ';background:' . $st['bg'] . ';">' . $st['label'] . '</span>';
            echo '<br><span class="br-quick-status" data-id="' . $post_id . '" style="margin-top:4px;display:block;">';
            foreach ($status_map as $key => $s) {
                if ($key !== $status) {
                    echo '<a href="' . admin_url('admin-post.php?action=br_change_status&booking_id=' . $post_id . '&status=' . $key . '&_wpnonce=' . wp_create_nonce('br_change_status_' . $post_id)) . '" style="font-size:10px;color:#94a3b8;margin-right:4px;" onclick="return confirm(\'Đổi sang ' . $s['label'] . '?\')">' . $s['label'] . '</a>';
                }
            }
            echo '</span>';
            break;
        case 'created':
            $post = get_post($post_id);
            echo date_i18n('d/m/Y H:i', strtotime($post->post_date));
            break;
    }
}

// Sắp xếp theo cột
add_filter('manage_edit-booking_sortable_columns', function($cols) {
    $cols['created']     = 'date';
    $cols['total_price'] = 'total_price';
    return $cols;
});

// ============================================================
// 2. ĐỔI TRẠNG THÁI BOOKING NHANH
// ============================================================

add_action('admin_post_br_change_status', 'br_handle_change_status');
function br_handle_change_status() {
    $bid    = intval($_GET['booking_id'] ?? 0);
    $status = sanitize_key($_GET['status'] ?? '');
    $nonce  = $_GET['_wpnonce'] ?? '';

    if (!$bid || !wp_verify_nonce($nonce, 'br_change_status_' . $bid) || !current_user_can('edit_posts')) {
        wp_die('Không có quyền.');
    }

    $allowed = array('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled');
    if (!in_array($status, $allowed)) wp_die('Trạng thái không hợp lệ.');

    update_post_meta($bid, '_status', $status);

    // Gửi email xác nhận nếu đổi sang confirmed
    if ($status === 'confirmed') {
        br_send_confirmation_email($bid);
    }

    $ref = wp_get_referer() ?: admin_url('edit.php?post_type=booking');
    wp_safe_redirect(add_query_arg('br_updated', $status, $ref));
    exit;
}

// ============================================================
// 3. GỬI EMAIL XÁC NHẬN CHO KHÁCH HÀNG
// ============================================================

function br_send_confirmation_email($booking_id) {
    $email   = get_post_meta($booking_id, '_email', true);
    $name    = get_post_meta($booking_id, '_customer_name', true) ?: 'Quý khách';
    $room_id = get_post_meta($booking_id, '_room_id', true);
    $ci      = get_post_meta($booking_id, '_check_in', true);
    $co      = get_post_meta($booking_id, '_check_out', true);
    $rooms   = get_post_meta($booking_id, '_selected_rooms', true);

    if (!$email) return false;

    $site    = get_bloginfo('name');
    $subject = "✅ Xác nhận đặt phòng #{$booking_id} – {$site}";

    $ci_fmt = $ci ? date_i18n('d/m/Y', strtotime($ci)) : '—';
    $co_fmt = $co ? date_i18n('d/m/Y', strtotime($co)) : '—';
    $room_name = $room_id ? get_the_title($room_id) : '—';

    $body = "
    <div style='font-family:Inter,sans-serif;max-width:600px;margin:auto;background:#f8fafc;border-radius:16px;overflow:hidden;'>
      <div style='background:linear-gradient(135deg,#2563eb,#1d4ed8);padding:32px;text-align:center;'>
        <h1 style='color:#fff;margin:0;font-size:24px;'>✅ Đặt phòng được xác nhận!</h1>
      </div>
      <div style='padding:32px;background:#fff;'>
        <p style='font-size:16px;color:#1e293b;'>Xin chào <strong>{$name}</strong>,</p>
        <p style='color:#475569;'>Chúng tôi xác nhận đơn đặt phòng của bạn. Vui lòng kiểm tra thông tin bên dưới.</p>
        <div style='background:#f1f5f9;border-radius:12px;padding:20px;margin:20px 0;'>
          <table style='width:100%;border-collapse:collapse;font-size:14px;'>
            <tr><td style='padding:8px;color:#64748b;'>🔖 Mã đặt phòng</td><td style='padding:8px;font-weight:700;color:#1e293b;'>#{$booking_id}</td></tr>
            <tr><td style='padding:8px;color:#64748b;'>🛏️ Loại phòng</td><td style='padding:8px;font-weight:600;'>{$room_name}</td></tr>
            <tr><td style='padding:8px;color:#64748b;'>🔢 Số phòng</td><td style='padding:8px;'>" . esc_html($rooms ?: '—') . "</td></tr>
            <tr><td style='padding:8px;color:#64748b;'>📅 Nhận phòng</td><td style='padding:8px;font-weight:600;'>{$ci_fmt}</td></tr>
            <tr><td style='padding:8px;color:#64748b;'>📅 Trả phòng</td><td style='padding:8px;font-weight:600;'>{$co_fmt}</td></tr>
          </table>
        </div>
        <p style='color:#64748b;font-size:13px;'>Nếu có thắc mắc, vui lòng liên hệ hotline: <strong>" . get_theme_mod('bookingroom_hotline', '0123 456 789') . "</strong></p>
      </div>
      <div style='background:#f1f5f9;padding:16px;text-align:center;font-size:12px;color:#94a3b8;'>
        © " . date('Y') . " {$site}. Cảm ơn bạn đã chọn chúng tôi!
      </div>
    </div>";

    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site . ' <' . get_bloginfo('admin_email') . '>');
    return wp_mail($email, $subject, $body, $headers);
}

// ============================================================
// 4. LƯU _NOTE VÀ _TOTAL_PRICE KHI SUBMIT BOOKING AJAX
// ============================================================
// Hook vào action process_booking đã có, thêm lưu note + total
add_filter('bookingroom_after_booking_save', 'br_save_extra_booking_fields', 10, 2);

// ============================================================
// 5. TRANG ADMIN "KHÁCH HÀNG" VỚI DASHBOARD + EXPORT CSV
// ============================================================

add_action('admin_menu', 'br_register_customer_page');
function br_register_customer_page() {
    add_submenu_page(
        'edit.php?post_type=booking',
        'Báo cáo & Xuất dữ liệu',
        '📊 Báo cáo',
        'manage_options',
        'br-customer-report',
        'br_customer_report_page'
    );
}

function br_customer_report_page() {
    // Xử lý export CSV
    if (isset($_GET['export']) && $_GET['export'] === 'csv' && current_user_can('manage_options')) {
        br_export_csv();
        return;
    }

    // Thống kê
    $all_bookings = get_posts(array('post_type' => 'booking', 'posts_per_page' => -1, 'post_status' => 'publish'));
    $total = count($all_bookings);
    $stats = array('pending' => 0, 'confirmed' => 0, 'checked_in' => 0, 'checked_out' => 0, 'cancelled' => 0);
    $revenue = 0;
    foreach ($all_bookings as $b) {
        $st = get_post_meta($b->ID, '_status', true) ?: 'pending';
        if (isset($stats[$st])) $stats[$st]++;
        $price = get_post_meta($b->ID, '_total_price', true);
        if ($price && $st !== 'cancelled') $revenue += floatval($price);
    }

    // Khách hàng gần đây (20 booking mới nhất)
    $recent = get_posts(array('post_type' => 'booking', 'posts_per_page' => 20, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC'));

    ?>
    <div class="wrap" style="font-family: -apple-system, sans-serif;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
            📊 Báo cáo & Quản lý Khách hàng
            <a href="<?php echo admin_url('admin-post.php?action=br_export_csv&_wpnonce=' . wp_create_nonce('br_export_csv')); ?>"
               style="margin-left:auto;background:#10b981;color:#fff;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                ⬇️ Xuất Excel/CSV
            </a>
        </h1>

        <!-- Stats Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:28px;">
            <?php
            $card_data = array(
                array('label' => 'Tổng đặt phòng', 'value' => $total, 'color' => '#2563eb', 'bg' => '#dbeafe', 'icon' => '📋'),
                array('label' => 'Chờ xác nhận',   'value' => $stats['pending'],   'color' => '#f59e0b', 'bg' => '#fef3c7', 'icon' => '⏳'),
                array('label' => 'Đã xác nhận',    'value' => $stats['confirmed'], 'color' => '#10b981', 'bg' => '#d1fae5', 'icon' => '✅'),
                array('label' => 'Đã nhận phòng',  'value' => $stats['checked_in'],'color' => '#3b82f6', 'bg' => '#dbeafe', 'icon' => '🏨'),
                array('label' => 'Đã trả phòng',   'value' => $stats['checked_out'],'color'=> '#8b5cf6', 'bg' => '#ede9fe', 'icon' => '🚪'),
                array('label' => 'Đã huỷ',         'value' => $stats['cancelled'], 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => '❌'),
                array('label' => 'Doanh thu (est.)', 'value' => number_format($revenue) . 'đ', 'color' => '#f97316', 'bg' => '#ffedd5', 'icon' => '💰'),
            );
            foreach ($card_data as $c): ?>
                <div style="background:<?php echo $c['bg']; ?>;border-radius:12px;padding:18px 20px;">
                    <div style="font-size:24px;"><?php echo $c['icon']; ?></div>
                    <div style="font-size:24px;font-weight:800;color:<?php echo $c['color']; ?>;margin:6px 0;"><?php echo $c['value']; ?></div>
                    <div style="font-size:12px;color:#64748b;font-weight:600;"><?php echo $c['label']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Recent Bookings Table -->
        <div style="background:#fff;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.08);overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                <h2 style="margin:0;font-size:16px;font-weight:700;">📋 20 Đặt phòng gần nhất</h2>
                <a href="<?php echo admin_url('edit.php?post_type=booking'); ?>" style="color:#2563eb;font-size:13px;font-weight:600;">Xem tất cả →</a>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Mã ĐP</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Khách hàng</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">SĐT / Email</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Phòng</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Check-in</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Check-out</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Tổng tiền</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#64748b;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $status_map = array(
                    'pending'    => array('label' => 'Chờ XN',       'color' => '#f59e0b', 'bg' => '#fef3c7'),
                    'confirmed'  => array('label' => 'Đã XN',        'color' => '#10b981', 'bg' => '#d1fae5'),
                    'checked_in' => array('label' => 'Nhận phòng',   'color' => '#3b82f6', 'bg' => '#dbeafe'),
                    'checked_out'=> array('label' => 'Trả phòng',    'color' => '#8b5cf6', 'bg' => '#ede9fe'),
                    'cancelled'  => array('label' => 'Đã huỷ',       'color' => '#ef4444', 'bg' => '#fee2e2'),
                );
                foreach ($recent as $i => $b):
                    $bid     = $b->ID;
                    $name    = get_post_meta($bid, '_customer_name', true) ?: '—';
                    $phone   = get_post_meta($bid, '_phone', true) ?: '—';
                    $email   = get_post_meta($bid, '_email', true) ?: '—';
                    $room_id = get_post_meta($bid, '_room_id', true);
                    $ci      = get_post_meta($bid, '_check_in', true);
                    $co      = get_post_meta($bid, '_check_out', true);
                    $price   = get_post_meta($bid, '_total_price', true);
                    $status  = get_post_meta($bid, '_status', true) ?: 'pending';
                    $st      = $status_map[$status] ?? $status_map['pending'];
                    $row_bg  = $i % 2 === 0 ? '#fff' : '#f8fafc';
                ?>
                    <tr style="background:<?php echo $row_bg; ?>;border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px;"><a href="<?php echo get_edit_post_link($bid); ?>" style="font-weight:700;color:#2563eb;">#<?php echo $bid; ?></a></td>
                        <td style="padding:12px 16px;font-weight:600;"><?php echo esc_html($name); ?></td>
                        <td style="padding:12px 16px;">
                            <div><?php echo esc_html($phone); ?></div>
                            <div style="color:#94a3b8;font-size:11px;"><?php echo esc_html($email); ?></div>
                        </td>
                        <td style="padding:12px 16px;"><?php echo $room_id ? esc_html(get_the_title($room_id)) : '—'; ?></td>
                        <td style="padding:12px 16px;"><?php echo $ci ? date_i18n('d/m/Y', strtotime($ci)) : '—'; ?></td>
                        <td style="padding:12px 16px;"><?php echo $co ? date_i18n('d/m/Y', strtotime($co)) : '—'; ?></td>
                        <td style="padding:12px 16px;font-weight:700;color:#f97316;"><?php echo $price ? number_format($price) . 'đ' : '—'; ?></td>
                        <td style="padding:12px 16px;">
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;color:<?php echo $st['color']; ?>;background:<?php echo $st['bg']; ?>;"><?php echo $st['label']; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// ============================================================
// 6. EXPORT CSV
// ============================================================

add_action('admin_post_br_export_csv', 'br_export_csv_handler');
function br_export_csv_handler() {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'br_export_csv')) {
        wp_die('Không có quyền.');
    }
    br_export_csv();
}

function br_export_csv() {
    $bookings = get_posts(array('post_type' => 'booking', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC'));

    $filename = 'booking-khachhang-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // BOM for Excel UTF-8
    fputs($out, "\xEF\xBB\xBF");

    fputcsv($out, array('Mã ĐP', 'Họ tên', 'Số ĐT', 'Email', 'Loại phòng', 'Số phòng', 'Nhận phòng', 'Trả phòng', 'Số đêm', 'Tổng tiền (đ)', 'Trạng thái', 'Ghi chú', 'Ngày đặt'));

    $status_labels = array(
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'checked_in' => 'Đã nhận phòng',
        'checked_out'=> 'Đã trả phòng',
        'cancelled'  => 'Đã huỷ',
    );

    foreach ($bookings as $b) {
        $bid     = $b->ID;
        $name    = get_post_meta($bid, '_customer_name', true);
        $phone   = get_post_meta($bid, '_phone', true);
        $email   = get_post_meta($bid, '_email', true);
        $room_id = get_post_meta($bid, '_room_id', true);
        $ci      = get_post_meta($bid, '_check_in', true);
        $co      = get_post_meta($bid, '_check_out', true);
        $rooms   = get_post_meta($bid, '_selected_rooms', true);
        $price   = get_post_meta($bid, '_total_price', true);
        $status  = get_post_meta($bid, '_status', true) ?: 'pending';
        $note    = get_post_meta($bid, '_note', true);

        $nights_count = 0;
        if ($ci && $co) {
            $d1 = new DateTime($ci);
            $d2 = new DateTime($co);
            $nights_count = max(0, (int)$d2->diff($d1)->days);
        }

        fputcsv($out, array(
            '#' . $bid,
            $name,
            $phone,
            $email,
            $room_id ? get_the_title($room_id) : '',
            $rooms,
            $ci ? date('d/m/Y', strtotime($ci)) : '',
            $co ? date('d/m/Y', strtotime($co)) : '',
            $nights_count,
            $price ?: '',
            $status_labels[$status] ?? $status,
            $note,
            date_i18n('d/m/Y H:i', strtotime($b->post_date)),
        ));
    }

    fclose($out);
    exit;
}

// ============================================================
// 7. LƯU _NOTE VÀ _TOTAL_PRICE TRONG AJAX HANDLER (override)
// ============================================================
// Ghi đè hàm process_booking để lưu thêm note và total_price
remove_action('wp_ajax_process_booking', 'bookingroom_process_booking');
remove_action('wp_ajax_nopriv_process_booking', 'bookingroom_process_booking');

add_action('wp_ajax_process_booking', 'br_process_booking_enhanced');
add_action('wp_ajax_nopriv_process_booking', 'br_process_booking_enhanced');

function br_process_booking_enhanced() {
    check_ajax_referer('booking_nonce', 'nonce');

    $room_id       = intval($_POST['room_id']       ?? 0);
    $check_in      = sanitize_text_field($_POST['check_in']      ?? '');
    $check_out     = sanitize_text_field($_POST['check_out']     ?? '');
    $guests        = intval($_POST['guests']        ?? 1);
    $name          = sanitize_text_field($_POST['name']          ?? '');
    $phone         = sanitize_text_field($_POST['phone']         ?? '');
    $email         = sanitize_email($_POST['email']             ?? '');
    $note          = sanitize_textarea_field($_POST['note']      ?? '');
    $selected_rooms= sanitize_text_field($_POST['selected_rooms']?? '');
    $user_id       = get_current_user_id();

    if (!$room_id || !$check_in || !$check_out || !$phone || !$name) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ thông tin bắt buộc (Tên, SĐT, ngày nhận/trả phòng).'));
    }

    // Tính tổng tiền
    $base_price   = floatval(get_post_meta($room_id, '_price', true) ?: 0);
    $d1           = new DateTime($check_in);
    $d2           = new DateTime($check_out);
    $nights_n     = max(1, (int)$d2->diff($d1)->days);
    $rooms_n      = $selected_rooms ? count(array_filter(array_map('trim', explode(',', $selected_rooms)))) : 1;
    $subtotal     = $base_price * $nights_n * $rooms_n;
    $total_price  = round($subtotal * 1.05); // +5% phí

    // Tạo booking post
    $booking_id = wp_insert_post(array(
        'post_type'   => 'booking',
        'post_status' => 'publish',
        'post_title'  => 'Đặt phòng #' . time() . ' – ' . $name,
        'post_author' => $user_id ?: 1,
    ));

    if (is_wp_error($booking_id)) {
        wp_send_json_error(array('message' => 'Lỗi hệ thống, vui lòng thử lại.'));
    }

    // Lưu tất cả meta
    $metas = array(
        '_room_id'        => $room_id,
        '_check_in'       => $check_in,
        '_check_out'      => $check_out,
        '_guests'         => $guests,
        '_customer_name'  => $name,
        '_phone'          => $phone,
        '_email'          => $email,
        '_note'           => $note,
        '_selected_rooms' => $selected_rooms,
        '_nights'         => $nights_n,
        '_total_price'    => $total_price,
        '_status'         => 'pending',
        '_ip'             => $_SERVER['REMOTE_ADDR'] ?? '',
        '_user_agent'     => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200),
    );
    foreach ($metas as $key => $val) {
        update_post_meta($booking_id, $key, $val);
    }

    // Gửi email thông báo cho ADMIN
    br_notify_admin_new_booking($booking_id, $name, $phone, $email, $room_id, $check_in, $check_out, $selected_rooms, $total_price);

    wp_send_json_success(array(
        'message'    => '🎉 Đặt phòng thành công! Mã đặt phòng: #' . $booking_id . '. Chúng tôi sẽ liên hệ xác nhận sớm nhất.',
        'booking_id' => $booking_id,
    ));
}

// ============================================================
// 8. THÔNG BÁO ADMIN KHI CÓ ĐẶT PHÒNG MỚI
// ============================================================

function br_notify_admin_new_booking($bid, $name, $phone, $email, $room_id, $ci, $co, $rooms, $total) {
    $admin_email = get_option('admin_email');
    $site        = get_bloginfo('name');
    $room_name   = $room_id ? get_the_title($room_id) : '—';
    $edit_link   = admin_url('post.php?post=' . $bid . '&action=edit');

    $subject = "🔔 [{$site}] Đặt phòng mới #{$bid} từ {$name}";
    $body    = "
    <div style='font-family:Inter,sans-serif;max-width:560px;margin:auto;'>
      <div style='background:#1e293b;padding:24px;border-radius:12px 12px 0 0;'>
        <h2 style='color:#fff;margin:0;'>🔔 Đặt phòng mới!</h2>
      </div>
      <div style='background:#fff;padding:24px;border:1px solid #e2e8f0;'>
        <table style='width:100%;font-size:14px;border-collapse:collapse;'>
          <tr><td style='padding:8px;color:#64748b;font-weight:600;'>Mã đặt phòng</td><td style='padding:8px;font-weight:700;color:#2563eb;'>#{$bid}</td></tr>
          <tr style='background:#f8fafc;'><td style='padding:8px;color:#64748b;font-weight:600;'>Khách hàng</td><td style='padding:8px;font-weight:600;'>{$name}</td></tr>
          <tr><td style='padding:8px;color:#64748b;font-weight:600;'>Số ĐT</td><td style='padding:8px;'>{$phone}</td></tr>
          <tr style='background:#f8fafc;'><td style='padding:8px;color:#64748b;font-weight:600;'>Email</td><td style='padding:8px;'>{$email}</td></tr>
          <tr><td style='padding:8px;color:#64748b;font-weight:600;'>Loại phòng</td><td style='padding:8px;'>{$room_name}</td></tr>
          <tr style='background:#f8fafc;'><td style='padding:8px;color:#64748b;font-weight:600;'>Số phòng</td><td style='padding:8px;'>" . esc_html($rooms ?: '—') . "</td></tr>
          <tr><td style='padding:8px;color:#64748b;font-weight:600;'>Nhận phòng</td><td style='padding:8px;font-weight:600;'>" . ($ci ? date('d/m/Y', strtotime($ci)) : '—') . "</td></tr>
          <tr style='background:#f8fafc;'><td style='padding:8px;color:#64748b;font-weight:600;'>Trả phòng</td><td style='padding:8px;font-weight:600;'>" . ($co ? date('d/m/Y', strtotime($co)) : '—') . "</td></tr>
          <tr><td style='padding:8px;color:#64748b;font-weight:600;'>Tổng tiền (est.)</td><td style='padding:8px;font-weight:800;color:#f97316;'>" . number_format($total) . "đ</td></tr>
        </table>
        <div style='margin-top:20px;text-align:center;'>
          <a href='{$edit_link}' style='background:#2563eb;color:#fff;padding:12px 28px;border-radius:8px;font-weight:700;text-decoration:none;display:inline-block;'>Xem & Xác nhận ngay →</a>
        </div>
      </div>
    </div>";

    $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$site} <{$admin_email}>");
    wp_mail($admin_email, $subject, $body, $headers);
}

// ============================================================
// 9. META BOX CHI TIẾT BOOKING TRONG ADMIN EDIT
// ============================================================

add_action('add_meta_boxes', 'br_add_booking_detail_metabox');
function br_add_booking_detail_metabox() {
    add_meta_box('br_booking_detail', '📋 Chi tiết Đặt phòng & Khách hàng', 'br_booking_detail_cb', 'booking', 'normal', 'high');
    add_meta_box('br_booking_actions', '⚡ Hành động nhanh', 'br_booking_actions_cb', 'booking', 'side', 'high');
}

function br_booking_detail_cb($post) {
    $bid     = $post->ID;
    $name    = get_post_meta($bid, '_customer_name', true);
    $phone   = get_post_meta($bid, '_phone', true);
    $email   = get_post_meta($bid, '_email', true);
    $note    = get_post_meta($bid, '_note', true);
    $room_id = get_post_meta($bid, '_room_id', true);
    $ci      = get_post_meta($bid, '_check_in', true);
    $co      = get_post_meta($bid, '_check_out', true);
    $guests  = get_post_meta($bid, '_guests', true);
    $rooms   = get_post_meta($bid, '_selected_rooms', true);
    $nights  = get_post_meta($bid, '_nights', true);
    $total   = get_post_meta($bid, '_total_price', true);
    $ip      = get_post_meta($bid, '_ip', true);
    $status  = get_post_meta($bid, '_status', true) ?: 'pending';

    wp_nonce_field('br_save_booking_detail', 'br_booking_nonce');
    ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;padding:4px 0;">
        <div>
            <h3 style="margin:0 0 12px;color:#1e293b;border-bottom:2px solid #f1f5f9;padding-bottom:8px;">👤 Thông tin khách hàng</h3>
            <table style="width:100%;font-size:13px;">
                <tr><td style="padding:6px 0;color:#64748b;width:120px;">Họ tên</td><td><input type="text" name="br_customer_name" value="<?php echo esc_attr($name); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Số điện thoại</td><td><input type="text" name="br_phone" value="<?php echo esc_attr($phone); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Email</td><td><input type="email" name="br_email" value="<?php echo esc_attr($email); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Ghi chú</td><td><textarea name="br_note" class="widefat" rows="3" style="border-radius:6px;"><?php echo esc_textarea($note); ?></textarea></td></tr>
                <?php if ($ip): ?><tr><td style="padding:6px 0;color:#94a3b8;font-size:11px;">IP</td><td style="color:#94a3b8;font-size:11px;"><?php echo esc_html($ip); ?></td></tr><?php endif; ?>
            </table>
        </div>
        <div>
            <h3 style="margin:0 0 12px;color:#1e293b;border-bottom:2px solid #f1f5f9;padding-bottom:8px;">🛏️ Thông tin đặt phòng</h3>
            <table style="width:100%;font-size:13px;">
                <tr><td style="padding:6px 0;color:#64748b;width:120px;">Loại phòng</td><td><?php echo $room_id ? '<a href="' . get_edit_post_link($room_id) . '">' . esc_html(get_the_title($room_id)) . '</a>' : '—'; ?></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Số phòng</td><td><input type="text" name="br_selected_rooms" value="<?php echo esc_attr($rooms); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Nhận phòng</td><td><input type="date" name="br_check_in" value="<?php echo esc_attr($ci); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Trả phòng</td><td><input type="date" name="br_check_out" value="<?php echo esc_attr($co); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Số đêm</td><td><?php echo esc_html($nights ?: '—'); ?></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Số khách</td><td><?php echo esc_html($guests ?: '—'); ?></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Tổng tiền</td><td><input type="number" name="br_total_price" value="<?php echo esc_attr($total); ?>" class="widefat" style="border-radius:6px;"></td></tr>
                <tr><td style="padding:6px 0;color:#64748b;">Trạng thái</td><td>
                    <select name="br_status" class="widefat" style="border-radius:6px;">
                        <?php foreach (array('pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','checked_in'=>'Đã nhận phòng','checked_out'=>'Đã trả phòng','cancelled'=>'Đã huỷ') as $k=>$v): ?>
                            <option value="<?php echo $k; ?>" <?php selected($status, $k); ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
            </table>
        </div>
    </div>
    <?php
}

function br_booking_actions_cb($post) {
    $bid   = $post->ID;
    $email = get_post_meta($bid, '_email', true);
    $name  = get_post_meta($bid, '_customer_name', true);
    ?>
    <div style="font-size:13px;">
        <p style="margin:0 0 10px;color:#64748b;">Gửi email xác nhận cho khách hàng:</p>
        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=br_send_confirm_email&booking_id=' . $bid), 'br_send_email_' . $bid); ?>"
           onclick="return confirm('Gửi email xác nhận đến <?php echo esc_js($email); ?>?')"
           class="button button-primary" style="width:100%;text-align:center;margin-bottom:8px;">
            📧 Gửi email xác nhận
        </a>
        <hr>
        <p style="color:#94a3b8;font-size:11px;margin:8px 0 0;">Khách: <?php echo esc_html($name); ?><br>Email: <?php echo esc_html($email); ?></p>
    </div>
    <?php
}

// Xử lý gửi email từ admin
add_action('admin_post_br_send_confirm_email', function() {
    $bid = intval($_GET['booking_id'] ?? 0);
    if (!$bid || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'br_send_email_' . $bid) || !current_user_can('edit_posts')) wp_die('Không có quyền.');
    $sent = br_send_confirmation_email($bid);
    $msg  = $sent ? 'br_email_sent' : 'br_email_fail';
    wp_safe_redirect(add_query_arg($msg, 1, get_edit_post_link($bid, 'url')));
    exit;
});

// Lưu meta khi save post
add_action('save_post_booking', 'br_save_booking_detail_meta');
function br_save_booking_detail_meta($post_id) {
    if (!isset($_POST['br_booking_nonce']) || !wp_verify_nonce($_POST['br_booking_nonce'], 'br_save_booking_detail')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array(
        'br_customer_name'  => '_customer_name',
        'br_phone'          => '_phone',
        'br_email'          => '_email',
        'br_note'           => '_note',
        'br_selected_rooms' => '_selected_rooms',
        'br_check_in'       => '_check_in',
        'br_check_out'      => '_check_out',
        'br_total_price'    => '_total_price',
        'br_status'         => '_status',
    );
    foreach ($fields as $post_key => $meta_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }
    // Tính lại số đêm nếu cả 2 ngày đều có
    $ci = sanitize_text_field($_POST['br_check_in'] ?? '');
    $co = sanitize_text_field($_POST['br_check_out'] ?? '');
    if ($ci && $co) {
        $d1 = new DateTime($ci);
        $d2 = new DateTime($co);
        $n  = max(0, (int)$d2->diff($d1)->days);
        update_post_meta($post_id, '_nights', $n);
    }
    // Gửi email nếu confirm
    $new_status = sanitize_key($_POST['br_status'] ?? '');
    if ($new_status === 'confirmed') {
        br_send_confirmation_email($post_id);
    }
}

// ============================================================
// 10. ADMIN NOTICE KHI ĐỔI TRẠNG THÁI
// ============================================================
add_action('admin_notices', function() {
    if (isset($_GET['br_updated'])) {
        $labels = array('confirmed'=>'Đã xác nhận','pending'=>'Về chờ xác nhận','cancelled'=>'Đã huỷ','checked_in'=>'Đã nhận phòng','checked_out'=>'Đã trả phòng');
        $label = $labels[$_GET['br_updated']] ?? $_GET['br_updated'];
        echo '<div class="notice notice-success is-dismissible"><p>✅ Đã đổi trạng thái đặt phòng → <strong>' . esc_html($label) . '</strong></p></div>';
    }
    if (isset($_GET['br_email_sent'])) {
        echo '<div class="notice notice-success is-dismissible"><p>📧 Đã gửi email xác nhận cho khách hàng thành công!</p></div>';
    }
    if (isset($_GET['br_email_fail'])) {
        echo '<div class="notice notice-error is-dismissible"><p>❌ Gửi email thất bại. Kiểm tra cấu hình SMTP.</p></div>';
    }
});
