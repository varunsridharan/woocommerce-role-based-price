<?php
/**
 * Plugin's Admin code
 *
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Admin {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ));
		add_filter( 'woocommerce_screen_ids',array($this,'set_wc_screen_ids'),99);
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_filter( 'plugin_action_links_'.WC_RBP_FILE, array($this,'plugin_action_links'),10,10); 
	}

	
	public function set_wc_screen_ids($screens){ 
        $screen = $screens; 
      	$screen[] = 'woocommerce_page_woocommerce-role-based-price-settings';
        return $screen;
    }    
	
    /**
     * Inits Admin Sttings
     */
    public function admin_init(){
		new WooCommerce_Role_Based_Price_Admin_Product_Functions;
        new WooCommerce_Role_Based_Price_Admin_Ajax_Handler;
		new WooCommerce_Role_Based_Price_Addons;
       # new WooCommerce_Role_Based_Price_Admin_Sample_Class;
    } 
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() { 
		$current_screen = wc_rbp_current_screen();
        $addon_url = admin_url('admin-ajax.php?action=wc_rbp_addon_custom_css');
        wp_enqueue_style(WC_RBP_SLUG.'_backend_style',WC_RBP_CSS.'backend.css' , array(), WC_RBP_V, 'all' );  
	//	wp_enqueue_style(WC_RBP_SLUG.'_addons_style',$addon_url , array(), WC_RBP_V, 'all' );  
        
        if(in_array($current_screen , wc_rbp_get_screen_ids())) { }
		
        if('woocommerce_page_woocommerce-role-based-price-settings' == $current_screen){
			wp_enqueue_style(WC_RBP_SLUG.'_settings_selectize_style',WC_RBP_CSS.'selectize.js.css' , array(), WC_RBP_V, 'all' );  
		}
        
        if('product' == $current_screen) {
		 	wp_enqueue_style(WC_RBP_SLUG.'_tabs_style',WC_RBP_CSS.'tabs.css' , array(), WC_RBP_V, 'all' );  
            wp_enqueue_style(WC_RBP_SLUG.'_jquery-custombox_style',WC_RBP_CSS.'custombox.min.css' , array(), WC_RBP_V, 'all' );  
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		$current_screen = wc_rbp_current_screen();
        $addon_url = admin_url('admin-ajax.php?action=wc_rbp_addon_custom_js');
        wp_enqueue_script(WC_RBP_SLUG.'_backend_script', WC_RBP_JS.'backend.js', array('jquery'), WC_RBP_V, false ); 
	//	wp_enqueue_script(WC_RBP_SLUG.'_addons_script', $addon_url, array('jquery'), WC_RBP_V, false ); 
        
        if(in_array($current_screen , wc_rbp_get_screen_ids())) {}
		
		if('woocommerce_page_woocommerce-role-based-price-settings' == $current_screen){
			wp_enqueue_script(WC_RBP_SLUG.'_settings_selectize.js', WC_RBP_JS.'selectize.js', array('jquery'), WC_RBP_V, false ); 
			wp_enqueue_script(WC_RBP_SLUG.'_settings_checkbox.js', WC_RBP_JS.'checkbox.js', array('jquery'), WC_RBP_V, false ); 
			wp_enqueue_script(WC_RBP_SLUG.'_settings_js', WC_RBP_JS.'settings-page.js', array('jquery',WC_RBP_SLUG.'_settings_selectize.js'), WC_RBP_V, false ); 
		}
        
        if('product' == $current_screen){
			wp_enqueue_script(WC_RBP_SLUG.'_settings_checkbox.js', WC_RBP_JS.'checkbox.js', array('jquery'), WC_RBP_V, false ); 
		 	wp_enqueue_script(WC_RBP_SLUG.'_jquery-tabs-script', WC_RBP_JS.'tabs.js', array('jquery'), WC_RBP_V, false ); 
            wp_enqueue_script(WC_RBP_SLUG.'_jquery-custombox-script', WC_RBP_JS.'custombox.min.js', array('jquery'), WC_RBP_V, false ); 
            wp_enqueue_script(WC_RBP_SLUG.'_jquery-custombox-legacy-script', WC_RBP_JS.'custombox-legacy.min.js', array(WC_RBP_SLUG.'_jquery-custombox-script'), WC_RBP_V, false ); 
            wp_enqueue_script(WC_RBP_SLUG.'_jquery-product-script', WC_RBP_JS.'product-page.js', array(WC_RBP_SLUG.'_jquery-custombox-legacy-script'), WC_RBP_V, false ); 
        }
	}
     
 
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
    public function plugin_action_links($action,$file,$plugin_meta,$status){
        $actions[] = sprintf('<a href="%s">%s</a>', '#', __('Settings',WC_RBP_TXT) );
        $actions[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WC_RBP_TXT) );
        $action = array_merge($actions,$action);
        return $action;
    }
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WC_RBP_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('F.A.Q',WC_RBP_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('View On Github',WC_RBP_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('Report Issue',WC_RBP_TXT) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', '#', __('Donate',WC_RBP_TXT) );
		}
		return $plugin_meta;
	}	    
}

?>
