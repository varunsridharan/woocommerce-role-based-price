<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    Role Based Price For WooCommerce
 * @subpackage Role Based Price For WooCommerce/core
 * @since      3.0
 */
class WooCommerce_Role_Based_Price_Deactivator {
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {

    }

    public static function dependency_deactivate() {
        if( is_plugin_active(WC_RBP_FILE) ) {
            add_action('update_option_active_plugins', array( __CLASS__, 'deactivate_dependent' ));
        }
    }

    public static function deactivate_dependent() {
        delete_transient('_welcome_redirect_wcrbp');
        deactivate_plugins(WC_RBP_FILE);
    }

}