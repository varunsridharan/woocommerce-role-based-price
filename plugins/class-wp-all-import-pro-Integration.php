<?php 
/**
 * Integration For WP-ALL-IMPORTER-PRO
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.4
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once('class-wp-all-import-pro-pluggable.php');
class WooCommerce_Role_Based_Price_WPALLIMPORTER_PLUG {
    private $is_importer_page = false;
    private $wpallimport_plug = '';
    
    public function __construct (){
        add_action( 'admin_init', array($this,'wc_rbp_import_init'),1);  
    }
    
    public function wc_rbp_import_init(){  
        $this->wpallimport_plug = new RapidAddon( __('WooCommerce Role Based Pricing',WC_RBP_TXT), 'wc_rbp_wp_all_import');
        $this->add_fields();
        $this->plug()->run(array( "post_types" => array( "product" ) ));  
        $this->plug()->set_import_function(array($this,'wc_rbp_importer_function'));
    }
    
    private function plug(){ return $this->wpallimport_plug; }
    
    private function add_fields(){
        $this->plug()->add_field('_enable_role_based_price', 'Enable Role Based Price', 'text', null,'314agdag');
        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        $allowed_Roles = WC_RBP()->get_allowed_roles(); 

        foreach($allowed_Roles as $key => $val){ 
            $name = WC_RBP()->get_mod_name($key);
            $fields = array();
            if($regular_price){ $fields[] = $this->plug()->add_field( $key.'_regular_price', __( 'Regular Price', WC_RBP_TXT), 'text', null, '' ); }
            if($regular_price){ $fields[] = $this->plug()->add_field( $key.'_selling_price', __( 'Selling Price', WC_RBP_TXT), 'text', null, '' ); } 
            $this->plug()->add_options('',__($name.' { '.$key.' } ',WC_RBP_TXT),$fields); 
            unset($fields);
        }      
    } 
    
    public function wc_rbp_importer_function($post_id, $data, $import_options){
        //$product = get_product( $post_id);
      
        $roles = array_keys(WC_RBP()->get_allowed_roles());
        $enable = $data['_enable_role_based_price'];
        $prices = array();
        foreach($roles as $role){
            $prices[$role] = '';
            $prices[$role]['regular_price'] = $data[$role.'_regular_price'];
            $prices[$role]['selling_price'] = $data[$role.'_selling_price'];
            
        }
       
        if(!empty($enable)){
            update_post_meta($post_id,'_enable_role_based_price', 'true');
        } else {
            update_post_meta($post_id,'_enable_role_based_price', 'false');
        }
        update_post_meta($post_id,'_role_based_price', $prices);
    }
 
}

new WooCommerce_Role_Based_Price_WPALLIMPORTER_PLUG;

?>