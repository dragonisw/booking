<?php
/*
 * Template Name: Tin Tức
 * Description: Trang danh sách tin tức – layout card grid đẹp
 */
get_header();

$paged      = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$per_page   = 9;
$cat_filter = isset( $_GET['cat'] ) ? intval( $_GET['cat'] ) : 0;

$args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
if ( $cat_filter > 0 ) {
    $args['cat'] = $cat_filter;
}

$news_query  = new WP_Query( $args );
$categories  = get_categories( array( 'hide_empty' => true, 'number' => 20 ) );
$page_title  = t( 'TIN TỨC', 'NEWS' );
$page_sub    = t( 'Cập nhật thông tin mới nhất', 'Latest Updates' );
?>

<!–– ════ STYLES ════ ––>
<style>
/* ================================================================
   TIN TỨC PAGE — Luxury dark-green theme
   ================================================================ */
:root {
    --tn-green-900: #071c0f;
    --tn-green-800: #0a2618;
    --tn-green-700: #0f3d22;
    --tn-green-600: #155e2f;
    --tn-green-500: #1a7a3c;
    --tn-gold:      #c8a96e;
    --tn-gold-l:    #e2c99a;
    --tn-white:     #ffffff;
    --tn-gray:      rgba(255,255,255,0.6);
    --tn-border:    rgba(255,255,255,0.12);
    --tn-radius:    16px;
    --tn-ease:      cubic-bezier(0.16, 1, 0.3, 1);
}

.tn-page {
    background: var(--tn-green-900);
    min-height: 100vh;
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif;
    color: var(--tn-white);
}
.tn-page *, .tn-page *::before, .tn-page *::after { box-sizing: border-box; }
.tn-page img { max-width: 100%; display: block; }
.tn-page p, .tn-page h1, .tn-page h2, .tn-page h3 { margin: 0; }
.tn-page a { text-decoration: none; color: inherit; }

/* ── Container ── */
.tn-container {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* ── Hero Banner ── */
.tn-hero {
    position: relative;
    background: linear-gradient(180deg, var(--tn-green-800) 0%, var(--tn-green-900) 100%);
    padding: 6rem 0 4rem;
    text-align: center;
    overflow: hidden;
}
.tn-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 60% at 50% 0%, rgba(26,122,60,0.18) 0%, transparent 70%),
        radial-gradient(ellipse 40% 40% at 80% 80%, rgba(200,169,110,0.06) 0%, transparent 60%);
    pointer-events: none;
}
/* Decorative grid lines */
.tn-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}
.tn-hero__inner { position: relative; z-index: 1; }
.tn-hero__label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--tn-gold);
    margin-bottom: 1.25rem;
}
.tn-hero__label::before,
.tn-hero__label::after {
    content: '';
    display: block;
    width: 40px;
    height: 1px;
    background: var(--tn-gold);
    opacity: 0.5;
}
.tn-hero__title {
    font-size: clamp(2.4rem, 6vw, 4.5rem);
    font-weight: 800;
    letter-spacing: -0.01em;
    color: var(--tn-white);
    line-height: 1.05;
    margin-bottom: 1rem;
}
.tn-hero__sub {
    font-size: 1rem;
    color: var(--tn-gray);
    letter-spacing: 0.05em;
}

/* ── Category Filter Bar ── */
.tn-filter {
    background: rgba(10,38,24,0.95);
    border-bottom: 1px solid var(--tn-border);
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.tn-filter__inner {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 1rem 0;
    scrollbar-width: none;
}
.tn-filter__inner::-webkit-scrollbar { display: none; }
.tn-filter__btn {
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    padding: 0.45rem 1.1rem;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 1px solid var(--tn-border);
    color: var(--tn-gray);
    background: transparent;
    cursor: pointer;
    transition: all 0.22s ease;
    text-decoration: none;
}
.tn-filter__btn:hover {
    border-color: var(--tn-gold);
    color: var(--tn-gold);
}
.tn-filter__btn.is-active {
    background: var(--tn-gold);
    border-color: var(--tn-gold);
    color: var(--tn-green-900);
}

/* ── Grid ── */
.tn-section {
    padding: 4rem 0 5rem;
}
.tn-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}
@media (max-width: 1024px) { .tn-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .tn-grid { grid-template-columns: 1fr; gap: 1.25rem; } }

/* Featured first card – spans 2 cols */
.tn-grid .tn-card:first-child {
    grid-column: span 1;
}

/* ── Card ── */
.tn-card {
    position: relative;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--tn-border);
    border-radius: var(--tn-radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.32s var(--tn-ease), border-color 0.25s ease, box-shadow 0.32s ease;
    cursor: pointer;
    group: true;
}
.tn-card:hover {
    transform: translateY(-6px);
    border-color: rgba(200,169,110,0.35);
    box-shadow: 0 24px 48px rgba(0,0,0,0.4), 0 0 0 1px rgba(200,169,110,0.1);
}

/* Thumbnail */
.tn-card__thumb {
    position: relative;
    aspect-ratio: 16/10;
    overflow: hidden;
    background: var(--tn-green-800);
    flex-shrink: 0;
}
.tn-card__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.55s var(--tn-ease);
}
.tn-card:hover .tn-card__thumb img {
    transform: scale(1.06);
}
.tn-card__thumb-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(7,28,15,0.75) 0%, transparent 55%);
}
/* Category badge on image */
.tn-card__cat {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: var(--tn-gold);
    color: var(--tn-green-900);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.28rem 0.7rem;
    border-radius: 9999px;
    z-index: 2;
}
/* No-image placeholder */
.tn-card__no-img {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--tn-green-700), var(--tn-green-800));
}
.tn-card__no-img svg {
    width: 48px;
    height: 48px;
    opacity: 0.2;
}

/* Body */
.tn-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
}
.tn-card__title {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.45;
    color: var(--tn-white);
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
    transition: color 0.2s;
}
.tn-card:hover .tn-card__title { color: var(--tn-gold-l); }
.tn-card__excerpt {
    font-size: 0.82rem;
    color: var(--tn-gray);
    line-height: 1.6;
    margin-bottom: 1.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Footer: date + arrow */
.tn-card__footer {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.07);
}
.tn-card__date {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    line-height: 1;
}
.tn-card__date-day {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--tn-gold);
    line-height: 1;
    letter-spacing: -0.02em;
}
.tn-card__date-mdy {
    display: flex;
    flex-direction: column;
    gap: 1px;
    padding-bottom: 4px;
}
.tn-card__date-month {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--tn-gold);
    text-transform: uppercase;
}
.tn-card__date-year {
    font-size: 0.62rem;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.05em;
}
.tn-card__arrow {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50%;
    color: rgba(255,255,255,0.5);
    transition: all 0.25s ease;
    flex-shrink: 0;
}
.tn-card:hover .tn-card__arrow {
    border-color: var(--tn-gold);
    color: var(--tn-gold);
    background: rgba(200,169,110,0.08);
    transform: rotate(45deg);
}

/* ── No Posts ── */
.tn-empty {
    text-align: center;
    padding: 5rem 2rem;
    color: var(--tn-gray);
}
.tn-empty svg { width: 56px; height: 56px; margin: 0 auto 1.5rem; opacity: 0.3; }
.tn-empty h3 { font-size: 1.3rem; margin-bottom: 0.5rem; color: rgba(255,255,255,0.5); }

/* ── Pagination ── */
.tn-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 2.5rem 0 0;
    flex-wrap: wrap;
}
.tn-pagination a,
.tn-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid var(--tn-border);
    color: var(--tn-gray);
    transition: all 0.2s ease;
}
.tn-pagination a:hover {
    border-color: var(--tn-gold);
    color: var(--tn-gold);
}
.tn-pagination .current,
.tn-pagination span.current {
    background: var(--tn-gold);
    border-color: var(--tn-gold);
    color: var(--tn-green-900);
    font-weight: 800;
}

/* ── Section Divider ── */
.tn-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--tn-border), transparent);
    margin: 0;
}

/* ── Scroll Reveal ── */
@keyframes tnFadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}
.tn-reveal {
    opacity: 0;
    animation: tnFadeUp 0.55s var(--tn-ease) forwards;
}
</style>

<main class="tn-page">

    <!-- ── Hero ── -->
    <section class="tn-hero">
        <div class="tn-container">
            <div class="tn-hero__inner">
                <div class="tn-hero__label"><?php echo t( 'Thông Tin', 'Information' ); ?></div>
                <h1 class="tn-hero__title"><?php echo esc_html( $page_title ); ?></h1>
                <p class="tn-hero__sub"><?php echo esc_html( $page_sub ); ?></p>
            </div>
        </div>
    </section>

    <!-- ── Category Filter ── -->
    <?php if ( ! empty( $categories ) ) : ?>
    <div class="tn-filter">
        <div class="tn-container">
            <div class="tn-filter__inner">
                <!-- Tất cả -->
                <a href="<?php echo esc_url( get_permalink() ); ?>"
                   class="tn-filter__btn <?php echo ( $cat_filter === 0 ) ? 'is-active' : ''; ?>">
                    <?php echo t( 'Tất cả', 'All' ); ?>
                </a>
                <?php foreach ( $categories as $cat ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'cat', $cat->term_id, get_permalink() ) ); ?>"
                       class="tn-filter__btn <?php echo ( $cat_filter === $cat->term_id ) ? 'is-active' : ''; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                        <span style="opacity:0.5;margin-left:4px;font-weight:400;">(<?php echo $cat->count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="tn-divider"></div>

    <!-- ── News Grid ── -->
    <section class="tn-section">
        <div class="tn-container">

            <?php if ( $news_query->have_posts() ) : ?>

                <div class="tn-grid" id="tn-grid">
                    <?php
                    $card_i = 0;
                    while ( $news_query->have_posts() ) :
                        $news_query->the_post();
                        $card_i++;

                        $post_link  = get_permalink();
                        $post_title = get_the_title();
                        $post_exc   = wp_trim_words( get_the_excerpt() ?: strip_tags( get_the_content() ), 18, '...' );
                        $day        = get_the_date( 'd' );
                        $month      = get_the_date( 'm' );
                        $year       = get_the_date( 'Y' );
                        $thumb_url  = get_the_post_thumbnail_url( null, 'large' );

                        $post_cats  = get_the_category();
                        $cat_name   = ! empty( $post_cats ) ? $post_cats[0]->name : '';

                        $delay = ( ( $card_i - 1 ) % 3 ) * 0.1;
                    ?>
                    <a href="<?php echo esc_url( $post_link ); ?>"
                       class="tn-card tn-reveal"
                       style="animation-delay: <?php echo $delay; ?>s;">

                        <!-- Thumbnail -->
                        <div class="tn-card__thumb">
                            <?php if ( $thumb_url ) : ?>
                                <img src="<?php echo esc_url( $thumb_url ); ?>"
                                     alt="<?php echo esc_attr( $post_title ); ?>"
                                     loading="lazy">
                            <?php else : ?>
                                <div class="tn-card__no-img">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="tn-card__thumb-overlay"></div>
                            <?php if ( $cat_name ) : ?>
                                <span class="tn-card__cat"><?php echo esc_html( $cat_name ); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Body -->
                        <div class="tn-card__body">
                            <h2 class="tn-card__title"><?php echo esc_html( $post_title ); ?></h2>
                            <?php if ( $post_exc ) : ?>
                                <p class="tn-card__excerpt"><?php echo esc_html( $post_exc ); ?></p>
                            <?php endif; ?>

                            <!-- Footer: date + arrow -->
                            <div class="tn-card__footer">
                                <div class="tn-card__date">
                                    <span class="tn-card__date-day"><?php echo $day; ?></span>
                                    <div class="tn-card__date-mdy">
                                        <span class="tn-card__date-month"><?php echo sprintf( '%02d', $month ); ?></span>
                                        <span class="tn-card__date-year"><?php echo $year; ?></span>
                                    </div>
                                </div>
                                <span class="tn-card__arrow" aria-hidden="true">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                    </a>
                    <?php endwhile; ?>
                </div>

                <!-- ── Pagination ── -->
                <?php if ( $news_query->max_num_pages > 1 ) : ?>
                    <nav class="tn-pagination" aria-label="<?php esc_attr_e('Phân trang', 'bookingroom'); ?>">
                        <?php
                        $big = 999999999;
                        echo paginate_links( array(
                            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                            'format'    => '?paged=%#%',
                            'current'   => max( 1, $paged ),
                            'total'     => $news_query->max_num_pages,
                            'prev_text' => '←',
                            'next_text' => '→',
                            'type'      => 'plain',
                        ) );
                        ?>
                    </nav>
                <?php endif; ?>

            <?php else : ?>

                <!-- No Posts -->
                <div class="tn-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h2"/>
                    </svg>
                    <h3><?php echo t( 'Chưa có bài viết nào', 'No posts yet' ); ?></h3>
                    <p><?php echo t( 'Vui lòng quay lại sau.', 'Please check back later.' ); ?></p>
                </div>

            <?php endif; wp_reset_postdata(); ?>

        </div>
    </section>

</main>

<script>
/* ── Scroll reveal for cards ── */
(function() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.tn-reveal').forEach(function(el) {
            el.style.opacity = '1';
        });
        return;
    }
    var obs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                e.target.style.animationPlayState = 'running';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.tn-reveal').forEach(function(el) {
        el.style.animationPlayState = 'paused';
        obs.observe(el);
    });
})();
</script>

<?php get_footer(); ?>
