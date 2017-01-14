<?php
/**
 * Integration For Aelia Currency Switcher + WPALL Import
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.4
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once('class-wp-all-import-pro-pluggable.php');

class WooCommerce_role_based_price_aelia_wp_all_import_integration{
    private $is_importer_page = false;
    private $wpallimport_plug = '';
    
    public function __construct (){
        add_action( 'admin_init', array($this,'wc_rbp_aelia_wp_all_import_init'),1); 
    }
    
    public function wc_rbp_aelia_wp_all_import_init(){  
        $this->wpallimport_plug = new RapidAddon( __('Aelia Currency Switcher Integration',WC_RBP_TXT), 'wc_rbp_aelia_wp_all_import_init');
        $this->add_fields();
        $this->plug()->run(array( "post_types" => array( "product" ) ));  
        $this->plug()->set_import_function(array($this,'wc_rbp_importer_function'));
    }
    
    private function plug(){ return $this->wpallimport_plug; }
    
    private function add_fields(){

        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        $allowed_Roles = WC_RBP()->get_allowed_roles(); 
        $allowed_currency = get_option(rbp_key.'acs_allowed_currencies');

        
        foreach($allowed_Roles as $key => $val){ 
            $fields = array();
            $name = WC_RBP()->get_mod_name($key);
            foreach($allowed_currency as $currency) {
                $symbol = get_woocommerce_currency_symbol($currency) ; 
                $symbol = ! empty($symbol) ? ' ('.$symbol.') ' : ' ('.$currency.') ';
                
                 

                if($regular_price){ 
                    $fields[] = $this->plug()->add_field( 'aelia_wcrbp_'.$currency.'_regular_price_'.$key.'_field',
                                                         __( 'Regular Price'.$symbol , WC_RBP_TXT), 'text',null, '' ); 
                }

                if($selling_price){ 
                    $fields[] = $this->plug()->add_field( 'aelia_wcrbp_'.$currency.'_selling_price_'.$key.'_field',
                                                         __( 'Selling Price'.$symbol , WC_RBP_TXT), 'text', null, '' ); 
                } 
                
            }
            
            $this->plug()->add_options('',__($name.' { '.$key.' } ',WC_RBP_TXT),$fields); 
            unset($fields);
        }      
    } 
    
    public function wc_rbp_importer_function($post_id, $data, $import_options){
        ///$product = get_product( $post_id);
        
        
        $allowed_currency = get_option(rbp_key.'acs_allowed_currencies');
        
        $roles = array_keys(WC_RBP()->get_allowed_roles());
        $enable = $data['_enable_role_based_price'];
        $prices = array();
        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        
        
        foreach($roles as $role){  
            foreach($allowed_currency as $currency) {
                if($regular_price){
                    $prices[$role][$currency]['regular_price'] = $data['aelia_wcrbp_'.$currency.'_regular_price_'.$role.'_field'];
                }
                
                if($selling_price){
                    $prices[$role][$currency]['selling_price'] = $data['aelia_wcrbp_'.$currency.'_selling_price_'.$role.'_field'];
                }
                
            }
        }
      
        
        update_post_meta($post_id,'_acs_role_based_price', $prices);
    }
} 

if(is_admin()){
	add_action('wc_rbp_loaded','load_aelia_wp_all_import');
	function load_aelia_wp_all_import(){
		new WooCommerce_role_based_price_aelia_wp_all_import_integration;
	}
}
?>