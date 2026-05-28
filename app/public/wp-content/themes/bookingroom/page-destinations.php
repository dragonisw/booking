<?php /* Template Name: Điểm đến */ ?>
<?php get_header(); ?>

<?php
// Lấy danh sách điểm đến từ Custom Post Type "destination"
$destinations = brd_get_destinations(array(
    'limit'   => -1,  // Lấy tất cả
    'orderby' => 'meta_value_num',
    'order'   => 'ASC',
    'meta_key'=> '_dest_home_order',
));
?>

<style>
    /* ===== PAGE DESTINATION STYLES ===== */
    .dest-page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        position: relative;
        overflow: hidden;
        padding: 80px 0 60px;
        text-align: center;
    }
    .dest-page-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 20% 50%, rgba(37,99,235,0.15) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.12) 0%, transparent 50%);
    }
    .dest-page-hero-inner {
        position: relative;
        z-index: 1;
        max-width: 700px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .dest-page-hero-badge {
        display: inline-block;
        background: rgba(37,99,235,0.15);
        border: 1px solid rgba(37,99,235,0.3);
        color: #93c5fd;
        padding: 6px 18px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 20px;
    }
    .dest-page-hero h1 {
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 900;
        color: #fff;
        margin: 0 0 16px;
        line-height: 1.15;
    }
    .dest-page-hero h1 span {
        background: linear-gradient(135deg, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .dest-page-hero p {
        color: rgba(255,255,255,0.65);
        font-size: 1.05rem;
        line-height: 1.7;
        margin: 0;
    }
    .dest-page-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 36px;
        flex-wrap: wrap;
    }
    .dest-page-stats .stat {
        text-align: center;
    }
    .dest-page-stats .stat-val {
        font-size: 1.6rem;
        font-weight: 900;
        color: #60a5fa;
    }
    .dest-page-stats .stat-lbl {
        font-size: 12px;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: .07em;
        font-weight: 600;
    }

    /* ===== GRID ===== */
    .dest-page-section {
        background: #f8fafc;
        padding: 64px 0 80px;
    }
    .dest-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .dest-page-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }
    @media (max-width: 1024px) {
        .dest-page-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .dest-page-grid { grid-template-columns: 1fr; }
        .dest-page-hero { padding: 60px 0 40px; }
    }

    /* ===== CARD ===== */
    .dest-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .dest-card:hover {
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        transform: translateY(-6px);
        border-color: #e2e8f0;
    }
    .dest-card-img-wrap {
        position: relative;
        height: 240px;
        overflow: hidden;
    }
    .dest-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
    }
    .dest-card:hover .dest-card-img-wrap img {
        transform: scale(1.08);
    }
    .dest-card-img-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(15,23,42,0.5) 100%);
    }
    .dest-card-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: #f97316;
        color: #fff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        z-index: 2;
    }
    .dest-card-count {
        position: absolute;
        bottom: 14px;
        right: 14px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(8px);
        color: #fff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        z-index: 2;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .dest-card-body {
        padding: 22px 24px 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .dest-card-body h3 {
        margin: 0 0 6px;
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.3;
    }
    .dest-card-body h3 a {
        color: inherit;
        text-decoration: none;
        transition: color .2s;
    }
    .dest-card-body h3 a:hover { color: #2563eb; }
    .dest-card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        font-size: 12px;
        color: #94a3b8;
        flex-wrap: wrap;
    }
    .dest-card-meta span { display: flex; align-items: center; gap: 4px; }
    .dest-card-tagline {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
        flex: 1;
    }
    .dest-card-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2563eb;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        padding: 10px 0;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
        transition: gap .25s, color .2s;
        position: relative;
    }
    .dest-card-btn::after {
        content: '';
        position: absolute;
        inset: 0;
    }
    .dest-card:hover .dest-card-btn {
        gap: 12px;
        color: #1d4ed8;
    }

    /* Empty state */
    .dest-empty {
        text-align: center;
        padding: 80px 24px;
        grid-column: 1 / -1;
    }
    .dest-empty-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }
    .dest-empty h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px;
    }
    .dest-empty p {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
    }

    /* Section header */
    .dest-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .dest-section-label {
        font-size: 12px;
        font-weight: 800;
        color: #2563eb;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .dest-section-title-h2 {
        font-size: 1.8rem;
        font-weight: 900;
        color: #1e293b;
        margin: 0;
    }
    .dest-section-count {
        background: #eff6ff;
        color: #2563eb;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }
</style>

<main>
    <!-- Hero -->
    <div class="dest-page-hero">
        <div class="dest-page-hero-inner">
            <span class="dest-page-hero-badge">🗺️ Khám phá</span>
            <h1>Điểm đến <span>tuyệt vời</span> đang chờ bạn</h1>
            <p>Tìm kiếm khách sạn tuyệt vời tại những thành phố du lịch hàng đầu Việt Nam và thế giới.</p>
            <?php if (!empty($destinations)): ?>
            <div class="dest-page-stats">
                <div class="stat">
                    <div class="stat-val"><?php echo count($destinations); ?>+</div>
                    <div class="stat-lbl">Điểm đến</div>
                </div>
                <div class="stat">
                    <div class="stat-val">500+</div>
                    <div class="stat-lbl">Khách sạn</div>
                </div>
                <div class="stat">
                    <div class="stat-val">4.9★</div>
                    <div class="stat-lbl">Đánh giá TB</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid -->
    <section class="dest-page-section">
        <div class="dest-page-container">
            <div class="dest-section-header">
                <div>
                    <div class="dest-section-label">Tất cả điểm đến</div>
                    <h2 class="dest-section-title-h2">Chọn điểm đến lý tưởng</h2>
                </div>
                <?php if (!empty($destinations)): ?>
                <span class="dest-section-count"><?php echo count($destinations); ?> điểm đến</span>
                <?php endif; ?>
            </div>

            <div class="dest-page-grid">
                <?php if (!empty($destinations)): ?>
                    <?php foreach ($destinations as $dest_post):
                        $pid         = $dest_post->ID;
                        $title       = get_the_title($pid);
                        $thumb       = get_the_post_thumbnail_url($pid, 'large');
                        $hotel_count = get_post_meta($pid, '_dest_hotel_count', true);
                        $tagline     = get_post_meta($pid, '_dest_tagline', true);
                        $badge       = get_post_meta($pid, '_dest_badge', true);
                        $country     = get_post_meta($pid, '_dest_country', true) ?: 'Việt Nam';
                        $region      = get_post_meta($pid, '_dest_region', true);
                        $search_link = get_post_meta($pid, '_dest_search_link', true);
                        $dest_link   = get_permalink($pid);
                        $book_link   = !empty($search_link) ? $search_link : esc_url(add_query_arg('s', $title, home_url('/rooms')));

                        if (!$thumb) {
                            $thumb = 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=600&q=80';
                        }
                    ?>
                    <div class="dest-card">
                        <!-- Image -->
                        <div class="dest-card-img-wrap">
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>">
                            <div class="dest-card-img-overlay"></div>
                            <?php if ($badge): ?>
                                <span class="dest-card-badge"><?php echo esc_html($badge); ?></span>
                            <?php endif; ?>
                            <?php if ($hotel_count): ?>
                                <span class="dest-card-count">🏨 <?php echo esc_html($hotel_count); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Body -->
                        <div class="dest-card-body">
                            <h3><a href="<?php echo esc_url($dest_link); ?>"><?php echo esc_html($title); ?></a></h3>
                            <div class="dest-card-meta">
                                <?php if ($region): ?>
                                    <span>📌 <?php echo esc_html($region); ?></span>
                                <?php endif; ?>
                                <?php if ($country): ?>
                                    <span>🌍 <?php echo esc_html($country); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($tagline): ?>
                                <p class="dest-card-tagline"><?php echo esc_html($tagline); ?></p>
                            <?php else: ?>
                                <p class="dest-card-tagline" style="color:#cbd5e1;font-style:italic;">Điểm đến du lịch tuyệt vời đang chờ bạn khám phá.</p>
                            <?php endif; ?>

                            <a href="<?php echo esc_url($dest_link); ?>" class="dest-card-btn">
                                <span>Khám phá ngay</span>
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dest-empty">
                        <div class="dest-empty-icon">🗺️</div>
                        <h3>Chưa có điểm đến nào</h3>
                        <p>Hãy thêm điểm đến trong trang quản trị WordPress → <strong>Điểm đến → Thêm điểm đến</strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>