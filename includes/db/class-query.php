<?php

namespace WC_RBP\DB;

use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class Query
 *
 * @package WC_RBP\DB
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Query {
	/**
	 * Returns DB Table Instance.
	 *
	 * @return \Varunsridharan\WordPress\DB_Table|\WC_RBP\DB\Price
	 */
	private static function db() {
		return Price::instance();
	}

	/**
	 * Checks if price exists in DB.
	 *
	 * @param $object_type
	 * @param $product_id
	 * @param $price_type
	 * @param $user_role
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public static function exists( $object_type, $product_id, $price_type, $user_role ) {
		try {
			$result = self::db()
				->selectCount( 'ID', 'count' )
				->where( 'object_type', $object_type )
				->where( 'product_id', $product_id )
				->where( 'price_type', $price_type )
				->where( 'user_role', $user_role )
				->one();
			return ( isset( $result->count ) ) ? $result->count : false;
		} catch ( Exception $exception ) {
			return false;
		}
	}

	/**
	 * Fetches Single Price Info From DB.
	 *
	 * @param      $object_type
	 * @param bool $product_id
	 * @param bool $price_type
	 * @param bool $user_role
	 *
	 * @return bool|object|array
	 */
	public static function get_price( $object_type, $product_id = false, $price_type = false, $user_role = false ) {
		try {
			return self::db()
				->select( '*' )
				->where( 'object_type', $object_type )
				->where( 'product_id', $product_id )
				->where( 'price_type', $price_type )
				->where( 'user_role', $user_role )
				->one();
		} catch ( Exception $exception ) {
			return false;
		}
	}

	/**
	 * @param string|bool $object_type
	 * @param string|bool $product_id
	 * @param string|bool $price_type
	 * @param string|bool $user_role
	 * @param string|bool $regular_price
	 * @param string|bool $sale_price
	 *
	 * @return mixed
	 */
	public static function add( $object_type, $product_id = false, $price_type = false, $user_role = false, $regular_price = false, $sale_price = false ) {
		return self::db()->insert( array(
			'object_type'   => $object_type,
			'product_id'    => $product_id,
			'price_type'    => $price_type,
			'user_role'     => $user_role,
			'regular_price' => $regular_price,
			'sale_price'    => $sale_price,
		) );
	}
}
