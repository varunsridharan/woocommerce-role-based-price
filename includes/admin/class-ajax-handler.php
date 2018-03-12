<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price_Admin_Ajax_Handler {

    public function __construct() {
        add_action('wp_ajax_wc_rbp_clear_variation_cache', array( $this, 'clear_variation_cache' ));
        add_action('wp_ajax_wc_rbp_save_product_prices', array( $this, 'save_product_rbp_price' ));
        add_action('wp_ajax_nopriv_wc_rbp_addon_custom_css', array( $this, 'render_addon_css' ));
        add_action('wp_ajax_wc_rbp_addon_custom_css', array( $this, 'render_addon_css' ));
        add_action('wp_ajax_nopriv_wc_rbp_addon_custom_js', array( $this, 'render_addon_js' ));
        add_action('wp_ajax_wc_rbp_addon_custom_js', array( $this, 'render_addon_js' ));

        add_action('wp_ajax_wc_rbp_metabox_refersh', array( $this, 'refresh_metabox' ));
    }

    public function clear_variation_cache() {
        if( ! isset($_REQUEST['post_id']) ) {
            wp_send_json_error(__('Invalid Product ID', WC_RBP_TXT));
        }
        $id = $_REQUEST['post_id'];

        $parent = wp_get_post_parent_id($id);
        if( $parent !== FALSE ) {
            $allowed_roles = array_keys(wc_rbp_get_user_roles_selectbox());
            foreach( $allowed_roles as $role ) {
                wc_rbp_delete_variation_data($id, $role);
            }
        }

        wp_send_json_success(array( __("Success") ));
    }

    public function refresh_metabox() {
        if( ! isset($_REQUEST['pid']) ) {
            wp_send_json_error(__('Invalid Product ID', WC_RBP_TXT));
        }

        if( ! isset($_REQUEST['parentID']) ) {
            wp_send_json_error(__('Invalid Product ID', WC_RBP_TXT));
        }

        $id       = $_REQUEST['pid'];
        $parentid = $_REQUEST['parentID'];
        $metabox  = new WooCommerce_Role_Based_Price_Product_Metabox;
        ob_start();
        //$metabox->generate_variation_selectbox($parentid,$id);
        $metabox->render_price_editor_metabox($id);
        $content = ob_get_contents();
        ob_end_clean();
        wp_send_json_success($content);
        wp_die();
    }

    public function render_addon_css() {
        header('Content-Type: text/css');
        do_action('wc_rbp_addon_styles');
        wp_die();
    }

    public function render_addon_js() {
        header('Content-Type: text/javascript');
        do_action('wc_rbp_addon_scripts');
        wp_die();
    }

    public function save_product_rbp_price() {
        $is_verifyed_nounce = wp_verify_nonce($_POST['wc_rbp_nounce'], 'wc_rbp_save_product_prices');
        $error              = array();
        $type               = isset($_POST['type']) ? $_POST['type'] : 'default';
        $success            = array( 'hidden_fields' => wc_rbp_get_editor_fields($type) );
        $posted_values      = $_POST;

        if( $is_verifyed_nounce ) {
            do_action_ref_array('wc_rbp_product_save_' . $type, array( &$posted_values, &$success, &$error ));
        } else {
            $error['html'] = '<h3>' . __("Unable To Process Your Request Please Try Again later", WC_RBP_TXT) . '</h3>';
        }

        if( empty($error) ) {
            wp_send_json_success($success);
        } else {
            $error['hidden_fields'] = wc_rbp_get_editor_fields($type);
            wp_send_json_error($error);
        }
        wp_die();
    }
}