<?php
/**
 * Location Page Meta Boxes
 * Cho phép chỉnh sửa toàn bộ nội dung trang Vị trí qua WordPress Editor
 * File: inc/location-metabox.php
 */

// ============================================================
// 1. ĐĂNG KÝ META BOXES (chỉ hiện trên trang có template "Vị trí")
// ============================================================
add_action('add_meta_boxes', 'brl_add_location_metaboxes');
function brl_add_location_metaboxes() {
    $screens = array('page');
    add_meta_box(
        'brl_location_hero',
        '🖼️ Hero — Tiêu đề & Ảnh nền',
        'brl_hero_cb',
        $screens, 'normal', 'high'
    );
    add_meta_box(
        'brl_location_contact',
        '📞 Thông tin liên hệ & Địa chỉ',
        'brl_contact_cb',
        $screens, 'normal', 'high'
    );
    add_meta_box(
        'brl_location_map',
        '🗺️ Bản đồ',
        'brl_map_cb',
        $screens, 'normal', 'high'
    );
    add_meta_box(
        'brl_location_stats',
        '📊 Thống kê Hero (Stat Bar)',
        'brl_stats_cb',
        $screens, 'normal', 'default'
    );
    add_meta_box(
        'brl_location_transport',
        '🚌 Cách di chuyển',
        'brl_transport_cb',
        $screens, 'normal', 'default'
    );
    add_meta_box(
        'brl_location_attractions',
        '🏛️ Địa điểm nổi bật lân cận',
        'brl_attractions_cb',
        $screens, 'normal', 'default'
    );
    add_meta_box(
        'brl_location_landmarks',
        '📍 Khoảng cách đến địa danh',
        'brl_landmarks_cb',
        $screens, 'normal', 'default'
    );
    add_meta_box(
        'brl_location_cta',
        '🎯 CTA Banner cuối trang',
        'brl_cta_cb',
        $screens, 'side', 'default'
    );
}

// ── Ẩn meta box nếu không phải template Vị trí ──
add_action('admin_head', 'brl_hide_non_location_metaboxes');
function brl_hide_non_location_metaboxes() {
    global $post;
    if (!$post) return;
    $template = get_page_template_slug($post->ID);
    if ($template !== 'page-location.php') {
        // Ẩn bằng CSS (chạy trước khi JS remove)
        $ids = array(
            'brl_location_hero', 'brl_location_contact', 'brl_location_map',
            'brl_location_stats', 'brl_location_transport', 'brl_location_attractions',
            'brl_location_landmarks', 'brl_location_cta'
        );
        foreach ($ids as $id) {
            echo '<style>#' . esc_attr($id) . '{display:none!important}</style>';
        }
    }
}

// ── Shared CSS & JS ──
add_action('admin_head', 'brl_metabox_assets');
function brl_metabox_assets() {
    global $post;
    if (!$post || get_page_template_slug($post->ID) !== 'page-location.php') return;
    ?>
    <style>
        /* ── Base layout ── */
        .brl-box          { padding: 4px 0; }
        .brl-grid         { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .brl-grid-3       { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        .brl-full         { grid-column: 1 / -1; }
        .brl-field        { display: flex; flex-direction: column; gap: 5px; }
        .brl-field label  { font-weight: 700; font-size: 11.5px; color: #475569; text-transform: uppercase; letter-spacing: .06em; }
        .brl-field input,
        .brl-field textarea,
        .brl-field select { border-radius: 8px !important; border: 1.5px solid #e2e8f0 !important;
                            padding: 8px 12px !important; font-size: 13px !important; width: 100%;
                            transition: border-color .2s; box-sizing: border-box; }
        .brl-field input:focus,
        .brl-field textarea:focus { border-color: #2563eb !important; outline: none !important;
                                    box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important; }
        .brl-field textarea { min-height: 72px; resize: vertical; }
        .brl-tip          { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .brl-section-sep  { border: none; border-top: 1px dashed #e2e8f0; margin: 18px 0; }

        /* ── Repeater (transport / attractions / landmarks) ── */
        .brl-repeater       { display: flex; flex-direction: column; gap: 12px; }
        .brl-repeater-item  { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;
                              padding: 14px 16px; position: relative; }
        .brl-repeater-item .brl-grid { gap: 10px; }
        .brl-item-header    { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .brl-item-title     { font-weight: 700; font-size: 12px; color: #334155; text-transform: uppercase; letter-spacing: .05em; }
        .brl-remove-btn     { background: #fee2e2; color: #dc2626; border: none; border-radius: 6px;
                              padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer; transition: background .2s; }
        .brl-remove-btn:hover { background: #fecaca; }
        .brl-add-btn        { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe;
                              border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 700;
                              cursor: pointer; transition: all .2s; width: 100%; margin-top: 8px; }
        .brl-add-btn:hover  { background: #dbeafe; }

        /* ── Image picker ── */
        .brl-img-wrap       { display: flex; align-items: flex-start; gap: 12px; }
        .brl-img-preview    { width: 100px; height: 70px; object-fit: cover; border-radius: 8px;
                              border: 1.5px solid #e2e8f0; display: block; }
        .brl-img-preview.empty { background: #f1f5f9; display: flex; align-items: center;
                                 justify-content: center; color: #94a3b8; font-size: 11px;
                                 text-align: center; }
        .brl-img-actions    { display: flex; flex-direction: column; gap: 6px; }
        .brl-img-select     { background: #2563eb; color: #fff; border: none; border-radius: 8px;
                              padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; }
        .brl-img-remove     { background: transparent; color: #94a3b8; border: none; font-size: 11px;
                              cursor: pointer; text-decoration: underline; text-align: left; padding: 0; }

        /* ── Color badge ── */
        .brl-badge         { display: inline-block; padding: 2px 8px; border-radius: 100px;
                             font-size: 10px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
        .brl-badge-blue    { background: #eff6ff; color: #2563eb; }
        .brl-badge-teal    { background: #f0fdfa; color: #0d9488; }
        .brl-badge-amber   { background: #fffbeb; color: #d97706; }
        .brl-badge-purple  { background: #f5f3ff; color: #7c3aed; }
    </style>
    <script>
    jQuery(function($){
        // ── Media picker ──
        $(document).on('click', '.brl-img-select', function(e){
            e.preventDefault();
            var btn = $(this);
            var frame = wp.media({ title: 'Chọn ảnh', button: { text: 'Dùng ảnh này' }, multiple: false });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                btn.closest('.brl-img-wrap').find('.brl-img-url').val(att.url);
                btn.closest('.brl-img-wrap').find('img.brl-img-preview').attr('src', att.url).show().removeClass('empty');
            });
            frame.open();
        });
        $(document).on('click', '.brl-img-remove', function(e){
            e.preventDefault();
            var wrap = $(this).closest('.brl-img-wrap');
            wrap.find('.brl-img-url').val('');
            wrap.find('img.brl-img-preview').attr('src','').hide();
        });

        // ── Repeater add/remove ──
        $(document).on('click', '.brl-add-btn', function(e){
            e.preventDefault();
            var tpl = $(this).prev('.brl-repeater').find('.brl-repeater-item:first').clone();
            // Clear values
            tpl.find('input, textarea, select').val('');
            tpl.find('img.brl-img-preview').attr('src','').hide();
            // Re-index names (basic: replace index in name attr)
            var container = $(this).prev('.brl-repeater');
            var newIdx = container.find('.brl-repeater-item').length;
            tpl.find('[name]').each(function(){
                var n = $(this).attr('name').replace(/\[\d+\]/, '['+newIdx+']');
                $(this).attr('name', n);
            });
            tpl.find('.brl-item-title').text('Mục ' + (newIdx + 1));
            container.append(tpl);
        });
        $(document).on('click', '.brl-remove-btn', function(e){
            e.preventDefault();
            var rep = $(this).closest('.brl-repeater');
            if(rep.find('.brl-repeater-item').length <= 1) return; // giữ ít nhất 1
            $(this).closest('.brl-repeater-item').remove();
            // Re-index
            rep.find('.brl-repeater-item').each(function(i){
                $(this).find('[name]').each(function(){
                    var n = $(this).attr('name').replace(/\[\d+\]/, '['+i+']');
                    $(this).attr('name', n);
                });
                $(this).find('.brl-item-title').text('Mục ' + (i + 1));
            });
        });
    });
    </script>
    <?php
}

// ============================================================
// 2. CALLBACK — HERO
// ============================================================
function brl_hero_cb($post) {
    wp_nonce_field('brl_save_location', 'brl_location_nonce');
    $title    = get_post_meta($post->ID, '_loc_hero_title',  true) ?: 'Vị trí của chúng tôi';
    $desc     = get_post_meta($post->ID, '_loc_hero_desc',   true) ?: 'Toạ lạc tại trung tâm thành phố, Sonata dễ dàng tiếp cận từ mọi hướng.';
    $badge    = get_post_meta($post->ID, '_loc_hero_badge',  true) ?: 'Trung tâm thành phố';
    $img      = get_post_meta($post->ID, '_loc_hero_image',  true);
    ?>
    <div class="brl-box brl-grid">
        <div class="brl-field brl-full">
            <label for="brl_hero_title">📝 Tiêu đề chính (H1)</label>
            <input type="text" id="brl_hero_title" name="brl_hero_title" value="<?php echo esc_attr($title); ?>" placeholder="Vị trí của chúng tôi">
            <span class="brl-tip">2 từ đầu sẽ hiển thị màu trắng, phần còn lại sẽ được tô màu gradient xanh.</span>
        </div>
        <div class="brl-field brl-full">
            <label for="brl_hero_desc">💬 Mô tả phụ</label>
            <textarea id="brl_hero_desc" name="brl_hero_desc" placeholder="Toạ lạc tại trung tâm thành phố..."><?php echo esc_textarea($desc); ?></textarea>
        </div>
        <div class="brl-field">
            <label for="brl_hero_badge">🏷️ Badge text (dòng tag nhỏ)</label>
            <input type="text" id="brl_hero_badge" name="brl_hero_badge" value="<?php echo esc_attr($badge); ?>" placeholder="Trung tâm thành phố">
        </div>
        <div class="brl-field">
            <label>🖼️ Ảnh nền Hero</label>
            <div class="brl-img-wrap">
                <?php if ($img) : ?>
                    <img class="brl-img-preview" src="<?php echo esc_url($img); ?>">
                <?php else : ?>
                    <img class="brl-img-preview empty" src="" style="display:none;">
                    <span class="brl-img-preview empty">Chưa có ảnh</span>
                <?php endif; ?>
                <div class="brl-img-actions">
                    <input type="hidden" name="brl_hero_image" class="brl-img-url" value="<?php echo esc_attr($img); ?>">
                    <button type="button" class="brl-img-select">Chọn ảnh</button>
                    <button type="button" class="brl-img-remove">✕ Xóa ảnh</button>
                </div>
            </div>
            <span class="brl-tip">Nếu để trống sẽ dùng nền tối gradient mặc định.</span>
        </div>
    </div>
    <?php
}

// ============================================================
// 3. CALLBACK — CONTACT INFO
// ============================================================
function brl_contact_cb($post) {
    $address    = get_post_meta($post->ID, '_loc_address',    true) ?: '123 Đường Trần Hưng Đạo, Quận 1, TP. Hồ Chí Minh';
    $phone      = get_post_meta($post->ID, '_loc_phone',      true) ?: '0123 456 789';
    $phone_link = get_post_meta($post->ID, '_loc_phone_link', true) ?: 'tel:0123456789';
    $email      = get_post_meta($post->ID, '_loc_email',      true) ?: 'info@sonata.vn';
    $checkin    = get_post_meta($post->ID, '_loc_checkin',    true) ?: '14:00 – 24:00 mọi ngày';
    $checkout   = get_post_meta($post->ID, '_loc_checkout',   true) ?: 'Trước 12:00';
    ?>
    <div class="brl-box brl-grid">
        <div class="brl-field brl-full">
            <label for="brl_address">📍 Địa chỉ đầy đủ</label>
            <input type="text" id="brl_address" name="brl_address" value="<?php echo esc_attr($address); ?>" placeholder="123 Đường..., Quận..., TP.HCM">
        </div>
        <div class="brl-field">
            <label for="brl_phone">📞 Số điện thoại (hiển thị)</label>
            <input type="text" id="brl_phone" name="brl_phone" value="<?php echo esc_attr($phone); ?>" placeholder="0123 456 789">
        </div>
        <div class="brl-field">
            <label for="brl_phone_link">🔗 Liên kết điện thoại</label>
            <input type="text" id="brl_phone_link" name="brl_phone_link" value="<?php echo esc_attr($phone_link); ?>" placeholder="tel:0123456789">
            <span class="brl-tip">Dùng định dạng tel:xxxxxxxxxx</span>
        </div>
        <div class="brl-field">
            <label for="brl_email">✉️ Email</label>
            <input type="email" id="brl_email" name="brl_email" value="<?php echo esc_attr($email); ?>" placeholder="info@sonata.vn">
        </div>
        <div class="brl-field">
            <label for="brl_checkin">🕑 Giờ Check-in</label>
            <input type="text" id="brl_checkin" name="brl_checkin" value="<?php echo esc_attr($checkin); ?>" placeholder="14:00 – 24:00 mọi ngày">
        </div>
        <div class="brl-field">
            <label for="brl_checkout">🕛 Giờ Check-out</label>
            <input type="text" id="brl_checkout" name="brl_checkout" value="<?php echo esc_attr($checkout); ?>" placeholder="Trước 12:00">
        </div>
    </div>
    <?php
}

// ============================================================
// 4. CALLBACK — MAP
// ============================================================
function brl_map_cb($post) {
    $embed = get_post_meta($post->ID, '_loc_map_embed', true);
    $query = get_post_meta($post->ID, '_loc_map_query', true) ?: '123+Tran+Hung+Dao,+Ho+Chi+Minh+City';
    ?>
    <div class="brl-box brl-grid">
        <div class="brl-field brl-full">
            <label for="brl_map_embed">🗺️ Nhúng iframe Google Maps (tùy chọn)</label>
            <textarea id="brl_map_embed" name="brl_map_embed" style="min-height:90px;font-family:monospace;font-size:12px;" placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'><?php echo esc_textarea($embed); ?></textarea>
            <span class="brl-tip">Lấy từ Google Maps → Chia sẻ → Nhúng bản đồ → Sao chép HTML. Nếu để trống sẽ dùng bản đồ mặc định.</span>
        </div>
        <div class="brl-field brl-full">
            <label for="brl_map_query">🔍 Từ khóa tìm kiếm Maps (khi dùng API key)</label>
            <input type="text" id="brl_map_query" name="brl_map_query" value="<?php echo esc_attr($query); ?>" placeholder="123+Tran+Hung+Dao,+Ho+Chi+Minh+City">
            <span class="brl-tip">Chỉ dùng khi đã cài Google Maps API Key trong Customizer. Thay khoảng trắng bằng dấu +.</span>
        </div>
    </div>
    <?php
}

// ============================================================
// 5. CALLBACK — STATS
// ============================================================
function brl_stats_cb($post) {
    $stats = get_post_meta($post->ID, '_loc_stats', true);
    if (empty($stats) || !is_array($stats)) {
        $stats = array(
            array('num' => '5', 'unit' => 'km',  'label' => 'Từ sân bay'),
            array('num' => '200', 'unit' => 'm', 'label' => 'Đến trung tâm'),
            array('num' => '12',  'unit' => '+',  'label' => 'Điểm nổi tiếng gần đây'),
        );
    }
    ?>
    <div class="brl-box">
        <p class="brl-tip" style="margin-bottom:12px;">Thanh thống kê hiển thị bên dưới hero. Tối đa 3 mục.</p>
        <div class="brl-repeater">
            <?php foreach ($stats as $i => $s) : ?>
            <div class="brl-repeater-item">
                <div class="brl-item-header">
                    <span class="brl-item-title">Mục <?php echo $i + 1; ?></span>
                    <button type="button" class="brl-remove-btn">✕ Xóa</button>
                </div>
                <div class="brl-grid-3">
                    <div class="brl-field">
                        <label>Số</label>
                        <input type="text" name="brl_stats[<?php echo $i; ?>][num]" value="<?php echo esc_attr($s['num']); ?>" placeholder="5">
                    </div>
                    <div class="brl-field">
                        <label>Đơn vị</label>
                        <input type="text" name="brl_stats[<?php echo $i; ?>][unit]" value="<?php echo esc_attr($s['unit']); ?>" placeholder="km">
                    </div>
                    <div class="brl-field">
                        <label>Nhãn</label>
                        <input type="text" name="brl_stats[<?php echo $i; ?>][label]" value="<?php echo esc_attr($s['label']); ?>" placeholder="Từ sân bay">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="brl-add-btn">+ Thêm mục</button>
    </div>
    <?php
}

// ============================================================
// 6. CALLBACK — TRANSPORT
// ============================================================
function brl_transport_cb($post) {
    $items = get_post_meta($post->ID, '_loc_transport', true);
    if (empty($items) || !is_array($items)) {
        $items = array(
            array('icon' => '✈️', 'title' => 'Máy bay',    'time' => '~20 phút', 'color' => 'blue',   'desc' => 'Từ sân bay Tân Sơn Nhất, đi taxi hoặc xe buýt sân bay đến trung tâm, chỉ cách 5km.'),
            array('icon' => '🚌', 'title' => 'Xe buýt',    'time' => '~10 phút', 'color' => 'teal',   'desc' => 'Nhiều tuyến xe buýt đi qua khu vực. Trạm xe buýt gần nhất chỉ cách 150m.'),
            array('icon' => '🚖', 'title' => 'Taxi / Grab','time' => '~15 phút', 'color' => 'amber',  'desc' => 'Taxi và Grab luôn sẵn sàng. Nhân viên lễ tân có thể đặt xe hộ bất kỳ lúc nào.'),
            array('icon' => '🚗', 'title' => 'Xe tự lái',  'time' => 'Bãi đỗ xe','color' => 'purple', 'desc' => 'Bãi đỗ xe tại chỗ và đường phố. Nhập địa chỉ vào GPS để được chỉ đường chính xác.'),
        );
    }
    $colors = array('blue' => '🔵 Blue', 'teal' => '🟢 Teal', 'amber' => '🟡 Amber', 'purple' => '🟣 Purple');
    ?>
    <div class="brl-box">
        <div class="brl-repeater">
            <?php foreach ($items as $i => $item) : ?>
            <div class="brl-repeater-item">
                <div class="brl-item-header">
                    <span class="brl-item-title">Mục <?php echo $i + 1; ?> — <?php echo esc_html($item['title']); ?></span>
                    <button type="button" class="brl-remove-btn">✕ Xóa</button>
                </div>
                <div class="brl-grid">
                    <div class="brl-field">
                        <label>😊 Emoji icon</label>
                        <input type="text" name="brl_transport[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($item['icon']); ?>" placeholder="✈️">
                    </div>
                    <div class="brl-field">
                        <label>📛 Tên phương tiện</label>
                        <input type="text" name="brl_transport[<?php echo $i; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" placeholder="Máy bay">
                    </div>
                    <div class="brl-field">
                        <label>⏱️ Thời gian / Label badge</label>
                        <input type="text" name="brl_transport[<?php echo $i; ?>][time]" value="<?php echo esc_attr($item['time']); ?>" placeholder="~20 phút">
                    </div>
                    <div class="brl-field">
                        <label>🎨 Màu card</label>
                        <select name="brl_transport[<?php echo $i; ?>][color]">
                            <?php foreach ($colors as $val => $label) : ?>
                                <option value="<?php echo $val; ?>" <?php selected($item['color'], $val); ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="brl-field brl-full">
                        <label>📝 Mô tả</label>
                        <textarea name="brl_transport[<?php echo $i; ?>][desc]" placeholder="Mô tả cách di chuyển..."><?php echo esc_textarea($item['desc']); ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="brl-add-btn">+ Thêm phương tiện</button>
    </div>
    <?php
}

// ============================================================
// 7. CALLBACK — NEARBY ATTRACTIONS
// ============================================================
function brl_attractions_cb($post) {
    $items = get_post_meta($post->ID, '_loc_attractions', true);
    if (empty($items) || !is_array($items)) {
        $items = array(
            array('emoji' => '🏛️', 'name' => 'Bến Nhà Rồng',              'cat' => 'Di tích lịch sử', 'dist' => '1.2 km', 'walk' => '15 phút đi bộ', 'desc' => 'Bến cảng lịch sử nổi tiếng, nơi Bác Hồ ra đi tìm đường cứu nước năm 1911.'),
            array('emoji' => '🛍️', 'name' => 'Chợ Bến Thành',             'cat' => 'Mua sắm',         'dist' => '800 m',  'walk' => '10 phút đi bộ', 'desc' => 'Biểu tượng của TP.HCM với hàng trăm gian hàng ẩm thực và lưu niệm.'),
            array('emoji' => '⛪',  'name' => 'Nhà thờ Đức Bà',            'cat' => 'Kiến trúc',       'dist' => '1.5 km', 'walk' => '20 phút đi bộ', 'desc' => 'Công trình kiến trúc Gothic nổi tiếng nhất Sài Gòn, xây dựng thế kỷ 19.'),
            array('emoji' => '🎭', 'name' => 'Phố đi bộ Nguyễn Huệ',      'cat' => 'Giải trí',        'dist' => '600 m',  'walk' => '8 phút đi bộ',  'desc' => 'Con phố hiện đại sầm uất với nhiều nhà hàng, quán cà phê và sự kiện.'),
            array('emoji' => '🏅', 'name' => 'Bảo tàng Chứng tích Chiến tranh','cat' => 'Bảo tàng',   'dist' => '2 km',   'walk' => '25 phút đi bộ', 'desc' => 'Một trong những bảo tàng được tham quan nhiều nhất Đông Nam Á.'),
            array('emoji' => '🏰', 'name' => 'Dinh Thống Nhất',             'cat' => 'Di tích lịch sử','dist' => '2.3 km', 'walk' => '30 phút đi bộ', 'desc' => 'Cung điện lịch sử mang kiến trúc độc đáo, chứng kiến nhiều sự kiện lịch sử.'),
        );
    }
    ?>
    <div class="brl-box">
        <p class="brl-tip" style="margin-bottom:12px;">Hiển thị dạng lưới 3 cột. Nên để 3–6 địa điểm.</p>
        <div class="brl-repeater">
            <?php foreach ($items as $i => $item) : ?>
            <div class="brl-repeater-item">
                <div class="brl-item-header">
                    <span class="brl-item-title">Địa điểm <?php echo $i + 1; ?> — <?php echo esc_html($item['name']); ?></span>
                    <button type="button" class="brl-remove-btn">✕ Xóa</button>
                </div>
                <div class="brl-grid">
                    <div class="brl-field">
                        <label>😊 Emoji</label>
                        <input type="text" name="brl_attractions[<?php echo $i; ?>][emoji]" value="<?php echo esc_attr($item['emoji']); ?>" placeholder="🏛️">
                    </div>
                    <div class="brl-field">
                        <label>🏷️ Danh mục</label>
                        <input type="text" name="brl_attractions[<?php echo $i; ?>][cat]" value="<?php echo esc_attr($item['cat']); ?>" placeholder="Di tích lịch sử">
                    </div>
                    <div class="brl-field brl-full">
                        <label>📛 Tên địa điểm</label>
                        <input type="text" name="brl_attractions[<?php echo $i; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" placeholder="Bến Nhà Rồng">
                    </div>
                    <div class="brl-field">
                        <label>📏 Khoảng cách</label>
                        <input type="text" name="brl_attractions[<?php echo $i; ?>][dist]" value="<?php echo esc_attr($item['dist']); ?>" placeholder="1.2 km">
                    </div>
                    <div class="brl-field">
                        <label>🚶 Thời gian đi bộ</label>
                        <input type="text" name="brl_attractions[<?php echo $i; ?>][walk]" value="<?php echo esc_attr($item['walk']); ?>" placeholder="15 phút đi bộ">
                    </div>
                    <div class="brl-field brl-full">
                        <label>📝 Mô tả ngắn</label>
                        <textarea name="brl_attractions[<?php echo $i; ?>][desc]" placeholder="Mô tả địa điểm..."><?php echo esc_textarea($item['desc']); ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="brl-add-btn">+ Thêm địa điểm</button>
    </div>
    <?php
}

// ============================================================
// 8. CALLBACK — LANDMARKS (dark section)
// ============================================================
function brl_landmarks_cb($post) {
    $items = get_post_meta($post->ID, '_loc_landmarks', true);
    if (empty($items) || !is_array($items)) {
        $items = array(
            array('emoji' => '🛍️', 'name' => 'Chợ Bến Thành',           'dist' => '800m',  'time' => '10 phút'),
            array('emoji' => '🎭', 'name' => 'Phố đi bộ Nguyễn Huệ',    'dist' => '600m',  'time' => '8 phút'),
            array('emoji' => '⛪',  'name' => 'Nhà thờ Đức Bà',          'dist' => '1.5km', 'time' => '20 phút'),
            array('emoji' => '🏛️', 'name' => 'Bến Nhà Rồng',             'dist' => '1.2km', 'time' => '15 phút'),
            array('emoji' => '🌿', 'name' => 'Thảo Cầm Viên',            'dist' => '2.5km', 'time' => '12 phút xe'),
            array('emoji' => '✈️', 'name' => 'Sân bay Tân Sơn Nhất',    'dist' => '5km',   'time' => '20 phút xe'),
        );
    }
    ?>
    <div class="brl-box">
        <p class="brl-tip" style="margin-bottom:12px;">Danh sách hiển thị trong section tối. Cũng được dùng cho bản đồ vòng tròn (tối đa 6 chấm).</p>
        <div class="brl-repeater">
            <?php foreach ($items as $i => $item) : ?>
            <div class="brl-repeater-item">
                <div class="brl-item-header">
                    <span class="brl-item-title">Địa danh <?php echo $i + 1; ?> — <?php echo esc_html($item['name']); ?></span>
                    <button type="button" class="brl-remove-btn">✕ Xóa</button>
                </div>
                <div class="brl-grid-3">
                    <div class="brl-field">
                        <label>😊 Emoji</label>
                        <input type="text" name="brl_landmarks[<?php echo $i; ?>][emoji]" value="<?php echo esc_attr($item['emoji']); ?>" placeholder="🛍️">
                    </div>
                    <div class="brl-field">
                        <label>📏 Khoảng cách</label>
                        <input type="text" name="brl_landmarks[<?php echo $i; ?>][dist]" value="<?php echo esc_attr($item['dist']); ?>" placeholder="800m">
                    </div>
                    <div class="brl-field">
                        <label>⏱️ Thời gian</label>
                        <input type="text" name="brl_landmarks[<?php echo $i; ?>][time]" value="<?php echo esc_attr($item['time']); ?>" placeholder="10 phút">
                    </div>
                    <div class="brl-field brl-full">
                        <label>📛 Tên địa danh</label>
                        <input type="text" name="brl_landmarks[<?php echo $i; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" placeholder="Chợ Bến Thành">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="brl-add-btn">+ Thêm địa danh</button>
    </div>
    <?php
}

// ============================================================
// 9. CALLBACK — CTA
// ============================================================
function brl_cta_cb($post) {
    $title = get_post_meta($post->ID, '_loc_cta_title', true) ?: 'Sẵn sàng đến với Sonata?';
    $desc  = get_post_meta($post->ID, '_loc_cta_desc',  true) ?: 'Đặt phòng ngay hôm nay và trải nghiệm sự tiện lợi của vị trí trung tâm cùng dịch vụ đẳng cấp.';
    $btn1_text = get_post_meta($post->ID, '_loc_cta_btn1_text', true) ?: 'Đặt phòng ngay';
    $btn1_url  = get_post_meta($post->ID, '_loc_cta_btn1_url',  true) ?: '/dat-phong';
    $btn2_text = get_post_meta($post->ID, '_loc_cta_btn2_text', true) ?: 'Liên hệ chúng tôi';
    $btn2_url  = get_post_meta($post->ID, '_loc_cta_btn2_url',  true) ?: '/lien-he';
    ?>
    <div class="brl-box" style="display:flex;flex-direction:column;gap:12px;">
        <div class="brl-field">
            <label>📢 Tiêu đề CTA</label>
            <input type="text" name="brl_cta_title" value="<?php echo esc_attr($title); ?>" placeholder="Sẵn sàng đến với Sonata?">
        </div>
        <div class="brl-field">
            <label>💬 Mô tả CTA</label>
            <textarea name="brl_cta_desc" placeholder="Mô tả ngắn..."><?php echo esc_textarea($desc); ?></textarea>
        </div>
        <hr class="brl-section-sep">
        <div class="brl-field">
            <label>🔵 Nút chính — Text</label>
            <input type="text" name="brl_cta_btn1_text" value="<?php echo esc_attr($btn1_text); ?>" placeholder="Đặt phòng ngay">
        </div>
        <div class="brl-field">
            <label>🔵 Nút chính — URL</label>
            <input type="text" name="brl_cta_btn1_url" value="<?php echo esc_attr($btn1_url); ?>" placeholder="/dat-phong">
        </div>
        <hr class="brl-section-sep">
        <div class="brl-field">
            <label>⚪ Nút phụ — Text</label>
            <input type="text" name="brl_cta_btn2_text" value="<?php echo esc_attr($btn2_text); ?>" placeholder="Liên hệ chúng tôi">
        </div>
        <div class="brl-field">
            <label>⚪ Nút phụ — URL</label>
            <input type="text" name="brl_cta_btn2_url" value="<?php echo esc_attr($btn2_url); ?>" placeholder="/lien-he">
        </div>
    </div>
    <?php
}

// ============================================================
// 10. LƯU DỮ LIỆU KHI SAVE
// ============================================================
add_action('save_post', 'brl_save_location_meta');
function brl_save_location_meta($post_id) {
    // Security checks
    if (!isset($_POST['brl_location_nonce'])) return;
    if (!wp_verify_nonce($_POST['brl_location_nonce'], 'brl_save_location')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_page_template_slug($post_id) !== 'page-location.php') return;

    // ── Simple text fields ──
    $text_fields = array(
        'brl_hero_title'   => '_loc_hero_title',
        'brl_hero_desc'    => '_loc_hero_desc',
        'brl_hero_badge'   => '_loc_hero_badge',
        'brl_hero_image'   => '_loc_hero_image',
        'brl_address'      => '_loc_address',
        'brl_phone'        => '_loc_phone',
        'brl_phone_link'   => '_loc_phone_link',
        'brl_email'        => '_loc_email',
        'brl_checkin'      => '_loc_checkin',
        'brl_checkout'     => '_loc_checkout',
        'brl_map_embed'    => '_loc_map_embed',
        'brl_map_query'    => '_loc_map_query',
        'brl_cta_title'    => '_loc_cta_title',
        'brl_cta_desc'     => '_loc_cta_desc',
        'brl_cta_btn1_text'=> '_loc_cta_btn1_text',
        'brl_cta_btn1_url' => '_loc_cta_btn1_url',
        'brl_cta_btn2_text'=> '_loc_cta_btn2_text',
        'brl_cta_btn2_url' => '_loc_cta_btn2_url',
    );
    foreach ($text_fields as $post_key => $meta_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }

    // Map embed: allow iframe tags
    if (isset($_POST['brl_map_embed'])) {
        update_post_meta($post_id, '_loc_map_embed', wp_kses($_POST['brl_map_embed'], array(
            'iframe' => array('src'=>true,'width'=>true,'height'=>true,'allowfullscreen'=>true,'loading'=>true,'referrerpolicy'=>true,'style'=>true,'frameborder'=>true),
        )));
    }

    // ── Repeater: Stats ──
    if (isset($_POST['brl_stats']) && is_array($_POST['brl_stats'])) {
        $stats = array();
        foreach ($_POST['brl_stats'] as $s) {
            $stats[] = array(
                'num'   => sanitize_text_field($s['num']   ?? ''),
                'unit'  => sanitize_text_field($s['unit']  ?? ''),
                'label' => sanitize_text_field($s['label'] ?? ''),
            );
        }
        update_post_meta($post_id, '_loc_stats', $stats);
    }

    // ── Repeater: Transport ──
    if (isset($_POST['brl_transport']) && is_array($_POST['brl_transport'])) {
        $items = array();
        $allowed_colors = array('blue','teal','amber','purple');
        foreach ($_POST['brl_transport'] as $t) {
            $items[] = array(
                'icon'  => sanitize_text_field($t['icon']  ?? ''),
                'title' => sanitize_text_field($t['title'] ?? ''),
                'time'  => sanitize_text_field($t['time']  ?? ''),
                'color' => in_array($t['color'] ?? '', $allowed_colors) ? $t['color'] : 'blue',
                'desc'  => sanitize_textarea_field($t['desc'] ?? ''),
            );
        }
        update_post_meta($post_id, '_loc_transport', $items);
    }

    // ── Repeater: Attractions ──
    if (isset($_POST['brl_attractions']) && is_array($_POST['brl_attractions'])) {
        $items = array();
        foreach ($_POST['brl_attractions'] as $a) {
            $items[] = array(
                'emoji' => sanitize_text_field($a['emoji'] ?? ''),
                'name'  => sanitize_text_field($a['name']  ?? ''),
                'cat'   => sanitize_text_field($a['cat']   ?? ''),
                'dist'  => sanitize_text_field($a['dist']  ?? ''),
                'walk'  => sanitize_text_field($a['walk']  ?? ''),
                'desc'  => sanitize_textarea_field($a['desc'] ?? ''),
            );
        }
        update_post_meta($post_id, '_loc_attractions', $items);
    }

    // ── Repeater: Landmarks ──
    if (isset($_POST['brl_landmarks']) && is_array($_POST['brl_landmarks'])) {
        $items = array();
        foreach ($_POST['brl_landmarks'] as $l) {
            $items[] = array(
                'emoji' => sanitize_text_field($l['emoji'] ?? ''),
                'name'  => sanitize_text_field($l['name']  ?? ''),
                'dist'  => sanitize_text_field($l['dist']  ?? ''),
                'time'  => sanitize_text_field($l['time']  ?? ''),
            );
        }
        update_post_meta($post_id, '_loc_landmarks', $items);
    }
}
