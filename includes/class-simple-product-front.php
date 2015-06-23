<?php 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
class simple_product_role_based_price{
    private $post_ID = '';
    
	public function init(){ 
        add_action( 'wp', array($this,'get_ID'),1);
        
	}
    
    public function wc_init(){
        # Single Product Page Price Load
        #add_action('woocommerce_single_product_summary',array($this,'get_ID'),1);
        
        # Shop Loop Page Price Load
        #add_action('woocommerce_after_shop_loop_item_title',array($this,'get_ID'),1);
        
        add_filter('woocommerce_get_sale_price',array($this,'role_based_sale_price'),1,2);
        add_filter('woocommerce_get_regular_price',array($this,'role_based_regular_price'),1,2);
        add_filter('woocommerce_get_price',array($this,'role_based_price_all'),1,2);
    }
 
    public function get_ID(){
        global $post; 
        $id = $post->ID;  
        $this->post_ID = $id;
        
        $product = get_product($id); 
        WC_RBP()->sp_function()->get_db_price($id);

        if($product->product_type == 'simple'  &&  WC_RBP()->sp_function()->get_status($id)){
            $this->wc_init(); 
        } 
    }
	
	public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
	
	public function role_based_sale_price($price,$product){
		$post_id = $product->id;
		$role = $this->get_current_role();  
		$price_new = WC_RBP()->sp_function()->get_selprice($role,'selling_price');
        if(!empty($price_new)){return floatval($price_new);}
        return $price_new;
		
	}
	public function role_based_regular_price($price,$product){
		$post_id = $product->id;
		$role = $this->get_current_role();  
		$price_new = WC_RBP()->sp_function()->get_selprice($role,'regular_price');
        if(!empty($price_new)){return floatval($price_new);}
        return $price_new;
	}
    
    public function role_based_price_all($price,$product){
        $sale_price = $this->role_based_sale_price('',$product);
        $regular_price = $this->role_based_regular_price('',$product);
        
        if(! empty($sale_price) ){
            return $sale_price;
        } else if(! empty($regular_price)){
            return $regular_price;
        } else {
            return $price;
        }
        
    }
}

$simple_role_based_price = new simple_product_role_based_price;
    $simple_role_based_price->init();
?>