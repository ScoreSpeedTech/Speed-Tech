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
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( $cart_item['product_id'] == $product_id ) {
            wc_add_notice( __( 'Sản phẩm này đã có trong giỏ hàng. Bạn chỉ có thể thêm mỗi sản phẩm một lần.', 'speedtech' ), 'error' );
            return false;
        }
    }
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
    $is_user_logged_in = is_user_logged_in();

    // --- Bắt đầu xác thực dữ liệu ---
    
    // Lấy danh sách các trường bắt buộc từ form
    $required_fields = isset($posted_data['spt_required_fields']) ? json_decode($posted_data['spt_required_fields'], true) : [];

    // **LOGIC VALIDATION MỚI**: Dựa trên danh sách trường bắt buộc
    foreach ($required_fields as $field_id) {
        // Bỏ qua validation mật khẩu nếu đã đăng nhập
        if ($field_id === 'account_password' && $is_user_logged_in) {
            continue;
        }

        if ($field_id === 'spt-logo') { // Xử lý cho trường file
            if (empty($_FILES[$field_id]['name'])) {
                $error_fields[$field_id] = 'Vui lòng tải lên file cho trường này.';
            }
        } else { // Xử lý cho các trường khác
            if (empty($posted_data[$field_id])) {
                $error_fields[$field_id] = 'Đây là trường bắt buộc.';
            }
        }
    }
    
    // Validation riêng cho các trường cụ thể
    if ( empty($posted_data['billing_email']) || ! is_email($posted_data['billing_email']) ) {
        $error_fields['billing_email'] = 'Vui lòng nhập một địa chỉ email hợp lệ.';
    }

    // **VALIDATION MẬT KHẨU NÂNG CAO**: Chỉ kiểm tra nếu người dùng chưa đăng nhập
    if ( ! $is_user_logged_in && in_array('account_password', $required_fields) ) {
        $password = $posted_data['account_password'];
        $pattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        if ( empty($password) ) {
            $error_fields['account_password'] = 'Vui lòng nhập mật khẩu.';
        } elseif ( strlen($password) < 8 ) {
            $error_fields['account_password'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        } elseif ( !preg_match($pattern, $password) ) {
            $error_fields['account_password'] = 'Mật khẩu phải bao gồm chữ, số và ký tự đặc biệt (@$!%*?&).';
        }
    }

    if ( empty($posted_data['payment_method']) ) {
         $messages[] = 'Vui lòng chọn một phương thức thanh toán.';
    }

    // Xác thực file upload (nếu có)
    if (isset($_FILES['spt-logo']) && !empty($_FILES['spt-logo']['name'])) {
        $file = $_FILES['spt-logo'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowed_mimes)) {
            $error_fields['spt-logo'] = 'Định dạng file không hợp lệ (chỉ chấp nhận jpg, png, doc, docx, pdf).';
        }
        if ($file['size'] > 500 * 1024) { // 500KB
            $error_fields['spt-logo'] = 'Dung lượng file không được vượt quá 500KB.';
        }
    }

    // --- Kết thúc xác thực ---

    if (!empty($error_fields) || !empty($messages)) {
        $all_messages = array_merge($messages, array_values($error_fields));
        wp_send_json_error(['messages' => $all_messages, 'error_fields' => $error_fields]);
    }

    // --- Bắt đầu xử lý tạo đơn hàng nếu không có lỗi ---
    try {
        $order = wc_create_order();

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $order->add_product( $cart_item['data'], $cart_item['quantity'] );
        }
        
        // **LOGIC MỚI**: Lấy email và user ID
        $billing_email = sanitize_email($posted_data['billing_email']);
        $address = ['email' => $billing_email];
        $order->set_address( $address, 'billing' );
        
        if ( $is_user_logged_in ) {
            $order->set_customer_id( get_current_user_id() );
        } else {
            $user_id = email_exists( $billing_email );
            if ( ! $user_id ) {
                $user_id = wc_create_new_customer( $billing_email, '', $posted_data['account_password'] );
                if ( is_wp_error( $user_id ) ) {
                    throw new Exception( $user_id->get_error_message() );
                }
                 wc_set_customer_auth_cookie($user_id);
            }
            $order->set_customer_id( $user_id );
        }
        
        // **LOGIC MỚI**: Lưu tất cả các trường tùy chỉnh bắt đầu bằng 'spt-'
        foreach ($posted_data as $key => $value) {
            if (strpos($key, 'spt-') === 0 && $key !== 'spt_required_fields') {
                 $order->update_meta_data('_' . $key, sanitize_text_field($value));
            }
        }
        // Lưu trường email và các trường woocommerce chuẩn khác nếu cần
        $order->update_meta_data('_billing_email', $billing_email);


        // Xử lý file upload
        foreach ($_FILES as $key => $file) {
            if (strpos($key, 'spt-') === 0 && !empty($file['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                $attachment_id = media_handle_upload($key, $order->get_id());
                if (!is_wp_error($attachment_id)) {
                    $order->update_meta_data('_' . $key, $attachment_id);
                } else {
                     $order->add_order_note('Lỗi upload file ' . $key . ': ' . $attachment_id->get_error_message());
                }
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

// **Hàm hiển thị dữ liệu trên trang Cảm ơn và Admin (Nâng cấp)**
function spt_display_custom_order_data( $order ) {
    if ( ! $order ) return;

    // Lấy tất cả metadata của đơn hàng
    $meta_data = $order->get_meta_data();
    $custom_data_to_display = [];

    // Lọc những meta bắt đầu bằng '_spt-'
    foreach ($meta_data as $meta) {
        $data = $meta->get_data();
        if (strpos($data['key'], '_spt-') === 0) {
            $label = ucwords(str_replace(['_spt-', '-'], ' ', $data['key']));
            $value = $data['value'];

            // Xử lý hiển thị cho file upload
            if (strpos($data['key'], '_spt-logo') !== false && is_numeric($value)) {
                 $file_url = wp_get_attachment_url( $value );
                 $file_name = get_the_title( $value ) ?: basename($file_url);
                 $display_value = '<a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a>';
            } else {
                $display_value = esc_html($value);
            }
            $custom_data_to_display[] = ['label' => $label, 'value' => $display_value];
        }
    }

    if (empty($custom_data_to_display)) return;

    echo '<h2>' . __('Thông tin bổ sung', 'speedtech') . '</h2>';
    echo '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details"><tbody>';
    foreach ( $custom_data_to_display as $data_item ) {
        echo '<tr><th>' . esc_html($data_item['label']) . ':</th><td>' . wp_kses_post($data_item['value']) . '</td></tr>';
    }
    echo '</tbody></table>';
}


// Hiển thị trên trang Cảm ơn
add_action( 'woocommerce_thankyou', 'spt_display_custom_data_on_thankyou_page', 20, 1 );
function spt_display_custom_data_on_thankyou_page( $order_id ) {
    $order = wc_get_order( $order_id );
    spt_display_custom_order_data($order);
}

// Hiển thị trong trang chi tiết đơn hàng ở Admin
add_action( 'woocommerce_admin_order_data_after_billing_address', 'spt_display_custom_data_in_admin_order', 10, 1 );
function spt_display_custom_data_in_admin_order( $order ){
    echo '<div class="order_data_column">';
    spt_display_custom_order_data($order);
    echo '</div>';
}