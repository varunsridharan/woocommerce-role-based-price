<?php 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
class varirable_product_role_based_price{
    
	public function init(){ 
        add_action( 'wp', array($this,'get_ID'),1);
        
	}
    
    public function wc_init(){ 
        add_filter('woocommerce_get_sale_price',array($this,'role_based_sale_price'),1,4);
        add_filter('woocommerce_get_regular_price',array($this,'role_based_regular_price'),1,4);
        add_filter('woocommerce_get_price',array($this,'role_based_price_all'),1,4);
        add_filter('woocommerce_get_price_html',array($this,'get_price_html'),1,2);
    }
 
   public function get_ID(){
        global $post; 
        $id = $post->ID;  
        $this->post_ID = $id;
        $product = get_product($id); 
        WC_RBP()->sp_function()->get_db_price($id); 
        #var_dump(WC_RBP()->sp_function()->get_status($id));
        if($product->product_type == 'variable'){ $this->wc_init(); }  
    }
	
	public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
	
	public function role_based_sale_price($price, $product, $min_or_max = 'min', $display = false ){ 
        $variation_id = $product->variation_id;

		if ( ! $variation_id ) {
			$price = false;
		} else {
            WC_RBP()->sp_function()->get_db_price($variation_id);
            $role = $this->get_current_role();
            $price = WC_RBP()->sp_function()->get_selprice($role,'selling_price'); 

			if ( $display ) {
				$variation        = $product->get_child( $variation_id );
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				$price            = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $price ) : $variation->get_price_excluding_tax( 1, $price );
			}
		}
 
        return floatval($price); 
	}
    
    
	public function role_based_regular_price($price, $product, $min_or_max = 'min', $display = false  ){
        $variation_id = $product->variation_id;

		if ( ! $variation_id ) {
			$price = false;
		} else {
            WC_RBP()->sp_function()->get_db_price($variation_id);
            $role = $this->get_current_role();
            $price = WC_RBP()->sp_function()->get_selprice($role,'regular_price');
            if(!empty($price)){$price = floatval($price);}
            $variation = $product->get_child( $variation_id); 
			if ( $display && $variation) {
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				$price            = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $price ) : $variation->get_price_excluding_tax( 1, $price );
			}
		}

		return floatval($price); 
	}
    
    
    
    public function role_based_price_all($price,$product,$min_or_max = 'min', $display = false  ){   
        $sale_price = $this->role_based_sale_price('',$product);
        $regular_price = $this->role_based_regular_price('',$product); 
        
        if(!empty($sale_price)){
            return $sale_price; 
        } else if(! empty($regular_price)){
            return $regular_price;
        } else if(! empty($sale_price)){
            return $sale_price;
        } else {
            return $price;
        } 
        
    }
    
    
    
    
    /**
	 * Returns the price in html format.
	 *
	 * @access public
	 * @param string $price (default: '')
	 * @return string
	 */
	public function get_price_html( $price = '',$product ) {

		// Ensure variation prices are synced with variations
		if ( $product->get_variation_regular_price( 'min' ) === false || $product->get_variation_price( 'min' ) === false || $product->get_variation_price( 'min' ) === '' || $product->get_price() === '' ) {
			$product->variable_product_sync( $product->id );
		}

		// Get the price
		if ( $product->get_price() === '' ) {

			$price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );

		} else {

			// Main price
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			$price  = $prices[0] !== $prices[1] ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );

			// Sale
			$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			sort( $prices );
			$saleprice = $prices[0] !== $prices[1] ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $prices[0] ), wc_price( $prices[1] ) ) : wc_price( $prices[0] );  
			#if ( $price !== $saleprice ) {
			#	$price = apply_filters( 'woocommerce_variable_sale_price_html', $product->get_price_html_from_to( $saleprice, $price ) . $product->get_price_suffix(), $product );
			#} else {
			#	
			#}
            
            if ( $prices[0] == 0 && $prices[1] == 0 ) {
					$price = __( 'Free!', 'woocommerce' );
					$price = apply_filters( 'woocommerce_variable_free_price_html', $price, $product );
				} else {
					$price = apply_filters( 'woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product );
				}
		}

		return $price;
	}
}

if(!is_admin()){
    $varirable_product_role_based_price = new varirable_product_role_based_price;
    $varirable_product_role_based_price->init();
}
?>