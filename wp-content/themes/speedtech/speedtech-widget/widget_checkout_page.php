<?php
// FILE: /speedtech-widget/widget_checkout_page.php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SpeedTech_Widget_Checkout_Page extends Widget_Base {

    public function get_name() { return 'speedtech_checkout_page'; }
    public function get_title() { return 'Checkout Page (SPT)'; }
    public function get_icon() { return 'eicon-form-horizontal'; }
    public function get_categories() { return ['speedtech']; }

    public function get_script_depends() {
        wp_register_script( 'spt-checkout-ajax', get_stylesheet_directory_uri() . '/assets/js/widgets/spt-checkout-ajax.js', ['jquery'], time(), true );
        wp_localize_script( 'spt-checkout-ajax', 'spt_checkout_params', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'spt-checkout-nonce' ),
        ]);
        return ['spt-checkout-ajax'];
    }

    public function get_style_depends() {
        wp_register_style('spt-checkout-form-style', get_stylesheet_directory_uri() . '/assets/css/widgets/spt-checkout-form.css');
        return ['spt-checkout-form-style'];
    }

    protected function _register_controls() {
        // === Section: Order Summary Display Settings ===
        $this->start_controls_section('section_summary_display', [
            'label' => esc_html__( 'Order Summary', 'speedtech' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('show_product_image', ['label' => esc_html__( 'Show Product Image', 'speedtech' ),'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_product_title', ['label' => esc_html__( 'Show Product Title', 'speedtech' ),'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_product_description', ['label' => esc_html__( 'Show Short Description', 'speedtech' ),'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_product_price', ['label' => esc_html__( 'Show Product Price', 'speedtech' ),'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->end_controls_section();

        // === Section: Order Summary Styling ===
        $this->start_controls_section('section_order_summary_style', [
            'label' => esc_html__( 'Order Summary Styling', 'speedtech' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('product_image_size', ['label' => esc_html__('Product Image Size', 'speedtech'),'type' => Controls_Manager::SELECT, 'options' => get_intermediate_image_sizes(), 'default' => 'thumbnail',]);
        $this->add_control('product_title_heading', ['label' => esc_html__( 'Product Title', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_title_typography', 'selector' => '{{WRAPPER}} .spt-summary-product-title a']);
        $this->add_control('product_title_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-summary-product-title a' => 'color: {{VALUE}};']]);
        $this->add_control('product_desc_heading', ['label' => esc_html__( 'Short Description', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_desc_typography', 'selector' => '{{WRAPPER}} .spt-summary-product-desc']);
        $this->add_control('product_desc_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-summary-product-desc' => 'color: {{VALUE}};']]);
        $this->add_control('product_price_heading', ['label' => esc_html__( 'Product Price', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_price_typography', 'selector' => '{{WRAPPER}} .spt-summary-product-price .amount']);
        $this->add_control('product_price_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-summary-product-price .amount' => 'color: {{VALUE}};']]);
        $this->end_controls_section();

        // === Section: Form Fields Builder ===
        $this->start_controls_section('section_form_fields', [
            'label' => esc_html__( 'Form Fields Builder', 'speedtech' ), 'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control('field_id', ['label' => 'Field ID', 'type' => Controls_Manager::TEXT, 'description' => 'ID duy nhất, không dấu, không khoảng trắng (e.g., my_custom_field).']);
        $repeater->add_control('field_label', ['label' => 'Field Label', 'type' => Controls_Manager::TEXT, 'default' => 'My Field']);
        $repeater->add_control('field_type', [
            'label' => 'Field Type', 'type' => Controls_Manager::SELECT,
            'options' => [ 'text' => 'Text', 'email' => 'Email', 'password' => 'Password', 'date' => 'Date', 'radio' => 'Radio', 'file' => 'File Upload' ],
            'default' => 'text',
        ]);
        $repeater->add_control('radio_options', ['label' => 'Radio Options', 'type' => Controls_Manager::TEXTAREA, 'description' => 'Mỗi lựa chọn trên một dòng. Định dạng: value|Text', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => '==', 'value' => 'radio']]]]);

        // **NÂNG CẤP MỚI**: Thêm các control tùy chỉnh style cho từng trường
        $repeater->start_controls_tabs('field_styles_tabs');
        $repeater->start_controls_tab('field_style_normal', ['label' => 'Styling']);
        $repeater->add_control('field_label_color', ['label' => 'Label Color','type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-repeater-item-{{_id}} label' => 'color: {{VALUE}};']]);
        $repeater->add_control('field_text_color', ['label' => 'Input Text Color','type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-repeater-item-{{_id}} .input-text' => 'color: {{VALUE}};']]);
        $repeater->add_control('field_background_color', ['label' => 'Input Background Color','type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-repeater-item-{{_id}} .input-text' => 'background-color: {{VALUE}};']]);
        $repeater->add_group_control(Group_Control_Border::get_type(), ['name' => 'field_border', 'selector' => '{{WRAPPER}} .elementor-repeater-item-{{_id}} .input-text']);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'field_typography', 'selector' => '{{WRAPPER}} .elementor-repeater-item-{{_id}} .input-text']);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();

        $this->add_control('form_fields', [
            'label' => 'Add and Customize Fields', 'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['field_id' => 'billing_email', 'field_label' => 'Địa chỉ Email', 'field_type' => 'email'],
                ['field_id' => 'account_password', 'field_label' => 'Mật khẩu', 'field_type' => 'password'],
                ['field_id' => 'spt-date-start', 'field_label' => 'Ngày bắt đầu', 'field_type' => 'date'],
                ['field_id' => 'spt-company-name', 'field_label' => 'Tên công ty', 'field_type' => 'text'],
                ['field_id' => 'spt-main-color', 'field_label' => 'Màu sắc chủ đạo', 'field_type' => 'radio', 'radio_options' => "red|Màu Đỏ\nblue|Màu Xanh\ngreen|Màu Lá"],
                ['field_id' => 'spt-logo', 'field_label' => 'Tải lên Logo', 'field_type' => 'file'],
            ],
            'title_field' => '{{{ field_label }}} ({{{ field_type }}})',
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! is_checkout() || is_wc_endpoint_url() ) { if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) { echo '<div class="elementor-alert">Widget only visible on Checkout page.</div>'; } return; }
        $settings = $this->get_settings_for_display();
        $cart = WC()->cart;
        if ( $cart->is_empty() ) { wc_get_template( 'cart/cart-empty.php' ); return; }

        echo '<div class="spt-checkout-wrapper-v9">';
        
        // --- Phần 1: Tóm tắt đơn hàng ---
        echo '<h2>' . esc_html__('Tóm tắt đơn hàng', 'speedtech') . '</h2>';
        echo '<div class="spt-custom-order-summary">';
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = $cart_item['data'];
            if ( $_product && $_product->exists() ) {
                echo '<div class="spt-summary-item">';
                if ( 'yes' === $settings['show_product_image'] ) {
                    echo '<div class="spt-summary-product-image">' . $_product->get_image($settings['product_image_size']) . '</div>';
                }
                echo '<div class="spt-summary-product-details">';
                if ( 'yes' === $settings['show_product_title'] ) {
                    $permalink = $_product->is_visible() ? $_product->get_permalink() : '#';
                    echo '<div class="spt-summary-product-title"><a href="'.esc_url($permalink).'">'. $_product->get_name() .'</a></div>';
                }
                if ( 'yes' === $settings['show_product_description'] && $_product->get_short_description() ) {
                    echo '<div class="spt-summary-product-desc">' . wp_kses_post( $_product->get_short_description() ) . '</div>';
                }
                if ( 'yes' === $settings['show_product_price'] ) {
                    echo '<div class="spt-summary-product-price">' . $cart->get_product_price( $_product ) . '</div>';
                }
                echo '</div>'; // .spt-summary-product-details
                echo '</div>'; // .spt-summary-item
            }
        }
        echo '</div>'; // .spt-custom-order-summary

        // --- Phần 2: Form thanh toán tùy chỉnh ---
        echo '<form id="spt-checkout-form" class="woocommerce-checkout" enctype="multipart/form-data">';

        if ( ! empty( $settings['form_fields'] ) ) {
            foreach ( $settings['form_fields'] as $field ) {
                echo '<p class="form-row elementor-repeater-item-' . esc_attr($field['_id']) . '" id="'.esc_attr($field['field_id']).'_field">';
                echo '<label for="'.esc_attr($field['field_id']).'">'.esc_html($field['field_label']).'</label>';
                $this->render_field_html( $field );
                echo '</p>';
            }
        }
        
        if ( WC()->cart->needs_payment() ) {
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            if ( ! empty( $available_gateways ) ) {
                echo '<div id="payment" class="woocommerce-checkout-payment">';
                echo '<ul class="wc_payment_methods payment_methods methods">';
                foreach ( $available_gateways as $gateway ) {
                    wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                }
                echo '</ul>';
                echo '</div>';
            }
        }

        echo '<div class="form-row place-order">';
        echo '<button type="submit" class="button alt" id="spt-place-order-button">' . esc_html__('Đặt hàng', 'speedtech') . '</button>';
        echo '</div>';
        echo '<div id="spt-checkout-messages"></div>';

        if ( isset( $_GET['cart_item_key'] ) ) {
             echo '<input type="hidden" name="cart_item_key" value="' . esc_attr( $_GET['cart_item_key'] ) . '" />';
        }

        echo '</form>';
        echo '</div>';
    }

    private function render_field_html( $field ) {
        $id = esc_attr($field['field_id']);
        switch ($field['field_type']) {
            case 'radio':
                $options = explode("\n", $field['radio_options']);
                echo '<span class="woocommerce-input-wrapper">';
                foreach ($options as $option) {
                    $parts = explode('|', $option);
                    if (count($parts) === 2) {
                        $value = esc_attr(trim($parts[0]));
                        $text = esc_html(trim($parts[1]));
                        echo '<label class="radio"><input type="radio" name="'.$id.'" value="'.$value.'"> '.$text.'</label>';
                    }
                }
                echo '</span>';
                break;
            case 'file':
                echo '<span class="woocommerce-input-wrapper"><input type="file" class="input-text" name="'.$id.'" id="'.$id.'"></span>';
                break;
            default:
                $type = esc_attr($field['field_type']);
                echo '<span class="woocommerce-input-wrapper"><input type="'.$type.'" class="input-text" name="'.$id.'" id="'.$id.'"></span>';
                break;
        }
    }
}