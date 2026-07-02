<?php
/**
 * Customer Review Custom Post Type
 * CPT "review" – Bài đánh giá khách hàng
 * Tạo & quản lý đánh giá trực tiếp trong WordPress Admin
 */

// ============================================================
// 1. ĐĂNG KÝ CPT "review"
// ============================================================
add_action('init', 'brd_register_review_cpt');
function brd_register_review_cpt() {
    $labels = array(
        'name'               => 'Đánh giá',
        'singular_name'      => 'Đánh giá',
        'add_new'            => 'Thêm đánh giá',
        'add_new_item'       => 'Thêm đánh giá mới',
        'edit_item'          => 'Chỉnh sửa đánh giá',
        'new_item'           => 'Đánh giá mới',
        'view_item'          => 'Xem đánh giá',
        'search_items'       => 'Tìm kiếm đánh giá',
        'not_found'          => 'Không tìm thấy đánh giá nào',
        'not_found_in_trash' => 'Không có đánh giá trong thùng rác',
        'menu_name'          => 'Đánh giá',
        'all_items'          => 'Tất cả đánh giá',
    );

    register_post_type('review', array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => false, // Không cần trang archive riêng cho review
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-star-filled',
        'menu_position' => 25, // Đặt dưới mục Comments
        'supports'      => array('title', 'editor', 'thumbnail'), // title: Tiêu đề đánh giá, editor: Nội dung, thumbnail: Avatar khách hàng
        'show_in_menu'  => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => false, // Thường review chỉ hiển thị nhúng trên trang khác
    ));
}

// ============================================================
// 2. META BOX CHI TIẾT ĐÁNH GIÁ
// ============================================================
add_action('add_meta_boxes', 'brd_add_review_metabox');
function brd_add_review_metabox() {
    add_meta_box(
        'brd_review_details',
        '⭐ Thông tin Đánh giá',
        'brd_review_metabox_cb',
        'review',
        'normal',
        'high'
    );
}

function brd_review_metabox_cb($post) {
    wp_nonce_field('brd_save_review', 'brd_review_nonce');

    $reviewer_name  = get_post_meta($post->ID, '_review_reviewer_name', true);
    $rating         = get_post_meta($post->ID, '_review_rating', true) ?: '5';
    $stay_date      = get_post_meta($post->ID, '_review_stay_date', true);
    $location       = get_post_meta($post->ID, '_review_location', true);
    $contributions  = get_post_meta($post->ID, '_review_contributions', true) ?: '0';
    $sort_order     = get_post_meta($post->ID, '_review_sort_order', true) ?: '0';
    ?>
    <style>
        .brd-metabox { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 4px 0; }
        .brd-field { display: flex; flex-direction: column; gap: 6px; }
        .brd-field label { font-weight: 600; font-size: 12px; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
        .brd-field input, .brd-field select { border-radius: 8px !important; border: 1.5px solid #e2e8f0 !important; padding: 8px 12px !important; font-size: 13px !important; transition: border-color .2s; }
        .brd-field input:focus, .brd-field select:focus { border-color: #2563eb !important; outline: none !important; box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important; }
        .brd-tip { font-size: 11px; color: #94a3b8; margin-top: 2px; }
    </style>
    <div class="brd-metabox">
        <div class="brd-field">
            <label for="brd_reviewer_name">👤 Tên khách hàng (Username)</label>
            <input type="text" id="brd_reviewer_name" name="brd_reviewer_name" value="<?php echo esc_attr($reviewer_name); ?>" placeholder="VD: mtruong2003">
        </div>
        <div class="brd-field">
            <label for="brd_rating">⭐ Số sao (1-5)</label>
            <select id="brd_rating" name="brd_rating">
                <option value="5" <?php selected($rating, '5'); ?>>5 Sao - Rất tuyệt vời</option>
                <option value="4" <?php selected($rating, '4'); ?>>4 Sao - Tuyệt vời</option>
                <option value="3" <?php selected($rating, '3'); ?>>3 Sao - Bình thường</option>
                <option value="2" <?php selected($rating, '2'); ?>>2 Sao - Kém</option>
                <option value="1" <?php selected($rating, '1'); ?>>1 Sao - Rất kém</option>
            </select>
        </div>
        <div class="brd-field">
            <label for="brd_stay_date">📅 Ngày đánh giá / Ngày lưu trú</label>
            <input type="text" id="brd_stay_date" name="brd_stay_date" value="<?php echo esc_attr($stay_date); ?>" placeholder="VD: Feb 2024">
        </div>
        <div class="brd-field">
            <label for="brd_location">📍 Địa điểm khách (Location)</label>
            <input type="text" id="brd_location" name="brd_location" value="<?php echo esc_attr($location); ?>" placeholder="VD: Hồ Chí Minh, Vietnam">
        </div>
        <div class="brd-field">
            <label for="brd_contributions">✍️ Số đóng góp (Contributions)</label>
            <input type="number" id="brd_contributions" name="brd_contributions" value="<?php echo esc_attr($contributions); ?>" placeholder="0" min="0">
        </div>
        <div class="brd-field">
            <label for="brd_sort_order">📶 Thứ tự hiển thị</label>
            <input type="number" id="brd_sort_order" name="brd_sort_order" value="<?php echo esc_attr($sort_order); ?>" placeholder="0 = đầu tiên" min="0">
        </div>
    </div>
    <p class="brd-tip" style="grid-column: 1 / -1; margin-top: 10px;">
        <em>Lưu ý: Tiêu đề bài viết là <strong>Tiêu đề đánh giá</strong> (VD: Trải nghiệm tại Resort...). Nội dung bài viết là <strong>Nội dung đánh giá chi tiết</strong>. Ảnh đại diện (Featured Image) sẽ được dùng làm <strong>Avatar của khách</strong>.</em>
    </p>
    <?php
}

// Lưu meta khi save
add_action('save_post_review', 'brd_save_review_meta');
function brd_save_review_meta($post_id) {
    if (!isset($_POST['brd_review_nonce']) || !wp_verify_nonce($_POST['brd_review_nonce'], 'brd_save_review')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array(
        'brd_reviewer_name' => '_review_reviewer_name',
        'brd_rating'        => '_review_rating',
        'brd_stay_date'     => '_review_stay_date',
        'brd_location'      => '_review_location',
        'brd_contributions' => '_review_contributions',
        'brd_sort_order'    => '_review_sort_order',
    );

    foreach ($fields as $post_key => $meta_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }
}

// ============================================================
// 3. ADMIN COLUMNS
// ============================================================
add_filter('manage_review_posts_columns', 'brd_review_columns');
function brd_review_columns($cols) {
    $new = array();
    $new['cb']            = $cols['cb'];
    $new['thumbnail']     = 'Avatar';
    $new['title']         = 'Tiêu đề đánh giá';
    $new['reviewer_name'] = 'Tên khách hàng';
    $new['rating']        = '⭐ Số sao';
    $new['stay_date']     = '📅 Ngày';
    $new['date']          = 'Ngày tạo';
    return $new;
}

add_action('manage_review_posts_custom_column', 'brd_review_column_content', 10, 2);
function brd_review_column_content($col, $post_id) {
    switch ($col) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50), array('style' => 'border-radius: 50%; object-fit: cover;'));
            } else {
                echo '<div style="width:50px;height:50px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:20px;">👤</div>';
            }
            break;
        case 'reviewer_name':
            $name = get_post_meta($post_id, '_review_reviewer_name', true);
            echo esc_html($name ?: 'Ẩn danh');
            break;
        case 'rating':
            $rating = get_post_meta($post_id, '_review_rating', true);
            $stars = str_repeat('⭐', (int)$rating);
            echo "<strong>{$rating}/5</strong> <br><span style='font-size:10px;'>{$stars}</span>";
            break;
        case 'stay_date':
            $stay_date = get_post_meta($post_id, '_review_stay_date', true);
            echo esc_html($stay_date ?: '-');
            break;
    }
}

// Cho phép sort theo rating
add_filter('manage_edit-review_sortable_columns', 'brd_review_sortable_columns');
function brd_review_sortable_columns($columns) {
    $columns['rating'] = 'rating';
    return $columns;
}

add_action('pre_get_posts', 'brd_review_orderby');
function brd_review_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') === 'review' && $query->get('orderby') === 'rating') {
        $query->set('meta_key', '_review_rating');
        $query->set('orderby', 'meta_value_num');
    }
}
