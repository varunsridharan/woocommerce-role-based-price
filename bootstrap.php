<?php

use VSP\Framework;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_RBP' ) ) {
	/**
	 * Class WC_RBP
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 */
	final class WC_RBP extends Framework {
		/**
		 * WC_RBP constructor.
		 *
		 * @throws \Exception
		 */
		public function __construct() {
			$options                  = array(
				'name'      => WC_RBP_NAME,
				'version'   => WC_RBP_VERSION,
				'db_slug'   => '_wcrbp',
				'hook_slug' => 'wc_rbp',
				'file'      => WC_RBP_FILE,
				'logging'   => false,
				'addons'    => array(
					'base_path'               => $this->plugin_path( 'addons/', WC_RBP_FILE ),
					'base_url'                => $this->plugin_url( 'addons/', WC_RBP_FILE ),
					'addon_listing_tab_title' => __( 'Addons' ),
					'addon_listing_tab_icon'  => ' wpoic-plug',
				),
				'localizer' => false,
			);
			$options['autoloader']    = array(
				'base_path' => $this->plugin_path( 'includes/', WC_RBP_FILE ),
				'namespace' => 'WC_RBP',
			);
			$options['settings_page'] = array(
				'option_name'     => '_wc_role_based_price',
				'framework_title' => __( 'Role Based Price For WooCommerce' ),
				'framework_desc'  => __( 'Sell product in different price for different user role based on your settings.' ),
				'theme'           => 'wp',
				'is_single_page'  => false,
				'ajax'            => true,
				'assets'          => array( 'jquery-ui-sortable' ),
				'menu'            => array(
					'menu_title' => __( 'Role Based Price' ),
					'menu_slug'  => 'wc-rbp',
					'submenu'    => 'woocommerce',
				),
			);
			parent::__construct( $options );
		}

		/**
		 * Settings On Before Init.
		 */
		public function settings_init_before() {
			$this->_instance( '\WC_RBP\Admin\Settings\Settings' );
		}

		/**
		 * Loads Required Files.
		 *
		 * @since {NEWVERSION}
		 */
		protected function load_files() {
			$this->load_file( 'includes/functions/*.php' );
		}
	}
}
