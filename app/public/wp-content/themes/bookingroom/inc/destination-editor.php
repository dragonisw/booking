<?php
/**
 * Destination Custom Editor
 * Thay thế Gutenberg bằng giao diện quản lý đẹp và đầy đủ tính năng
 * cho post type "destination"
 */

// ============================================================
// 1. TẮT GUTENBERG CHO POST TYPE DESTINATION
// ============================================================
add_filter('use_block_editor_for_post_type', 'brd_disable_gutenberg_for_destination', 10, 2);
function brd_disable_gutenberg_for_destination($use_block_editor, $post_type) {
    if ($post_type === 'destination') {
        return false;
    }
    return $use_block_editor;
}

// ============================================================
// 2. LOAD CSS + JS CHO CUSTOM EDITOR
// ============================================================
add_action('admin_enqueue_scripts', 'brd_editor_assets');
function brd_editor_assets($hook) {
    global $post_type, $post;
    if ($post_type !== 'destination') return;
    if (!in_array($hook, ['post.php', 'post-new.php'])) return;

    // Media uploader
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

// ============================================================
// 3. ĐỀ LÊN EDITOR BẰNG CSS (ẨN DEFAULT EDITOR, HIỆN CUSTOM)
// ============================================================
add_action('admin_head', 'brd_editor_overlay_css');
function brd_editor_overlay_css() {
    global $post_type, $pagenow;
    if ($post_type !== 'destination') return;
    if (!in_array($pagenow, ['post.php', 'post-new.php'])) return;
    ?>
    <style>
    /* ── Ẩn các thành phần mặc định không cần thiết ── */
    #postdivrich,
    #postdiv,
    #wp-content-wrap,
    #ed_toolbar,
    .wp-editor-container,
    #content-tmce,
    #content-html,
    #content-tmce + .quicktags-toolbar,
    div#wp-content-editor-tools { display: none !important; }

    /* ── Layout tổng thể ── */
    #post-body { margin: 0 !important; }
    #post-body-content { float: none !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
    #side-sortables, #side-info-column { display: none !important; }
    #postbox-container-1 { display: none !important; }
    #postbox-container-2 { width: 100% !important; float: none !important; padding: 0 !important; }

    /* Ẩn titlediv default — ta tự vẽ lại */
    #titlediv { display: none !important; }

    /* Ẩn metaboxes cũ (ta tích hợp vào editor mới) */
    #brd_destination_details, #brd_destination_display { display: none !important; }
    #submitdiv { display: none !important; }
    #postimagediv { display: none !important; }
    #slugdiv { display: none !important; }
    #wpseo-meta-section-primary { display: none !important; }

    /* Màn hình full */
    .wrap { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    #wpcontent { padding-left: 0 !important; }
    #wpbody-content { padding-bottom: 0 !important; }

    /* ── Custom Editor Shell ── */
    #brd-custom-editor {
        --ce-bg: #f1f5f9;
        --ce-white: #ffffff;
        --ce-dark: #0f172a;
        --ce-navy: #1e293b;
        --ce-gray: #64748b;
        --ce-light: #f8fafc;
        --ce-border: #e2e8f0;
        --ce-blue: #2563eb;
        --ce-blue-d: #1d4ed8;
        --ce-cyan: #06b6d4;
        --ce-green: #10b981;
        --ce-amber: #f59e0b;
        --ce-red: #ef4444;
        --ce-radius: 16px;
        --ce-shadow: 0 4px 24px rgba(0,0,0,0.07);
        --ce-ease: cubic-bezier(0.16,1,0.3,1);

        font-family: 'Inter', 'Segoe UI', -apple-system, sans-serif;
        background: var(--ce-bg);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ── TOPBAR ── */
    .ce-topbar {
        position: sticky;
        top: 32px;
        z-index: 100;
        background: var(--ce-white);
        border-bottom: 1px solid var(--ce-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 28px;
        height: 60px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    }
    .admin-bar .ce-topbar { top: 32px; }
    @media (max-width: 782px) { .admin-bar .ce-topbar { top: 46px; } }

    .ce-topbar__left { display: flex; align-items: center; gap: 16px; }
    .ce-topbar__back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--ce-gray);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 8px;
        transition: all .2s;
    }
    .ce-topbar__back:hover { background: var(--ce-light); color: var(--ce-dark); }
    .ce-topbar__back svg { width: 16px; height: 16px; }

    .ce-topbar__title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ce-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ce-topbar__type {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--ce-blue);
        background: #eff6ff;
        padding: 3px 10px;
        border-radius: 100px;
    }

    .ce-topbar__right { display: flex; align-items: center; gap: 10px; }
    .ce-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        border: none;
        line-height: 1;
    }
    .ce-btn svg { width: 15px; height: 15px; flex-shrink: 0; }
    .ce-btn--ghost {
        background: transparent;
        color: var(--ce-gray);
        border: 1.5px solid var(--ce-border);
    }
    .ce-btn--ghost:hover { background: var(--ce-light); color: var(--ce-dark); border-color: #cbd5e1; }
    .ce-btn--primary {
        background: linear-gradient(135deg, var(--ce-blue) 0%, var(--ce-blue-d) 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37,99,235,.35);
    }
    .ce-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,.4); }
    .ce-btn--success {
        background: linear-gradient(135deg, var(--ce-green) 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16,185,129,.3);
    }
    .ce-btn--success:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,.4); }
    .ce-btn--danger { background: #fef2f2; color: var(--ce-red); border: 1.5px solid #fecaca; }
    .ce-btn--danger:hover { background: var(--ce-red); color: #fff; border-color: var(--ce-red); }

    .ce-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 100px;
    }
    .ce-status-badge--draft { background: #fff7ed; color: #c2410c; }
    .ce-status-badge--publish { background: #f0fdf4; color: #16a34a; }
    .ce-status-badge__dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

    /* ── MAIN LAYOUT ── */
    .ce-body {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        padding: 24px 28px;
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
        flex: 1;
    }
    @media (max-width: 1100px) { .ce-body { grid-template-columns: 1fr; } }

    /* ── CARD ── */
    .ce-card {
        background: var(--ce-white);
        border-radius: var(--ce-radius);
        border: 1px solid var(--ce-border);
        box-shadow: var(--ce-shadow);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .ce-card__head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--ce-border);
        background: var(--ce-light);
    }
    .ce-card__head-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .ce-card__head-icon.blue { background: #eff6ff; }
    .ce-card__head-icon.green { background: #f0fdf4; }
    .ce-card__head-icon.amber { background: #fffbeb; }
    .ce-card__head-icon.purple { background: #f5f3ff; }
    .ce-card__head-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--ce-dark);
    }
    .ce-card__head-desc {
        font-size: 12px;
        color: var(--ce-gray);
        margin-left: auto;
    }
    .ce-card__body { padding: 20px; }

    /* ── FORM FIELDS ── */
    .ce-field { margin-bottom: 18px; }
    .ce-field:last-child { margin-bottom: 0; }
    .ce-field__label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: var(--ce-gray);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 7px;
    }
    .ce-field__label .ce-required {
        color: var(--ce-red);
        font-size: 14px;
        line-height: 1;
    }
    .ce-field__input,
    .ce-field__textarea,
    .ce-field__select {
        width: 100%;
        border: 1.5px solid var(--ce-border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        font-family: inherit;
        color: var(--ce-dark);
        background: var(--ce-white);
        box-sizing: border-box;
        transition: border-color .2s, box-shadow .2s;
        line-height: 1.5;
    }
    .ce-field__input:focus,
    .ce-field__textarea:focus,
    .ce-field__select:focus {
        outline: none;
        border-color: var(--ce-blue);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .ce-field__input--title {
        font-size: 22px;
        font-weight: 700;
        padding: 12px 16px;
        border-radius: 12px;
    }
    .ce-field__textarea { resize: vertical; min-height: 90px; }
    .ce-field__hint { font-size: 11.5px; color: #94a3b8; margin-top: 5px; }

    .ce-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ce-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
    @media (max-width: 700px) { .ce-grid-2, .ce-grid-3 { grid-template-columns: 1fr; } }

    /* ── THUMBNAIL UPLOAD ── */
    .ce-thumb {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        aspect-ratio: 16/9;
        cursor: pointer;
        border: 2px dashed var(--ce-border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        transition: all .25s;
    }
    .ce-thumb:hover { border-color: var(--ce-blue); }
    .ce-thumb img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
    }
    .ce-thumb__placeholder { text-align: center; color: rgba(255,255,255,.5); }
    .ce-thumb__placeholder svg { width: 40px; height: 40px; margin: 0 auto 8px; display: block; opacity: .5; }
    .ce-thumb__placeholder p { font-size: 12px; font-weight: 600; }
    .ce-thumb__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity .2s;
        gap: 10px;
    }
    .ce-thumb:hover .ce-thumb__overlay { opacity: 1; }
    .ce-thumb__action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 8px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
    }
    .ce-thumb__action:hover { background: rgba(255,255,255,.25); }
    .ce-thumb__action svg { width: 14px; height: 14px; }

    /* ── TOGGLE SWITCH ── */
    .ce-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: var(--ce-light);
        border: 1px solid var(--ce-border);
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background .2s;
    }
    .ce-toggle:hover { background: #f1f5f9; }
    .ce-toggle__label { font-size: 13px; font-weight: 600; color: var(--ce-dark); display: flex; align-items: center; gap: 8px; }
    .ce-toggle__desc { font-size: 11.5px; color: var(--ce-gray); }
    .ce-toggle input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 40px;
        height: 22px;
        background: #cbd5e1;
        border-radius: 100px;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        transition: background .2s;
    }
    .ce-toggle input[type="checkbox"]::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 16px;
        height: 16px;
        background: #fff;
        border-radius: 50%;
        transition: transform .2s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .ce-toggle input[type="checkbox"]:checked { background: var(--ce-blue); }
    .ce-toggle input[type="checkbox"]:checked::after { transform: translateX(18px); }

    /* ── SLUG PREVIEW ── */
    .ce-slug-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--ce-light);
        border: 1px solid var(--ce-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 12px;
        color: var(--ce-gray);
        margin-top: -10px;
        margin-bottom: 18px;
    }
    .ce-slug-bar strong { color: var(--ce-blue); word-break: break-all; }
    .ce-slug-bar svg { width: 13px; height: 13px; flex-shrink: 0; }

    /* ── TAGS / HIGHLIGHTS ── */
    .ce-tags-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-height: 38px;
        padding: 8px;
        border: 1.5px solid var(--ce-border);
        border-radius: 10px;
        background: var(--ce-white);
        transition: border-color .2s;
        cursor: text;
    }
    .ce-tags-wrap:focus-within { border-color: var(--ce-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .ce-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        color: var(--ce-blue);
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 100px;
        cursor: default;
    }
    .ce-tag__remove {
        cursor: pointer;
        opacity: .6;
        font-size: 14px;
        line-height: 1;
        transition: opacity .15s;
    }
    .ce-tag__remove:hover { opacity: 1; }
    .ce-tags-input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
        min-width: 120px;
        flex: 1;
        padding: 2px 4px;
        color: var(--ce-dark);
    }

    /* ── PREVIEW PANEL ── */
    .ce-preview-card {
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        aspect-ratio: 4/5;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .ce-preview-card img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .75;
    }
    .ce-preview-card__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 30%, rgba(0,0,0,.8) 100%);
    }
    .ce-preview-card__body {
        position: relative;
        z-index: 2;
        padding: 20px;
    }
    .ce-preview-card__badge {
        display: inline-block;
        background: #f97316;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 3px 10px;
        border-radius: 100px;
        margin-bottom: 8px;
    }
    .ce-preview-card__title {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 4px;
        line-height: 1.3;
    }
    .ce-preview-card__sub { font-size: 12px; color: rgba(255,255,255,.7); margin: 0; }

    /* ── NOTICE ── */
    .ce-notice {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }
    .ce-notice svg { width: 16px; height: 16px; flex-shrink: 0; }
    .ce-notice--info { background: #eff6ff; color: #1d4ed8; }
    .ce-notice--success { background: #f0fdf4; color: #15803d; }
    .ce-notice--warn { background: #fffbeb; color: #b45309; }

    /* ── SECTION DIVIDER ── */
    .ce-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 4px 0 16px;
        color: var(--ce-gray);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
    }
    .ce-divider::before, .ce-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--ce-border);
    }

    /* ── ANIMATIONS ── */
    @keyframes ceFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    #brd-custom-editor { animation: ceFadeIn .35s var(--ce-ease) both; }

    /* Spinner */
    @keyframes ceSpin { to { transform: rotate(360deg); } }
    .ce-spin { animation: ceSpin .8s linear infinite; }
    </style>
    <?php
}

// ============================================================
// 4. INJECT HTML VÀO EDITOR (sau titlediv)
// ============================================================
add_action('edit_form_after_title', 'brd_inject_custom_editor');
function brd_inject_custom_editor($post) {
    if ($post->post_type !== 'destination') return;

    // Lấy dữ liệu
    $post_id     = $post->ID;
    $title       = $post->post_title;
    $status      = $post->post_status;
    $slug        = $post->post_name ?: sanitize_title($title);
    $excerpt     = $post->post_excerpt;

    $tagline      = get_post_meta($post_id, '_dest_tagline',     true);
    $hotel_count  = get_post_meta($post_id, '_dest_hotel_count', true);
    $country      = get_post_meta($post_id, '_dest_country',     true) ?: 'Việt Nam';
    $region       = get_post_meta($post_id, '_dest_region',      true);
    $highlight    = get_post_meta($post_id, '_dest_highlight',   true);
    $search_link  = get_post_meta($post_id, '_dest_search_link', true);
    $badge        = get_post_meta($post_id, '_dest_badge',       true);
    $featured     = get_post_meta($post_id, '_dest_featured',    true);
    $show_home    = get_post_meta($post_id, '_dest_show_home',   true);
    $home_order   = get_post_meta($post_id, '_dest_home_order',  true) ?: 99;
    $sort_order   = get_post_meta($post_id, '_dest_sort_order',  true) ?: 0;

    $thumb_id     = get_post_thumbnail_id($post_id);
    $thumb_url    = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';

    $status_label = $status === 'publish' ? 'Đã xuất bản' : 'Bản nháp';
    $status_cls   = $status === 'publish' ? 'publish' : 'draft';
    $site_url     = trailingslashit(get_home_url()) . 'destination/';
    $tags_arr     = array_filter(array_map('trim', explode(',', $highlight)));

    wp_nonce_field('brd_custom_editor_save', 'brd_ce_nonce');
    ?>
    <div id="brd-custom-editor">

        <!-- ══ TOPBAR ══ -->
        <div class="ce-topbar">
            <div class="ce-topbar__left">
                <a href="<?php echo admin_url('edit.php?post_type=destination'); ?>" class="ce-topbar__back">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Danh sách
                </a>
                <div class="ce-topbar__title">
                    <span>🗺️</span>
                    <?php echo $post_id ? 'Chỉnh sửa điểm đến' : 'Thêm điểm đến mới'; ?>
                    <span class="ce-topbar__type">Điểm đến</span>
                </div>
            </div>
            <div class="ce-topbar__right">
                <span class="ce-status-badge ce-status-badge--<?php echo esc_attr($status_cls); ?>">
                    <span class="ce-status-badge__dot"></span>
                    <?php echo esc_html($status_label); ?>
                </span>

                <?php if ($post_id && $status === 'publish') : ?>
                <a href="<?php echo get_permalink($post_id); ?>" target="_blank" class="ce-btn ce-btn--ghost">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Xem trang
                </a>
                <?php endif; ?>

                <button type="button" class="ce-btn ce-btn--ghost" onclick="brdSaveDraft()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Lưu nháp
                </button>

                <button type="button" class="ce-btn ce-btn--primary" onclick="brdPublish()" id="brd-publish-btn">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php echo $status === 'publish' ? 'Cập nhật' : 'Xuất bản'; ?>
                </button>
            </div>
        </div>

        <!-- ══ BODY ══ -->
        <div class="ce-body">

            <!-- ══ CỘT TRÁI ══ -->
            <div class="ce-main">

                <!-- Card: Tên & Slug -->
                <div class="ce-card">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon blue">📝</div>
                        <div class="ce-card__head-title">Thông tin cơ bản</div>
                        <div class="ce-card__head-desc">Tên & đường dẫn điểm đến</div>
                    </div>
                    <div class="ce-card__body">
                        <div class="ce-field">
                            <label class="ce-field__label">
                                Tên điểm đến <span class="ce-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="brd_ce_title"
                                name="post_title"
                                class="ce-field__input ce-field__input--title"
                                value="<?php echo esc_attr($title); ?>"
                                placeholder="VD: Hội An, Đà Lạt, Phú Quốc..."
                                oninput="brdUpdateSlug(this.value); brdUpdatePreview();"
                                autocomplete="off"
                            >
                        </div>
                        <div class="ce-slug-bar">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <?php echo esc_html($site_url); ?><strong id="brd-slug-preview"><?php echo esc_html($slug ?: 'duong-dan-tu-dong'); ?></strong>
                            <input type="hidden" name="post_name" id="brd_ce_slug" value="<?php echo esc_attr($slug); ?>">
                        </div>

                        <div class="ce-grid-2">
                            <div class="ce-field">
                                <label class="ce-field__label">🌍 Quốc gia</label>
                                <input type="text" name="brd_country" class="ce-field__input" value="<?php echo esc_attr($country); ?>" placeholder="Việt Nam">
                            </div>
                            <div class="ce-field">
                                <label class="ce-field__label">📌 Vùng / Miền</label>
                                <input type="text" name="brd_region" class="ce-field__input" value="<?php echo esc_attr($region); ?>" placeholder="VD: Miền Trung">
                            </div>
                        </div>

                        <div class="ce-field">
                            <label class="ce-field__label">📝 Slogan / Mô tả ngắn</label>
                            <input type="text" name="brd_tagline" id="brd_ce_tagline" class="ce-field__input" value="<?php echo esc_attr($tagline); ?>" placeholder="VD: Thành phố đáng sống nhất Việt Nam" oninput="brdUpdatePreview()">
                            <div class="ce-field__hint">Hiển thị dưới tên điểm đến trong card</div>
                        </div>

                        <div class="ce-field">
                            <label class="ce-field__label">📄 Mô tả chi tiết (Excerpt)</label>
                            <textarea name="excerpt" class="ce-field__textarea" rows="4" placeholder="Mô tả dài hơn về điểm đến này, ẩm thực, văn hóa, khí hậu..."><?php echo esc_textarea($excerpt); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card: Ảnh đại diện -->
                <div class="ce-card">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon green">🖼️</div>
                        <div class="ce-card__head-title">Ảnh đại diện</div>
                        <div class="ce-card__head-desc">Nhấp vào ảnh để thay đổi</div>
                    </div>
                    <div class="ce-card__body">
                        <input type="hidden" name="_thumbnail_id" id="brd_thumb_id" value="<?php echo esc_attr($thumb_id ?: ''); ?>">
                        <div class="ce-thumb" id="brd-thumb-wrap" onclick="brdOpenMedia()">
                            <?php if ($thumb_url): ?>
                                <img src="<?php echo esc_url($thumb_url); ?>" id="brd-thumb-img" alt="Ảnh đại diện">
                            <?php else: ?>
                                <div class="ce-thumb__placeholder" id="brd-thumb-placeholder">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p>Nhấp để chọn ảnh</p>
                                    <p style="font-size:11px;opacity:.6;">Tỷ lệ 4:3 hoặc 16:9 tốt nhất</p>
                                </div>
                            <?php endif; ?>
                            <div class="ce-thumb__overlay">
                                <span class="ce-thumb__action">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?php echo $thumb_url ? 'Thay ảnh' : 'Chọn ảnh'; ?>
                                </span>
                                <?php if ($thumb_url): ?>
                                <span class="ce-thumb__action" onclick="event.stopPropagation(); brdRemoveThumb();">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Xóa ảnh
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Chi tiết & SEO -->
                <div class="ce-card">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon amber">⚙️</div>
                        <div class="ce-card__head-title">Chi tiết & Thông số</div>
                        <div class="ce-card__head-desc">Số liệu và thông tin bổ sung</div>
                    </div>
                    <div class="ce-card__body">
                        <div class="ce-grid-2">
                            <div class="ce-field">
                                <label class="ce-field__label">🏨 Số khách sạn</label>
                                <input type="text" name="brd_hotel_count" class="ce-field__input" value="<?php echo esc_attr($hotel_count); ?>" placeholder="VD: 124 Khách sạn">
                            </div>
                            <div class="ce-field">
                                <label class="ce-field__label">🏷️ Badge (Nhãn nổi bật)</label>
                                <input type="text" name="brd_badge" id="brd_ce_badge" class="ce-field__input" value="<?php echo esc_attr($badge); ?>" placeholder="VD: Hot, Mới, Xu hướng" oninput="brdUpdatePreview()">
                            </div>
                        </div>

                        <div class="ce-divider">Điểm nổi bật</div>

                        <div class="ce-field">
                            <label class="ce-field__label">✨ Điểm nổi bật (nhấn Enter hoặc dấu phẩy để thêm)</label>
                            <div class="ce-tags-wrap" id="brd-tags-wrap" onclick="document.getElementById('brd-tags-input').focus()">
                                <?php foreach ($tags_arr as $tag): ?>
                                <span class="ce-tag">
                                    <?php echo esc_html($tag); ?>
                                    <span class="ce-tag__remove" onclick="brdRemoveTag(this)">×</span>
                                </span>
                                <?php endforeach; ?>
                                <input type="text" id="brd-tags-input" class="ce-tags-input" placeholder="Thêm điểm nổi bật...">
                            </div>
                            <input type="hidden" name="brd_highlight" id="brd_highlight_val" value="<?php echo esc_attr($highlight); ?>">
                            <div class="ce-field__hint">VD: Bãi biển đẹp, Ẩm thực phong phú, Di sản UNESCO</div>
                        </div>

                        <div class="ce-field">
                            <label class="ce-field__label">🔗 Link tìm phòng tùy chỉnh</label>
                            <input type="url" name="brd_search_link" class="ce-field__input" value="<?php echo esc_attr($search_link); ?>" placeholder="https://... (bỏ trống = tự động)">
                            <div class="ce-field__hint">Nếu để trống sẽ tự link đến trang tìm phòng theo tên điểm đến</div>
                        </div>
                    </div>
                </div>

            </div><!-- /ce-main -->

            <!-- ══ CỘT PHẢI ══ -->
            <div class="ce-sidebar">

                <!-- Preview Card -->
                <div class="ce-card" style="margin-bottom:20px;">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon purple">👁️</div>
                        <div class="ce-card__head-title">Preview Card</div>
                    </div>
                    <div class="ce-card__body" style="padding:14px;">
                        <div class="ce-preview-card" id="brd-preview-card">
                            <img src="<?php echo esc_url($thumb_url ?: 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=600&q=80'); ?>" id="brd-preview-img" alt="">
                            <div class="ce-preview-card__overlay"></div>
                            <div class="ce-preview-card__body">
                                <?php if ($badge): ?>
                                <div class="ce-preview-card__badge" id="brd-preview-badge"><?php echo esc_html($badge); ?></div>
                                <?php else: ?>
                                <div class="ce-preview-card__badge" id="brd-preview-badge" style="display:none;"><?php echo esc_html($badge); ?></div>
                                <?php endif; ?>
                                <h3 class="ce-preview-card__title" id="brd-preview-title"><?php echo esc_html($title ?: 'Tên điểm đến'); ?></h3>
                                <p class="ce-preview-card__sub" id="brd-preview-sub"><?php echo esc_html($tagline ?: 'Mô tả ngắn về điểm đến...'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trạng thái & Xuất bản -->
                <div class="ce-card" style="margin-bottom:20px;">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon blue">🚀</div>
                        <div class="ce-card__head-title">Xuất bản</div>
                    </div>
                    <div class="ce-card__body">
                        <div class="ce-field">
                            <label class="ce-field__label">Trạng thái</label>
                            <select name="post_status" class="ce-field__select">
                                <option value="draft" <?php selected($status, 'draft'); ?>>📝 Bản nháp</option>
                                <option value="publish" <?php selected($status, 'publish'); ?>>✅ Xuất bản</option>
                                <option value="private" <?php selected($status, 'private'); ?>>🔒 Riêng tư</option>
                            </select>
                        </div>

                        <div class="ce-grid-2" style="margin-top:4px;">
                            <div class="ce-field">
                                <label class="ce-field__label">📶 Thứ tự</label>
                                <input type="number" name="brd_sort_order" class="ce-field__input" value="<?php echo esc_attr($sort_order); ?>" min="0" placeholder="0">
                            </div>
                            <div class="ce-field">
                                <label class="ce-field__label">🏠 Thứ tự TC</label>
                                <input type="number" name="brd_home_order" class="ce-field__input" value="<?php echo esc_attr($home_order); ?>" min="1" max="999" placeholder="99">
                            </div>
                        </div>

                        <button type="button" class="ce-btn ce-btn--primary" style="width:100%;justify-content:center;margin-top:4px;" onclick="brdPublish()" id="brd-publish-btn-2">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <?php echo $status === 'publish' ? 'Cập nhật' : 'Xuất bản'; ?>
                        </button>

                        <?php if ($post_id): ?>
                        <button type="button" class="ce-btn ce-btn--danger" style="width:100%;justify-content:center;margin-top:8px;" onclick="brdTrash()">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Chuyển vào thùng rác
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tùy chọn hiển thị -->
                <div class="ce-card">
                    <div class="ce-card__head">
                        <div class="ce-card__head-icon amber">🖥️</div>
                        <div class="ce-card__head-title">Tùy chọn hiển thị</div>
                    </div>
                    <div class="ce-card__body">
                        <label class="ce-toggle">
                            <span class="ce-toggle__label">
                                ⭐ Điểm đến nổi bật
                                <span class="ce-toggle__desc" style="display:block;font-weight:400;font-size:11px;color:#94a3b8;margin-top:1px;">Hiển thị nhãn nổi bật</span>
                            </span>
                            <input type="checkbox" name="brd_featured" value="1" <?php checked($featured, '1'); ?>>
                        </label>
                        <label class="ce-toggle">
                            <span class="ce-toggle__label">
                                🏠 Hiển thị trang chủ
                                <span class="ce-toggle__desc" style="display:block;font-weight:400;font-size:11px;color:#94a3b8;margin-top:1px;">Đưa vào section Điểm đến</span>
                            </span>
                            <input type="checkbox" name="brd_show_home" value="1" <?php checked($show_home, '1'); ?>>
                        </label>

                        <div class="ce-notice ce-notice--info" style="margin-top:8px;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tối đa 8 điểm đến trên trang chủ, sắp xếp theo Thứ tự TC.
                        </div>
                    </div>
                </div>

            </div><!-- /ce-sidebar -->
        </div><!-- /ce-body -->
    </div><!-- #brd-custom-editor -->

    <script>
    (function() {
        // ── Media Uploader ──────────────────────────────────────────────
        var mediaFrame;
        window.brdOpenMedia = function() {
            if (mediaFrame) { mediaFrame.open(); return; }
            mediaFrame = wp.media({
                title: 'Chọn ảnh đại diện cho điểm đến',
                button: { text: 'Đặt làm ảnh đại diện' },
                multiple: false,
                library: { type: 'image' }
            });
            mediaFrame.on('select', function() {
                var att = mediaFrame.state().get('selection').first().toJSON();
                document.getElementById('brd_thumb_id').value = att.id;
                // Update wrap
                var wrap = document.getElementById('brd-thumb-wrap');
                var ph = document.getElementById('brd-thumb-placeholder');
                if (ph) ph.style.display = 'none';
                var img = document.getElementById('brd-thumb-img');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'brd-thumb-img';
                    wrap.insertBefore(img, wrap.firstChild);
                }
                img.src = att.url;
                // Update preview
                var previewImg = document.getElementById('brd-preview-img');
                if (previewImg) previewImg.src = att.url;
            });
            mediaFrame.open();
        };

        window.brdRemoveThumb = function() {
            document.getElementById('brd_thumb_id').value = '';
            var img = document.getElementById('brd-thumb-img');
            if (img) img.remove();
            var ph = document.getElementById('brd-thumb-placeholder');
            if (ph) ph.style.display = '';
            var previewImg = document.getElementById('brd-preview-img');
            if (previewImg) previewImg.src = 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=600&q=80';
        };

        // ── Live Preview ────────────────────────────────────────────────
        window.brdUpdatePreview = function() {
            var title = document.getElementById('brd_ce_title').value || 'Tên điểm đến';
            var sub   = document.querySelector('[name="brd_tagline"]').value || 'Mô tả ngắn về điểm đến...';
            var badge = document.getElementById('brd_ce_badge') ? document.getElementById('brd_ce_badge').value : '';

            document.getElementById('brd-preview-title').textContent = title;
            document.getElementById('brd-preview-sub').textContent   = sub;
            var bd = document.getElementById('brd-preview-badge');
            if (bd) {
                bd.textContent    = badge;
                bd.style.display  = badge ? '' : 'none';
            }
        };

        // ── Slug Generator ──────────────────────────────────────────────
        window.brdUpdateSlug = function(val) {
            // Chỉ tự update slug nếu là bài mới (post_name trống)
            var slugInput = document.getElementById('brd_ce_slug');
            var slugPrev  = document.getElementById('brd-slug-preview');
            var slug = val
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            if (slugInput.dataset.auto !== 'false') {
                slugInput.value = slug;
                slugPrev.textContent = slug || 'duong-dan-tu-dong';
            }
        };
        // Khi user tự sửa slug -> không auto nữa
        document.getElementById('brd_ce_slug').addEventListener('input', function() {
            this.dataset.auto = 'false';
            document.getElementById('brd-slug-preview').textContent = this.value || 'duong-dan-tu-dong';
        });
        // Init auto flag
        var slugInput = document.getElementById('brd_ce_slug');
        slugInput.dataset.auto = slugInput.value ? 'false' : 'true';

        // ── Tags / Highlights ───────────────────────────────────────────
        function syncTags() {
            var wrap = document.getElementById('brd-tags-wrap');
            var tags = Array.from(wrap.querySelectorAll('.ce-tag')).map(function(t) {
                return t.firstChild.nodeValue.trim();
            });
            document.getElementById('brd_highlight_val').value = tags.join(', ');
        }
        function addTag(val) {
            val = val.trim();
            if (!val) return;
            var wrap = document.getElementById('brd-tags-wrap');
            var inp  = document.getElementById('brd-tags-input');
            var tag = document.createElement('span');
            tag.className = 'ce-tag';
            tag.innerHTML = val + '<span class="ce-tag__remove" onclick="brdRemoveTag(this)">×</span>';
            wrap.insertBefore(tag, inp);
            inp.value = '';
            syncTags();
        }
        window.brdRemoveTag = function(btn) {
            btn.parentElement.remove();
            syncTags();
        };
        document.getElementById('brd-tags-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(this.value.replace(',', ''));
            } else if (e.key === 'Backspace' && !this.value) {
                var tags = document.querySelectorAll('#brd-tags-wrap .ce-tag');
                if (tags.length) tags[tags.length - 1].remove();
                syncTags();
            }
        });
        document.getElementById('brd-tags-input').addEventListener('blur', function() {
            if (this.value.trim()) addTag(this.value);
        });

        // ── Save / Publish ──────────────────────────────────────────────
        function brdSetSaving(on) {
            var btns = [document.getElementById('brd-publish-btn'), document.getElementById('brd-publish-btn-2')];
            btns.forEach(function(btn) {
                if (!btn) return;
                if (on) {
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="ce-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Đang lưu...';
                } else {
                    btn.disabled = false;
                }
            });
        }

        window.brdPublish = function() {
            document.querySelector('[name="post_status"]').value = 'publish';
            brdSetSaving(true);
            document.getElementById('post').submit();
        };
        window.brdSaveDraft = function() {
            document.querySelector('[name="post_status"]').value = 'draft';
            brdSetSaving(true);
            document.getElementById('post').submit();
        };
        window.brdTrash = function() {
            if (!confirm('Bạn có chắc muốn chuyển điểm đến này vào thùng rác?')) return;
            document.querySelector('[name="post_status"]').value = 'trash';
            document.getElementById('post').submit();
        };

        // ── Keyboard shortcut Ctrl+S ────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                brdPublish();
            }
        });

    })();
    </script>
    <?php
}

// ============================================================
// 5. XỬ LÝ SAVE – GHI THUMBNAIL VÀO POST
// ============================================================
add_action('save_post_destination', 'brd_custom_editor_save_thumb', 10, 2);
function brd_custom_editor_save_thumb($post_id, $post) {
    if (!isset($_POST['brd_ce_nonce'])) return;
    if (!wp_verify_nonce($_POST['brd_ce_nonce'], 'brd_custom_editor_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Lưu thumbnail
    if (isset($_POST['_thumbnail_id'])) {
        $thumb_id = intval($_POST['_thumbnail_id']);
        if ($thumb_id > 0) {
            set_post_thumbnail($post_id, $thumb_id);
        } else {
            delete_post_thumbnail($post_id);
        }
    }
}
