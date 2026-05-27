jQuery(document).ready(function($) {
    var frame;
    $('#upload-banner-btn').on('click', function(e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Chọn ảnh banner',
            button: {
                text: 'Dùng ảnh này'
            },
            multiple: true
        });

        frame.on('select', function() {
            var selections = frame.state().get('selection');
            var ids = $('#home_banner_ids').val() ? $('#home_banner_ids').val().split(',') : [];
            
            selections.map(function(attachment) {
                attachment = attachment.toJSON();
                if (ids.indexOf(attachment.id.toString()) === -1) {
                    ids.push(attachment.id);
                    $('#banner-images-container').append(
                        '<div class="banner-image-preview" data-id="' + attachment.id + '" style="position: relative; border: 1px solid #ccc; padding: 2px;">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" style="width: 80px; height: 80px; object-fit: cover; display: block;">' +
                        '<a href="#" class="remove-banner-img" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; text-align: center; line-height: 16px; text-decoration: none; font-size: 12px;">×</a>' +
                        '</div>'
                    );
                }
            });
            
            $('#home_banner_ids').val(ids.join(','));
        });

        frame.open();
    });

    $(document).on('click', '.remove-banner-img', function(e) {
        e.preventDefault();
        var id = $(this).parent().data('id');
        var ids = $('#home_banner_ids').val().split(',');
        var index = ids.indexOf(id.toString());
        
        if (index > -1) {
            ids.splice(index, 1);
            $('#home_banner_ids').val(ids.join(','));
            $(this).parent().remove();
        }
    });

    // Destination Picker
    var dest_frame;
    $('#upload-dest-btn').on('click', function(e) {
        e.preventDefault();
        if (dest_frame) {
            dest_frame.open();
            return;
        }
        dest_frame = wp.media({
            title: 'Chọn ảnh Điểm đến',
            button: { text: 'Dùng ảnh này' },
            multiple: true
        });
        dest_frame.on('select', function() {
            var selections = dest_frame.state().get('selection');
            var ids = $('#home_destination_ids').val() ? $('#home_destination_ids').val().split(',') : [];
            selections.map(function(attachment) {
                attachment = attachment.toJSON();
                if (ids.indexOf(attachment.id.toString()) === -1) {
                    ids.push(attachment.id);
                    $('#destination-images-container').append(
                        '<div class="destination-image-preview" data-id="' + attachment.id + '" style="position: relative; border: 1px solid #ccc; padding: 5px; width: 100px; text-align: center; background: #f9f9f9;">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" style="width: 80px; height: 80px; object-fit: cover; display: block; margin: 0 auto 5px;">' +
                        '<span style="font-size: 10px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + attachment.title + '</span>' +
                        '<a href="#" class="remove-dest-img" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; text-align: center; line-height: 16px; text-decoration: none; font-size: 12px;">×</a>' +
                        '</div>'
                    );
                }
            });
            $('#home_destination_ids').val(ids.join(','));
        });
        dest_frame.open();
    });

    $(document).on('click', '.remove-dest-img', function(e) {
        e.preventDefault();
        var id = $(this).parent().data('id');
        var ids = $('#home_destination_ids').val().split(',');
        var index = ids.indexOf(id.toString());
        if (index > -1) {
            ids.splice(index, 1);
            $('#home_destination_ids').val(ids.join(','));
            $(this).parent().remove();
        }
    });

    // ── About Section – Ảnh chính ─────────────────────────────────────────
    function makeSingleImagePicker(btnId, removeId, inputId, previewId) {
        var f;
        $(document).on('click', '#' + btnId, function(e) {
            e.preventDefault();
            if (f) { f.open(); return; }
            f = wp.media({
                title: 'Chọn hình ảnh',
                button: { text: 'Dùng ảnh này' },
                multiple: false
            });
            f.on('select', function() {
                var att = f.state().get('selection').first().toJSON();
                var thumbUrl = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                $('#' + inputId).val(att.id);
                // Thay preview
                var $prev = $('#' + previewId);
                if ($prev.is('img')) {
                    $prev.attr('src', thumbUrl);
                } else {
                    $prev.replaceWith('<img src="' + thumbUrl + '" id="' + previewId + '" style="width:90px;height:70px;object-fit:cover;border-radius:6px;border:2px solid #e2e8f0;">');
                }
                // Hiện nút xóa nếu chưa có
                if (!$('#' + removeId).length) {
                    $('#' + btnId).after('<br><button type="button" class="button about-img-remove" id="' + removeId + '" data-input="' + inputId + '" data-preview="' + previewId + '" style="margin-top:6px;color:red;">✕ Xóa</button>');
                }
            });
            f.open();
        });
    }

    makeSingleImagePicker('about-img-btn',  'about-img-remove',  'home_about_img',  'about-img-preview');
    makeSingleImagePicker('about-img2-btn', 'about-img2-remove', 'home_about_img2', 'about-img2-preview');

    $(document).on('click', '.about-img-remove', function(e) {
        e.preventDefault();
        var inputId   = $(this).data('input');
        var previewId = $(this).data('preview');
        $('#' + inputId).val('');
        var $prev = $('#' + previewId);
        $prev.replaceWith('<div class="no-img" id="' + previewId + '" style="width:90px;height:70px;background:#f1f5f9;border:2px dashed #cbd5e1;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;text-align:center;">Chưa có ảnh</div>');
        $(this).remove();
    });
});

