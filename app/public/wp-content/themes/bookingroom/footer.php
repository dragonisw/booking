</div><!-- #content -->

<footer id="colophon" class="br-footer" role="contentinfo">

    <!-- Top Decorative Bar -->
    <div class="br-footer__topbar"></div>

    <!-- Main Footer Body -->
    <div class="br-footer__body">
        <div class="br-footer__container">
            <div class="br-footer__grid">

                <!-- Column 1: Branding -->
                <div class="br-footer__col br-footer__col--brand">
                    <div class="br-footer__logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?> - Trang chủ">
                            <?php if (has_custom_logo()) : ?>
                                <div class="br-footer__logo-img">
                                    <?php the_custom_logo(); ?>
                                </div>
                            <?php else : ?>
                                <span class="br-footer__logo-text"><?php bloginfo('name'); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    <p class="br-footer__tagline">
                        <?php echo esc_html(get_bloginfo('description')) ?: 'Mang đến trải nghiệm đặt phòng khách sạn dễ dàng, nhanh chóng và tin cậy nhất cho kỳ nghỉ của bạn.'; ?>
                    </p>

                    <!-- Contact Info -->
                    <ul class="br-footer__contact">
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                            </span>
                            <span><?php echo esc_html(get_theme_mod('footer_address', '123 Đường Lê Lợi, Q.1, TP.HCM')); ?></span>
                        </li>
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/></svg>
                            </span>
                            <a href="tel:<?php echo esc_attr(get_theme_mod('footer_phone', '+84901234567')); ?>" class="br-footer__contact-link">
                                <?php echo esc_html(get_theme_mod('footer_phone', '+84 90 123 4567')); ?>
                            </a>
                        </li>
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>
                            </span>
                            <a href="mailto:<?php echo esc_attr(get_theme_mod('footer_email', 'info@travelrooms.vn')); ?>" class="br-footer__contact-link">
                                <?php echo esc_html(get_theme_mod('footer_email', 'info@travelrooms.vn')); ?>
                            </a>
                        </li>
                    </ul>

                    <!-- Social Media -->
                    <div class="br-footer__social">
                        <?php
                        $socials = [
                            'facebook'  => ['label' => 'Facebook',  'url' => get_theme_mod('social_facebook', '#'), 'path' => 'M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z'],
                            'instagram' => ['label' => 'Instagram', 'url' => get_theme_mod('social_instagram', '#'), 'path' => 'M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z'],
                            'youtube'   => ['label' => 'YouTube',   'url' => get_theme_mod('social_youtube', '#'),   'path' => 'M19.59 6.69a4.83 4.83 0 01-3.77-2.75 12.64 12.64 0 00-4-.54 12.64 12.64 0 00-4 .54 4.83 4.83 0 01-3.77 2.75A48.67 48.67 0 004 12a48.67 48.67 0 00.54 5.31 4.83 4.83 0 003.77 2.75 12.64 12.64 0 004 .54 12.64 12.64 0 004-.54 4.83 4.83 0 003.77-2.75A48.67 48.67 0 0020 12a48.67 48.67 0 00-.41-5.31zM9.75 15V9l5.25 3-5.25 3z'],
                        ];
                        foreach ($socials as $key => $s) : ?>
                            <a href="<?php echo esc_url($s['url']); ?>" class="br-footer__social-link" aria-label="<?php echo esc_attr($s['label']); ?>" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true">
                                    <path d="<?php echo esc_attr($s['path']); ?>"/>
                                </svg>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 2: Khám phá -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-accent"></span>
                        Khám phá
                    </h3>
                    <?php
                    if (has_nav_menu('footer-explore')) {
                        wp_nav_menu([
                            'theme_location' => 'footer-explore',
                            'menu_class'     => 'br-footer__nav',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                    } else { ?>
                        <ul class="br-footer__nav">
                            <li><a href="<?php echo esc_url(home_url('/destination')); ?>">Địa điểm</a></li>
                            <li><a href="<?php echo esc_url(home_url('/services')); ?>">Dịch vụ</a></li>
                            <li><a href="<?php echo esc_url(home_url('/uu-dai')); ?>">Ưu đãi</a></li>
                            <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
                            <li><a href="<?php echo esc_url(home_url('/rooms')); ?>">Phòng nghỉ</a></li>
                        </ul>
                    <?php } ?>
                </div>

                <!-- Column 3: Hỗ trợ -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-accent"></span>
                        Hỗ trợ
                    </h3>
                    <?php
                    if (has_nav_menu('footer-support')) {
                        wp_nav_menu([
                            'theme_location' => 'footer-support',
                            'menu_class'     => 'br-footer__nav',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                    } else { ?>
                        <ul class="br-footer__nav">
                            <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Liên hệ</a></li>
                            <li><a href="<?php echo esc_url(home_url('/faq')); ?>">Câu hỏi thường gặp</a></li>
                            <li><a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat')); ?>">Chính sách bảo mật</a></li>
                            <li><a href="<?php echo esc_url(home_url('/dieu-khoan-dich-vu')); ?>">Điều khoản dịch vụ</a></li>
                            <li><a href="<?php echo esc_url(home_url('/tra-cuu-phong')); ?>">Tra cứu đặt phòng</a></li>
                        </ul>
                    <?php } ?>
                </div>

                <!-- Column 4: Newsletter + Badges -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-accent"></span>
                        Nhận ưu đãi độc quyền
                    </h3>
                    <p class="br-footer__newsletter-desc">Đăng ký nhận thông tin về các ưu đãi và phòng nghỉ mới nhất.</p>

                    <form class="br-footer__newsletter-form" action="#" method="post" novalidate>
                        <?php wp_nonce_field('br_newsletter_nonce', 'br_newsletter_nonce'); ?>
                        <div class="br-footer__newsletter-input-wrap">
                            <label for="br-newsletter-email" class="sr-only">Email của bạn</label>
                            <input
                                type="email"
                                id="br-newsletter-email"
                                name="newsletter_email"
                                placeholder="Email của bạn..."
                                class="br-footer__newsletter-input"
                                required
                                autocomplete="email"
                            >
                            <button type="submit" class="br-footer__newsletter-btn" aria-label="Đăng ký nhận tin">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                    <path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Trust Badges -->
                    <div class="br-footer__badges">
                        <p class="br-footer__badges-label">Thanh toán an toàn</p>
                        <div class="br-footer__badges-row">
                            <!-- Visa -->
                            <span class="br-footer__badge" title="Visa">
                                <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg" height="20"><rect width="48" height="30" rx="4" fill="#1A1F71"/><path d="M18.3 20.5H15.1L13 10.5h3.2l1.4 7 3.8-7h3.3L18.3 20.5z" fill="#fff"/><path d="M26.8 10.5L25 20.5h-3l1.8-10h3z" fill="#fff"/><path d="M35.9 10.8c-.7-.3-1.7-.5-3-.5-3.3 0-5.6 1.7-5.6 4.2 0 1.8 1.6 2.8 2.9 3.4 1.3.6 1.7 1 1.7 1.5 0 .8-.9 1.2-1.8 1.2-1.2 0-2.2-.3-2.9-.6l-.4 2.4c.8.3 2.1.6 3.5.6 3.5 0 5.8-1.7 5.8-4.3 0-1.3-.8-2.3-2.5-3.1-1-.5-1.7-.9-1.7-1.5 0-.5.6-1 1.8-1 1 0 1.8.2 2.4.5l.3-2.3h-.5z" fill="#fff"/><path d="M43.6 10.5h-2.5c-.8 0-1.4.4-1.6 1l-4.7 9h3.3l.7-1.8h4l.4 1.8h2.9l-2.5-10zm-4.3 6.8l1.7-4.5.9 4.5h-2.6z" fill="#fff"/></svg>
                            </span>
                            <!-- Mastercard -->
                            <span class="br-footer__badge" title="Mastercard">
                                <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg" height="20"><rect width="48" height="30" rx="4" fill="#252525"/><circle cx="18" cy="15" r="8" fill="#EB001B"/><circle cx="30" cy="15" r="8" fill="#F79E1B"/><path d="M24 8.5a8 8 0 010 13A8 8 0 0124 8.5z" fill="#FF5F00"/></svg>
                            </span>
                            <!-- PayPal -->
                            <span class="br-footer__badge" title="PayPal">
                                <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg" height="20"><rect width="48" height="30" rx="4" fill="#fff" stroke="#e2e8f0" stroke-width="1"/><text x="8" y="20" font-family="Arial" font-weight="bold" font-size="10" fill="#003087">Pay</text><text x="22" y="20" font-family="Arial" font-weight="bold" font-size="10" fill="#009cde">Pal</text></svg>
                            </span>
                            <!-- VNPay -->
                            <span class="br-footer__badge" title="VNPay">
                                <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg" height="20"><rect width="48" height="30" rx="4" fill="#0066CC"/><text x="6" y="20" font-family="Arial" font-weight="bold" font-size="10" fill="#fff">VNPay</text></svg>
                            </span>
                        </div>
                    </div>
                </div>

            </div><!-- .br-footer__grid -->
        </div><!-- .br-footer__container -->
    </div><!-- .br-footer__body -->

    <!-- Divider -->
    <div class="br-footer__divider"></div>

    <!-- Bottom Bar -->
    <div class="br-footer__bottom">
        <div class="br-footer__container">
            <div class="br-footer__bottom-inner">
                <p class="br-footer__copyright">
                    &copy; <?php echo esc_html(date('Y')); ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="br-footer__copyright-link"><?php bloginfo('name'); ?></a>.
                    Bảo lưu mọi quyền.
                </p>
                <nav class="br-footer__bottom-nav" aria-label="Footer legal links">
                    <a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat')); ?>">Bảo mật</a>
                    <span aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url(home_url('/dieu-khoan-dich-vu')); ?>">Điều khoản</a>
                    <span aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>">Sitemap</a>
                </nav>
                <p class="br-footer__credit">
                    Thiết kế với <span class="br-footer__heart" aria-hidden="true">♥</span> bởi
                    <a href="#" class="br-footer__credit-link" rel="nofollow">Antigravity</a>
                </p>
            </div>
        </div>
    </div><!-- .br-footer__bottom -->

</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>

</html>