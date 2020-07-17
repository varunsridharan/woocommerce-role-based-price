<?php

use VSP\Helper;
use WC_RBP\Cache;
use WPOnion\Exception\Cache_Not_Found;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_rbp_option' ) ) {
	/**
	 * @param string $key
	 * @param bool   $default
	 *
	 * @return array|bool|\WPOnion\DB\Option
	 */
	function wc_rbp_option( $key = '', $default = false ) {
		return wpo_settings( '_wc_role_based_price', $key, $default );
	}
}

if ( ! function_exists( 'wc_rbp_allowed_roles' ) ) {
	/**
	 * @return array|bool|\WPOnion\DB\Option
	 */
	function wc_rbp_allowed_roles() {
		$roles = wc_rbp_option( 'allowed_roles' );
		if ( empty( $roles ) ) {
			$roles = array_keys( Helper::user_roles_lists() );
		}

		return $roles;
	}
}

if ( ! function_exists( 'wc_rbp_get_opposite_metakey' ) ) {
	/**
	 * @param $key
	 *
	 * @return string
	 */
	function wc_rbp_get_opposite_metakey( $key ) {
		return ( 'sale_price' === $key ) ? 'regular_price' : $key;
	}
}

if ( ! function_exists( 'wc_rbp_avaiable_price_type' ) ) {
	/**
	 * Returns avaiable_price type with label
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	function wc_rbp_avaiable_price_type( $key = '' ) {
		$avaiable_price = apply_filters( 'wc_rbp_avaiable_price', array(
			'regular_price' => __( 'Regular Price' ),
			'sale_price'    => __( 'Sale Price' ),
		) );
		return ( ! empty( $key ) && isset( $avaiable_price[ $key ] ) ) ? $avaiable_price[ $key ] : $avaiable_price;
	}
}

if ( ! function_exists( 'wc_rbp_allowed_prices' ) ) {
	/**
	 * @return array|bool|\WPOnion\DB\Option
	 */
	function wc_rbp_allowed_prices() {
		$roles = wc_rbp_option( 'allowed_prices' );
		if ( empty( $roles ) ) {
			$roles = array_keys( wc_rbp_avaiable_price_type() );
		}

		return $roles;
	}
}

if ( ! function_exists( 'wc_rbp_price_type_label' ) ) {
	/**
	 * Returns A Valid Price Type Label.
	 *
	 * @param string $key
	 *
	 * @return string|array
	 */
	function wc_rbp_price_type_label( $key = '' ) {
		try {
			$price_types = Cache::get( 'price_types/labels' );
		} catch ( Cache_Not_Found $exception ) {
			$price_types = wc_rbp_avaiable_price_type();
			foreach ( $price_types as $price_id => $default_label ) {
				$price_types[ $price_id ] = wc_rbp_option( $price_id . '_label', $default_label );
			}
		}
		return ( ! empty( $key ) && isset( $price_types[ $key ] ) ) ? $price_types[ $key ] : $price_types;
	}
}
