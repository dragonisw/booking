<?php /* Template Name: Tra cứu thông tin */ ?>
<?php get_header(); ?>

<style>
/* ============================================================
   TRANG TRA CỨU ĐẶT PHÒNG — Pure CSS, không phụ thuộc Tailwind
   ============================================================ */

.tracuu-page {
    min-height: 80vh;
    padding: 80px 1.5rem 4rem;
    background: #f8fafc;
    box-sizing: border-box;
}
.admin-bar .tracuu-page       { padding-top: 112px; }
@media screen and (max-width: 782px) {
    .admin-bar .tracuu-page   { padding-top: 126px; }
}

/* ── Hero heading ── */
.tracuu-hero {
    text-align: center;
    margin-bottom: 2.5rem;
}
.tracuu-hero h1 {
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 0.6rem;
    letter-spacing: -0.02em;
}
.tracuu-hero p {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
    line-height: 1.65;
}

/* ── Card wrapper ── */
.tracuu-card-wrap {
    max-width: 560px;
    margin: 0 auto;
    position: relative;
}

/* Decorative blobs */
.tracuu-card-wrap::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, transparent 70%);
    border-radius: 50%;
    top: -60px;
    right: -60px;
    pointer-events: none;
}
.tracuu-card-wrap::after {
    content: '';
    position: absolute;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(16,185,129,0.07) 0%, transparent 70%);
    border-radius: 50%;
    bottom: -40px;
    left: -40px;
    pointer-events: none;
}

/* ── Card ── */
.tracuu-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.09), 0 1px 4px rgba(0,0,0,0.04);
    border: 1px solid #f1f5f9;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    z-index: 0;
}
@media (min-width: 480px) {
    .tracuu-card { padding: 3rem; }
}

/* ── Form ── */
.tracuu-form-group {
    margin-bottom: 1.25rem;
}
.tracuu-form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.45rem;
    letter-spacing: 0.01em;
}
.tracuu-input-wrap {
    position: relative;
}
.tracuu-input-wrap input {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.9rem 3.2rem 0.9rem 1.1rem;
    font-size: 0.95rem;
    color: #1e293b;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    box-sizing: border-box;
    font-family: inherit;
    -webkit-appearance: none;
}
.tracuu-input-wrap input::placeholder { color: #94a3b8; }
.tracuu-input-wrap input:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.tracuu-input-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #cbd5e1;
    width: 20px;
    height: 20px;
    pointer-events: none;
}

/* Submit button */
.tracuu-btn-submit {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    margin-top: 1.5rem;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.22s ease;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    font-family: inherit;
    letter-spacing: 0.01em;
}
.tracuu-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37,99,235,0.45);
}
.tracuu-btn-submit:active { transform: translateY(0); }
.tracuu-btn-submit svg    { width: 18px; height: 18px; flex-shrink: 0; }

/* ── Loading overlay ── */
#tracuu-loading {
    display: none;
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 20;
    border-radius: 24px;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 0.75rem;
}
#tracuu-loading.is-loading {
    display: flex;
}
.tracuu-spinner {
    width: 44px;
    height: 44px;
    border: 4px solid #e0e7ff;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: tracuu-spin 0.75s linear infinite;
    flex-shrink: 0;   /* ← quan trọng: không bị co giãn */
}
@keyframes tracuu-spin {
    to { transform: rotate(360deg); }
}
#tracuu-loading p {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin: 0;
}

/* ── Results area ── */
#tracuu-results {
    display: none;
    margin-top: 2rem;
    padding-top: 1.75rem;
    border-top: 1px solid #f1f5f9;
    animation: tracuu-fadein 0.35s ease-out;
}
#tracuu-results.is-visible {
    display: block;
}
@keyframes tracuu-fadein {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Result header */
.result-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.result-header h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

/* Status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.status-badge.confirmed  { background: #d1fae5; color: #065f46; }
.status-badge.pending    { background: #fff7ed; color: #92400e; }
.status-badge.cancelled  { background: #fee2e2; color: #991b1b; }
.status-badge.checked-in { background: #dbeafe; color: #1e40af; }
.status-badge.default    { background: #f1f5f9; color: #475569; }

/* Info grid */
.result-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.result-grid-item p:first-child {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin: 0 0 0.25rem;
}
.result-grid-item p:last-child {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

/* Info notice */
.result-notice {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    padding: 0.9rem 1rem;
    background: #eff6ff;
    border-radius: 12px;
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.55;
}
.result-notice svg {
    width: 18px;
    height: 18px;
    color: #3b82f6;
    flex-shrink: 0;
    margin-top: 1px;
}

/* Error state */
.result-error {
    text-align: center;
    padding: 2rem 1rem;
}
.result-error-icon {
    width: 56px;
    height: 56px;
    background: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    flex-shrink: 0;
}
.result-error-icon svg {
    width: 26px;
    height: 26px;
    color: #ef4444;
}
.result-error h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.35rem;
}
.result-error p {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}
</style>

<main class="tracuu-page">

    <!-- Hero -->
    <div class="tracuu-hero">
        <h1><?php echo t('Tra cứu Thông tin Đặt phòng', 'Booking Lookup'); ?></h1>
        <p><?php echo t('Vui lòng nhập số điện thoại và mã đặt phòng để kiểm tra trạng thái.', 'Please enter your phone number and booking ID to check the status.'); ?></p>
    </div>

    <!-- Card -->
    <div class="tracuu-card-wrap">
        <div class="tracuu-card">

            <!-- Loading overlay -->
            <div id="tracuu-loading">
                <div class="tracuu-spinner"></div>
                <p><?php echo t('Đang kiểm tra...', 'Checking...'); ?></p>
            </div>

            <!-- Form -->
            <form id="tracuu-form" novalidate>

                <div class="tracuu-form-group">
                    <label for="lookup_phone"><?php echo t('Số điện thoại', 'Phone Number'); ?></label>
                    <div class="tracuu-input-wrap">
                        <input type="tel" id="lookup_phone" name="phone" required
                            placeholder="<?php echo esc_attr(t('Nhập số điện thoại khi đặt phòng', 'Enter phone number used for booking')); ?>"
                            autocomplete="tel">
                        <svg class="tracuu-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                </div>

                <div class="tracuu-form-group">
                    <label for="lookup_id"><?php echo t('Mã đặt phòng', 'Booking ID'); ?></label>
                    <div class="tracuu-input-wrap">
                        <input type="text" id="lookup_id" name="booking_id" required
                            placeholder="<?php echo esc_attr(t('Ví dụ: 1234', 'Example: 1234')); ?>"
                            inputmode="numeric">
                        <svg class="tracuu-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 11h.01M7 15h.01M11 7h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01M19 7h.01M19 11h.01M19 15h.01M7 19h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <button type="submit" class="tracuu-btn-submit">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <?php echo t('Tra cứu ngay', 'Lookup Now'); ?>
                </button>
            </form>

            <!-- Results -->
            <div id="tracuu-results"></div>

        </div><!-- /.tracuu-card -->
    </div><!-- /.tracuu-card-wrap -->

</main>

<script>
(function () {
    'use strict';

    var form      = document.getElementById('tracuu-form');
    var results   = document.getElementById('tracuu-results');
    var loading   = document.getElementById('tracuu-loading');
    var phoneInp  = document.getElementById('lookup_phone');
    var idInp     = document.getElementById('lookup_id');

    /* ── Helpers ── */
    function showLoading() {
        loading.classList.add('is-loading');
        results.classList.remove('is-visible');
    }
    function hideLoading() {
        loading.classList.remove('is-loading');
    }

    function getStatusBadge(status) {
        var map = {
            'confirmed':  ['confirmed',  '<?php echo t('Đã xác nhận', 'Confirmed'); ?>'],
            'publish':    ['confirmed',  '<?php echo t('Đã xác nhận', 'Confirmed'); ?>'],
            'pending':    ['pending',    '<?php echo t('Chờ xử lý', 'Pending'); ?>'],
            'checked_in': ['checked-in', '<?php echo t('Nhận phòng', 'Checked in'); ?>'],
            'cancelled':  ['cancelled',  '<?php echo t('Đã huỷ', 'Cancelled'); ?>'],
        };
        var info = map[status] || ['default', status];
        return '<span class="status-badge ' + info[0] + '">' + info[1] + '</span>';
    }

    function renderSuccess(data) {
        results.innerHTML =
            '<div class="result-header">' +
                '<h3><?php echo t('Kết quả tìm thấy', 'Search Results'); ?></h3>' +
                getStatusBadge(data.status) +
            '</div>' +
            '<div class="result-grid">' +
                '<div class="result-grid-item"><p><?php echo t('Khách hàng', 'Customer'); ?></p><p>' + (data.customer_name || '—') + '</p></div>' +
                '<div class="result-grid-item"><p><?php echo t('Phòng', 'Room'); ?></p><p>' + (data.room_title || '—') + '</p></div>' +
                '<div class="result-grid-item"><p><?php echo t('Ngày nhận phòng', 'Check-in'); ?></p><p>' + (data.check_in || '—') + '</p></div>' +
                '<div class="result-grid-item"><p><?php echo t('Ngày trả phòng', 'Check-out'); ?></p><p>' + (data.check_out || '—') + '</p></div>' +
            '</div>' +
            '<div class="result-notice">' +
                '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>' +
                '<span><?php echo t('Có thắc mắc? Liên hệ hotline <strong>1900 6067</strong> để được hỗ trợ.', 'Any questions? Contact hotline <strong>1900 6067</strong> for support.'); ?></span>' +
            '</div>';
        results.classList.add('is-visible');
    }

    function renderError(message) {
        results.innerHTML =
            '<div class="result-error">' +
                '<div class="result-error-icon">' +
                    '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>' +
                    '</svg>' +
                '</div>' +
                '<h3><?php echo t('Không tìm thấy thông tin', 'Information not found'); ?></h3>' +
                '<p>' + (message || '<?php echo t('Vui lòng kiểm tra lại số điện thoại và mã đặt phòng.', 'Please double check the phone number and booking ID.'); ?>') + '</p>' +
            '</div>';
        results.classList.add('is-visible');
    }

    /* ── AJAX lookup ── */
    function performLookup(phone, bookingId) {
        if (!phone || !bookingId) {
            renderError('<?php echo t('Vui lòng điền đầy đủ số điện thoại và mã đặt phòng.', 'Please fill in both phone number and booking ID.'); ?>');
            results.classList.add('is-visible');
            return;
        }

        showLoading();

        // Dùng Fetch API thay jQuery
        var formData = new FormData();
        formData.append('action', 'lookup_booking');
        formData.append('phone', phone);
        formData.append('booking_id', bookingId);
        formData.append('nonce', (typeof booking_ajax !== 'undefined') ? booking_ajax.nonce : '');

        var ajaxUrl = (typeof booking_ajax !== 'undefined') ? booking_ajax.ajax_url : '/wp-admin/admin-ajax.php';

        fetch(ajaxUrl, { method: 'POST', body: formData })
            .then(function (res) { return res.json(); })
            .then(function (response) {
                hideLoading();
                if (response.success) {
                    renderSuccess(response.data);
                } else {
                    renderError(response.data ? response.data.message : null);
                }
            })
            .catch(function () {
                hideLoading();
                renderError('<?php echo t('Lỗi kết nối. Vui lòng thử lại sau.', 'Connection error. Please try again later.'); ?>');
            });
    }

    /* ── Form submit ── */
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            performLookup(phoneInp.value.trim(), idInp.value.trim());
        });
    }

    /* ── Auto-lookup từ URL params ── */
    var params = new URLSearchParams(window.location.search);
    var urlPhone = params.get('phone');
    var urlId    = params.get('booking_id');
    if (urlPhone && urlId) {
        if (phoneInp) phoneInp.value = urlPhone;
        if (idInp)    idInp.value    = urlId;
        performLookup(urlPhone, urlId);
    }
})();
</script>

<?php get_footer(); ?>
