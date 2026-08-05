<?php
/*
 * Template Name: Vị trí
 * Description: Trang vị trí – bản đồ, hướng đi, điểm lân cận
 */
get_header();

// ── Lấy dữ liệu từ Post Meta (chỉnh sửa qua Editor) ────────────────────────
$pid = get_the_ID();

// Hero
$loc_hero_title   = get_post_meta($pid, '_loc_hero_title',  true) ?: get_theme_mod('loc_hero_title', 'Vị trí của chúng tôi');
$loc_hero_desc    = get_post_meta($pid, '_loc_hero_desc',   true) ?: get_theme_mod('loc_hero_desc',  'Toạ lạc tại trung tâm thành phố, Sonata dễ dàng tiếp cận từ mọi hướng. Chỉ vài phút đến các điểm du lịch nổi tiếng nhất.');
$loc_hero_badge   = get_post_meta($pid, '_loc_hero_badge',  true) ?: 'Trung tâm thành phố';
$loc_hero_image   = get_post_meta($pid, '_loc_hero_image',  true) ?: get_theme_mod('loc_hero_image', '');

// Contact
$loc_address_full = get_post_meta($pid, '_loc_address',     true) ?: get_theme_mod('loc_address_full', '123 Đường Trần Hưng Đạo, Phường Nguyễn Cư Trinh, Quận 1, TP. Hồ Chí Minh');
$loc_phone        = get_post_meta($pid, '_loc_phone',       true) ?: get_theme_mod('loc_phone',        '0123 456 789');
$loc_phone_link   = get_post_meta($pid, '_loc_phone_link',  true) ?: get_theme_mod('loc_phone_link',   'tel:0123456789');
$loc_email        = get_post_meta($pid, '_loc_email',       true) ?: get_theme_mod('loc_email',        'info@sonata.vn');
$loc_checkin      = get_post_meta($pid, '_loc_checkin',     true) ?: '14:00 – 24:00 mọi ngày';
$loc_checkout     = get_post_meta($pid, '_loc_checkout',    true) ?: 'Trước 12:00';

// Map
$loc_map_embed    = get_post_meta($pid, '_loc_map_embed',   true) ?: get_theme_mod('loc_map_embed', '');
$loc_map_query    = get_post_meta($pid, '_loc_map_query',   true) ?: get_theme_mod('loc_map_query', '123+Tran+Hung+Dao,+Ho+Chi+Minh+City');
$maps_api         = get_theme_mod('google_maps_api_key', '');

// Repeaters
$loc_stats = get_post_meta($pid, '_loc_stats', true);
if (empty($loc_stats) || !is_array($loc_stats)) {
    $loc_stats = array(
        array('num' => '5',   'unit' => 'km', 'label' => t('Từ sân bay',             'From Airport')),
        array('num' => '200', 'unit' => 'm',  'label' => t('Đến trung tâm',          'To City Center')),
        array('num' => '12',  'unit' => '+',  'label' => t('Điểm nổi tiếng gần đây', 'Nearby Attractions')),
    );
}

$loc_transport = get_post_meta($pid, '_loc_transport', true);
if (empty($loc_transport) || !is_array($loc_transport)) {
    $loc_transport = array(
        array('icon'=>'✈️','title'=>t('Máy bay','By Plane'),      'time'=>'~20 '.t('phút','min'),'color'=>'blue',  'desc'=>t('Từ sân bay Tân Sơn Nhất, đi taxi hoặc xe buýt sân bay đến trung tâm, chỉ cách 5km.','From Tan Son Nhat Airport, take a taxi or airport bus to the city center, only 5km away.')),
        array('icon'=>'🚌','title'=>t('Xe buýt','By Bus'),        'time'=>'~10 '.t('phút','min'),'color'=>'teal',  'desc'=>t('Nhiều tuyến xe buýt đi qua khu vực. Trạm xe buýt gần nhất chỉ cách 150m.','Multiple bus lines pass through the area. The nearest bus stop is only 150m away.')),
        array('icon'=>'🚖','title'=>t('Taxi / Grab','Taxi / Grab'),'time'=>'~15 '.t('phút','min'),'color'=>'amber', 'desc'=>t('Taxi và Grab luôn sẵn sàng. Nhân viên lễ tân có thể đặt xe hộ bất kỳ lúc nào.','Taxis and Grab are always available. Front desk staff can book for you anytime.')),
        array('icon'=>'🚗','title'=>t('Xe tự lái','By Car'),      'time'=>t('Bãi đỗ xe','Parking'),'color'=>'purple','desc'=>t('Bãi đỗ xe tại chỗ và đường phố. Nhập địa chỉ vào GPS để được chỉ đường chính xác.','On-site and street parking available. Enter our address into GPS for accurate directions.')),
    );
}

$loc_attractions = get_post_meta($pid, '_loc_attractions', true);
if (empty($loc_attractions) || !is_array($loc_attractions)) {
    $loc_attractions = array(
        array('emoji'=>'🏛️','name'=>t('Bến Nhà Rồng','Ben Nha Rong Wharf'),                'cat'=>t('Di tích lịch sử','Historic Site'),'dist'=>'1.2 km','walk'=>'15 '.t('phút đi bộ','min walk'),'desc'=>t('Bến cảng lịch sử nổi tiếng, nơi Bác Hồ ra đi tìm đường cứu nước năm 1911.','Famous historic wharf where Ho Chi Minh left to find a path to save the country in 1911.')),
        array('emoji'=>'🛍️','name'=>t('Chợ Bến Thành','Ben Thanh Market'),                  'cat'=>t('Mua sắm','Shopping'),            'dist'=>'800 m', 'walk'=>'10 '.t('phút đi bộ','min walk'),'desc'=>t('Biểu tượng của TP.HCM với hàng trăm gian hàng ẩm thực, đặc sản và hàng lưu niệm.','Icon of HCMC with hundreds of food stalls, local specialties and souvenirs.')),
        array('emoji'=>'⛪', 'name'=>t('Nhà thờ Đức Bà','Notre-Dame Cathedral'),             'cat'=>t('Kiến trúc','Architecture'),       'dist'=>'1.5 km','walk'=>'20 '.t('phút đi bộ','min walk'),'desc'=>t('Công trình kiến trúc Gothic nổi tiếng nhất Sài Gòn, được xây dựng từ thế kỷ 19.','Saigon\'s most famous Gothic architecture, built in the 19th century.')),
        array('emoji'=>'🎭','name'=>t('Phố đi bộ Nguyễn Huệ','Nguyen Hue Walking Street'), 'cat'=>t('Giải trí','Entertainment'),       'dist'=>'600 m', 'walk'=>'8 '.t('phút đi bộ','min walk'), 'desc'=>t('Con phố hiện đại sầm uất với nhiều nhà hàng, quán cà phê và sự kiện âm nhạc về đêm.','Bustling modern street with many restaurants, cafes and nighttime music events.')),
        array('emoji'=>'🏅','name'=>t('Bảo tàng Chứng tích Chiến tranh','War Remnants Museum'),'cat'=>t('Bảo tàng','Museum'),          'dist'=>'2 km',  'walk'=>'25 '.t('phút đi bộ','min walk'),'desc'=>t('Một trong những bảo tàng được tham quan nhiều nhất Đông Nam Á.','One of the most visited museums in Southeast Asia.')),
        array('emoji'=>'🏰','name'=>t('Dinh Thống Nhất','Reunification Palace'),              'cat'=>t('Di tích lịch sử','Historic Site'),'dist'=>'2.3 km','walk'=>'30 '.t('phút đi bộ','min walk'),'desc'=>t('Cung điện lịch sử mang kiến trúc độc đáo, chứng kiến nhiều sự kiện lịch sử trọng đại.','Historic palace with unique architecture, witness to many major historical events.')),
    );
}

$loc_landmarks = get_post_meta($pid, '_loc_landmarks', true);
if (empty($loc_landmarks) || !is_array($loc_landmarks)) {
    $loc_landmarks = array(
        array('emoji'=>'🛍️','name'=>t('Chợ Bến Thành',        'Ben Thanh Market'),      'dist'=>'800m',  'time'=>'10 phút'),
        array('emoji'=>'🎭','name'=>t('Phố đi bộ Nguyễn Huệ', 'Nguyen Hue Walking St'), 'dist'=>'600m',  'time'=>'8 phút'),
        array('emoji'=>'⛪', 'name'=>t('Nhà thờ Đức Bà',       'Notre-Dame Cathedral'),  'dist'=>'1.5km', 'time'=>'20 phút'),
        array('emoji'=>'🏛️','name'=>t('Bến Nhà Rồng',          'Ben Nha Rong'),           'dist'=>'1.2km', 'time'=>'15 phút'),
        array('emoji'=>'🌿','name'=>t('Thảo Cầm Viên',         'Saigon Zoo & Botanical'), 'dist'=>'2.5km', 'time'=>'12 phút xe'),
        array('emoji'=>'✈️','name'=>t('Sân bay Tân Sơn Nhất', 'Tan Son Nhat Airport'),   'dist'=>'5km',   'time'=>'20 phút xe'),
    );
}

// CTA
$loc_cta_title     = get_post_meta($pid, '_loc_cta_title',    true) ?: t('Sẵn sàng đến với Sonata?', 'Ready to Visit Sonata?');
$loc_cta_desc      = get_post_meta($pid, '_loc_cta_desc',     true) ?: t('Đặt phòng ngay hôm nay và trải nghiệm sự tiện lợi của vị trí trung tâm cùng dịch vụ đẳng cấp.', 'Book today and experience the convenience of a central location with world-class service.');
$loc_cta_btn1_text = get_post_meta($pid, '_loc_cta_btn1_text',true) ?: t('Đặt phòng ngay', 'Book Now');
$loc_cta_btn1_url  = get_post_meta($pid, '_loc_cta_btn1_url', true) ?: home_url('/dat-phong');
$loc_cta_btn2_text = get_post_meta($pid, '_loc_cta_btn2_text',true) ?: t('Liên hệ chúng tôi', 'Contact Us');
$loc_cta_btn2_url  = get_post_meta($pid, '_loc_cta_btn2_url', true) ?: home_url('/lien-he');
?>

<style>
/* =====================================================================
   TRANG VỊ TRÍ — Pure CSS, không phụ thuộc Tailwind
   ===================================================================== */
:root {
    --lp-blue:    #2563eb;
    --lp-blue-d:  #1d4ed8;
    --lp-cyan:    #06b6d4;
    --lp-teal:    #0d9488;
    --lp-amber:   #f59e0b;
    --lp-green:   #16a34a;
    --lp-dark:    #0f172a;
    --lp-navy:    #1e293b;
    --lp-gray:    #64748b;
    --lp-light:   #f8fafc;
    --lp-border:  #e2e8f0;
    --lp-white:   #ffffff;
    --lp-radius:  20px;
    --lp-ease:    cubic-bezier(0.16, 1, 0.3, 1);
}

.lp-page { font-family: 'Inter', 'Segoe UI', ui-sans-serif, system-ui, sans-serif; }
.lp-page *, .lp-page *::before, .lp-page *::after { box-sizing: border-box; }
.lp-page img { max-width: 100%; display: block; }
.lp-page p, .lp-page h1, .lp-page h2, .lp-page h3, .lp-page h4 { margin: 0; }
.lp-page a { color: inherit; text-decoration: none; }
.lp-page ul { list-style: none; margin: 0; padding: 0; }

.lp-container {
    width: 100%;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 24px;
    padding-right: 24px;
}

/* ── Eyebrow ── */
.lp-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--lp-blue);
    margin-bottom: 14px;
}
.lp-eyebrow::before {
    content: '';
    width: 28px; height: 2px;
    background: linear-gradient(90deg, var(--lp-blue), var(--lp-cyan));
    border-radius: 2px;
    flex-shrink: 0;
}

/* ── Animations ── */
@keyframes lpFadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes lpPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(1.5); }
}
@keyframes lpHeroZoom {
    from { transform: scale(1.05); }
    to   { transform: scale(1); }
}
@keyframes lpFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

/* ===================================================================
   1. HERO
   =================================================================== */
.lp-hero {
    position: relative;
    min-height: 580px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--lp-dark);
    padding-top: 80px; /* offset fixed header */
}
.admin-bar .lp-hero { padding-top: 112px; }
@media (max-width: 782px) { .admin-bar .lp-hero { padding-top: 126px; } }

.lp-hero__bg {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.4;
    transform: scale(1.05);
    animation: lpHeroZoom 12s ease-out forwards;
}
.lp-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        160deg,
        rgba(15,23,42,0.72) 0%,
        rgba(15,23,42,0.35) 50%,
        rgba(15,23,42,0.9) 100%
    );
}
/* Decorative grid lines */
.lp-hero__grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
}
.lp-hero__content {
    position: relative;
    z-index: 2;
    padding: 60px 0 80px;
    width: 100%;
}
.lp-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff;
    font-size: 0.73rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 7px 18px;
    border-radius: 100px;
    margin-bottom: 22px;
    animation: lpFadeUp 0.7s var(--lp-ease) 0.1s both;
}
.lp-hero__badge-dot {
    width: 6px; height: 6px;
    background: #60a5fa;
    border-radius: 50%;
    animation: lpPulse 1.8s ease-in-out infinite;
}
.lp-hero__title {
    font-size: clamp(2.4rem, 6vw, 4.8rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.1;
    letter-spacing: -0.03em;
    text-shadow: 0 4px 28px rgba(0,0,0,0.4);
    margin-bottom: 20px;
    animation: lpFadeUp 0.8s var(--lp-ease) 0.25s both;
}
.lp-hero__title .lp-hl {
    background: linear-gradient(135deg, #60a5fa 0%, #34d399 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.lp-hero__subtitle {
    font-size: clamp(1rem, 2vw, 1.15rem);
    color: rgba(255,255,255,0.8);
    line-height: 1.75;
    max-width: 580px;
    animation: lpFadeUp 0.8s var(--lp-ease) 0.4s both;
}
.lp-hero__breadcrumb {
    position: absolute;
    bottom: 28px; right: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    animation: lpFadeUp 0.8s var(--lp-ease) 0.55s both;
}
.lp-hero__breadcrumb a { color: rgba(255,255,255,0.7); transition: color .2s; }
.lp-hero__breadcrumb a:hover { color: #fff; }

/* ── Hero bottom stat bar ── */
.lp-hero__stats {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    animation: lpFadeUp 0.8s var(--lp-ease) 0.65s both;
}
.lp-hero__stat {
    flex: 1;
    max-width: 280px;
    padding: 18px 24px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.lp-hero__stat:last-child { border-right: none; }
.lp-hero__stat-num {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}
.lp-hero__stat-num span { color: #60a5fa; }
.lp-hero__stat-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: rgba(255,255,255,0.55);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
@media (max-width: 600px) {
    .lp-hero__stats { flex-wrap: wrap; }
    .lp-hero__stat { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); max-width: 100%; }
    .lp-hero__stat:last-child { border-bottom: none; }
}

/* ===================================================================
   2. QUICK INFO STRIP
   =================================================================== */
.lp-strip {
    background: #fff;
    border-bottom: 1px solid var(--lp-border);
    padding: 0;
}
.lp-strip__inner {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
}
@media (max-width: 700px) { .lp-strip__inner { grid-template-columns: 1fr; } }
.lp-strip__item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 28px 32px;
    border-right: 1px solid var(--lp-border);
    transition: background .2s;
}
.lp-strip__item:last-child { border-right: none; }
.lp-strip__item:hover { background: var(--lp-light); }
.lp-strip__icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lp-strip__icon svg { width: 22px; height: 22px; }
.lp-strip__icon.blue   { background: #eff6ff; color: var(--lp-blue); }
.lp-strip__icon.teal   { background: #f0fdfa; color: var(--lp-teal); }
.lp-strip__icon.amber  { background: #fffbeb; color: var(--lp-amber); }
.lp-strip__label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--lp-gray);
    margin-bottom: 4px;
}
.lp-strip__value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--lp-dark);
    line-height: 1.4;
}
.lp-strip__value a { color: var(--lp-blue); }
.lp-strip__value a:hover { text-decoration: underline; }

/* ===================================================================
   3. MAP + ADDRESS SECTION
   =================================================================== */
.lp-map-section {
    padding: 80px 0;
    background: var(--lp-light);
}
.lp-map-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 48px;
    align-items: start;
}
@media (max-width: 960px) {
    .lp-map-layout { grid-template-columns: 1fr; }
}

/* Map frame */
.lp-map-frame {
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    position: relative;
    background: #cbd5e1;
    min-height: 480px;
}
.lp-map-frame iframe {
    width: 100%; height: 480px;
    border: none; display: block;
    filter: contrast(1.05) saturate(0.92);
    transition: filter .4s;
}
.lp-map-frame:hover iframe { filter: contrast(1.05) saturate(1); }
.lp-map-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    height: 480px;
    color: var(--lp-gray);
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
}
.lp-map-placeholder svg { width: 56px; height: 56px; opacity: 0.4; }
.lp-map-placeholder p { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; }

/* ── Map Open button ── */
.lp-map-open-btn {
    position: absolute;
    top: 16px; right: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--lp-border);
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--lp-blue);
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transition: all .2s;
}
.lp-map-open-btn:hover {
    background: var(--lp-blue);
    color: #fff;
    border-color: var(--lp-blue);
    transform: translateY(-2px);
}
.lp-map-open-btn svg { width: 14px; height: 14px; }

/* Address card */
.lp-address-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.07);
    border: 1px solid var(--lp-border);
    padding: 40px 36px;
    display: flex;
    flex-direction: column;
    gap: 28px;
}
.lp-address-card__title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--lp-dark);
    letter-spacing: -0.02em;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--lp-border);
}
.lp-address-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.lp-address-item__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lp-address-item__icon svg { width: 20px; height: 20px; }
.lp-address-item__icon.blue  { background: #eff6ff; color: var(--lp-blue); }
.lp-address-item__icon.teal  { background: #f0fdfa; color: var(--lp-teal); }
.lp-address-item__icon.amber { background: #fffbeb; color: var(--lp-amber); }
.lp-address-item__icon.green { background: #f0fdf4; color: var(--lp-green); }
.lp-address-item__label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--lp-gray);
    margin-bottom: 5px;
}
.lp-address-item__val {
    font-size: 0.92rem;
    color: var(--lp-dark);
    line-height: 1.6;
    font-weight: 500;
}
.lp-address-item__val a { color: var(--lp-blue); }
.lp-address-item__val a:hover { text-decoration: underline; }

/* Directions button */
.lp-btn-directions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 24px;
    background: linear-gradient(135deg, var(--lp-blue) 0%, var(--lp-blue-d) 100%);
    color: #fff;
    font-size: 0.92rem;
    font-weight: 700;
    border-radius: 14px;
    text-decoration: none;
    transition: all .25s;
    box-shadow: 0 6px 20px rgba(37,99,235,0.35);
    margin-top: 8px;
}
.lp-btn-directions:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(37,99,235,0.45);
    color: #fff;
}
.lp-btn-directions svg { width: 18px; height: 18px; }

/* ===================================================================
   4. TRANSPORT / HOW TO GET HERE
   =================================================================== */
.lp-transport {
    padding: 80px 0;
    background: #fff;
}
.lp-transport__header {
    text-align: center;
    margin-bottom: 56px;
}
.lp-section-title {
    font-size: clamp(1.75rem, 4vw, 2.8rem);
    font-weight: 800;
    color: var(--lp-dark);
    line-height: 1.2;
    letter-spacing: -0.025em;
}
.lp-section-title .lp-accent {
    background: linear-gradient(135deg, var(--lp-blue) 0%, var(--lp-cyan) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.lp-section-desc {
    font-size: 1.0rem;
    color: var(--lp-gray);
    line-height: 1.75;
    margin-top: 12px;
    max-width: 560px;
    margin-left: auto;
    margin-right: auto;
}

.lp-transport__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
@media (max-width: 880px) { .lp-transport__grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .lp-transport__grid { grid-template-columns: 1fr; } }

.lp-transport-card {
    background: var(--lp-light);
    border: 1px solid var(--lp-border);
    border-radius: var(--lp-radius);
    padding: 32px 24px;
    text-align: center;
    transition: all .25s var(--lp-ease);
    position: relative;
    overflow: hidden;
}
.lp-transport-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--lp-radius) var(--lp-radius) 0 0;
    opacity: 0;
    transition: opacity .25s;
}
.lp-transport-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    background: #fff;
}
.lp-transport-card:hover::before { opacity: 1; }

.lp-transport-card.blue::before  { background: linear-gradient(90deg, var(--lp-blue), var(--lp-cyan)); }
.lp-transport-card.teal::before  { background: linear-gradient(90deg, var(--lp-teal), #34d399); }
.lp-transport-card.amber::before { background: linear-gradient(90deg, var(--lp-amber), #fbbf24); }
.lp-transport-card.purple::before{ background: linear-gradient(90deg, #7c3aed, #a855f7); }

.lp-transport-card__icon {
    width: 72px; height: 72px;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    transition: transform .3s var(--lp-ease);
}
.lp-transport-card:hover .lp-transport-card__icon { transform: scale(1.12) rotate(-3deg); }
.lp-transport-card.blue  .lp-transport-card__icon { background: #eff6ff; }
.lp-transport-card.teal  .lp-transport-card__icon { background: #f0fdfa; }
.lp-transport-card.amber .lp-transport-card__icon { background: #fffbeb; }
.lp-transport-card.purple .lp-transport-card__icon { background: #f5f3ff; }

.lp-transport-card__icon svg { width: 32px; height: 32px; }
.lp-transport-card.blue   .lp-transport-card__icon svg { color: var(--lp-blue); }
.lp-transport-card.teal   .lp-transport-card__icon svg { color: var(--lp-teal); }
.lp-transport-card.amber  .lp-transport-card__icon svg { color: var(--lp-amber); }
.lp-transport-card.purple .lp-transport-card__icon svg { color: #7c3aed; }

.lp-transport-card__title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--lp-dark);
    margin-bottom: 10px;
}
.lp-transport-card__time {
    display: inline-block;
    background: var(--lp-blue);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 100px;
    margin-bottom: 14px;
    letter-spacing: 0.05em;
}
.lp-transport-card.teal   .lp-transport-card__time { background: var(--lp-teal); }
.lp-transport-card.amber  .lp-transport-card__time { background: var(--lp-amber); }
.lp-transport-card.purple .lp-transport-card__time { background: #7c3aed; }

.lp-transport-card__desc {
    font-size: 0.85rem;
    color: var(--lp-gray);
    line-height: 1.65;
}

/* ===================================================================
   5. NEARBY ATTRACTIONS
   =================================================================== */
.lp-nearby {
    padding: 80px 0;
    background: var(--lp-light);
}
.lp-nearby__header {
    text-align: center;
    margin-bottom: 56px;
}
.lp-nearby__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
@media (max-width: 900px) { .lp-nearby__grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .lp-nearby__grid { grid-template-columns: 1fr; } }

.lp-nearby-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--lp-border);
    overflow: hidden;
    transition: all .25s var(--lp-ease);
    display: flex;
    flex-direction: column;
}
.lp-nearby-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.lp-nearby-card__img {
    height: 180px;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
}
.lp-nearby-card__img-bg {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.7;
    transition: transform .4s var(--lp-ease);
}
.lp-nearby-card:hover .lp-nearby-card__img-bg { transform: scale(1.06); }
.lp-nearby-card__category {
    position: absolute;
    top: 14px; left: 14px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 4px 10px;
    border-radius: 100px;
    color: var(--lp-navy);
}
.lp-nearby-card__distance {
    position: absolute;
    bottom: 14px; right: 14px;
    background: rgba(15,23,42,0.75);
    backdrop-filter: blur(8px);
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
    padding: 5px 12px;
    border-radius: 100px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.lp-nearby-card__distance svg { width: 12px; height: 12px; }
.lp-nearby-card__body {
    padding: 22px 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.lp-nearby-card__name {
    font-size: 1rem;
    font-weight: 800;
    color: var(--lp-dark);
    line-height: 1.3;
}
.lp-nearby-card__desc {
    font-size: 0.85rem;
    color: var(--lp-gray);
    line-height: 1.6;
    flex: 1;
}
.lp-nearby-card__walk {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--lp-blue);
    margin-top: 4px;
}
.lp-nearby-card__walk svg { width: 14px; height: 14px; }

/* ===================================================================
   6. VISUAL LOCATION MAP (Decorative)
   =================================================================== */
.lp-landmark-section {
    padding: 80px 0;
    background: var(--lp-dark);
    overflow: hidden;
    position: relative;
}
.lp-landmark-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 48px 48px;
}
.lp-landmark-section .lp-eyebrow { color: #60a5fa; }
.lp-landmark-section .lp-eyebrow::before { background: linear-gradient(90deg, #60a5fa, #34d399); }
.lp-landmark-section .lp-section-title { color: #fff; }
.lp-landmark-section .lp-section-desc { color: rgba(255,255,255,0.6); }

.lp-landmark-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 1;
}
@media (max-width: 860px) { .lp-landmark-layout { grid-template-columns: 1fr; gap: 48px; } }

.lp-landmark-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.lp-landmark-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px;
    transition: all .2s;
    cursor: default;
}
.lp-landmark-item:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.2);
    transform: translateX(4px);
}
.lp-landmark-item__dot {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
}
.lp-landmark-item__name {
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 2px;
}
.lp-landmark-item__dist {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.5);
}
.lp-landmark-item__time {
    margin-left: auto;
    font-size: 0.78rem;
    font-weight: 700;
    color: #60a5fa;
    white-space: nowrap;
}

/* Visual map decoration */
.lp-visual-map {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    max-width: 440px;
    margin: 0 auto;
}
.lp-visual-map__ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 1px solid rgba(96,165,250,0.15);
    animation: lpRingPulse 3s ease-in-out infinite;
}
.lp-visual-map__ring:nth-child(2) {
    inset: 12%;
    border-color: rgba(96,165,250,0.22);
    animation-delay: -1s;
}
.lp-visual-map__ring:nth-child(3) {
    inset: 28%;
    border-color: rgba(96,165,250,0.3);
    animation-delay: -2s;
}
@keyframes lpRingPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.03); opacity: 0.7; }
}
.lp-visual-map__center {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 80px; height: 80px;
    background: linear-gradient(135deg, var(--lp-blue), var(--lp-cyan));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 0 12px rgba(37,99,235,0.2), 0 0 0 24px rgba(37,99,235,0.1);
    animation: lpFloat 3s ease-in-out infinite;
}
.lp-visual-map__center svg { width: 36px; height: 36px; color: #fff; }
.lp-visual-map__dot {
    position: absolute;
    width: 48px; height: 48px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    backdrop-filter: blur(8px);
    transition: all .3s;
}
.lp-visual-map__dot:hover {
    background: rgba(255,255,255,0.15);
    transform: scale(1.1);
}
.lp-visual-map__dot--1 { top: 8%; left: 42%; }
.lp-visual-map__dot--2 { top: 28%; right: 6%; }
.lp-visual-map__dot--3 { bottom: 28%; right: 6%; }
.lp-visual-map__dot--4 { bottom: 8%; left: 42%; }
.lp-visual-map__dot--5 { top: 28%; left: 6%; }
.lp-visual-map__dot--6 { bottom: 28%; left: 6%; }
.lp-visual-map__line {
    position: absolute;
    top: 50%; left: 50%;
    height: 1px;
    background: linear-gradient(90deg, rgba(96,165,250,0.6), transparent);
    transform-origin: left center;
    pointer-events: none;
}

/* ===================================================================
   7. MY LOCATION BUTTON & USER MARKER
   =================================================================== */
/* My Location Btn */
.lp-myloc-btn {
    position: absolute;
    bottom: 16px; left: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--lp-border);
    border-radius: 10px;
    padding: 9px 16px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--lp-dark);
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transition: all .25s;
    z-index: 10;
    white-space: nowrap;
}
.lp-myloc-btn:hover {
    background: var(--lp-blue);
    color: #fff;
    border-color: var(--lp-blue);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37,99,235,0.3);
}
.lp-myloc-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.lp-myloc-btn.loading { pointer-events: none; opacity: 0.7; }
.lp-myloc-btn.loading .lp-myloc-icon { animation: lpLocSpin 1s linear infinite; }
@keyframes lpLocSpin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* Distance badge (shown after geolocation) */
.lp-distance-badge {
    display: none;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 100px;
    margin-top: 10px;
    width: fit-content;
    box-shadow: 0 4px 12px rgba(16,185,129,0.35);
    animation: lpFadeUp 0.4s var(--lp-ease) both;
}
.lp-distance-badge svg { width: 14px; height: 14px; flex-shrink: 0; }
.lp-distance-badge.visible { display: inline-flex; }

/* Directions from my location button */
.lp-btn-from-me {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 13px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    font-size: 0.88rem;
    font-weight: 700;
    border-radius: 14px;
    border: none;
    cursor: pointer;
    transition: all .25s;
    box-shadow: 0 4px 16px rgba(16,185,129,0.35);
    width: 100%;
    text-decoration: none;
}
.lp-btn-from-me:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(16,185,129,0.45);
    color: #fff;
}
.lp-btn-from-me svg { width: 18px; height: 18px; }
.lp-btn-from-me:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* ===================================================================
   8. CTA BANNER
   =================================================================== */
.lp-cta {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--lp-blue) 0%, #1d4ed8 50%, #7c3aed 100%);
    position: relative;
    overflow: hidden;
    text-align: center;
}
.lp-cta::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 25% 50%, rgba(255,255,255,0.08) 0%, transparent 50%),
        radial-gradient(circle at 75% 50%, rgba(255,255,255,0.06) 0%, transparent 50%);
}
.lp-cta__content { position: relative; z-index: 1; }
.lp-cta__title {
    font-size: clamp(1.8rem, 4vw, 3rem);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.025em;
    line-height: 1.15;
    margin-bottom: 16px;
}
.lp-cta__desc {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.8);
    line-height: 1.7;
    margin-bottom: 36px;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
}
.lp-cta__buttons {
    display: flex;
    gap: 14px;
    justify-content: center;
    flex-wrap: wrap;
}
.lp-cta-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 32px;
    background: #fff;
    color: var(--lp-blue);
    font-size: 0.95rem;
    font-weight: 800;
    border-radius: 14px;
    text-decoration: none;
    transition: all .25s;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}
.lp-cta-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 36px rgba(0,0,0,0.25);
    color: var(--lp-blue);
}
.lp-cta-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 32px;
    background: rgba(255,255,255,0.12);
    border: 2px solid rgba(255,255,255,0.3);
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
    border-radius: 14px;
    text-decoration: none;
    transition: all .25s;
    backdrop-filter: blur(8px);
}
.lp-cta-btn-secondary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-3px);
    color: #fff;
}
.lp-cta-btn-primary svg, .lp-cta-btn-secondary svg { width: 18px; height: 18px; }

</style>

<div class="lp-page">

    <!-- ══════════════════════════════════════════════════
         1. HERO
         ══════════════════════════════════════════════════ -->
    <section class="lp-hero">
        <div class="lp-hero__grid"></div>

        <?php if ($loc_hero_image) : ?>
            <img class="lp-hero__bg" src="<?php echo esc_url($loc_hero_image); ?>" alt="<?php echo esc_attr($loc_hero_title); ?>" loading="eager">
        <?php endif; ?>
        <div class="lp-hero__overlay"></div>

        <div class="lp-container">
            <div class="lp-hero__content">
                <div class="lp-hero__badge">
                    <span class="lp-hero__badge-dot"></span>
                    <?php echo esc_html($loc_hero_badge); ?>
                </div>
                <h1 class="lp-hero__title">
                    <?php
                    $parts = explode(' ', $loc_hero_title, 3);
                    if (count($parts) >= 3) {
                        echo esc_html($parts[0] . ' ' . $parts[1]) . ' <span class="lp-hl">' . esc_html(implode(' ', array_slice($parts, 2))) . '</span>';
                    } else {
                        echo esc_html($loc_hero_title);
                    }
                    ?>
                </h1>
                <p class="lp-hero__subtitle"><?php echo esc_html($loc_hero_desc); ?></p>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="lp-container" style="position:relative;">
            <div class="lp-hero__breadcrumb">
                <a href="<?php echo home_url('/'); ?>"><?php echo t('Trang chủ', 'Home'); ?></a>
                <span class="lp-hero__breadcrumb-sep">›</span>
                <span><?php echo esc_html($loc_hero_title); ?></span>
            </div>
        </div>

        <!-- Stat bar (from meta) -->
        <div class="lp-hero__stats" style="padding-bottom:0;">
            <?php foreach ($loc_stats as $stat) : ?>
            <div class="lp-hero__stat">
                <div class="lp-hero__stat-num"><?php echo esc_html($stat['num']); ?><span><?php echo esc_html($stat['unit']); ?></span></div>
                <div class="lp-hero__stat-label"><?php echo esc_html($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         2. QUICK INFO STRIP
         ══════════════════════════════════════════════════ -->
    <div class="lp-strip">
        <div class="lp-container">
            <div class="lp-strip__inner">
                <div class="lp-strip__item">
                    <div class="lp-strip__icon blue">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="lp-strip__label"><?php echo t('Địa chỉ', 'Address'); ?></div>
                        <div class="lp-strip__value"><?php echo esc_html($loc_address_full); ?></div>
                    </div>
                </div>
                <div class="lp-strip__item">
                    <div class="lp-strip__icon teal">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="lp-strip__label"><?php echo t('Hotline', 'Phone'); ?></div>
                        <div class="lp-strip__value"><a href="<?php echo esc_url($loc_phone_link); ?>"><?php echo esc_html($loc_phone); ?></a></div>
                    </div>
                </div>
                <div class="lp-strip__item">
                    <div class="lp-strip__icon amber">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="lp-strip__label"><?php echo t('Giờ check-in', 'Check-in Time'); ?></div>
                        <div class="lp-strip__value"><?php echo esc_html($loc_checkin); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         3. MAP + ADDRESS CARD
         ══════════════════════════════════════════════════ -->
    <section class="lp-map-section">
        <div class="lp-container">
            <div class="lp-map-layout">

                <!-- Interactive map -->
                <div class="lp-map-frame" id="lp-map-frame">
                    <?php
                    // Store hotel coordinates for JS use
                    $hotel_lat = 10.757627;
                    $hotel_lng = 106.690443;
                    ?>
                    <?php if ($loc_map_embed) : ?>
                        <?php echo $loc_map_embed; // Allow iframe from Customizer ?>
                    <?php elseif ($maps_api) : ?>
                        <iframe id="lp-map-iframe"
                            src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr($maps_api); ?>&q=<?php echo esc_attr($loc_map_query); ?>&zoom=15"
                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    <?php else : ?>
                        <iframe id="lp-map-iframe"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.664228018082!2d106.69044291462196!3d10.757626992303655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f38f9ed887b%3A0x14aded5703768989!2zVHLhuqduIEjGsG5nIMSQxqFvLCBUcC4gSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2svn!4v1627000000000!5m2!1svi!2svn"
                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    <?php endif; ?>

                    <!-- Open in Google Maps button (top right) -->
                    <a href="https://www.google.com/maps/search/<?php echo urlencode($loc_address_full); ?>" target="_blank" rel="noopener" class="lp-map-open-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <?php echo t('Mở Google Maps', 'Open in Google Maps'); ?>
                    </a>

                    <!-- My Location button (bottom left) -->
                    <button id="lp-myloc-btn" class="lp-myloc-btn" onclick="lpGetMyLocation()" title="<?php echo t('Đánh dấu vị trí của tôi', 'Mark my location'); ?>">
                        <svg class="lp-myloc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                        </svg>
                        <span id="lp-myloc-label"><?php echo t('Vị trí của tôi', 'My Location'); ?></span>
                    </button>
                </div>

                <!-- Address info card -->
                <div class="lp-address-card">
                    <div class="lp-address-card__title"><?php echo t('Thông tin Vị trí', 'Location Details'); ?></div>

                    <div class="lp-address-item">
                        <div class="lp-address-item__icon blue">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="lp-address-item__label"><?php echo t('Địa chỉ đầy đủ', 'Full Address'); ?></div>
                            <div class="lp-address-item__val"><?php echo esc_html($loc_address_full); ?></div>
                        </div>
                    </div>

                    <div class="lp-address-item">
                        <div class="lp-address-item__icon teal">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="lp-address-item__label"><?php echo t('Điện thoại', 'Phone'); ?></div>
                            <div class="lp-address-item__val">
                                <a href="<?php echo esc_url($loc_phone_link); ?>"><?php echo esc_html($loc_phone); ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="lp-address-item">
                        <div class="lp-address-item__icon amber">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="lp-address-item__label">Email</div>
                            <div class="lp-address-item__val">
                                <a href="mailto:<?php echo esc_attr($loc_email); ?>"><?php echo esc_html($loc_email); ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="lp-address-item">
                        <div class="lp-address-item__icon green">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="lp-address-item__label"><?php echo t('Giờ làm việc', 'Working Hours'); ?></div>
                            <div class="lp-address-item__val"><?php echo t('Check-in: 14:00 – 24:00<br>Check-out: Trước 12:00', 'Check-in: 14:00 – Midnight<br>Check-out: Before 12:00'); ?></div>
                        </div>
                    </div>

                    <!-- Distance badge (shown after location detected) -->
                    <div id="lp-distance-badge" class="lp-distance-badge">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span id="lp-distance-text"></span>
                    </div>

                    <!-- Standard directions button -->
                    <a id="lp-btn-directions" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($loc_address_full); ?>" target="_blank" rel="noopener" class="lp-btn-directions">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <?php echo t('Chỉ đường đến đây', 'Get Directions'); ?>
                    </a>

                    <!-- Directions FROM my location (shown after GPS fix) -->
                    <a id="lp-btn-from-me" href="#" class="lp-btn-from-me" target="_blank" rel="noopener" style="display:none;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                        </svg>
                        <?php echo t('Chỉ đường từ vị trí của tôi', 'Route from My Location'); ?>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         4. TRANSPORT OPTIONS
         ══════════════════════════════════════════════════ -->
    <section class="lp-transport">
        <div class="lp-container">
            <div class="lp-transport__header">
                <div class="lp-eyebrow"><?php echo t('Cách di chuyển', 'How to Get Here'); ?></div>
                <h2 class="lp-section-title"><?php echo t('Đến Sonata', 'Getting to'); ?> <span class="lp-accent">Sonata</span></h2>
                <p class="lp-section-desc"><?php echo t('Nhiều lựa chọn phương tiện di chuyển thuận tiện từ mọi điểm trong thành phố và cả tỉnh thành khác.', 'Multiple convenient transport options from anywhere in the city and beyond.'); ?></p>
            </div>
            <div class="lp-transport__grid">
                <?php foreach ($loc_transport as $tc) :
                    $tc_color = esc_attr($tc['color'] ?? 'blue');
                ?>
                <div class="lp-transport-card <?php echo $tc_color; ?>">
                    <div class="lp-transport-card__icon">
                        <span style="font-size:2rem;"><?php echo $tc['icon']; ?></span>
                    </div>
                    <div class="lp-transport-card__title"><?php echo esc_html($tc['title']); ?></div>
                    <span class="lp-transport-card__time"><?php echo esc_html($tc['time']); ?></span>
                    <p class="lp-transport-card__desc"><?php echo esc_html($tc['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         5. NEARBY ATTRACTIONS
         ══════════════════════════════════════════════════ -->
    <section class="lp-nearby">
        <div class="lp-container">
            <div class="lp-nearby__header">
                <div class="lp-eyebrow"><?php echo t('Khám phá xung quanh', 'Explore Nearby'); ?></div>
                <h2 class="lp-section-title"><?php echo t('Điểm nổi bật', 'Nearby'); ?> <span class="lp-accent"><?php echo t('lân cận', 'Attractions'); ?></span></h2>
                <p class="lp-section-desc"><?php echo t('Hàng chục địa điểm du lịch, ẩm thực và giải trí nằm ngay trong tầm tay.', 'Dozens of tourist spots, dining and entertainment venues are right at your fingertips.'); ?></p>
            </div>
            <div class="lp-nearby__grid">
                <?php foreach ($loc_attractions as $att) : ?>
                    <div class="lp-nearby-card">
                        <div class="lp-nearby-card__img">
                            <span style="position:relative;z-index:1;font-size:3.5rem;"><?php echo $att['emoji']; ?></span>
                            <span class="lp-nearby-card__category"><?php echo esc_html($att['cat']); ?></span>
                            <span class="lp-nearby-card__distance">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <?php echo esc_html($att['dist']); ?>
                            </span>
                        </div>
                        <div class="lp-nearby-card__body">
                            <div class="lp-nearby-card__name"><?php echo esc_html($att['name']); ?></div>
                            <p class="lp-nearby-card__desc"><?php echo esc_html($att['desc']); ?></p>
                            <div class="lp-nearby-card__walk">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <?php echo esc_html($att['walk']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         6. VISUAL LOCATION MAP (Dark)
         ══════════════════════════════════════════════════ -->
    <section class="lp-landmark-section">
        <div class="lp-container">
            <div class="lp-landmark-layout">
                <div>
                    <div class="lp-eyebrow"><?php echo t('Tâm điểm thành phố', 'City Epicenter'); ?></div>
                    <h2 class="lp-section-title"><?php echo t('Khoảng cách đến', 'Distance to'); ?><br><span class="lp-accent"><?php echo t('các địa danh', 'Landmarks'); ?></span></h2>
                    <p class="lp-section-desc" style="color:rgba(255,255,255,0.6);margin-bottom:36px;"><?php echo t('Từ Sonata, bạn chỉ cần vài phút để chạm đến những trải nghiệm đặc sắc nhất của thành phố.', 'From Sonata, you are just minutes away from the city\'s most iconic experiences.'); ?></p>

                    <div class="lp-landmark-list">
                        <?php foreach ($loc_landmarks as $lm) : ?>
                        <div class="lp-landmark-item">
                            <div class="lp-landmark-item__dot" style="background:rgba(255,255,255,0.06);">
                                <?php echo $lm['emoji']; ?>
                            </div>
                            <div>
                                <div class="lp-landmark-item__name"><?php echo esc_html($lm['name']); ?></div>
                                <div class="lp-landmark-item__dist"><?php echo esc_html($lm['dist']); ?></div>
                            </div>
                            <div class="lp-landmark-item__time"><?php echo esc_html($lm['time']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Visual radial map (emoji từ landmarks) -->
                <div class="lp-visual-map">
                    <div class="lp-visual-map__ring"></div>
                    <div class="lp-visual-map__ring"></div>
                    <div class="lp-visual-map__ring"></div>
                    <div class="lp-visual-map__center">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <?php
                    $dot_positions = array('--1','--2','--3','--4','--5','--6');
                    foreach (array_slice($loc_landmarks, 0, 6) as $di => $dlm) :
                    ?>
                    <div class="lp-visual-map__dot lp-visual-map__dot<?php echo $dot_positions[$di]; ?>" title="<?php echo esc_attr($dlm['name']); ?>"><?php echo $dlm['emoji']; ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         7. CTA BANNER
         ══════════════════════════════════════════════════ -->
    <section class="lp-cta">
        <div class="lp-container">
            <div class="lp-cta__content">
                <h2 class="lp-cta__title"><?php echo esc_html($loc_cta_title); ?></h2>
                <p class="lp-cta__desc"><?php echo esc_html($loc_cta_desc); ?></p>
                <div class="lp-cta__buttons">
                    <a href="<?php echo esc_url($loc_cta_btn1_url); ?>" class="lp-cta-btn-primary">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo esc_html($loc_cta_btn1_text); ?>
                    </a>
                    <a href="<?php echo esc_url($loc_cta_btn2_url); ?>" class="lp-cta-btn-secondary">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <?php echo esc_html($loc_cta_btn2_text); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div><!-- .lp-page -->

<script>
(function() {
    // ── Hotel location (fixed) ────────────────────────────────────────
    var HOTEL_LAT = 10.757627;
    var HOTEL_LNG = 106.690443;
    var HOTEL_ADDR = encodeURIComponent('<?php echo addslashes($loc_address_full); ?>');

    // ── Haversine distance (km) ───────────────────────────────────────
    function haversine(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // ── Format distance ───────────────────────────────────────────────
    function formatDist(km) {
        if (km < 1) return Math.round(km * 1000) + ' m';
        return km.toFixed(1) + ' km';
    }

    // ── Main geolocation function ─────────────────────────────────────
    window.lpGetMyLocation = function() {
        var btn   = document.getElementById('lp-myloc-btn');
        var label = document.getElementById('lp-myloc-label');
        var badge = document.getElementById('lp-distance-badge');
        var distText = document.getElementById('lp-distance-text');
        var btnFromMe = document.getElementById('lp-btn-from-me');
        var btnDir    = document.getElementById('lp-btn-directions');
        var iframe    = document.getElementById('lp-map-iframe');

        if (!navigator.geolocation) {
            alert('<?php echo t('Trình duyệt không hỗ trợ định vị GPS.', 'Your browser does not support geolocation.'); ?>');
            return;
        }

        // Loading state
        btn.classList.add('loading');
        label.textContent = '<?php echo t('Đang định vị...', 'Locating...'); ?>';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                var dist = haversine(lat, lng, HOTEL_LAT, HOTEL_LNG);

                // ── Update map iframe to show both user + hotel ───────
                if (iframe) {
                    var mapUrl = 'https://www.google.com/maps/embed/v1/directions'
                        + '?key=AIzaSyBiB01mNIWcWKJTbLWHHYXDOj_vPKVEYXk'
                        + '&origin=' + lat + ',' + lng
                        + '&destination=' + HOTEL_LAT + ',' + HOTEL_LNG
                        + '&mode=driving'
                        + '&language=vi';
                    // Fallback: use directions URL in a new search if no API key
                    // We use a directions embed without API key (search mode)
                    iframe.src = 'https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d'
                        + '3919.664228018082!2d106.69044291462196!3d10.757626992303655'
                        + '!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e0'
                        + '!4m5!1s0x0%3A0x0!2s' + lat + '%2C' + lng
                        + '!3m2!1d' + lat + '!2d' + lng
                        + '!4m5!1s0x31752f38f9ed887b%3A0x14aded5703768989'
                        + '!2zVHLhuqduIEjGsG5nIMSQxqFvLCBUcC4gSOG7kyBDaMOtIE1pbmg'
                        + '!3m2!1d10.757627!2d106.690443!5e0!3m2!1svi!2svn!4v1627000000001!5m2!1svi!2svn';
                }

                // ── Distance badge ────────────────────────────────────
                var distLabel = formatDist(dist);
                distText.textContent = '<?php echo t('Bạn cách chúng tôi', 'You are'); ?> ' + distLabel;
                badge.classList.add('visible');

                // ── "From my location" directions button ──────────────
                var gmapUrl = 'https://www.google.com/maps/dir/?api=1'
                    + '&origin=' + lat + ',' + lng
                    + '&destination=' + HOTEL_LAT + ',' + HOTEL_LNG
                    + '&travelmode=driving';
                btnFromMe.href = gmapUrl;
                btnFromMe.style.display = 'flex';
                if (btnDir) btnDir.style.display = 'none';

                // ── Reset button ──────────────────────────────────────
                btn.classList.remove('loading');
                btn.style.background = '#10b981';
                btn.style.color = '#fff';
                btn.style.borderColor = '#10b981';
                label.textContent = '<?php echo t('Đã định vị ✓', 'Located ✓'); ?>';
            },
            function(err) {
                btn.classList.remove('loading');
                label.textContent = '<?php echo t('Vị trí của tôi', 'My Location'); ?>';
                var msg = '<?php echo t('Không thể lấy vị trí. Vui lòng cho phép truy cập GPS.', 'Cannot get location. Please allow GPS access.'); ?>';
                if (err.code === 1) msg = '<?php echo t('Bạn đã từ chối quyền truy cập vị trí.', 'You denied location access.'); ?>';
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    };
})();
</script>

<?php get_footer(); ?>
