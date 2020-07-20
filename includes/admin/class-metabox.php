<?php

namespace WC_RBP\Admin;

use VSP\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Metabox
 *
 * @package WC_RBP\Admin
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Metabox extends Base {

	/**
	 * Metabox constructor.
	 */
	public function __construct() {
		$this->add_action( 'wponion/metabox/render/_wc_role_based_price/before', 'before_hook', 10 );
		$instance = wc_rbp()->_instance( '\WC_RBP\Admin\Price_Fields' );
		wponion_metabox( array(
			'metabox_id'    => 'role-based-price-editor',
			'option_name'   => '_wc_role_based_price',
			'save_type'     => '\WC_RBP\Admin\Save_Handler\WPO_Metabox',
			'screens'       => 'product',
			'assets'        => array( 'wcrbp-admin', 'selectize' ),
			'metabox_title' => __( 'Role Based Price Editor' ),
		), array( $instance, 'get' ) );
	}

	/**
	 * @param int|string $post_id
	 */
	public function before_hook( $post_id ) {
		wc_rbp()->_instance( '\WC_RBP\Admin\Metabox_Sub_Product_Selector', $post_id );
	}
}
