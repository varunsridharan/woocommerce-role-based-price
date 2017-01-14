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
		if(! WC_RBP()->sp_function()->is_aeliacs_active()) {
			add_filter( 'woocommerce_get_regular_price', array( &$this, 'get_regular_price') , 99, 2 );
			add_filter( 'woocommerce_get_sale_price', array( &$this, 'get_selling_price') , 99, 2 );
			add_filter( 'woocommerce_get_price', array( &$this, 'get_price' ), 99, 2 );
			add_filter( 'woocommerce_get_variation_regular_price', array( &$this, 'get_variation_regular_price' ), 99, 4 );
			add_filter( 'woocommerce_get_variation_price', array( &$this, 'get_variation_price' ), 99, 4 );	 
		}

		add_filter( 'woocommerce_get_price_html',array( &$this,'get_price_html' ),1,2);   

		add_filter( 'init',array(&$this,'check_remove_add_to_cart'),99);
        
    }
    
    public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
        if($user_role == null){
            return 'logedout';
        }
		return $user_role;
	}
    
    public function check_remove_add_to_cart(){
        $current_role = $this->get_current_role();
        $resticted_role = WC_RBP()->get_option(rbp_key.'hide_cart_button_role');
        $products_to = WC_RBP()->get_option(rbp_key.'products_hide_settings');
        $variable_status = WC_RBP()->get_option(rbp_key.'products_variable_settings'); 
        if(empty($resticted_role)){return;}
        if(empty($products_to)){return;}
        if(empty($variable_status)){return;}
        if(!empty($resticted_role)){
            if(in_array($current_role,$resticted_role)){
                add_filter( 'woocommerce_loop_add_to_cart_link',array(&$this,'remove_add_to_cart_link'),99);
                foreach($products_to as $pto){
                    if($pto == 'simple'){
                        remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
                    }
                    if($pto == 'variable'){
                        if($variable_status == 'hide'){
                            remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );      
                        } else if ($variable_status == 'show') {
                            remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button',20);
                        }
                    }
                }
            }       
        }

    }
    
    public function remove_add_to_cart_link($link){ return ''; }
    
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
        if(defined('WC_RBP_SHORTCODE_PRODUCT_BASE_PRICING')){ return $price; }
        $opposit_key = 'selling_price';
		$wcrbp_price = $price;
		$cRole = $this->get_current_role();

		if($price_meta_key == 'selling_price'){$opposit_key = 'regular_price';}
		
        if ( get_class( $product ) == 'WC_Product_Variation' ) {
            $post_id = $product->variation_id;	
            $meta_key = '_role_based_price';
        } else {
            $post_id = $product->id;  
            $meta_key = '_role_based_price';
        }		
         
        if($this->get_status($post_id)){
            $wcrbp_price_new = get_post_meta( $post_id, $meta_key, true );
			
			if(isset($wcrbp_price_new[$cRole])){
				$wcrbp_price = $this->get_role_prices($wcrbp_price_new,$cRole,$price_meta_key,$opposit_key);
			}
			
			
			
			//if(isset($wcrbp_price_new[$cRole])) { 
			//	if(isset($wcrbp_price_new[$cRole][$opposit_key]) && $wcrbp_price_new[$cRole][$opposit_key] == 0){
			//		$wcrbp_price = 0;
			//	} else if(empty($wcrbp_price_new[$cRole][$opposit_key]) && empty($wcrbp_price_new[$cRole][$price_meta_key])){
			//		$wcrbp_price = $price;
			//	} else if(! empty($wcrbp_price_new[$cRole][$price_meta_key]) || empty($wcrbp_price_new[$cRole][$opposit_key])){
			//		$wcrbp_price = $wcrbp_price_new[$cRole][$price_meta_key];
			//	} /*else if(empty($wcrbp_price_new[$cRole][$price_meta_key]) || ! empty($wcrbp_price_new[$cRole][$opposit_key])){
			//		$wcrbp_price = $wcrbp_price_new[$cRole][$opposit_key];
			//		//$wcrbp_price = $price;
			//	}*/ else {
			//		$wcrbp_price = $price;
			//	}
			//}
        }
        
        $wcrbp_price = apply_filters('woocommerce_role_based_product_price_value',$wcrbp_price,$post_id,$price_meta_key,$cRole);
        $wcrbp_price = wc_format_decimal($wcrbp_price);
		
		//if(!empty($wcrbp_price)){
		//	$wcrbp_price = $product->get_price_including_tax(1,$wcrbp_price);
		//}
		
		return $wcrbp_price;
	}

	public function get_role_prices($price,$role,$key,$opKey){
		$regular_price = WC_RBP()->get_allowed_price('regular');
		$selling_price = WC_RBP()->get_allowed_price('sale');		
		$wcrbp_price = 0;
		
		if($regular_price && $key == 'regular_price'){
			$wcrbp_price = $this->get_product_role_price($price,$role,$key,$opKey);
		}
		
		if($selling_price && $key == 'selling_price'){
			$wcrbp_price = $this->get_product_role_price($price,$role,$key,$opKey);
		} 

		return $wcrbp_price;
	}
	
	private function get_product_role_price($price,$role,$key,$opKey){
		$wcrbp_price = null;
		
		/* if(isset($price[$role][$key]) && $price[$role][$key] == 0 ){
			$wcrbp_price = 0;
		} else */ 
		if(isset($price[$role][$key]) && ! empty($price[$role][$key])){
			$wcrbp_price = $price[$role][$key];
		} elseif(isset($price[$role][$opKey]) && ! empty($price[$role][$opKey])){
			$wcrbp_price = $price[$role][$opKey];
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
        $wcrbp_price = wc_format_decimal($wcrbp_price); 
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
        $current_role = $this->get_current_role();
        $resticted_role = WC_RBP()->get_option(rbp_key.'hide_price_role');  

        if(!empty($resticted_role)){

            if(in_array($current_role,$resticted_role)){
                $price_notice = WC_RBP()->get_option(rbp_key.'replace_currency_symbol');
				$price_notice = apply_filters('woocommerce_role_based_price_hide_text',$price_notice,$current_role);
                if(!empty($price_notice)){
                    $symbol = get_woocommerce_currency_symbol() ; 
                    $price_notice = str_replace('[curr]',$symbol,$price_notice);
                    return $price_notice;
				}
                return '';
            } 
        } 
         
         if('WC_Product_Variable' == get_class( $product )){
        
        	// Ensure variation prices are synced with variations
            if($product->get_variation_regular_price( 'min' ) === false || 
              $product->get_variation_price( 'min' ) === false || 
              $product->get_variation_price( 'min' ) === '' || 
              $product->get_price() === '' ) {
                $product->variable_product_sync( $product->id );
            }

		    // Get the price
            if ( $product->get_price() === '' ) {
                $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );

            } else {
				
                // Main price
                $prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
                $price  = $prices[0] !== $prices[1] ? sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );

                // Sale
                $prices = array($product->get_variation_regular_price('min',true), $product->get_variation_regular_price('max',true));
                sort($prices);
                $saleprice = $prices[0] !== $prices[1] ? sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );  
                 if ( $price !== $saleprice ) {
                    $price = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $saleprice, $price ) . $product->get_price_suffix(), $product );
                 } else {
                 }

                if ( $prices[0] == 0 && $prices[1] == 0 ) {
                    $price = __( 'Free!', 'woocommerce' );
                    $price = apply_filters( 'woocommerce_variable_free_price_html', $price, $product );
                } else {
                    $price = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
                }
            }
         }
		
		return $price;
        } 
    
 }
endif;

?>