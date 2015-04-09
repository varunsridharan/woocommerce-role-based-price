<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      0.1
 *
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 *
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
class WooCommerce_Role_Based_Price_Admin {
	private static $_instance = null;

    public $admin_notice;
    public $simple_product;
    public static $admin_settings = null;
    
    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        $this->load_required_files();
        $this->initiate_class();
        add_filter( 'woocommerce_get_settings_pages',  array($this,'settings_page') );
	}

    /**
     * Provides access to a single instances of the class using the singleton pattern
     * @return object
     */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Loads Required Files From includes Dir
     * @since 0.3
     * @return file
     */
    public function load_required_files(){
        foreach( glob( plugin_dir_path( __FILE__ ) . 'includes/*.php' ) as $files ){
            require_once( $files );
        }        
    }
    
    /**
     * Constructs Included Class
     */
    public function initiate_class(){
        // Get Instance For Settings Panel
        self::$admin_settings = WooCommerce_Role_Based_Price_Admin_Settings::get_instance();
    }

        
    
	/**
	 * Adds Settings Page
	 */
 	public function settings_page( $settings ) {
		$settings[] = $this->admin_settings();  
		return $settings;
	}
    
    
    public function admin_settings(){
        return self::$admin_settings;
    }
    
    /**
     * @return Admin Notice Class [WooCommerce_Role_Based_Price_Admin_Notice]
     */
    public function admin_notice(){
        return $this->admin_notice->instance();
    }
    
    
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		#wp_enqueue_style( $this->plugin_name, 'css/plugin-name-admin.css', array(), $this->version, 'all' );
	}
	
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		#wp_enqueue_script( $this->plugin_name, 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );
	}
}