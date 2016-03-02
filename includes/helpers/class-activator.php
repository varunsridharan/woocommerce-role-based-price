<?php 
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since 3.0
 */
class WooCommerce_Role_Based_Price_Activator {
	
    public function __construct() {
    }
	
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once(WC_RBP_INC.'helpers/class-version-check.php');
		require_once(WC_RBP_INC.'helpers/class-dependencies.php');
		
		if(WooCommerce_Role_Based_Price_Dependencies(WC_RBP_DEPEN)){
			WooCommerce_Role_Based_Price_Version_Check::activation_check('3.7');	
		} else {
			if ( is_plugin_active(WC_RBP_FILE) ) { deactivate_plugins(WC_RBP_FILE);} 
			wp_die(wc_rbp_dependency_message());
		}
	} 
 
}