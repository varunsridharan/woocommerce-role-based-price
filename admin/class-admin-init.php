<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      1.0
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

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_filter( 'woocommerce_get_settings_pages',  array($this,'settings_page') );
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_links' ), 10, 2 );
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
		wp_enqueue_style(WC_RBP_NAME,plugins_url('css/style.css',__FILE__) , array(), WC_RBP_VERSION, 'all' );
	}
	
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(WC_RBP_NAME, plugins_url('js/script.js',__FILE__), array( 'jquery' ), WC_RBP_VERSION, false );
        
	}
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
         
		if (  'woocommerce-role-based-price/woocommerce-role-based-price.php' == $plugin_file ) {
            $plugin_meta[ ] = sprintf(
                ' <a href="%s">%s</a>',
                admin_url('admin.php?page=wc-settings&tab=products&section=wc_rbp'),
                __('Settings',lang_dom)
            );
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://wordpress.org/plugins/woocommerce-role-based-price/faq/',
				__('F.A.Q',lang_dom)
			);
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Role-Based-Price',
				'View On Github'
			);
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/new',
				__('Report Issue',lang_dom)
			);
            
            $plugin_meta[ ] = sprintf(
				'&hearts; <a href="%s">%s</a>',
				'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=36Y7KSYPF7KTU',
				__('Donate',lang_dom)
			);
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'http://varunsridharan.in/plugin-support/',
				__('Contact Author',lang_dom)
			);
            
		}
		return $plugin_meta;
	}	
}