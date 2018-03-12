<?php
/**
 * Simple Product Role Based Price Settings
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.0
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if( ! defined('WPINC') ) {
    exit;
}

class WooCommerce_Role_Based_Price_Shortcode_Handler {

    private static $_instance = NULL;
    private static $db_prices = NULL;

    public function __construct() {
        add_shortcode('wc_rbp', array( $this, 'shortcodehandler' ));
    }

    function is_aeliacs_active() {
        return isset($GLOBALS['woocommerce-aelia-currencyswitcher']) && is_object($GLOBALS['woocommerce-aelia-currencyswitcher']);
    }

    public function shortcodehandler($attrs) {
        $vars = shortcode_atts(array(
            'id'    => NULL,
            'price' => 'regular_price',
            'role'  => 'current',
        ), $attrs, 'wc_rbp');

        if( $vars['id'] == NULL ) {
            global $product;


            $id = '';
            if( wc_rbp_is_wc_v('>=', '3.0.1') ) {
                $id = $product->get_id();
            } else {
                $product->id;
            }

            if( ! isset($id) ) {
                return __('Invalid Product ID Given', WC_RBP_TXT);
            }
            $vars['id'] = $id;
        }

        if( $vars['role'] == NULL ) {
            return __('Invalid User Role Given', WC_RBP_TXT);
        }

        if( $vars['price'] == 'product_regular_price' || $vars['price'] == 'product_selling_price' ) {
            return self::get_base_product_price($vars['id'], $vars['price']);
        }

        if( $vars['price'] != 'regular_price' && $vars['price'] != 'selling_price' ) {
            return __('Invalid Price Type Given', WC_RBP_TXT);
        }

        $product_status = product_rbp_status($vars['id']);
        $this->rbpPP    = '';
        if( $product_status ) {
            $this->rbpPP = new WooCommerce_Role_Based_Price_Product_Pricing;

            if( $vars['role'] == 'current' ) {
                $vars['role'] = wc_rbp_get_current_user();
            }

            return self::get_selprice($vars['role'], $vars['price'], $vars['id']);
        }
        return '';
    }


    public function get_base_product_price($id, $price) {
        if( ! defined('WC_RBP_SHORTCODE_PRODUCT_BASE_PRICING') ) {
            define('WC_RBP_SHORTCODE_PRODUCT_BASE_PRICING', TRUE);
        }

        $product = new WC_Product($id);
        if( $price == 'product_regular_price' ) {
            return $product->get_regular_price();
        }
        if( $price == 'product_selling_price' ) {
            return $product->get_sale_price();
        }
    }

    public function get_selprice($role, $price = 'all', $product_id) {
        $product = wc_get_product($product_id);
        $p       = $this->rbpPP->get_product_price('', $product, $price, $role);
        return wc_price($p);
    }
}