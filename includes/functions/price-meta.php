<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_rbp_get_meta' ) ) {
	/**
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
	 *                          the specified object. Default empty.
	 * @param bool   $single Optional. If true, return only the first value of the specified meta_key.
	 *                          This parameter has no effect if meta_key is not specified. Default false.
	 *
	 * @return mixed Single metadata value, or array of values
	 * @since 4.0
	 */
	function wc_rbp_get_meta( $object_id, $meta_key = '', $single = false ) {
		return get_metadata( 'wc_role_based_price', $object_id, $meta_key, $single );
	}
}

if ( ! function_exists( 'wc_rbp_add_meta' ) ) {
	/**
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique Optional. Whether the specified metadata key should be unique for the object.
	 *                           If true, and the object already has a value for the specified metadata key,
	 *                           no change will be made. Default false.
	 *
	 * @return int|false The meta ID on success, false on failure.
	 * @since 4.0
	 */
	function wc_rbp_add_meta( $object_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( 'wc_role_based_price', $object_id, $meta_key, $meta_value, $unique );
	}
}


if ( ! function_exists( 'wc_rbp_update_meta' ) ) {
	/**
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. If specified, only update existing metadata entries
	 *                           with this value. Otherwise, update all entries.
	 *
	 * @return int|bool The new meta field ID if a field with the given key didn't exist and was
	 *                  therefore added, true on successful update, false on failure.
	 * @since 4.0
	 */
	function wc_rbp_update_meta( $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'wc_role_based_price', $object_id, $meta_key, $meta_value, $prev_value );
	}
}


if ( ! function_exists( 'wc_rbp_delete_meta' ) ) {
	/**
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if non-scalar.
	 *                           If specified, only delete metadata entries with this value.
	 *                           Otherwise, delete all entries with the specified meta_key.
	 *                           Pass `null`, `false`, or an empty string to skip this check.
	 *                           (For backward compatibility, it is not possible to pass an empty string
	 *                           to delete those entries with an empty string for a value.)
	 * @param bool   $delete_all Optional. If true, delete matching metadata entries for all objects,
	 *                           ignoring the specified object_id. Otherwise, only delete
	 *                           matching metadata entries for the specified object_id. Default false.
	 *
	 * @return bool True on successful delete, false on failure.
	 * @since 4.0
	 */
	function wc_rbp_delete_meta( $object_id, $meta_key, $meta_value = '', $delete_all = false ) {
		return delete_metadata( 'wc_role_based_price', $object_id, $meta_key, $meta_value, $delete_all );
	}
}
