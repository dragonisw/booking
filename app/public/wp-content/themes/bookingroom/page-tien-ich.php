<?php
/*
 * Template Name: Tiện Ích
 * Description: Trang tiện ích resort cao cấp
 */
get_header();

function ti_meta( $key, $default = '' ) {
    $val = bookingroom_get_meta_lang( get_the_ID(), $key, true );
    return $val !== '' ? $val : $default;
}
?>

<style>
/* =====================================================================
   AMENITIES PAGE — Sonata Resort & Spa · Pure CSS, no Tailwind
   Color palette: off-black #16181c, gold #b89d6c, cream #f5f0e8
   Typography: Playfair Display (serif) + Inter (sans)
   ===================================================================== */

:root {
    --ti-black:      #16181c;
    --ti-black-2:    #1e2026;
    --ti-black-3:    #252830;
    --ti-gold:       #b89d6c;
    --ti-gold-light: #d4b88a;
    --ti-gold-dark:  #8a7250;
    --ti-cream:      #f5f0e8;
    --ti-cream-2:    #ede6d6;
    --ti-muted:      rgba(245,240,232,0.55);
    --ti-border:     rgba(184,157,108,0.18);
    --ti-serif:      'Playfair Display', Georgia, 'Times New Roman', serif;
    --ti-sans:       'Inter', 'Segoe UI', system-ui, sans-serif;
    --ti-ease:       cubic-bezier(0.16, 1, 0.3, 1);
    --ti-ease-out:   cubic-bezier(0.25, 0.46, 0.45, 0.94);
    --ti-radius:     4px;
    --ti-max:        1280px;
}

/* ── Reset & Base ── */
.ti-page { font-family: var(--ti-sans); background: var(--ti-black); color: var(--ti-cream); }
.ti-page *, .ti-page *::before, .ti-page *::after { box-sizing: border-box; }
.ti-page img { max-width: 100%; display: block; }
.ti-page p, .ti-page h1, .ti-page h2, .ti-page h3, .ti-page h4 { margin: 0; }
.ti-page a { color: inherit; text-decoration: none; }
.ti-page ul { list-style: none; margin: 0; padding: 0; }

/* ── Container ── */
.ti-container {
    width: 100%;
    max-width: var(--ti-max);
    margin-left: auto;
    margin-right: auto;
    padding-left: 32px;
    padding-right: 32px;
}
@media (max-width: 768px) {
    .ti-container { padding-left: 20px; padding-right: 20px; }
}

/* ── Ornament Line ── */
.ti-ornament {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}
.ti-ornament__line {
    width: 40px;
    height: 1px;
    background: var(--ti-gold);
    opacity: 0.7;
}
.ti-ornament__label {
    font-family: var(--ti-sans);
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ti-gold);
}

/* ── Section Heading ── */
.ti-heading {
    font-family: var(--ti-serif);
    font-size: clamp(2rem, 4.5vw, 3.5rem);
    font-weight: 400;
    color: var(--ti-cream);
    line-height: 1.2;
    letter-spacing: -0.01em;
}
.ti-heading .ti-italic { font-style: italic; color: var(--ti-gold); }
.ti-subheading {
    font-size: 1rem;
    color: var(--ti-muted);
    line-height: 1.75;
    margin-top: 14px;
    max-width: 560px;
}

/* ── Reveal Animation ── */
.ti-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.75s var(--ti-ease), transform 0.75s var(--ti-ease);
}
.ti-reveal.is-visible {
    opacity: 1;
    transform: none;
}
.ti-reveal-delay-1 { transition-delay: 0.1s; }
.ti-reveal-delay-2 { transition-delay: 0.2s; }
.ti-reveal-delay-3 { transition-delay: 0.3s; }
.ti-reveal-delay-4 { transition-delay: 0.4s; }
.ti-reveal-delay-5 { transition-delay: 0.5s; }

/* ===========================================================
   1. HERO
   =========================================================== */
.ti-hero {
    position: relative;
    min-height: 100vh;
    min-height: min(100vh, 860px);
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--ti-black);
}
.ti-hero__bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.5;
    transform: scale(1.06);
    animation: tiHeroZoom 12s var(--ti-ease-out) forwards;
}
@keyframes tiHeroZoom {
    from { transform: scale(1.06); }
    to   { transform: scale(1.0); }
}
.ti-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(22,24,28,0.98) 0%,
        rgba(22,24,28,0.6) 45%,
        rgba(22,24,28,0.2) 100%
    );
}
.ti-hero__overlay-top {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(22,24,28,0.55) 0%,
        transparent 35%
    );
}
.ti-hero__content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 100px 0 80px;
}
.ti-hero__tag {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(184,157,108,0.12);
    border: 1px solid rgba(184,157,108,0.3);
    border-radius: 100px;
    padding: 6px 18px;
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ti-gold);
    margin-bottom: 28px;
    animation: tiFadeUp 0.8s var(--ti-ease) 0.1s both;
}
.ti-hero__tag-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--ti-gold);
    animation: tiPulse 2s ease-in-out infinite;
}
@keyframes tiPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(1.5)} }
.ti-hero__title {
    font-family: var(--ti-serif);
    font-size: clamp(2.8rem, 7vw, 6rem);
    font-weight: 400;
    color: var(--ti-cream);
    line-height: 1.08;
    letter-spacing: -0.025em;
    margin-bottom: 24px;
    animation: tiFadeUp 0.9s var(--ti-ease) 0.25s both;
}
.ti-hero__title em {
    font-style: italic;
    color: var(--ti-gold);
}
.ti-hero__desc {
    font-size: clamp(1rem, 1.5vw, 1.125rem);
    color: rgba(245,240,232,0.75);
    line-height: 1.8;
    max-width: 560px;
    margin-bottom: 44px;
    animation: tiFadeUp 0.9s var(--ti-ease) 0.4s both;
}
.ti-hero__actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    animation: tiFadeUp 0.9s var(--ti-ease) 0.55s both;
}
.ti-hero__btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--ti-gold);
    color: var(--ti-black);
    font-family: var(--ti-serif);
    font-size: 0.9375rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    padding: 16px 36px;
    border-radius: var(--ti-radius);
    transition: background 0.3s ease, transform 0.3s ease;
}
.ti-hero__btn-primary:hover {
    background: var(--ti-gold-light);
    transform: translateY(-2px);
}
.ti-hero__btn-primary svg { transition: transform 0.3s ease; }
.ti-hero__btn-primary:hover svg { transform: translateX(4px); }
.ti-hero__btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--ti-cream);
    font-size: 0.9375rem;
    font-weight: 400;
    letter-spacing: 0.04em;
    padding: 16px 28px;
    border: 1px solid rgba(245,240,232,0.25);
    border-radius: var(--ti-radius);
    transition: border-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
}
.ti-hero__btn-secondary:hover {
    border-color: var(--ti-gold);
    color: var(--ti-gold);
    transform: translateY(-2px);
}

/* Breadcrumb */
.ti-hero__breadcrumb {
    position: absolute;
    bottom: 32px;
    right: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: rgba(245,240,232,0.4);
    animation: tiFadeUp 0.9s var(--ti-ease) 0.7s both;
}
.ti-hero__breadcrumb a { color: rgba(245,240,232,0.6); transition: color 0.2s; }
.ti-hero__breadcrumb a:hover { color: var(--ti-gold); }
.ti-hero__breadcrumb-sep { opacity: 0.4; }
/* Scroll indicator */
.ti-hero__scroll {
    position: absolute;
    bottom: 36px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    animation: tiFadeUp 1s var(--ti-ease) 0.9s both;
    z-index: 2;
}
.ti-hero__scroll-text {
    font-size: 0.625rem;
    font-weight: 600;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(245,240,232,0.4);
}
.ti-hero__scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, rgba(184,157,108,0.7), transparent);
    animation: tiScrollLine 2s ease-in-out infinite;
}
@keyframes tiScrollLine {
    0%   { transform: scaleY(0); transform-origin: top; opacity: 0; }
    40%  { transform: scaleY(1); transform-origin: top; opacity: 1; }
    80%  { transform: scaleY(1); transform-origin: bottom; opacity: 1; }
    100% { transform: scaleY(0); transform-origin: bottom; opacity: 0; }
}

@keyframes tiFadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: none; }
}

/* ===========================================================
   2. STAT BAR
   =========================================================== */
.ti-statbar {
    background: var(--ti-black-2);
    border-top: 1px solid var(--ti-border);
    border-bottom: 1px solid var(--ti-border);
}
.ti-statbar__inner {
    display: flex;
    align-items: stretch;
}
.ti-statbar__item {
    flex: 1;
    padding: 36px 32px;
    text-align: center;
    border-right: 1px solid var(--ti-border);
    position: relative;
}
.ti-statbar__item:last-child { border-right: none; }
.ti-statbar__num {
    font-family: var(--ti-serif);
    font-size: clamp(1.8rem, 3vw, 2.6rem);
    font-weight: 400;
    color: var(--ti-gold);
    line-height: 1;
    margin-bottom: 8px;
}
.ti-statbar__label {
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ti-muted);
}
@media (max-width: 640px) {
    .ti-statbar__inner { flex-wrap: wrap; }
    .ti-statbar__item { flex: 0 0 50%; border-right: 1px solid var(--ti-border); border-bottom: 1px solid var(--ti-border); }
    .ti-statbar__item:nth-child(2n) { border-right: none; }
    .ti-statbar__item:last-child, .ti-statbar__item:nth-last-child(2) { border-bottom: none; }
}

/* ===========================================================
   3. CATEGORY TABS
   =========================================================== */
.ti-tabs-section {
    background: var(--ti-black);
    padding: 80px 0 0;
}
.ti-tabs-head {
    text-align: center;
    margin-bottom: 52px;
}
.ti-tabs-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    border-bottom: 1px solid var(--ti-border);
    flex-wrap: wrap;
}
.ti-tab-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 14px 28px;
    font-family: var(--ti-sans);
    font-size: 0.8125rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--ti-muted);
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color 0.3s ease, border-color 0.3s ease;
    position: relative;
}
.ti-tab-btn:hover { color: var(--ti-cream); }
.ti-tab-btn.is-active {
    color: var(--ti-gold);
    border-bottom-color: var(--ti-gold);
}
.ti-tab-pane { display: none; }
.ti-tab-pane.is-active { display: block; }

/* ===========================================================
   4. AMENITIES SHOWCASE (Featured)
   =========================================================== */
.ti-showcase {
    padding: 80px 0;
    background: var(--ti-black);
}
.ti-showcase__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 2px;
    overflow: hidden;
    border-radius: 8px;
}
.ti-showcase__cell {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}
.ti-showcase__cell--large { grid-row: span 2; }
.ti-showcase__cell img {
    width: 100%;
    height: 100%;
    min-height: 280px;
    object-fit: cover;
    transition: transform 0.8s var(--ti-ease-out);
}
.ti-showcase__cell:hover img { transform: scale(1.05); }
.ti-showcase__cell--large img { min-height: 560px; }
.ti-showcase__caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 32px 28px 28px;
    background: linear-gradient(to top, rgba(22,24,28,0.9) 0%, transparent 100%);
    transform: translateY(8px);
    opacity: 0.9;
    transition: transform 0.4s var(--ti-ease), opacity 0.4s ease;
}
.ti-showcase__cell:hover .ti-showcase__caption { transform: none; opacity: 1; }
.ti-showcase__caption-label {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ti-gold);
    margin-bottom: 6px;
}
.ti-showcase__caption-title {
    font-family: var(--ti-serif);
    font-size: 1.4rem;
    font-weight: 400;
    color: var(--ti-cream);
    line-height: 1.3;
}
@media (max-width: 768px) {
    .ti-showcase__grid { grid-template-columns: 1fr; }
    .ti-showcase__cell--large { grid-row: auto; }
    .ti-showcase__cell--large img { min-height: 340px; }
    .ti-showcase__cell img { min-height: 240px; }
}

/* ===========================================================
   5. AMENITY CARDS GRID
   =========================================================== */
.ti-cards-section {
    padding: 80px 0;
    background: var(--ti-black-2);
}
.ti-cards-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 32px;
    margin-bottom: 56px;
    flex-wrap: wrap;
}
.ti-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
@media (max-width: 1024px) { .ti-cards-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .ti-cards-grid { grid-template-columns: 1fr; } }

.ti-card {
    background: var(--ti-black-3);
    border: 1px solid var(--ti-border);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.4s ease, transform 0.4s var(--ti-ease), box-shadow 0.4s ease;
    cursor: pointer;
}
.ti-card:hover {
    border-color: rgba(184,157,108,0.45);
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(0,0,0,0.5);
}
.ti-card__img-wrap {
    position: relative;
    overflow: hidden;
    height: 220px;
}
.ti-card__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s var(--ti-ease-out);
}
.ti-card:hover .ti-card__img-wrap img { transform: scale(1.08); }
.ti-card__badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(22,24,28,0.8);
    backdrop-filter: blur(8px);
    border: 1px solid var(--ti-border);
    border-radius: 100px;
    padding: 4px 14px;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ti-gold);
}
.ti-card__icon-ring {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(22,24,28,0.75);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(184,157,108,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ti-gold);
}
.ti-card__body { padding: 28px; }
.ti-card__title {
    font-family: var(--ti-serif);
    font-size: 1.25rem;
    font-weight: 400;
    color: var(--ti-cream);
    margin-bottom: 10px;
    transition: color 0.3s ease;
}
.ti-card:hover .ti-card__title { color: var(--ti-gold-light); }
.ti-card__desc {
    font-size: 0.875rem;
    color: var(--ti-muted);
    line-height: 1.7;
    margin-bottom: 20px;
}
.ti-card__features {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.ti-card__feature-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(184,157,108,0.07);
    border: 1px solid rgba(184,157,108,0.15);
    border-radius: 100px;
    padding: 4px 12px;
    font-size: 0.6875rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    color: rgba(245,240,232,0.65);
}
.ti-card__feature-tag svg { color: var(--ti-gold); flex-shrink: 0; }
.ti-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--ti-border);
}
.ti-card__footer-label { font-size: 0.75rem; color: var(--ti-muted); letter-spacing: 0.06em; }
.ti-card__footer-link {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--ti-gold);
    transition: gap 0.3s ease;
}
.ti-card__footer-link:hover { gap: 10px; }

/* ===========================================================
   6. FEATURED AMENITY — Horizontal Split
   =========================================================== */
.ti-feature-section {
    padding: 100px 0;
    background: var(--ti-black);
}
.ti-feature-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}
.ti-feature-split--reverse { direction: rtl; }
.ti-feature-split--reverse > * { direction: ltr; }

@media (max-width: 1024px) {
    .ti-feature-split { grid-template-columns: 1fr; gap: 48px; }
    .ti-feature-split--reverse { direction: ltr; }
}

.ti-feature__img-wrap {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}
.ti-feature__img-wrap img {
    width: 100%;
    height: 520px;
    object-fit: cover;
    display: block;
    transition: transform 0.8s var(--ti-ease-out);
}
.ti-feature__img-wrap:hover img { transform: scale(1.04); }
/* accent block */
.ti-feature__img-accent {
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 180px;
    height: 180px;
    border-radius: 8px;
    overflow: hidden;
    border: 4px solid var(--ti-black);
    box-shadow: 0 12px 32px rgba(0,0,0,0.5);
}
.ti-feature__img-accent img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: none;
}
.ti-feature__badge-float {
    position: absolute;
    top: 28px;
    left: 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(22,24,28,0.88);
    backdrop-filter: blur(12px);
    border: 1px solid var(--ti-border);
    border-radius: 8px;
    padding: 14px 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
.ti-feature__badge-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--ti-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ti-black);
    flex-shrink: 0;
}
.ti-feature__badge-num {
    font-family: var(--ti-serif);
    font-size: 1.375rem;
    font-weight: 400;
    color: var(--ti-cream);
}
.ti-feature__badge-lbl {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ti-muted);
    margin-top: 2px;
}
.ti-feature__body { }
.ti-feature__list {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-top: 40px;
}
.ti-feature__list-item {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    padding: 20px;
    border: 1px solid var(--ti-border);
    border-radius: 8px;
    background: var(--ti-black-2);
    transition: border-color 0.3s ease, background 0.3s ease;
}
.ti-feature__list-item:hover {
    border-color: rgba(184,157,108,0.4);
    background: var(--ti-black-3);
}
.ti-feature__list-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(184,157,108,0.1);
    border: 1px solid rgba(184,157,108,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ti-gold);
    flex-shrink: 0;
}
.ti-feature__list-title {
    font-family: var(--ti-serif);
    font-size: 1.0625rem;
    font-weight: 400;
    color: var(--ti-cream);
    margin-bottom: 4px;
}
.ti-feature__list-desc {
    font-size: 0.85rem;
    color: var(--ti-muted);
    line-height: 1.65;
}
.ti-feature__cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 36px;
    background: var(--ti-gold);
    color: var(--ti-black);
    font-family: var(--ti-serif);
    font-size: 0.9375rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    padding: 15px 32px;
    border-radius: var(--ti-radius);
    transition: background 0.3s ease, transform 0.3s ease;
}
.ti-feature__cta:hover { background: var(--ti-gold-light); transform: translateY(-2px); }
.ti-feature__cta svg { transition: transform 0.3s ease; }
.ti-feature__cta:hover svg { transform: translateX(4px); }

/* ===========================================================
   7. INCLUSION LIST
   =========================================================== */
.ti-inclusions-section {
    padding: 80px 0;
    background: var(--ti-black-2);
    border-top: 1px solid var(--ti-border);
    border-bottom: 1px solid var(--ti-border);
}
.ti-inclusions-head { text-align: center; margin-bottom: 60px; }
.ti-inclusions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}
@media (max-width: 1024px) { .ti-inclusions-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .ti-inclusions-grid { grid-template-columns: 1fr; } }

.ti-inclusion-card {
    background: var(--ti-black);
    border: 1px solid var(--ti-border);
    border-radius: 8px;
    padding: 32px 24px;
    text-align: center;
    transition: border-color 0.4s ease, transform 0.4s var(--ti-ease), box-shadow 0.4s ease;
}
.ti-inclusion-card:hover {
    border-color: rgba(184,157,108,0.5);
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.4);
}
.ti-inclusion-card__icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(184,157,108,0.08);
    border: 1px solid rgba(184,157,108,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ti-gold);
    margin: 0 auto 20px;
    transition: background 0.4s ease, border-color 0.4s ease;
}
.ti-inclusion-card:hover .ti-inclusion-card__icon {
    background: var(--ti-gold);
    border-color: var(--ti-gold);
    color: var(--ti-black);
}
.ti-inclusion-card__title {
    font-family: var(--ti-serif);
    font-size: 1.125rem;
    font-weight: 400;
    color: var(--ti-cream);
    margin-bottom: 10px;
}
.ti-inclusion-card__desc {
    font-size: 0.8125rem;
    color: var(--ti-muted);
    line-height: 1.7;
}

/* ===========================================================
   8. TIMELINE / HOURS
   =========================================================== */
.ti-hours-section {
    padding: 80px 0;
    background: var(--ti-black);
}
.ti-hours-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
}
@media (max-width: 1024px) { .ti-hours-grid { grid-template-columns: 1fr; gap: 48px; } }

.ti-hours__label {
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ti-gold);
    margin-bottom: 6px;
}
.ti-hours__title {
    font-family: var(--ti-serif);
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 400;
    color: var(--ti-cream);
    margin-bottom: 16px;
}
.ti-hours__desc {
    font-size: 0.9375rem;
    color: var(--ti-muted);
    line-height: 1.75;
    margin-bottom: 40px;
}
.ti-hours__table {
    width: 100%;
    border-collapse: collapse;
}
.ti-hours__table tr {
    border-bottom: 1px solid var(--ti-border);
}
.ti-hours__table tr:last-child { border-bottom: none; }
.ti-hours__table td {
    padding: 16px 0;
    font-size: 0.9rem;
    vertical-align: middle;
}
.ti-hours__table td:first-child {
    color: var(--ti-cream);
    font-weight: 500;
    width: 60%;
}
.ti-hours__table td:last-child {
    color: var(--ti-gold);
    text-align: right;
    font-size: 0.875rem;
}
.ti-hours__dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--ti-gold);
    margin-right: 10px;
    vertical-align: middle;
}

/* Map visual */
.ti-map-card {
    background: var(--ti-black-2);
    border: 1px solid var(--ti-border);
    border-radius: 8px;
    overflow: hidden;
}
.ti-map-card__header {
    padding: 24px 28px;
    border-bottom: 1px solid var(--ti-border);
}
.ti-map-card__title {
    font-family: var(--ti-serif);
    font-size: 1.25rem;
    color: var(--ti-cream);
    margin-bottom: 4px;
}
.ti-map-card__subtitle { font-size: 0.8125rem; color: var(--ti-muted); }
.ti-map-placeholder {
    height: 280px;
    background: linear-gradient(135deg, #1e2026 0%, #252830 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.ti-map-placeholder::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(184,157,108,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(184,157,108,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
}
.ti-map-pin {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 1;
}
.ti-map-pin__dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--ti-gold);
    box-shadow: 0 0 0 6px rgba(184,157,108,0.2), 0 0 0 12px rgba(184,157,108,0.08);
    animation: tiMapPulse 2.5s ease-in-out infinite;
}
@keyframes tiMapPulse {
    0%,100% { box-shadow: 0 0 0 6px rgba(184,157,108,0.2), 0 0 0 12px rgba(184,157,108,0.08); }
    50%      { box-shadow: 0 0 0 10px rgba(184,157,108,0.25), 0 0 0 20px rgba(184,157,108,0.05); }
}
.ti-map-pin__label {
    background: var(--ti-black);
    border: 1px solid var(--ti-border);
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: var(--ti-gold);
    white-space: nowrap;
}
.ti-map-card__footer {
    padding: 18px 28px;
    border-top: 1px solid var(--ti-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.ti-map-card__address { font-size: 0.8125rem; color: var(--ti-muted); line-height: 1.5; }
.ti-map-card__directions {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--ti-gold);
    border: 1px solid rgba(184,157,108,0.3);
    border-radius: 100px;
    padding: 8px 18px;
    transition: background 0.3s ease, border-color 0.3s ease;
    white-space: nowrap;
}
.ti-map-card__directions:hover {
    background: rgba(184,157,108,0.08);
    border-color: var(--ti-gold);
}

/* ===========================================================
   9. CTA BANNER
   =========================================================== */
.ti-cta-section {
    padding: 100px 0;
    background: var(--ti-black-2);
    position: relative;
    overflow: hidden;
}
.ti-cta-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(ellipse 70% 60% at 50% 50%, rgba(184,157,108,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.ti-cta-inner {
    text-align: center;
    position: relative;
    z-index: 1;
}
.ti-cta__divider {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 36px;
}
.ti-cta__divider-line {
    width: 80px;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--ti-gold));
    opacity: 0.5;
}
.ti-cta__divider-line:last-child {
    background: linear-gradient(to left, transparent, var(--ti-gold));
}
.ti-cta__divider-diamond {
    width: 8px;
    height: 8px;
    border: 1px solid var(--ti-gold);
    transform: rotate(45deg);
    opacity: 0.7;
}
.ti-cta__title {
    font-family: var(--ti-serif);
    font-size: clamp(2rem, 4.5vw, 3.5rem);
    font-weight: 400;
    color: var(--ti-cream);
    line-height: 1.2;
    margin-bottom: 18px;
}
.ti-cta__title em { font-style: italic; color: var(--ti-gold); }
.ti-cta__desc {
    font-size: 1rem;
    color: var(--ti-muted);
    line-height: 1.75;
    max-width: 520px;
    margin: 0 auto 44px;
}
.ti-cta__actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}
.ti-cta__btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--ti-gold);
    color: var(--ti-black);
    font-family: var(--ti-serif);
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    padding: 18px 44px;
    border-radius: var(--ti-radius);
    transition: background 0.3s ease, transform 0.3s ease;
}
.ti-cta__btn-primary:hover { background: var(--ti-gold-light); transform: translateY(-2px); }
.ti-cta__btn-primary svg { transition: transform 0.3s ease; }
.ti-cta__btn-primary:hover svg { transform: translateX(4px); }
.ti-cta__btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--ti-cream);
    font-size: 1rem;
    padding: 18px 36px;
    border: 1px solid rgba(245,240,232,0.2);
    border-radius: var(--ti-radius);
    transition: border-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
}
.ti-cta__btn-outline:hover { border-color: var(--ti-gold); color: var(--ti-gold); transform: translateY(-2px); }
.ti-cta__tel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    color: var(--ti-muted);
    margin-top: 24px;
}
.ti-cta__tel a { color: var(--ti-gold); transition: color 0.2s; }
.ti-cta__tel a:hover { color: var(--ti-gold-light); }

/* Divider */
.ti-section-divider {
    width: 100%;
    height: 1px;
    background: var(--ti-border);
}

/* ===========================================================
   Responsive tweaks
   =========================================================== */
@media (max-width: 768px) {
    .ti-cards-head { align-items: flex-start; }
    .ti-feature__img-wrap img { height: 320px; }
    .ti-feature__img-accent { display: none; }
}
@media (max-width: 480px) {
    .ti-tab-btn { padding: 12px 16px; font-size: 0.75rem; }
    .ti-hero__btn-primary, .ti-hero__btn-secondary { width: 100%; justify-content: center; }
    .ti-hero__actions { flex-direction: column; }
    .ti-cta__btn-primary, .ti-cta__btn-outline { width: 100%; justify-content: center; }
    .ti-cta__actions { flex-direction: column; }
}
</style>

<div class="ti-page" id="ti-page">

    <!-- ═══════════════════════════════════════════════════
         1. HERO
    ═══════════════════════════════════════════════════ -->
    <section class="ti-hero" id="ti-hero" aria-label="Tiện ích">
        <img
            class="ti-hero__bg"
            src="<?php echo esc_url( ti_meta('_ti_hero_bg', 'https://pub-2a47248d4bf142d3a9e6b6dec48dd911.r2.dev/1 Bungalow 4PN.jpg') ); ?>"
            alt="Tiện ích resort Sonata"
            loading="eager"
        >
        <div class="ti-hero__overlay" aria-hidden="true"></div>
        <div class="ti-hero__overlay-top" aria-hidden="true"></div>

        <div class="ti-hero__content">
            <div class="ti-container" style="position:relative;">
                <div class="ti-hero__tag">
                    <span class="ti-hero__tag-dot" aria-hidden="true"></span>
                    <?php echo t('Sonata Resort &amp; Spa', 'Sonata Resort &amp; Spa'); ?> · <?php echo t('Phan Thiết', 'Phan Thiet'); ?>
                </div>
                <h1 class="ti-hero__title">
                    <?php echo t(
                        'Tiện Ích &amp;<br><em>Trải Nghiệm</em>',
                        'Amenities &amp;<br><em>Experiences</em>'
                    ); ?>
                </h1>
                <p class="ti-hero__desc">
                    <?php echo t(
                        'Nơi mọi khoảnh khắc đều được nâng tầm — từ hồ bơi vô cực nhìn ra biển đến spa sang trọng, nhà hàng hải sản tươi sống và bãi biển riêng tư. Tất cả được kiến tạo để mang đến sự nghỉ ngơi hoàn hảo nhất.',
                        'Where every moment is elevated — from the infinity pool overlooking the sea to the luxurious spa, fresh seafood restaurant, and private beach. All crafted to deliver the most perfect rest.'
                    ); ?>
                </p>
                <div class="ti-hero__actions">
                    <a href="<?php echo esc_url( home_url('/') ); ?>booking" class="ti-hero__btn-primary">
                        <?php echo t('Đặt phòng ngay', 'Reserve Now'); ?>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#ti-amenities" class="ti-hero__btn-secondary">
                        <?php echo t('Khám phá tiện ích', 'Explore Amenities'); ?>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>

                <!-- Breadcrumb -->
                <nav class="ti-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url( home_url('/') ); ?>"><?php echo t('Trang chủ', 'Home'); ?></a>
                    <span class="ti-hero__breadcrumb-sep" aria-hidden="true">/</span>
                    <span><?php echo t('Tiện ích', 'Amenities'); ?></span>
                </nav>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="ti-hero__scroll" aria-hidden="true">
            <span class="ti-hero__scroll-text"><?php echo t('Cuộn xuống', 'Scroll'); ?></span>
            <span class="ti-hero__scroll-line"></span>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         2. STAT BAR
    ═══════════════════════════════════════════════════ -->
    <div class="ti-statbar" role="region" aria-label="Số liệu">
        <div class="ti-container">
            <div class="ti-statbar__inner">
                <?php
                $stats = [
                    [ ti_meta('_ti_stat1_num', '17+'),   ti_meta('_ti_stat1_lbl', t('Tiện ích cao cấp', 'Premium Amenities')) ],
                    [ ti_meta('_ti_stat2_num', '2'),      ti_meta('_ti_stat2_lbl', t('Hồ bơi vô cực', 'Infinity Pools')) ],
                    [ ti_meta('_ti_stat3_num', '5★'),     ti_meta('_ti_stat3_lbl', t('Spa & Wellness', 'Spa & Wellness')) ],
                    [ ti_meta('_ti_stat4_num', '24/7'),   ti_meta('_ti_stat4_lbl', t('Phục vụ liên tục', 'Concierge Service')) ],
                ];
                foreach ( $stats as $i => $s ) : ?>
                    <div class="ti-statbar__item ti-reveal" style="transition-delay:<?php echo $i * 0.1; ?>s">
                        <div class="ti-statbar__num"><?php echo esc_html( $s[0] ); ?></div>
                        <div class="ti-statbar__label"><?php echo esc_html( $s[1] ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         3. SHOWCASE MOSAIC
    ═══════════════════════════════════════════════════ -->
    <section class="ti-showcase" id="ti-amenities" aria-label="Hình ảnh tiện ích">
        <div class="ti-container">
            <div class="ti-showcase__grid ti-reveal">
                <!-- Large cell left -->
                <div class="ti-showcase__cell ti-showcase__cell--large">
                    <img
                        src="<?php echo esc_url( ti_meta('_ti_img1', 'https://pub-2a47248d4bf142d3a9e6b6dec48dd911.r2.dev/1 Bungalow 4PN.jpg') ); ?>"
                        alt="Hồ bơi vô cực Sonata Resort"
                        loading="lazy"
                    >
                    <div class="ti-showcase__caption">
                        <div class="ti-showcase__caption-label"><?php echo t('Hồ bơi', 'Pool'); ?></div>
                        <div class="ti-showcase__caption-title"><?php echo t('Hồ bơi vô cực nhìn ra biển', 'Infinity Pool Sea View'); ?></div>
                    </div>
                </div>
                <!-- Small cells right -->
                <div class="ti-showcase__cell">
                    <img
                        src="<?php echo esc_url( ti_meta('_ti_img2', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=900&q=80') ); ?>"
                        alt="Spa cao cấp Sonata"
                        loading="lazy"
                    >
                    <div class="ti-showcase__caption">
                        <div class="ti-showcase__caption-label"><?php echo t('Spa', 'Spa'); ?></div>
                        <div class="ti-showcase__caption-title"><?php echo t('Liệu pháp phục hồi toàn diện', 'Total Wellness Therapy'); ?></div>
                    </div>
                </div>
                <div class="ti-showcase__cell">
                    <img
                        src="<?php echo esc_url( ti_meta('_ti_img3', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=900&q=80') ); ?>"
                        alt="Nhà hàng và ẩm thực"
                        loading="lazy"
                    >
                    <div class="ti-showcase__caption">
                        <div class="ti-showcase__caption-label"><?php echo t('Ẩm thực', 'Dining'); ?></div>
                        <div class="ti-showcase__caption-title"><?php echo t('Ẩm thực sang trọng bên bờ biển', 'Fine Dining by the Sea'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         4. AMENITY CARDS
    ═══════════════════════════════════════════════════ -->
    <section class="ti-cards-section" aria-label="Danh sách tiện ích">
        <div class="ti-container">
            <div class="ti-cards-head">
                <div class="ti-reveal">
                    <div class="ti-ornament">
                        <span class="ti-ornament__line"></span>
                        <span class="ti-ornament__label"><?php echo t('Tiện ích cao cấp', 'Premium Amenities'); ?></span>
                    </div>
                    <h2 class="ti-heading">
                        <?php echo t(
                            'Mọi thứ bạn<br><em class="ti-italic">cần & muốn</em>',
                            'Everything You<br><em class="ti-italic">Need & Desire</em>'
                        ); ?>
                    </h2>
                </div>
                <p class="ti-subheading ti-reveal ti-reveal-delay-2">
                    <?php echo t(
                        'Từ không gian nghỉ dưỡng thư giãn đến những hoạt động kích thích năng lượng, Sonata Resort cung cấp đầy đủ tiện ích để kỳ nghỉ của bạn trở nên trọn vẹn.',
                        'From relaxing retreats to invigorating activities, Sonata Resort provides all the amenities to make your stay truly complete.'
                    ); ?>
                </p>
            </div>

            <div class="ti-cards-grid">
                <?php
                $amenity_cards = [
                    [
                        'badge'  => t('Điểm nhấn', 'Highlight'),
                        'title'  => t('Hồ bơi vô cực', 'Infinity Pool'),
                        'desc'   => t('Hai hồ bơi vô cực nhìn thẳng ra biển Đông, mở cửa cả ngày. Hồ bơi ban đêm được chiếu sáng lung linh tạo nên trải nghiệm đặc biệt.', 'Two infinity pools facing the South China Sea, open all day. The night pool is beautifully lit for an extraordinary experience.'),
                        'img'    => ti_meta('_ti_pool_img', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Hồ bơi vô cực', 'Infinity Pool'),
                        'tags'   => [ t('Nhìn ra biển', 'Ocean View'), t('Mở cả ngày', 'Open All Day'), t('Hồ đêm', 'Night Pool') ],
                        'footer_label' => t('Mở cửa: 07:00 – 22:00', 'Open: 07:00 – 22:00'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>',
                    ],
                    [
                        'badge'  => t('5 Sao', '5 Star'),
                        'title'  => t('Spa & Wellness', 'Spa & Wellness'),
                        'desc'   => t('Trải nghiệm các liệu pháp chăm sóc sức khỏe truyền thống Việt Nam và hiện đại. Massage thư giãn, trị liệu đá nóng và chăm sóc da mặt cao cấp.', 'Experience traditional Vietnamese and modern wellness therapies. Relaxing massages, hot stone therapy, and premium facial treatments.'),
                        'img'    => ti_meta('_ti_spa_img', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Spa cao cấp', 'Luxury Spa'),
                        'tags'   => [ t('Massage', 'Massage'), t('Đá nóng', 'Hot Stone'), t('Chăm sóc da', 'Skincare') ],
                        'footer_label' => t('Mở cửa: 09:00 – 21:00', 'Open: 09:00 – 21:00'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                    ],
                    [
                        'badge'  => t('Bãi Biển', 'Beach'),
                        'title'  => t('Bãi biển riêng tư', 'Private Beach'),
                        'desc'   => t('Bãi cát trắng trải dài 300m độc quyền cho khách resort. Đội ngũ nhân viên phục vụ đồ uống và ăn nhẹ tận bãi biển trong suốt cả ngày.', '300m of exclusive white sand beach for resort guests. Beach staff serve drinks and snacks throughout the day.'),
                        'img'    => ti_meta('_ti_beach_img', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Bãi biển riêng', 'Private Beach'),
                        'tags'   => [ t('300m bãi cát', '300m Beach'), t('Ghế tắm nắng', 'Sun Loungers'), t('Beach Bar', 'Beach Bar') ],
                        'footer_label' => t('Mở cửa: 06:00 – 20:00', 'Open: 06:00 – 20:00'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                    ],
                    [
                        'badge'  => t('Ẩm thực', 'Dining'),
                        'title'  => t('Nhà hàng & Quầy Bar', 'Restaurant & Bar'),
                        'desc'   => t('Nhà hàng hải sản tươi sống phục vụ các món đặc sản Phan Thiết và ẩm thực quốc tế. Pool Bar và Beach Bar phục vụ cocktail sáng tạo.', 'Fresh seafood restaurant serving Phan Thiet specialties and international cuisine. Pool Bar and Beach Bar serve creative cocktails.'),
                        'img'    => ti_meta('_ti_dining_img', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Nhà hàng', 'Restaurant'),
                        'tags'   => [ t('Hải sản tươi', 'Fresh Seafood'), t('Pool Bar', 'Pool Bar'), t('Beach Bar', 'Beach Bar') ],
                        'footer_label' => t('Phục vụ 06:00 – 23:00', 'Service: 06:00 – 23:00'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
                    ],
                    [
                        'badge'  => t('Thể thao', 'Sports'),
                        'title'  => t('Phòng gym & Thể thao', 'Fitness & Sports'),
                        'desc'   => t('Phòng gym hiện đại nhìn ra biển với trang thiết bị cao cấp. Sân tennis, bóng chuyền bãi biển, kayak và các hoạt động thể thao dưới nước.', 'Modern gym with ocean views and premium equipment. Tennis court, beach volleyball, kayaking and water sports.'),
                        'img'    => ti_meta('_ti_gym_img', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Phòng Gym', 'Fitness Center'),
                        'tags'   => [ t('Gym hiện đại', 'Modern Gym'), t('Tennis', 'Tennis'), t('Kayak', 'Kayak') ],
                        'footer_label' => t('Mở cửa: 06:00 – 22:00', 'Open: 06:00 – 22:00'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>',
                    ],
                    [
                        'badge'  => t('Doanh nghiệp', 'Business'),
                        'title'  => t('Hội nghị & Sự kiện', 'Meetings & Events'),
                        'desc'   => t('Trung tâm hội nghị đẳng cấp với 3 phòng họp có sức chứa từ 20 đến 300 khách. Đội ngũ chuyên nghiệp hỗ trợ tổ chức sự kiện từ A đến Z.', 'World-class conference center with 3 meeting rooms for 20 to 300 guests. Professional team supports event planning from A to Z.'),
                        'img'    => ti_meta('_ti_meeting_img', 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80'),
                        'img_alt'=> t('Phòng hội nghị', 'Meeting Room'),
                        'tags'   => [ t('3 phòng họp', '3 Meeting Rooms'), t('Sức chứa 300', 'Capacity 300'), t('A/V hiện đại', 'Modern A/V') ],
                        'footer_label' => t('Đặt lịch sự kiện', 'Book an Event'),
                        'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    ],
                ];

                foreach ( $amenity_cards as $idx => $c ) : ?>
                    <article class="ti-card ti-reveal" style="transition-delay:<?php echo ($idx % 3) * 0.1 + 0.1; ?>s" aria-label="<?php echo esc_attr( $c['title'] ); ?>">
                        <div class="ti-card__img-wrap">
                            <img src="<?php echo esc_url($c['img']); ?>" alt="<?php echo esc_attr($c['img_alt']); ?>" loading="lazy">
                            <div class="ti-card__badge"><?php echo esc_html($c['badge']); ?></div>
                            <div class="ti-card__icon-ring" aria-hidden="true">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <?php echo $c['icon_svg']; ?>
                                </svg>
                            </div>
                        </div>
                        <div class="ti-card__body">
                            <h3 class="ti-card__title"><?php echo esc_html($c['title']); ?></h3>
                            <p class="ti-card__desc"><?php echo esc_html($c['desc']); ?></p>
                            <div class="ti-card__features">
                                <?php foreach ( $c['tags'] as $tag ) : ?>
                                    <span class="ti-card__feature-tag">
                                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <?php echo esc_html($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <div class="ti-card__footer">
                                <span class="ti-card__footer-label"><?php echo esc_html($c['footer_label']); ?></span>
                                <a href="<?php echo esc_url( home_url('/') ); ?>booking" class="ti-card__footer-link" aria-label="<?php echo esc_attr( t('Tìm hiểu thêm', 'Learn More') ); ?>">
                                    <?php echo t('Tìm hiểu thêm', 'Learn More'); ?>
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         5. FEATURED — SPA HIGHLIGHT
    ═══════════════════════════════════════════════════ -->
    <section class="ti-feature-section" aria-label="Spa & Wellness">
        <div class="ti-container">
            <div class="ti-feature-split">
                <!-- Image -->
                <div class="ti-feature__img-wrap ti-reveal">
                    <img
                        src="<?php echo esc_url( ti_meta('_ti_spa_feature_img', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80') ); ?>"
                        alt="Spa Sonata Resort"
                        loading="lazy"
                    >
                    <div class="ti-feature__badge-float" aria-label="Giải thưởng">
                        <div class="ti-feature__badge-icon" aria-hidden="true">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <div>
                            <div class="ti-feature__badge-num"><?php echo t('Top 5', 'Top 5'); ?></div>
                            <div class="ti-feature__badge-lbl"><?php echo t('Spa tốt nhất VN', 'Best Spa in VN'); ?></div>
                        </div>
                    </div>
                    <div class="ti-feature__img-accent">
                        <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=300&q=80" alt="Liệu pháp spa" loading="lazy">
                    </div>
                </div>
                <!-- Content -->
                <div class="ti-feature__body ti-reveal ti-reveal-delay-2">
                    <div class="ti-ornament">
                        <span class="ti-ornament__line"></span>
                        <span class="ti-ornament__label"><?php echo t('Spa & Wellness', 'Spa & Wellness'); ?></span>
                    </div>
                    <h2 class="ti-heading">
                        <?php echo t(
                            'Liệu pháp phục hồi<br><em class="ti-italic">tâm hồn & thể xác</em>',
                            'Therapies to Restore<br><em class="ti-italic">Mind & Body</em>'
                        ); ?>
                    </h2>
                    <p class="ti-subheading">
                        <?php echo t(
                            'Sonata Spa mang đến hơn 20 liệu pháp được lựa chọn từ tinh hoa y học cổ truyền Việt Nam và kỹ thuật spa hiện đại. Không gian tĩnh lặng, hương thơm tinh tế và đôi tay lành nghề của chuyên viên sẽ đưa bạn vào trạng thái thư giãn sâu nhất.',
                            'Sonata Spa offers over 20 therapies selected from the essence of traditional Vietnamese medicine and modern spa techniques. Serene spaces, subtle aromas, and the skillful hands of therapists will guide you into the deepest state of relaxation.'
                        ); ?>
                    </p>
                    <ul class="ti-feature__list" aria-label="Dịch vụ spa">
                        <?php
                        $spa_items = [
                            [
                                'title' => t('Massage thư giãn truyền thống', 'Traditional Relaxation Massage'),
                                'desc'  => t('Kỹ thuật massage độc đáo kết hợp các điểm huyệt đạo cổ truyền và liệu pháp thư giãn hiện đại, thời gian 60 – 120 phút.', 'Unique massage technique combining traditional acupressure points and modern relaxation therapy, 60–120 minutes.'),
                                'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                            ],
                            [
                                'title' => t('Trị liệu đá nóng núi lửa', 'Volcanic Hot Stone Therapy'),
                                'desc'  => t('Các viên đá bazan được nung nóng đặt lên các điểm năng lượng cơ thể, giúp tan biến căng thẳng và cải thiện tuần hoàn máu.', 'Heated basalt stones placed on body energy points to melt away tension and improve blood circulation.'),
                                'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>',
                            ],
                            [
                                'title' => t('Chăm sóc da mặt cao cấp', 'Premium Facial Treatment'),
                                'desc'  => t('Sử dụng dòng mỹ phẩm cao cấp từ thảo mộc địa phương, quy trình chăm sóc da chuyên sâu 90 phút cho làn da tươi sáng và căng mịn.', 'Using premium cosmetics from local herbs, 90-minute intensive skincare for bright and smooth skin.'),
                                'svg'   => '<circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>',
                            ],
                        ];
                        foreach ( $spa_items as $item ) : ?>
                            <li class="ti-feature__list-item">
                                <div class="ti-feature__list-icon" aria-hidden="true">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <?php echo $item['svg']; ?>
                                    </svg>
                                </div>
                                <div>
                                    <div class="ti-feature__list-title"><?php echo esc_html($item['title']); ?></div>
                                    <div class="ti-feature__list-desc"><?php echo esc_html($item['desc']); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url( home_url('/') ); ?>booking" class="ti-feature__cta">
                        <?php echo t('Đặt lịch spa', 'Book Spa Treatment'); ?>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         6. FEATURED — BEACH & POOL (reversed)
    ═══════════════════════════════════════════════════ -->
    <div class="ti-section-divider"></div>
    <section class="ti-feature-section" style="background:var(--ti-black-2);" aria-label="Bãi biển và hồ bơi">
        <div class="ti-container">
            <div class="ti-feature-split ti-feature-split--reverse">
                <!-- Image -->
                <div class="ti-feature__img-wrap ti-reveal ti-reveal-delay-1">
                    <img
                        src="<?php echo esc_url( ti_meta('_ti_beach_feature_img', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80') ); ?>"
                        alt="Bãi biển Sonata Resort"
                        loading="lazy"
                    >
                    <div class="ti-feature__badge-float" aria-label="Số liệu">
                        <div class="ti-feature__badge-icon" aria-hidden="true">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div>
                            <div class="ti-feature__badge-num">300m</div>
                            <div class="ti-feature__badge-lbl"><?php echo t('Bãi biển riêng', 'Private Beachfront'); ?></div>
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="ti-feature__body ti-reveal ti-reveal-delay-2">
                    <div class="ti-ornament">
                        <span class="ti-ornament__line"></span>
                        <span class="ti-ornament__label"><?php echo t('Bãi biển & Hồ bơi', 'Beach & Pool'); ?></span>
                    </div>
                    <h2 class="ti-heading">
                        <?php echo t(
                            'Thiên đường<br><em class="ti-italic">bên bờ biển</em>',
                            'Paradise<br><em class="ti-italic">By the Shore</em>'
                        ); ?>
                    </h2>
                    <p class="ti-subheading">
                        <?php echo t(
                            'Bãi biển riêng tư trải dài 300m với cát trắng mịn màng và nước biển trong xanh. Hai hồ bơi vô cực được thiết kế để hòa mình vào không gian đại dương bao la.',
                            'A 300m private beach with fine white sand and clear blue waters. Two infinity pools designed to merge with the vast ocean expanse.'
                        ); ?>
                    </p>
                    <ul class="ti-feature__list" aria-label="Tiện ích bãi biển">
                        <?php
                        $beach_items = [
                            [
                                'title' => t('Hồ bơi vô cực 2 tầng', 'Two-level Infinity Pool'),
                                'desc'  => t('Hồ bơi tràn viền thiết kế theo từng tầng địa hình, tạo góc nhìn panorama ra biển. Hồ trẻ em riêng biệt, an toàn và thú vị.', 'Infinity-edge pools designed following the natural terrain, creating a panoramic sea view. Separate children\'s pool, safe and fun.'),
                                'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>',
                            ],
                            [
                                'title' => t('Beach Butler Service', 'Beach Butler Service'),
                                'desc'  => t('Đội ngũ butler phục vụ trực tiếp tại bãi biển — mang đồ uống, khăn tắm, kem chống nắng và hỗ trợ mọi yêu cầu trong suốt thời gian bạn ở đây.', 'Beach butler team serves directly on the beach — bringing drinks, towels, sunscreen and assisting with all requests throughout your stay.'),
                                'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                            ],
                            [
                                'title' => t('Thể thao dưới nước', 'Water Sports'),
                                'desc'  => t('Kayak, paddle board, lặn biển ngắm san hô và câu cá. Trung tâm thể thao biển trang bị đầy đủ dụng cụ cho mọi cấp độ.', 'Kayaking, paddle boarding, coral snorkeling, and fishing. Water sports center fully equipped for all skill levels.'),
                                'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/>',
                            ],
                        ];
                        foreach ( $beach_items as $item ) : ?>
                            <li class="ti-feature__list-item">
                                <div class="ti-feature__list-icon" aria-hidden="true">
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <?php echo $item['svg']; ?>
                                    </svg>
                                </div>
                                <div>
                                    <div class="ti-feature__list-title"><?php echo esc_html($item['title']); ?></div>
                                    <div class="ti-feature__list-desc"><?php echo esc_html($item['desc']); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url( home_url('/') ); ?>booking" class="ti-feature__cta">
                        <?php echo t('Đặt phòng & tận hưởng', 'Reserve & Enjoy'); ?>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         7. INCLUDED AMENITIES GRID
    ═══════════════════════════════════════════════════ -->
    <section class="ti-inclusions-section" aria-label="Tiện ích bao gồm">
        <div class="ti-container">
            <div class="ti-inclusions-head ti-reveal">
                <div class="ti-ornament" style="justify-content:center;">
                    <span class="ti-ornament__line"></span>
                    <span class="ti-ornament__label"><?php echo t('Bao gồm trong giá phòng', 'Included with Your Stay'); ?></span>
                    <span class="ti-ornament__line" style="transform:scaleX(-1);"></span>
                </div>
                <h2 class="ti-heading" style="text-align:center;">
                    <?php echo t(
                        'Tất cả những gì<br><em class="ti-italic">bạn cần</em>',
                        'Everything<br><em class="ti-italic">Included</em>'
                    ); ?>
                </h2>
                <p class="ti-subheading" style="text-align:center;margin:14px auto 0;">
                    <?php echo t(
                        'Nhiều tiện ích được miễn phí hoàn toàn cho mọi khách lưu trú.',
                        'Many amenities are completely complimentary for all resort guests.'
                    ); ?>
                </p>
            </div>

            <div class="ti-inclusions-grid">
                <?php
                $inclusions = [
                    [
                        'title' => t('WiFi tốc độ cao', 'High-Speed WiFi'),
                        'desc'  => t('Kết nối không dây tốc độ cao tại mọi khu vực của resort, bao gồm cả bãi biển.', 'High-speed wireless throughout all resort areas, including the beach.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>',
                    ],
                    [
                        'title' => t('Bữa sáng tự chọn', 'Breakfast Buffet'),
                        'desc'  => t('Bữa sáng buffet phong phú với đặc sản địa phương và món quốc tế cho mỗi ngày.', 'Rich breakfast buffet with local specialties and international dishes each day.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    ],
                    [
                        'title' => t('Ghế tắm nắng & Dù che', 'Sun Loungers & Umbrellas'),
                        'desc'  => t('Ghế tắm nắng và ô che cao cấp tại bãi biển và hồ bơi, miễn phí cho mọi khách.', 'Premium sun loungers and umbrellas at beach and pool, complimentary for all guests.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    ],
                    [
                        'title' => t('Đưa đón sân bay', 'Airport Transfer'),
                        'desc'  => t('Xe đưa đón sân bay Phan Thiết và Tân Sơn Nhất theo yêu cầu với xe sang trọng.', 'Airport transfers to/from Phan Thiet and Tan Son Nhat with luxury vehicles on request.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    ],
                    [
                        'title' => t('Concierge 24/7', 'Concierge 24/7'),
                        'desc'  => t('Đội ngũ concierge đa ngôn ngữ sẵn sàng phục vụ và hỗ trợ bạn suốt 24 giờ.', 'Multilingual concierge team available 24 hours to serve and assist you.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    ],
                    [
                        'title' => t('Hồ bơi trẻ em', 'Kids Pool'),
                        'desc'  => t('Hồ bơi nông an toàn dành riêng cho trẻ em với khu vui chơi nước thú vị và đầy màu sắc.', 'Safe shallow pool exclusively for children with colorful and fun water play area.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'title' => t('Phòng gym miễn phí', 'Complimentary Gym'),
                        'desc'  => t('Sử dụng phòng gym hiện đại với đầy đủ trang thiết bị không tính phí trong giờ mở cửa.', 'Use the modern gym with full equipment at no charge during operating hours.'),
                        'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>',
                    ],
                    [
                        'title' => t('Lớp Yoga & Thiền', 'Yoga & Meditation'),
                        'desc'  => t('Lớp yoga và thiền định buổi sáng tại bãi biển — trải nghiệm cân bằng cơ thể và tâm trí dưới bầu trời.', 'Morning yoga and meditation classes on the beach — balance body and mind under the open sky.'),
                        'svg'   => '<circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>',
                    ],
                ];
                foreach ( $inclusions as $idx => $inc ) : ?>
                    <div class="ti-inclusion-card ti-reveal" style="transition-delay:<?php echo ($idx % 4) * 0.1; ?>s">
                        <div class="ti-inclusion-card__icon" aria-hidden="true">
                            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <?php echo $inc['svg']; ?>
                            </svg>
                        </div>
                        <h3 class="ti-inclusion-card__title"><?php echo esc_html($inc['title']); ?></h3>
                        <p class="ti-inclusion-card__desc"><?php echo esc_html($inc['desc']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         8. HOURS & LOCATION
    ═══════════════════════════════════════════════════ -->
    <section class="ti-hours-section" aria-label="Giờ hoạt động và vị trí">
        <div class="ti-container">
            <div class="ti-hours-grid">
                <!-- Operating Hours -->
                <div class="ti-reveal">
                    <div class="ti-hours__label"><?php echo t('Giờ hoạt động', 'Operating Hours'); ?></div>
                    <h2 class="ti-hours__title"><?php echo t('Lịch hoạt động<br>các tiện ích', 'Amenities<br>Schedule'); ?></h2>
                    <p class="ti-hours__desc">
                        <?php echo t(
                            'Mọi tiện ích được thiết kế để phục vụ bạn từ sáng sớm đến đêm khuya. Một số dịch vụ cần đặt lịch trước — đội ngũ concierge luôn sẵn sàng hỗ trợ.',
                            'All amenities are designed to serve you from early morning to late night. Some services require advance booking — our concierge team is always ready to assist.'
                        ); ?>
                    </p>
                    <table class="ti-hours__table" aria-label="Giờ hoạt động">
                        <tbody>
                            <?php
                            $hours = [
                                [ t('Hồ bơi vô cực', 'Infinity Pool'),         '07:00 – 22:00' ],
                                [ t('Bãi biển riêng', 'Private Beach'),          '06:00 – 20:00' ],
                                [ t('Spa & Wellness', 'Spa & Wellness'),          '09:00 – 21:00' ],
                                [ t('Nhà hàng chính', 'Main Restaurant'),        '06:00 – 23:00' ],
                                [ t('Beach Bar', 'Beach Bar'),                   '10:00 – 21:00' ],
                                [ t('Pool Bar', 'Pool Bar'),                     '09:00 – 22:00' ],
                                [ t('Phòng Gym', 'Fitness Center'),              '06:00 – 22:00' ],
                                [ t('Yoga & Thiền (bãi biển)', 'Yoga (Beach)'),  '06:30 – 08:00' ],
                            ];
                            foreach ($hours as $h) : ?>
                                <tr>
                                    <td>
                                        <span class="ti-hours__dot" aria-hidden="true"></span>
                                        <?php echo esc_html($h[0]); ?>
                                    </td>
                                    <td><?php echo esc_html($h[1]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Map Card -->
                <div class="ti-reveal ti-reveal-delay-2">
                    <div class="ti-map-card" aria-label="Vị trí">
                        <div class="ti-map-card__header">
                            <div class="ti-map-card__title"><?php echo t('Vị trí Resort', 'Resort Location'); ?></div>
                            <div class="ti-map-card__subtitle">
                                <?php echo t('Km 17, Đường 719 (Lạc Long Quân), Tiến Thành, Phan Thiết', 'Km 17, Road 719 (Lac Long Quan), Tien Thanh, Phan Thiet'); ?>
                            </div>
                        </div>
                        <div class="ti-map-placeholder" role="img" aria-label="Bản đồ vị trí Sonata Resort">
                            <div class="ti-map-pin">
                                <div class="ti-map-pin__dot" aria-hidden="true"></div>
                                <div class="ti-map-pin__label">Sonata Resort &amp; Spa</div>
                            </div>
                        </div>
                        <div class="ti-map-card__footer">
                            <address class="ti-map-card__address">
                                <?php echo t('Km 17, Đường 719', 'Km 17, Road 719'); ?><br>
                                <?php echo t('Tiến Thành, Bình Thuận', 'Tien Thanh, Binh Thuan'); ?>
                            </address>
                            <a
                                href="https://maps.google.com/?q=Sonata+Resort+Spa+Phan+Thiet"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ti-map-card__directions"
                                aria-label="<?php echo t('Xem bản đồ Google Maps', 'View on Google Maps'); ?>"
                            >
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <?php echo t('Xem bản đồ', 'Get Directions'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Quick contact -->
                    <div style="margin-top:24px;padding:24px;background:var(--ti-black-2);border:1px solid var(--ti-border);border-radius:8px;">
                        <div class="ti-hours__label" style="margin-bottom:12px;"><?php echo t('Liên hệ đặt dịch vụ', 'Book a Service'); ?></div>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            <a href="tel:+842523846768" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--ti-cream);transition:color 0.2s;" onmouseover="this.style.color='#b89d6c'" onmouseout="this.style.color='var(--ti-cream)'">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--ti-gold)" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                (+84) 252 384 6768
                            </a>
                            <a href="tel:+840975232968" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--ti-cream);transition:color 0.2s;" onmouseover="this.style.color='#b89d6c'" onmouseout="this.style.color='var(--ti-cream)'">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--ti-gold)" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                0975 232 968
                            </a>
                            <a href="mailto:salesgroup@sonataresort.com" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--ti-cream);transition:color 0.2s;" onmouseover="this.style.color='#b89d6c'" onmouseout="this.style.color='var(--ti-cream)'">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--ti-gold)" stroke-width="2" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
                                salesgroup@sonataresort.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         9. CTA BANNER
    ═══════════════════════════════════════════════════ -->
    <section class="ti-cta-section" aria-label="Đặt phòng">
        <div class="ti-container">
            <div class="ti-cta-inner ti-reveal">
                <div class="ti-cta__divider" aria-hidden="true">
                    <span class="ti-cta__divider-line"></span>
                    <span class="ti-cta__divider-diamond"></span>
                    <span class="ti-cta__divider-line"></span>
                </div>
                <h2 class="ti-cta__title">
                    <?php echo t(
                        'Sẵn sàng tận hưởng<br><em>kỳ nghỉ trọn vẹn?</em>',
                        'Ready to Experience<br><em>the Perfect Stay?</em>'
                    ); ?>
                </h2>
                <p class="ti-cta__desc">
                    <?php echo t(
                        'Đặt phòng ngay hôm nay để trải nghiệm tất cả tiện ích đẳng cấp tại Sonata Resort & Spa — nơi biển cả và sự sang trọng gặp nhau tại Phan Thiết.',
                        'Book today to experience all the world-class amenities at Sonata Resort & Spa — where the sea and luxury meet in Phan Thiet.'
                    ); ?>
                </p>
                <div class="ti-cta__actions">
                    <a href="<?php echo esc_url( home_url('/') ); ?>booking" class="ti-cta__btn-primary" id="ti-cta-book">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <?php echo t('Đặt phòng ngay', 'Reserve Now'); ?>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="<?php echo esc_url( home_url('/') ); ?>contact" class="ti-cta__btn-outline" id="ti-cta-contact">
                        <?php echo t('Liên hệ tư vấn', 'Contact Us'); ?>
                    </a>
                </div>
                <div class="ti-cta__tel">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    <?php echo t('Gọi ngay:', 'Call Now:'); ?>
                    <a href="tel:+842523846768">(+84) 252 384 6768</a>
                    &nbsp;/&nbsp;
                    <a href="tel:+840975232968">0975 232 968</a>
                </div>
            </div>
        </div>
    </section>

</div><!-- /.ti-page -->

<script>
(function () {
    /* ── Scroll reveal ── */
    var reveals = document.querySelectorAll('.ti-reveal');
    if (!reveals.length) return;

    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach(function (el) { io.observe(el); });

    /* ── Smooth scroll for anchor links ── */
    var anchors = document.querySelectorAll('a[href^="#ti-"]');
    anchors.forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── Tab navigation ── */
    var tabBtns = document.querySelectorAll('.ti-tab-btn');
    var tabPanes = document.querySelectorAll('.ti-tab-pane');
    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = btn.dataset.tab;
            tabBtns.forEach(function (b) { b.classList.remove('is-active'); });
            tabPanes.forEach(function (p) { p.classList.remove('is-active'); });
            btn.classList.add('is-active');
            var pane = document.getElementById(target);
            if (pane) pane.classList.add('is-active');
        });
    });
})();
</script>

<?php get_footer(); ?>
