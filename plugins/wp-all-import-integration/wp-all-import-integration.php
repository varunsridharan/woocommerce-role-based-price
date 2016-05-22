<?php 
/**
 * Plugin Name: WP All Import WooCommerce Addon Integration
 * Plugin URI:
 * Version: 1.0
 * Description: Integrates With WP All Import WooCommerce Addon And show role based price fields for bulk import
 * Author: Varun Sridharan
 * Author URI: http://varunsridharan.in
 * Last Update: 2016-03-04 
 * Required Plugins: [ Name : WP All Import - WooCommerce Add-On Pro | URL : http://wpallimport.com | Version : 2.2.7 | Slug : wpai-woocommerce-add-on/wpai-woocommerce-add-on.php]
 * Category:Integration
 */



if ( ! defined( 'WC_RBP_PLUGIN' ) ) { die; }
if(!class_exists('RapidAddon')){ include_once(__DIR__.'/wp-all-import-pluggable.php');}
if(!class_exists('WP_All_Import_WooCommerce_Addon_Extender')){ include_once(__DIR__.'/wp-all-import-custom.php'); }


class WP_All_Import_Integation_WC_RBP {
    
	public $framework;
	
    public function __construct() { 
		add_action('admin_init', array($this, 'add_fields'));			
        $this->wpallimport_plug = new WP_All_Import_WooCommerce_Addon_Extender(__('RBP Price Import',WC_RBP_TXT), WC_RBP_DB.'integration');
        $this->wpallimport_plug->run(array( "post_types" => array( "product" ) ));  
        $this->wpallimport_plug->set_import_function(array($this,'wc_rbp_importer_function'));
    }
	
	public function add_fields(){
		$allowed_roles = wc_rbp_allowed_roles();
		$allowed_price = wc_rbp_allowed_price();
		$role_data = wc_rbp_get_wp_roles();
		$price_values = wc_rbp_avaiable_price_type();
		$curr = get_woocommerce_currency_symbol();
		
		$fields = array();
		$tool = __('To Enable Please Use [active,yes] Or To Disable Please leave empty ',WC_RBP_TXT);
		$fields[] = $this->wpallimport_plug->add_field('_enable_rbp_product',__('Enable Role Based Price',WC_RBP_TXT), 'text', null, $tool );
		$this->wpallimport_plug->add_options('',__('General Settings',WC_RBP_TXT),$fields); 
		
		foreach($allowed_roles as $key => $val){ 
			$name = isset($role_data[$val]['name']) ? $role_data[$val]['name'] : ''; 
			$fields = array();
			foreach($allowed_price as $price ){
				$fields[] = $this->wpallimport_plug->add_field($val.'_'.$price, $price_values[$price], 'text', null, '' );
			}
            $this->wpallimport_plug->add_options('',$name.' { '.$val.' } ',$fields); 
            unset($fields);
		}	
	}
	
	public function wc_rbp_importer_function($post_id, $data, $import_options){
		$allowed_roles = wc_rbp_allowed_roles();
		$allowed_price = wc_rbp_allowed_price();
		$price_values = wc_rbp_avaiable_price_type();
		$status = false;
		$final_price = array();
		
		if(isset($data['_enable_rbp_product'])){
			if( $data['_enable_rbp_product'] == 'active'){ $status = true;}
			else if( $data['_enable_rbp_product'] == 'yes'){$status = true;}
		}
		
		foreach($allowed_roles as $role){
			foreach($allowed_price as $price){
				$search_key = $role.'_'.$price;
				if(isset($data[$search_key])){ $final_price[$role][$price] = $data[$search_key]; }
			}
		}
		
		wc_rbp_update_role_based_price_status($post_id,$status);
		wc_rbp_update_role_based_price($post_id,$final_price);
	}
}

return new WP_All_Import_Integation_WC_RBP;