<?php
/**
 * Room Admin Columns & Dashboard Widgets
 * Tổng số phòng theo từng loại trong WordPress Admin (hỗ trợ cả 'room' và 'hb_room'):
 *   1. Cột "Số phòng" trong danh sách phòng (edit.php)
 *   2. Thanh tổng quan (admin notice bar) trên đầu trang danh sách phòng
 *   3. Dashboard Widget – Tổng hợp tất cả loại phòng
 *   4. Sub-menu "📊 Tổng quan phòng" trong menu Phòng
 *
 * @package BookingRoom
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ═══════════════════════════════════════════════════════════════════════
// 1. CỘT ADMIN: SỐ PHÒNG + TRẠNG THÁI HÔM NAY (Cho 'room' và 'hb_room')
// ═══════════════════════════════════════════════════════════════════════

add_filter( 'manage_room_posts_columns', 'brrc_add_room_columns' );
add_filter( 'manage_hb_room_posts_columns', 'brrc_add_room_columns' );
function brrc_add_room_columns( $cols ) {
    $new = [];
    foreach ( $cols as $key => $label ) {
        $new[$key] = $label;
        if ( $key === 'title' ) {
            $new['room_type_badge'] = '🏷️ Loại phòng';
            $new['room_quantity']   = '🔢 Tổng phòng';
            $new['room_today']      = '📊 Hôm nay';
            $new['room_price_col']  = '💰 Giá/đêm';
        }
    }
    return $new;
}

add_action( 'manage_room_posts_custom_column', 'brrc_render_room_column', 10, 2 );
add_action( 'manage_hb_room_posts_custom_column', 'brrc_render_room_column', 10, 2 );
function brrc_render_room_column( $col, $post_id ) {
    switch ( $col ) {
        case 'room_type_badge':
            $taxonomies = get_object_taxonomies( get_post_type($post_id) );
            $term_names = [];
            foreach ( $taxonomies as $tax ) {
                $terms = get_the_terms( $post_id, $tax );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $t ) {
                        $term_names[] = $t->name;
                    }
                }
            }
            if ( ! empty( $term_names ) ) {
                foreach ( array_unique($term_names) as $name ) {
                    echo '<span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;'
                        . 'background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;margin:2px 2px 2px 0;">'
                        . esc_html( $name ) . '</span>';
                }
            } else {
                echo '<span style="color:#94a3b8;font-size:12px;">—</span>';
            }
            break;

        case 'room_quantity':
            $qty     = brrc_get_total_units( $post_id );
            $numbers = get_post_meta( $post_id, '_room_numbers', true );
            $nums_arr = $numbers
                ? array_filter( array_map( 'trim', explode( ',', $numbers ) ) )
                : [];

            echo '<div style="text-align:center;">';
            echo '<span style="display:inline-block;font-size:22px;font-weight:900;color:#1e293b;line-height:1;">' . $qty . '</span>';
            echo '<div style="font-size:10px;color:#94a3b8;margin-top:2px;">phòng</div>';
            if ( ! empty( $nums_arr ) ) {
                echo '<div style="font-size:10px;color:#64748b;margin-top:3px;">';
                $preview = array_slice( $nums_arr, 0, 4 );
                echo esc_html( implode( ', ', $preview ) );
                if ( count( $nums_arr ) > 4 ) echo ' <em>+' . ( count( $nums_arr ) - 4 ) . '</em>';
                echo '</div>';
            }
            echo '</div>';
            break;

        case 'room_today':
            $total    = brrc_get_total_units( $post_id );
            $occupied = brrc_count_occupied_today( $post_id );
            $free     = max( 0, $total - $occupied );

            $free_pct   = $total > 0 ? round( $free / $total * 100 ) : 0;
            $bar_color  = $free === 0 ? '#ef4444' : ( $free < $total * 0.3 ? '#f59e0b' : '#10b981' );
            $status_txt = $free === 0 ? 'Hết phòng' : "{$free}/{$total} trống";
            $status_clr = $free === 0 ? '#dc2626' : ( $free < $total * 0.3 ? '#92400e' : '#065f46' );
            $status_bg  = $free === 0 ? '#fee2e2' : ( $free < $total * 0.3 ? '#fef3c7' : '#dcfce7' );

            echo '<div style="text-align:center;min-width:80px;">';
            echo '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;'
                . "background:{$status_bg};color:{$status_clr};" . '">' . $status_txt . '</span>';

            echo '<div style="margin-top:5px;background:#f1f5f9;border-radius:99px;height:5px;overflow:hidden;">';
            echo '<div style="width:' . $free_pct . '%;height:100%;background:' . $bar_color . ';border-radius:99px;transition:width .3s;"></div>';
            echo '</div>';
            echo '</div>';
            break;

        case 'room_price_col':
            $price = brrc_get_room_price( $post_id );
            if ( $price > 0 ) {
                echo '<div style="text-align:right;">';
                echo '<span style="font-weight:800;color:#d35400;font-size:13px;">' . number_format( $price ) . 'đ</span>';
                echo '</div>';
            } else {
                echo '<span style="color:#94a3b8;">—</span>';
            }
            break;
    }
}

add_filter( 'manage_edit-room_sortable_columns', 'brrc_sortable_columns' );
add_filter( 'manage_edit-hb_room_sortable_columns', 'brrc_sortable_columns' );
function brrc_sortable_columns( $cols ) {
    $cols['room_quantity'] = 'room_quantity';
    $cols['room_price_col'] = 'room_price_col';
    return $cols;
}

add_action( 'admin_head', function() {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->id, ['edit-room', 'edit-hb_room'] ) ) return;
    ?>
    <style>
    .column-room_type_badge { width: 130px; }
    .column-room_quantity   { width: 120px; text-align: center !important; }
    .column-room_today      { width: 130px; text-align: center !important; }
    .column-room_price_col  { width: 110px; text-align: right !important; }
    .column-room_quantity .column-room_today { vertical-align: middle; }
    .column-taxonomy-room_category, .column-taxonomy-hb_room_type { display: none !important; }
    
    #brrc-summary-bar {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 14px 20px; margin: 16px 0 20px;
        display: flex; flex-wrap: wrap; gap: 14px; align-items: center;
    }
    .brrc-sum-card {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px; border-radius: 10px;
        background: #f8fafc; border: 1px solid #f1f5f9;
        min-width: 140px; flex: 1;
    }
    .brrc-sum-icon { font-size: 22px; line-height: 1; }
    .brrc-sum-value { font-size: 22px; font-weight: 900; line-height: 1; color: #1e293b; }
    .brrc-sum-label { font-size: 11px; color: #94a3b8; margin-top: 2px; font-weight: 600; }
    </style>
    <?php
} );

add_action( 'admin_notices', 'brrc_room_list_summary_bar' );
function brrc_room_list_summary_bar() {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->id, ['edit-room', 'edit-hb_room'] ) ) return;
    $post_type = $screen->id === 'edit-hb_room' ? 'hb_room' : 'room';

    $all_rooms = get_posts([
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $grand_total = 0; $grand_free = 0; $grand_occupied = 0;

    foreach ( $all_rooms as $rid ) {
        $units    = brrc_get_total_units( $rid );
        $occupied = brrc_count_occupied_today( $rid );
        $grand_total    += $units;
        $grand_occupied += $occupied;
        $grand_free     += max( 0, $units - $occupied );
    }

    $occ_pct = $grand_total > 0 ? round( $grand_occupied / $grand_total * 100 ) : 0;
    ?>
    <div id="brrc-summary-bar">
        <div style="font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.06em;min-width:120px;">
            📊 Tổng quan hôm nay<br>
            <span style="font-size:10px;font-weight:500;color:#94a3b8;"><?php echo date_i18n('d/m/Y'); ?></span>
        </div>
        <div class="brrc-sum-card">
            <div class="brrc-sum-icon">🏠</div>
            <div>
                <div class="brrc-sum-value"><?php echo $grand_total; ?></div>
                <div class="brrc-sum-label">Tổng phòng vật lý</div>
            </div>
        </div>
        <div class="brrc-sum-card" style="border-color:#bbf7d0;background:#f0fdf4;">
            <div class="brrc-sum-icon">✅</div>
            <div>
                <div class="brrc-sum-value" style="color:#16a34a;"><?php echo $grand_free; ?></div>
                <div class="brrc-sum-label">Phòng trống hôm nay</div>
            </div>
        </div>
        <div class="brrc-sum-card" style="border-color:#fecaca;background:#fef2f2;">
            <div class="brrc-sum-icon">🛌</div>
            <div>
                <div class="brrc-sum-value" style="color:#dc2626;"><?php echo $grand_occupied; ?></div>
                <div class="brrc-sum-label">Đang có khách</div>
            </div>
        </div>
        <div class="brrc-sum-card" style="border-color:#bfdbfe;background:#eff6ff;">
            <div class="brrc-sum-icon">📈</div>
            <div>
                <div class="brrc-sum-value" style="color:#2563eb;"><?php echo $occ_pct; ?>%</div>
                <div class="brrc-sum-label">Tỷ lệ lấp đầy</div>
            </div>
        </div>
        <div style="margin-left:auto;display:flex;flex-direction:column;gap:6px;">
            <a href="<?php echo admin_url('admin.php?page=brrc-room-overview'); ?>"
               style="display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#475569;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid #e2e8f0;">
                📊 Báo cáo phòng
            </a>
        </div>
    </div>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════
// 2. TRANG TỔNG QUAN PHÒNG (Sub-menu)
// ═══════════════════════════════════════════════════════════════════════

add_action( 'admin_menu', 'brrc_register_overview_page' );
function brrc_register_overview_page() {
    add_submenu_page(
        'edit.php?post_type=room',
        'Báo cáo phòng',
        '📊 Báo cáo phòng',
        'manage_options',
        'brrc-room-overview',
        'brrc_overview_page_render'
    );
    // Add same page to hb_room if it exists
    add_submenu_page(
        'edit.php?post_type=hb_room',
        'Báo cáo phòng',
        '📊 Báo cáo phòng',
        'manage_options',
        'brrc-room-overview',
        'brrc_overview_page_render'
    );
}

function brrc_overview_page_render() {
    $today = date( 'Y-m-d' );
    $all_rooms = get_posts([
        'post_type'      => ['room', 'hb_room'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $grand = [ 'total' => 0, 'free' => 0, 'occupied' => 0, 'revenue_est' => 0 ];
    $rows  = [];

    foreach ( $all_rooms as $room ) {
        $rid   = $room->ID;
        $units = brrc_get_total_units( $rid );
        $occ   = brrc_count_occupied_today( $rid );
        $free  = max( 0, $units - $occ );
        $price = brrc_get_room_price( $rid );
        
        $taxonomies = get_object_taxonomies( get_post_type($rid) );
        $type_name = '—';
        foreach ( $taxonomies as $tax ) {
            $terms = get_the_terms( $rid, $tax );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $type_name = implode(', ', array_column($terms, 'name'));
                break;
            }
        }

        $rev_today = $occ * $price;
        $grand['total']       += $units;
        $grand['free']        += $free;
        $grand['occupied']    += $occ;
        $grand['revenue_est'] += $rev_today;

        $rows[] = [
            'id'        => $rid,
            'title'     => $room->post_title,
            'type'      => $type_name,
            'units'     => $units,
            'free'      => $free,
            'occupied'  => $occ,
            'price'     => $price,
            'thumb'     => get_the_post_thumbnail_url( $rid, 'thumbnail' ) ?: '',
        ];
    }
    $occ_pct = $grand['total'] > 0 ? round( $grand['occupied'] / $grand['total'] * 100 ) : 0;
    ?>
    <div class="wrap" id="brrc-overview-page" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;max-width:1200px;">
        <h1 style="font-size:22px;font-weight:800;margin-bottom:20px;">📊 Báo cáo số lượng phòng tổng hợp</h1>
        <div style="display:flex;gap:16px;margin-bottom:24px;">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;flex:1;text-align:center;">
                <div style="font-size:36px;font-weight:900;color:#1e293b;"><?php echo $grand['total']; ?></div>
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;">Tổng phòng vật lý</div>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;flex:1;text-align:center;">
                <div style="font-size:36px;font-weight:900;color:#16a34a;"><?php echo $grand['free']; ?></div>
                <div style="font-size:12px;font-weight:700;color:#16a34a;text-transform:uppercase;">Trống hôm nay</div>
            </div>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:20px;flex:1;text-align:center;">
                <div style="font-size:36px;font-weight:900;color:#dc2626;"><?php echo $grand['occupied']; ?></div>
                <div style="font-size:12px;font-weight:700;color:#dc2626;text-transform:uppercase;">Đang có khách</div>
            </div>
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;flex:1;text-align:center;">
                <div style="font-size:36px;font-weight:900;color:#2563eb;"><?php echo $occ_pct; ?>%</div>
                <div style="font-size:12px;font-weight:700;color:#2563eb;text-transform:uppercase;">Tỷ lệ lấp đầy</div>
            </div>
        </div>
        
        <table style="width:100%;background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-collapse:collapse;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="padding:16px;text-align:left;font-size:13px;">Loại phòng</th>
                    <th style="padding:16px;text-align:center;font-size:13px;">Tổng SL</th>
                    <th style="padding:16px;text-align:center;font-size:13px;">Tình trạng hôm nay</th>
                    <th style="padding:16px;text-align:right;font-size:13px;">Giá/đêm</th>
                    <th style="padding:16px;text-align:center;font-size:13px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): $free_pct_r = $r['units'] > 0 ? ($r['free']/$r['units']*100) : 0; ?>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px;">
                        <a href="<?php echo get_edit_post_link($r['id']); ?>" style="font-weight:700;color:#1e293b;font-size:14px;text-decoration:none;"><?php echo esc_html($r['title']); ?></a>
                        <div style="color:#64748b;font-size:12px;margin-top:4px;"><?php echo esc_html($r['type']); ?></div>
                    </td>
                    <td style="padding:16px;text-align:center;font-size:20px;font-weight:800;color:#1e293b;"><?php echo $r['units']; ?></td>
                    <td style="padding:16px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;font-weight:700;">
                            <span style="color:#16a34a;"><?php echo $r['free']; ?> trống</span>
                            <span style="color:#dc2626;"><?php echo $r['occupied']; ?> khách</span>
                        </div>
                        <div style="background:#f1f5f9;border-radius:99px;height:6px;overflow:hidden;width:100%;">
                            <div style="width:<?php echo $free_pct_r; ?>%;background:#10b981;height:100%;border-radius:99px;"></div>
                        </div>
                    </td>
                    <td style="padding:16px;text-align:right;font-weight:700;color:#d35400;">
                        <?php echo number_format($r['price']); ?>đ
                    </td>
                    <td style="padding:16px;text-align:center;">
                        <a href="<?php echo get_edit_post_link($r['id']); ?>" class="button button-small">Chỉnh sửa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════
// 3. DASHBOARD WIDGET
// ═══════════════════════════════════════════════════════════════════════

add_action( 'wp_dashboard_setup', 'brrc_register_dashboard_widget' );
function brrc_register_dashboard_widget() {
    wp_add_dashboard_widget('brrc_room_overview_widget', '🏨 Tổng quan phòng hôm nay', 'brrc_dashboard_widget_render');
}

function brrc_dashboard_widget_render() {
    $rooms = get_posts([
        'post_type'      => ['room', 'hb_room'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    if ( empty( $rooms ) ) {
        echo '<p>Chưa có phòng nào.</p>';
        return;
    }

    $grand_total = 0; $grand_free = 0; $grand_occupied = 0;
    foreach ( $rooms as $rid ) {
        $units = brrc_get_total_units($rid);
        $occ = brrc_count_occupied_today($rid);
        $grand_total += $units;
        $grand_occupied += $occ;
        $grand_free += max(0, $units - $occ);
    }
    ?>
    <div style="display:flex;gap:10px;margin-bottom:15px;text-align:center;">
        <div style="flex:1;background:#f8fafc;padding:10px;border-radius:8px;border:1px solid #e2e8f0;">
            <div style="font-size:20px;font-weight:800;color:#1e293b;"><?php echo $grand_total; ?></div>
            <div style="font-size:11px;color:#64748b;">Tổng SL</div>
        </div>
        <div style="flex:1;background:#f0fdf4;padding:10px;border-radius:8px;border:1px solid #bbf7d0;">
            <div style="font-size:20px;font-weight:800;color:#16a34a;"><?php echo $grand_free; ?></div>
            <div style="font-size:11px;color:#16a34a;">Trống</div>
        </div>
        <div style="flex:1;background:#fef2f2;padding:10px;border-radius:8px;border:1px solid #fecaca;">
            <div style="font-size:20px;font-weight:800;color:#dc2626;"><?php echo $grand_occupied; ?></div>
            <div style="font-size:11px;color:#dc2626;">Đang ở</div>
        </div>
    </div>
    <a href="<?php echo admin_url('admin.php?page=brrc-room-overview'); ?>" class="button button-primary" style="width:100%;text-align:center;">Xem chi tiết báo cáo →</a>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS
// ═══════════════════════════════════════════════════════════════════════

function brrc_get_total_units( $post_id ) {
    $pt = get_post_type($post_id);
    if ($pt === 'hb_room') {
        $c = get_post_meta($post_id, '_hb_room_count', true) ?: get_post_meta($post_id, '_tp_room_count', true);
        return $c ? (int)$c : 1;
    }
    
    $numbers = get_post_meta( $post_id, '_room_numbers', true );
    if ( $numbers ) {
        $arr = array_filter( array_map( 'trim', explode( ',', $numbers ) ) );
        if ( count( $arr ) > 0 ) return count( $arr );
    }
    $qty = (int) get_post_meta( $post_id, '_room_quantity', true );
    return $qty > 0 ? $qty : 10;
}

function brrc_count_occupied_today( $post_id ) {
    $today = date( 'Y-m-d' );
    $q = new WP_Query([
        'post_type'      => 'booking', // Giả sử dùng custom 'booking'
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'AND',
            [ 'key' => '_room_id',   'value' => $post_id, 'compare' => '=' ],
            [ 'key' => '_check_in',  'value' => $today,   'compare' => '<=', 'type' => 'DATE' ],
            [ 'key' => '_check_out', 'value' => $today,   'compare' => '>',  'type' => 'DATE' ],
            [ 'key' => '_status',    'value' => ['cancelled'], 'compare' => 'NOT IN' ],
        ],
    ]);
    return $q->found_posts;
}

function brrc_get_room_price( $post_id ) {
    $pt = get_post_type($post_id);
    if ($pt === 'hb_room') {
        return (float) (get_post_meta($post_id, '_hb_room_price', true) ?: get_post_meta($post_id, '_tp_room_price', true) ?: 0);
    }
    return (float) get_post_meta( $post_id, '_price', true );
}
