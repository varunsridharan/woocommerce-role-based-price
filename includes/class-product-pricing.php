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
		add_filter( 'woocommerce_get_variation_price', array( &$this, 'get_variation_price' ), 99, 4 );
		add_filter( 'woocommerce_get_price_html',array( &$this,'get_price_html' ),1,2); 
        //add_filter( 'woocommerce_variation_prices',array(&$this,'change_variation_price'),10,4);
	}
	
	public function get_product_price($price,$product,$price_meta_key = 'regular_price',$current_user = ''){
		$return = false;
		$product_id = '';
		$opposite_key = 'selling_price';
		if($price_meta_key == 'selling_price'){$opposite_key = 'regular_price';}
 		$product_id = $this->check_product_get_id($product);
		$status = product_rbp_status($product_id,$product);
		if(!$status){ $return = $price; }
        if(empty($current_user)){$current_user = wc_rbp_get_current_user();}
		
		$rbp_price = wc_rbp_price($product_id,$current_user,'all',array(),$product);
        
        if($status){
            if($rbp_price === false){
                $return = $price;
            } else {
                if($price_meta_key == 'all'){$return = $rbp_price[$price_meta_key];}

                if(isset($rbp_price[$price_meta_key]) && isset($rbp_price[$opposite_key])){
                    if($rbp_price[$price_meta_key] == "" && $rbp_price[$opposite_key] == ""){
                        $return = $price;
                    } else if( $rbp_price[$price_meta_key] == ""  && $rbp_price[$opposite_key] != ""){
                        $return = $rbp_price[$opposite_key];
                    } else if($rbp_price[$price_meta_key] != ""  && $rbp_price[$opposite_key] == ""){
                        $return = $rbp_price[$price_meta_key];
                    } else if($rbp_price[$price_meta_key] != ""){
                        $return = $rbp_price[$price_meta_key];
                    }
                } else if(isset($rbp_price[$price_meta_key]) && ! isset($rbp_price[$opposite_key])){
                    if($rbp_price[$price_meta_key] == ""){
                        $return = $price;
                    } else if($rbp_price[$price_meta_key] != ""){
                        $return = $rbp_price[$price_meta_key];
                    }
                } else if(isset($rbp_price[$opposite_key]) && ! isset($rbp_price[$price_meta_key])){
                    if($rbp_price[$opposite_key] == ""){
                        $return = $price;
                    } else if($rbp_price[$opposite_key] != ""){
                        $return = $rbp_price[$opposite_key];
                    }
                }
            }
        }
        
        
	 	$return = apply_filters('wc_rbp_product_price_value',$return,$price,$product_id,$product,$price_meta_key,$current_user);
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
		$return = $price;
		$prices = array();
		$display = array();
        
		foreach ($product->get_children() as $variation_id) {
			$variation = $product->get_child( $variation_id );
			if ( $variation ) {
				$prices[$variation_id] =   $this->get_product_price($price,$variation,$price_meta_key);
			}				 
		}			
		
		if ( $min_or_max == 'min' ) { asort($prices);  }
		else { arsort($prices);}		
		
		if ( $display ) {
			$variation_id = key( $prices );				
			$return = $display[$variation_id];
		} 
		else {$return = current($prices);}
		if(empty($return)){$return = 0;}
		return $return;
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
	 * @return string
	 */
	/* public function get_price_html( $price = '',$product ) {
       // return $price;
		$return = $price;

         if('WC_Product_Variable' == get_class( $product )){
 
			$prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
			$price  = $prices[0] !== $prices[1] ? sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );
			 
			$prices = array($product->get_variation_regular_price('min',true), $product->get_variation_regular_price('max',true));
			sort($prices);
             var_dump($price);
			$saleprice = $prices[0] !== $prices[1] ? sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );  
			 if ( $price !== $saleprice ) {
				$return = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $saleprice, $price ) . $product->get_price_suffix(), $product );
			 } 
						
		}
        
		 /* if('WC_Product_Variable' == get_class( $product )){
 
			$prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
            
            if($prices[0] !== $prices[1]){
               $saleprice = sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price($prices[0]), wc_price($prices[1]));
            } else {
                $saleprice = wc_price( $prices[0] );
            }
            
            $prices = '';
			$prices = array($product->get_variation_regular_price('min',true), $product->get_variation_regular_price('max',true));
			sort($prices);
            
            if($prices[0] !== $prices[1]){
                $price = sprintf(_x( '%1$s&ndash;%2$s','Price range: from-to','woocommerce'), wc_price($prices[0]),wc_price($prices[1]));
            }else {
                $price =  wc_price( $prices[0] );      
            }
			
            
            if ( ($prices[0] == 0) && ($prices[1] == 0) ) { 
                $price = __( 'Free!', 'woocommerce' );
                $return = apply_filters( 'woocommerce_variable_free_price_html', $price, $product );
            } else if ( $price !== $saleprice ) {
                $return = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $saleprice, $price ) . $product->get_price_suffix(), $product ); 
            }  else {
                $return = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
            }
						

		} 
        
       if('WC_Product_Variable' == get_class( $product )){
            $prices = $this->change_variation_price(array(),$product, true ); 

            if ( $product->get_price() === '' || empty( $prices['price'] ) ) {
                $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );
            } else {
                $min_price = current( $prices['price'] );
                $max_price = end( $prices['price'] );
                $price     = $min_price !== $max_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_price ), wc_price( $max_price ) ) : wc_price( $min_price );
                $is_free   = $min_price == 0 && $max_price == 0;

                if ( $product->is_on_sale() ) {
                    $min_regular_price = current( $prices['regular_price'] );
                    $max_regular_price = end( $prices['regular_price'] );
                    $regular_price     = $min_regular_price !== $max_regular_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_regular_price ), wc_price( $max_regular_price ) ) : wc_price( $min_regular_price );
                    $return             = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $regular_price, $price ) . $product->get_price_suffix(), $product );
                } elseif ( $is_free ) {
                    $return = apply_filters( 'woocommerce_variable_free_price_html', __( 'Free!', 'woocommerce' ), $product );
                } else {
                    $return = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
                }
            }
        } 
	 	return $return;
	}*/
    
    public function get_price_html($price = '', $product){
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
        
                
                if ( $prices[0] == 0 && $prices[1] == 0 ) {
                    $price = __( 'Free!', 'woocommerce' );
                    $price = apply_filters( 'woocommerce_variable_free_price_html', $price, $product );
                }else if ( $price !== $saleprice ) {
                    $price = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $saleprice, $price ) . $product->get_price_suffix(), $product );
                } else {
                    $price = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
                }
            }
        
         }
        
        return $price;
    }
    
    public function change_variation_price($pricess,$product,$display){
        
        $prices         = array();
        $regular_prices = array();
        $sale_prices    = array();
        $variation_ids  = $product->get_children( true );

        foreach ( $variation_ids as $variation_id ) {
            if ( $variation = $product->get_child( $variation_id ) ) {

                $price         = $this->get_price( $variation->price, $variation);
                $regular_price = $this->get_regular_price( $variation->regular_price, $variation);
                $sale_price    = $this->get_selling_price( $variation->sale_price, $variation); 


                // If sale price does not equal price, the product is not yet on sale
                if ( $sale_price === $regular_price || $sale_price !== $price ) {
                    $sale_price = $regular_price;
                }
                
                // If we are getting prices for display, we need to account for taxes
                if ( $display ) {
                    if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
                        $price         = '' === $price ? ''         : $variation->get_price_including_tax( 1, $price );
                        $regular_price = '' === $regular_price ? '' : $variation->get_price_including_tax( 1, $regular_price );
                        $sale_price    = '' === $sale_price ? ''    : $variation->get_price_including_tax( 1, $sale_price );
                    } else {
                        $price         = '' === $price ? ''         : $variation->get_price_excluding_tax( 1, $price );
                        $regular_price = '' === $regular_price ? '' : $variation->get_price_excluding_tax( 1, $regular_price );
                        $sale_price    = '' === $sale_price ? ''    : $variation->get_price_excluding_tax( 1, $sale_price );
                    }
                }

                $prices[ $variation_id ]         = $price;
                $regular_prices[ $variation_id ] = $regular_price;
                $sale_prices[ $variation_id ]    = $sale_price;
            }
        } 
        asort( $prices );
        asort( $regular_prices );
        asort( $sale_prices );
      //  var_dump(array( 'price' => $prices, 'regular_price' => $regular_prices, 'sale_price' => $sale_prices , 'd' => $display));
        return array( 'price' => $prices, 'regular_price' => $regular_prices, 'sale_price' => $sale_prices);
        
    }
}
?>