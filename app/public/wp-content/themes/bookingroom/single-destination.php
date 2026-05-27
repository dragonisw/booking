<?php
/**
 * Template: single-destination.php
 * Trang chi tiết điểm đến
 */
get_header();

$pid         = get_the_ID();
$title       = get_the_title();
$thumb       = get_the_post_thumbnail_url($pid, 'full');
$hotel_count = get_post_meta($pid, '_dest_hotel_count', true) ?: '0 Khách sạn';
$tagline     = get_post_meta($pid, '_dest_tagline', true);
$country     = get_post_meta($pid, '_dest_country', true) ?: 'Việt Nam';
$region      = get_post_meta($pid, '_dest_region', true);
$highlight   = get_post_meta($pid, '_dest_highlight', true);
$badge       = get_post_meta($pid, '_dest_badge', true);
$search_link = get_post_meta($pid, '_dest_search_link', true);
$book_link   = !empty($search_link) ? $search_link : esc_url(add_query_arg('s', $title, home_url('/rooms')));
$highlights  = $highlight ? array_map('trim', explode(',', $highlight)) : array();

if (!$thumb) {
    $thumb = 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=1600&q=80';
}

// Related destinations
$related = get_posts(array(
    'post_type'      => 'destination',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'post__not_in'   => array($pid),
    'orderby'        => 'rand',
));
?>

<style>
    /* ===== SINGLE DESTINATION STYLES ===== */
    .dest-hero {
        position: relative;
        height: 65vh;
        min-height: 420px;
        max-height: 680px;
        overflow: hidden;
    }
    .dest-hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.04);
        animation: destZoom 8s ease-out forwards;
    }
    @keyframes destZoom {
        from { transform: scale(1.08); }
        to   { transform: scale(1); }
    }
    .dest-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,.2) 0%, rgba(0,0,0,.65) 100%);
    }
    .dest-hero-content {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        padding: 48px 24px 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .dest-badge {
        display: inline-block;
        background: #f97316;
        color: #fff;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .dest-hero-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 900;
        color: #fff;
        margin: 0 0 8px;
        text-shadow: 0 4px 20px rgba(0,0,0,.4);
    }
    .dest-hero-sub {
        font-size: 1rem;
        color: rgba(255,255,255,.82);
        margin: 0;
    }
    .dest-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px 80px;
    }
    .dest-info-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(0,0,0,.10);
        padding: 32px 36px;
        margin-top: -56px;
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 32px;
        flex-wrap: wrap;
        border: 1px solid rgba(255,255,255,.8);
    }
    .dest-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        flex: 1;
        min-width: 100px;
        text-align: center;
    }
    .dest-stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #2563eb;
    }
    .dest-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .07em;
    }
    .dest-stat-divider {
        width: 1px;
        height: 48px;
        background: #f1f5f9;
        flex-shrink: 0;
    }
    .dest-book-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(37,99,235,.35);
        transition: all .25s;
        white-space: nowrap;
    }
    .dest-book-btn:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        box-shadow: 0 8px 30px rgba(37,99,235,.45);
        transform: translateY(-2px);
        color: #fff;
    }
    .dest-section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dest-highlight-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        transition: all .2s;
    }
    .dest-highlight-tag:hover {
        background: #0ea5e9;
        color: #fff;
        border-color: #0ea5e9;
        transform: translateY(-2px);
    }
    .dest-content-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 40px;
        margin-top: 40px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .dest-content-grid { grid-template-columns: 1fr; }
        .dest-info-card { gap: 16px; padding: 20px; margin-top: -32px; }
        .dest-hero-content { padding: 24px 20px 28px; }
    }
    .dest-sidebar-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,.08);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        position: sticky;
        top: 100px;
    }
    .dest-related-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 16px;
    }
    .dest-related-card {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        height: 180px;
        display: block;
        text-decoration: none;
    }
    .dest-related-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .5s;
    }
    .dest-related-card:hover img { transform: scale(1.08); }
    .dest-related-card .overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(0,0,0,.75));
    }
    .dest-related-card .info {
        position: absolute;
        bottom: 10px;
        left: 12px;
        right: 12px;
    }
    .dest-related-card .info h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }
    .dest-related-card .info p {
        margin: 2px 0 0;
        font-size: 11px;
        color: rgba(255,255,255,.7);
    }
    .breadcrumb-bar {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }
    .breadcrumb-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
    }
    .breadcrumb-inner a { color: #64748b; text-decoration: none; }
    .breadcrumb-inner a:hover { color: #2563eb; }
    .breadcrumb-inner span.sep { color: #cbd5e1; }
    .breadcrumb-inner span.current { color: #1e293b; font-weight: 600; }
</style>

<main>
    <?php while (have_posts()) : the_post(); ?>

    <!-- Breadcrumbs -->
    <div class="breadcrumb-bar">
        <div class="breadcrumb-inner">
            <a href="<?php echo home_url(); ?>">🏠 Trang chủ</a>
            <span class="sep">/</span>
            <a href="<?php echo get_post_type_archive_link('destination'); ?>">Điểm đến</a>
            <span class="sep">/</span>
            <span class="current"><?php echo esc_html($title); ?></span>
        </div>
    </div>

    <!-- Hero -->
    <div class="dest-hero">
        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" class="dest-hero-img">
        <div class="dest-hero-overlay"></div>
        <div class="dest-hero-content">
            <?php if ($badge): ?>
                <span class="dest-badge"><?php echo esc_html($badge); ?></span>
            <?php endif; ?>
            <h1 class="dest-hero-title"><?php echo esc_html($title); ?></h1>
            <p class="dest-hero-sub">
                📍 <?php echo esc_html($region ? $region . ', ' : ''); echo esc_html($country); ?>
                <?php if ($hotel_count): ?> &nbsp;·&nbsp; 🏨 <?php echo esc_html($hotel_count); ?><?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Info card -->
    <div class="dest-main">
        <div class="dest-info-card">
            <?php if ($hotel_count): ?>
            <div class="dest-stat">
                <span class="dest-stat-value"><?php echo esc_html(preg_replace('/[^0-9+]/', '', $hotel_count) ?: $hotel_count); ?></span>
                <span class="dest-stat-label">Khách sạn</span>
            </div>
            <div class="dest-stat-divider"></div>
            <?php endif; ?>
            <?php if ($country): ?>
            <div class="dest-stat">
                <span class="dest-stat-value" style="font-size:1.2rem;">🌏</span>
                <span class="dest-stat-label"><?php echo esc_html($country); ?></span>
            </div>
            <div class="dest-stat-divider"></div>
            <?php endif; ?>
            <?php if ($region): ?>
            <div class="dest-stat">
                <span class="dest-stat-value" style="font-size:1.2rem;">📌</span>
                <span class="dest-stat-label"><?php echo esc_html($region); ?></span>
            </div>
            <div class="dest-stat-divider"></div>
            <?php endif; ?>
            <div style="margin-left:auto;">
                <a href="<?php echo esc_url($book_link); ?>" class="dest-book-btn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tìm phòng tại <?php echo esc_html($title); ?>
                </a>
            </div>
        </div>

        <!-- Content + Sidebar -->
        <div class="dest-content-grid">
            <!-- Main content -->
            <div>
                <?php if ($tagline): ?>
                <p style="font-size:1.1rem;color:#475569;line-height:1.8;margin:32px 0 24px;font-style:italic;border-left:4px solid #2563eb;padding-left:16px;">
                    <?php echo esc_html($tagline); ?>
                </p>
                <?php endif; ?>

                <?php if (has_excerpt() || get_the_content()): ?>
                <div style="background:#fff;border-radius:16px;padding:28px 32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f1f5f9;margin-bottom:28px;">
                    <h2 class="dest-section-title">🗺️ Giới thiệu</h2>
                    <div style="color:#475569;line-height:1.8;font-size:15px;">
                        <?php the_content(); ?>
                        <?php if (!get_the_content() && has_excerpt()): ?>
                            <?php the_excerpt(); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($highlights)): ?>
                <div style="background:#fff;border-radius:16px;padding:28px 32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f1f5f9;margin-bottom:28px;">
                    <h2 class="dest-section-title">✨ Điểm nổi bật</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <?php foreach ($highlights as $hl): ?>
                            <span class="dest-highlight-tag">✓ <?php echo esc_html($hl); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Rooms at this destination -->
                <?php
                $rooms = get_posts(array(
                    'post_type'      => 'room',
                    'posts_per_page' => 3,
                    's'              => $title,
                    'post_status'    => 'publish',
                ));
                if (!$rooms) {
                    $rooms = get_posts(array('post_type' => 'room', 'posts_per_page' => 3, 'post_status' => 'publish'));
                }
                if ($rooms):
                ?>
                <div style="background:#fff;border-radius:16px;padding:28px 32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                        <h2 class="dest-section-title" style="margin:0;">🛏️ Phòng nghỉ gợi ý</h2>
                        <a href="<?php echo esc_url($book_link); ?>" style="color:#2563eb;font-weight:700;font-size:13px;text-decoration:none;">Xem tất cả →</a>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                    <?php foreach ($rooms as $room):
                        $room_price = get_post_meta($room->ID, '_price', true) ?: 1500000;
                        $room_thumb = get_the_post_thumbnail_url($room->ID, 'medium');
                    ?>
                        <a href="<?php echo get_permalink($room->ID); ?>" style="display:flex;gap:16px;align-items:center;padding:14px;background:#f8fafc;border-radius:12px;text-decoration:none;transition:background .2s;" onmouseenter="this.style.background='#eff6ff'" onmouseleave="this.style.background='#f8fafc'">
                            <img src="<?php echo $room_thumb ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=200'; ?>" alt="" style="width:80px;height:64px;object-fit:cover;border-radius:10px;flex-shrink:0;">
                            <div style="flex:1;min-width:0;">
                                <h4 style="margin:0 0 4px;font-size:14px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html($room->post_title); ?></h4>
                                <p style="margin:0;font-size:12px;color:#94a3b8;">Từ <strong style="color:#f97316;"><?php echo number_format($room_price); ?>đ</strong>/đêm</p>
                            </div>
                            <svg width="16" height="16" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="dest-sidebar-card">
                    <div style="background:linear-gradient(135deg,#1e40af,#2563eb);padding:24px 24px 20px;color:#fff;">
                        <h3 style="margin:0 0 6px;font-size:18px;font-weight:800;">Đặt phòng ngay</h3>
                        <p style="margin:0;font-size:13px;opacity:.85;">Tìm phòng tốt nhất tại <?php echo esc_html($title); ?></p>
                    </div>
                    <div style="padding:20px 24px 24px;">
                        <a href="<?php echo esc_url($book_link); ?>" class="dest-book-btn" style="width:100%;justify-content:center;box-sizing:border-box;display:flex;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Xem phòng có sẵn
                        </a>
                        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
                            <p style="margin:0 0 10px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;">Thông tin nhanh</p>
                            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#475569;">
                                <?php if ($hotel_count): ?><div>🏨 <?php echo esc_html($hotel_count); ?></div><?php endif; ?>
                                <?php if ($region): ?><div>📌 <?php echo esc_html($region); ?></div><?php endif; ?>
                                <?php if ($country): ?><div>🌍 <?php echo esc_html($country); ?></div><?php endif; ?>
                                <?php if ($badge): ?><div>🏷️ <?php echo esc_html($badge); ?></div><?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($highlights)): ?>
                        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;">
                            <p style="margin:0 0 10px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;">Điểm nổi bật</p>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <?php foreach ($highlights as $hl): ?>
                                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569;">
                                        <span style="width:18px;height:18px;background:#dcfce7;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg width="10" height="10" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                        <?php echo esc_html($hl); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related destinations -->
        <?php if (!empty($related)): ?>
        <div style="margin-top:56px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h2 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">🗺️ Điểm đến khác</h2>
                <a href="<?php echo get_post_type_archive_link('destination'); ?>" style="color:#2563eb;font-weight:700;font-size:13px;text-decoration:none;">Xem tất cả →</a>
            </div>
            <div class="dest-related-grid">
                <?php foreach ($related as $rel):
                    $rel_thumb = get_the_post_thumbnail_url($rel->ID, 'medium_large') ?: 'https://images.unsplash.com/photo-1528127269322-539815df45d6?auto=format&fit=crop&w=400';
                    $rel_count = get_post_meta($rel->ID, '_dest_hotel_count', true);
                ?>
                    <a href="<?php echo get_permalink($rel->ID); ?>" class="dest-related-card">
                        <img src="<?php echo esc_url($rel_thumb); ?>" alt="<?php echo esc_attr($rel->post_title); ?>">
                        <div class="overlay"></div>
                        <div class="info">
                            <h4><?php echo esc_html($rel->post_title); ?></h4>
                            <?php if ($rel_count): ?><p><?php echo esc_html($rel_count); ?></p><?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.dest-main -->

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
