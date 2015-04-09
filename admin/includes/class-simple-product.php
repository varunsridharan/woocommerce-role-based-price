<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      0.1
 *
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_Simple_Product_Admin {
    
	/**
     * Load The Class
     * @since 0.1
	 */
	public function __construct(){
		add_filter('woocommerce_product_data_tabs',array($this,'role_based_price_link'));
		add_action('woocommerce_product_data_panels',array($this,'role_based_price_form'));
        add_action('woocommerce_product_after_variable_attributes',array($this,'role_based_price_form'));
		add_action('woocommerce_process_product_meta_simple',array($this,'save_role_based_price'));
        add_action('woocommerce_init',array($this,'wc_init'));
	}
    
    /**
     * Load Other Works After WooCommerce Loaded
     * @since 0.1
     */
    public function wc_init(){
        add_filter('woocommerce_get_sale_price',array($this,'role_based_sale_price'),1,2);
		add_filter('woocommerce_get_regular_price',array($this,'role_based_regular_price'),1,2);
		add_filter('woocommerce_get_price',array($this,'role_based_price_all'),1,2);
    }
    
	/**
	 * Adds Menu Link In Product Edit
	 * @since 0.1
	 * @filter_use woocommerce_product_data_tabs
	 */
	public function role_based_price_link($array){
		$array['role_price'] = array('label' => 'Role Based Price' , 'target' => 'role_based_price_container','class'=>array('hide_if_variable'));
		return $array;
	} 
	
	/**
	 * Get's Registered User Roles
	 * @return Array
	 * @since 0.1
	 */
	private function get_roles(){
		$user_roles = get_editable_roles();
		return $user_roles;
	}
    
	/**
	 * Adds Form In WC Product Edit Page
	 * @since 0.1
	 * @filter_use woocommerce_product_data_panels
	 */
	public function role_based_price_form(){
		echo '<div class="panel woocommerce_options_panel show_if_simple show_if_variable show_if_grouped" id="role_based_price_container" style="display: none;">';
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
	
	/**
	 * Saves Product Data In DB
	 * @since 0.1
	 * @filter_use woocommerce_process_product_meta_simple
	 */
	public function save_role_based_price($post_id){ 
		$prices = $_POST['role_based_price'];
		foreach($prices as $key => $val){
			update_post_meta($post_id,'_selling_price_'.$key, wc_clean($val['selling_price']) );
			update_post_meta($post_id,'_regular_price_'.$key, wc_clean($val['regular_price']) );
		} 
	}
	
	/**
	 * Get Current Logged In User Role
	 * @since 0.1
	 */
	public function get_current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
	
	/**
	 * Gets Role Based Sale Price
	 * @since 0.1
	 * @filter_use woocommerce_get_sale_price
	 */
	public function role_based_sale_price($price,$product){
		$post_id = $product->id;
		$role = $this->get_current_role();  
		$price_new = get_post_meta($post_id,'_selling_price_'.$role);
        if(!empty($price_new)){
            $product->sale_price = $price_new[0];
            return floatval($price_new[0]);
        }
        return $price;
	}
    
	/**
	 * Gets Role Based Regular Price
	 * @since 0.1
	 * @filter_use woocommerce_get_regular_price
	 */
	public function role_based_regular_price($price,$product){
		$post_id = $product->id;
		$role = $this->get_current_role();  
		$price_new = get_post_meta($post_id,'_regular_price_'.$role);
        if(!empty($price_new)){
            $product->regular_price = $price_new[0];
            return floatval($price_new[0]);
        }
        return $price;
	}
    
    /**
     * Gets Product Actual Price Based On User Role
     * @since 0.1
     * @updated 0.2
     * @filter_use woocommerce_get_price
     */
    public function role_based_price_all($price,$product){
        $sale_price = $this->role_based_sale_price($price,$product);
        $regular_price = $this->role_based_regular_price($price,$product);
        $return_price = '';

        if($sale_price < $regular_price){
            $return_price = $sale_price;
        } else if(empty($sale_price) && ! empty($regular_price)){
            $return_price = $regular_price;
        }
        if(!empty($return_price)){
            return $return_price;
        } 
        return $price;
        
    }
}

new WooCommerce_Role_Based_Price_Simple_Product_Admin;
?>