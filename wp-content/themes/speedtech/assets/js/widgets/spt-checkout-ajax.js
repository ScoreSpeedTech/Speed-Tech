// FILE: /assets/js/spt-checkout-ajax.js
jQuery(function($) {
    'use strict';

    var form = $('#spt-checkout-form');
    var messagesContainer = $('#spt-checkout-messages');
    var placeOrderButton = $('#spt-place-order-button');

    form.on('submit', function(e) {
        e.preventDefault();

        // Xóa các cảnh báo cũ trước khi submit
        $('.spt-field-error').removeClass('spt-field-error');
        $('.spt-error-message').remove();
        messagesContainer.slideUp().empty();
        
        placeOrderButton.prop('disabled', true).text('Đang xử lý...');

        var formData = new FormData(this);
        formData.append('action', 'spt_process_checkout');
        formData.append('nonce', spt_checkout_params.nonce);

        $.ajax({
            url: spt_checkout_params.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    messagesContainer.removeClass('woocommerce-error').addClass('woocommerce-message').html('Thành công! Đang chuyển hướng...').slideDown();
                    // Chuyển hướng đến trang cảm ơn hoặc cổng thanh toán
                    window.location.href = response.data.redirect_url;
                } else {
                    // Hiển thị lỗi tổng hợp trong hộp thông báo
                    if (response.data.messages && response.data.messages.length > 0) {
                        var errorHtml = '<ul>';
                        $.each(response.data.messages, function(i, message) {
                            errorHtml += '<li>' + message + '</li>';
                        });
                        errorHtml += '</ul>';
                        messagesContainer.removeClass('woocommerce-message').addClass('woocommerce-error').html(errorHtml).slideDown();
                    }

                    // Đánh dấu các trường bị lỗi và hiển thị thông báo riêng
                    if (response.data.error_fields) {
                        $.each(response.data.error_fields, function(field_id, message) {
                            var fieldWrapper = $('#' + field_id + '_field');
                            fieldWrapper.addClass('spt-field-error');
                            // Thêm thông báo lỗi ngay dưới trường đó
                            fieldWrapper.find('.woocommerce-input-wrapper, input[type="file"]').last().after('<span class="spt-error-message">' + message + '</span>');
                        });
                        // Cuộn lên lỗi đầu tiên
                         $('html, body').animate({
                            scrollTop: ($('.spt-field-error').first().offset().top - 100)
                        }, 500);
                    }
                    
                    placeOrderButton.prop('disabled', false).text('Đặt hàng');
                }
            },
            error: function() {
                messagesContainer.removeClass('woocommerce-message').addClass('woocommerce-error').html('Đã xảy ra lỗi không xác định. Vui lòng thử lại.').slideDown();
                placeOrderButton.prop('disabled', false).text('Đặt hàng');
            }
        });
    });
});