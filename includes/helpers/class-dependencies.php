<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since      3.0
 */

if( ! class_exists('WooCommerce_Role_Based_Price_Dependencies') ) {
    class WooCommerce_Role_Based_Price_Dependencies {

        private static $active_plugins;

        public static function init() {
            self::$active_plugins = (array) get_option('active_plugins', array());
            if( is_multisite() )
                self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
        }

        public static function active_check($pluginToCheck = '') {
            if( ! self::$active_plugins )
                self::init();
            return in_array($pluginToCheck, self::$active_plugins) || array_key_exists($pluginToCheck, self::$active_plugins);
        }
    }
}
/**
 * WC Detection
 */
if( ! function_exists('WooCommerce_Role_Based_Price_Dependencies') ) {
    function WooCommerce_Role_Based_Price_Dependencies($pluginToCheck = 'woocommerce/woocommerce.php') {
        return WooCommerce_Role_Based_Price_Dependencies::active_check($pluginToCheck);
    }
}
?>