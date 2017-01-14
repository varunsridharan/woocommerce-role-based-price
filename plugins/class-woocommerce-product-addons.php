<?php
/**
 * Intergation For Aelia Currency Switcher
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.4
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_WC_Product_Addons {
    public $is_in_function = false;
    public function __construct (){
        
        add_action( 'init', array($this,'admin_init'),1); 
    }
    
    public function admin_init(){
        add_filter( 'woocommerce_role_based_product_price_value',array($this,'change_price'),5,5);
        add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 99, 1 );
    }
    

	/**
	 * add_cart_item function.
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @return void
	 */
	public function add_cart_item( $cart_item ) {
        $get_new_price = new front_end_product_pricing;
        $product_price = $get_new_price->get_price($cart_item['data']->price,$cart_item['data']);
        $addon_price = $this->get_addon_price($cart_item['addons']);
        $total_price = $product_price + $addon_price;
        $cart_item['data']->wc_rbp_wc_product_addon_pprice = $product_price;
        $cart_item['data']->price = $total_price;
        $cart_item['data']->set_price($total_price);
        unset($get_new_price);
		return $cart_item;
	}    
    
    
    
    public function get_addon_price($addons){
        $extra_price = 0;
        foreach($addons as $addon){
            $extra_price = $extra_price + intval($addon['price']);
        }
        return $extra_price;
    }
    
           
    public function change_price($wcrbp_price,$post_id,$price_meta_key,$user_role){ 
        global $woocommerce;
        $wcrbp_price = intval($wcrbp_price); 
        if(! is_product()){   
            if(!get_transient( 'wc_rbp_prod_addon_loop' )){
                   set_transient( 'wc_rbp_prod_addon_loop', 'yes' );
                   $wcrbp_price = $this->get_price_from_product_cart($post_id);
            } else {
                delete_transient('wc_rbp_prod_addon_loop' );
            }
           
        }   
        return $wcrbp_price; 
    }
    
 
    
    public function get_price_from_product_cart($product_id){
        global $woocommerce;
        $wcrbp_price = 0;
        
        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
                //$this->is_in_function = true;
                
                if(isset($cart_item['product_id'])){
                    
                    if($cart_item['product_id'] == $product_id ){
                        $get_new_price = new front_end_product_pricing;
                        $product_price = $get_new_price->get_price($cart_item['data']->price,$cart_item['data']);
                        $addon_price = $this->get_addon_price($cart_item['addons']);
                        $total_price = $product_price + $addon_price; 
                        return $total_price;
                    }
                }

                if(isset($cart_item['variation_id'])){
                    if($cart_item['variation_id'] == $product_id ){
                        $get_new_price = new front_end_product_pricing;
                        $product_price = $get_new_price->get_price($cart_item['data']->price,$cart_item['data']);
                        $addon_price = $this->get_addon_price($cart_item['addons']);
                        $total_price = $product_price + $addon_price; 
                        return $total_price;
                    }
                }
            
                
        } 
        return $wcrbp_price;
    }
    
} 



new WooCommerce_Role_Based_Price_WC_Product_Addons;
?>