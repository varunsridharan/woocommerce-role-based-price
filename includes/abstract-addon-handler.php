<?php
/**
 *
 * Addons Handler
 *
 * @link       https://codecanyon.net/item/advanced-product-reviews-for-woocommerce/15385857
 * @package    APRWC
 * @subpackage APRWC/FrontEnd
 * @since      2.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price_Addon_Handler {

    public function __construct() {
        add_filter('wc_rbp_addon_sections', array( $this, 'register_section' ));
        add_filter('wc_rbp_addon_fields', array( $this, 'register_fields' ));

        add_action('wp_enqueue_scripts', array( $this, 'frontend_style' ));
        add_action('wp_enqueue_scripts', array( $this, 'frontend_script' ));

        if( is_admin() ) {
            add_action('wc_rbp_admin_styles', array( $this, 'admin_style' ));
            add_action('wc_rbp_admin_scripts', array( $this, 'admin_script' ));
        }

        add_action('wc_rbp_loaded', array( $this, 'init_class' ));
    }

    public function register_section($settings_section) {
        return $settings_section;
    }

    public function register_fields($settings_fields) {
        return $settings_fields;
    }

    public function init_class() {
    }

    public function admin_style($screen = '') {
    }

    public function admin_script($screen = '') {
    }

    public function frontend_style() {
    }

    public function frontend_script() {
    }

    public function addon_path($file = __DIR__) {
        return plugin_dir_path($file);
    }

    public function addon_url($file = __FILE__) {
        return plugin_dir_url($file);
    }
}