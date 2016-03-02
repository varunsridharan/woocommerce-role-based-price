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
global $section;

$section['settings_general'][] = array(
	'id'=>'general',
	'title'=>'', 
	'validate_callback' =>array( $this, 'validate_section' )
);

$section['settings_message'][] = array(
    'id'=>'message',
    'title'=>'Donation Error :', 
    'desc' => '',
    'validate_callback'=>array( $this, 'validate_section' ),
);


$section['settings_shortcode'][] = array(
    'id'=>'shortcode',
    'title'=>'', 
    'desc' => '',
    'validate_callback'=>array( $this, 'validate_section' ),
); 