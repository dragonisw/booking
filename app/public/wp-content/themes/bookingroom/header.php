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
            gap: 0;
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
            transition: color 0.22s ease;
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
            <li><a href="/vi" class="<?php echo (!defined('SITE_LANG') || SITE_LANG === 'vi' ? 'active' : ''); ?>">VI</a></li>
            <li class="sep">|</li>
            <li><a href="/en" class="<?php echo (defined('SITE_LANG') && SITE_LANG === 'en' ? 'active' : ''); ?>">EN</a></li>
        </ul>
</div>

                <!-- Nút đặt phòng (desktop) -->
                <a href="<?php echo esc_url(home_url('/booking')); ?>" class="btn-booking">
                    <?php echo t('Đặt phòng ngay', 'Book Now'); ?>
                </a>

                <!-- Hamburger (mobile) -->
                <button id="btn-hamburger" class="btn-hamburger" aria-label="<?php echo esc_attr(t('Mở menu', 'Open menu')); ?>" aria-expanded="false" aria-controls="mobile-menu-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
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
            <a href="<?php echo esc_url(home_url('/booking')); ?>">
                <?php echo t('Đặt phòng ngay', 'Book Now'); ?>
            </a>
        </div>
    </div>

    <!-- ══ CONTENT WRAPPER ══════════════════════════════════════════ -->
    <div id="content" class="site-content">