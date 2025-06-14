<?php

// use ElementorPro\Plugin;

class SpeedTech_Elementor_Widgets
{
    protected static $instance = null;

    public static function get_instance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct()
    {
        require_once 'widget_popup_account.php';
        require_once 'widget_cart_page.php';
        require_once 'widget_checkout_page.php';

       
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    public function register_widgets()
    {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\SpeedTech_Widget_Popup_Account());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\SpeedTech_Widget_Cart_Page());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\SpeedTech_Widget_Checkout_Page());
    }
}

function add_elementor_widget_categories($elements_manager)
{
    $elements_manager->add_category(
        'speedtech',
        [
            'title' => __('Speed Tech', 'speedtech_widget'),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');

add_action('init', 'mercyships_elementor_init');
function mercyships_elementor_init()
{
    SpeedTech_Elementor_Widgets::get_instance();
}