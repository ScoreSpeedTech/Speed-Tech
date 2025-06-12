<?php
/**
 * Theme functions and definitions
 *
 * @package Speed Tech
 */


 /* Mercyship Elementor Widget */
 
require_once 'speedtech-widget/widgets.php';

/* END Mercyship Elementor Widget */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Load the parent style.css and child style.css
 *
 * @return void
 */
function speed_tech_enqueue_styles() {
    wp_enqueue_style(
        'hello-elementor-style', get_template_directory_uri() . '/style.css', [], wp_get_theme()->parent()->get( 'Version' )
    );
    wp_enqueue_style(
        'speed-tech-style', get_stylesheet_directory_uri() . '/style.css', ['hello-elementor-style'], wp_get_theme()->get( 'Version' )
    );
    wp_enqueue_style(
        'product-style', get_stylesheet_directory_uri() . '/assets/css/product.css', ['hello-elementor-style'], wp_get_theme()->get( 'Version' )
    );

}
add_action( 'wp_enqueue_scripts', 'speed_tech_enqueue_styles' );

/**
 * Disable comments on pages that are NOT single posts or single products.
 */
function disable_comments_on_non_single_pages() {
    if ( ! is_single() && ! is_product() ) {
        add_filter( 'comments_open', '__return_false', 10, 2 );
        add_filter( 'pings_open', '__return_false', 10, 2 );
    }
}
add_action( 'wp', 'disable_comments_on_non_single_pages' );


/**
 * Handles the "Buy Now" functionality for the SPT Cart Page widget.
 * When the ?spt_buy_now=PRODUCT_ID parameter is found in the checkout URL,
 * it clears the cart, adds the specified product, and proceeds to checkout.
 */
add_action( 'template_redirect', 'spt_handle_buy_now' );
function spt_handle_buy_now() {
    // Chỉ chạy trên trang checkout và khi có tham số spt_buy_now
    if ( ! is_checkout() || ! isset( $_GET['spt_buy_now'] ) ) {
        return;
    }
    
    // Kiểm tra nếu WooCommerce không hoạt động
    if ( ! function_exists( 'WC' ) ) {
        return;
    }
    
    $product_id = absint( $_GET['spt_buy_now'] );
    
    // Kiểm tra ID sản phẩm hợp lệ
    if ( $product_id <= 0 ) {
        return;
    }
    
    // Lấy đối tượng sản phẩm
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return;
    }
    
    // Xóa giỏ hàng hiện tại
    WC()->cart->empty_cart();
    
    // Thêm sản phẩm được chọn vào giỏ hàng
    try {
        WC()->cart->add_to_cart( $product_id, 1 );
    } catch ( Exception $e ) {
        // Xử lý lỗi nếu có, ví dụ: ghi log
        wc_add_notice( $e->getMessage(), 'error' );
        return;
    }
    
    // Chuyển hướng đến trang thanh toán (loại bỏ tham số để tránh lặp)
    wp_redirect( wc_get_checkout_url() );
    exit;
}