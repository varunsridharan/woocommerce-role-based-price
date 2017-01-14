<?php
/**
 * Integration For Aelia Currency Switcher
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.4
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_AeliaCurrencySwitcher_Plug {
    private $db_price;
    private $base_currency;
    
    public function __construct (){
        $this->base_currency = get_option('woocommerce_currency');
        add_action( 'init', array($this,'admin_init'),1); 
    }
    
    public function admin_init(){
        
        
        add_filter('wc_rbpsection_aelia-currencyswitcher-intergation',array($this,'add_settings'));
        add_action('woocommerce_role_based_price_fields',array($this,'add_fields'),5,5);
        add_action('woocommerce_role_based_price_data_save',array($this,'save_data'));
        add_filter('woocommerce_role_based_product_price_value',array($this,'change_price'),1,5);
        
        if(WC_RBP()->is_request( 'frontend' )){
            //remove_filter('woocommerce_get_price', array('WC_Aelia_CurrencySwitcher', 'woocommerce_get_price'), 5);
            add_filter('wc_aelia_currencyswitcher_product_currency_prices', array($this,'my_custom_prices'), 15, 3);
        }
        
        
        add_filter('wc_rbp_sections',array($this,'add_section'));
    }
    
    
    public function save_data($post_id){ 
        if(isset($_POST['acs']) && is_array($_POST['acs'])){ 
            $prices = $_POST['acs'];
            update_post_meta($post_id,'_acs_role_based_price', $prices);
        } 
    } 
        
    public function add_section($section){ 
        $section['aelia-currencyswitcher-intergation'] = __('Aelia Currency Switcher',WC_RBP_TXT);
        return $section;
    
    }
    
    public function add_settings($settings){ 
        
        $settings[] = array(
            'name' => '',
            'type' => 'title',
            'desc' => '',
            'id' => rbp_key.'aeliacurrencyswitcher_settings_start'
        );
        $settings[] = array(
            'name' => __('Allowed Currency',WC_RBP_TXT),
            'desc' => __('Allowed Currency To Show In Role Based Price Listing',WC_RBP_TXT),
            'id' => rbp_key.'acs_allowed_currencies',
            'type' => 'multiselect', 
            'class' =>'chosen_select', 
            'options' =>  $this->get_currency_key_val()
        );


        $settings[] =  array(
            'type' => 'sectionend',
            'id' => rbp_key.'aeliacurrencyswitcher_settings_end'
        );
        
        return $settings;
    }
    
    private function get_enabled_currencies () {
        return apply_filters('wc_aelia_cs_enabled_currencies', array(get_option('woocommerce_currency')));
    }
    
    private function get_currency_key_val() {
        $wc_currencies = get_woocommerce_currencies();
        $enabled_currency = $this->get_enabled_currencies();  
        $return_currency = array();
        foreach($enabled_currency as $curr ){
            if(isset($wc_currencies[$curr])) {
                $symbol = get_woocommerce_currency_symbol($curr) ; 
                $symbol = ! empty($symbol) ? ' [ '.$symbol.' ] ' : '';
                $return_currency[$curr] = $wc_currencies[$curr].$symbol;
            }
        }
        
        return $return_currency;
    }
    
    public function add_fields($regular_price,$selling_price,$thepostid,$user_role_key,$name){
        $allowed_currency = get_option(rbp_key.'acs_allowed_currencies');
        if(empty($allowed_currency)){
            $allowed_currency = $this->get_enabled_currencies ();
        } 

        
        $this->acs_get_db_price($thepostid);
        echo '<div class="wc_rbp_plugin_field_container">'; 
        echo '<hr/> <h3>'.__( 'Role Based Price for Aelia Currency Switcher' , WC_RBP_TXT).'</h3>';
        
       
            foreach($allowed_currency as $currency) {
                if($this->base_currency == $currency){continue;} 
                
                $symbol = get_woocommerce_currency_symbol($currency) ; 
                $symbol = ! empty($symbol) ? ' ('.$symbol.') ' : ' ('.$currency.') '; 
                
                if($regular_price){

                    echo '<p class="form-field '.$currency.'_regular_price_'.$user_role_key.'_field form-row-first">
                            <label for="'.$currency.'_regular_price_'.$user_role_key.'_field">'.__( 'Regular Price'.$symbol , WC_RBP_TXT).'</label>
                            <input type="text" id="'.$currency.'_regular_price_'.$user_role_key.'_field" 
                                   name="acs['.$user_role_key.']['.$currency.'][regular_price]" class="short wc_input_price"
                                   value="'.$this->acs_crp($currency,$user_role_key,'regular_price').'">
                            </p>';
                }

                if($selling_price){
                    echo '<p class="form-field '.$currency.'_selling_price_'.$user_role_key.'_field form-row-last">
                    <label for="'.$currency.'_selling_price_'.$user_role_key.'_field">'.__( 'Selling Price'.$symbol , WC_RBP_TXT).'</label>
                    <input type="text"  id="'.$currency.'_selling_price_'.$user_role_key.'_field" 
                           name="acs['.$user_role_key.']['.$currency.'][selling_price]" class="short wc_input_price"
                           value="'.$this->acs_crp($currency,$user_role_key,'selling_price').'" >
                    </p>'; 
                }
                
            }
             
        
           echo ' </div>';
    }
    
    private function acs_get_db_price($id){ 
        $this->db_prices = get_post_meta($id,'_acs_role_based_price',true );  
        if(is_array($this->db_prices)){
            return $this->db_prices;
        }
        return false;
    }

    public function acs_crp($currency,$role,$price = 'all',$post_id = null){ 
        if($this->base_currency == $currency){
            WC_RBP()->sp_function()->get_db_price($post_id);
            $price = WC_RBP()->sp_function()->get_selprice($role,$price);    
            return $price; 
        }
        
        if(isset($this->db_prices[$role][$currency][$price])){
            return $this->db_prices[$role][$currency][$price];
        }
        return false;
    } 
    
 
           
           
    public function change_price($wcrbp_price,$post_id,$price_meta_key,$user_role){ 
        if(!WC_RBP()->sp_function()->get_status($post_id)){return $wcrbp_price;}
        $this->acs_get_db_price($post_id);
        $price = '';
        $allowed_currency = get_option(rbp_key.'acs_allowed_currencies');
        
        if(empty($allowed_currency)){
            $allowed_currency = $this->get_enabled_currencies ();
        } 
        
        $send_currency = array(); 
        foreach($allowed_currency as $currency) {
            
            $price = $this->acs_crp($currency,$user_role,$price_meta_key,$post_id);
            $send_currency[$currency] = 0;
            
            if(!empty($price)){
                $send_currency[$currency] = $price;
            } 
        }   
        
        $rprice = $this->get_price_in_currency($wcrbp_price, $send_currency) ;
        
        return $rprice;
    }
    

    
    
    /**
     * Replaces the product prices with custom ones.
     *
     * @param array product_prices An array of product prices.
     * @param int product_id The product ID.
     * @param int price_type The price types to be replaced (e.g. regular prices,
     * sale prices, etc).
     * @return array An array of currency => price entries
     *
     * @author Aelia <support@aelia.co>
     * @link http://aelia.co/about
     */
    public function my_custom_prices($product_prices, $product_id, $price_type) {
            /*
            $price_type can have one of the following values:
            - '_regular_currency_prices' -> Product's regular prices
            - '_sale_currency_prices' -> Product's sale prices
            - 'variable_regular_currency_prices' -> Variation's regular prices
            - 'variable_sale_currency_prices' -> Variation's simple prices

            Using $price_type, load the appropriate prices (regular or sale), and store
            them in an array, using the currency as the key. If you don't have one of the
            prices (e.g. GBP), don't add it to the array. Example:
            */
         
            $type = "selling_price";
            if($price_type == 'variable_regular_currency_prices' || $price_type == '_regular_currency_prices' ){
                $type = "regular_price";
            }
        
            $this->acs_get_db_price($product_id);
            $price = '';
            $allowed_currency = get_option(rbp_key.'acs_allowed_currencies');
        
            if(empty($allowed_currency)){
                $allowed_currency = $this->get_enabled_currencies ();
            }
        
            $send_currency = array(); 
            foreach($allowed_currency as $currency) {
                $price = $this->acs_crp($currency,WC_RBP()->current_role(),$type,$product_id);
				//$send_currency[$currency] = 0;
                if(!empty($price)){
                    $send_currency[$currency] = $price;
                } 
            } 
        
		
            //You can now merge the original prices with the ones you loaded.
            $product_prices = array_merge($product_prices, $send_currency); 
		
            //Finally, return the overridden prices
            return $product_prices;
    }    
    
    /**
    * Advanced integration with WooCommerce Currency Switcher, developed by Aelia
    * (http://aelia.co). This method can be used by any 3rd party plugin to
    * return prices converted to the active currency.
    *
    * @param double price         The source price.
    * @param string to_currency   The target currency. If empty, the active currency
    *                             will be taken.
    * @param string from_currency The source currency. If empty, WooCommerce base
    *                             currency will be taken.
    * @return double The price converted from source to destination currency.
    *                              @author Aelia <support@aelia.co>
    *                              @link http://aelia.co
    */
    protected function get_price_in_currency($price, $prices_per_currency = array(), $to_currency = null, $from_currency = null) {
        if(empty($from_currency)) {
          $from_currency = get_option('woocommerce_currency');
        }
        if(empty($to_currency)) {
          $to_currency = get_woocommerce_currency();
        } 
        // If an explicit price was passed for the target currency, just take it
        if(!empty($prices_per_currency[$to_currency])) {
           return $prices_per_currency[$to_currency];
        } 
        
		if($price == 0){return $price;}
        return apply_filters('wc_aelia_cs_convert', $price, $from_currency, $to_currency);
    }    
} 



new WooCommerce_Role_Based_Price_AeliaCurrencySwitcher_Plug;
?>