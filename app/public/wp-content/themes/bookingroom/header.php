<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>

    <style>
        /* ============================================================
           HEADER — Hoàn chỉnh, Pure CSS, không phụ thuộc Tailwind CDN
           ============================================================ */

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* ── Masthead Base ── */
        #masthead {
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background: #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            transition: background 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
        }

        /* Trang chủ: header cố định đè lên hero */
        .is-home-style #masthead {
            position: fixed !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        /* Khi scroll: glassmorphism */
        #masthead.is-scrolled {
            background: rgba(255,255,255,0.88) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08) !important;
            border-bottom: 1px solid rgba(226,232,240,0.7);
        }

        /* WordPress Admin Bar offset */
        .admin-bar #masthead { top: 32px; }
        @media screen and (max-width: 782px) {
            .admin-bar #masthead { top: 46px; }
        }

        /* ── Header Container ── */
        .header-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 3rem;
            height: 80px;
            box-sizing: border-box;
            transition: height 0.3s ease;
        }
        #masthead.is-scrolled .header-wrap {
            height: 64px;
        }

        /* ── Logo (trái) ── */
        .header-logo {
            flex: 1;
            display: flex;
            align-items: center;
            z-index: 2;
        }
        /* Text logo link (fallback) */
        .header-logo > a,
        .header-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 10px;
        }
        /* Custom logo link (do WordPress tạo ra qua the_custom_logo()) */
        .custom-logo-container,
        .custom-logo-container a,
        .custom-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .header-logo .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
            transition: transform 0.2s ease;
        }
        .header-logo a:hover .logo-icon {
            transform: scale(1.05);
        }
        .header-logo .site-name {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
            transition: color 0.2s;
        }
        .header-logo a:hover .site-name {
            color: #2563eb;
        }
        .custom-logo-container {
            display: flex;
            align-items: center;
        }

        <?php
        $raw_logo_height = get_theme_mod('logo_height', 80);
        $logo_height = min(max(absint($raw_logo_height), 30), 120);
        $logo_height_scrolled = max(36, $logo_height - 28);
        ?>
        .custom-logo-container img {
            max-height: <?php echo min($logo_height, 60) . 'px'; ?>;
            width: auto;
            display: block;
            transition: max-height 0.3s ease;
        }
        @media (min-width: 768px) {
            .custom-logo-container img {
                max-height: <?php echo $logo_height . 'px'; ?>;
            }
        }
        #masthead.is-scrolled .custom-logo-container img {
            max-height: <?php echo min($logo_height_scrolled, 44) . 'px'; ?>;
        }

        /* ── Desktop Navigation (giữa — absolute center) ── */
        #site-navigation {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: none;
            align-items: center;
            z-index: 3;  /* cao hơn logo (2) và actions (2) để nhận click */
            pointer-events: auto;
        }
        @media (min-width: 768px) {
            #site-navigation {
                display: flex !important;
            }
        }

        /* Nav list */
        #primary-menu-list {
            display: flex !important;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 16px; /* Tăng giãn cách giữa các menu item */
            white-space: nowrap;
        }
        #primary-menu-list > li {
            position: relative;
        }
        #primary-menu-list > li > a {
            display: block;
            padding: 0.5rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            position: relative;
            transition: all 0.22s ease;
            letter-spacing: 0.01em;
        }


        #primary-menu-list > li > a::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0.9rem;
            right: 0.9rem;
            height: 2px;
            background: #2563eb;
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.22s ease;
        }
        #primary-menu-list > li > a:hover {
            color: #2563eb;
        }
        #primary-menu-list > li > a:hover::after,
        #primary-menu-list > li.current-menu-item > a::after,
        #primary-menu-list > li.current-menu-ancestor > a::after {
            transform: scaleX(1);
        }
        #primary-menu-list > li.current-menu-item > a,
        #primary-menu-list > li.current-menu-ancestor > a {
            color: #2563eb;
        }

        /* Transparent header nav links */
        .is-home-style #masthead:not(.is-scrolled) #primary-menu-list > li > a {
            color: rgba(255,255,255,0.9);
        }
        .is-home-style #masthead:not(.is-scrolled) #primary-menu-list > li > a:hover,
        .is-home-style #masthead:not(.is-scrolled) #primary-menu-list > li.current-menu-item > a {
            color: #ffffff;
        }
        .is-home-style #masthead:not(.is-scrolled) #primary-menu-list > li > a::after {
            background: #ffffff;
        }

        /* ── Language Switcher ── */
        .lang-switcher {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 700;
            color: #475569;
        }
        .lang-switcher a {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s;
        }
        .lang-switcher a:hover,
        .lang-switcher a.active {
            color: #2563eb;
        }
        .lang-switcher .sep {
            color: #cbd5e1;
            font-weight: 400;
        }
        .is-home-style #masthead:not(.is-scrolled) .lang-switcher {
            color: rgba(255,255,255,0.9);
        }
        .is-home-style #masthead:not(.is-scrolled) .lang-switcher a:hover,
        .is-home-style #masthead:not(.is-scrolled) .lang-switcher a.active {
            color: #ffffff;
        }

        /* ── Header Actions (phải) ── */
        .header-actions {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            z-index: 2;
        }

        /* Nút Đặt phòng (desktop) */
        .btn-booking {
            display: none;
            padding: 0.55rem 1.4rem;
            background: #2563eb;
            color: #fff !important;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 9999px;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }
        .btn-booking:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.45);
        }
        @media (min-width: 768px) {
            .btn-booking { display: inline-block; }
        }

        /* Glassmorphism button trên trang chủ khi chưa scroll */
        .is-home-style #masthead:not(.is-scrolled) .btn-booking {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: none;
            color: #fff !important;
        }
        .is-home-style #masthead:not(.is-scrolled) .btn-booking:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Nút hamburger (mobile) */
        .btn-hamburger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #475569;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .btn-hamburger:hover {
            background: #f1f5f9;
        }
        .is-home-style #masthead:not(.is-scrolled) .btn-hamburger {
            color: #fff;
        }
        .is-home-style #masthead:not(.is-scrolled) .btn-hamburger:hover {
            background: rgba(255,255,255,0.15);
        }
        @media (min-width: 768px) {
            .btn-hamburger { display: none !important; }
        }

        /* ── Site Name màu trắng trên trang chủ ── */
        .is-home-style #masthead:not(.is-scrolled) .site-name {
            color: #ffffff !important;
        }
        .is-home-style #masthead:not(.is-scrolled) .logo-icon {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            box-shadow: none;
        }

        /* ============================================================
           MOBILE MENU
           ============================================================ */
        #mobile-menu-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #mobile-menu-overlay.is-open {
            display: block;
            opacity: 1;
        }

        #mobile-menu-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: min(320px, 85vw);
            background: #fff;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.32s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -8px 0 40px rgba(0,0,0,0.12);
        }
        #mobile-menu-drawer.is-open {
            transform: translateX(0);
        }

        /* Drawer header */
        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .drawer-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: #f8fafc;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            color: #64748b;
            transition: background 0.2s, color 0.2s;
        }
        .drawer-close:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        /* Drawer nav */
        .drawer-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        .drawer-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .drawer-nav ul li a {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .drawer-nav ul li a:hover,
        .drawer-nav ul li.current-menu-item a {
            color: #2563eb;
            background: #eff6ff;
            border-left-color: #2563eb;
        }

        /* Drawer footer */
        .drawer-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #f1f5f9;
        }
        .drawer-footer a {
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.9rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
            transition: all 0.2s;
        }
        .drawer-footer a:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.45);
        }

        /* ── Body push-down khi header fixed ── */
        .is-home-style #content {
            padding-top: 0;
        }

        /* ============================================================
           FULLSCREEN ICON BAR MENU (Overlay Menu)
           ============================================================ */

        /* Nút mở fullscreen menu (icon bar) */
        .btn-iconbar-menu {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 44px;
            height: 44px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            transition: background 0.2s ease;
            position: relative;
        }
        .btn-iconbar-menu:hover {
            background: rgba(0,0,0,0.06);
        }
        .btn-iconbar-menu .bar {
            display: block;
            width: 22px;
            height: 2px;
            background: #1e293b;
            border-radius: 2px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }
        .btn-iconbar-menu .bar:nth-child(2) {
            width: 16px;
            align-self: flex-start;
        }
        .btn-iconbar-menu:hover .bar:nth-child(2) {
            width: 22px;
        }
        .is-home-style #masthead:not(.is-scrolled) .btn-iconbar-menu .bar {
            background: #ffffff;
        }
        .is-home-style #masthead:not(.is-scrolled) .btn-iconbar-menu:hover {
            background: rgba(255,255,255,0.15);
        }
        /* Active state (menu đang mở) */
        .btn-iconbar-menu.is-active .bar:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .btn-iconbar-menu.is-active .bar:nth-child(2) {
            width: 22px;
            transform: scaleX(0);
            opacity: 0;
        }
        .btn-iconbar-menu.is-active .bar:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Fullscreen overlay container */
        #fs-menu-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            visibility: hidden;
            pointer-events: none;
        }
        #fs-menu-overlay.is-open {
            visibility: visible;
            pointer-events: auto;
        }

        /* Background curtain – hiệu ứng rèm kéo từ trên xuống */
        #fs-menu-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, #0a2618 0%, #0f3d22 40%, #0d2f1b 70%, #081a10 100%);
            transform: translateY(-100%);
            transition: transform 0.65s cubic-bezier(0.77, 0, 0.175, 1);
        }
        #fs-menu-overlay.is-open #fs-menu-bg {
            transform: translateY(0);
        }

        /* Texture overlay */
        #fs-menu-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(255,255,255,0.03) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(255,255,255,0.02) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Inner wrapper */
        #fs-menu-inner {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transition: opacity 0.3s ease 0.3s;
        }
        #fs-menu-overlay.is-open #fs-menu-inner {
            opacity: 1;
        }

        /* Header của overlay menu */
        .fs-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.4rem 3rem;
            flex-shrink: 0;
        }

        /* Nút đóng (X) */
        .fs-menu-close {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.85);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-family: inherit;
            transition: color 0.2s ease;
            padding: 0.5rem;
        }
        .fs-menu-close:hover {
            color: #ffffff;
        }
        .fs-menu-close .close-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 50%;
            transition: all 0.25s ease;
        }
        .fs-menu-close:hover .close-icon {
            border-color: rgba(255,255,255,0.7);
            background: rgba(255,255,255,0.08);
            transform: rotate(90deg);
        }

        /* Header lang switcher trong overlay */
        .fs-menu-lang {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }
        .fs-menu-lang a {
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: color 0.2s;
        }
        .fs-menu-lang a:hover,
        .fs-menu-lang a.active {
            color: #ffffff;
        }
        .fs-menu-lang .sep {
            color: rgba(255,255,255,0.25);
        }

        /* Body chính của overlay menu */
        .fs-menu-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 3rem;
        }

        /* Grid 3 cột: nav-trái | logo-giữa | nav-phải */
        .fs-menu-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 0 4rem;
            align-items: center;
            width: 100%;
            max-width: 1100px;
        }

        /* Cột nav */
        .fs-nav-col {
            display: flex;
            flex-direction: column;
        }
        .fs-nav-col.left {
            align-items: flex-end;
            text-align: right;
        }
        .fs-nav-col.right {
            align-items: flex-start;
            text-align: left;
        }

        /* Link items */
        .fs-nav-col ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        .fs-nav-col ul li a {
            display: inline-block;
            font-size: clamp(1.1rem, 2.2vw, 1.5rem);
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            padding: 0.55rem 0;
            position: relative;
            transition: color 0.25s ease;
            font-family: inherit;
        }
        .fs-nav-col ul li a::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255,255,255,0.4);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        .fs-nav-col.right ul li a::after {
            transform-origin: left;
        }
        .fs-nav-col.left ul li a::after {
            transform-origin: right;
        }
        .fs-nav-col ul li a:hover {
            color: #ffffff;
        }
        .fs-nav-col ul li a:hover::after {
            transform: scaleX(1);
        }
        .fs-nav-col ul li.current-menu-item a {
            color: #ffffff;
        }
        .fs-nav-col ul li.current-menu-item a::after {
            transform: scaleX(1);
            background: rgba(255,255,255,0.6);
        }

        /* Stagger animation cho các menu items */
        .fs-nav-col ul li {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.45s ease, transform 0.45s ease;
        }
        #fs-menu-overlay.is-open .fs-nav-col ul li {
            opacity: 1;
            transform: translateY(0);
        }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(1) { transition-delay: 0.35s; }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(2) { transition-delay: 0.42s; }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(3) { transition-delay: 0.49s; }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(4) { transition-delay: 0.56s; }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(5) { transition-delay: 0.63s; }
        #fs-menu-overlay.is-open .fs-nav-col ul li:nth-child(6) { transition-delay: 0.70s; }

        /* Logo giữa */
        .fs-menu-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            opacity: 0;
            transform: scale(0.88);
            transition: opacity 0.5s ease 0.4s, transform 0.5s ease 0.4s;
        }
        #fs-menu-overlay.is-open .fs-menu-logo {
            opacity: 1;
            transform: scale(1);
        }
        .fs-menu-logo img {
            max-height: 120px;
            max-width: 180px;
            width: auto;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }
        .fs-menu-logo .logo-text {
            font-size: 0.65rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            text-align: center;
        }

        /* Đường kẻ phân tách dọc */
        .fs-menu-divider {
            width: 1px;
            height: 0;
            background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.2), transparent);
            align-self: stretch;
            transition: height 0.6s ease 0.4s;
            margin: 0 auto;
        }
        #fs-menu-overlay.is-open .fs-menu-divider {
            height: 100%;
        }

        /* Footer của overlay: social icons */
        .fs-menu-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            padding: 1.5rem 3rem 2rem;
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.4s ease 0.65s, transform 0.4s ease 0.65s;
        }
        #fs-menu-overlay.is-open .fs-menu-footer {
            opacity: 1;
            transform: translateY(0);
        }
        .fs-social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .fs-social-link:hover {
            border-color: rgba(255,255,255,0.6);
            color: #ffffff;
            background: rgba(255,255,255,0.08);
            transform: translateY(-3px);
        }
        .fs-social-link svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* Responsive: mobile */
        @media (max-width: 767px) {
            .fs-menu-grid {
                grid-template-columns: 1fr;
                gap: 0;
                justify-items: center;
            }
            .fs-menu-divider {
                display: none;
            }
            .fs-nav-col.left,
            .fs-nav-col.right {
                align-items: center;
                text-align: center;
            }
            .fs-menu-logo {
                order: -1;
                margin-bottom: 1.5rem;
            }
            .fs-nav-col ul li a {
                font-size: 1rem;
            }
            .fs-menu-header {
                padding: 1rem 1.25rem;
            }
            .fs-menu-body {
                padding: 1rem 1.5rem;
                align-items: flex-start;
                overflow-y: auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── Scroll effect ──
            var header = document.getElementById('masthead');
            function onScroll() {
                if (window.scrollY > 20) {
                    header.classList.add('is-scrolled');
                } else {
                    header.classList.remove('is-scrolled');
                }
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // ── Mobile Drawer ──
            var overlay = document.getElementById('mobile-menu-overlay');
            var drawer  = document.getElementById('mobile-menu-drawer');
            var btnOpen = document.getElementById('btn-hamburger');
            var btnClose = document.getElementById('btn-drawer-close');

            function openDrawer() {
                overlay.style.display = 'block';
                // force reflow then add class for transition
                requestAnimationFrame(function() {
                    overlay.classList.add('is-open');
                    drawer.classList.add('is-open');
                });
                document.body.style.overflow = 'hidden';
            }
            function closeDrawer() {
                overlay.classList.remove('is-open');
                drawer.classList.remove('is-open');
                document.body.style.overflow = '';
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 320);
            }

            if (btnOpen) btnOpen.addEventListener('click', openDrawer);
            if (btnClose) btnClose.addEventListener('click', closeDrawer);
            if (overlay) overlay.addEventListener('click', closeDrawer);

            // Đóng khi click link trong drawer
            if (drawer) {
                drawer.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', closeDrawer);
                });
            }

            // ── Fullscreen Overlay Menu ──
            var fsOverlay    = document.getElementById('fs-menu-overlay');
            var btnIconbar   = document.getElementById('btn-iconbar-open');
            var btnFsClose   = document.getElementById('btn-fs-menu-close');

            function openFsMenu() {
                if (!fsOverlay) return;
                fsOverlay.classList.add('is-open');
                if (btnIconbar) btnIconbar.classList.add('is-active');
                document.body.style.overflow = 'hidden';
                if (btnIconbar) btnIconbar.setAttribute('aria-expanded', 'true');
            }

            function closeFsMenu() {
                if (!fsOverlay) return;
                fsOverlay.classList.remove('is-open');
                if (btnIconbar) btnIconbar.classList.remove('is-active');
                document.body.style.overflow = '';
                if (btnIconbar) btnIconbar.setAttribute('aria-expanded', 'false');
            }

            if (btnIconbar) btnIconbar.addEventListener('click', function(e) {
                e.stopPropagation();
                if (fsOverlay && fsOverlay.classList.contains('is-open')) {
                    closeFsMenu();
                } else {
                    openFsMenu();
                }
            });

            if (btnFsClose) btnFsClose.addEventListener('click', closeFsMenu);

            // Đóng khi nhấn Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && fsOverlay && fsOverlay.classList.contains('is-open')) {
                    closeFsMenu();
                }
            });

            // Đóng khi click vào link trong overlay
            if (fsOverlay) {
                fsOverlay.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        // Không đóng nếu là lang switcher (trang mới)
                        closeFsMenu();
                    });
                });
            }
        });
    </script>
</head>

<body <?php body_class(is_front_page() || is_home() || is_page_template('page-trang-chu.php') ? 'is-home-style' : ''); ?>>
    <?php wp_body_open(); ?>

    <!-- ══ HEADER ══════════════════════════════════════════════════ -->
    <header id="masthead" role="banner">
        <div class="header-wrap">

            <!-- Logo -->
            <div class="header-logo">
                <?php if (has_custom_logo()): ?>
                    <!-- the_custom_logo() đã tự tạo thẻ <a>, KHÔNG wrap thêm <a> nữa -->
                    <div class="custom-logo-container">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else: ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo-link">
                        <div class="logo-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="site-name"><?php bloginfo('name'); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Desktop Navigation -->
            <nav id="site-navigation" aria-label="<?php esc_attr_e('Main Menu', 'bookingroom'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary-menu',
                    'container'      => false,
                    'menu_id'        => 'primary-menu-list',
                    'menu_class'     => '',
                    'fallback_cb'    => function () {
                        if (current_user_can('manage_options')) {
                            printf(
                                '<p style="color:#b45309;font-size:12px;padding:6px 12px;background:#fef3c7;border-radius:8px;white-space:nowrap;">⚠ Chưa gán menu. <a href="%s" style="color:#2563eb;font-weight:700;">Gán ngay →</a></p>',
                                esc_url(admin_url('nav-menus.php'))
                            );
                        }
                    },
                    'items_wrap'     => '<ul id="primary-menu-list">%3$s</ul>',
                ));
                ?>
            </nav>

            <!-- Actions -->
            <div class="header-actions">
                <!-- Language Switcher -->
               <div class="lang-switcher">
        <ul style="display:flex; list-style:none; margin:0; padding:0; gap:6px;">
            <li><a href="<?php echo esc_url(bookingroom_get_lang_switch_url('vi')); ?>" class="<?php echo (!defined('SITE_LANG') || SITE_LANG === 'vi' ? 'active' : ''); ?>">VI</a></li>
            <li class="sep">|</li>
            <li><a href="<?php echo esc_url(bookingroom_get_lang_switch_url('en')); ?>" class="<?php echo (defined('SITE_LANG') && SITE_LANG === 'en' ? 'active' : ''); ?>">EN</a></li>
        </ul>
</div>

                <!-- Nút đặt phòng (desktop) -->
                <a href="<?php echo esc_url(get_theme_mod('header_btn_url', home_url('/booking'))); ?>" class="btn-booking">
                    <?php echo esc_html(get_theme_mod('header_btn_text', t('Đặt phòng ngay', 'Book Now'))); ?>
                </a>

                <!-- Hamburger (mobile) -->
                <button id="btn-hamburger" class="btn-hamburger" aria-label="<?php echo esc_attr(t('Mở menu', 'Open menu')); ?>" aria-expanded="false" aria-controls="mobile-menu-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Icon Bar Menu Button (Fullscreen Overlay) -->
                <button id="btn-iconbar-open" class="btn-iconbar-menu" aria-label="<?php echo esc_attr(t('Mở menu toàn màn hình', 'Open fullscreen menu')); ?>" aria-expanded="false" aria-controls="fs-menu-overlay">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>

        </div>
    </header>

    <!-- ══ MOBILE DRAWER ════════════════════════════════════════════ -->
    <div id="mobile-menu-overlay" role="dialog" aria-modal="true" aria-label="Mobile menu" style="display:none;"></div>

    <div id="mobile-menu-drawer">
        <!-- Drawer Header -->
        <div class="drawer-header">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration:none;">
                <?php if (has_custom_logo()): ?>
                    <div class="custom-logo-container" style="max-height:50px;">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else: ?>
                    <span style="font-size:1.1rem;font-weight:800;color:#1e293b;"><?php bloginfo('name'); ?></span>
                <?php endif; ?>
            </a>
            <button id="btn-drawer-close" class="drawer-close" aria-label="<?php echo esc_attr(t('Đóng menu', 'Close menu')); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Drawer Nav -->
        <nav class="drawer-nav" aria-label="Mobile Menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary-menu',
                'container'      => false,
                'menu_class'     => '',
                'fallback_cb'    => false,
            ));
            ?>
        </nav>

        <!-- Drawer Footer -->
        <div class="drawer-footer">
            <a href="<?php echo esc_url(get_theme_mod('header_btn_url', home_url('/booking'))); ?>">
                <?php echo esc_html(get_theme_mod('header_btn_text', t('Đặt phòng ngay', 'Book Now'))); ?>
            </a>
        </div>
    </div>

    <!-- ══ FULLSCREEN OVERLAY MENU ═════════════════════════════════ -->
    <div id="fs-menu-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Fullscreen Menu', 'bookingroom'); ?>">
        <!-- Background curtain -->
        <div id="fs-menu-bg"></div>

        <!-- Inner content -->
        <div id="fs-menu-inner">

            <!-- Header row: lang + close -->
            <div class="fs-menu-header">
                <!-- Lang switcher -->
                <div class="fs-menu-lang">
                    <a href="<?php echo esc_url(bookingroom_get_lang_switch_url('vi')); ?>" class="<?php echo (!defined('SITE_LANG') || SITE_LANG === 'vi' ? 'active' : ''); ?>">VI</a>
                    <span class="sep">|</span>
                    <a href="<?php echo esc_url(bookingroom_get_lang_switch_url('en')); ?>" class="<?php echo (defined('SITE_LANG') && SITE_LANG === 'en' ? 'active' : ''); ?>">EN</a>
                </div>

                <!-- Close button -->
                <button id="btn-fs-menu-close" class="fs-menu-close" aria-label="<?php echo esc_attr(t('Đóng menu', 'Close menu')); ?>">
                    <span><?php echo t('ĐÓNG', 'CLOSE'); ?></span>
                    <span class="close-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Main body: nav grid -->
            <div class="fs-menu-body">
                <div class="fs-menu-grid">

                    <!-- LEFT nav column -->
                    <div class="fs-nav-col left">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary-menu',
                            'container'      => false,
                            'items_wrap'     => '<ul>%3$s</ul>',
                            'fallback_cb'    => function() {
                                echo '<ul>';
                                $pages = array(
                                    t('Trang chủ', 'Home')       => home_url('/'),
                                    t('Giới thiệu', 'About')     => home_url('/gioi-thieu'),
                                    t('Vị trí', 'Location')      => home_url('/vi-tri'),
                                    t('Mặt bằng', 'Floor Plan')  => home_url('/mat-bang'),
                                );
                                foreach ($pages as $label => $url) {
                                    echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                                }
                                echo '</ul>';
                            },
                        ));
                        ?>
                    </div>

                    <!-- CENTER: logo + dividers -->
                    <div class="fs-menu-logo">
                        <?php if (has_custom_logo()): ?>
                            <?php the_custom_logo(); ?>
                        <?php else: ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration:none;color:#fff;font-size:1.3rem;font-weight:800;letter-spacing:0.05em;">
                                <?php bloginfo('name'); ?>
                            </a>
                        <?php endif; ?>
                        <span class="logo-text"><?php echo t('Khách sạn & Nghỉ dưỡng', 'Hotel & Resort'); ?></span>
                    </div>

                    <!-- RIGHT nav column -->
                    <div class="fs-nav-col right">
                        <?php
                        // Dùng menu phụ nếu có, nếu không dùng fallback tĩnh
                        $right_menu_args = array(
                            'theme_location' => 'fs-menu-right',
                            'container'      => false,
                            'items_wrap'     => '<ul>%3$s</ul>',
                            'fallback_cb'    => function() {
                                echo '<ul>';
                                $pages = array(
                                    t('Tiện ích', 'Amenities')   => home_url('/tien-ich'),
                                    t('Thư viện', 'Gallery')     => home_url('/thu-vien'),
                                    t('Tin tức', 'News')         => home_url('/tin-tuc'),
                                    t('Liên hệ', 'Contact')      => home_url('/lien-he'),
                                );
                                foreach ($pages as $label => $url) {
                                    echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                                }
                                echo '</ul>';
                            },
                        );
                        wp_nav_menu($right_menu_args);
                        ?>
                    </div>

                </div><!-- .fs-menu-grid -->
            </div><!-- .fs-menu-body -->

            <!-- Footer: Social icons -->
            <div class="fs-menu-footer">
                <?php
                $fb_url  = get_theme_mod('social_facebook', '#');
                $yt_url  = get_theme_mod('social_youtube', '#');
                $tt_url  = get_theme_mod('social_tiktok', '#');
                $ig_url  = get_theme_mod('social_instagram', '#');
                ?>
                <!-- Facebook -->
                <?php if (!empty($fb_url) && $fb_url !== '#') : ?>
                <a href="<?php echo esc_url($fb_url); ?>" class="fs-social-link" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <?php endif; ?>
                <!-- YouTube -->
                <?php if (!empty($yt_url) && $yt_url !== '#') : ?>
                <a href="<?php echo esc_url($yt_url); ?>" class="fs-social-link" target="_blank" rel="noopener" aria-label="YouTube">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <?php endif; ?>
                <!-- TikTok -->
                <?php if (!empty($tt_url) && $tt_url !== '#') : ?>
                <a href="<?php echo esc_url($tt_url); ?>" class="fs-social-link" target="_blank" rel="noopener" aria-label="TikTok">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                </a>
                <?php endif; ?>
                <!-- Instagram -->
                <?php if (!empty($ig_url) && $ig_url !== '#') : ?>
                <a href="<?php echo esc_url($ig_url); ?>" class="fs-social-link" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                </a>
                <?php endif; ?>
                <!-- Fallback: hiển thị icons mặc định nếu không có social URLs -->
                <?php if (empty($fb_url) || $fb_url === '#'): ?>
                <a href="#" class="fs-social-link" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="fs-social-link" aria-label="YouTube">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="#" class="fs-social-link" aria-label="TikTok">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                </a>
                <?php endif; ?>
            </div><!-- .fs-menu-footer -->

        </div><!-- #fs-menu-inner -->
    </div><!-- #fs-menu-overlay -->

    <!-- ══ CONTENT WRAPPER ══════════════════════════════════════════ -->
    <div id="content" class="site-content">