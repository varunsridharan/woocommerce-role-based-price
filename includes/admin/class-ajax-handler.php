<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Admin_Ajax_Handler {
    
    public function __construct() {
		$allowed_roles = wc_rbp_option('allowed_roles');
        add_action( 'wp_ajax_wc_rbp_product_editor', array($this,'product_editor_template_handler' ));
		add_action( 'wp_ajax_wc_rbp_save_product_prices',array($this,'save_product_rbp_price'));
		add_action('wp_ajax_nopriv_wc_rbp_addon_custom_css',array($this,'render_addon_css'));
		add_action('wp_ajax_wc_rbp_addon_custom_css',array($this,'render_addon_css'));
		add_action('wp_ajax_nopriv_wc_rbp_addon_custom_js',array($this,'render_addon_js'));
		add_action('wp_ajax_wc_rbp_addon_custom_js',array($this,'render_addon_js'));
    }
	
	public function render_addon_css(){
		echo '<style>';
		do_action('wc_rbp_addon_styles');
		echo '</style>';
		wp_die();
	}
    
	public function render_addon_js(){
		echo '<script>';
		do_action('wc_rbp_addon_scripts');
		echo '</script>';
		wp_die();
	}
	
	public function save_product_rbp_price(){
		$is_verifyed_nounce = wp_verify_nonce($_POST['wc_rbp_nounce'], 'wc_rbp_save_product_prices' );
		$error = array();
		$success = array('hidden_fields' => wc_rbp_get_editor_fields());
		$posted_values = $_POST;
		
		if($is_verifyed_nounce){
			$post_id = $posted_values['product_id'];

			do_action_ref_array('wc_rbp_product_save_before',array(&$posted_values));
			if(isset($posted_values['role_based_price'])){
				$status = isset($posted_values['enable_role_based_price']) ? true : false;
				wc_rbp_update_role_based_price_status($post_id,$status);
				wc_rbp_update_role_based_price($post_id,$posted_values['role_based_price']);
				clean_post_cache($post_id);
				$success['html'] = '<h3>'.__("Product Price Updated.",WC_RBP_TXT).'</h3>';
			} else { 
				$error['html'] = '<h3>'.__("Price Not Defined. Please Try Again",WC_RBP_TXT).'</h3>'; 
			}
			do_action_ref_array('wc_rbp_product_save_after',array(&$posted_values));
			
		} else { 
			$error['html'] = '<h3>'.__("Unable To Process Your Request Please Try Again later",WC_RBP_TXT).'</h3>'; 
		}
	
		if(empty($error)){ 
			wp_send_json_success($success); 
		} else {
			$error['hidden_fields'] =wc_rbp_get_editor_fields();
			wp_send_json_error($error);
		}
		wp_die();
	}
	
    public function product_editor_template_handler(){
        $msg = '';
        $post_data = $_REQUEST; 

		if(isset($post_data['post_id'])){
			$this->product_price_editor_template_loader($post_data['post_id'],$post_data['type']);
        } else {
            $title = __('Product Price Edit Failed',WC_RBP_TXT);
            $content = __('<h3> Invalid Product Selected Or Unable To Process Your Request Now.. <small> Please Try Again Later</small> </h3>',WC_RBP_TXT);
            $msg = wc_rbp_modal_template($title,$content);
        }
        wp_die($msg);
    }
	
	public function product_price_editor_template_loader($id,$viewType='single'){
		global $type,$product_id;
		$type = $viewType;
		$product_id = $id;
		wc_rbp_modal_header();
		include(WC_RBP_ADMIN.'views/ajax-modal-price-editor.php');
		wc_rbp_modal_footer();
	}
	
}
?>