<?php
/**
 * Theme functions and definitions
 *
 * @package Speed Tech
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/* Mercyship Elementor Widget */
require_once 'speedtech-widget/widgets.php';
/* END Mercyship Elementor Widget */

/**
 * Load the parent style.css and child style.css
 *
 * @return void
 */
function speed_tech_enqueue_styles() {
    wp_enqueue_style(
        'hello-elementor-style', 
        get_template_directory_uri() . '/style.css', 
        [], 
        wp_get_theme()->parent()->get( 'Version' )
    );
    wp_enqueue_style(
        'speed-tech-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        ['hello-elementor-style'], 
        wp_get_theme()->get( 'Version' )
    );
    wp_enqueue_style(
        'product-style', 
        get_stylesheet_directory_uri() . '/assets/css/product.css', 
        ['hello-elementor-style'], 
        wp_get_theme()->get( 'Version' )
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
 * =================================================================
 * LOGIC CHO QUY TRÌNH THANH TOÁN TÙY CHỈNH CỦA WIDGET SPT CART PAGE
 * =================================================================
 */

/**
 * Ngăn không cho thêm sản phẩm đã có vào giỏ hàng.
 */
add_filter( 'woocommerce_add_to_cart_validation', 'spt_prevent_duplicate_products_in_cart', 20, 3 );
function spt_prevent_duplicate_products_in_cart( $passed, $product_id, $quantity ) {
    // Lặp qua giỏ hàng để kiểm tra
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        // Kiểm tra cả sản phẩm đơn giản và sản phẩm có biến thể
        if ( $cart_item['product_id'] == $product_id ) {
            // Nếu tìm thấy sản phẩm trùng lặp, hiển thị thông báo lỗi
            wc_add_notice( __( 'Sản phẩm này đã có trong giỏ hàng. Bạn chỉ có thể thêm mỗi sản phẩm một lần.', 'speedtech' ), 'error' );
            // Trả về false để ngăn sản phẩm được thêm vào
            return false;
        }
    }
    // Nếu không tìm thấy, cho phép thêm vào giỏ hàng
    return $passed;
}

/**
 * Chỉ hiển thị sản phẩm được chọn trên trang thanh toán.
 */
add_filter( 'woocommerce_get_cart_contents', 'spt_filter_cart_for_single_checkout' );
function spt_filter_cart_for_single_checkout( $cart_contents ) {
    // Chỉ áp dụng bộ lọc này khi có tham số đặc biệt trên URL và đang ở trang thanh toán
    if ( ! is_checkout() || ! isset( $_GET['spt_checkout_single'] ) || ! isset( $_GET['cart_item_key'] ) ) {
        return $cart_contents;
    }

    $cart_item_key_to_checkout = sanitize_text_field( $_GET['cart_item_key'] );

    // Nếu không có sản phẩm nào khớp với key, không làm gì cả
    if ( ! isset( $cart_contents[ $cart_item_key_to_checkout ] ) ) {
        return $cart_contents;
    }

    // Tạo một giỏ hàng mới chỉ chứa duy nhất sản phẩm được chọn
    $new_cart_contents = [];
    $new_cart_contents[ $cart_item_key_to_checkout ] = $cart_contents[ $cart_item_key_to_checkout ];

    return $new_cart_contents;
}

/**
 * Lưu key của sản phẩm đang thanh toán vào đơn hàng để xử lý sau.
 */
add_action( 'woocommerce_checkout_update_order_meta', 'spt_save_single_checkout_key_to_order' );
function spt_save_single_checkout_key_to_order( $order_id ) {
    // Kiểm tra nếu có key của sản phẩm trong URL khi thanh toán
    if ( isset( $_GET['cart_item_key'] ) && isset( $_GET['spt_checkout_single'] ) ) {
        $cart_item_key = sanitize_text_field( $_GET['cart_item_key'] );
        // Lưu key này như một meta của đơn hàng để dùng sau
        update_post_meta( $order_id, '_spt_cart_item_key', $cart_item_key );
    }
}

/**
 * Xóa sản phẩm vừa thanh toán khỏi giỏ hàng sau khi đặt hàng thành công.
 */
add_action( 'woocommerce_thankyou', 'spt_remove_single_item_from_cart_after_payment' );
function spt_remove_single_item_from_cart_after_payment( $order_id ) {
    // Lấy key của sản phẩm đã được lưu trong đơn hàng
    $cart_item_key = get_post_meta( $order_id, '_spt_cart_item_key', true );

    // Nếu có key, tiến hành xóa sản phẩm đó khỏi giỏ hàng
    if ( ! empty( $cart_item_key ) ) {
        // Đảm bảo rằng giỏ hàng chưa bị xóa bởi các tiến trình khác
        if ( WC()->cart && ! WC()->cart->is_empty() ) {
            WC()->cart->remove_cart_item( $cart_item_key );
        }
    }
}