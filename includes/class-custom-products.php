<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

if(class_exists('WC_Product_Simple')){
	class WC_Product_Simple_RBP extends WC_Product_Simple {
		public function __construct($product, $args = array()) { 
			parent::__construct($product,$args);
			$this->wc_rbp = wc_rbp_get_product_price($product->ID);
			$status = wc_rbp_product_status($product->ID);
			$this->wc_rbp_status = $status;
			do_action_ref_array('wc_rbp_custom_simple_class',array(&$this));
		}
	}
}

if(class_exists('WC_Product_Variation')){
	class WC_Product_Variation_RBP extends WC_Product_Variation{
		public function __construct( $variation, $args = array() ) { 
			parent::__construct( $variation, $args);
			$this->wc_rbp = wc_rbp_get_product_price($variation->ID);
			$status = wc_rbp_product_status($variation->ID);
			$this->wc_rbp_status = $status;
			do_action_ref_array('wc_rbp_custom_variation_class',array(&$this));
			//if($status){$this->wc_rbp_status = true; }
		}
	}
}