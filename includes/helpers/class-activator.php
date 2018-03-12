<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since      3.0
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
        require_once( WC_RBP_INC . 'helpers/class-version-check.php' );
        require_once( WC_RBP_INC . 'helpers/class-dependencies.php' );

        if( WooCommerce_Role_Based_Price_Dependencies(WC_RBP_DEPEN) ) {
            WooCommerce_Role_Based_Price_Version_Check::activation_check('3.7');

            $message = '<h3> <center> ' . __("Thank you for installing <strong>WooCommerce Role Based Price</strong> : <strong>Version 3.0 </strong>", WC_RBP_TXT) . '</center> </h3>';
            $message .= '<p>' . __("We have worked entire 1 year to improve our plugin to best of our ability and we hope you will enjoy working with it. We are always open for your sugestions and feature requests", WC_RBP_TXT) . '</p>';

            $message .= '</hr>';
            $message .= '<p>' . __("If you have installed <strong>WPRB</strong> for the 1st time or upgrading from <strong> Version 2.8.7</strong> then you will need to update its' settings once again or this plugin will not function properly. ", WC_RBP_TXT);

            $url     = admin_url('admin.php?page=woocommerce-role-based-price-settings');
            $message .= '<a href="' . $url . '" class="button button-primary">' . __("Click Here to update the settings", WC_RBP_TXT) . '</a> </p>';

            wc_rbp_admin_update($message, 1, 'activate_message', array(), array( 'wraper' => FALSE, 'times' => 1 ));

            set_transient('_welcome_redirect_wcrbp', TRUE, 60);

        } else {
            if( is_plugin_active(WC_RBP_FILE) ) {
                deactivate_plugins(WC_RBP_FILE);
            }
            wp_die(wc_rbp_dependency_message());
        }
    }

}