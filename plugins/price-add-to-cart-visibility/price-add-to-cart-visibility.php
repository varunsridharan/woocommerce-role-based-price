<?php
/**
 * Plugin Name: Price & Add To Cart Visibility
 * Plugin URI:
 * Version: 1.0
 * Description: Allows to hide products price & add to cart button based on the user roles.
 * Author: Varun Sridharan
 * Author URI: http://varunsridharan.in
 * Last Update: 2016-03-04
 * Category: Tools
 */


if( ! defined('WC_RBP_PLUGIN') ) {
    die;
}

class Price_add_to_cart_visibility_WC_RBP {

    public function __construct() {
        add_filter('wc_rbp_addon_sections', array( $this, 'add_settings_section' ));
        add_filter('wc_rbp_addon_fields', array( $this, 'add_settings_fields' ));
        add_filter('init', array( $this, 'check_remove_add_to_cart' ), 99);
        add_filter('woocommerce_get_price_html', array( $this, 'remove_price' ), 99, 2);
    }


    public function check_remove_add_to_cart() {
        $current_role    = wc_rbp_get_current_user();
        $resticted_role  = wc_rbp_option('hide_product_addtocart');
        $variable_status = wc_rbp_option('hide_variable_product');

        if( empty($resticted_role) ) {
            return;
        }
        if( in_array($current_role, $resticted_role) ) {
            add_filter('woocommerce_loop_add_to_cart_link', array( &$this, 'remove_add_to_cart_link' ), 99);
            remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
            if( $variable_status ) {
                remove_action('woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30);
            } else {
                remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
            }
        }
    }

    public function remove_add_to_cart_link($link) {
        return '';
    }


    public function remove_price($price) {
        $current_role   = wc_rbp_get_current_user();
        $resticted_role = wc_rbp_option('hide_product_price');

        if( ! empty($resticted_role) ) {
            if( in_array($current_role, $resticted_role) ) {
                $price_notice = wc_rbp_option('pv_custom_message');
                $price_notice = apply_filters('wc_rbp_price_visibility_custom_price_message', $price_notice, $current_role);
                if( ! empty($price_notice) ) {
                    $symbol       = get_woocommerce_currency_symbol();
                    $price_notice = str_replace('[currency]', $symbol, $price_notice);
                    return $price_notice;
                }
                return '';
            }
        }

        return $price;
    }


    public function add_settings_fields($fields) {
        $fields['price_visibility'][] = array(
            'id'       => WC_RBP_DB . 'hide_product_price',
            'multiple' => 'true',
            'type'     => 'select',
            'label'    => __('Hide Price For', WC_RBP_TXT),
            'desc'     => __('Product Price will be hidden for the selected user roles', WC_RBP_TXT),
            'options'  => wc_rbp_get_user_roles_selectbox(),
            'attr'     => array(
                'class'    => 'wc-rbp-enhanced-select',
                'multiple' => 'multiple',
            ),
        );

        $fields['price_visibility'][] = array(
            'id'       => WC_RBP_DB . 'hide_product_addtocart',
            'multiple' => 'true',
            'type'     => 'select',
            'label'    => __('Hide Add To Cart Button', WC_RBP_TXT),
            'desc'     => __('Product Add To Cart Button will be hidden for the selected user roles', WC_RBP_TXT),
            'options'  => wc_rbp_get_user_roles_selectbox(),
            'attr'     => array(
                'class'    => 'wc-rbp-enhanced-select',
                'multiple' => 'multiple',
            ),
        );

        $fields['price_visibility'][] = array(
            'id'    => WC_RBP_DB . 'hide_variable_product',
            'type'  => 'checkbox',
            'label' => __('Hide Product Variations', WC_RBP_TXT),
            'desc'  => __('if checked Variable product variation will be hidden from product page ', WC_RBP_TXT),
            'attr'  => array( 'class' => 'wc_rbp_checkbox', ),
        );


        $fields['price_visibility'][] = array(
            'id'                => WC_RBP_DB . 'pv_custom_message',
            'value'             => html_entity_decode(wc_rbp_option('pv_custom_message')),
            'type'              => 'richtext',
            'richtext_settings' => array( 'textarea_rows' => 5 ),
            'label'             => __('Custom Message', WC_RBP_TXT),
            'desc'              => __('Used when product price is hidden.. use <code>[currency]</code> to get current store currency', WC_RBP_TXT),
            'attr'              => array( 'class' => 'wc_rbp_checkbox', ),
        );

        return $fields;
    }

    public function add_settings_section($section) {
        $c_section          = array();
        $c_section['id']    = 'price_visibility';
        $c_section['title'] = __('Price & AddToCart Visibility', WC_RBP_TXT);
        $c_section['desc']  = __('Hide Product Price & Add To Cart button based on users role and set custom messsage to show if price is hidden', WC_RBP_TXT);
        $section[]          = $c_section;
        return $section;
    }

}

return new Price_add_to_cart_visibility_WC_RBP;