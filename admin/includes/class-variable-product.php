<?php
/**
 * Variable Product Role Based Price Settings
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.0
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_Variable_Product_Admin { 
    private $status;
    
	/**
     * Load The Class
     * @since 0.1
	 */
	public function __construct(){
		add_action('woocommerce_product_after_variable_attributes',array($this,'price_form'),3,3);
        add_action('woocommerce_save_product_variation',array($this,'save_product_price'),5,2);
 	}
    
	/**
	 * Adds Form In WC Product Edit Page
	 * @since 0.1
	 * @filter_use woocommerce_product_data_panels 
	 */
	public function price_form($loop,$vdata,$varition){  
        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        $this->status = WC_RBP()->sp_function()->get_status($varition->ID); 
        WC_RBP()->sp_function()->get_db_price($varition->ID);
        $status = '';
        $display = 'hidden';
        if($this->status == true){
            $status = 'checked';
            $display = '';
        } 

        echo '<div class="options_group">
                <p class="form-field ">
                    <label class="enable_text">'.__( 'Enable Role Based Pricing', lang_dom).'</label>
                    <label class="wc_rbp_switch wc_rbp_switch-green">
                        <input type="checkbox" class="switch-input enable_variable_role_based_price" id="enable_variable_role_based_price_product_'.$varition->ID.'" 
                            data-target="variable_role_based_price_field_container_'.$varition->ID.'" 
                            name="enable_variable_role_based_price_product_'.$varition->ID.'" data-type="variable" '.$status.'>
                        <span class="switch-label" data-on="'.__( 'On', lang_dom).'" data-off="'.__( 'off', lang_dom).'"></span>
                        <span class="switch-handle"></span>
                    </label>
                </p>
             </div> 
            <div id="variable_role_based_price_field_container_'.$varition->ID.'" class=" '.$display.' variable_roles">';

            foreach(WC_RBP()->get_allowed_roles() as $key => $val){
                $name = WC_RBP()->get_mod_name($key);
                echo '<div class="variable_pricing '.$key.'_role_price" id="'.$key.'_role_price">';
                    echo '<h3>'.__( $name, lang_dom).'</h3>';
                
                    if($regular_price){
                        woocommerce_wp_text_input( array( 
                            'id' => 'variable_regular_price_'.$key,
                            'name'=>'   variable_role_based_price_product_'.$varition->ID.'['.$key.'][regular_price]',
                            'label' => __( 'Regular Price', lang_dom), 	
                            'desc_tip' => 'false',
                            'value' => WC_RBP()->sp_function()->get_selprice($key,'regular_price'),
                            'wrapper_class' => 'form-row-first form-row'
                        )); 
                    }

                    if($selling_price){
                        woocommerce_wp_text_input( array( 
                            'id' => 'variable_selling_price_'.$key,
                            'name'=>'variable_role_based_price_product_'.$varition->ID.'['.$key.'][selling_price]',
                            'label' => __( 'Selling Price', lang_dom), 	
                            'desc_tip' => 'false',
                             'value' => WC_RBP()->sp_function()->get_selprice($key,'selling_price'),
                            'wrapper_class' => 'form-row-last form-row'
                        ));                    
                    }

                echo '</div>';
            }
		echo '</div> '; 
	}
	
	/**
	 * Saves Product Data In DB
	 * @since 0.1
	 * @filter_use woocommerce_process_product_meta_simple
	 */
	public function save_product_price($post_id,$i){  
        if(isset($_POST['enable_variable_role_based_price_product_'.$post_id])){ update_post_meta($post_id,'_enable_role_based_price', 'true'); } 
        else { update_post_meta($post_id,'_enable_role_based_price', 'false'); }
        
        if(isset($_POST['variable_role_based_price_product_'.$post_id]) && is_array($_POST['variable_role_based_price_product_'.$post_id])){ 
            $prices = $_POST['variable_role_based_price_product_'.$post_id];
            update_post_meta($post_id,'_role_based_price', $prices);
             
        } 
	}
}

new WooCommerce_Role_Based_Price_Variable_Product_Admin;
?>