<?php
/**
 * class for form fields display.
 * For detailed instructions see: https://github.com/keesiemeijer/WP-Settings
 *
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/WordPress/Settings
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }
global $fields;

/** General Settings **/
$fields['settings_general']['general'][] = array(
    'id'      =>  WC_RBP_DB.'redirect_user',
    'type'    => 'select',
    'label'   => __( 'Redirect User To', WC_RBP_TXT),
    'desc'    => __( 'After Donation Added To Cart',WC_RBP_TXT),
    'size '   => 'small',
    'options' => array('cart' => __('Cart Page',WC_RBP_TXT) , 'checkout' => __('Checkout Page',WC_RBP_TXT)),
    'attr'    => array('class' => 'wc-enhanced-select','style' => 'width:auto;max-width:35%;')
); 


/** Message Settings **/
$fields['settings_message']['message'][] =  array(
	'desc'  => sprintf(__( '<span> Add <code>%s</code> To Get Ented Amount By User.</span>  <br/>
               <span> Add <code>%s</code> To Get Minimum Required Amount From Selected Project </span>   <br/>
               <span> Add <code>%s</code> To Get Minimum Required Amount From Selected Project  </span>',WC_RBP_TXT),'{donation_amount}','{min_amount}','{max_amount}'),
	'id'    =>  WC_RBP_DB.'empty_donation_msg_1',
    'attr'  => array('style' => 'min-width:50%; width:auto;max-width:75%;'),
	'type'  => 'content'
);


$fields['settings_message']['message'][] =  array(
	'label' => __( 'Donation Conflict', WC_RBP_TXT),
	'desc'  => __( 'Custom Message To Show When User Trying To Add Donation With Other Products',WC_RBP_TXT),
	'id'    =>   WC_RBP_DB.'donation_with_other_products',
    'attr'  => array('style' => 'min-width:50%; width:auto;max-width:75%;'),
	'type'  => 'textarea'
);


/** Shortcode Settings **/
$fields['settings_shortcode']['shortcode'][] = array(
	'id'      =>  WC_RBP_DB.'default_render_type',
    'type'    => 'select',
    'label'   => __( 'Pre Selected Project Name', WC_RBP_TXT),
    'desc'    => __( 'default project render type',WC_RBP_TXT),
    'size '   => 'small',
    'options' => array('select' => __('Select Box',WC_RBP_TXT), 'radio' => __('Radio Button',WC_RBP_TXT)),
    'attr'    => array('class' => 'wc-enhanced-select','style' => 'width:auto;max-width:35%;')		
);

$fields['settings_shortcode']['shortcode'][] = array(
	'id'      =>  WC_RBP_DB.'shortcode_show_errors',
    'type'    => 'select',
    'label'   => __( 'Show Errors', WC_RBP_TXT),
    'desc'    => __( 'Set to hide errors when <code> wc_print_notice</code> called before loading dontion form',WC_RBP_TXT),
    'size '   => 'small',
    'options' => array('true' => __('Show Errors',WC_RBP_TXT), 'false' => __('Hide Errors',WC_RBP_TXT)),
    'attr'    => array('class' => 'wc-enhanced-select','style' => 'width:auto;max-width:35%;')		
);