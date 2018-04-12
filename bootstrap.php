<?php
/**
 * Plugin Main File
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price {
    protected static $_instance         = NULL;
    protected static $functions         = NULL;
    protected static $admin             = NULL; # Required Plugin Class Instance
    protected static $settings          = NULL; # Required Plugin Class Instance
    protected static $frontend          = NULL;     # Required Plugin Class Instance
    protected static $shortcode_handler = NULL;  # Required Plugin Class Instance
    public           $version           = '3.3.2';  # Required Plugin Class INstance
    public           $plugin_vars       = array();  # Required Plugin Class INstance

    /**
     * Class Constructor
     */
    public function __construct() {
        $this->define_constant();
        $this->load_required_files();
        $this->init_hooks();
        do_action('wc_rbp_loaded');
    }

    /**
     * Define Required Constant
     */
    private function define_constant() {
        $this->define('WC_RBP_NAME', 'WooCommerce Role Based Price'); # Plugin Name
        $this->define('WC_RBP_SLUG', 'woocommerce-role-based-price'); # Plugin Slug
        $this->define('WC_RBP_TXT', 'woocommerce-role-based-price'); #plugin lang Domain
        $this->define('WC_RBP_DB', 'wc_rbp_');
        $this->define('WC_RBP_V', $this->version); # Plugin Version

        $this->define('WC_RBP_LANGUAGE_PATH', WC_RBP_PATH . 'languages'); # Plugin Language Folder
        $this->define('WC_RBP_ADMIN', WC_RBP_INC . 'admin/'); # Plugin Admin Folder
        $this->define('WC_RBP_SETTINGS', WC_RBP_ADMIN . 'settings/'); # Plugin Settings Folder
        $this->define('WC_RBP_PLUGIN', WC_RBP_PATH . 'plugins/');

        $this->define('WC_RBP_URL', plugins_url('', __FILE__) . '/');  # Plugin URL
        $this->define('WC_RBP_PLUGIN_URL', WC_RBP_URL . 'plugins/');  # Plugin URL
        $this->define('WC_RBP_CSS', WC_RBP_URL . 'includes/css/'); # Plugin CSS URL
        $this->define('WC_RBP_IMG', WC_RBP_URL . 'includes/img/'); # Plugin IMG URL
        $this->define('WC_RBP_JS', WC_RBP_URL . 'includes/js/'); # Plugin JS URL
    }

    /**
     * Define constant if not already set
     *
     * @param  string      $name
     * @param  string|bool $value
     */
    protected function define($key, $value) {
        if( ! defined($key) ) {
            define($key, $value);
        }
    }

    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files() {
        $this->load_files(WC_RBP_INC . 'abstract-*.php');
        $this->load_files(WC_RBP_INC . 'helpers/class-admin-notice.php');
        $this->load_files(WC_RBP_INC . 'class-*.php');
        $this->load_files(WC_RBP_ADMIN . 'settings_framework/class-wp-plugin-options.php');
        $this->load_files(WC_RBP_ADMIN . 'settings_framework/class-wp-*.php');

        if( wc_rbp_is_request('admin') ) {
            $this->load_files(WC_RBP_ADMIN . 'class-*.php');
        }

        do_action('wc_rbp_before_addons_load');
        $this->load_addons();

    }

    /**
     * Loads Files Based On Give Path & regex
     */
    protected function load_files($path, $type = 'require') {
        foreach( glob($path) as $files ) {
            if( $type == 'require' ) {
                require_once( $files );
            } else if( $type == 'include' ) {
                include_once( $files );
            }
        }
    }

    public function load_addons() {
        $addons = wc_rbp_get_active_addons();
        if( ! empty($addons) ) {
            foreach( $addons as $addon ) {
                if( apply_filters('wc_rbp_load_addon', TRUE, $addon) ) {
                    do_action('wc_rbp_before_' . $addon . '_addon_load');
                    $this->load_addon($addon);
                    do_action('wc_rbp_after_' . $addon . '_addon_load');
                }
            }
        }
    }

    public function load_addon($file) {
        $other_file = apply_filters('wc_rbp_addon_file_location', $file);

        if( file_exists(WC_RBP_PLUGIN . $file) ) {
            $this->load_files(WC_RBP_PLUGIN . $file);
        } else if( file_exists($other_file) ) {
            $this->load_files($other_file);
        } else {
            if( has_action('wc_rbp_addon_' . $file . '_load') ) {
                do_action('wc_rbp_addon_' . $file . '_load');
            } else {
                wc_rbp_deactivate_addon($file);
            }
        }

    }

    public function init_hooks() {
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile', array( $this, 'load_plugin_mo_files' ), 10, 2);
        add_action('init', array( $this, 'init' ), 0);
    }

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if( NULL == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    # Returns Plugin's Functions Instance

    /**
     * Throw error on object clone.
     *
     * Cloning instances of the class is forbidden.
     *
     * @since 1.0
     * @return void
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cloning instances of the class is forbidden.', WC_RBP_TXT), WC_RBP_V);
    }

    # Returns Plugin's Functions Instance

    /**
     * Disable unserializing of the class
     *
     * Unserializing instances of the class is forbidden.
     *
     * @since 1.0
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Unserializing instances of the class is forbidden.', WC_RBP_TXT), WC_RBP_V);
    }

    # Returns Plugin's Settings Instance

    /**
     * Inits loaded Class
     */
    public function init() {
        do_action('wc_rbp_before_init');

        self::$functions         = new WooCommerce_Role_Based_Price_Functions;
        self::$settings          = new WooCommerce_Role_Based_Price_Settings_Framework;
        self::$shortcode_handler = new WooCommerce_Role_Based_Price_Shortcode_Handler;

        if( wc_rbp_is_request('admin') ) {
            self::$admin = new WooCommerce_Role_Based_Price_Admin;
        } else {
            self::$frontend = new WooCommerce_Role_Based_Price_Product_Pricing;
        }

        do_action('wc_rbp_init');
    }

    # Returns Plugin's Admin Instance

    public function func() {
        return self::$functions;
    }

    public function frontend() {
        return self::$frontend;
    }

    public function settings() {
        return self::$settings;
    }

    public function admin() {
        return self::$admin;
    }

    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded() {
        load_plugin_textdomain(WC_RBP_TXT, FALSE, WC_RBP_LANGUAGE_PATH);
    }

    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if( WC_RBP_TXT === $domain ) {
            return WC_RBP_LANGUAGE_PATH . '/' . get_locale() . '.mo';
        }
        return $mofile;
    }

}