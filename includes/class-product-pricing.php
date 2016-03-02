<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Product_Pricing {
    
    public function __construct() {
    	add_action( 'woocommerce_init', array( $this, 'wc_init'));
    }
	
	
	public function wc_init(){
		add_filter( 'woocommerce_get_regular_price', array( &$this, 'get_regular_price') , 99, 2 );
		add_filter( 'woocommerce_get_sale_price', array( &$this, 'get_selling_price') , 99, 2 );
		add_filter( 'woocommerce_get_price', array( &$this, 'get_price' ), 99, 2 );
		add_filter( 'woocommerce_get_variation_regular_price', array( &$this, 'get_variation_regular_price' ), 99, 4 );
	}
	
	public function get_product_price($price,$product,$price_meta_key = 'regular_price'){
		$return = $price;
		$product_id = '';
		$opposite_key = 'selling_price';
		if($price_meta_key == 'selling_price'){$opposite_key = 'regular_price';}
 		$product_id = $this->check_product_get_id($product);
		$status = product_rbp_status($product_id,$product);
		if(!$status){ $return = $price; }
		$current_user = wc_rbp_get_current_user();
		$rbp_price = wc_rbp_price($product_id,$current_user,'all',$product);
		$return = $rbp_price[$price_meta_key];
		
		
		if(isset($rbp_price[$price_meta_key]) && isset($rbp_price[$opposite_key])){
			if($rbp_price[$price_meta_key] == "" && $rbp_price[$opposite_key] == ""){
				$return = $price;
			} else if( $rbp_price[$price_meta_key] == ""  && $rbp_price[$opposite_key] != ""){
				$return = $rbp_price[$opposite_key];
			} else if($rbp_price[$price_meta_key] != ""  && $rbp_price[$opposite_key] == ""){
				$return = $rbp_price[$price_meta_key];
			}
		}
		 
	 	$return = apply_filters('wc_rbp_product_price_value',$return,$orginal_price,$product_id,$product,$price_meta_key);
		$return = wc_format_decimal($return);
		return $return;
	}
	
	public function check_product_get_id($product){
		$product_id = 0;

		if($this->is_simple_product($product)){ 
			$product_id = $product->id; 
		} else if($this->is_variable_product($product)){
			$product_id = $product->id;
		} else if($this->is_variation_product($product)){
			$product_id = $product->variation_id;
		}
		

		return $product_id;
	}
	
	
	private function get_product_class($product){
		$class = get_class($product);
		$class = str_replace('_RBP','',$class);
		return $class;
	}
	
	private function is_simple_product($product){
		$class = $this->get_product_class($product);
		if($class == 'WC_Product_Simple'){return true;}
		return false;
	}
	
	private function is_variable_product($product){
		$class = $this->get_product_class($product);
		if($class == 'WC_Product_Variable'){return true;}
		return false;
	}	
	
	private function is_variation_product($product){
		$class = $this->get_product_class($product);
		if($class == 'WC_Product_Variation'){return true;}
		return false;
	}
	
	
	
	/**
	 * Returns the product's regular price
	 * @return string price
	 */
	public function get_regular_price($price, $product){
		$price = $this->get_product_price($price,$product);
		return $price;
	}
	
	
	/**
	 * Returns the product's sale price
	 * @return string price
	 */
	public function get_selling_price($price, $product){
		$price = $this->get_product_price($price,$product,'selling_price');
		return $price;
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
		var_dump(func_get_args());
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
}
?>