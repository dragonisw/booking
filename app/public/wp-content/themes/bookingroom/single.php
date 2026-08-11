<?php
/**
 * single.php – Single Post Template
 * Thiết kế sang trọng theo phong cách luxury hotel
 */
get_header();

if ( have_posts() ) : while ( have_posts() ) : the_post();

$post_id    = get_the_ID();
$title      = get_the_title();
$day        = get_the_date( 'd' );
$month      = get_the_date( 'm' );
$year       = get_the_date( 'Y' );
$full_date  = get_the_date( 'd/m/Y' );
$author     = get_the_author();
$thumb_url  = get_the_post_thumbnail_url( null, 'full' );
$thumb_med  = get_the_post_thumbnail_url( null, 'large' );
$categories = get_the_category();
$tags       = get_the_tags();
$cat_name   = ! empty( $categories ) ? $categories[0]->name : '';
$cat_link   = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '';
$views      = get_post_meta( $post_id, 'post_views_count', true );

// Track views
$views = $views ? intval($views) + 1 : 1;
update_post_meta( $post_id, 'post_views_count', $views );

// Related posts
$related = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => array( $post_id ),
    'category__in'   => wp_list_pluck( $categories ?: array(), 'term_id' ),
    'orderby'        => 'rand',
) );
?>

<style>
/* ================================================================
   SINGLE POST PAGE — Luxury dark-green theme
   ================================================================ */
:root {
    --sp-green-900: #071c0f;
    --sp-green-800: #0a2618;
    --sp-green-700: #0f3d22;
    --sp-gold:      #c8a96e;
    --sp-gold-l:    #e2c99a;
    --sp-white:     #ffffff;
    --sp-gray:      rgba(255,255,255,0.6);
    --sp-border:    rgba(255,255,255,0.1);
    --sp-ease:      cubic-bezier(0.16, 1, 0.3, 1);
}
.sp-page {
    background: var(--sp-green-900);
    min-height: 100vh;
    font-family: 'Inter', 'Be Vietnam Pro', sans-serif;
    color: var(--sp-white);
}
.sp-page *, .sp-page *::before, .sp-page *::after { box-sizing: border-box; }
.sp-page img { max-width: 100%; display: block; }
.sp-page p, .sp-page h1, .sp-page h2, .sp-page h3 { margin: 0; }
.sp-page a { text-decoration: none; color: inherit; }

/* ── Container ── */
.sp-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}
.sp-container--narrow {
    max-width: 820px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* ── Hero ── */
.sp-hero {
    position: relative;
    min-height: 68vh;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--sp-green-800);
}
.sp-hero__bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}
.sp-hero__bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform-origin: center;
    animation: spZoom 12s ease-out forwards;
}
@keyframes spZoom {
    from { transform: scale(1.08); }
    to   { transform: scale(1); }
}
.sp-hero__bg-fallback {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--sp-green-700), var(--sp-green-800));
}
.sp-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(7,28,15,0.95) 0%,
        rgba(7,28,15,0.6) 40%,
        rgba(7,28,15,0.25) 70%,
        rgba(7,28,15,0.15) 100%
    );
    z-index: 1;
}
.sp-hero__content {
    position: relative;
    z-index: 2;
    padding: 3rem 0;
    width: 100%;
}
/* Breadcrumb */
.sp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.5);
    margin-bottom: 1.5rem;
}
.sp-breadcrumb a { color: rgba(255,255,255,0.5); transition: color 0.2s; }
.sp-breadcrumb a:hover { color: var(--sp-gold); }
.sp-breadcrumb__sep { color: rgba(255,255,255,0.2); }
.sp-breadcrumb__current { color: var(--sp-gold); }
/* Category badge */
.sp-cat-badge {
    display: inline-block;
    background: var(--sp-gold);
    color: var(--sp-green-900);
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.32rem 0.9rem;
    border-radius: 9999px;
    margin-bottom: 1.25rem;
}
/* Title */
.sp-hero__title {
    font-size: clamp(1.6rem, 4.5vw, 3rem);
    font-weight: 800;
    line-height: 1.2;
    color: var(--sp-white);
    margin-bottom: 1.5rem;
    letter-spacing: -0.01em;
}
/* Meta row */
.sp-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
    font-size: 0.78rem;
    color: rgba(255,255,255,0.55);
}
.sp-meta__item {
    display: flex;
    align-items: center;
    gap: 6px;
}
.sp-meta__item svg {
    width: 14px;
    height: 14px;
    opacity: 0.6;
    flex-shrink: 0;
}
.sp-meta__sep {
    width: 1px;
    height: 14px;
    background: rgba(255,255,255,0.15);
}
/* Date big display */
.sp-meta__date-big {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    line-height: 1;
}
.sp-meta__day {
    font-size: 2rem;
    font-weight: 800;
    color: var(--sp-gold);
    line-height: 1;
    letter-spacing: -0.02em;
}
.sp-meta__mdy {
    display: flex;
    flex-direction: column;
    padding-bottom: 3px;
    gap: 1px;
}
.sp-meta__month {
    font-size: 0.6rem;
    font-weight: 700;
    color: var(--sp-gold);
    letter-spacing: 0.1em;
}
.sp-meta__year {
    font-size: 0.6rem;
    color: rgba(255,255,255,0.3);
    letter-spacing: 0.05em;
}

/* ── Article Body ── */
.sp-article {
    padding: 3.5rem 0 4rem;
}
.sp-article__inner {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--sp-border);
    border-radius: 20px;
    padding: 3rem;
    position: relative;
}
@media (max-width: 640px) { .sp-article__inner { padding: 1.5rem; border-radius: 14px; } }

/* Decorative top line */
.sp-article__inner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 3rem;
    right: 3rem;
    height: 2px;
    background: linear-gradient(to right, transparent, var(--sp-gold), transparent);
    border-radius: 9999px;
}

/* Content prose styles */
.sp-content {
    font-size: 1.02rem;
    line-height: 1.85;
    color: rgba(255,255,255,0.82);
}
.sp-content p { margin: 0 0 1.5rem; }
.sp-content p:last-child { margin-bottom: 0; }
.sp-content h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--sp-white);
    margin: 2.5rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(200,169,110,0.2);
}
.sp-content h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--sp-white);
    margin: 2rem 0 0.75rem;
}
.sp-content h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--sp-gold-l);
    margin: 1.5rem 0 0.5rem;
}
.sp-content a {
    color: var(--sp-gold);
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color 0.2s;
}
.sp-content a:hover { color: var(--sp-gold-l); }
.sp-content ul, .sp-content ol {
    padding-left: 1.5rem;
    margin: 0 0 1.5rem;
}
.sp-content ul { list-style: none; padding-left: 0; }
.sp-content ul li {
    padding: 0.35rem 0 0.35rem 1.5rem;
    position: relative;
    color: rgba(255,255,255,0.75);
}
.sp-content ul li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: var(--sp-gold);
    font-size: 0.8em;
    top: 0.5rem;
}
.sp-content ol li {
    margin-bottom: 0.5rem;
    color: rgba(255,255,255,0.75);
}
.sp-content blockquote {
    margin: 2rem 0;
    padding: 1.5rem 1.75rem;
    border-left: 3px solid var(--sp-gold);
    background: rgba(200,169,110,0.05);
    border-radius: 0 12px 12px 0;
    font-style: italic;
    color: rgba(255,255,255,0.7);
}
.sp-content blockquote p { margin: 0; }
.sp-content img {
    width: 100%;
    border-radius: 12px;
    margin: 1.5rem 0;
    border: 1px solid var(--sp-border);
}
.sp-content strong { color: var(--sp-white); font-weight: 700; }
.sp-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
    font-size: 0.88rem;
}
.sp-content th {
    background: rgba(200,169,110,0.12);
    padding: 0.75rem 1rem;
    text-align: left;
    color: var(--sp-gold);
    font-weight: 700;
    border: 1px solid var(--sp-border);
}
.sp-content td {
    padding: 0.6rem 1rem;
    border: 1px solid var(--sp-border);
    color: rgba(255,255,255,0.7);
}
.sp-content tr:hover td { background: rgba(255,255,255,0.02); }

/* ── Tags ── */
.sp-tags {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid var(--sp-border);
}
.sp-tags__label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    margin-right: 0.25rem;
}
.sp-tag {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border: 1px solid rgba(200,169,110,0.3);
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--sp-gold);
    transition: all 0.2s;
}
.sp-tag:hover {
    background: var(--sp-gold);
    color: var(--sp-green-900);
}

/* ── Share Bar ── */
.sp-share {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid var(--sp-border);
}
.sp-share__label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    margin-right: 0.25rem;
    white-space: nowrap;
}
.sp-share__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    border: 1px solid var(--sp-border);
    color: rgba(255,255,255,0.6);
    transition: all 0.2s;
    cursor: pointer;
    background: none;
}
.sp-share__btn svg { width: 14px; height: 14px; fill: currentColor; }
.sp-share__btn:hover { color: var(--sp-gold); border-color: rgba(200,169,110,0.5); }
.sp-share__btn--fb:hover  { background: rgba(24,119,242,0.12); border-color: rgba(24,119,242,0.4); color: #4d90fe; }
.sp-share__btn--tw:hover  { background: rgba(29,155,240,0.12); border-color: rgba(29,155,240,0.4); color: #1d9bf0; }
.sp-share__btn--link:hover { background: rgba(200,169,110,0.1); }

/* ── Post Navigation ── */
.sp-nav {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 2rem;
}
@media (max-width: 640px) { .sp-nav { grid-template-columns: 1fr; } }
.sp-nav__link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 1px solid var(--sp-border);
    border-radius: 14px;
    transition: all 0.25s ease;
    background: rgba(255,255,255,0.02);
}
.sp-nav__link:hover {
    border-color: rgba(200,169,110,0.35);
    background: rgba(200,169,110,0.04);
    transform: translateY(-2px);
}
.sp-nav__link--next { justify-content: flex-end; text-align: right; }
.sp-nav__icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--sp-border);
    border-radius: 50%;
    color: rgba(255,255,255,0.4);
    flex-shrink: 0;
    transition: all 0.2s;
}
.sp-nav__link:hover .sp-nav__icon {
    border-color: var(--sp-gold);
    color: var(--sp-gold);
}
.sp-nav__label {
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.3);
    margin-bottom: 4px;
}
.sp-nav__title {
    font-size: 0.88rem;
    font-weight: 700;
    color: rgba(255,255,255,0.8);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

/* ── Related Posts ── */
.sp-related {
    padding: 3rem 0 5rem;
    border-top: 1px solid var(--sp-border);
}
.sp-related__head {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2.5rem;
}
.sp-related__label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--sp-gold);
    white-space: nowrap;
}
.sp-related__line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, rgba(200,169,110,0.3), transparent);
}
.sp-related__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
@media (max-width: 900px) { .sp-related__grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .sp-related__grid { grid-template-columns: 1fr; } }
.sp-rel-card {
    border: 1px solid var(--sp-border);
    border-radius: 14px;
    overflow: hidden;
    background: rgba(255,255,255,0.02);
    transition: all 0.28s var(--sp-ease);
    display: flex;
    flex-direction: column;
}
.sp-rel-card:hover {
    transform: translateY(-4px);
    border-color: rgba(200,169,110,0.3);
    box-shadow: 0 16px 32px rgba(0,0,0,0.3);
}
.sp-rel-card__img {
    aspect-ratio: 16/9;
    overflow: hidden;
    background: var(--sp-green-800);
    flex-shrink: 0;
}
.sp-rel-card__img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s var(--sp-ease);
}
.sp-rel-card:hover .sp-rel-card__img img { transform: scale(1.05); }
.sp-rel-card__body { padding: 1.1rem; flex: 1; }
.sp-rel-card__title {
    font-size: 0.88rem;
    font-weight: 700;
    line-height: 1.45;
    color: rgba(255,255,255,0.85);
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 0.75rem;
    transition: color 0.2s;
}
.sp-rel-card:hover .sp-rel-card__title { color: var(--sp-gold-l); }
.sp-rel-card__date {
    font-size: 0.68rem;
    color: rgba(255,255,255,0.35);
    font-weight: 600;
    letter-spacing: 0.06em;
}

/* Scroll reveal */
@keyframes spFadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
.sp-reveal {
    opacity: 0;
    animation: spFadeUp 0.55s var(--sp-ease) forwards;
}
</style>

<main class="sp-page">

    <!-- ── Hero ── -->
    <section class="sp-hero">
        <div class="sp-hero__bg">
            <?php if ( $thumb_url ) : ?>
                <img src="<?php echo esc_url( $thumb_url ); ?>"
                     alt="<?php echo esc_attr( $title ); ?>">
            <?php else : ?>
                <div class="sp-hero__bg-fallback"></div>
            <?php endif; ?>
        </div>
        <div class="sp-hero__overlay"></div>

        <div class="sp-hero__content">
            <div class="sp-container">

                <!-- Breadcrumb -->
                <nav class="sp-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url( home_url('/') ); ?>"><?php echo t('Trang chủ','Home'); ?></a>
                    <span class="sp-breadcrumb__sep">›</span>
                    <a href="<?php echo esc_url( home_url('/tin-tuc') ); ?>"><?php echo t('Tin tức','News'); ?></a>
                    <?php if ( $cat_name ) : ?>
                        <span class="sp-breadcrumb__sep">›</span>
                        <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                    <?php endif; ?>
                    <span class="sp-breadcrumb__sep">›</span>
                    <span class="sp-breadcrumb__current"><?php echo esc_html( wp_trim_words( $title, 5, '...' ) ); ?></span>
                </nav>

                <!-- Category badge -->
                <?php if ( $cat_name ) : ?>
                    <div>
                        <a href="<?php echo esc_url( $cat_link ); ?>" class="sp-cat-badge">
                            <?php echo esc_html( $cat_name ); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Title -->
                <h1 class="sp-hero__title"><?php echo esc_html( $title ); ?></h1>

                <!-- Meta -->
                <div class="sp-meta">
                    <!-- Date big -->
                    <div class="sp-meta__date-big">
                        <span class="sp-meta__day"><?php echo $day; ?></span>
                        <div class="sp-meta__mdy">
                            <span class="sp-meta__month"><?php echo sprintf('%02d', $month); ?></span>
                            <span class="sp-meta__year"><?php echo $year; ?></span>
                        </div>
                    </div>
                    <span class="sp-meta__sep"></span>
                    <!-- Author -->
                    <div class="sp-meta__item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <?php echo esc_html( $author ); ?>
                    </div>
                    <!-- Views -->
                    <?php if ( $views ) : ?>
                    <span class="sp-meta__sep"></span>
                    <div class="sp-meta__item">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <?php echo number_format( $views ); ?> <?php echo t('lượt xem','views'); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ── Article ── -->
    <article class="sp-article">
        <div class="sp-container--narrow">
            <div class="sp-article__inner sp-reveal">

                <!-- Content -->
                <div class="sp-content">
                    <?php the_content(); ?>
                </div>

                <!-- Tags -->
                <?php if ( $tags ) : ?>
                <div class="sp-tags">
                    <span class="sp-tags__label"><?php echo t('Thẻ','Tags'); ?>:</span>
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                           class="sp-tag">#<?php echo esc_html( $tag->name ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Share -->
                <div class="sp-share">
                    <span class="sp-share__label"><?php echo t('Chia sẻ','Share'); ?></span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>"
                       class="sp-share__btn sp-share__btn--fb"
                       target="_blank" rel="noopener" aria-label="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( $title ); ?>"
                       class="sp-share__btn sp-share__btn--tw"
                       target="_blank" rel="noopener" aria-label="Twitter">
                        <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        Twitter
                    </a>
                    <button class="sp-share__btn sp-share__btn--link" onclick="navigator.clipboard.writeText(window.location.href).then(function(){this.textContent='✓ Đã sao chép!';var btn=this;setTimeout(function(){btn.innerHTML='<svg viewBox=\'0 0 24 24\' width=\'14\' height=\'14\' fill=\'currentColor\'><path d=\'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1\'/></svg> Copy link\';},2000)}.bind(this))" aria-label="Copy link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Copy link
                    </button>
                </div>

                <!-- Post Navigation -->
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                if ( $prev_post || $next_post ) :
                ?>
                <nav class="sp-nav" aria-label="Post navigation">
                    <!-- Previous -->
                    <?php if ( $prev_post ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="sp-nav__link sp-nav__link--prev">
                        <span class="sp-nav__icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                        <div>
                            <div class="sp-nav__label"><?php echo t('Bài trước','Previous'); ?></div>
                            <div class="sp-nav__title"><?php echo esc_html( $prev_post->post_title ); ?></div>
                        </div>
                    </a>
                    <?php else : ?><div></div><?php endif; ?>

                    <!-- Next -->
                    <?php if ( $next_post ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="sp-nav__link sp-nav__link--next">
                        <div>
                            <div class="sp-nav__label"><?php echo t('Bài tiếp theo','Next'); ?></div>
                            <div class="sp-nav__title"><?php echo esc_html( $next_post->post_title ); ?></div>
                        </div>
                        <span class="sp-nav__icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </a>
                    <?php else : ?><div></div><?php endif; ?>
                </nav>
                <?php endif; ?>

            </div>
        </div>
    </article>

    <!-- ── Related Posts ── -->
    <?php if ( $related->have_posts() ) : ?>
    <section class="sp-related">
        <div class="sp-container">

            <div class="sp-related__head">
                <span class="sp-related__label"><?php echo t('Bài viết liên quan', 'Related Articles'); ?></span>
                <span class="sp-related__line"></span>
                <a href="<?php echo esc_url( home_url('/tin-tuc') ); ?>"
                   style="font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.35);white-space:nowrap;transition:color 0.2s;"
                   onmouseover="this.style.color='#c8a96e'" onmouseout="this.style.color='rgba(255,255,255,0.35)'">
                    <?php echo t('Xem tất cả →', 'View All →'); ?>
                </a>
            </div>

            <div class="sp-related__grid">
                <?php
                $ri = 0;
                while ( $related->have_posts() ) : $related->the_post();
                    $ri++;
                    $r_url   = get_permalink();
                    $r_title = get_the_title();
                    $r_thumb = get_the_post_thumbnail_url( null, 'medium_large' );
                    $r_date  = get_the_date('d/m/Y');
                ?>
                <a href="<?php echo esc_url( $r_url ); ?>"
                   class="sp-rel-card sp-reveal"
                   style="animation-delay: <?php echo ( $ri * 0.1 ); ?>s">
                    <div class="sp-rel-card__img">
                        <?php if ( $r_thumb ) : ?>
                            <img src="<?php echo esc_url( $r_thumb ); ?>"
                                 alt="<?php echo esc_attr( $r_title ); ?>"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="sp-rel-card__body">
                        <div class="sp-rel-card__title"><?php echo esc_html( $r_title ); ?></div>
                        <div class="sp-rel-card__date"><?php echo $r_date; ?></div>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

        </div>
    </section>
    <?php endif; ?>

    <?php endwhile; endif; ?>

</main>

<script>
/* Scroll reveal */
(function() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.sp-reveal').forEach(function(el){el.style.opacity='1';});
        return;
    }
    var obs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                e.target.style.animationPlayState = 'running';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.sp-reveal').forEach(function(el) {
        el.style.animationPlayState = 'paused';
        obs.observe(el);
    });
})();
</script>

<?php get_footer(); ?>
