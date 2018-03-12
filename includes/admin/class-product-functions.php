<?php
/**
 * WooCommerce Product Role Based Price Edit Functions
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price_Admin_Product_Functions {

    public function __construct() {
        add_action('wc_rbp_product_save_default', array( $this, 'simple_save_product_price' ), 1, 3);
    }

    public function simple_save_product_price(&$posted_values, &$success, &$error) {
        $post_id = $posted_values['product_id'];

        do_action_ref_array('wc_rbp_product_save_before', array( &$posted_values ));
        if( isset($posted_values['role_based_price']) ) {
            $status = isset($posted_values['enable_role_based_price']) ? TRUE : FALSE;
            wc_rbp_update_role_based_price_status($post_id, $status);
            wc_rbp_update_role_based_price($post_id, $posted_values['role_based_price']);
            clean_post_cache($post_id);
            $success['html'] = '<h3>' . __("Product Price Updated.", WC_RBP_TXT) . '</h3>';
        } else {
            $error['html'] = '<h3>' . __("Price Not Defined. Please Try Again", WC_RBP_TXT) . '</h3>';
        }
        do_action_ref_array('wc_rbp_product_save_after', array( &$posted_values ));
    }
}

return new WooCommerce_Role_Based_Price_Admin_Product_Functions;