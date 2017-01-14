<?php
/**
 * Admin Settings Class For WooCommerce Role Based Price
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      0.3
 *
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * 
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

class WooCommerce_Role_Based_Price_Admin_Settings  extends WC_Settings_Page{
    
    private static $_instance = null;
    
    
    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        $this->id = pp_key;
        add_filter( 'woocommerce_settings_tabs_array',   array( $this,'add_settings_tab' ),50 );
        add_filter( 'woocommerce_sections_'.pp_key,      array( $this, 'output_sections' ));
        add_filter( 'woocommerce_settings_'.pp_key,      array( $this, 'output_settings' )); 
        add_action( 'woocommerce_settings_save_'.pp_key, array( $this, 'save'            ));
		
    }
    
	
    
    
    /**
     * Adds Section in Admin
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs[pp_key] = __('WooCommerce Role Based Pricing',WC_RBP_TXT);
        return $settings_tabs;
    }   
    
    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            ''            => __( 'General', WC_RBP_TXT),
            'user-role-edit'     => __( 'User Role Rename',WC_RBP_TXT),
            'price-views' => __('Price Visibility',WC_RBP_TXT),
            //'newsletter' => __('Newsletter',WC_RBP_TXT),
        );

        $sections = apply_filters(pp_key.'_sections', $sections );
        
        $sections['plugin'] = __( 'Plugins', WC_RBP_TXT);

        return apply_filters( 'woocommerce_get_sections_' . pp_key, $sections );
    }
    
 
    
    public function output_settings(){
        global $current_section; 
        $settings = $this->get_settings( $current_section );
        $hide_sec = array('plugin','newsletter');
        if(in_array($current_section,$hide_sec)){ 
            $GLOBALS['hide_save_button'] = true;
        } else {
            WC_Admin_Settings::output_fields( $settings );
        }
                
    }
    
    
    
    
    
    /**
     * Get sections
     *
     * @return array
     */
    public function get_settings( $section = null ) { 
        global $settings;
        $file = '';
        $settings = array();
        $file_name = '';
        if(!empty($section)){ $file_name = 'settings-'.$section.'.php'; }
        if(empty($section)){ $file_name = 'settings-general.php'; }
       
        
        if(file_exists(WC_RBP_ADMIN_PATH.'/includes/'.$file_name)) {
            include(WC_RBP_ADMIN_PATH.'/includes/'.$file_name); 
        } else { 
            $settings = apply_filters($this->id.'section_'.$section,$settings);
        } 
         
        return apply_filters( 'wc_settings_tab_'.$this->id.'_settings', $settings, $section );

    }

    
       
    /**
     * Save settings
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );
    }  
    
    
    
    
    
    
    
}

return new WooCommerce_Role_Based_Price_Admin_Settings();
?>