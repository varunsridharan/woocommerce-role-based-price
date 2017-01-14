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

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) ,99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        
        add_filter( 'woocommerce_get_settings_pages',  array($this,'settings_page') ); 
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_links' ), 10, 2 );
		add_action( 'woocommerce_variable_product_bulk_edit_actions', array($this,'add_bulk_edit'));
		add_action( 'woocommerce_bulk_edit_variations', array($this,'edit_bulk'),10,10);
	}
	
	public function edit_bulk(){
		var_dump(func_get_args()); 
	}
	
	
	public function add_bulk_edit(){
		echo '<option id="BulkEditVariables" onclick="role_Based_bulk_edit(this);"  value="variable_role_based_price_bulk_edit" >'.__('Role Based Price Bulk Edit').'</option>';
	}
    public function get_selectbox_user_role(){
        $user_roles = WC_RBP()->get_registered_roles();
        $list_roles = '';
        $roles = array_keys($user_roles);
        foreach($roles as $role){
            $list_roles[$role] = $user_roles[$role]['name'];
        }
        return $list_roles;
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
            $filename = substr(basename($files), 0, 5 );
            if($filename == 'class'){
                require_once( $files );
            } 
        }        
    }
    
    /**
     * Constructs Included Class
     */
    public function initiate_class(){
        // Get Instance For Settings Panel
        //self::$admin_settings = WooCommerce_Role_Based_Price_Admin_Settings::get_instance();
       
    } 
    
	/**
	 * Adds Settings Page
	 */
 	public function settings_page( $settings ) {
		$settings[] = include('class-admin-settings.php');  
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
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_style(WC_RBP_SLUG.'core_style',plugins_url('css/style.css',__FILE__) , array(), WC_RBP_VERSION, 'all' );  
            wp_enqueue_style(WC_RBP_SLUG.'colorbox',plugins_url('css/colorbox.css',__FILE__) , array(), WC_RBP_VERSION, 'all' );  
            wp_enqueue_style(WC_RBP_SLUG.'modifed_jquery_ui',plugins_url('css/jqueryUI/jquery-ui.theme.css',__FILE__) , array(), WC_RBP_VERSION, 'all' ); 
            
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            
            wp_enqueue_script(WC_RBP_NAME.'COLORBOX', plugins_url('js/jquery.colorbox.js',__FILE__), array( 'jquery' ), WC_RBP_VERSION, false ); 
            wp_enqueue_script(WC_RBP_NAME, plugins_url('js/script.js',__FILE__), array( 'jquery',WC_RBP_NAME.'COLORBOX'), WC_RBP_VERSION, false ); 
            //wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-tabs');
        }
 
	}
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
        $screen_ids[] = 'edit-product';
        $screen_ids[] = 'product';
        return $screen_ids;
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
                __('Settings',WC_RBP_TXT)
            );
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://wordpress.org/plugins/woocommerce-role-based-price/faq/',
				__('F.A.Q',WC_RBP_TXT)
			);
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Role-Based-Price',
				'View On Github'
			);
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/new',
				__('Report Issue',WC_RBP_TXT)
			);
            
            $plugin_meta[ ] = sprintf(
				'&hearts; <a href="%s">%s</a>',
				'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=36Y7KSYPF7KTU',
				__('Donate',WC_RBP_TXT)
			);
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'http://varunsridharan.in/plugin-support/',
				__('Contact Author',WC_RBP_TXT)
			);
            
		}
		return $plugin_meta;
	}	
}