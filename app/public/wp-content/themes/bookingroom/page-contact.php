<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

<style>
/* ============================================================
   TRANG LIÊN HỆ — Pure CSS, không phụ thuộc Tailwind
   ============================================================ */

/* Offset cho sticky/fixed header (~80px) */
.contact-page {
    padding-top: 80px;
    background: #f8fafc;
    min-height: 100vh;
}
.admin-bar .contact-page {
    padding-top: 112px; /* 80 + 32 (admin bar) */
}
@media screen and (max-width: 782px) {
    .admin-bar .contact-page {
        padding-top: 126px; /* 80 + 46 */
    }
}

/* ── Hero ── */
.contact-hero {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 4rem 1.5rem;
    text-align: center;
}
.contact-hero h1 {
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 0.75rem;
    letter-spacing: -0.02em;
}
.contact-hero p {
    color: #64748b;
    font-size: 1.05rem;
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.7;
}

/* ── Main section wrapper ── */
.contact-section {
    padding: 4rem 1.5rem;
    max-width: 1100px;
    margin: 0 auto;
    box-sizing: border-box;
}

/* ── Card ── */
.contact-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
@media (min-width: 768px) {
    .contact-card {
        flex-direction: row;
    }
}

/* ── Info panel (trái) ── */
.contact-info {
    background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    padding: 3rem 2.5rem;
    flex-shrink: 0;
    width: 100%;
    box-sizing: border-box;
}
@media (min-width: 768px) {
    .contact-info {
        width: 38%;
    }
}
.contact-info h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0 0 2rem;
}
.contact-info-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}
.contact-info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}
.contact-info-icon {
    width: 42px;
    height: 42px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,0.2);
}
.contact-info-icon svg {
    width: 20px;
    height: 20px;
    color: #fff;
}
.contact-info-text h4 {
    font-size: 0.875rem;
    font-weight: 700;
    margin: 0 0 0.25rem;
    color: #fff;
}
.contact-info-text p {
    font-size: 0.875rem;
    color: rgba(255,255,255,0.8);
    margin: 0;
    line-height: 1.6;
}
.contact-info-text a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.contact-info-text a:hover {
    color: #fff;
    text-decoration: underline;
}

/* Social links */
.contact-social {
    margin-top: 2.5rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.15);
}
.contact-social h4 {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255,255,255,0.7);
    margin: 0 0 0.85rem;
}
.social-links {
    display: flex;
    gap: 0.65rem;
}
.social-link {
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
    transition: all 0.2s;
}
.social-link:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
}
.social-link svg {
    width: 18px;
    height: 18px;
}

/* ── Form panel (phải) ── */
.contact-form-wrap {
    flex: 1;
    padding: 3rem 2.5rem;
    box-sizing: border-box;
}
.contact-form-wrap h2 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem;
}
.contact-form-wrap > p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0 0 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
@media (min-width: 560px) {
    .form-row { grid-template-columns: 1fr 1fr; }
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1.25rem;
}
.form-group label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #374151;
    letter-spacing: 0.01em;
}
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1e293b;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    box-sizing: border-box;
    font-family: inherit;
    -webkit-appearance: none;
}
.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #94a3b8;
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.form-group textarea {
    resize: vertical;
    min-height: 130px;
}

/* Submit button */
.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 2rem;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(37,99,235,0.35);
    font-family: inherit;
    letter-spacing: 0.01em;
}
.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37,99,235,0.45);
}
.btn-submit:active {
    transform: translateY(0);
}
.btn-submit svg {
    width: 18px;
    height: 18px;
}

/* ── Form success/error notices ── */
.form-notice {
    padding: 0.85rem 1.1rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.form-notice.success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}
.form-notice.error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* ── Map section ── */
.contact-map {
    background: #e2e8f0;
    position: relative;
    overflow: hidden;
    min-height: 380px;
}
.contact-map iframe {
    width: 100%;
    height: 380px;
    border: none;
    display: block;
    filter: grayscale(20%);
    transition: filter 0.4s;
}
.contact-map:hover iframe {
    filter: grayscale(0%);
}
.contact-map-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: #94a3b8;
}
.contact-map-placeholder svg {
    width: 40px;
    height: 40px;
    opacity: 0.5;
}
.contact-map-placeholder span {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* ── Quick info cards ── */
.contact-cards-row {
    padding: 3.5rem 1.5rem;
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
}
@media (min-width: 600px) {
    .contact-cards-row { grid-template-columns: 1fr 1fr; }
}
@media (min-width: 900px) {
    .contact-cards-row { grid-template-columns: repeat(3, 1fr); }
}
.contact-quick-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.75rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
    transition: transform 0.2s, box-shadow 0.2s;
}
.contact-quick-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.contact-quick-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.contact-quick-card-icon.blue  { background: #eff6ff; color: #2563eb; }
.contact-quick-card-icon.green { background: #f0fdf4; color: #16a34a; }
.contact-quick-card-icon.amber { background: #fffbeb; color: #d97706; }
.contact-quick-card-icon svg { width: 24px; height: 24px; }
.contact-quick-card h3 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.3rem;
}
.contact-quick-card p {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
    line-height: 1.55;
}
</style>

<main class="contact-page">

    <!-- ── Hero ── -->
    <section class="contact-hero">
        <h1><?php echo t('Liên hệ với Chúng tôi', 'Contact Us'); ?></h1>
        <p><?php echo t('Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để lại lời nhắn hoặc liên hệ trực tiếp qua hotline.', 'We are always ready to listen and support you. Please leave a message or contact us directly via hotline.'); ?></p>
    </section>

    <!-- ── Quick Info Cards ── -->
    <div class="contact-cards-row">
        <div class="contact-quick-card">
            <div class="contact-quick-card-icon blue">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3><?php echo t('Giờ làm việc', 'Working Hours'); ?></h3>
                <p><?php echo t('Thứ 2 – Thứ 6: 8:00 – 18:00<br>Thứ 7 – CN: 9:00 – 17:00', 'Mon – Fri: 8:00 – 18:00<br>Sat – Sun: 9:00 – 17:00'); ?></p>
            </div>
        </div>
        <div class="contact-quick-card">
            <div class="contact-quick-card-icon green">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div>
                <h3><?php echo t('Hotline hỗ trợ', 'Support Hotline'); ?></h3>
                <p><a href="tel:01234567890" style="color:#16a34a;font-weight:700;">0123 456 789</a><br><?php echo t('Hỗ trợ 24/7 mọi ngày', '24/7 Support every day'); ?></p>
            </div>
        </div>
        <div class="contact-quick-card">
            <div class="contact-quick-card-icon amber">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h3><?php echo t('Phản hồi email', 'Email Feedback'); ?></h3>
                <p><?php echo t('Trong vòng 2–4 giờ làm việc', 'Within 2-4 working hours'); ?><br>support@bookingroom.com</p>
            </div>
        </div>
    </div>

    <!-- ── Contact Card (Info + Form) ── -->
    <section class="contact-section" style="padding-top:0;">
        <div class="contact-card">

            <!-- Info Panel -->
            <div class="contact-info">
                <h2><?php echo t('Thông tin Liên hệ', 'Contact Information'); ?></h2>
                <ul class="contact-info-list">
                    <li class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo t('Địa chỉ', 'Address'); ?></h4>
                            <p><?php echo t('123 Đường Trần Hưng Đạo<br>Quận 1, TP. Hồ Chí Minh', '123 Tran Hung Dao Street<br>District 1, Ho Chi Minh City'); ?></p>
                        </div>
                    </li>
                    <li class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo t('Điện thoại', 'Phone'); ?></h4>
                            <p><a href="tel:01234567890">0123 456 789</a><br><a href="tel:19001234">1900 1234</a></p>
                        </div>
                    </li>
                    <li class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo t('Email', 'Email'); ?></h4>
                            <p><a href="mailto:support@bookingroom.com">support@bookingroom.com</a><br><a href="mailto:info@bookingroom.com">info@bookingroom.com</a></p>
                        </div>
                    </li>
                </ul>

                <div class="contact-social">
                    <h4><?php echo t('Theo dõi chúng tôi', 'Follow Us'); ?></h4>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="YouTube">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon fill="#1d4ed8" points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Zalo">
                            <svg fill="currentColor" viewBox="0 0 24 24"><text x="3" y="17" font-size="10" font-weight="bold">Zalo</text></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Panel -->
            <div class="contact-form-wrap">
                <h2><?php echo t('Gửi tin nhắn cho chúng tôi', 'Send us a message'); ?></h2>
                <p><?php echo t('Điền thông tin bên dưới, chúng tôi sẽ phản hồi sớm nhất có thể.', 'Fill in the information below, we will respond as soon as possible.'); ?></p>

                <?php
                // Xử lý form submit
                $notice_html = '';
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_nonce'])) {
                    if (wp_verify_nonce($_POST['contact_nonce'], 'contact_form_submit')) {
                        $name    = sanitize_text_field($_POST['contact_name'] ?? '');
                        $email   = sanitize_email($_POST['contact_email'] ?? '');
                        $subject = sanitize_text_field($_POST['contact_subject'] ?? '');
                        $message = sanitize_textarea_field($_POST['contact_message'] ?? '');

                        if ($name && $email && $message) {
                            $to      = get_option('admin_email');
                            $subj    = '[' . t('Liên hệ', 'Contact') . '] ' . ($subject ?: t('Tin nhắn từ website', 'Message from website'));
                            $body    = t('Họ tên: ', 'Name: ') . "$name\n" . t('Email: ', 'Email: ') . "$email\n" . t('Chủ đề: ', 'Subject: ') . "$subject\n\n" . t('Nội dung:', 'Message:') . "\n$message";
                            $headers = ['Content-Type: text/plain; charset=UTF-8', "Reply-To: $name <$email>"];
                            wp_mail($to, $subj, $body, $headers);
                            $notice_html = '<div class="form-notice success">✅ ' . t('Gửi thành công! Chúng tôi sẽ phản hồi trong 2–4 giờ.', 'Successfully sent! We will respond within 2-4 hours.') . '</div>';
                        } else {
                            $notice_html = '<div class="form-notice error">⚠ ' . t('Vui lòng điền đầy đủ: Họ tên, Email và Lời nhắn.', 'Please fill in completely: Name, Email and Message.') . '</div>';
                        }
                    }
                }
                echo $notice_html;
                ?>

                <form method="POST" action="" novalidate>
                    <?php wp_nonce_field('contact_form_submit', 'contact_nonce'); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_name"><?php echo t('Họ và tên', 'Full Name'); ?> <span style="color:#ef4444;">*</span></label>
                            <input type="text" id="contact_name" name="contact_name"
                                placeholder="<?php echo esc_attr(t('Nguyễn Văn A', 'John Doe')); ?>"
                                value="<?php echo esc_attr($_POST['contact_name'] ?? ''); ?>"
                                required autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="contact_email"><?php echo t('Địa chỉ Email', 'Email Address'); ?> <span style="color:#ef4444;">*</span></label>
                            <input type="email" id="contact_email" name="contact_email"
                                placeholder="example@gmail.com"
                                value="<?php echo esc_attr($_POST['contact_email'] ?? ''); ?>"
                                required autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone"><?php echo t('Số điện thoại', 'Phone Number'); ?></label>
                        <input type="tel" id="contact_phone" name="contact_phone"
                            placeholder="0123 456 789"
                            value="<?php echo esc_attr($_POST['contact_phone'] ?? ''); ?>"
                            autocomplete="tel">
                    </div>

                    <div class="form-group">
                        <label for="contact_subject"><?php echo t('Chủ đề', 'Subject'); ?></label>
                        <input type="text" id="contact_subject" name="contact_subject"
                            placeholder="<?php echo esc_attr(t('Tôi cần hỗ trợ về đặt phòng...', 'I need support with booking...')); ?>"
                            value="<?php echo esc_attr($_POST['contact_subject'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="contact_message"><?php echo t('Lời nhắn', 'Message'); ?> <span style="color:#ef4444;">*</span></label>
                        <textarea id="contact_message" name="contact_message"
                            placeholder="<?php echo esc_attr(t('Viết nội dung tin nhắn của bạn ở đây...', 'Write your message here...')); ?>"
                            required><?php echo esc_textarea($_POST['contact_message'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <?php echo t('Gửi tin nhắn ngay', 'Send Message Now'); ?>
                    </button>
                </form>
            </div>

        </div>
    </section>

    <!-- ── Map ── -->
    <div class="contact-map">
        <?php
        $maps_api = get_theme_mod('google_maps_api_key', '');
        if ($maps_api) :
        ?>
            <iframe
                src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr($maps_api); ?>&q=123+Tran+Hung+Dao,+Ho+Chi+Minh+City"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        <?php else : ?>
            <div class="contact-map-placeholder">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span><?php echo t('Bản đồ Google Maps', 'Google Maps'); ?></span>
                <small style="font-size:0.75rem;color:#cbd5e1;"><?php echo t('Cài đặt Google Maps API Key trong giao diện → Tuỳ chỉnh', 'Setup Google Maps API Key in Appearance → Customize'); ?></small>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── Page Editor Content (nếu có) ── -->
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php if (!empty(get_the_content())) : ?>
            <section style="padding:4rem 1.5rem;background:#fff;border-top:1px solid #f1f5f9;">
                <div style="max-width:900px;margin:0 auto;">
                    <?php the_content(); ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>
