<?php 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
class varirable_product_role_based_price{
    private $post_ID = '';
    
	public function init(){ 
         add_action('woocommerce_init',array($this,'wc_init'));
	}
    
    public function wc_init(){
        add_filter('woocommerce_get_variation_sale_price',array($this,'role_based_sale_price'),1,4);
        add_filter('woocommerce_get_variation_regular_price',array($this,'role_based_regular_price'),1,4);
        add_filter('woocommerce_get_variation_price',array($this,'role_based_price_all'),1,4);
    }
 
   /* public function get_ID(){
        global $post;
        
        $id = $post->ID; 
        WC_RBP()->sp_function()->get_db_price($id);
        $this->post_ID = $id;
        
        if(WC_RBP()->sp_function()->get_status($id)){
           
        } 
    }*/
	
	public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
	
	public function role_based_sale_price($price, $product, $min_or_max = 'min', $display = false ){
        $variation_id = get_post_meta( $product->id, '_' . $min_or_max . '_sale_price_variation_id', true );

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
 
        return $price; 
	}
    
    
	public function role_based_regular_price($price, $product, $min_or_max = 'min', $display = false  ){
        $variation_id = get_post_meta( $product->id, '_' . $min_or_max . '_regular_price_variation_id', true );

		if ( ! $variation_id ) {
			$price = false;
		} else {
            WC_RBP()->sp_function()->get_db_price($variation_id);
            $role = $this->get_current_role();
            $price = WC_RBP()->sp_function()->get_selprice($role,'regular_price');
            if(!empty($price)){$price = floatval($price);}
            
			if ( $display && ( $variation = $product->get_child( $variation_id ) ) ) {
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				$price            = $tax_display_mode == 'incl' ? $variation->get_price_including_tax( 1, $price ) : $variation->get_price_excluding_tax( 1, $price );
			}
		}

		return $price; 
	}
    
    
    
    public function role_based_price_all($price,$product,$min_or_max = 'min', $display = false  ){    
        
        $variation_id = get_post_meta( $product->id, '_' . $min_or_max . '_price_variation_id', true );

		if ( $display ) {
			$variation        = $product->get_child( $variation_id );

			if ( $variation ) {
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				$price            = $tax_display_mode == 'incl' ? $variation->get_price_including_tax() : $variation->get_price_excluding_tax();
			} else {
				$price = '';
			}
		} else {
            $price = get_post_meta( $variation_id, '_price', true );
        }
		return $price;
        
   /*   $sale_price = $this->role_based_sale_price('',$product);
        $regular_price = $this->role_based_regular_price('',$product);
        
        if(!empty($sale_price) &&  !empty($regular_price)){
            if($sale_price > $regular_price){
                return $sale_price;
            } else {
                return $regular_price;
            }
        } else if(! empty($regular_price)){
            return $regular_price;
        } else if(! empty($sale_price)){
            return $sale_price;
        } else {
            return $price;
        }*/
        
    }
}

if(!is_admin()){
    $varirable_product_role_based_price = new varirable_product_role_based_price;
    $varirable_product_role_based_price->init();
}
?>