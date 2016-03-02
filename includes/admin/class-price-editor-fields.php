<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Admin_Price_Editor_Fields {
    
    public function __construct() {
    	add_action('wc_rbp_price_edit_before_tab',array($this,'add_status_field'),10,2);
    }
	
	public function add_status_field($product_id,$type){
		$status = product_rbp_status($product_id) == 'true' ? 'checked' : '';

		echo '<div class="enable_field_container">';
		echo '<p class="form-field ">';
		echo '<label class="enable_text" for="enable_role_based_price">'.__('Enable Role Based Pricing',WC_RBP_TXT).'</label>';
		echo '<input type="checkbox" data-secondaryColor="#999" data-size="small" class="wc_rbp_checkbox" id="enable_role_based_price" name="enable_role_based_price" '.$status.'/>';
		echo '</p>';
		echo '</div>';
		$this->register_price_fields();
	}
	
	public function register_price_fields(){
		$allowed_roles = wc_rbp_option('allowed_roles');
		foreach($allowed_roles as $role){
			add_action('wc_rbp_price_edit_tab_'.$role,array($this,'generate_price_field'),10,3);
		}		
	}
	
	public function generate_price_field($product_id,$view_type,$tab_id){
		global $product; 
		$allowed_price = wc_rbp_option('allowed_price');
		$price_exists = wc_rbp_avaiable_price_type();
		$output_html = '<div class="wc_rbp_popup_section wc_rbp_popup_section_'.$tab_id.'">';
		
		foreach($allowed_price as $price){
			$field_id = 'role_based_price['.$tab_id.']['.$price.']';
			$defaults = array(
				'type'              => 'text',
				'label'             => $price_exists[$price],
				'description'       => __('Enter Product\'s ').$price_exists[$price],
				'class'             => array(),
				'label_class'       => array(),
				'input_class'       => array(),
				'return'            => true,
				'custom_attributes' => array(),
			);
			$output_html .= '<div class="wc_rbp_pop_field_50 wc_rbp_pop_field_'.$price.'">';
			$price = wc_rbp_price($product_id,$tab_id,$price);
			
			$output_html .= woocommerce_form_field($field_id,$defaults,$price);
			$output_html .= '</div>';
		}
		$output_html .= '</div>';
		echo $output_html;
	}
	
	
}

return new WooCommerce_Role_Based_Price_Admin_Price_Editor_Fields;
?>