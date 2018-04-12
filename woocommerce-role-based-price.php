<?php
/**
 * WooCommerce Role Based Price Main File
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since             3.0
 * @package           WooCommerce Role Based Price
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Role Based Price
 * Plugin URI:        https://wordpress.org/plugins/woocommerce-role-based-price/
 * Description:       Sell product in different price for different user role based on your settings.
 * Version:           3.3.2
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-role-based-price
 * Domain Path:       /languages/
 */

if( ! defined('WPINC') ) {
    die;
}

define('WC_RBP_FILE', plugin_basename(__FILE__));
define('WC_RBP_PATH', plugin_dir_path(__FILE__)); # Plugin DIR
define('WC_RBP_INC', WC_RBP_PATH . 'includes/'); # Plugin INC Folder
define('WC_RBP_DEPEN', 'woocommerce/woocommerce.php');
define('WC_RBP_VARIABLE_VERSION', '3.0.0.2');
register_activation_hook(__FILE__, 'wc_rbp_activate_plugin_name');
register_deactivation_hook(__FILE__, 'wc_rbp_deactivate_plugin_name');
register_deactivation_hook(WC_RBP_DEPEN, 'wc_rbp_dependency_plugin_deactivate');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function wc_rbp_activate_plugin_name() {
    require_once( WC_RBP_INC . 'helpers/class-activator.php' );
    woocommerce_role_based_price_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_rbp_deactivate_plugin_name() {
    require_once( WC_RBP_INC . 'helpers/class-deactivator.php' );
    woocommerce_role_based_price_Deactivator::deactivate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_rbp_dependency_plugin_deactivate() {
    require_once( WC_RBP_INC . 'helpers/class-deactivator.php' );
    woocommerce_role_based_price_Deactivator::dependency_deactivate();
}

require_once( WC_RBP_INC . 'functions.php' );
require_once( plugin_dir_path(__FILE__) . 'bootstrap.php' );

if( ! function_exists('woocommerce_role_based_price') ) {
    function woocommerce_role_based_price() {
        return woocommerce_role_based_price::get_instance();
    }
}
woocommerce_role_based_price();