<?php
// FILE: /speedtech-widget/widget_thank_you_page.php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SpeedTech_Widget_Thank_You_Page extends Widget_Base {

    public function get_name() { return 'speedtech_thank_you_page'; }
    public function get_title() { return 'Thank You Page (SPT)'; }
    public function get_icon() { return 'eicon-document-file'; }
    public function get_categories() { return ['speedtech']; }

    protected function _register_controls() {
        // === Section: Thank You Message ===
        $this->start_controls_section('section_thank_you_message', [
            'label' => esc_html__( 'Lời Cảm Ơn', 'speedtech' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('thank_you_text', [
            'label' => esc_html__( 'Nội dung', 'speedtech' ),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__( 'Cảm ơn bạn. Đơn hàng của bạn đã được nhận.', 'speedtech' ),
            'description' => 'Nội dung lời cảm ơn sẽ hiển thị ở đầu trang.'
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'thank_you_typography', 
            'selector' => '{{WRAPPER}} .spt-thank-you-message'
        ]);
        $this->add_control('thank_you_color', [
            'label' => esc_html__( 'Màu chữ', 'speedtech' ), 
            'type' => Controls_Manager::COLOR, 
            'selectors' => ['{{WRAPPER}} .spt-thank-you-message' => 'color: {{VALUE}};']
        ]);
        $this->end_controls_section();

        // === Section: Order Details ===
        $this->start_controls_section('section_order_details', [
            'label' => esc_html__( 'Chi Tiết Đơn Hàng', 'speedtech' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        // Product Image
        $this->add_control('show_product_image', ['label' => esc_html__( 'Hiển thị ảnh sản phẩm', 'speedtech' ),'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('product_image_size', ['label' => esc_html__('Kích thước ảnh', 'speedtech'),'type' => Controls_Manager::SELECT, 'options' => get_intermediate_image_sizes(), 'default' => 'thumbnail', 'condition' => ['show_product_image' => 'yes']]);
        
        // Product Title
        $this->add_control('product_title_heading', ['label' => esc_html__( 'Tên sản phẩm', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_title_typography', 'selector' => '{{WRAPPER}} .spt-product-name a']);
        $this->add_control('product_title_color', ['label' => esc_html__( 'Màu chữ', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-name a' => 'color: {{VALUE}};']]);

        // Product Short Description
        $this->add_control('product_desc_heading', ['label' => esc_html__( 'Mô tả tóm tắt', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_desc_typography', 'selector' => '{{WRAPPER}} .spt-product-short-desc']);
        $this->add_control('product_desc_color', ['label' => esc_html__( 'Màu chữ', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-short-desc' => 'color: {{VALUE}};']]);

        // Product Price
        $this->add_control('product_price_heading', ['label' => esc_html__( 'Giá sản phẩm', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'product_price_typography', 'selector' => '{{WRAPPER}} .spt-product-total .amount']);
        $this->add_control('product_price_color', ['label' => esc_html__( 'Màu chữ', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-total .amount' => 'color: {{VALUE}};']]);
        $this->end_controls_section();

        // === Section: Additional Info ===
        $this->start_controls_section('section_additional_info', [
            'label' => esc_html__( 'Thông Tin Bổ Sung', 'speedtech' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('additional_info_title', ['label' => esc_html__( 'Tiêu đề', 'speedtech' ),'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Thông tin bổ sung', 'speedtech' )]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'additional_title_typography', 'selector' => '{{WRAPPER}} .spt-additional-info-title']);
        $this->add_control('additional_title_color', ['label' => esc_html__( 'Màu tiêu đề', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-additional-info-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'additional_content_typography', 'label' => 'Kiểu chữ nội dung', 'selector' => '{{WRAPPER}} .spt-additional-info-table td']);
        $this->add_control('additional_content_color', ['label' => esc_html__( 'Màu nội dung', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-additional-info-table td' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $order = false;

        if ( is_wc_endpoint_url( 'order-received' ) ) {
            global $wp;
            $order_id = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
            if ($order_id) {
                $order = wc_get_order( $order_id );
                $order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : '';
                if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
                    $order = false; // Invalidate order if key doesn't match
                }
            }
        } elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            // In editor mode, get the latest order for preview
            $orders = wc_get_orders(['limit' => 1, 'orderby' => 'date', 'order' => 'DESC']);
            if (!empty($orders)) {
                $order = $orders[0];
            }
        }

        if ( ! $order ) {
            echo '<p>' . esc_html__('Không tìm thấy thông tin đơn hàng. Widget này chỉ hiển thị trên trang "Đơn hàng đã nhận" hoặc khi có đơn hàng để xem trước.', 'speedtech') . '</p>';
            return;
        }

        // Render the output
        ?>
        <div class="spt-thank-you-page-widget">
            <p class="spt-thank-you-message">
                <?php echo esc_html( $settings['thank_you_text'] ); ?>
            </p>

            <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Chi tiết đơn hàng', 'speedtech' ); ?></h2>
            <table class="shop_table order_details">
                <thead>
                    <tr>
                        <th class="product-name" colspan="<?php echo $settings['show_product_image'] === 'yes' ? 2 : 1; ?>"><?php esc_html_e( 'Sản phẩm', 'woocommerce' ); ?></th>
                        <th class="product-total"><?php esc_html_e( 'Tổng cộng', 'woocommerce' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $order->get_items() as $item ) :
                        $product = $item->get_product();
                    ?>
                    <tr>
                        <?php if ($settings['show_product_image'] === 'yes') : ?>
                            <td class="product-thumbnail">
                                <?php echo $product ? $product->get_image($settings['product_image_size']) : ''; ?>
                            </td>
                        <?php endif; ?>
                        <td class="product-name spt-product-name">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($item->get_name()); ?></a>
                            <strong class="product-quantity">&times;&nbsp;<?php echo esc_html($item->get_quantity()); ?></strong>
                            <?php if ($product && $product->get_short_description()) : ?>
                                <div class="spt-product-short-desc"><?php echo wp_kses_post($product->get_short_description()); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="product-total spt-product-total">
                            <?php echo $order->get_formatted_line_subtotal( $item ); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?php foreach ( $order->get_order_item_totals() as $key => $total ) :
                        if ('cart_subtotal' === $key) continue;
                    ?>
                        <tr>
                            <th scope="row" colspan="<?php echo $settings['show_product_image'] === 'yes' ? 2 : 1; ?>"><?php echo esc_html( $total['label'] ); ?></th>
                            <td><?php echo wp_kses_post( $total['value'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tfoot>
            </table>

            <?php 
            $meta_data_items = $order->get_meta_data();
            $custom_fields_to_display = [];
            foreach ($meta_data_items as $meta) {
                if (strpos($meta->key, '_spt-') === 0) {
                    $custom_fields_to_display[] = $meta;
                }
            }

            if (!empty($custom_fields_to_display)) :
            ?>
            <h2 class="spt-additional-info-title"><?php echo esc_html($settings['additional_info_title']); ?></h2>
            <table class="woocommerce-table spt-additional-info-table">
                <tbody>
                    <?php foreach ($custom_fields_to_display as $meta) : 
                        $label = ucwords(str_replace(['_spt-', '-'], ' ', $meta->key));
                    ?>
                    <tr>
                        <th><?php echo esc_html($label); ?>:</th>
                        <td><?php echo esc_html($meta->value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) ); ?>
        </div>
        <?php
    }
}