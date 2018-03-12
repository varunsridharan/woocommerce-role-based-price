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

class WooCommerce_Role_Based_Price_Admin_Settings_Options {

    public function __construct() {
        add_filter('wc_rbp_settings_pages', array( $this, 'settings_pages' ));
        add_filter('wc_rbp_settings_section', array( $this, 'settings_section' ));
        add_filter('wc_rbp_settings_fields', array( $this, 'settings_fields' ));
    }

    public function settings_pages($page) {
        $page[] = array( 'id' => 'general', 'slug' => 'general', 'title' => __('General', WC_RBP_TXT) );
        $page[] = array(
            'id'    => 'addonssettings',
            'slug'  => 'addonssettings',
            'title' => __('Extensions Options', WC_RBP_TXT),
        );
        $page[] = array( 'id' => 'addons', 'slug' => 'wcrbpaddons', 'title' => __('Extensions', WC_RBP_TXT) );
        return $page;
    }

    public function settings_section($section) {
        $section['general'][] = array( 'id' => 'general', 'title' => __('General', WC_RBP_TXT) );
        $section['addons'][]  = array( 'id' => 'addons', 'title' => '' );
        $addonSettings        = array(
            'addon_sample' => array(
                'id'    => 'addonssettings',
                'title' => __('No Addons Activated / Installed.', WC_RBP_TXT),
            ),
        );
        $addonSettings        = apply_filters('wc_rbp_addon_sections', $addonSettings);

        if( count($addonSettings) > 1 )
            unset($addonSettings['addon_sample']);
        $section['addonssettings'] = $addonSettings;
        return $section;
    }

    public function settings_fields($fields) {
        $fields['general']['general'][] = array(
            'id'       => WC_RBP_DB . 'allowed_roles',
            'multiple' => 'true',
            'type'     => 'select',
            'label'    => __('Allowed User Roles', WC_RBP_TXT),
            'desc'     => __('User Roles To List In Product Edit Page', WC_RBP_TXT),
            'options'  => wc_rbp_sort_array_by_array(wc_rbp_get_user_roles_selectbox(), wc_rbp_allowed_roles()),
            'attr'     => array(
                'class'    => 'wc-rbp-enhanced-select',
                'multiple' => 'multiple',
            ),
        );


        $fields['general']['general'][] = array(
            'id'       => WC_RBP_DB . 'allowed_price',
            'type'     => 'select',
            'multiple' => TRUE,
            'label'    => __('Allowed Product Pricing', WC_RBP_TXT),
            'desc'     => __('Price Fields To List In Product Edit Page', WC_RBP_TXT),
            'options'  => wc_rbp_sort_array_by_array(wc_rbp_avaiable_price_type(), wc_rbp_allowed_price()),
            'attr'     => array(
                'class'    => 'wc-rbp-enhanced-select',
                'style'    => 'width:auto;max-width:35%;',
                'multiple' => 'multiple',
            ),
        );

        $price_type = wc_rbp_avaiable_price_type();

        foreach( $price_type as $pK => $pV ) {
            $fields['general']['general'][] = array(
                'id'      => WC_RBP_DB . $pK . '_label',
                'type'    => 'text',
                'label'   => $pV . __(' Label ', WC_RBP_TXT),
                'default' => $pV,
                'attr'    => array(
                    'style' => 'width:auto;max-width:35%;',
                ),
            );
        }

        if( class_exists('woocommerce_wpml') ) {
            $fields['general']['general'][] = array(
                'id'    => WC_RBP_DB . 'enable_wpml_integration',
                'type'  => 'checkbox',
                'label' => __('WPML Integration', WC_RBP_TXT),
                'desc'  => __('check if you have installed wpml and the price are showing wrong. ', WC_RBP_TXT),
                'attr'  => array( 'class' => 'wc_rbp_checkbox', ),
            );
        }


        $addonSettings = array( 'addon_sample' => array() );
        $addonSettings = apply_filters('wc_rbp_addon_fields', $addonSettings);
        unset($addonSettings['addon_sample']);
        $fields['addonssettings'] = $addonSettings;

        return $fields;
    }

}

return new WooCommerce_Role_Based_Price_Admin_Settings_Options;