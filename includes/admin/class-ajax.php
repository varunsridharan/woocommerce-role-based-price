<?php

namespace WC_RBP\Admin;

use VSP\Ajaxer;
use WPOnion\Modules\Metabox\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Class Ajax
 *
 * @package WC_RBP\Admin
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Ajax extends Ajaxer {
	/**
	 * Action Name
	 * provide value if all ajax requests runs in a single action key.
	 *
	 * @var string
	 */
	protected $action = 'wcrbp';

	/**
	 * Array of ajax actions
	 *
	 * @example array('ajax_action_1' => true,'ajax_action_2' => false)
	 *          if value set to true then it runs for both loggedout / logged in users
	 *          if value set to false then it runs only for the logged in user
	 *
	 * @example array('ajax_action_1' => array('auth' => false,'callback' => array(CLASSNAME,METHODNAME)))
	 *          if auth value set to true then it runs for both loggedout / logged in users
	 *          if auth value set to false then it runs only for the logged in user
	 *          callback can either be a string,array or a actual dynamic function.
	 *
	 * @var array
	 */
	protected $actions = array( 'reload-metabox' => false );

	/**
	 * Set to true if plugin's ajax runs in a single action
	 * OR
	 * Set a custom key so convert plugin-slug=ajax-action into your-key=ajax-action
	 *
	 * @example Single Ajax Action :
	 *              admin-ajax.php?action=plugin-slug&plugin-slug-action=ajax-action&param1=value1&param2=value=2
	 *          Multiple Ajax Actions :
	 *              admin-ajax.php?action=plugin-slug-ajax-action1&param1=value1=param2=value2
	 *
	 * @example Single Ajax Action :
	 *            admin-ajax.php?action=plugin-slug&custom-key-action=ajax-action&param1=value1&param2=value=2
	 *
	 *          Multiple Ajax Actions:
	 *             admin-ajax.php?action=plugin-slug-ajax-action1&param1=value1=param2=value2
	 *
	 * @var bool
	 */
	protected $is_single = true;

	/**
	 * Callback Used To Reload Metabox.
	 */
	public function reload_metabox() {
		$product_id = $this->validate_request( 'wcrbp_product_id', __( 'Invalid Product ID' ), __( 'No Valid Product ID Provided' ) );
		$this->validate_request( 'wcrbp_sub_product_id', __( 'Invalid Sub Product' ), __( 'Sub Product Not Found' ) );

		/**
		 * @var \WC_RBP\Admin\Metabox $metabox
		 */
		$metabox = wc_rbp()->_instance( '\WC_RBP\Admin\Metabox' );

		if ( $metabox->metabox() instanceof Metabox ) {
			wponion_catch_output( true );
			wponion_localize();
			$metabox->metabox()->set_id( $product_id );
			$metabox->metabox()->on_page_load();
			$metabox->metabox()->render( $product_id );
			$this->json_success( array( 'html' => wponion_catch_output( false ) ) );
		}
	}
}
