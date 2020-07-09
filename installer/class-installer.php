<?php

defined( 'ABSPATH' ) || exit;

use Varunsridharan\WordPress\Plugin_Version_Management;
use WC_RBP\DB\Price;
use WC_RBP\DB\Price_Meta;

if ( ! class_exists( 'WC_RBP_Installer' ) ) {
	/**
	 * Class WC_RBP_Installer
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 */
	final class WC_RBP_Installer {
		/**
		 * Creates Basic Tables.
		 */
		public static function upgrade_v4_0() {
			$instance = Price::instance();
			$instance->maybe_upgrade();

			$instance = Price_Meta::instance();
			$instance->maybe_upgrade();
		}
	}
}

$instance = new Plugin_Version_Management( array(
	'slug'    => 'rbp-for-wc',
	'version' => WC_RBP_VERSION,
	'logs'    => false,
), array( '4.0' => array( 'WC_RBP_Installer', 'upgrade_v4_0' ) ) );
$instance->run();
