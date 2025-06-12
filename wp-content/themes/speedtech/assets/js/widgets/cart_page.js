(function($) {
    'use strict';

    var SptCartPage = {
        init: function() {
            $(document).on('click', '.spt-remove-item', this.handleRemoveItem);
        },

        handleRemoveItem: function(e) {
            e.preventDefault();

            var $thisButton = $(this);
            var $cartItemRow = $thisButton.closest('tr.spt-cart-item');
            var cart_item_key = $thisButton.data('cart_item_key');

            // Add a processing class to give visual feedback
            $cartItemRow.addClass('processing');

            $.ajax({
                type: 'POST',
                url: wc_cart_fragments_params.ajax_url,
                data: {
                    action: 'woocommerce_remove_from_cart',
                    cart_item_key: cart_item_key,
                    _wpnonce: wc_cart_fragments_params.remove_from_cart_nonce // Use nonce for security
                },
                success: function(response) {
                    if (!response || !response.fragments) {
                        // If something goes wrong, maybe reload the page
                        window.location.reload();
                        return;
                    }

                    // Trigger the fragments refresh to update mini-cart etc.
                    $(document.body).trigger('wc_fragment_refresh');

                    // Trigger a custom event for other elements to listen to
                    $(document.body).trigger('removed_from_cart', [response.fragments, response.cart_hash, $thisButton]);

                    // Fade out and remove the row from the table
                    $cartItemRow.fadeOut(300, function() {
                        $(this).remove();
                        // Check if the cart is now empty
                        if ($('.spt-cart-item').length === 0) {
                            // If cart is empty, reload to show the "empty cart" message
                            window.location.reload();
                        }
                    });
                },
                error: function() {
                    // On error, remove processing class and maybe show a message
                    $cartItemRow.removeClass('processing');
                    alert('An error occurred while removing the item. Please try again.');
                }
            });
        }
    };

    $(document).ready(function() {
        SptCartPage.init();
    });

})(jQuery);