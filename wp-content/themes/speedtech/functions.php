<?php
/**
 * Theme functions and definitions
 *
 * @package Speed Tech
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/* Elementor Widget */
require_once 'speedtech-widget/widgets.php';
/* END Elementor Widget */

/**
 * Load the parent style.css and child style.css
 */
function speed_tech_enqueue_styles() {
    wp_enqueue_style( 'hello-elementor-style', get_template_directory_uri() . '/style.css', [], wp_get_theme()->parent()->get( 'Version' ) );
    wp_enqueue_style( 'speed-tech-style', get_stylesheet_directory_uri() . '/style.css', ['hello-elementor-style'], wp_get_theme()->get( 'Version' ) );
    wp_enqueue_style( 'product-style', get_stylesheet_directory_uri() . '/assets/css/product.css', ['hello-elementor-style'], wp_get_theme()->get( 'Version' ) );
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
 * Logic kiểm soát giỏ hàng (từ phiên bản 16)
 */
add_filter( 'woocommerce_add_to_cart_validation', 'spt_single_item_cart_validation', 10, 3 );
function spt_single_item_cart_validation( $passed, $product_id, $quantity ) {
    if ( ! WC()->cart->is_empty() ) {
        wc_add_notice( __( 'Quý khách vui lòng thanh toán sản phẩm trong giỏ hàng, trước khi mua thêm sản phẩm.', 'speedtech' ), 'error' );
        return false;
    }
    if ( is_user_logged_in() ) {
        $current_customer_id = get_current_user_id();
        $unfinished_statuses = ['wc-pending', 'wc-on-hold', 'wc-processing', 'wc-failed'];
        $orders = wc_get_orders(['customer_id' => $current_customer_id, 'status' => $unfinished_statuses, 'limit' => -1]);
        if ( ! empty( $orders ) ) {
            foreach ( $orders as $order ) {
                foreach ( $order->get_items() as $item ) {
                    if ( $item->get_product_id() == $product_id || $item->get_variation_id() == $product_id ) {
                        $status_label = wc_get_order_status_name( $order->get_status() );
                        $message = sprintf( __( 'Đã có đơn hàng ở trạng thái "%s" cho sản phẩm này. Vui lòng kiểm tra lại trong tài khoản của bạn.', 'speedtech' ), $status_label );
                        wc_add_notice( $message, 'error' );
                        return false;
                    }
                }
            }
        }
    }
    return $passed;
}

/**
 * **CHỈNH SỬA LẦN 19: Chuyển hướng thẳng đến trang checkout sau khi thêm vào giỏ hàng thành công.**
 */
add_filter( 'woocommerce_add_to_cart_redirect', 'spt_redirect_to_checkout_after_add_to_cart', 99 );
function spt_redirect_to_checkout_after_add_to_cart( $url ) {
    return wc_get_checkout_url();
}


/* =================================================================
 * LOGIC THANH TOÁN VÀ XỬ LÝ GIỎ HÀNG (TỪ PHIÊN BẢN 17)
 * ================================================================= */

add_filter( 'woocommerce_get_cart_contents', 'spt_filter_cart_for_single_checkout' );
function spt_filter_cart_for_single_checkout( $cart_contents ) {
    if ( is_checkout() && ! is_wc_endpoint_url() && isset( $_GET['spt_checkout_single'] ) && isset( $_GET['cart_item_key'] ) ) {
        $cart_item_key_to_checkout = sanitize_text_field( $_GET['cart_item_key'] );
        if ( isset( $cart_contents[ $cart_item_key_to_checkout ] ) ) {
            return [ $cart_item_key_to_checkout => $cart_contents[ $cart_item_key_to_checkout ] ];
        }
    }
    return $cart_contents;
}

add_action('wp_ajax_spt_process_checkout', 'spt_process_checkout_ajax_handler');
add_action('wp_ajax_nopriv_spt_process_checkout', 'spt_process_checkout_ajax_handler');
function spt_process_checkout_ajax_handler() {
    check_ajax_referer('spt-checkout-nonce', 'nonce');
    if ( WC()->session && WC()->cart ) {
        WC()->session->set('spt_cart_backup', WC()->cart->get_cart_for_session());
    }
    try {
        $order = wc_create_order();
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $order->add_product( $cart_item['data'], $cart_item['quantity'] );
        }
        $posted_data = wp_unslash($_POST);
        $billing_email = sanitize_email($posted_data['billing_email']);
        $order->set_address(['email' => $billing_email], 'billing');
        if ( is_user_logged_in() ) {
            $order->set_customer_id( get_current_user_id() );
        } else {
            $user_id = email_exists($billing_email);
            if (!$user_id) {
                $user_id = wc_create_new_customer($billing_email, '', $posted_data['account_password']);
                if (is_wp_error($user_id)) throw new Exception($user_id->get_error_message());
                wc_set_customer_auth_cookie($user_id);
            }
            $order->set_customer_id($user_id);
        }
        foreach ($posted_data as $key => $value) {
            if (strpos($key, 'spt-') === 0) {
                 $order->update_meta_data('_' . $key, sanitize_text_field($value));
            }
        }
        $order->calculate_totals();
        $order->save();
        WC()->session->set('spt_last_order_id', $order->get_id());
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        $result = $payment_gateways[sanitize_text_field($posted_data['payment_method'])]->process_payment($order->get_id());
        if ($result['result'] == 'success') {
            wp_send_json_success(['redirect_url' => $result['redirect']]);
        } else {
            throw new Exception('Không thể xử lý thanh toán. Vui lòng thử lại.');
        }
    } catch (Exception $e) {
        wp_send_json_error(['messages' => [$e->getMessage()]]);
    }
}

add_action( 'template_redirect', 'spt_restore_and_clean_cart' );
function spt_restore_and_clean_cart() {
    if ( ! is_wc_endpoint_url('order-received') || ! WC()->session ) return;
    $cart_backup = WC()->session->get('spt_cart_backup');
    $last_order_id = WC()->session->get('spt_last_order_id');
    if ( $cart_backup !== null && $last_order_id && WC()->cart ) {
        WC()->session->set('cart', $cart_backup);
        WC()->cart->get_cart_from_session();
        $order = wc_get_order($last_order_id);
        if ($order) {
            $ordered_product_ids = [];
            foreach( $order->get_items() as $item ) {
                $ordered_product_ids[$item->get_product_id()] = true;
                if ($item->get_variation_id()) $ordered_product_ids[$item->get_variation_id()] = true;
            }
            if ( ! empty( $ordered_product_ids ) ) {
                foreach( WC()->cart->get_cart() as $key => $item ) {
                    if (isset($ordered_product_ids[$item['product_id']]) || isset($ordered_product_ids[$item['variation_id']])) {
                        WC()->cart->remove_cart_item( $key );
                    }
                }
            }
        }
        WC()->session->__unset('spt_cart_backup');
        WC()->session->__unset('spt_last_order_id');
    }
}

// **CHỈNH SỬA LẦN 19: Xóa các hàm hiển thị cũ, vì đã chuyển vào widget**
// Hàm spt_render_thank_you_page_content() đã được xóa.
// Hàm spt_add_custom_thank_you_page_css() đã được xóa.
// Hàm spt_display_custom_order_data() giờ được gọi bên trong widget mới.