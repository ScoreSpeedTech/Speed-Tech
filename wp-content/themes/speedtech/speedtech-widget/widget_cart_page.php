<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SpeedTech_Widget_Cart_Page extends Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        // Đăng ký CSS cho widget
        $css_path = get_stylesheet_directory() . '/assets/css/widgets/cart_page.css';
        wp_register_style(
            'spt-cart-page-style',
            get_stylesheet_directory_uri() . '/assets/css/widgets/cart_page.css',
            [],
            file_exists($css_path) ? filemtime($css_path) : time()
        );

        // Đăng ký JS cho widget
        $js_path = get_stylesheet_directory() . '/assets/js/widgets/cart_page.js';
        wp_register_script(
            'spt-cart-page-js',
            get_stylesheet_directory_uri() . '/assets/js/widgets/cart_page.js',
            ['jquery', 'wc-cart-fragments'],
            file_exists($js_path) ? filemtime($js_path) : time(),
            true
        );
    }

    public function get_name() {
        return 'speedtech_cart_page';
    }

    public function get_title() {
        return 'Cart Page (SPT)';
    }

    public function get_icon() {
        return 'eicon-cart';
    }

    public function get_categories() {
        return ['speedtech'];
    }

    public function get_style_depends() {
        return ['spt-cart-page-style'];
    }

    public function get_script_depends() {
        return ['spt-cart-page-js'];
    }

    protected function _register_controls() {
        // === Section: General Settings ===
        $this->start_controls_section(
            'section_general_settings',
            [
                'label' => esc_html__( 'General Settings', 'speedtech' ),
            ]
        );

        $this->add_control(
            'show_product_image',
            [
                'label' => esc_html__( 'Show Product Image', 'speedtech' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'speedtech' ),
                'label_off' => esc_html__( 'Hide', 'speedtech' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'remove_icon',
            [
                'label' => esc_html__( 'Remove Icon', 'speedtech' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-trash-alt',
                    'library' => 'solid',
                ],
            ]
        );

        $this->end_controls_section();

        // === Section: Table Styling ===
        $this->start_controls_section(
            'section_table_style',
            [
                'label' => esc_html__( 'Table Styling', 'speedtech' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'table_header_typography',
                'label' => esc_html__( 'Header Typography', 'speedtech' ),
                'selector' => '{{WRAPPER}} .spt-cart-table th',
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'table_content_typography',
				'label' => esc_html__( 'Content Typography', 'speedtech' ),
				'selector' => '{{WRAPPER}} .spt-cart-table td',
			]
		);

        $this->end_controls_section();

        // === Section: Product Row Styling ===
        $this->start_controls_section(
            'section_row_style',
            [
                'label' => esc_html__( 'Product Row Styling', 'speedtech' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'row_background_color',
            [
                'label' => esc_html__( 'Background Color', 'speedtech' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .spt-cart-table tbody tr' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'row_border',
                'label' => esc_html__( 'Border', 'speedtech' ),
                'selector' => '{{WRAPPER}} .spt-cart-table tbody tr',
            ]
        );

        $this->end_controls_section();

        // === Section: Order Button Styling ===
        $this->start_controls_section(
            'section_order_button_style',
            [
                'label' => esc_html__( '"Order Now" Button', 'speedtech' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .spt-button-order-now',
            ]
        );

        $this->start_controls_tabs( 'button_style_tabs' );

        // Normal Tab
        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'speedtech' ),
            ]
        );
        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'speedtech' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .spt-button-order-now' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'speedtech' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .spt-button-order-now' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'speedtech' ),
            ]
        );
        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__( 'Text Color', 'speedtech' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .spt-button-order-now:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'speedtech' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .spt-button-order-now:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__( 'Padding', 'speedtech' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .spt-button-order-now' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Kiểm tra nếu WooCommerce chưa được kích hoạt
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<div class="elementor-alert elementor-alert-danger">' . esc_html__( 'WooCommerce is not activated.', 'speedtech' ) . '</div>';
            return;
        }

        // Lấy giỏ hàng của WooCommerce
        $cart = WC()->cart;

        // Nếu giỏ hàng trống, hiển thị template mặc định của Woo
        if ( $cart->is_empty() ) {
            wc_get_template( 'cart/cart-empty.php' );
            return;
        }
        
        $settings = $this->get_settings_for_display();
        ?>
        <div class="spt-cart-page-widget">
            <table class="spt-cart-table">
                <thead>
                    <tr>
                        <th class="product-name"><?php esc_html_e( 'Tên sản phẩm', 'speedtech' ); ?></th>
                        <th class="product-category"><?php esc_html_e( 'Tên danh mục', 'speedtech' ); ?></th>
                        <th class="product-price"><?php esc_html_e( 'Giá', 'speedtech' ); ?></th>
                        <th class="product-actions"><?php esc_html_e( 'Thao tác', 'speedtech' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="spt-cart-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
                                <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'speedtech' ); ?>">
                                    <div class="spt-product-info">
                                        <?php if ( 'yes' === $settings['show_product_image'] ) : ?>
                                            <div class="spt-product-thumbnail">
                                                <?php
                                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                                if ( ! $product_permalink ) {
                                                    echo $thumbnail;
                                                } else {
                                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="spt-product-title">
                                            <?php
                                            if ( ! $product_permalink ) {
                                                echo wp_kses_post( $_product->get_name() );
                                            } else {
                                                echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
                                            }
                                            // Meta data
                                            echo wc_get_formatted_cart_item_data( $cart_item );
                                            ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="product-category" data-title="<?php esc_attr_e( 'Category', 'speedtech' ); ?>">
                                    <?php echo wc_get_product_category_list( $product_id, ', ', '<span class="posted_in">', '</span>' ); ?>
                                </td>

                                <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'speedtech' ); ?>">
                                    <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                                </td>

                                <td class="product-actions" data-title="<?php esc_attr_e( 'Actions', 'speedtech' ); ?>">
                                    <div class="spt-actions-wrapper">
                                        <?php
                                        // Nút Đặt hàng ngay
                                        $buy_now_url = add_query_arg( 'spt_buy_now', $product_id, wc_get_checkout_url() );
                                        ?>
                                        <a href="<?php echo esc_url( $buy_now_url ); ?>" class="button spt-button-order-now">
                                            <?php esc_html_e( 'Đặt hàng ngay', 'speedtech' ); ?>
                                        </a>

                                        <?php
                                        // Biểu tượng xóa
                                        $remove_url = wc_get_cart_remove_url( $cart_item_key );
                                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                            '<a href="%s" class="spt-remove-item" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s">',
                                            esc_url( $remove_url ),
                                            esc_html__( 'Remove this item', 'speedtech' ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() ),
                                            esc_attr( $cart_item_key )
                                        ), $cart_item_key );
                                        Icons_Manager::render_icon( $settings['remove_icon'], [ 'aria-hidden' => 'true' ] );
                                        echo '</a>';
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    protected function _content_template() {
        // Live preview trong Elementor editor sẽ được xử lý bằng JS nếu cần.
        // Để đơn giản, phần này có thể để trống và widget sẽ hiển thị đầy đủ khi refresh.
    }
}