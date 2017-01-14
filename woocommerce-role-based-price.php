<?php
/**
 * Plugin Name:       WooCommerce Role Based Price
 * Plugin URI:        https://wordpress.org/plugins/woocommerce-role-based-price/
 * Description:       Set WooCommerce Product Price Based On User Role
 * Version:           2.8.8
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       woocommerce-role-based-price
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: https://github.com/technofreaky/WooCommerce-Role-Based-Price
 */

if ( ! defined( 'WPINC' ) ) { die; }

define('WC_RBP_NAME','WC Role Based Price',true); # Plugin Name
define('WC_RBP_SLUG','wc-role-based-price',true); # Plugin Slug
define('WC_RBP_VERSION','2.8.8',true); # Plugin Version
define('WC_RBP_PATH',plugin_dir_path( __FILE__ ),true); # Plugin DIR
define('WC_RBP_ADMIN_PATH',WC_RBP_PATH.'admin/',true); # Plugin DIR
define('WC_RBP_ADMIN_CSS',WC_RBP_PATH.'admini/css/'); # Plugin DIR
define('WC_RBP_ADMIN_JS',WC_RBP_PATH.'admini/js/'); # Plugin DIR
define('rbp_key','wc_rbp_'); # PLugin DB Prefix
define('pp_key','wc_rbp'); # PLugin DB Prefix
define('WC_RBP_DB_KEY',rbp_key); # Plugin Prefix
define('WC_RBP_TXT','woocommerce-role-based-price',true); #plugin lang Domain
define('plugin_url',plugins_url('', __FILE__ ));


/**
 * Class to initiate the plugin
 */
final class  WooCommerce_Role_Based_Price{
    
    private static $_instance = null;
    public static $admin_instance = null;
    private $avaiable_price = array('regular','sale');
    
    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }   
    
    /**
     * Class Constructor
     */
    private function __construct() {
		require_once( 'includes/class-activation.php' ); 
		$default_args = array(
			'dbslug' => WC_RBP_DB_KEY,
			'welcome_slug' => WC_RBP_SLUG.'-welcome-page',
			'wp_plugin_slug' => 'woocommerce-role-based-price',
			'wp_plugin_url' => 'http://wordpress.org/plugins/woocommerce-role-based-price/',
			'tweet_text' => 'Sell product in different price for different user role based on your settings. ',
			'twitter_user' => 'varunsridharan2',
			'twitter_hash' => 'WCRBP',
			'gitub_user' => 'technofreaky',
			'github_repo' => 'WooCommerce-Role-Based-Price',
			'plugin_name' => WC_RBP_NAME,
			'version' => WC_RBP_VERSION,
			'template' => WC_RBP_PATH.'includes/page-html.php',
			'menu_name' => 'Welcome WP Plugin Welcome Page',
			'plugin_file' => __FILE__,
		);
		new WC_RBP_Activation($default_args);				
        //register_activation_hook( __FILE__, array(__CLASS__,'plugin_activate' ));
        add_action( 'init', array( $this, 'init' ), 0 );
        add_action('plugins_loaded', array( $this, 'langs' ));
        add_filter('load_textdomain_mofile',  array( $this, 'replace_my_plugin_default_language_files' ), 10, 2);
    }
    
    
    public static function plugin_activate() {
        set_transient( 'wc_rbp_welcome_screen_activation_redirect', true, 30 );
        self::plugin_upgrade_check();
    }    
    
    public static function plugin_upgrade_check(){
        update_option(WC_RBP_DB_KEY.'version',WC_RBP_VERSION);
        require_once('updates/wc_rbp_update_v2.5.php'); 
        require_once('updates/wc_rbp_update_v2.7.4.php'); 
    }

 
    public function load_plugins(){
        $plugins = $this->get_activated_plugin(); 
        $plugin_list = $this->get_plugins_list();
        
        if(! empty($plugins)){
            foreach($plugins as $plugin){ 
                if(isset($plugin_list[$plugin])){
                    include(WC_RBP_PATH.'plugins/'.$plugin_list[$plugin]['file']);
                }
            }
        } 
    }
    
    public function is_request( $type ) {
        $is_ajax = defined('DOING_AJAX') && DOING_AJAX;
        switch ( $type ) {
            case 'admin' : 
                return ( is_admin() && !$is_ajax ) || ( is_admin() && $is_ajax && isset( $_REQUEST['action'] ));
            case 'frontend' :
                return ! $this->is_request('bot') && ( ! is_admin() || ( ! is_admin() && ! $is_ajax ) ) && ! defined( 'DOING_CRON' );
            case 'bot':
                $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
                return preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent );
        }
    }
    
    /**
     * Runs After WP Loaded
     */
    public function init(){
        $this->load_plugins();
        
       // Autoload Required Files
        require_once( 'includes/class-product-functions.php' ); 
		
        
        foreach( glob(WC_RBP_PATH . 'includes/class-*.php' ) as $files ){  require_once( $files ); }
        
        if($this->is_request( 'admin' )){
            require_once(WC_RBP_PATH . 'admin/class-admin-init.php' );
        }
        
        if($this->is_request( 'frontend' )){
            new front_end_product_pricing;
        }        
        
        if($this->is_request( 'admin' )){
            $this->admin_init();
        } 

        new WooCommerce_Role_Based_Price_Simple_Product_Functions;
		do_action( 'wc_rbp_loaded' );
    }
    
    public function langs(){
        load_plugin_textdomain(WC_RBP_TXT, false, dirname(plugin_basename(__FILE__)).'/lang/' );
    }
    
    function replace_my_plugin_default_language_files($mofile, $domain) {
        if (WC_RBP_TXT === $domain)
            return WC_RBP_PATH.'lang/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Inits Admin Class
     */
    public function admin_init(){
        self::$admin_instance = WooCommerce_Role_Based_Price_Admin::get_instance();
    }
    
    /**
     * Inits Simple Product Function Class
     * @return [[Type]] [[Description]]
     */
    public function sp_function(){
        return WooCommerce_Role_Based_Price_Simple_Product_Functions::get_instance();
    }
    
    
    /**
     * Get Registered WP User Roles
     * @return Array
     */
    public function get_registered_roles(){
        $user_roles = get_editable_roles();
        $user_roles['logedout'] = array('name' => 'Visitor / LogedOut User');  
        return $user_roles;
    }
    
	/**
	 * Get Current Logged In User Role
	 * @since 0.1
	 */
	public function current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
        if($user_role == null){
            return 'logedout';
        }
		return $user_role;
	}
    
    /**
     * Returns Admin Class Instance
     */
    public function admin(){
        return self::$admin_instance;
    }
    
    public function get_option($key){ 
        return get_option($key);
    }
    
    /**
     * Returns User Selected / Defined Roles From Settings
     * @return [[Type]] [[Description]]
     */
    public function get_allowed_roles(){
        $db_roles = $this->get_option(rbp_key.'list_roles');
        $registered_roles = $this->get_registered_roles();
        $return_roles = array();
        if(!empty($db_roles)){
            foreach($db_roles as $role){
                if(isset($registered_roles[$role])){
                    $return_roles[$role] = $registered_roles[$role];
                } else {
                    continue;
                }
            }
        } else {
            $return_roles = $registered_roles;
        }
        return $return_roles;        
    }
    
    /**
     * Returns User Selected / Defined Role Price Files From Settings
     * @return [[Type]] [[Description]]
     */
    public function get_allowed_price($price = 'all'){
        $allowed_price = $this->get_option(rbp_key.'allowed_price');
        
        if(empty($allowed_price)) { $allowed_price = $this->avaiable_price; }

        if($price !== 'all'){
            if(in_array($price, $allowed_price)){
                return true;
            } else {
                return false;
            }
        } else {
            if(!empty($allowed_price)){
                return $allowed_price;
            } else {
                return $this->avaiable_price;
            }  
        }
        
           
    }
    
    public function get_mod_name($role_name = ''){
        $name = $this->get_option(rbp_key.'role_name');
        $registered_roles = $this->get_registered_roles();
        
        if(!empty($name)){
            if(isset($name[$role_name]) && ! empty($name[$role_name])){
                return $name[$role_name];
            } else {
                if(isset($registered_roles[$role_name]['name'])){
                    return $registered_roles[$role_name]['name'];
                }
            }
        }  else {
            if(isset($registered_roles[$role_name]['name'])){
                return $registered_roles[$role_name]['name'];
            }
        }
        
    }
     
    
    public function get_activated_plugin(){
        $plugins = $this->get_option(rbp_key.'activated_plugin');
        if($plugins)
            return $plugins;
        
        return array();
    }
    
    public function get_plugins_list(){
    return array(
            'wpallimport' => array(
                'title'     => 'WP All Importer Integration',
                'description'    => 'Adds Option To Import Products With Role Based Pricing In WP All Importer <br/>
<a href="http://www.wpallimport.com/" >Go To Plugin Website -> </a> ',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'WP All Import - WooCommerce Add-On Pro',
                'actions' => 'wpai-woocommerce-add-on/wpai-woocommerce-add-on.php',
                'update' => '',
                'file' => 'class-wp-all-import-pro-Integration.php',
                'slug' => 'wpallimport',
                'testedupto' => 'V 4.1.6'
            ),
            'aeliacurrency' => array(
                'title'     => 'ACS Currency Switcher Integration',
                'description'    => 'Adds Option Set Product Price Based On Currency Choosen <br/> <a href="https://aelia.co/shop/currency-switcher-woocommerce/" >Go To Plugin Website -> </a>',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'Aelia Currency Switcher for WooCommerce',
                'actions' => 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php',
                'update' => '16th SEP 2015',
                'file' => 'class-aelia-currencyswitcher-Integration.php',
                'slug' => 'aeliacurrency',
                'testedupto' => 'V 3.8.4'
            ),
           'aeliacurrency_wpallimport' => array(
                'title'     => 'ACS Integration With [WP ALL Import]',
                'description'    => 'Intergates Aelia Currency Switcher With WP All Import Plugin',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => array('Aelia Currency Switcher','WP All Import - WooCommerce Add-On Pro'),
                'actions' => array('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php','wpai-woocommerce-add-on/wpai-woocommerce-add-on.php'),
                'update' => '',
                'file' => 'class-wc-rbp-wp-all-import-aelia-Integration.php',
                'slug' => 'aeliacurrency_wpallimport',
                'testedupto' => 'ACS : V 3.8.4 <br/> WPALLIMPORT : V 4.1.6'
            ),
            'woocommerce_product_addon_intergation' =>  array(
                'title'     => 'WooCommerce Product Addon',
                'description'    => 'Intergates With WooCommerce Product Addon',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => array('WooCommerce Product Addons'),
                'actions' => 'woocommerce-product-addons/woocommerce-product-addons.php',
                'update' => '',
                'file' => 'class-woocommerce-product-addons.php',
                'slug' => 'woocommerce_product_addon_intergation',
                'testedupto' => 'WooCommerce Product Addons V 2.6.6 or greater'
            )
        );
    
    }

    
}



/**
 * Check if WooCommerce is active 
 * if yes then call the class
 */

#add_action( 'init', 'wc_rbp_run' );

#function wc_rbp_run(){
    if(! function_exists('is_plugin_active')){ require_once( ABSPATH . '/wp-admin/includes/plugin.php' ); }
    
    if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
        if(! function_exists( 'WC_RBP' )){
            function WC_RBP(){ return WooCommerce_Role_Based_Price::get_instance(); }
        }
		WC_RBP();
        $GLOBALS['woocommerce'] = WC_RBP();
        

    } else {
        add_action( 'admin_notices', 'wc_rbp_activate_failed_notice' );
    }
#}




function wc_rbp_activate_failed_notice() {
	echo '<div class="error"><p> '.__('<strong> <i> WooCommerce Role Based Pricing </i> </strong> Requires',WC_RBP_TXT).'<a href="'.admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce').'"> <strong>'.__(' <u>Woocommerce</u>',WC_RBP_TXT).'</strong>  </a> '.__(' To Be Installed And Activated',WC_RBP_TXT).' </p></div>';
} 

