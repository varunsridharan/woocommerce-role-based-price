<?php
/**
 * Simple Product Role Based Price Settings
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      0.1
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_Simple_Product_Admin { 
    private $status;
    
	/**
     * Load The Class
     * @since 0.1
	 */
	public function __construct(){
		add_filter('woocommerce_product_data_tabs',array($this,'add_menu_link'));
		add_action('woocommerce_product_data_panels',array($this,'price_form'));
		add_action('woocommerce_process_product_meta_simple',array($this,'save_product_price'));
        add_action('edit_form_top',array($this,'get_datas')); 

 	}
     
	/**
	 * Adds Menu Link In Product Edit
	 * @since 0.1
	 * @filter_use woocommerce_product_data_tabs
	 */
	public function add_menu_link($array){
		$array['role_based_simple_product_container'] = array('label' => __('Role Based Price',lang_dom) , 
                                     'target' => 'role_based_simple_product_container',
                                     'class'=>array('show_if_simple'));
		return $array;
	} 

    
    public function get_datas($post){ 
        $this->status = WC_RBP()->sp_function()->get_status($post->ID);  
    }
    
    
	/**
	 * Adds Form In WC Product Edit Page
	 * @since 0.1
	 * @filter_use woocommerce_product_data_panels 
	 */
	public function price_form(){
        global $post_id; 
        
        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        WC_RBP()->sp_function()->get_db_price($post_id);
        $status = '';
        $display = 'hidden';
        if($this->status == true){
            $status = 'checked';
            $display = '';
        }

        echo '<div class="panel woocommerce_options_panel" id="role_based_simple_product_container" style="display: none;">';
        echo '<div class="options_group">
                <p class="form-field ">
                    <label class="enable_text">'.__('Enable Role Based Pricing',lang_dom).'</label>
                    <label class="wc_rbp_switch wc_rbp_switch-green">
                        <input type="checkbox" class="switch-input" id="enable_simple_role_based_price" 
                            data-target="simple_role_based_price_field_container" 
                            name="enable_simple_role_based_price" data-type="simple" '.$status.'>
                        <span class="switch-label" data-on="'.__('on',lang_dom).'" data-off="'.__('off',lang_dom).'"></span>
                        <span class="switch-handle"></span>
                    </label>
                </p>
             </div> 
            <div id="simple_role_based_price_field_container" class="'.$display.'">';
           
            foreach(WC_RBP()->get_allowed_roles() as $key => $val){
                $name = WC_RBP()->get_mod_name($key);
                echo '<div class="options_group '.$key.'_role_price" id="'.$key.'_role_price">';
                    echo '<h3>'.__($name,lang_dom).'</h3>';
                
                    if($regular_price){
                        woocommerce_wp_text_input( array( 
                            'id' => 'simple_regular_price_'.$key,
                            'name'=>'simple_role_based_price['.$key.'][regular_price]',
                            'label' => __( 'Regular Price', lang_dom), 	
                            'desc_tip' => 'false',
                            'value' => WC_RBP()->sp_function()->get_selprice($key,'regular_price')
                        )); 
                    }

                    if($selling_price){
                        woocommerce_wp_text_input( array( 
                            'id' => 'simple_selling_price_'.$key,
                            'name'=>'simple_role_based_price['.$key.'][selling_price]',
                            'label' => __( 'Selling Price', lang_dom), 	
                            'desc_tip' => 'false',
                             'value' => WC_RBP()->sp_function()->get_selprice($key,'selling_price')
                        ));                    
                    }

                echo '</div>';
            }
		echo '</div></div>';
	}
	
	/**
	 * Saves Product Data In DB
	 * @since 0.1
	 * @filter_use woocommerce_process_product_meta_simple
	 */
	public function save_product_price($post_id){  
        
        if(isset($_POST['enable_simple_role_based_price'])){
            update_post_meta($post_id,'_enable_role_based_price', 'true');
        } else {
            update_post_meta($post_id,'_enable_role_based_price', 'false');
        }
        
        if(isset($_POST['simple_role_based_price']) && is_array($_POST['simple_role_based_price'])){ 
            $prices = $_POST['simple_role_based_price'];
            update_post_meta($post_id,'_role_based_price', $prices);
             
        } 
	}
	

   
}

new WooCommerce_Role_Based_Price_Simple_Product_Admin;
?>