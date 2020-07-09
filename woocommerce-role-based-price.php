<?php
/**
 * Plugin Name:       Role Based Price For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/woocommerce-role-based-price/
 * Description:       Sell product in different price for different user role based on your settings.
 * Version:           4.0
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       wc-role-based-price
 * Domain Path:       /i18n/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/varunsridharan/woocommerce-role-based-price/
 */

defined( 'ABSPATH' ) || exit;

defined( 'WC_RBP_FILE' ) || define( 'WC_RBP_FILE', __FILE__ );
defined( 'WC_RBP_VERSION' ) || define( 'WC_RBP_VERSION', '4.0' );
defined( 'WC_RBP_NAME' ) || define( 'WC_RBP_NAME', __( 'Role Based Price For WooCommerce' ) );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( function_exists( 'wponion_load' ) ) {
	wponion_load( __DIR__ . '/vendor/wponion/wponion' );
}

if ( function_exists( 'vsp_maybe_load' ) ) {
	vsp_maybe_load( 'wc_rbp_init', __DIR__ . '/vendor/varunsridharan/' );
}

register_activation_hook( WC_RBP_FILE, 'wc_rbp_installer' );

if ( ! function_exists( 'wc_rbp_installer' ) ) {
	/**
	 * Runs Installer Script.
	 */
	function wc_rbp_installer() {
		require_once __DIR__ . '/includes/db/class-price.php';
		require_once __DIR__ . '/includes/db/class-price-meta.php';
		require_once __DIR__ . '/installer/class-installer.php';
	}
}

if ( ! function_exists( 'wc_rbp_init' ) ) {
	/**
	 * Inits The Plugin.
	 */
	function wc_rbp_init() {
		if ( ! vsp_add_wc_required_notice( WC_RBP_NAME ) ) {
			require_once __DIR__ . '/bootstrap.php';
			wc_rbp();
		}
	}
}

if ( ! function_exists( 'wc_rbp' ) ) {
	/**
	 * Returns A New instance.
	 *
	 * @return \WC_RBP
	 */
	function wc_rbp() {
		return WC_RBP::instance();
	}
}
