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
		$instance = new Price_Fields();
		wponion_metabox( array(
			'metabox_id'    => 'role-based-price-editor',
			'option_name'   => '_wc_role_based_price',
			'screens'       => 'product',
			'metabox_title' => __( 'Role Based Price Editor' ),
		), array( $instance, 'get' ) );
	}
}
