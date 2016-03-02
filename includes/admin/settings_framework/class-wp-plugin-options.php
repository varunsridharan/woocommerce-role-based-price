<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Admin_Settings_Options {
    
    public function __construct() {
    	add_filter('wc_rbp_settings_pages',array($this,'settings_pages'));
		add_filter('wc_rbp_settings_section',array($this,'settings_section'));
		add_filter('wc_rbp_settings_fields',array($this,'settings_fields'));
    }
	
	public function settings_pages($page){
		$page[] = array('id'=>'general','slug'=>'general','title'=>__('General',WC_RBP_TXT));
		$page[] = array('id'=>'addons','slug'=>'wcrbpaddons','title'=>__('Add-ons',WC_RBP_TXT));
		return $page;
	}
	public function settings_section($section){
		$section['general'][] = array( 'id'=>'general', 'title'=> __('General',WC_RBP_TXT));
		$section['general'][] = array( 'id'=>'price_edit_view', 'title'=> __('Popup Editor View',WC_RBP_TXT));
		
		$section['addons'][] = array( 'id'=>'general2', 'title'=>'',  );
		return $section;
	}
	public function settings_fields($fields){
		$fields['general']['general'][] = array(
			'id' => WC_RBP_DB.'allowed_roles',
			'multiple' => 'true',
			'type'    => 'select',
			'label' => __('Allowed User Roles',WC_RBP_TXT),
			'desc' => __('User Roles To List In Product Edit Page',WC_RBP_TXT),
			'options' => wc_rbp_get_user_roles_selectbox(),
			'attr'    => array(
				'class' => 'wc-rbp-enhanced-select',
				'multiple' => 'multiple'
			),
		);
		
		$fields['general']['general'][] =
			array(
			'id' => WC_RBP_DB.'allowed_price',
			'type' => 'select',
			'multiple' => true,
			'label' => __('Allowed Product Pricing',WC_RBP_TXT),
			'desc' => __('Price Fields To List In Product Edit Page',WC_RBP_TXT),
			'options' => wc_rbp_avaiable_price_type(),
			'attr'    => array(
				'class' => 'wc-rbp-enhanced-select',
				'style' => 'width:auto;max-width:35%;',
				'multiple' => 'multiple',
			)
		);
		
		$fields['general']['price_edit_view'][] =
			array(
			'id' => WC_RBP_DB.'price_editor_tab_pos',
			'type' => 'select',
			'label' => __('Price Editor TAB Position',WC_RBP_TXT),
			'desc' => __('you can change the tab position in price editor view',WC_RBP_TXT),
			'options' => array( 
				'horizontal_top' => __('Horizontal Top',WC_RBP_TXT), 
				'horizontal_bottom' => __('Horizontal Bottom',WC_RBP_TXT), 
				'vertical_left' => __('Vertical Left',WC_RBP_TXT),
				'vertical_right' => __('Vertical right',WC_RBP_TXT),
				),
			'attr'    => array( 'class' => 'wc-rbp-enhanced-select', 'style' => 'width:auto;max-width:35%;', )
		);
		 
	
		return $fields;
	}
	
}

return new WooCommerce_Role_Based_Price_Admin_Settings_Options;
?>