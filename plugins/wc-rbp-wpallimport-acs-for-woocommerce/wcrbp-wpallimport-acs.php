<?php 
/**
 * Plugin Name: WC RBP - WpAllimport - ACS for Woocommerce
 * Plugin URI:
 * Version: 1.0
 * Description: Integrates With WP All Import WooCommerce Addon + Aelia Currency Switcher to allow easy way of importing role based price using currency
 * Author: Varun Sridharan
 * Author URI: http://varunsridharan.in
 * Last Update: 2016-03-04 
 * Required Plugins: [ Name : WP All Import - WooCommerce Add-On Pro | URL : http://wpallimport.com | Version : 2.2.7 | Slug : wpai-woocommerce-add-on/wpai-woocommerce-add-on.php] , [ Name : Aelia Currency Switcher for WooCommerce | URL : http://aelia.co/shop/currency-switcher-woocommerce/ | Version : 3.8.2 | Slug : woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php]
 * Category:Integration
 */

if ( ! defined( 'WC_RBP_PLUGIN' ) ) { die; }
if(!class_exists('RapidAddon')){ include_once(__DIR__.'/wp-all-import-pluggable.php');}
if(!class_exists('WP_All_Import_WooCommerce_Addon_Extender')){ include_once(__DIR__.'/wp-all-import-custom.php'); }

class WCRBP_WPALLIMPORT_ACS {
    
	public $framework;
	
    public function __construct() { 
		add_action('admin_init', array($this, 'add_fields'));			
        $this->wpallimport_plug = new WP_All_Import_WooCommerce_Addon_Extender(__('ACS + WCRBP Price Import',WC_RBP_TXT), WC_RBP_DB.'integration_acs');
        $this->wpallimport_plug->run(array( "post_types" => array( "product" ) ));  
        $this->wpallimport_plug->set_import_function(array($this,'wc_rbp_importer_function'));
    }
	
	public function add_fields(){
		$allowed_roles = wc_rbp_allowed_roles();
		$allowed_price = wc_rbp_allowed_price();
		$allowed_currency = wc_rbp_option('acs_allowed_currencies');

		$role_data = wc_rbp_get_wp_roles();
		$price_values = wc_rbp_price_types();
		$curr = get_woocommerce_currency_symbol();
		
		$fields = array();
		
		foreach($allowed_roles as $key => $val){
			$fields = array();
			$name = isset($role_data[$val]['name']) ? $role_data[$val]['name'] : ''; 
			foreach($allowed_currency as $currency) {
				$field = array();
				
				foreach($allowed_price as $price ){
					$field[] = $this->wpallimport_plug->add_field($val.'_'.$currency.'_'.$price, $price_values[$price].' | '.$currency, 'text', null, '' );
				}
				
				$fields[] = $this->wpallimport_plug->add_options('',$currency,$field); 
				unset($field);
			}
			$this->wpallimport_plug->add_options('',$name.' { '.$val.' } ',$fields); 
			unset($fields);
			
		}	
	}
	
	public function wc_rbp_importer_function($post_id, $data, $import_options){
		$allowed_roles = wc_rbp_allowed_roles();
		$allowed_price = wc_rbp_allowed_price();
		$allowed_currency = wc_rbp_option('acs_allowed_currencies');
		$price_values = wc_rbp_price_types();
		$final_price = array();
		
		foreach($allowed_roles as $role){
			foreach($allowed_currency as $currency){
				foreach($allowed_price as $price){
					$search_key = $role.'_'.$currency.'_'.$price;
					if(isset($data[$search_key])){ $final_price[$role][$currency][$price] = $data[$search_key]; }
				}
			}
		}
		
		wc_rbp_update_acs_role_based_price($post_id,$final_price);
	}
}

return new WCRBP_WPALLIMPORT_ACS;