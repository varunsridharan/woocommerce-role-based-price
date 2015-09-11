<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'front_end_product_pricing' ) ) :

/**
 * WCPBC_Frontend
 *
 * WooCommerce Role Based Pricing Front-End
 *
 * @class 		front_end_product_pricing
 * @version		1.1
 * @author 		Varun Sridharan <varunsridharan23@gmail.com>
 */
class front_end_product_pricing {

	function __construct(){
        add_action( 'woocommerce_init', array( $this, 'wc_init'));	
	}		 
    
    public function wc_init(){
		add_filter( 'woocommerce_get_regular_price', array( &$this, 'get_regular_price') , 10, 2 );

		add_filter( 'woocommerce_get_sale_price', array( &$this, 'get_selling_price') , 10, 2 );
		
		add_filter('woocommerce_get_price', array( &$this, 'get_price' ), 10, 2 );
		
		add_filter( 'woocommerce_get_variation_regular_price', array( &$this, 'get_variation_regular_price' ), 10, 4 );
					
		add_filter( 'woocommerce_get_variation_price', array( &$this, 'get_variation_price' ), 10, 4 );	 
                
        add_filter('woocommerce_get_price_html',array( &$this,'get_price_html' ),1,2);     
    }
    
    public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
    
    
     public function get_status($id){
       $status = get_post_meta($id,'_enable_role_based_price',true );
       if($status == true && $status == 'true'){
           return true;
       }
       return false;
    }
    
    
	/**
	 * Returns the product's regular price
	 * @return string price
	 */
	public function get_regular_price ( $price, $product, $price_meta_key = 'regular_price' ) {	
		$wcrbp_price = $price;
		
        if ( get_class( $product ) == 'WC_Product_Variation' ) {
            $post_id = $product->variation_id;	
            $meta_key = '_role_based_price';
        } else {
            $post_id = $product->id;  
            $meta_key = '_role_based_price';
        }		
        
        if($this->get_status($post_id)){
            $wcrbp_price_new = get_post_meta( $post_id, $meta_key, true );
            $cRole = $this->get_current_role();
            if(isset($wcrbp_price_new[$cRole])){ $wcrbp_price = $wcrbp_price_new[$cRole][$price_meta_key]; }
        }
            
		return $wcrbp_price;
	}

	
	/**
	 * Returns the product's sale price
	 * @return string price
	 */
	public function get_selling_price ( $price, $product ) {	
		return $this->get_regular_price( $price, $product, 'selling_price');
	}
	

	/**
	 * Returns the product's active price.	 
	 * @return string price
	 */
	public function get_price ($price, $product) {			
		$sale_price = $product->get_sale_price();
		$wcrbp_price = ( $sale_price !== '' && $sale_price > 0 )? $sale_price : $this->get_regular_price( $price, $product );
		return $wcrbp_price; 
	}
	
	/**
	 * Get the min or max variation regular price.
	 * @param  string $min_or_max - min or max
	 * @param  boolean  $display Whether the value is going to be displayed
	 * @return string price
	 */
	public function get_variation_regular_price( $price, $product, $min_or_max, $display, $price_meta_key = 'regular_price') {
		$wcrbp_price = $price;		
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$prices = array();
		$display = array();
		$price_func = 'get_' . $price_meta_key;

		foreach ($product->get_children() as $variation_id) {
			$variation = $product->get_child( $variation_id );
			if ( $variation ) {
				$prices[$variation_id] = $this->$price_func( $price, $variation );							
				$display[$variation_id] = ( $tax_display_mode == 'incl' ) ? $variation->get_price_including_tax( 1, $prices[$variation_id] ) : $variation->get_price_excluding_tax( 1, $prices[$variation_id] );
			}				 
		}			
		
		if ( $min_or_max == 'min' ) { asort($prices); } else { arsort($prices); }		
		if ( $display ) {
			$variation_id = key( $prices );				
			$wcrbp_price = $display[$variation_id];
		} else {			
			$wcrbp_price = current($prices);
		}
		
		return $wcrbp_price;
	}
	
	/**
	 * Get the min or max variation active price.
	 * @param  string $min_or_max - min or max
	 * @param  boolean  $display Whether the value is going to be displayed
	 * @return string price
	 */		
	public function get_variation_price( $price, $product, $min_or_max, $display ) {		
		return $this->get_variation_regular_price( $price, $product, $min_or_max, $display, 'selling_price' );		
	}	
    
    /**
	 * Returns the price in html format.
	 *
	 * @access public
	 * @param string $price (default: '')
	 *                      @return string
	 */
	public function get_price_html( $price = '',$product ) {
        if('WC_Product_Variable' == get_class( $product )){

            $prices = array($product->get_variation_price('min',true), $product->get_variation_price('max',true));
            $price = $prices[0] !== $prices[1] ? sprintf(_x('%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price($prices[0]), wc_price($prices[1])) : wc_price($prices[0]);
            $prices = array($product->get_variation_regular_price('min',true),$product->get_variation_regular_price('max',true));
            sort( $prices );
            $saleprice = $prices[0] !== $prices[1] ? sprintf(_x('%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price($prices[0]), wc_price($prices[1])) : wc_price($prices[0]);  

            if ( $prices[0] == 0 && $prices[1] == 0 ) { $price = __( 'Free!', 'woocommerce' ); $price = $price; } else { $price = $price . $product->get_price_suffix(); } 
        }
		return $price;
	}
 }
endif;

?>