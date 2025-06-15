/**
 * JS for Product List (SPT) Widget
 * Version: 20.0
 */
(function($) {
    'use strict';

    var ProductListFilter = function($scope, $) {
        var $widget = $scope.find('.spt-product-list-widget');
        var $filterButtons = $widget.find('.spt-service-filter button');
        var $productGrid = $widget.find('.spt-product-grid');

        $filterButtons.on('click', function() {
            var filterValue = $(this).data('filter');
            
            // Cập nhật trạng thái active cho button
            $filterButtons.removeClass('active');
            $(this).addClass('active');

            // Logic lọc sản phẩm
            if (filterValue === 'all') {
                $productGrid.find('.spt-product-item').show();
            } else {
                $productGrid.find('.spt-product-item').hide();
                $productGrid.find('.spt-product-item[data-service-package="' + filterValue + '"]').show();
            }
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/speedtech_product_list.default', ProductListFilter);
    });

})(jQuery);