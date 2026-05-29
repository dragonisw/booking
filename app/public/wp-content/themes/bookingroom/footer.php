</div><!-- #content -->

<footer id="colophon" class="br-footer" role="contentinfo">

    <!-- Top Gradient Bar -->
    <div class="br-footer__topbar"></div>

    <!-- Main Body -->
    <div class="br-footer__body">
        <div class="br-footer__container">
            <div class="br-footer__grid">

                <!-- Col 1: Branding + Contact + Social -->
                <div class="br-footer__col br-footer__col--brand">

                    <!-- Logo -->
                    <div class="br-footer__logo">
                        <a href="<?php echo esc_url( home_url('/') ); ?>" aria-label="<?php bloginfo('name'); ?> – Trang chủ">
                            <?php if ( has_custom_logo() ) : ?>
                                <div class="br-footer__logo-img">
                                    <?php the_custom_logo(); ?>
                                </div>
                            <?php else : ?>
                                <span class="br-footer__logo-text"><?php bloginfo('name'); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- Tagline -->
                    <p class="br-footer__tagline">
                        <?php
                        $desc = get_bloginfo('description');
                        echo esc_html( $desc ?: 'Mang đến trải nghiệm đặt phòng khách sạn dễ dàng, nhanh chóng và tin cậy nhất cho kỳ nghỉ của bạn.' );
                        ?>
                    </p>

                    <!-- Contact -->
                    <ul class="br-footer__contact" aria-label="Thông tin liên hệ">
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                            </span>
                            <span><?php echo esc_html( get_theme_mod('footer_address', '123 Đường Lê Lợi, Q.1, TP.HCM') ); ?></span>
                        </li>
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/></svg>
                            </span>
                            <a href="tel:<?php echo esc_attr( preg_replace('/\D/', '', get_theme_mod('footer_phone', '+84901234567')) ); ?>" class="br-footer__contact-link">
                                <?php echo esc_html( get_theme_mod('footer_phone', '+84 90 123 4567') ); ?>
                            </a>
                        </li>
                        <li class="br-footer__contact-item">
                            <span class="br-footer__contact-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>
                            </span>
                            <a href="mailto:<?php echo esc_attr( get_theme_mod('footer_email', 'info@travelrooms.vn') ); ?>" class="br-footer__contact-link">
                                <?php echo esc_html( get_theme_mod('footer_email', 'info@travelrooms.vn') ); ?>
                            </a>
                        </li>
                    </ul>

                    <!-- Social Icons -->
                    <div class="br-footer__social" aria-label="Mạng xã hội">
                        <?php
                        $socials = [
                            'facebook'  => [
                                'label' => 'Facebook',
                                'url'   => get_theme_mod('social_facebook', '#'),
                                'svg'   => '<path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>',
                            ],
                            'instagram' => [
                                'label' => 'Instagram',
                                'url'   => get_theme_mod('social_instagram', '#'),
                                'svg'   => '<path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>',
                            ],
                            'youtube'   => [
                                'label' => 'YouTube',
                                'url'   => get_theme_mod('social_youtube', '#'),
                                'svg'   => '<path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"/>',
                            ],
                        ];
                        foreach ( $socials as $key => $s ) : ?>
                            <a href="<?php echo esc_url( $s['url'] ); ?>"
                               class="br-footer__social-link br-footer__social-link--<?php echo esc_attr($key); ?>"
                               aria-label="<?php echo esc_attr( $s['label'] ); ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="17" height="17" aria-hidden="true">
                                    <?php echo $s['svg']; ?>
                                </svg>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div><!-- .br-footer__col--brand -->

                <!-- Col 2: Khám phá -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-bar" aria-hidden="true"></span>
                        Khám phá
                    </h3>
                    <?php if ( has_nav_menu('footer-explore') ) :
                        wp_nav_menu([
                            'theme_location' => 'footer-explore',
                            'menu_class'     => 'br-footer__nav',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                    else : ?>
                        <ul class="br-footer__nav">
                            <li><a href="<?php echo esc_url( home_url('/destinations') ); ?>">Địa điểm</a></li>
                            <li><a href="<?php echo esc_url( home_url('/services') ); ?>">Dịch vụ</a></li>
                            <li><a href="<?php echo esc_url( home_url('/uu-dai') ); ?>">Ưu đãi</a></li>
                            <li><a href="<?php echo esc_url( home_url('/blog') ); ?>">Blog</a></li>
                            <li><a href="<?php echo esc_url( home_url('/rooms') ); ?>">Phòng nghỉ</a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Col 3: Hỗ trợ -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-bar" aria-hidden="true"></span>
                        Hỗ trợ
                    </h3>
                    <?php if ( has_nav_menu('footer-support') ) :
                        wp_nav_menu([
                            'theme_location' => 'footer-support',
                            'menu_class'     => 'br-footer__nav',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                    else : ?>
                        <ul class="br-footer__nav">
                            <li><a href="<?php echo esc_url( home_url('/contact') ); ?>">Liên hệ</a></li>
                            <li><a href="<?php echo esc_url( home_url('/faq') ); ?>">Câu hỏi thường gặp</a></li>
                            <li><a href="<?php echo esc_url( home_url('/chinh-sach-bao-mat') ); ?>">Chính sách bảo mật</a></li>
                            <li><a href="<?php echo esc_url( home_url('/dieu-khoan-dich-vu') ); ?>">Điều khoản dịch vụ</a></li>
                            <li><a href="<?php echo esc_url( home_url('/tra-cuu-phong') ); ?>">Tra cứu đặt phòng</a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Col 4: Điểm đến -->
                <?php
                // Lấy trang dùng template page-destinations.php một lần, dùng chung
                $footer_dest_pages = get_pages([
                    'meta_key'   => '_wp_page_template',
                    'meta_value' => 'page-destinations.php',
                    'number'     => 1,
                ]);
                $footer_dest_url = $footer_dest_pages
                    ? get_permalink($footer_dest_pages[0]->ID)
                    : home_url('/diem-den');
                ?>
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-bar" aria-hidden="true"></span>
                        <a href="<?php echo esc_url($footer_dest_url); ?>" style="color:inherit;text-decoration:none;">Điểm đến</a>
                    </h3>
                    <?php if ( has_nav_menu('footer-destination') ) :
                        wp_nav_menu([
                            'theme_location' => 'footer-destination',
                            'menu_class'     => 'br-footer__nav',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                    else :
                        // Lấy các điểm đến từ CPT 'destinations' (nếu có)
                        $footer_dest_posts = get_posts([
                            'post_type'      => 'destinations',
                            'posts_per_page' => 6,
                            'post_status'    => 'publish',
                        ]);
                        ?>
                        <ul class="br-footer__nav">
                            <li>
                                <a href="<?php echo esc_url($footer_dest_url); ?>">Tất cả điểm đến</a>
                            </li>
                            <?php if ( $footer_dest_posts ) :
                                foreach ( $footer_dest_posts as $dest ) : ?>
                                    <li>
                                        <a href="<?php echo esc_url( get_permalink($dest->ID) ); ?>">
                                            <?php echo esc_html( $dest->post_title ); ?>
                                        </a>
                                    </li>
                                <?php endforeach;
                            else :
                                // Fallback tĩnh — tất cả trỏ về đúng trang page-destination.php
                                $static_dests = ['Đà Nẵng', 'Phú Quốc', 'Hội An', 'Hà Nội', 'Nha Trang', 'Sapa'];
                                foreach ( $static_dests as $city ) : ?>
                                    <li><a href="<?php echo esc_url( $footer_dest_url ); ?>"><?php echo esc_html($city); ?></a></li>
                                <?php endforeach;
                            endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Col 5: Newsletter + Badges -->
                <div class="br-footer__col">
                    <h3 class="br-footer__heading">
                        <span class="br-footer__heading-bar" aria-hidden="true"></span>
                        Nhận ưu đãi độc quyền
                    </h3>
                    <p class="br-footer__nl-desc">Đăng ký nhận thông tin về các ưu đãi và phòng nghỉ mới nhất.</p>

                    <form class="br-footer__nl-form" action="#" method="post" novalidate>
                        <?php wp_nonce_field('br_newsletter_nonce', 'br_newsletter_nonce'); ?>
                        <div class="br-footer__nl-wrap">
                            <label for="br-nl-email" class="br-footer__sr-only">Email của bạn</label>
                            <input
                                type="email"
                                id="br-nl-email"
                                name="newsletter_email"
                                placeholder="Email của bạn..."
                                class="br-footer__nl-input"
                                required
                                autocomplete="email"
                            >
                            <button type="submit" class="br-footer__nl-btn" aria-label="Đăng ký nhận tin">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
                                    <path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Payment Trust Badges -->
                    <div class="br-footer__badges">
                        <p class="br-footer__badges-label">Thanh toán an toàn</p>
                        <div class="br-footer__badges-row">
                            <!-- Visa -->
                            <span class="br-footer__badge" title="Visa" aria-label="Visa">
                                <svg viewBox="0 0 52 32" xmlns="http://www.w3.org/2000/svg" height="20" width="auto"><rect width="52" height="32" rx="5" fill="#1A1F71"/><text x="7" y="22" font-family="Arial,sans-serif" font-weight="900" font-size="15" fill="#fff" letter-spacing="1">VISA</text></svg>
                            </span>
                            <!-- Mastercard -->
                            <span class="br-footer__badge" title="Mastercard" aria-label="Mastercard">
                                <svg viewBox="0 0 52 32" xmlns="http://www.w3.org/2000/svg" height="20" width="auto"><rect width="52" height="32" rx="5" fill="#1a1a1a"/><circle cx="20" cy="16" r="9" fill="#EB001B" opacity=".95"/><circle cx="32" cy="16" r="9" fill="#F79E1B" opacity=".95"/><path d="M26 9.1a9 9 0 010 13.8A9 9 0 0126 9.1z" fill="#FF5F00"/></svg>
                            </span>
                            <!-- PayPal -->
                            <span class="br-footer__badge" title="PayPal" aria-label="PayPal">
                                <svg viewBox="0 0 52 32" xmlns="http://www.w3.org/2000/svg" height="20" width="auto"><rect width="52" height="32" rx="5" fill="#fff" stroke="#dde3ea" stroke-width="1.2"/><text x="8" y="21" font-family="Arial,sans-serif" font-weight="900" font-size="11" fill="#003087">Pay</text><text x="26" y="21" font-family="Arial,sans-serif" font-weight="900" font-size="11" fill="#009cde">Pal</text></svg>
                            </span>
                            <!-- VNPay -->
                            <span class="br-footer__badge" title="VNPay" aria-label="VNPay">
                                <svg viewBox="0 0 52 32" xmlns="http://www.w3.org/2000/svg" height="20" width="auto"><rect width="52" height="32" rx="5" fill="#005BAA"/><text x="6" y="22" font-family="Arial,sans-serif" font-weight="900" font-size="11" fill="#fff">VNPay</text></svg>
                            </span>
                        </div>
                    </div>
                </div>

            </div><!-- .br-footer__grid -->
        </div><!-- .br-footer__container -->
    </div><!-- .br-footer__body -->

    <!-- Gradient Divider -->
    <div class="br-footer__divider" role="separator" aria-hidden="true"></div>

    <!-- Bottom Bar -->
    <div class="br-footer__bottom">
        <div class="br-footer__container">
            <div class="br-footer__bottom-inner">

                <p class="br-footer__copyright">
                    &copy; <?php echo esc_html( date('Y') ); ?>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="br-footer__copyright-link"><?php bloginfo('name'); ?></a>.
                    Bảo lưu mọi quyền.
                </p>

                <nav class="br-footer__legal-nav" aria-label="Liên kết pháp lý">
                    <a href="<?php echo esc_url( home_url('/chinh-sach-bao-mat') ); ?>">Bảo mật</a>
                    <span aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url( home_url('/dieu-khoan-dich-vu') ); ?>">Điều khoản</a>
                    <span aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url( home_url('/sitemap.xml') ); ?>">Sitemap</a>
                </nav>

                <p class="br-footer__credit">
                    Thiết kế với <span class="br-footer__heart" aria-hidden="true">&#10084;</span> bởi
                    <a href="#" class="br-footer__credit-link" rel="nofollow">Antigravity</a>
                </p>

            </div>
        </div>
    </div><!-- .br-footer__bottom -->

</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
