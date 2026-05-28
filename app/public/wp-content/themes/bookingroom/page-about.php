<?php
/*
 * Template Name: Giới thiệu
 * Description: Trang giới thiệu chuyên nghiệp
 */
get_header();

$pid = get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
function abp_meta( $key, $default = '' ) {
    $val = get_post_meta( get_the_ID(), $key, true );
    return $val !== '' ? $val : $default;
}
?>

<style>
/* =====================================================================
   ABOUT PAGE — Standalone CSS (no Tailwind dependency)
   ===================================================================== */
:root {
    --ab-blue:    #2563eb;
    --ab-blue-d:  #1d4ed8;
    --ab-cyan:    #06b6d4;
    --ab-purple:  #7c3aed;
    --ab-dark:    #0f172a;
    --ab-navy:    #1e293b;
    --ab-gray:    #64748b;
    --ab-light:   #f8fafc;
    --ab-border:  #e2e8f0;
    --ab-white:   #ffffff;
    --ab-radius:  20px;
    --ab-font:    'Inter', 'Segoe UI', ui-sans-serif, system-ui, sans-serif;
    --ab-ease:    cubic-bezier(0.16, 1, 0.3, 1);
}

.ab-page { font-family: var(--ab-font); }
.ab-page *, .ab-page *::before, .ab-page *::after { box-sizing: border-box; }
.ab-page img { max-width: 100%; display: block; }
.ab-page p, .ab-page h1, .ab-page h2, .ab-page h3, .ab-page h4 { margin: 0; }
.ab-page a { color: inherit; text-decoration: none; }
.ab-page ul { list-style: none; margin: 0; padding: 0; }

/* ── Container ── */
.ab-container {
    width: 100%;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 24px;
    padding-right: 24px;
}

/* ── Eyebrow label ── */
.ab-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--ab-blue);
    margin-bottom: 16px;
}
.ab-eyebrow::before {
    content: '';
    display: inline-block;
    width: 28px;
    height: 2px;
    background: linear-gradient(90deg, var(--ab-blue), var(--ab-cyan));
    border-radius: 2px;
    flex-shrink: 0;
}

/* ── Section Title ── */
.ab-section-title {
    font-size: clamp(1.75rem, 4vw, 3rem);
    font-weight: 800;
    color: var(--ab-dark);
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.ab-section-title .ab-accent {
    background: linear-gradient(135deg, var(--ab-blue) 0%, var(--ab-cyan) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.ab-section-desc {
    font-size: 1.0625rem;
    color: var(--ab-gray);
    line-height: 1.75;
    margin-top: 12px;
    max-width: 600px;
}

/* ────────────────────────────────────────────────────────────────
   1. HERO
──────────────────────────────────────────────────────────────── */
.ab-hero {
    position: relative;
    min-height: 540px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--ab-dark);
}
.ab-hero__bg {
    position: absolute;
    inset: 0;
    object-fit: cover;
    width: 100%;
    height: 100%;
    opacity: 0.45;
    transform: scale(1.04);
    animation: abHeroZoom 10s ease-out forwards;
}
@keyframes abHeroZoom {
    from { transform: scale(1.04); }
    to   { transform: scale(1); }
}
.ab-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        160deg,
        rgba(15,23,42,0.6) 0%,
        rgba(15,23,42,0.3) 50%,
        rgba(15,23,42,0.85) 100%
    );
}
.ab-hero__content {
    position: relative;
    z-index: 2;
    padding: 120px 0 80px;
    width: 100%;
}
.ab-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    margin-bottom: 20px;
    animation: abFadeUp 0.7s var(--ab-ease) 0.1s both;
}
.ab-hero__badge-dot {
    width: 6px;
    height: 6px;
    background: #60a5fa;
    border-radius: 50%;
    animation: abPulse 1.5s ease-in-out infinite;
}
@keyframes abPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50% { opacity:0.5; transform:scale(1.5); }
}
.ab-hero__title {
    font-size: clamp(2.5rem, 6vw, 5rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.1;
    letter-spacing: -0.03em;
    text-shadow: 0 4px 24px rgba(0,0,0,0.35);
    margin-bottom: 20px;
    animation: abFadeUp 0.8s var(--ab-ease) 0.25s both;
}
.ab-hero__title .ab-blue { color: #60a5fa; }
.ab-hero__subtitle {
    font-size: clamp(1rem, 2vw, 1.2rem);
    color: rgba(255,255,255,0.82);
    line-height: 1.7;
    max-width: 620px;
    animation: abFadeUp 0.8s var(--ab-ease) 0.4s both;
}
.ab-hero__breadcrumb {
    position: absolute;
    bottom: 28px;
    right: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8125rem;
    color: rgba(255,255,255,0.55);
    animation: abFadeUp 0.8s var(--ab-ease) 0.6s both;
}
.ab-hero__breadcrumb a { color: rgba(255,255,255,0.75); transition: color 0.2s; }
.ab-hero__breadcrumb a:hover { color: #fff; }
.ab-hero__breadcrumb-sep { opacity: 0.4; }

@keyframes abFadeUp {
    from { opacity:0; transform:translateY(28px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── Stat bar below hero ── */
.ab-statbar {
    background: var(--ab-white);
    border-bottom: 1px solid var(--ab-border);
    box-shadow: 0 4px 24px rgba(15,23,42,0.06);
}
.ab-statbar__inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
}
.ab-statbar__item {
    padding: 28px 24px;
    text-align: center;
    border-right: 1px solid var(--ab-border);
    position: relative;
    transition: background 0.25s;
}
.ab-statbar__item:last-child { border-right: none; }
.ab-statbar__item:hover { background: #f8fafc; }
.ab-statbar__num {
    font-size: 2rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, var(--ab-blue), var(--ab-cyan));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 4px;
}
.ab-statbar__label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ab-gray);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

/* ────────────────────────────────────────────────────────────────
   2. STORY
──────────────────────────────────────────────────────────────── */
.ab-story {
    padding: 100px 0;
    background: var(--ab-white);
    position: relative;
    overflow: hidden;
}
.ab-story::before {
    content:'';
    position:absolute;
    top:-200px; right:-200px;
    width:600px; height:600px;
    background: radial-gradient(circle, rgba(37,99,235,0.05) 0%, transparent 70%);
    border-radius:50%;
    pointer-events:none;
}
.ab-story__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}
/* Image */
.ab-story__img-wrap { position: relative; }
.ab-story__img-main {
    width: 100%;
    height: 520px;
    object-fit: cover;
    border-radius: 28px;
    box-shadow: 0 32px 80px rgba(15,23,42,0.18);
    display: block;
}
.ab-story__img-accent {
    position: absolute;
    bottom: -32px;
    right: -32px;
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 20px;
    border: 5px solid var(--ab-white);
    box-shadow: 0 16px 48px rgba(15,23,42,0.2);
}
.ab-story__badge-float {
    position: absolute;
    top: 32px;
    left: -24px;
    background: var(--ab-white);
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(15,23,42,0.14);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    white-space: nowrap;
}
.ab-story__badge-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--ab-blue), var(--ab-cyan));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.ab-story__badge-icon svg { color: #fff; }
.ab-story__badge-num {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--ab-dark);
    letter-spacing: -0.03em;
    line-height: 1;
}
.ab-story__badge-lbl {
    font-size: 0.75rem;
    color: var(--ab-gray);
    font-weight: 600;
    margin-top: 2px;
}
/* Text */
.ab-story__body { }
.ab-story__text {
    font-size: 1.0625rem;
    color: var(--ab-gray);
    line-height: 1.8;
    margin-top: 20px;
}
.ab-story__text p + p { margin-top: 16px; }
.ab-story__features {
    margin-top: 36px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.ab-story__feature {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    padding: 16px 20px;
    background: var(--ab-light);
    border-radius: 14px;
    border: 1px solid var(--ab-border);
    transition: box-shadow 0.25s, transform 0.25s;
}
.ab-story__feature:hover {
    box-shadow: 0 8px 32px rgba(37,99,235,0.1);
    transform: translateX(4px);
}
.ab-story__feature-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--ab-blue), var(--ab-cyan));
}
.ab-story__feature-icon svg { color: #fff; }
.ab-story__feature-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--ab-dark);
    margin-bottom: 3px;
}
.ab-story__feature-desc {
    font-size: 0.875rem;
    color: var(--ab-gray);
    line-height: 1.6;
}
.ab-story__cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 36px;
    background: linear-gradient(135deg, var(--ab-blue), var(--ab-blue-d));
    color: #fff;
    padding: 14px 28px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 0.9375rem;
    box-shadow: 0 8px 32px rgba(37,99,235,0.3);
    transition: transform 0.25s, box-shadow 0.25s;
}
.ab-story__cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 40px rgba(37,99,235,0.4);
}

/* ────────────────────────────────────────────────────────────────
   3. VALUES
──────────────────────────────────────────────────────────────── */
.ab-values {
    padding: 100px 0;
    background: linear-gradient(180deg, #f1f5f9 0%, var(--ab-white) 100%);
    position: relative;
}
.ab-values__head {
    text-align: center;
    max-width: 640px;
    margin: 0 auto 56px;
}
.ab-values__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.ab-value-card {
    background: var(--ab-white);
    border: 1px solid var(--ab-border);
    border-radius: var(--ab-radius);
    padding: 40px 32px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s var(--ab-ease), box-shadow 0.3s var(--ab-ease), border-color 0.3s;
}
.ab-value-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--ab-blue), var(--ab-cyan));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s var(--ab-ease);
}
.ab-value-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 64px rgba(15,23,42,0.12);
    border-color: rgba(37,99,235,0.2);
}
.ab-value-card:hover::before { transform: scaleX(1); }
.ab-value-card__icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}
.ab-value-card__num {
    position: absolute;
    top: 24px;
    right: 24px;
    font-size: 3.5rem;
    font-weight: 900;
    color: var(--ab-border);
    line-height: 1;
    letter-spacing: -0.05em;
    pointer-events: none;
    user-select: none;
}
.ab-value-card__title {
    font-size: 1.125rem;
    font-weight: 800;
    color: var(--ab-dark);
    margin-bottom: 10px;
}
.ab-value-card__desc {
    font-size: 0.9375rem;
    color: var(--ab-gray);
    line-height: 1.7;
}

/* ────────────────────────────────────────────────────────────────
   4. TIMELINE / MILESTONES
──────────────────────────────────────────────────────────────── */
.ab-timeline {
    padding: 100px 0;
    background: var(--ab-white);
}
.ab-timeline__head {
    text-align: center;
    max-width: 600px;
    margin: 0 auto 64px;
}
.ab-timeline__track {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}
.ab-timeline__track::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0; bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, var(--ab-blue) 0%, var(--ab-cyan) 100%);
    transform: translateX(-50%);
}
.ab-timeline__item {
    display: flex;
    gap: 40px;
    align-items: flex-start;
    margin-bottom: 60px;
    position: relative;
}
.ab-timeline__item:last-child { margin-bottom: 0; }
.ab-timeline__item:nth-child(even) { flex-direction: row-reverse; }
.ab-timeline__item:nth-child(even) .ab-timeline__card { text-align: right; }
.ab-timeline__year-wrap {
    flex-shrink: 0;
    width: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}
.ab-timeline__year {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--ab-blue), var(--ab-cyan));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
    box-shadow: 0 0 0 6px rgba(37,99,235,0.12), 0 4px 16px rgba(37,99,235,0.3);
    position: relative;
    z-index: 1;
}
.ab-timeline__card {
    flex: 1;
    background: var(--ab-light);
    border: 1px solid var(--ab-border);
    border-radius: 16px;
    padding: 24px 28px;
    transition: box-shadow 0.25s;
}
.ab-timeline__card:hover {
    box-shadow: 0 12px 40px rgba(15,23,42,0.1);
}
.ab-timeline__card-title {
    font-size: 1.0625rem;
    font-weight: 800;
    color: var(--ab-dark);
    margin-bottom: 6px;
}
.ab-timeline__card-desc {
    font-size: 0.9rem;
    color: var(--ab-gray);
    line-height: 1.65;
}

/* ────────────────────────────────────────────────────────────────
   5. TEAM
──────────────────────────────────────────────────────────────── */
.ab-team {
    padding: 100px 0;
    background: var(--ab-light);
}
.ab-team__head {
    text-align: center;
    max-width: 560px;
    margin: 0 auto 56px;
}
.ab-team__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}
.ab-team-card {
    background: var(--ab-white);
    border-radius: var(--ab-radius);
    overflow: hidden;
    border: 1px solid var(--ab-border);
    transition: transform 0.3s var(--ab-ease), box-shadow 0.3s;
    text-align: center;
}
.ab-team-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 60px rgba(15,23,42,0.12);
}
.ab-team-card__img-wrap {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
}
.ab-team-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s var(--ab-ease);
}
.ab-team-card:hover .ab-team-card__img { transform: scale(1.07); }
.ab-team-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 40%, rgba(15,23,42,0.7) 100%);
    opacity: 0;
    transition: opacity 0.3s;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 20px;
    gap: 10px;
}
.ab-team-card:hover .ab-team-card__overlay { opacity: 1; }
.ab-team-card__social-btn {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: background 0.2s;
    text-decoration: none;
}
.ab-team-card__social-btn:hover { background: rgba(255,255,255,0.3); }
.ab-team-card__body {
    padding: 24px 20px;
}
.ab-team-card__name {
    font-size: 1.0625rem;
    font-weight: 800;
    color: var(--ab-dark);
    margin-bottom: 4px;
}
.ab-team-card__role {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ab-blue);
    text-transform: uppercase;
    letter-spacing: 0.07em;
}

/* ────────────────────────────────────────────────────────────────
   6. TESTIMONIAL
──────────────────────────────────────────────────────────────── */
.ab-testimonial {
    padding: 100px 0;
    background: var(--ab-dark);
    position: relative;
    overflow: hidden;
}
.ab-testimonial::before {
    content: '\201C';
    position: absolute;
    top: -40px; left: 40px;
    font-size: 18rem;
    font-weight: 900;
    color: rgba(255,255,255,0.03);
    line-height: 1;
    pointer-events: none;
    user-select: none;
    font-family: Georgia, serif;
}
.ab-testimonial::after {
    content: '';
    position: absolute;
    top: -120px; right: -120px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.ab-testimonial__inner {
    position: relative;
    z-index: 1;
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}
.ab-testimonial__stars {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-bottom: 28px;
}
.ab-testimonial__star { color: #f59e0b; }
.ab-testimonial__quote {
    font-size: clamp(1.2rem, 3vw, 1.75rem);
    font-weight: 600;
    color: rgba(255,255,255,0.92);
    line-height: 1.65;
    font-style: italic;
    margin-bottom: 40px;
}
.ab-testimonial__author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}
.ab-testimonial__avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.2);
}
.ab-testimonial__author-name {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    text-align: left;
}
.ab-testimonial__author-role {
    font-size: 0.875rem;
    color: #60a5fa;
    text-align: left;
    margin-top: 2px;
}

/* ────────────────────────────────────────────────────────────────
   7. CTA BANNER
──────────────────────────────────────────────────────────────── */
.ab-cta {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--ab-blue) 0%, var(--ab-blue-d) 50%, #1e3a8a 100%);
    position: relative;
    overflow: hidden;
}
.ab-cta::before {
    content:'';
    position:absolute;
    top:-100px; right:-100px;
    width:400px; height:400px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}
.ab-cta::after {
    content:'';
    position:absolute;
    bottom:-80px; left:-80px;
    width:300px; height:300px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
    pointer-events: none;
}
.ab-cta__inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
}
.ab-cta__title {
    font-size: clamp(1.5rem, 3.5vw, 2.25rem);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.2;
}
.ab-cta__sub {
    font-size: 1rem;
    color: rgba(255,255,255,0.75);
    margin-top: 8px;
    line-height: 1.6;
}
.ab-cta__actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.ab-btn-white {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    color: var(--ab-blue);
    padding: 15px 30px;
    border-radius: 100px;
    font-weight: 800;
    font-size: 0.9375rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    transition: transform 0.25s, box-shadow 0.25s;
    white-space: nowrap;
}
.ab-btn-white:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 40px rgba(0,0,0,0.25);
}
.ab-btn-outline-white {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid rgba(255,255,255,0.5);
    color: #fff;
    padding: 13px 28px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 0.9375rem;
    transition: border-color 0.25s, background 0.25s;
    white-space: nowrap;
}
.ab-btn-outline-white:hover {
    border-color: #fff;
    background: rgba(255,255,255,0.1);
}

/* ────────────────────────────────────────────────────────────────
   Scroll Animation
──────────────────────────────────────────────────────────────── */
.ab-reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity 0.8s var(--ab-ease), transform 0.8s var(--ab-ease);
}
.ab-reveal.ab-visible {
    opacity: 1;
    transform: translateY(0);
}
.ab-reveal-delay-1 { transition-delay: 0.1s; }
.ab-reveal-delay-2 { transition-delay: 0.2s; }
.ab-reveal-delay-3 { transition-delay: 0.3s; }
.ab-reveal-delay-4 { transition-delay: 0.4s; }

/* ────────────────────────────────────────────────────────────────
   Responsive
──────────────────────────────────────────────────────────────── */
@media (max-width: 1024px) {
    .ab-story__grid { grid-template-columns: 1fr; gap: 48px; }
    .ab-story__img-accent { display: none; }
    .ab-story__badge-float { left: 16px; }
    .ab-values__grid { grid-template-columns: repeat(2, 1fr); }
    .ab-timeline__track::before { left: 28px; }
    .ab-timeline__item { flex-direction: column !important; padding-left: 72px; }
    .ab-timeline__item:nth-child(even) .ab-timeline__card { text-align: left; }
    .ab-timeline__year-wrap { position: absolute; left: 0; }
    .ab-statbar__inner { grid-template-columns: repeat(2, 1fr); }
    .ab-statbar__item:nth-child(2) { border-right: none; }
}
@media (max-width: 768px) {
    .ab-hero__content { padding: 100px 0 60px; }
    .ab-story, .ab-values, .ab-timeline, .ab-team, .ab-testimonial, .ab-cta { padding: 64px 0; }
    .ab-values__grid { grid-template-columns: 1fr; }
    .ab-team__grid { grid-template-columns: repeat(2, 1fr); }
    .ab-cta__inner { flex-direction: column; text-align: center; }
    .ab-cta__actions { justify-content: center; }
}
@media (max-width: 480px) {
    .ab-team__grid { grid-template-columns: 1fr; max-width: 340px; margin: 0 auto; }
    .ab-statbar__inner { grid-template-columns: repeat(2, 1fr); }
}
</style>

<main class="ab-page" id="ab-main">

    <?php
    // ── Data ──────────────────────────────────────────────────────────────────
    $hero_title    = abp_meta('_about_hero_title',    'Về <span class="ab-blue">Chúng Tôi</span>');
    $hero_sub      = abp_meta('_about_hero_subtitle', 'Hành trình của chúng tôi bắt đầu từ niềm đam mê khám phá và khát khao mang lại những trải nghiệm nghỉ dưỡng đẳng cấp nhất.');
    $hero_bg       = abp_meta('_about_hero_bg_url',   'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=85&w=2000');

    $stat1_num = abp_meta('_about_stat1_num', '500+');   $stat1_lbl = abp_meta('_about_stat1_lbl', 'Khách sạn');
    $stat2_num = abp_meta('_about_stat2_num', '15K+');   $stat2_lbl = abp_meta('_about_stat2_lbl', 'Lượt đặt phòng');
    $stat3_num = abp_meta('_about_stat3_num', '10+');    $stat3_lbl = abp_meta('_about_stat3_lbl', 'Năm kinh nghiệm');
    $stat4_num = abp_meta('_about_stat4_num', '4.9★');   $stat4_lbl = abp_meta('_about_stat4_lbl', 'Đánh giá trung bình');

    $story_heading = abp_meta('_about_story_heading', 'Kiến tạo kỷ niệm <span class="ab-accent">nghỉ dưỡng</span> vô giá');
    $story_text    = abp_meta('_about_story_text', '<p>Được thành lập với sứ mệnh nâng tầm trải nghiệm du lịch Việt, chúng tôi không chỉ là một đại lý đặt phòng. Chúng tôi là người bạn đồng hành, giúp bạn tìm kiếm những không gian sống đẳng cấp và tinh tế nhất.</p><p>Mỗi khách sạn, mỗi căn phòng đều được lựa chọn kỹ lưỡng dựa trên tiêu chuẩn khắt khe về chất lượng dịch vụ, phong cách kiến trúc và sự tiện nghi vượt trội.</p>');
    $story_badge_num = abp_meta('_about_story_badge_num', '10+');
    $story_badge_lbl = abp_meta('_about_story_badge_lbl', 'Năm kinh nghiệm');
    $story_img1    = abp_meta('_about_story_img1', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&q=80&w=900');
    $story_img2    = abp_meta('_about_story_img2', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=400');
    $story_cta_text= abp_meta('_about_story_cta_text', 'Xem phòng nghỉ của chúng tôi');
    $story_cta_url = abp_meta('_about_story_cta_url', home_url('/rooms'));

    $features = [
        [ 'title' => abp_meta('_about_feat1_title', 'Không gian sang trọng, đẳng cấp 5 sao'), 'desc' => abp_meta('_about_feat1_desc', 'Thiết kế tinh tế, nội thất cao cấp mang lại cảm giác thư thái tuyệt đối.'), 'color' => '#eff6ff', 'icon_color' => 'var(--ab-blue)', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>'],
        [ 'title' => abp_meta('_about_feat2_title', 'Đặt phòng nhanh chóng, xác nhận tức thì'), 'desc' => abp_meta('_about_feat2_desc', 'Quy trình đặt phòng đơn giản, xác nhận ngay lập tức qua email và SMS.'), 'color' => '#f0fdf4', 'icon_color' => '#16a34a', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [ 'title' => abp_meta('_about_feat3_title', 'Cam kết giá tốt nhất, hoàn tiền 100%'), 'desc' => abp_meta('_about_feat3_desc', 'Giá ưu đãi độc quyền, chính sách hoàn tiền minh bạch khi có thay đổi.'), 'color' => '#fff7ed', 'icon_color' => '#ea580c', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    ];

    $values = [
        [ 'title' => abp_meta('_about_val1_title', 'Chất lượng hàng đầu'),  'desc' => abp_meta('_about_val1_desc', 'Chỉ hợp tác với các đối tác đạt tiêu chuẩn 4–5 sao quốc tế, đảm bảo mọi trải nghiệm đều hoàn hảo.'), 'bg' => '#eff6ff', 'color' => 'var(--ab-blue)', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        [ 'title' => abp_meta('_about_val2_title', 'Giá tốt nhất'),         'desc' => abp_meta('_about_val2_desc', 'Nhờ mạng lưới đối tác rộng lớn, chúng tôi luôn có mức giá ưu đãi đặc quyền dành riêng cho khách hàng.'), 'bg' => '#f0fdf4', 'color' => '#16a34a', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [ 'title' => abp_meta('_about_val3_title', 'Hỗ trợ 24/7'),          'desc' => abp_meta('_about_val3_desc', 'Đội ngũ chuyên viên luôn sẵn sàng hỗ trợ bạn bất kể thời gian nào, mọi nơi trên thế giới.'), 'bg' => '#fdf4ff', 'color' => '#9333ea', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>'],
    ];

    $milestones = [
        [ 'year' => abp_meta('_about_ms1_year', '2015'), 'title' => abp_meta('_about_ms1_title', 'Thành lập công ty'), 'desc' => abp_meta('_about_ms1_desc', 'Bắt đầu với đội ngũ 5 người và giấc mơ thay đổi ngành du lịch Việt Nam.') ],
        [ 'year' => abp_meta('_about_ms2_year', '2017'), 'title' => abp_meta('_about_ms2_title', 'Mở rộng toàn quốc'), 'desc' => abp_meta('_about_ms2_desc', 'Phủ sóng hơn 20 tỉnh thành, hợp tác với 100+ khách sạn đối tác uy tín.') ],
        [ 'year' => abp_meta('_about_ms3_year', '2020'), 'title' => abp_meta('_about_ms3_title', 'Ra mắt nền tảng online'), 'desc' => abp_meta('_about_ms3_desc', 'Hệ thống đặt phòng trực tuyến 24/7, xác nhận tức thì và thanh toán an toàn.') ],
        [ 'year' => abp_meta('_about_ms4_year', '2025'), 'title' => abp_meta('_about_ms4_title', 'Đạt 15.000+ khách hàng'), 'desc' => abp_meta('_about_ms4_desc', 'Cột mốc tự hào với hơn 15.000 lượt đặt phòng thành công và điểm đánh giá 4.9/5.') ],
    ];

    $team = [
        [ 'name' => abp_meta('_about_team1_name', 'Nguyễn Văn An'), 'role' => abp_meta('_about_team1_role', 'Giám đốc điều hành'), 'img' => abp_meta('_about_team1_img', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=600') ],
        [ 'name' => abp_meta('_about_team2_name', 'Trần Thị Bình'), 'role' => abp_meta('_about_team2_role', 'Giám đốc vận hành'), 'img' => abp_meta('_about_team2_img', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=600') ],
        [ 'name' => abp_meta('_about_team3_name', 'Lê Minh Tuấn'), 'role' => abp_meta('_about_team3_role', 'Trưởng phòng kinh doanh'), 'img' => abp_meta('_about_team3_img', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=600') ],
    ];

    $testi_quote = abp_meta('_about_testi_quote', '"Dịch vụ thật sự vượt ngoài mong đợi. Họ không chỉ tìm cho tôi một căn phòng đẹp, mà còn tư vấn những điểm ăn uống rất tinh tế. Chắc chắn tôi sẽ quay lại lần nữa."');
    $testi_name  = abp_meta('_about_testi_name', 'Anh Minh Nguyễn');
    $testi_role  = abp_meta('_about_testi_role', 'Giám đốc điều hành, TechCorp Việt Nam');
    $testi_img   = abp_meta('_about_testi_img', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=120');

    $cta_title   = abp_meta('_about_cta_title', 'Sẵn sàng cho chuyến nghỉ dưỡng hoàn hảo?');
    $cta_sub     = abp_meta('_about_cta_sub', 'Đặt phòng ngay hôm nay và nhận ưu đãi đặc biệt dành cho khách hàng mới.');
    $cta_btn1    = abp_meta('_about_cta_btn1', 'Đặt phòng ngay');
    $cta_btn1_url= abp_meta('_about_cta_btn1_url', home_url('/booking'));
    $cta_btn2    = abp_meta('_about_cta_btn2', 'Liên hệ với chúng tôi');
    $cta_btn2_url= abp_meta('_about_cta_btn2_url', home_url('/contact'));
    ?>

    <!-- ══════════════════════════════════════════════════════
         SECTION 1 · HERO
    ══════════════════════════════════════════════════════ -->
    <section class="ab-hero" id="ab-hero" aria-label="Giới thiệu">
        <img
            class="ab-hero__bg"
            src="<?php echo esc_url( $hero_bg ); ?>"
            alt="Ảnh nền giới thiệu"
            loading="eager"
        >
        <div class="ab-hero__overlay" aria-hidden="true"></div>

        <div class="ab-hero__content">
            <div class="ab-container" style="position:relative;">
                <div class="ab-hero__badge">
                    <span class="ab-hero__badge-dot" aria-hidden="true"></span>
                    <?php echo esc_html( bloginfo('name') ); ?> · Câu chuyện của chúng tôi
                </div>
                <h1 class="ab-hero__title"><?php echo wp_kses_post( $hero_title ); ?></h1>
                <p class="ab-hero__subtitle"><?php echo esc_html( $hero_sub ); ?></p>

                <!-- Breadcrumb -->
                <nav class="ab-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url( home_url('/') ); ?>">Trang chủ</a>
                    <span class="ab-hero__breadcrumb-sep" aria-hidden="true">/</span>
                    <span>Giới thiệu</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         STAT BAR
    ══════════════════════════════════════════════════════ -->
    <div class="ab-statbar" role="region" aria-label="Thống kê">
        <div class="ab-container">
            <div class="ab-statbar__inner">
                <?php
                $stats = [
                    [ $stat1_num, $stat1_lbl ],
                    [ $stat2_num, $stat2_lbl ],
                    [ $stat3_num, $stat3_lbl ],
                    [ $stat4_num, $stat4_lbl ],
                ];
                foreach ( $stats as $s ) : ?>
                    <div class="ab-statbar__item ab-reveal">
                        <div class="ab-statbar__num"><?php echo esc_html( $s[0] ); ?></div>
                        <div class="ab-statbar__label"><?php echo esc_html( $s[1] ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════
         SECTION 2 · OUR STORY
    ══════════════════════════════════════════════════════ -->
    <section class="ab-story" aria-label="Câu chuyện của chúng tôi">
        <div class="ab-container">
            <div class="ab-story__grid">

                <!-- Image Side -->
                <div class="ab-story__img-wrap ab-reveal">
                    <img
                        class="ab-story__img-main"
                        src="<?php echo esc_url( $story_img1 ); ?>"
                        alt="Nội thất khách sạn cao cấp"
                        loading="lazy"
                    >
                    <img
                        class="ab-story__img-accent"
                        src="<?php echo esc_url( $story_img2 ); ?>"
                        alt="Hồ bơi resort"
                        loading="lazy"
                    >
                    <!-- Floating Badge -->
                    <div class="ab-story__badge-float">
                        <div class="ab-story__badge-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="ab-story__badge-num"><?php echo esc_html( $story_badge_num ); ?></div>
                            <div class="ab-story__badge-lbl"><?php echo esc_html( $story_badge_lbl ); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Text Side -->
                <div class="ab-story__body ab-reveal ab-reveal-delay-2">
                    <div class="ab-eyebrow">Câu chuyện của chúng tôi</div>
                    <h2 class="ab-section-title"><?php echo wp_kses_post( $story_heading ); ?></h2>
                    <div class="ab-story__text"><?php echo wp_kses_post( $story_text ); ?></div>

                    <!-- Features -->
                    <ul class="ab-story__features" aria-label="Điểm nổi bật">
                        <?php foreach ( $features as $f ) : ?>
                            <li class="ab-story__feature">
                                <div class="ab-story__feature-icon" style="background:linear-gradient(135deg,<?php echo esc_attr($f['icon_color']); ?>,var(--ab-cyan));" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                        <?php echo $f['svg']; ?>
                                    </svg>
                                </div>
                                <div>
                                    <div class="ab-story__feature-title"><?php echo esc_html( $f['title'] ); ?></div>
                                    <div class="ab-story__feature-desc"><?php echo esc_html( $f['desc'] ); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <a href="<?php echo esc_url( $story_cta_url ); ?>" class="ab-story__cta">
                        <?php echo esc_html( $story_cta_text ); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         SECTION 3 · CORE VALUES
    ══════════════════════════════════════════════════════ -->
    <section class="ab-values" aria-label="Giá trị cốt lõi">
        <div class="ab-container">
            <div class="ab-values__head ab-reveal">
                <div class="ab-eyebrow" style="justify-content:center;">Giá trị cốt lõi</div>
                <h2 class="ab-section-title" style="text-align:center;">
                    Cam kết của <span class="ab-accent">chúng tôi</span>
                </h2>
                <p class="ab-section-desc" style="margin:12px auto 0; text-align:center;">
                    Những giá trị định hướng mọi quyết định và dịch vụ chúng tôi cung cấp.
                </p>
            </div>

            <div class="ab-values__grid">
                <?php foreach ( $values as $i => $v ) : ?>
                    <div class="ab-value-card ab-reveal ab-reveal-delay-<?php echo $i + 1; ?>" role="article">
                        <div class="ab-value-card__num" aria-hidden="true">0<?php echo $i + 1; ?></div>
                        <div class="ab-value-card__icon" style="background:<?php echo esc_attr($v['bg']); ?>;" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="<?php echo esc_attr($v['color']); ?>" stroke-width="1.8">
                                <?php echo $v['svg']; ?>
                            </svg>
                        </div>
                        <h3 class="ab-value-card__title"><?php echo esc_html( $v['title'] ); ?></h3>
                        <p class="ab-value-card__desc"><?php echo esc_html( $v['desc'] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         SECTION 4 · TIMELINE / MILESTONES
    ══════════════════════════════════════════════════════ -->
    <section class="ab-timeline" aria-label="Các cột mốc phát triển">
        <div class="ab-container">
            <div class="ab-timeline__head ab-reveal">
                <div class="ab-eyebrow" style="justify-content:center;">Hành trình phát triển</div>
                <h2 class="ab-section-title" style="text-align:center;">
                    Những cột mốc <span class="ab-accent">đáng nhớ</span>
                </h2>
            </div>

            <div class="ab-timeline__track" role="list">
                <?php foreach ( $milestones as $i => $m ) : ?>
                    <div class="ab-timeline__item ab-reveal ab-reveal-delay-<?php echo ($i % 2) + 1; ?>" role="listitem">
                        <div class="ab-timeline__year-wrap" aria-hidden="true">
                            <div class="ab-timeline__year"><?php echo esc_html( $m['year'] ); ?></div>
                        </div>
                        <div class="ab-timeline__card">
                            <h3 class="ab-timeline__card-title"><?php echo esc_html( $m['title'] ); ?></h3>
                            <p class="ab-timeline__card-desc"><?php echo esc_html( $m['desc'] ); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         SECTION 5 · TEAM
    ══════════════════════════════════════════════════════ -->
    <section class="ab-team" aria-label="Đội ngũ của chúng tôi">
        <div class="ab-container">
            <div class="ab-team__head ab-reveal">
                <div class="ab-eyebrow" style="justify-content:center;">Đội ngũ</div>
                <h2 class="ab-section-title" style="text-align:center;">
                    Những <span class="ab-accent">con người</span> tạo nên chúng tôi
                </h2>
            </div>

            <div class="ab-team__grid">
                <?php foreach ( $team as $i => $member ) : ?>
                    <article class="ab-team-card ab-reveal ab-reveal-delay-<?php echo $i + 1; ?>">
                        <div class="ab-team-card__img-wrap">
                            <img
                                class="ab-team-card__img"
                                src="<?php echo esc_url( $member['img'] ); ?>"
                                alt="<?php echo esc_attr( $member['name'] ); ?>"
                                loading="lazy"
                            >
                            <div class="ab-team-card__overlay" aria-hidden="true">
                                <a href="#" class="ab-team-card__social-btn" tabindex="-1" aria-label="LinkedIn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                                <a href="#" class="ab-team-card__social-btn" tabindex="-1" aria-label="Email">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>
                                </a>
                            </div>
                        </div>
                        <div class="ab-team-card__body">
                            <h3 class="ab-team-card__name"><?php echo esc_html( $member['name'] ); ?></h3>
                            <p class="ab-team-card__role"><?php echo esc_html( $member['role'] ); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         SECTION 6 · TESTIMONIAL
    ══════════════════════════════════════════════════════ -->
    <section class="ab-testimonial" aria-label="Nhận xét từ khách hàng">
        <div class="ab-container">
            <div class="ab-testimonial__inner ab-reveal">
                <!-- Stars -->
                <div class="ab-testimonial__stars" aria-label="5 sao">
                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                        <svg class="ab-testimonial__star" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <blockquote class="ab-testimonial__quote">
                    <?php echo wp_kses_post( $testi_quote ); ?>
                </blockquote>
                <div class="ab-testimonial__author">
                    <img
                        class="ab-testimonial__avatar"
                        src="<?php echo esc_url( $testi_img ); ?>"
                        alt="<?php echo esc_attr( $testi_name ); ?>"
                        loading="lazy"
                    >
                    <div>
                        <div class="ab-testimonial__author-name"><?php echo esc_html( $testi_name ); ?></div>
                        <div class="ab-testimonial__author-role"><?php echo esc_html( $testi_role ); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════
         SECTION 7 · CTA
    ══════════════════════════════════════════════════════ -->
    <section class="ab-cta" aria-label="Kêu gọi hành động">
        <div class="ab-container">
            <div class="ab-cta__inner">
                <div class="ab-reveal">
                    <h2 class="ab-cta__title"><?php echo wp_kses_post( $cta_title ); ?></h2>
                    <p class="ab-cta__sub"><?php echo esc_html( $cta_sub ); ?></p>
                </div>
                <div class="ab-cta__actions ab-reveal ab-reveal-delay-2">
                    <a href="<?php echo esc_url( $cta_btn1_url ); ?>" class="ab-btn-white">
                        <?php echo esc_html( $cta_btn1 ); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url( $cta_btn2_url ); ?>" class="ab-btn-outline-white">
                        <?php echo esc_html( $cta_btn2 ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main><!-- .ab-page -->

<script>
(function() {
    'use strict';

    /* ── Scroll reveal ── */
    var revealEls = document.querySelectorAll('.ab-reveal');
    if (!revealEls.length) return;

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('ab-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(function(el) { observer.observe(el); });

    /* ── Stat counter animation ── */
    var statNums = document.querySelectorAll('.ab-statbar__num');
    var statObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var raw = el.textContent.trim();
            var num = parseFloat(raw.replace(/[^0-9.]/g, ''));
            var suffix = raw.replace(/[0-9.]/g, '');
            if (isNaN(num)) return;
            var start = 0;
            var duration = 1600;
            var startTime = null;
            function step(ts) {
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = (num < 100 ? (eased * num).toFixed(1).replace('.0','') : Math.round(eased * num)) + suffix;
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
            statObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    statNums.forEach(function(el) { statObserver.observe(el); });
})();
</script>

<?php get_footer(); ?>