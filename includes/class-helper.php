<?php

namespace WC_RBP;

defined( 'ABSPATH' ) || exit;

/**
 * Class Helper
 *
 * @package WC_RBP
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Helper {

	/**
	 * @param bool $product_id
	 *
	 * @return bool|string
	 */
	public static function get_product_id( $product_id = false ) {
		return ( empty( $product_id ) ) ? wponion_get_var( 'wcrbp_product_id', false ) : $product_id;
	}

	/**
	 * @param bool $sub_product_id
	 *
	 * @return bool|string
	 */
	public static function get_sub_product_id( $sub_product_id = false ) {
		return ( empty( $sub_product_id ) ) ? wponion_get_var( 'wcrbp_sub_product_id', false ) : $sub_product_id;
	}

	/**
	 * @param bool $sub_product_type
	 *
	 * @return bool|string
	 */
	public static function get_sub_product_type( $sub_product_type = false ) {
		return ( empty( $sub_product_type ) ) ? wponion_get_var( 'wcrbp_sub_product_type', false ) : $sub_product_type;
	}
}
