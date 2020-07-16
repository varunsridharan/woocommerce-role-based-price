<?php

namespace WC_RBP;

use WC_RBP\DB\Query;
use WPOnion\Exception\Cache_Not_Found;

defined( 'ABSPATH' ) || exit;

/**
 * Class Price_Helper
 *
 * @package WC_RBP
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Price_Helper {
	/**
	 * @param string $object_type
	 * @param null   $product
	 * @param string $price_type
	 * @param bool   $user_role
	 *
	 * @return mixed|\WC_RBP\Price
	 * @throws \Exception
	 */
	public static function get( $object_type = 'product', $product = null, $price_type = 'core', $user_role = false ) {
		try {
			return Cache::get( $object_type . '/' . $product . '/' . $price_type . '/' . $user_role );
		} catch ( Cache_Not_Found $exception ) {
			$instance = new Price( $product, $price_type, $user_role );
			$exception->set( $instance );
			return $instance;
		}
	}
}
