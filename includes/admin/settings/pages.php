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
global $pages;
$pages[] = array('id'=>'settings_general','slug'=>'general','title'=>__('General',WC_RBP_TXT));
$pages[] = array('id'=>'settings_message','slug'=>'message','title'=>__('Message',WC_RBP_TXT));
$pages[] = array('id'=>'settings_shortcode','slug'=>'shortcode','title'=>__('ShortCode',WC_RBP_TXT));