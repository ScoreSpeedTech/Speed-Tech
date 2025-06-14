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

/* =================================================================
 * LOGIC CHO QUY TRÌNH THANH TOÁN TÙY CHỈNH (SINGLE ITEM & AJAX FORM)
 * ================================================================= */

// Giữ lại bộ lọc giỏ hàng để tương thích với widget_cart_page.php
add_filter( 'woocommerce_get_cart_contents', 'spt_filter_cart_for_single_checkout' );
function spt_filter_cart_for_single_checkout( $cart_contents ) {
    if ( ! is_checkout() || ! isset( $_GET['spt_checkout_single'] ) || ! isset( $_GET['cart_item_key'] ) ) {
        return $cart_contents;
    }
    $cart_item_key_to_checkout = sanitize_text_field( $_GET['cart_item_key'] );
    if ( ! isset( $cart_contents[ $cart_item_key_to_checkout ] ) ) {
        return $cart_contents;
    }
    $new_cart_contents = [];
    $new_cart_contents[ $cart_item_key_to_checkout ] = $cart_contents[ $cart_item_key_to_checkout ];
    return $new_cart_contents;
}

// Hàm xử lý AJAX cho form thanh toán tùy chỉnh
add_action('wp_ajax_spt_process_checkout', 'spt_process_checkout_ajax_handler');
add_action('wp_ajax_nopriv_spt_process_checkout', 'spt_process_checkout_ajax_handler');

function spt_process_checkout_ajax_handler() {
    check_ajax_referer('spt-checkout-nonce', 'nonce');
    
    $messages = [];
    $error_fields = [];
    $posted_data = wp_unslash($_POST);

    // --- Bắt đầu xác thực dữ liệu ---
    if ( empty($posted_data['billing_email']) || ! is_email($posted_data['billing_email']) ) {
        $error_fields['billing_email'] = 'Vui lòng nhập một địa chỉ email hợp lệ.';
    }
    if ( empty($posted_data['account_password']) ) {
         $error_fields['account_password'] = 'Vui lòng nhập mật khẩu.';
    }
    // ... Thêm các quy tắc xác thực cho các trường khác ở đây ...
    // Ví dụ:
    if ( empty($posted_data['spt-company-name']) ) {
         $error_fields['spt-company-name'] = 'Vui lòng nhập tên công ty.';
    }
    if ( empty($posted_data['payment_method']) ) {
         $messages[] = 'Vui lòng chọn một phương thức thanh toán.';
    }

    // Xác thực file upload
    if (isset($_FILES['spt-logo']) && !empty($_FILES['spt-logo']['name'])) {
        $file = $_FILES['spt-logo'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowed_mimes)) {
            $error_fields['spt-logo'] = 'Định dạng file không hợp lệ (chỉ chấp nhận jpg, png, doc, docx, pdf).';
        }
        if ($file['size'] > 500 * 1024) { // 500KB
            $error_fields['spt-logo'] = 'Dung lượng file không được vượt quá 500KB.';
        }
    } else if (isset($posted_data['spt-logo'])) { // Kiểm tra nếu trường file tồn tại trong form
        $error_fields['spt-logo'] = 'Vui lòng tải lên logo của bạn.';
    }

    // --- Kết thúc xác thực ---

    if (!empty($error_fields) || !empty($messages)) {
        // Gộp tất cả các lỗi vào một mảng chung để hiển thị
        $all_messages = array_merge($messages, array_values($error_fields));
        wp_send_json_error(['messages' => $all_messages, 'error_fields' => $error_fields]);
    }

    // --- Bắt đầu xử lý tạo đơn hàng nếu không có lỗi ---
    try {
        $order = wc_create_order();

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $order->add_product( $cart_item['data'], $cart_item['quantity'] );
        }

        $address = ['email' => sanitize_email($posted_data['billing_email'])];
        $order->set_address( $address, 'billing' );
        
        // Tạo khách hàng mới nếu chưa đăng nhập
        if ( ! is_user_logged_in() ) {
            $user_id = email_exists( $address['email'] );
            if ( ! $user_id ) {
                $user_id = wc_create_new_customer( $address['email'], '', $posted_data['account_password'] );
                if ( is_wp_error( $user_id ) ) {
                    throw new Exception( $user_id->get_error_message() );
                }
                 wc_set_customer_auth_cookie($user_id);
            }
            $order->set_customer_id( $user_id );
        }
        
        // Lưu các trường tùy chỉnh
        foreach ($posted_data as $key => $value) {
            if (strpos($key, 'spt-') === 0 || in_array($key, ['spt-company-name', 'spt-slogan', 'spt-main-color'])) {
                 $order->update_meta_data('_' . $key, sanitize_text_field($value));
            }
        }

        // Xử lý file upload
        if (isset($_FILES['spt-logo']) && !is_wp_error($_FILES['spt-logo'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            $attachment_id = media_handle_upload('spt-logo', $order->get_id());
            if (!is_wp_error($attachment_id)) {
                $order->update_meta_data('_spt-logo', $attachment_id);
            }
        }
        
        $order->calculate_totals();
        $order->set_payment_method( sanitize_text_field($posted_data['payment_method']) );
        $order->save();
        
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        $result = $payment_gateways[ $order->get_payment_method() ]->process_payment( $order->get_id() );

        if ($result['result'] == 'success') {
            if ( WC()->cart && ! WC()->cart->is_empty() ) {
                WC()->cart->empty_cart();
            }
            wp_send_json_success(['redirect_url' => $result['redirect']]);
        } else {
            throw new Exception('Không thể xử lý thanh toán. Vui lòng thử lại.');
        }

    } catch (Exception $e) {
        wp_send_json_error(['messages' => [$e->getMessage()]]);
    }
}

// Hàm hiển thị dữ liệu trên trang Cảm ơn và Admin (giữ nguyên)
add_action( 'woocommerce_thankyou', 'spt_display_custom_data_on_thankyou_page', 20 );
function spt_display_custom_data_on_thankyou_page( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;
    $field_labels = [
        '_spt-date-start'   => 'Ngày bắt đầu', '_spt-date-finish'  => 'Ngày kết thúc', '_spt-company-name' => 'Tên công ty', '_spt-slogan'       => 'Slogan', '_spt-main-color'   => 'Màu sắc chủ đạo', '_spt-logo'         => 'Logo',
    ];
    echo '<h2>' . __('Thông tin bổ sung', 'speedtech') . '</h2><div class="spt-order-meta-display">';
    foreach ( $field_labels as $key => $label ) {
        $value = $order->get_meta( $key );
        if ( $value ) {
            if ( $key == '_spt-logo' ) {
                $file_url = wp_get_attachment_url( $value ); $file_name = get_the_title( $value );
                echo '<p><strong>' . esc_html($label) . ':</strong> <a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a></p>';
            } else {
                echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
            }
        }
    }
    echo '</div>';
}

//add_action( 'woocommerce_admin_order_data_after_billing_address', 'spt_display_metform_data_in_admin_order', 10, 1 );
// ... (mã hàm spt_display_metform_data_in_admin_order, đổi tên nếu cần)