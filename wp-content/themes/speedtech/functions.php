<?php
/**
 * Theme functions and definitions
 *
 * @package Speed Tech
 */


 /* Mercyship Elementor Widget */
require_once 'speedtech-widget/widgets.php';
/* END Mercyship Elementor Widget */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

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

}
add_action( 'wp_enqueue_scripts', 'speed_tech_enqueue_styles' );
