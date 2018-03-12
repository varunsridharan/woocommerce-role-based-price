<?php
/**
 * Plugin's Admin code
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price_Admin {

    /**
     * Initialize the class and set its properties.
     *
     * @since      0.1
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_styles' ), 99);
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ));
        add_action('admin_init', array( $this, 'admin_init' ));
        add_filter('woocommerce_screen_ids', array( $this, 'set_wc_screen_ids' ), 99);
        add_filter('plugin_row_meta', array( $this, 'plugin_row_links' ), 10, 2);
        add_filter('plugin_action_links_' . WC_RBP_FILE, array( $this, 'plugin_action_links' ), 10, 10);
        //add_action( 'admin_menu',array($this,'add_welcome_menu'));
    }


    public function set_wc_screen_ids($screens) {
        $screen   = $screens;
        $screen[] = 'woocommerce_page_woocommerce-role-based-price-settings';
        $screen[] = 'product_page_rbp_global_addons';
        return $screen;
    }

    /**
     * Inits Admin Sttings
     */
    public function admin_init() {
        //$this->handle_welcome_page();


        new WooCommerce_Role_Based_Price_Admin_Ajax_Handler;
        new WooCommerce_Role_Based_Price_Addons;
    }

    public function handle_welcome_page() {
        if( ! get_transient('_welcome_redirect_wcrbp') ) {
            return;
        }

        delete_transient('_welcome_redirect_wcrbp');

        if( is_network_admin() || isset($_GET['activate-multi']) ) {
            return;
        }
        wp_safe_redirect(add_query_arg(array( 'page' => 'wcrbp_welcome_page' ), admin_url('plugins.php')));
    }

    public function add_welcome_menu() {
        if( ! get_transient('_welcome_redirect_wcrbp') ) {
            //return;
        }

        if( is_network_admin() || isset($_GET['activate-multi']) ) {
            return;
        }

        add_submenu_page('plugins.php', __('WC Role Based Price Welcome Page', 'WPW'), __('WC Role Based Price Welcome Page', 'WPW'), 'read', 'wcrbp_welcome_page', array(
            $this,
            'wcrbp_welcome_page_content',
        ));

    }

    public function wcrbp_welcome_page_content() {
        include( WC_RBP_ADMIN . 'views/plugin-welcome-page.php' );
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        $current_screen = wc_rbp_current_screen();
        $addon_url      = admin_url('admin-ajax.php?action=wc_rbp_addon_custom_css');

        wp_register_style(WC_RBP_SLUG . '_backend_style', WC_RBP_CSS . 'backend.css', array(), WC_RBP_V, 'all');
        wp_register_style(WC_RBP_SLUG . '_addons_style', $addon_url, array(), WC_RBP_V, 'all');
        wp_register_style(WC_RBP_SLUG . '_settings_selectize_style', WC_RBP_CSS . 'selectize.js.css', array(), WC_RBP_V, 'all');
        wp_register_style(WC_RBP_SLUG . '_tabs_style', WC_RBP_CSS . 'tabs.css', array(), WC_RBP_V, 'all');

        wp_enqueue_style(WC_RBP_SLUG . '_backend_style');
        wp_enqueue_style(WC_RBP_SLUG . '_addons_style');


        if( 'woocommerce_page_woocommerce-role-based-price-settings' == $current_screen ) {
            wp_enqueue_style(WC_RBP_SLUG . '_settings_selectize_style');
            add_thickbox();
        }

        if( 'product' == $current_screen ) {
            wp_enqueue_style(WC_RBP_SLUG . '_tabs_style');
            wp_enqueue_style(WC_RBP_SLUG . '_settings_selectize_style');
        }

        do_action('wc_rbp_admin_styles', $current_screen);
    }


    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        $current_screen = wc_rbp_current_screen();

        $addon_url = admin_url('admin-ajax.php?action=wc_rbp_addon_custom_js');

        wp_register_script(WC_RBP_SLUG . '_backend_script', WC_RBP_JS . 'backend.js', array( 'jquery' ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_addons_script', $addon_url, array( 'jquery' ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_settings_selectize.js', WC_RBP_JS . 'selectize.js', array( 'jquery' ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_settings_checkbox.js', WC_RBP_JS . 'checkbox.js', array( 'jquery' ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_settings_js', WC_RBP_JS . 'settings-page.js', array(
            'jquery',
            WC_RBP_SLUG . '_settings_selectize.js',
        ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_settings_checkbox.js', WC_RBP_JS . 'checkbox.js', array( 'jquery' ), WC_RBP_V, FALSE);
        wp_register_script(WC_RBP_SLUG . '_jquery-tabs-script', WC_RBP_JS . 'tabs.js', array( 'jquery' ), WC_RBP_V, FALSE);

        wp_enqueue_script(WC_RBP_SLUG . '_backend_script', WC_RBP_JS . 'backend.js', array( 'jquery' ), WC_RBP_V, FALSE);


        if( in_array($current_screen, wc_rbp_get_screen_ids()) ) {
        }

        if( 'woocommerce_page_woocommerce-role-based-price-settings' == $current_screen ) {
            wp_enqueue_script(WC_RBP_SLUG . '_settings_selectize.js');
            wp_enqueue_script(WC_RBP_SLUG . '_settings_checkbox.js');
            wp_enqueue_script(WC_RBP_SLUG . '_settings_js');
        }

        if( 'product' == $current_screen ) {
            wp_enqueue_script(WC_RBP_SLUG . '_settings_checkbox.js');
            wp_enqueue_script(WC_RBP_SLUG . '_jquery-tabs-script');
            wp_enqueue_script(WC_RBP_SLUG . '_jquery-product-script');
            wp_enqueue_script(WC_RBP_SLUG . '_settings_selectize.js');
        }

        do_action('wc_rbp_admin_scripts', $current_screen);

        wp_enqueue_script(WC_RBP_SLUG . '_addons_script', $addon_url, array( 'jquery' ), WC_RBP_V, FALSE);
    }


    /**
     * Adds Some Plugin Options
     *
     * @param  array  $plugin_meta
     * @param  string $plugin_file
     *
     * @since 0.11
     * @return array
     */
    public function plugin_action_links($action, $file, $plugin_meta, $status) {
        $url      = admin_url('admin.php?page=woocommerce-role-based-price-settings');
        $addonurl = admin_url('admin.php?page=woocommerce-role-based-price-settings&tab=wcrbpaddons');

        $actions[] = sprintf('<a href="%s">%s</a>', $url, __('Settings', WC_RBP_TXT));
        $actions[] = sprintf('<a href="%s">%s</a>', $addonurl, __('Add-ons', WC_RBP_TXT));

        $action = array_merge($actions, $action);
        return $action;
    }

    /**
     * Adds Some Plugin Options
     *
     * @param  array  $plugin_meta
     * @param  string $plugin_file
     *
     * @since 0.11
     * @return array
     */
    public function plugin_row_links($plugin_meta, $plugin_file) {
        if( WC_RBP_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('Docs', WC_RBP_TXT));
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/varunsridharan/woocommerce-role-based-price', __('View On Github', WC_RBP_TXT));
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/varunsridharan/woocommerce-role-based-price/issues', __('Report Issue', WC_RBP_TXT));
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', 'https://www.paypal.me/varunsridharan23', __('Donate', WC_RBP_TXT));
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author', WC_RBP_TXT));
        }
        return $plugin_meta;
    }
}
