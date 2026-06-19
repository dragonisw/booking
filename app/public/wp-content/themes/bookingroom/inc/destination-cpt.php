<?php
/**
 * Destination Custom Post Type
 * CPT "destination" – Điểm đến du lịch
 * Tạo & quản lý điểm đến trực tiếp trong WordPress Admin
 */

// ============================================================
// 1. ĐĂNG KÝ CPT "destination"
// ============================================================
add_action('init', 'brd_register_destination_cpt');
function brd_register_destination_cpt() {
    $labels = array(
        'name'               => 'Điểm đến',
        'singular_name'      => 'Điểm đến',
        'add_new'            => 'Thêm điểm đến',
        'add_new_item'       => 'Thêm điểm đến mới',
        'edit_item'          => 'Chỉnh sửa điểm đến',
        'new_item'           => 'Điểm đến mới',
        'view_item'          => 'Xem điểm đến',
        'search_items'       => 'Tìm kiếm điểm đến',
        'not_found'          => 'Không tìm thấy điểm đến',
        'not_found_in_trash' => 'Không có điểm đến trong thùng rác',
        'menu_name'          => 'Điểm đến',
        'all_items'          => 'Tất cả điểm đến',
    );

    register_post_type('destination', array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-location-alt',
        'menu_position' => 6,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'       => array('slug' => 'destination'),
        'show_in_menu'  => true,
    ));
}

// ============================================================
// 2. META BOX CHI TIẾT ĐIỂM ĐẾN
// ============================================================
add_action('add_meta_boxes', 'brd_add_destination_metabox');
function brd_add_destination_metabox() {
    add_meta_box(
        'brd_destination_details',
        '📍 Thông tin Điểm đến',
        'brd_destination_metabox_cb',
        'destination',
        'normal',
        'high'
    );
    add_meta_box(
        'brd_destination_display',
        '🖥️ Tùy chọn hiển thị',
        'brd_destination_display_cb',
        'destination',
        'side',
        'high'
    );
}

function brd_destination_metabox_cb($post) {
    wp_nonce_field('brd_save_destination', 'brd_destination_nonce');

    $hotel_count  = get_post_meta($post->ID, '_dest_hotel_count', true);
    $tagline      = get_post_meta($post->ID, '_dest_tagline', true);
    $country      = get_post_meta($post->ID, '_dest_country', true) ?: 'Việt Nam';
    $region       = get_post_meta($post->ID, '_dest_region', true);
    $highlight    = get_post_meta($post->ID, '_dest_highlight', true);
    $search_link  = get_post_meta($post->ID, '_dest_search_link', true);
    $badge        = get_post_meta($post->ID, '_dest_badge', true);
    $featured     = get_post_meta($post->ID, '_dest_featured', true);
    $sort_order   = get_post_meta($post->ID, '_dest_sort_order', true) ?: 0;
    ?>
    <style>
        .brd-metabox { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 4px 0; }
        .brd-field { display: flex; flex-direction: column; gap: 6px; }
        .brd-field label { font-weight: 600; font-size: 12px; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
        .brd-field input, .brd-field select, .brd-field textarea { border-radius: 8px !important; border: 1.5px solid #e2e8f0 !important; padding: 8px 12px !important; font-size: 13px !important; transition: border-color .2s; }
        .brd-field input:focus, .brd-field select:focus, .brd-field textarea:focus { border-color: #2563eb !important; outline: none !important; box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important; }
        .brd-full { grid-column: 1 / -1; }
        .brd-tip { font-size: 11px; color: #94a3b8; margin-top: 2px; }
    </style>
    <div class="brd-metabox">
        <div class="brd-field">
            <label for="brd_tagline">📝 Slogan / Mô tả ngắn</label>
            <input type="text" id="brd_tagline" name="brd_tagline" value="<?php echo esc_attr($tagline); ?>" placeholder="VD: Thành phố đáng sống nhất Việt Nam">
        </div>
        <div class="brd-field">
            <label for="brd_hotel_count">🏨 Số khách sạn</label>
            <input type="text" id="brd_hotel_count" name="brd_hotel_count" value="<?php echo esc_attr($hotel_count); ?>" placeholder="VD: 124 Khách sạn">
        </div>
        <div class="brd-field">
            <label for="brd_country">🌍 Quốc gia</label>
            <input type="text" id="brd_country" name="brd_country" value="<?php echo esc_attr($country); ?>" placeholder="Việt Nam">
        </div>
        <div class="brd-field">
            <label for="brd_region">📌 Vùng / Miền</label>
            <input type="text" id="brd_region" name="brd_region" value="<?php echo esc_attr($region); ?>" placeholder="VD: Miền Trung">
        </div>
        <div class="brd-field">
            <label for="brd_badge">🏷️ Badge (Nhãn nổi bật)</label>
            <input type="text" id="brd_badge" name="brd_badge" value="<?php echo esc_attr($badge); ?>" placeholder="VD: Hot, Mới, Xu hướng">
            <span class="brd-tip">Hiển thị nhãn nhỏ trên ảnh điểm đến</span>
        </div>
        <div class="brd-field">
            <label for="brd_sort_order">📶 Thứ tự hiển thị</label>
            <input type="number" id="brd_sort_order" name="brd_sort_order" value="<?php echo esc_attr($sort_order); ?>" placeholder="0 = đầu tiên" min="0">
            <span class="brd-tip">Số nhỏ hơn hiển thị trước</span>
        </div>
        <div class="brd-field brd-full">
            <label for="brd_highlight">✨ Điểm nổi bật (cách nhau bằng dấu phẩy)</label>
            <input type="text" id="brd_highlight" name="brd_highlight" value="<?php echo esc_attr($highlight); ?>" placeholder="VD: Bãi biển đẹp, Ẩm thực phong phú, Di sản UNESCO">
            <span class="brd-tip">Hiển thị dưới dạng tags trong trang chi tiết</span>
        </div>
        <div class="brd-field brd-full">
            <label for="brd_search_link">🔗 Link tìm phòng tùy chỉnh (bỏ trống = tự động)</label>
            <input type="url" id="brd_search_link" name="brd_search_link" value="<?php echo esc_attr($search_link); ?>" placeholder="https://...">
            <span class="brd-tip">Nếu để trống sẽ tự link đến trang tìm phòng theo tên điểm đến</span>
        </div>
    </div>
    <?php
}

function brd_destination_display_cb($post) {
    $featured   = get_post_meta($post->ID, '_dest_featured', true);
    $show_home  = get_post_meta($post->ID, '_dest_show_home', true);
    $home_order = get_post_meta($post->ID, '_dest_home_order', true) ?: 99;
    ?>
    <style>
        .brd-toggle { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding: 10px; background: #f8fafc; border-radius: 8px; }
        .brd-toggle label { font-weight: 600; font-size: 13px; color: #1e293b; cursor: pointer; }
        .brd-toggle input[type=checkbox] { width: 18px; height: 18px; cursor: pointer; accent-color: #2563eb; }
    </style>
    <div class="brd-toggle">
        <input type="checkbox" id="brd_featured" name="brd_featured" value="1" <?php checked($featured, '1'); ?>>
        <label for="brd_featured">⭐ Điểm đến nổi bật</label>
    </div>
    <div class="brd-toggle">
        <input type="checkbox" id="brd_show_home" name="brd_show_home" value="1" <?php checked($show_home, '1'); ?>>
        <label for="brd_show_home">🏠 Hiển thị trên trang chủ</label>
    </div>
    <div style="margin-top: 8px;">
        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">📶 Thứ tự trang chủ</label>
        <input type="number" name="brd_home_order" value="<?php echo esc_attr($home_order); ?>" min="1" max="999" style="width:100%;border-radius:6px;border:1.5px solid #e2e8f0;padding:6px 10px;">
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Số nhỏ = hiển thị trước. Mặc định tối đa 8 điểm đến.</p>
    </div>
    <?php
}

// Lưu meta khi save
add_action('save_post_destination', 'brd_save_destination_meta');
function brd_save_destination_meta($post_id) {
    if (!isset($_POST['brd_destination_nonce']) || !wp_verify_nonce($_POST['brd_destination_nonce'], 'brd_save_destination')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = array(
        'brd_tagline'     => '_dest_tagline',
        'brd_hotel_count' => '_dest_hotel_count',
        'brd_country'     => '_dest_country',
        'brd_region'      => '_dest_region',
        'brd_highlight'   => '_dest_highlight',
        'brd_search_link' => '_dest_search_link',
        'brd_badge'       => '_dest_badge',
        'brd_home_order'  => '_dest_home_order',
        'brd_sort_order'  => '_dest_sort_order',
    );
    foreach ($text_fields as $post_key => $meta_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }
    // Checkboxes
    update_post_meta($post_id, '_dest_featured',  isset($_POST['brd_featured'])  ? '1' : '');
    update_post_meta($post_id, '_dest_show_home', isset($_POST['brd_show_home']) ? '1' : '');
}

// ============================================================
// 3. ADMIN COLUMNS
// ============================================================
add_filter('manage_destination_posts_columns', 'brd_dest_columns');
function brd_dest_columns($cols) {
    $new = array();
    $new['cb']          = $cols['cb'];
    $new['thumbnail']   = '🖼️';
    $new['title']       = 'Tên điểm đến';
    $new['region']      = '📌 Vùng';
    $new['hotel_count'] = '🏨 Số KS';
    $new['badge']       = '🏷️ Badge';
    $new['show_home']   = '🏠 Trang chủ';
    $new['featured']    = '⭐ Nổi bật';
    $new['home_order']  = '📶 Thứ tự';
    $new['date']        = 'Ngày tạo';
    return $new;
}

add_action('manage_destination_posts_custom_column', 'brd_dest_column_content', 10, 2);
function brd_dest_column_content($col, $post_id) {
    switch ($col) {
        case 'thumbnail':
            $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
            if ($thumb) {
                echo '<img src="' . esc_url($thumb) . '" style="width:60px;height:50px;object-fit:cover;border-radius:8px;">';
            } else {
                echo '<span style="color:#cbd5e1;font-size:28px;">🗺️</span>';
            }
            break;
        case 'region':
            $r = get_post_meta($post_id, '_dest_region', true);
            $c = get_post_meta($post_id, '_dest_country', true) ?: 'VN';
            echo '<span style="font-size:12px;">' . esc_html($r ?: '—') . '</span>';
            echo '<br><span style="font-size:11px;color:#94a3b8;">' . esc_html($c) . '</span>';
            break;
        case 'hotel_count':
            echo '<strong>' . esc_html(get_post_meta($post_id, '_dest_hotel_count', true) ?: '—') . '</strong>';
            break;
        case 'badge':
            $b = get_post_meta($post_id, '_dest_badge', true);
            if ($b) echo '<span style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">' . esc_html($b) . '</span>';
            else echo '—';
            break;
        case 'show_home':
            $v = get_post_meta($post_id, '_dest_show_home', true);
            echo $v ? '<span style="color:#10b981;font-size:18px;">✓</span>' : '<span style="color:#cbd5e1;">—</span>';
            break;
        case 'featured':
            $v = get_post_meta($post_id, '_dest_featured', true);
            echo $v ? '⭐' : '—';
            break;
        case 'home_order':
            echo esc_html(get_post_meta($post_id, '_dest_home_order', true) ?: 99);
            break;
    }
}

// Sortable columns
add_filter('manage_edit-destination_sortable_columns', function($cols) {
    $cols['home_order'] = 'home_order';
    $cols['featured']   = 'featured';
    return $cols;
});

// Column width
add_action('admin_head', function() {
    global $post_type;
    if ($post_type !== 'destination') return;
    echo '<style>
        .column-thumbnail { width: 70px; }
        .column-hotel_count, .column-badge, .column-show_home, .column-featured, .column-home_order { width: 80px; text-align: center; }
        .column-region { width: 120px; }
    </style>';
});

// ============================================================
// 4. HELPER FUNCTION – LẤY DESTINATIONS CHO FRONTEND
// ============================================================

/**
 * Lấy danh sách điểm đến (dùng cho trang chủ hoặc trang destinations)
 *
 * @param array $args
 * @return WP_Post[]
 */
function brd_get_destinations($args = array()) {
    $defaults = array(
        'limit'     => 8,
        'home_only' => false,   // chỉ lấy điểm đến có "Hiển thị trang chủ"
        'featured'  => false,   // chỉ lấy nổi bật
        'orderby'   => 'meta_value_num',
        'order'     => 'ASC',
        'meta_key'  => '_dest_home_order',
    );
    $args = wp_parse_args($args, $defaults);

    $query_args = array(
        'post_type'      => 'destination',
        'post_status'    => 'publish',
        'posts_per_page' => $args['limit'],
        'orderby'        => $args['orderby'],
        'order'          => $args['order'],
        'meta_key'       => $args['meta_key'],
    );

    $meta_query = array('relation' => 'AND');
    if ($args['home_only']) {
        $meta_query[] = array('key' => '_dest_show_home', 'value' => '1', 'compare' => '=');
    }
    if ($args['featured']) {
        $meta_query[] = array('key' => '_dest_featured', 'value' => '1', 'compare' => '=');
    }
    if (count($meta_query) > 1) {
        $query_args['meta_query'] = $meta_query;
    }

    return get_posts($query_args);
}

// ============================================================
// 5. SHORTCODE [destinations] – Dùng ở bất kỳ trang nào
// ============================================================
add_shortcode('destinations', 'brd_destinations_shortcode');
function brd_destinations_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit'    => 8,
        'featured' => 'no',
        'cols'     => 4,
        'style'    => 'card', // card | overlay
    ), $atts, 'destinations');

    $posts = brd_get_destinations(array(
        'limit'    => intval($atts['limit']),
        'featured' => $atts['featured'] === 'yes',
    ));

    if (empty($posts)) return '';

    $cols_map = array(1 => '100%', 2 => 'repeat(2,1fr)', 3 => 'repeat(3,1fr)', 4 => 'repeat(4,1fr)');
    $grid_cols = $cols_map[intval($atts['cols'])] ?? 'repeat(4,1fr)';

    ob_start();
    echo '<div class="brd-dest-grid" style="display:grid;grid-template-columns:' . $grid_cols . ';gap:20px;">';
    foreach ($posts as $post) {
        echo brd_render_dest_card($post, $atts['style']);
    }
    echo '</div>';
    return ob_get_clean();
}

// ============================================================
// 6. RENDER CARD (dùng chung cho shortcode & trang chủ)
// ============================================================
function brd_render_dest_card($post, $style = 'overlay') {
    $pid          = $post->ID;
    $title        = get_the_title($pid);
    $thumb        = get_the_post_thumbnail_url($pid, 'large');
    $hotel_count  = get_post_meta($pid, '_dest_hotel_count', true) ?: '';
    $tagline      = get_post_meta($pid, '_dest_tagline', true);
    $badge        = get_post_meta($pid, '_dest_badge', true);
    $search_link  = get_post_meta($pid, '_dest_search_link', true);
    $link         = !empty($search_link) ? $search_link : esc_url(add_query_arg('s', $title, home_url('/rooms')));
    $dest_link    = get_permalink($pid);

    if (!$thumb) {
        $thumb = 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=600&q=80';
    }

    if ($style === 'card') {
        // Style: White card with image on top
        return '
        <div style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);transition:all .4s;position:relative;" class="brd-dest-card-item" onmouseenter="this.style.boxShadow=\'0 12px 40px rgba(0,0,0,0.15)\';this.style.transform=\'translateY(-4px)\'" onmouseleave="this.style.boxShadow=\'0 2px 12px rgba(0,0,0,0.08)\';this.style.transform=\'none\'">
            <a href="' . esc_url($dest_link) . '" style="display:block;position:relative;height:200px;overflow:hidden;">
                <img src="' . esc_url($thumb) . '" alt="' . esc_attr($title) . '" style="width:100%;height:100%;object-fit:cover;transition:transform .6s;" onmouseenter="this.style.transform=\'scale(1.08)\'" onmouseleave="this.style.transform=\'scale(1)\'">
                ' . ($badge ? '<span style="position:absolute;top:12px;left:12px;background:#f97316;color:#fff;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">' . esc_html($badge) . '</span>' : '') . '
            </a>
            <div style="padding:16px 20px 20px;">
                <h3 style="margin:0 0 4px;font-size:17px;font-weight:700;color:#1e293b;"><a href="' . esc_url($dest_link) . '" style="color:inherit;text-decoration:none;">' . esc_html($title) . '</a></h3>
                ' . ($hotel_count ? '<p style="margin:0 0 8px;font-size:12px;color:#94a3b8;">' . esc_html($hotel_count) . '</p>' : '') . '
                ' . ($tagline ? '<p style="margin:0 0 12px;font-size:13px;color:#64748b;line-height:1.5;">' . esc_html($tagline) . '</p>' : '') . '
                <a href="' . esc_url($link) . '" style="display:inline-flex;align-items:center;gap:6px;color:#2563eb;font-weight:700;font-size:13px;text-decoration:none;">Tìm phòng <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
            </div>
        </div>';
    }

    // Default: overlay style (như trang chủ hiện tại)
    return '
    <a href="' . esc_url($dest_link) . '" class="brd-dest-overlay" style="display:block;position:relative;overflow:hidden;border-radius:20px;height:320px;cursor:pointer;text-decoration:none;" onmouseenter="this.querySelector(\'img\').style.transform=\'scale(1.1)\'" onmouseleave="this.querySelector(\'img\').style.transform=\'scale(1)\'">
        <img src="' . esc_url($thumb) . '" alt="' . esc_attr($title) . '" style="width:100%;height:100%;object-fit:cover;transition:transform .7s ease;">
        <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.7) 100%);"></div>
        ' . ($badge ? '<span style="position:absolute;top:14px;right:14px;background:#f97316;color:#fff;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.05em;">' . esc_html($badge) . '</span>' : '') . '
        <div style="position:absolute;bottom:0;left:0;right:0;padding:20px 22px;">
            <h3 style="margin:0 0 4px;font-size:20px;font-weight:800;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,.3);">' . esc_html($title) . '</h3>
            ' . ($hotel_count ? '<p style="margin:0;font-size:13px;color:rgba(255,255,255,.75);">' . esc_html($hotel_count) . '</p>' : '') . '
        </div>
    </a>';
}

// ============================================================
// 7. TRANG ĐƠN "destination" (single-destination.php fallback)
// ============================================================
add_filter('single_template', 'brd_destination_single_template');
function brd_destination_single_template($template) {
    if (is_singular('destination')) {
        $t = locate_template('single-destination.php');
        if ($t) return $t;
    }
    return $template;
}

// ============================================================
// 8. FLUSH REWRITE KHI ACTIVATE
// ============================================================
add_action('after_switch_theme', function() {
    brd_register_destination_cpt();
    flush_rewrite_rules();
});
