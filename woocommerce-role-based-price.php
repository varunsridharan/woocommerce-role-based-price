<?php
/**
 * Plugin Name:       WooCommerce Role Based Price New
 * Plugin URI:        @TODO
 * Description:       Set WooCommerce Product Price Based On User Role
 * Version:           1.0
 * Author:            varunms
 * Author URI:        http://varunsridharan.in
 * Text Domain:       wc_role_based_price
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: @TODO
 */

if ( ! defined( 'WPINC' ) ) { die; }
define('WC_RBP_NAME','WC Role Based Price',true); # Plugin Name
define('WC_RBP_SLUG','wc-role-based-price',true); # Plugin Slug
define('WC_RBP_VERSION','0.3',true); # Plugin Version
define('WC_RBP_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
define('WC_RBP_ADMIN_CSS',WC_RBP_PATH.'admini/css/'); # Plugin DIR
define('WC_RBP_ADMIN_JS',WC_RBP_PATH.'admini/js/'); # Plugin DIR
define('rbp_key','wc_rbp_'); # PLugin DB Prefix
define('WC_DB_KEY',rbp_key); # Plugin Prefix


require('util.php');
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
        add_action( 'init', array( $this, 'init' ), 0 );
        
        // Autoload Required Files
        foreach( glob(WC_RBP_PATH . 'includes/*.php' ) as $files ){
            require_once( $files );
        }

        if(is_admin()){
            
            require_once(WC_RBP_PATH . 'admin/class-admin-init.php' );
        }
    }
    
    /**
     * Runs After WP Loaded
     */
    public function init(){
        if(is_admin()){
            $this->admin_init();
        } 
        new WooCommerce_Role_Based_Price_Simple_Product_Functions;
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
		return $user_role;
	}
    
    /**
     * Returns Admin Class Instance
     */
    public function admin(){
        return self::$admin_instance;
    }
    
    private function get_option($key){
        return get_option($key);
    }
    
    /**
     * Returns User Selected / Defined Roles From Settings
     * @return [[Type]] [[Description]]
     */
    public function get_allowed_roles(){
        $db_roles = $this->get_option(WC_DB_KEY.'list_roles');
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
        $allowed_price = $this->get_option(WC_DB_KEY.'allowed_price');

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
    
    
}

if(! function_exists( 'WC_RBP' )){
    function WC_RBP(){ 
        return WooCommerce_Role_Based_Price::get_instance();
    }
}
// Global for backwards compatibility.
$GLOBALS['woocommerce'] = WC_RBP();

do_action( 'wc_rbp_loaded' );