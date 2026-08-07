/**
 * popup-tuvan.js
 * Xử lý popup tư vấn: mở / đóng / auto-trigger / AJAX submit
 */
(function ($) {
  'use strict';

  /* ── SELECTORS ── */
  const OVERLAY  = '#tuvan-popup-overlay';
  const BOX      = '#tuvan-popup-box';
  const FORM     = '#tuvan-popup-form';
  const CLOSE    = '#tuvan-close-btn';
  const TRIGGER  = '#tuvan-trigger-btn';
  const SUBMIT   = '#tuvan-submit-btn';
  const RESULT   = '#tuvan-result';

  const OPEN_CLASS      = 'is-open';
  const SHOWN_KEY       = 'tuvan_popup_shown';
  const CLOSED_KEY      = 'tuvan_popup_closed';

  /* ── HELPERS ── */
  function openPopup() {
    $(OVERLAY).attr('aria-hidden', 'false').addClass(OPEN_CLASS);
    $('body').css('overflow', 'hidden');
    $(OVERLAY).find('input, textarea').first().trigger('focus');
    sessionStorage.setItem(SHOWN_KEY, '1');
  }

  function closePopup() {
    $(OVERLAY).attr('aria-hidden', 'true').removeClass(OPEN_CLASS);
    $('body').css('overflow', '');
    sessionStorage.setItem(CLOSED_KEY, '1');
  }

  /* ── AUTO-OPEN (sau N giây, nếu user chưa đóng) ── */
  function scheduleAutoOpen() {
    // Lấy delay từ data attribute (inject từ PHP) hoặc mặc định 5s
    var delay = parseInt($(OVERLAY).data('delay') ?? 5, 10);
    if (isNaN(delay) || delay <= 0) return; // delay = 0 → không tự mở

    // Không tự mở nếu user đã đóng trong phiên này
    if (sessionStorage.getItem(CLOSED_KEY) === '1') return;

    setTimeout(function () {
      if (!$(OVERLAY).hasClass(OPEN_CLASS)) {
        openPopup();
      }
    }, delay * 1000);
  }

  /* ── CLOSE ACTIONS ── */
  // Nút X
  $(document).on('click', CLOSE, function () {
    closePopup();
  });

  // Click backdrop
  $(document).on('click', OVERLAY, function (e) {
    if ($(e.target).is(OVERLAY)) {
      closePopup();
    }
  });

  // Phím ESC
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $(OVERLAY).hasClass(OPEN_CLASS)) {
      closePopup();
    }
  });

  /* ── TRIGGER BUTTON ── */
  $(document).on('click', TRIGGER, function () {
    if ($(OVERLAY).hasClass(OPEN_CLASS)) {
      closePopup();
    } else {
      openPopup();
    }
  });

  /* ── FORM VALIDATION (live) ── */
  $(document).on('blur', '.tuvan-input[required]', function () {
    var $el = $(this);
    if ($el.val().trim() === '') {
      $el.css('border-bottom-color', 'rgba(249,168,160,0.8)');
    } else {
      $el.css('border-bottom-color', 'rgba(255,255,255,0.9)');
    }
  });

  /* ── AJAX SUBMIT ── */
  $(document).on('submit', FORM, function (e) {
    e.preventDefault();

    var $form   = $(this);
    var $btn    = $form.find(SUBMIT);
    var $text   = $btn.find('.tuvan-submit-text, .tuvan-submit-icon');
    var $load   = $btn.find('.tuvan-submit-loading');
    var $result = $form.find(RESULT);

    // Lấy giá trị
    var name    = $.trim($('#tuvan-name').val());
    var phone   = $.trim($('#tuvan-phone').val());
    var email   = $.trim($('#tuvan-email').val());
    var message = $.trim($('#tuvan-message').val());

    // Validate cơ bản client-side
    if (!name) {
      showResult('error', 'Vui lòng nhập họ tên.');
      $('#tuvan-name').focus();
      return;
    }
    if (!phone) {
      showResult('error', 'Vui lòng nhập số điện thoại.');
      $('#tuvan-phone').focus();
      return;
    }

    // Loading state
    $text.hide();
    $load.show();
    $btn.prop('disabled', true);
    $result.hide().removeClass('is-success is-error');

    $.ajax({
      url:  tuvan_ajax.ajax_url,
      type: 'POST',
      data: {
        action:        'tuvan_submit',
        nonce:         tuvan_ajax.nonce,
        tuvan_name:    name,
        tuvan_phone:   phone,
        tuvan_email:   email,
        tuvan_message: message,
      },
      success: function (res) {
        if (res.success) {
          showResult('success', res.data.message || 'Gửi thành công!');
          $form[0].reset();
          // Đóng popup sau 3.5s
          setTimeout(function () {
            closePopup();
          }, 3500);
        } else {
          showResult('error', res.data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        }
      },
      error: function () {
        showResult('error', 'Không thể kết nối. Vui lòng thử lại sau.');
      },
      complete: function () {
        $text.show();
        $load.hide();
        $btn.prop('disabled', false);
      },
    });

    function showResult(type, msg) {
      $result
        .removeClass('is-success is-error')
        .addClass('is-' + type)
        .text(msg)
        .removeAttr('hidden')
        .show();
    }
  });

  /* ── INIT ── */
  $(document).ready(function () {
    scheduleAutoOpen();
  });

})(jQuery);
