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
define('WC_RBP_SLUG','wc-role-based-price',true);
define('WC_RBP_VERSION','0.3',true);
define('WC_RBP_PATH',plugin_dir_path( __FILE__ ));
define('rbp_key','wc_rbp_');
define('WC_DB_KEY',rbp_key);
require('util.php');
/**
 * Class to initiate the plugin
 */
final class  WooCommerce_Role_Based_Price{
    
    private static $_instance = null;
    public static $admin_instance = null;
    
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
        // Autoload Required Files
        foreach( glob(WC_RBP_PATH . 'includes/*.php' ) as $files ){
            require_once( $files );
        }

        if(is_admin()){
            require_once(WC_RBP_PATH . 'admin/class-admin-init.php' );
        }
        
        $this->admin_init();
        
        add_action( 'init', array( $this, 'init' ), 0 );
    }
    
    /**
     * Runs After WP Loaded
     */
    public function init(){
        $this->admin_init();
    }
    
    /**
     * Inits Admin Class
     */
    public function admin_init(){
        self::$admin_instance = WooCommerce_Role_Based_Price_Admin::get_instance();
        do_action('wc_role_based_admin_init');
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
     * Returns Admin Class Instance
     */
    public function admin(){
        return self::$admin_instance;
    }
    
    
}

if(! function_exists( 'WC_RBP' )){
    function WC_RBP(){ 
        return WooCommerce_Role_Based_Price::get_instance();
    }
}
// Global for backwards compatibility.
$GLOBALS['woocommerce'] = WC_RBP();