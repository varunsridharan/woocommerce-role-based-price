<?php

namespace WC_RBP\Admin\Save_Handler;

use WC_RBP\Abstracts\DB_Handler;
use WC_RBP\DB\Query;
use WC_RBP\Price_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class General
 *
 * @package WC_RBP\Admin\Save_Handler
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class General extends DB_Handler {

	/**
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function get() {
		$user_roles = wc_rbp_allowed_roles();
		$return     = array();
		foreach ( $user_roles as $role ) {
			$is_exists = Query::exists( 'product', $this->product_id(), 'core', $role );
			if ( $is_exists ) {
				$return[ $role ] = array();
				$price           = Price_Helper::get( 'product', $this->product_id(), 'core', $role );
				$return[ $role ] = array(
					'sale_price'    => $price->get_sale_price( 'core' ),
					'regular_price' => $price->get_regular_price( 'core' ),
				);
			}
		}
		return $return;
	}

	/**
	 * Saves Values in DB.
	 *
	 * @param $values
	 *
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function save( $values ) {
		if ( ! empty( $values ) ) {
			foreach ( $values as $user_role => $prices ) {
				$price = Price_Helper::get( 'product', $this->product_id(), 'core', $user_role );
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
