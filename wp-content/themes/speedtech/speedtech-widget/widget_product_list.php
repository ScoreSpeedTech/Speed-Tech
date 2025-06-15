<?php
/**
 * Elementor Product List Widget
 *
 * @package SpeedTech
 * @since 20.0
 * @version 20.6
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SpeedTech_Widget_Product_List extends Widget_Base {

    const VERSION = '20.6'; // Cập nhật phiên bản

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        $css_path = get_stylesheet_directory() . '/assets/css/widgets/product_list.css';
        wp_register_style( 'spt-product-list-style', get_stylesheet_directory_uri() . '/assets/css/widgets/product_list.css', [], file_exists( $css_path ) ? filemtime( $css_path ) : self::VERSION );
        $js_path = get_stylesheet_directory() . '/assets/js/widgets/product_list.js';
        wp_register_script( 'spt-product-list-js', get_stylesheet_directory_uri() . '/assets/js/widgets/product_list.js', [ 'jquery' ], file_exists( $js_path ) ? filemtime( $js_path ) : self::VERSION, true );
    }

    public function get_name() { return 'speedtech_product_list'; }
    public function get_title() { return esc_html__( 'Product List (SPT)', 'speedtech' ); }
    public function get_icon() { return 'eicon-products'; }
    public function get_categories() { return [ 'speedtech' ]; }
    public function get_style_depends() { return [ 'spt-product-list-style' ]; }
    public function get_script_depends() { return [ 'spt-product-list-js' ]; }

    private function get_product_categories() {
        $options = ['default' => esc_html__( 'Mặc định (Theo URL)', 'speedtech' ), 'all' => esc_html__( 'All Categories', 'speedtech' )];
        $categories = get_terms( ['taxonomy' => 'product_cat', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true] );
        if ( is_wp_error( $categories ) ) { return $options; }
        foreach ( $categories as $category ) { $options[ $category->slug ] = $category->name; }
        return $options;
    }

    private function get_unique_service_packages() {
        global $wpdb;
        $meta_key = '_spt_service_package_icon';
        $results = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != '' ORDER BY meta_value ASC", $meta_key ) );
        return $results;
    }

    protected function _register_controls() {
        // Section Query
        $this->start_controls_section( 'section_content_query', ['label' => esc_html__( 'Query', 'speedtech' ),'tab' => Controls_Manager::TAB_CONTENT] );
        $this->add_control( 'product_category', ['label' => esc_html__( 'Product Category', 'speedtech' ), 'type' => Controls_Manager::SELECT, 'options' => $this->get_product_categories(), 'default' => 'all'] );
        $this->add_control( 'include_subcategories', ['label' => esc_html__( 'Include Subcategories', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['product_category!' => ['all', 'default']]] );
        $this->add_control( 'posts_per_page', ['label' => esc_html__( 'Products Per Page', 'speedtech' ), 'type' => Controls_Manager::NUMBER, 'default' => 6] );
        $this->add_responsive_control( 'columns', ['label' => esc_html__( 'Columns', 'speedtech' ), 'type' => Controls_Manager::SELECT, 'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'], 'selectors' => ['{{WRAPPER}} .spt-product-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);']] );
        $this->add_control( 'orderby', ['label' => esc_html__( 'Order By', 'speedtech' ), 'type' => Controls_Manager::SELECT, 'default' => 'date', 'options' => ['date'=>'Date','title'=>'Title','ID'=>'ID','rand'=>'Random']] );
        $this->add_control( 'order', ['label' => esc_html__( 'Order', 'speedtech' ), 'type' => Controls_Manager::SELECT, 'default' => 'DESC', 'options' => ['ASC'=>'Ascending','DESC'=>'Descending']] );
        $this->end_controls_section();

        // Section Display Settings
        $this->start_controls_section( 'section_content_settings', ['label' => esc_html__( 'Display Settings', 'speedtech' ), 'tab' => Controls_Manager::TAB_CONTENT] );
        $this->add_control('show_image', ['label' => esc_html__( 'Show Image', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_description', ['label' => esc_html__( 'Show Short Description', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('description_word_limit', ['label' => esc_html__( 'Description Word Limit', 'speedtech' ), 'type' => Controls_Manager::NUMBER, 'default' => 20, 'condition' => ['show_description' => 'yes']]);
        $this->add_control('show_sale_badge', ['label' => esc_html__( 'Show Sale Badge', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_price_title', ['label' => esc_html__( 'Show Price Title', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_add_to_cart', ['label' => esc_html__( 'Show "Add to Cart" Button', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('add_to_cart_button_text', ['label' => esc_html__( '"Add to Cart" Text', 'speedtech' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Thêm vào giỏ', 'speedtech' ), 'condition' => ['show_add_to_cart' => 'yes']]);
        $this->add_control('show_buy_now', ['label' => esc_html__( 'Show "Buy Now" Button', 'speedtech' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('buy_now_button_text', ['label' => esc_html__( '"Buy Now" Text', 'speedtech' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Mua ngay', 'speedtech' ), 'condition' => ['show_buy_now' => 'yes']]);
        
        // MỚI: Tùy chọn ẩn/hiện icon trên thẻ sản phẩm
        $this->add_control(
            'show_service_icon_on_card',
            [
                'label' => esc_html__( 'Show Icon on Product Card', 'speedtech' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'speedtech' ),
                'label_off' => esc_html__( 'Hide', 'speedtech' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );
        $this->end_controls_section();

        // Section Service Packages
        $this->start_controls_section('section_service_packages', ['label' => esc_html__( 'Service Packages & Icons', 'speedtech' ), 'tab' => Controls_Manager::TAB_CONTENT, 'description' => esc_html__( 'Danh sách các gói dịch vụ được tự động tìm thấy từ trường tùy chỉnh `_spt_service_package_icon` của sản phẩm. Gán một biểu tượng cho mỗi gói để hiển thị trong bộ lọc.', 'speedtech' )]);
        $repeater = new Repeater();
        $repeater->add_control('package_value', ['label' => esc_html__( 'Package Value', 'speedtech' ), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => false], 'classes' => 'elementor-control-hidden']);
        $repeater->add_control('package_label', ['label' => esc_html__( 'Package Name', 'speedtech' ), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => false], 'label_block' => true, 'render_type' => 'ui', 'is_editable' => false]);
        $repeater->add_control('package_icon', ['label' => esc_html__( 'Icon', 'speedtech' ), 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'fas fa-check-circle', 'library' => 'solid']]);
        $default_packages = [];
        $available_packages = $this->get_unique_service_packages();
        if ( !empty($available_packages) ) { foreach ($available_packages as $package) { $default_packages[] = ['package_value' => $package, 'package_label' => ucfirst(str_replace(['_', '-'], ' ', $package))]; } }
        $this->add_control('service_package_map', ['label' => esc_html__( 'Assign Icons', 'speedtech' ), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => $default_packages, 'title_field' => '{{{ package_label }}}', 'prevent_empty' => false]);
        $this->end_controls_section();

        // ... Toàn bộ các Section trong TAB_STYLE giữ nguyên ...
        // Bắt đầu copy từ đây
        $this->start_controls_section( 'section_style_card', ['label' => esc_html__( 'Product Card', 'speedtech' ), 'tab' => Controls_Manager::TAB_STYLE] );
        $this->add_responsive_control( 'card_padding', ['label' => esc_html__( 'Padding', 'speedtech' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => ['{{WRAPPER}} .spt-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']] );
        $this->add_group_control( Group_Control_Background::get_type(), ['name' => 'card_background', 'selector' => '{{WRAPPER}} .spt-product-item'] );
        $this->add_group_control( Group_Control_Border::get_type(), ['name' => 'card_border', 'selector' => '{{WRAPPER}} .spt-product-item'] );
        $this->end_controls_section();
        $this->start_controls_section('section_style_content', ['label' => esc_html__( 'Content Styling', 'speedtech' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('title_heading', ['label' => esc_html__( 'Title', 'speedtech' ), 'type' => Controls_Manager::HEADING]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'selector' => '{{WRAPPER}} .spt-product-item .woocommerce-loop-product__title']);
        $this->add_control('title_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-item .woocommerce-loop-product__title a' => 'color: {{VALUE}};']]);
        $this->add_control('description_heading', ['label' => esc_html__( 'Description', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'description_typography', 'selector' => '{{WRAPPER}} .spt-product-item .spt-product-description']);
        $this->add_control('description_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-item .spt-product-description' => 'color: {{VALUE}};']]);
        $this->add_control('price_heading', ['label' => esc_html__( 'Price', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'price_typography', 'selector' => '{{WRAPPER}} .spt-product-item .price, {{WRAPPER}} .spt-product-item del .amount']);
        $this->add_control('price_color', ['label' => esc_html__( 'Regular Price Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-item del .amount' => 'color: {{VALUE}};']]);
        $this->add_control('sale_price_heading', ['label' => esc_html__( 'Sale Price', 'speedtech' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'sale_price_typography', 'selector' => '{{WRAPPER}} .spt-product-item .price ins .amount']);
        $this->add_control('sale_price_color', ['label' => esc_html__( 'Sale Price Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-item .price ins .amount, {{WRAPPER}} .spt-product-item .spt-price-title' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_buttons', ['label' => esc_html__( 'Buttons Styling', 'speedtech' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->start_controls_tabs('button_style_tabs');
        $this->start_controls_tab('tab_add_to_cart_style', ['label' => esc_html__( 'Add to Cart', 'speedtech' )]);
        $this->add_control('atc_text_color', ['label' => esc_html__( 'Text Color', 'speedtech' ),'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-actions .add_to_cart_button' => 'color: {{VALUE}};']]);
        $this->add_control('atc_bg_color', ['label' => esc_html__( 'Background Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-actions .add_to_cart_button' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'atc_border', 'selector' => '{{WRAPPER}} .spt-product-actions .add_to_cart_button']);
        $this->add_responsive_control('atc_padding', ['label' => esc_html__( 'Padding', 'speedtech' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .spt-product-actions .add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_buy_now_style', ['label' => esc_html__( 'Buy Now', 'speedtech' )]);
        $this->add_control('bn_text_color', ['label' => esc_html__( 'Text Color', 'speedtech' ),'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-actions .spt-buy-now-button' => 'color: {{VALUE}};']]);
        $this->add_control('bn_bg_color', ['label' => esc_html__( 'Background Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-product-actions .spt-buy-now-button' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'bn_border', 'selector' => '{{WRAPPER}} .spt-product-actions .spt-buy-now-button']);
        $this->add_responsive_control('bn_padding', ['label' => esc_html__( 'Padding', 'speedtech' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .spt-product-actions .spt-buy-now-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section('section_style_service_icon', ['label' => esc_html__( 'Service Icon Styling', 'speedtech' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('service_icon_size', ['label' => esc_html__( 'Size', 'speedtech' ), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em'], 'range' => ['px' => ['min' => 10, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .spt-service-icon i' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .spt-service-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']]);
        $this->add_control('service_icon_color', ['label' => esc_html__( 'Color', 'speedtech' ), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .spt-service-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .spt-service-icon svg' => 'fill: {{VALUE}};']]);
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) { echo '<div class="elementor-alert elementor-alert-danger">' . esc_html__( 'WooCommerce is not activated.', 'speedtech' ) . '</div>'; return; }
        $settings = $this->get_settings_for_display();
        
        $query_args=['post_type'=>'product','post_status'=>'publish','posts_per_page'=>$settings['posts_per_page'],'orderby'=>$settings['orderby'],'order'=>$settings['order']];
        $tax_query=[];$current_category_slug='';
        if($settings['product_category']==='default'){if(is_product_category()){$term=get_queried_object();if($term instanceof \WP_Term){$current_category_slug=$term->slug;$tax_query[]=['taxonomy'=>'product_cat','field'=>'slug','terms'=>$current_category_slug,'include_children'=>true];}}}elseif($settings['product_category']!=='all'){$current_category_slug=$settings['product_category'];$tax_query[]=['taxonomy'=>'product_cat','field'=>'slug','terms'=>$current_category_slug,'include_children'=>($settings['include_subcategories']==='yes')];}
        if(!empty($tax_query)){$query_args['tax_query']=$tax_query;}
        $products_query=new \WP_Query($query_args);

        $icon_map=[];if(!empty($settings['service_package_map'])){$icon_map=wp_list_pluck($settings['service_package_map'],'package_icon','package_value');}
        $unique_service_packages=$this->get_unique_service_packages();

        ?>
        <div class="spt-product-list-widget">
            <div class="spt-product-list-header">
                <div class="spt-category-title"><?php $category_name=esc_html__('All Products','speedtech');if(!empty($current_category_slug)){$term=get_term_by('slug',$current_category_slug,'product_cat');if($term){$category_name=esc_html($term->name);}}echo '<h3>'.$category_name.'</h3>';?></div>
                <?php if(!empty($unique_service_packages)):?><div class="spt-service-filter"><button data-filter="all" class="active"><?php esc_html_e('All','speedtech');?></button><?php foreach($unique_service_packages as $package):?><button data-filter="<?php echo esc_attr($package);?>" title="<?php echo esc_attr($package);?>"><?php if(isset($icon_map[$package])&&!empty($icon_map[$package]['value'])){Icons_Manager::render_icon($icon_map[$package],['aria-hidden'=>'true']);}else{echo esc_html(ucfirst(str_replace(['_','-'],' ',$package)));}?></button><?php endforeach;?></div><?php endif;?>
            </div>
            <?php if($products_query->have_posts()):?><div class="spt-product-grid woocommerce"><?php while($products_query->have_posts()):$products_query->the_post();global $post,$product;$service_package_value=get_post_meta(get_the_ID(),'_spt_service_package_icon',true);?>
                <div class="spt-product-item product" data-service-package="<?php echo esc_attr($service_package_value);?>">
                    <div class="spt-product-image-wrapper"><?php if('yes'===$settings['show_image']):?><div class="spt-product-image"><?php if('yes'===$settings['show_sale_badge']&&$product->is_on_sale()):echo apply_filters('woocommerce_sale_flash','<span class="onsale">'.esc_html__('Sale!','woocommerce').'</span>',$post,$product);endif;?><a href="<?php the_permalink();?>"><?php echo woocommerce_get_product_thumbnail();?></a></div><?php endif;?></div>
                    <div class="spt-product-content-wrapper">
                        <h2 class="woocommerce-loop-product__title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                        <div class="spt-product-price-row"><?php if('yes'===$settings['show_price_title']):?><span class="spt-price-title"><?php esc_html_e('Giá:','speedtech');?></span><?php endif;?><?php echo $product->get_price_html();?></div>
                        <?php if('yes'===$settings['show_description']):?><div class="spt-product-description"><?php $description=get_the_excerpt();if(!empty($settings['description_word_limit'])){echo wp_trim_words($description,$settings['description_word_limit'],'...');}else{echo $description;}?></div><?php endif;?>
                        <div class="spt-product-actions"><?php if('yes'===$settings['show_add_to_cart']):echo sprintf('<a href="%s" data-quantity="1" class="%s" %s>%s</a>',esc_url($product->add_to_cart_url()),esc_attr(implode(' ',array_filter(['button','product_type_'.$product->get_type(),$product->is_purchasable()&&$product->is_in_stock()?'add_to_cart_button':'',$product->supports('ajax_add_to_cart')?'ajax_add_to_cart':'']))),wc_implode_html_attributes(['data-product_id'=>$product->get_id(),'data-product_sku'=>$product->get_sku(),'aria-label'=>$product->add_to_cart_description(),'rel'=>'nofollow']),esc_html($settings['add_to_cart_button_text']));endif;?><?php if('yes'===$settings['show_buy_now']):$buy_now_url=add_query_arg(['add-to-cart'=>$product->get_id()],wc_get_checkout_url());?><a href="<?php echo esc_url($buy_now_url);?>" class="button spt-buy-now-button"><?php echo esc_html($settings['buy_now_button_text']);?></a><?php endif;?></div>
                    </div>
                    <?php // THAY ĐỔI: Thêm điều kiện kiểm tra tùy chọn ẩn/hiện icon
                    if ( 'yes' === $settings['show_service_icon_on_card'] ) {
                        if ( !empty($service_package_value) && isset( $icon_map[ $service_package_value ] ) && !empty($icon_map[ $service_package_value ]['value']) ) : ?>
                            <div class="spt-service-icon">
                                <?php Icons_Manager::render_icon( $icon_map[ $service_package_value ], [ 'aria-hidden' => 'true' ] ); ?>
                            </div>
                        <?php endif;
                    } ?>
                </div>
            <?php endwhile;?></div><?php wp_reset_postdata();?><?php else:?><p><?php esc_html_e('No products found.','speedtech');?></p><?php endif;?>
        </div>
        <?php
    }
}