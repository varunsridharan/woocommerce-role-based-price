<?php
/**
 * Plugin Name: WooCommerce Role Based Product Price
 * Plugin URI: http://www.woothemes.com/woocommerce/
 * Description: Product Price Based On User Role
 * Version: 0.1
 * Author: varunms
 * Author URI: http://woothemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/technofreaky/WooCommerce-Role-Based-Price
 * Requires at least: 3.8
 * Tested up to: 4.0
 * WC requires at least: 1.5
 * WC tested up to: 2.3.5
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class role_based_price{
    
	public function init(){
		add_filter('woocommerce_product_data_tabs',array($this,'role_based_price_link'));
		add_action('woocommerce_product_data_panels',array($this,'role_based_price_form'));
		add_action('woocommerce_process_product_meta_simple',array($this,'save_role_based_price'));
        add_action('woocommerce_init',array($this,'wc_init'));
	}
    
    public function wc_init(){
        add_filter('woocommerce_get_sale_price',array($this,'role_based_sale_price'),1,2);
		add_filter('woocommerce_get_regular_price',array($this,'role_based_regular_price'),1,2);
		add_filter('woocommerce_get_price',array($this,'role_based_price_all'),1,2);

    }
	public function role_based_price_link($array){
		$array['role_price'] = array('label' => 'Role Based Price' , 'target' => 'role_based_price_container','class'=>array());
		return $array;
	}
	
	private function get_roles(){
		$user_roles = get_editable_roles();
		return $user_roles;
	}
	public function role_based_price_form(){
		
		echo '<div class="panel woocommerce_options_panel" id="role_based_price_container" style="display: none;">';
		foreach($this->get_roles() as $key => $val){
            if($key == 'administrator'){continue;}
			echo '<div class="options_group"> ';
				echo '<h3>'.$val['name'].'</h3>';
				woocommerce_wp_text_input( array( 
					'id' => '_regular_price_'.$key,
					'name'=>'role_based_price['.$key.'][regular_price]',
					'label' => __( 'Regular Price', 'woocommerce' ), 	
					'desc_tip' => 'false'
				));
				woocommerce_wp_text_input( array( 
					'id' => '_selling_price_'.$key,
					'name'=>'role_based_price['.$key.'][selling_price]',
					'label' => __( 'Selling Price', 'woocommerce' ), 	
					'desc_tip' => 'false'
				));
			echo '	</div>';
		}
	
		
		echo '</div>';
	}
	
	public function save_role_based_price($post_id){ 
		$prices = $_POST['role_based_price'];
		foreach($prices as $key => $val){
			update_post_meta($post_id,'_selling_price_'.$key, wc_clean($val['selling_price']) );
			update_post_meta($post_id,'_regular_price_'.$key, wc_clean($val['regular_price']) );
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
		$price_new = get_post_meta($post_id,'_selling_price_'.$role);
        if(!empty($price_new)){return floatval($price_new[0]);}
        return $price;
		
	}
	public function role_based_regular_price($price,$product){
		$post_id = $product->id;
		$role = $this->get_current_role();  
		$price_new = get_post_meta($post_id,'_regular_price_'.$role);
        if(!empty($price_new)){return floatval($price_new[0]);}
        return $price;
	}
    
    public function role_based_price_all($price,$product){
        $sale_price = $this->role_based_sale_price('',$product);
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
        }
        
    }
}

$role_based_price = new role_based_price;
$role_based_price->init();
?>