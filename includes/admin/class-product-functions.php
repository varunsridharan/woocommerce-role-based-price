<?php
/**
 * WooCommerce Product Role Based Price Edit Functions
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Admin_Product_Functions {
    
    public function __construct() {
        add_action('woocommerce_product_options_pricing',array($this,'add_rbp_edit_button'),6);
		add_action('woocommerce_variation_options_pricing',array($this,'add_variable_rbp_edit_button'),10,3);
        add_action('admin_footer',array($this,'add_modal_code'));
        add_action('wc_rbp_product_save',array($this,'simple_save_product_price'),1,3);
        add_action('wc_rbp_price_editor_template',array($this,'show_simple_editor_view'),1,2);
    }
	
	public function add_variable_rbp_edit_button($var1,$var2,$var3){
		$notice = '';
		$disabled = '';
		if($var3->ID == null) {
			$disabled = 'disabled="disabled"';
			$notice = '<span class="wc_rbp_error"> <strong>'.__(' Please Save Or Publish This Product To Add Role Based Price',WC_RBP_TXT).' </strong> </span>';
		}
		echo '<p class="form-row form-row-last role_based_price_ajax">';
		echo wc_rbp_get_edit_button($var3->ID,'simple',array('attrs' => $disabled));
		echo $notice.'</p>';
	}
	
    
    /**
	 * Adds Edit Button After Pricing In General Only For Simple Product 
	 * @since 0.1
	 * @filter_use woocommerce_product_options_pricing
	 */
	public function add_rbp_edit_button(){
        global $thepostid; 
		$notice = '';
		$disabled = '';
		if($thepostid == null) {
			$disabled = 'disabled="disabled"';
			$notice = '<span class="wc_rbp_error"> <strong>'.__(' Please Save Or Publish This Product To Add Role Based Price',WC_RBP_TXT).' </strong> </span>';
		}
		echo '<p class="form-field role_based_price_ajax show_if_simple ">';
		echo wc_rbp_get_edit_button($thepostid,'simple',array('attrs' => $disabled));
		echo $notice.'</p>';  
	}
    
    
    
    public function add_modal_code(){
        $current_screen = wc_rbp_current_screen();
        if($current_screen == 'product'){
            echo '<div id="wc-rbp-product-price-editor" class="modal-demo"> <a href="javascript:void(0);"  class="close" onclick="Custombox.close();"> <span>&times;</span><span class="sr-only">Close</span>  </a>
        <h4 class="title">'.__("Role Based Price Editor",WC_RBP_TXT).'</h4> <div id="wc-rbp-product-price-editor-content"  class="text"> </div> </div>';
        }
        
    }
    
    public function show_simple_editor_view($post_id,$post_values){
        global $type,$product_id;
		$type = 'simple';
		$product_id = $post_id;
		wc_rbp_modal_header();
        wc_rbp_get_ajax_overlay(); 
		include(WC_RBP_ADMIN.'views/ajax-modal-price-editor.php');
		wc_rbp_modal_footer();
    }
    
    public function simple_save_product_price(&$posted_values,&$success,&$error){
        $post_id = $posted_values['product_id'];

        do_action_ref_array('wc_rbp_product_save_before',array(&$posted_values));
        if(isset($posted_values['role_based_price'])){
            $status = isset($posted_values['enable_role_based_price']) ? true : false;
            wc_rbp_update_role_based_price_status($post_id,$status);
            wc_rbp_update_role_based_price($post_id,$posted_values['role_based_price']);
            clean_post_cache($post_id);
            //$arr = array('html' => "<h3>".__("Product Price Updated.",WC_RBP_TXT)."</h3>");
            //$success = array_merge($arr,$success);
            $success['html'] = '<h3>'.__("Product Price Updated.",WC_RBP_TXT).'</h3>'; 
        } else { 
            $error['html'] = '<h3>'.__("Price Not Defined. Please Try Again",WC_RBP_TXT).'</h3>'; 
        }
        do_action_ref_array('wc_rbp_product_save_after',array(&$posted_values));
    }
} 
?>