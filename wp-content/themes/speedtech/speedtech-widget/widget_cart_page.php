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
        // === Section: Content Settings ===
        $this->start_controls_section(
            'section_content_settings',
            [
                'label' => esc_html__( 'Content Settings', 'speedtech' ),
                'tab' => Controls_Manager::TAB_CONTENT,
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

        $this->add_control(
			'button_text',
			[
				'label' => esc_html__( '"Order Now" Button Text', 'speedtech' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Đặt hàng ngay', 'speedtech' ),
                'dynamic' => [
					'active' => true,
				],
                'label_block' => true,
			]
		);

        $this->end_controls_section();

        // === Section: Column Content Styling ===
        $this->start_controls_section(
            'section_column_content_style',
            [
                'label' => esc_html__( 'Column Content Styling', 'speedtech' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'column_style_tabs' );

        // Tab cho cột "Tên sản phẩm"
        $this->start_controls_tab('tab_product_column_style', ['label' => esc_html__( 'Product', 'speedtech' )]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            ['name' => 'product_typography', 'selector' => '{{WRAPPER}} .spt-cart-table td.product-name']
        );
        $this->add_control(
            'product_color',
            ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-name, {{WRAPPER}} .spt-cart-table td.product-name a' => 'color: {{VALUE}};']]
        );
        $this->add_responsive_control(
            'product_h_align',
            ['label' => esc_html__( 'Horizontal Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__( 'Left', 'speedtech' ), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__( 'Center', 'speedtech' ), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__( 'Right', 'speedtech' ), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-name' => 'text-align: {{VALUE}};']]
        );
        $this->add_responsive_control(
            'product_v_align',
            ['label' => esc_html__( 'Vertical Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => esc_html__( 'Top', 'speedtech' ), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => esc_html__( 'Middle', 'speedtech' ), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => esc_html__( 'Bottom', 'speedtech' ), 'icon' => 'eicon-v-align-bottom']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-name' => 'vertical-align: {{VALUE}};'], 'default' => 'middle']
        );
        $this->end_controls_tab();

        // Tab cho cột "Tên danh mục"
        $this->start_controls_tab('tab_category_column_style', ['label' => esc_html__( 'Category', 'speedtech' )]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            ['name' => 'category_typography', 'selector' => '{{WRAPPER}} .spt-cart-table td.product-category']
        );
        $this->add_control(
            'category_color',
            ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-category, {{WRAPPER}} .spt-cart-table td.product-category a' => 'color: {{VALUE}};']]
        );
        $this->add_responsive_control(
            'category_h_align',
            ['label' => esc_html__( 'Horizontal Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__( 'Left', 'speedtech' ), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__( 'Center', 'speedtech' ), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__( 'Right', 'speedtech' ), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-category' => 'text-align: {{VALUE}};'], 'default' => 'center']
        );
        $this->add_responsive_control(
            'category_v_align',
            ['label' => esc_html__( 'Vertical Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => esc_html__( 'Top', 'speedtech' ), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => esc_html__( 'Middle', 'speedtech' ), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => esc_html__( 'Bottom', 'speedtech' ), 'icon' => 'eicon-v-align-bottom']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-category' => 'vertical-align: {{VALUE}};'], 'default' => 'middle']
        );
        $this->end_controls_tab();

        // Tab cho cột "Giá"
        $this->start_controls_tab('tab_price_column_style', ['label' => esc_html__( 'Price', 'speedtech' )]);
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            ['name' => 'price_typography', 'selector' => '{{WRAPPER}} .spt-cart-table td.product-price']
        );
        $this->add_control(
            'price_color',
            ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-price' => 'color: {{VALUE}};']]
        );
        $this->add_responsive_control(
            'price_h_align',
            ['label' => esc_html__( 'Horizontal Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__( 'Left', 'speedtech' ), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__( 'Center', 'speedtech' ), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__( 'Right', 'speedtech' ), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-price' => 'text-align: {{VALUE}};'], 'default' => 'center']
        );
        $this->add_responsive_control(
            'price_v_align',
            ['label' => esc_html__( 'Vertical Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => esc_html__( 'Top', 'speedtech' ), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => esc_html__( 'Middle', 'speedtech' ), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => esc_html__( 'Bottom', 'speedtech' ), 'icon' => 'eicon-v-align-bottom']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-price' => 'vertical-align: {{VALUE}};'], 'default' => 'middle']
        );
        $this->end_controls_tab();

        // Tab cho cột "Thao tác"
        $this->start_controls_tab('tab_actions_column_style', ['label' => esc_html__( 'Actions', 'speedtech' )]);
        $this->add_responsive_control(
            'actions_h_align',
            ['label' => esc_html__( 'Horizontal Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__( 'Left', 'speedtech' ), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__( 'Center', 'speedtech' ), 'icon' => 'eicon-text-align-center'], 'flex-end' => ['title' => esc_html__( 'Right', 'speedtech' ), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-actions .spt-actions-wrapper' => 'justify-content: {{VALUE}};'], 'default' => 'center']
        );
        $this->add_responsive_control(
            'actions_v_align',
            ['label' => esc_html__( 'Vertical Align', 'speedtech' ), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => esc_html__( 'Top', 'speedtech' ), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => esc_html__( 'Middle', 'speedtech' ), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => esc_html__( 'Bottom', 'speedtech' ), 'icon' => 'eicon-v-align-bottom']], 'selectors' => ['{{WRAPPER}} .spt-cart-table td.product-actions' => 'vertical-align: {{VALUE}};'], 'default' => 'middle']
        );
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

        // === Section: Table Header Styling ===
        $this->start_controls_section(
            'section_table_header_style',
            [
                'label' => esc_html__( 'Table Header Styling', 'speedtech' ),
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
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<div class="elementor-alert elementor-alert-danger">' . esc_html__( 'WooCommerce is not activated.', 'speedtech' ) . '</div>';
            return;
        }

        $cart = WC()->cart;
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
                                                echo ! $product_permalink ? $thumbnail : sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="spt-product-title">
                                            <?php
                                            echo ! $product_permalink ? wp_kses_post( $_product->get_name() ) : wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
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
                                        $buy_now_url = add_query_arg( 'spt_buy_now', $product_id, wc_get_checkout_url() );
                                        ?>
                                        <a href="<?php echo esc_url( $buy_now_url ); ?>" class="button spt-button-order-now">
                                            <?php 
                                            echo esc_html( $settings['button_text'] ); 
                                            ?>
                                        </a>

                                        <?php
                                        $remove_url = wc_get_cart_remove_url( $cart_item_key );
                                        
                                        // 1. Render icon vào một biến
                                        ob_start();
                                        Icons_Manager::render_icon( $settings['remove_icon'], [ 'aria-hidden' => 'true' ] );
                                        $icon_html = ob_get_clean();

                                        // 2. Tạo thẻ a hoàn chỉnh với icon bên trong
                                        $remove_link_html = sprintf(
                                            '<a href="%s" class="spt-remove-item" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s">%s</a>',
                                            esc_url( $remove_url ),
                                            esc_attr__( 'Remove this item', 'speedtech' ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() ),
                                            esc_attr( $cart_item_key ),
                                            $icon_html // Đưa HTML của icon vào đây
                                        );

                                        // 3. Áp dụng filter cho thẻ a đã hoàn chỉnh và hiển thị
                                        echo apply_filters( 'woocommerce_cart_item_remove_link', $remove_link_html, $cart_item_key );
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

    protected function _content_template() {}
}