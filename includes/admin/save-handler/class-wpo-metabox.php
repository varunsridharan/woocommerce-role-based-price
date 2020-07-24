<?php

namespace WC_RBP\Admin\Save_Handler;

use WC_RBP\DB\Query;
use WC_RBP\Helper;
use WC_RBP\Price_Helper;
use WC_RBP\Traits\Product_Info;
use WPOnion\Bridge\Custom_DB_Storage_Handler;

defined( 'ABSPATH' ) || exit;

/**
 * Class WPO_Metabox
 *
 * @package WC_RBP\Admin\Save_Handler
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class WPO_Metabox extends Custom_DB_Storage_Handler {
	use Product_Info;

	/**
	 * WPO_Metabox constructor.
	 *
	 * @param      $unique
	 * @param      $module
	 * @param bool $object_id
	 */
	public function __construct( $unique, $module, $object_id = false ) {
		parent::__construct( $unique, $module, $object_id );
		$this->setup_product_info( Helper::get_product_id( $this->object_id() ), Helper::get_sub_product_id(), Helper::get_sub_product_type() );
	}

	/**
	 * Returns A Valid DB Save Handler.
	 *
	 * @return false|\WC_RBP\Abstracts\DB_Handler
	 */
	protected function get_db_handler_class() {
		$type = Helper::get_sub_product_type();
		/* @var \WC_RBP\Abstracts\DB_Handler $class */
		$class = false;
		if ( 'variations' === $type && ! empty( Helper::get_sub_product_id() ) ) {
			$class = '\WC_RBP\Admin\Save_Handler\Variations';
		} elseif ( ! empty( $type ) && ! empty( Helper::get_sub_product_id() ) ) {
			$_class = str_replace( array( '-', '/' ), '_', strtolower( $type ) );
			$class  = '\WC_RBP\DB_Handler\\' . $_class;
		} elseif ( empty( $type ) ) {
			$class = '\WC_RBP\Admin\Save_Handler\General';
		}

		$class = wc_rbp()->apply_filter( 'db/price_save_handler', $class, $type, Helper::get_product_id() );

		if ( ! empty( $class ) ) {
			return new $class( $this->object_id(), $this->sub_product_id(), $this->sub_product_type() );
		}
		return false;
	}

	/**
	 * Fetches Value From DB Again.
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function get() {
		$return   = array();
		$instance = $this->get_db_handler_class();

		if ( false !== $instance ) {
			$return                     = $instance->get();
			$return['product_id']       = $this->product_id();
			$return['sub_product_id']   = $this->sub_product_id();
			$return['sub_product_type'] = $this->sub_product_type();
		}
		return $return;
	}

	/**
	 * Saves Values in DB.
	 *
	 * @param array $values
	 *
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function save( $values ) {
		$instance = $this->get_db_handler_class();

		if ( false !== $instance ) {
			$instance->save( $values );
		}
	}
}
