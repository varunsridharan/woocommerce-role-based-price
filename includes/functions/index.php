<?php
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


if ( ! function_exists( 'wc_rbp_get_opposite_metakey' ) ) {
	/**
	 * @param $key
	 *
	 * @return string
	 */
	function wc_rbp_get_opposite_metakey( $key ) {
		return ( 'selling_price' === $key ) ? 'regular_price' : $key;
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
			'selling_price' => __( 'Selling Price' ),
		) );
		return ( ! empty( $key ) && isset( $avaiable_price[ $key ] ) ) ? $avaiable_price[ $key ] : $avaiable_price;
	}
}


if ( ! function_exists( 'wc_rbp_allowed_roles' ) ) {
	/**
	 * @return array|bool|\WPOnion\DB\Option
	 */
	function wc_rbp_allowed_roles() {
		$roles = wc_rbp_option( 'allowed_roles' );
		if ( empty( $roles ) ) {
			$roles = array_keys( wc_rbp_get_user_roles_selectbox() );
		}

		return $roles;
	}
}

if ( ! function_exists( 'wc_rbp_allowed_price' ) ) {
	/**
	 * @return array|bool|\WPOnion\DB\Option
	 */
	function wc_rbp_allowed_price() {
		$roles = wc_rbp_option( 'allowed_price' );
		if ( empty( $roles ) ) {
			$roles = array_keys( wc_rbp_avaiable_price_type() );
		}

		return $roles;
	}
}
