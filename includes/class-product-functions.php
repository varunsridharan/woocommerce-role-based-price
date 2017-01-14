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

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_Simple_Product_Functions {
    
    private static $_instance = null;
    private static $db_prices = null;
    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }   
     
	/**
     * Load The Class
     * @since 0.1
	 */
	public function __construct(){
          add_shortcode( 'wc_rbp', array($this,'shortcodehandler' ));
 	}
     
	function is_aeliacs_active() {
	  return isset($GLOBALS['woocommerce-aelia-currencyswitcher']) && is_object($GLOBALS['woocommerce-aelia-currencyswitcher']);
	}	
    
    public function shortcodehandler($attrs){
        $vars = shortcode_atts( array( 
            'id' => null,
            'price' => 'regular_price',
            'role' => 'current',
        ), $attrs, 'wc_rbp' );
        
        if($vars['id'] == null){
			global $product;
			if(!isset($product->id)){
				return __('Invalid Product ID Given',WC_RBP_TXT);
			} 
			$vars['id'] = $product->id;
		}
        if($vars['role'] == null){return __('Invalid User Role Given',WC_RBP_TXT);}
        
        if($vars['price'] == 'product_regular_price' || $vars['price'] == 'product_selling_price'){
            return self::get_base_product_price($vars['id'],$vars['price']);
        }
        
        if($vars['price'] != 'regular_price' && $vars['price'] != 'selling_price'){
            return __('Invalid Price Type Given',WC_RBP_TXT);
        } 
        
        $product_status = self::get_status($vars['id']);
        if($product_status){
            if($vars['role'] == 'current'){ $vars['role'] = WC_RBP()->current_role();}
            $price = self::get_db_price($vars['id']);
           return self::get_selprice($vars['role'],$vars['price']);
        }
        return '';
    }
 
        
    public function get_base_product_price($id,$price){
        if(!defined('WC_RBP_SHORTCODE_PRODUCT_BASE_PRICING')){
            define('WC_RBP_SHORTCODE_PRODUCT_BASE_PRICING',true);
        }
        
        $product = new WC_Product($id);

        if($price == 'product_regular_price'){
            return $product->get_regular_price();
        }
         if($price == 'product_selling_price'){
            return $product->get_sale_price();
        }
    }
    
    public function get_status($id){
       $status = get_post_meta($id,'_enable_role_based_price',true );

       if($status == true && $status == 'true'){
           return true;
       }
       return false;
    }
    
    public function get_db_price($id){ 
        self::$db_prices = get_post_meta($id,'_role_based_price',true );  
        if(is_array(self::$db_prices)){
            return self::$db_prices;
        }
        return false;
    }
    
    public function get_selprice($role,$price = 'all'){
       
        $role_price = self::get_role_price($role);
        
        if($role_price){
            if($price == 'all'){ return $role_price; }
            if(isset($role_price[$price])){ return $role_price[$price]; }
        } 
        
        return false;
    }
    
    private function get_role_price($role){
        
        if(isset(self::$db_prices[$role]) && is_array(self::$db_prices[$role])){
            return self::$db_prices[$role];
        }
        return false;
    }
  
   
}


?>