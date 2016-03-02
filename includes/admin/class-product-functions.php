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
        add_action('woocommerce_product_options_pricing',array($this,'add_rbp_edit_button'));
		add_action('woocommerce_variation_options_pricing',array($this,'add_variable_rbp_edit_button'),10,3);
        add_action('admin_footer',array($this,'add_modal_code'));
    }
	
	public function add_variable_rbp_edit_button($var1,$var2,$var3){
		$notice = '';
		$disabled = '';
		if($var3->ID == null) {
			$disabled = 'disabled="disabled"';
			$notice = '<span class="wc_rbp_error"> <strong>'.__(' Please Save Or Publish This Product To Add Role Based Price',WC_RBP_TXT).' </strong> </span>';
		}
		echo '<p class="form-row form-row-last role_based_price_ajax">';
		echo $this->get_rbp_edit_button($var3->ID,'single',$disabled,__('Add / Edit Role Pricing',WC_RBP_TXT));
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
		echo $this->get_rbp_edit_button($thepostid,'single',$disabled,__('Add / Edit Role Pricing',WC_RBP_TXT));
		echo $notice.'</p>';  
	}
    
    public function get_rbp_edit_button($post_ID,$type = 'single',$attrs = '',$text = 'Add / Edit Role Pricing'){
        $return = '<button type="button" class="button button-primary wc_rbp_product_editor_btn"  ';
        $return .= ' data-href="'.admin_url('admin-ajax.php?action=wc_rbp_product_editor&type='.$type.'&post_id='.$post_ID).'" ';
        $return .= $attrs;
        $return .= '>'.__($text,WC_RBP_TXT).' </button>';
        return $return;
    }
    
    
    public function add_modal_code(){
        $current_screen = wc_rbp_current_screen();
        if($current_screen == 'product'){
            echo '<div id="wc-rbp-product-price-editor" class="modal-demo"> <a href="javascript:void(0);"  class="close" onclick="Custombox.close();"> <span>&times;</span><span class="sr-only">Close</span>  </a>
        <h4 class="title">'.__("Role Based Price Editor",WC_RBP_TXT).'</h4> <div id="wc-rbp-product-price-editor-content"  class="text"> </div> </div>';
        }
        
    }
} 
?>