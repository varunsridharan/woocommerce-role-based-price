<?php

namespace WC_RBP\Admin\Save_Handler;

use WC_RBP\DB\Query;
use WC_RBP\Price_Helper;
use WPOnion\Bridge\Custom_DB_Storage_Handler;

defined( 'ABSPATH' ) || exit;

/**
 * Class WPO_Metabox
 *
 * @package WC_RBP\Admin\Save_Handler
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class WPO_Metabox extends Custom_DB_Storage_Handler {

	/**
	 * Fetches Value From DB Again.
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function get() {
		$user_roles = wc_rbp_allowed_roles();
		$return     = array();
		foreach ( $user_roles as $role ) {
			$is_exists = Query::exists( 'product', $this->object_id(), 'core', $role );
			if ( $is_exists ) {
				$return[ $role ] = array();
				$price           = Price_Helper::get( 'product', $this->object_id(), 'core', $role );
				$return[ $role ] = array(
					'sale_price'    => $price->get_sale_price( 'core' ),
					'regular_price' => $price->get_regular_price( 'core' ),
				);
			}
		}
		$return['product_id']     = wponion_get_var( 'wcrbp_product_id', $this->object_id() );
		$return['sub_product_id'] = wponion_get_var( 'wcrbp_sub_product_id', false );
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
		if ( ! empty( $values ) ) {
			foreach ( $values as $user_role => $prices ) {
				$price = Price_Helper::get( 'product', $this->object_id(), 'core', $user_role );
				if ( isset( $prices['sale_price'] ) ) {
					$price->set_sale_price( $prices['sale_price'] );
				}
				if ( isset( $prices['regular_price'] ) ) {
					$price->set_regular_price( $prices['regular_price'] );
				}
				$price->save();
			}
		}
	}
}
