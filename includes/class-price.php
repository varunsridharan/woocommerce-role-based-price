<?php

namespace WC_RBP;

use VSP\WC_Compatibility;
use WC_RBP\DB\Query;
use WPOnion\Traits\Class_Options;

defined( 'ABSPATH' ) || exit;

/**
 * Class Price
 *
 * @package WC_RBP
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Price {
	use Class_Options;

	/**
	 * Stores USER ID.
	 *
	 * @var bool
	 */
	protected $user_id = false;

	/**
	 * Stores ID
	 *
	 * @var int
	 */
	protected $ID = false;

	/**
	 * Stores Object Type.
	 *
	 * @example product / user
	 * @var string
	 */
	protected $object_type = 'product';

	/**
	 * Stores product_id
	 *
	 * @var int
	 */
	protected $product_id = null;

	/**
	 * Stores product
	 *
	 * @var \WC_Product
	 */
	protected $product = null;

	/**
	 * Stores type
	 *
	 * @var string
	 */
	protected $price_type = 'core';

	/**
	 * Stores user_role
	 *
	 * @var string
	 */
	protected $user_role = null;

	/**
	 * Stores regular_price
	 *
	 * @var int|string
	 */
	protected $regular_price = null;

	/**
	 * Stores sale_price
	 *
	 * @var int|string
	 */
	protected $sale_price = null;

	/**
	 * Price constructor.
	 *
	 * @param int|\WC_Product $product_id
	 * @param string|bool     $user_role
	 * @param string          $price_type
	 */
	public function __construct( $product_id, $price_type = 'core', $user_role = false ) {
		$this->product_id = WC_Compatibility::get_product_id( $product_id );
		$this->user_role  = $user_role;
		$this->price_type = $price_type;
		$this->fetch_data();
	}

	/**
	 * @return \WC_RBP\DB\Price
	 */
	protected function db() {
		return DB\Price::instance();
	}

	/**
	 * Fetches Data From DB.
	 */
	protected function fetch_data() {
		$is_exists = Query::exists( $this->object_type, $this->product_id, $this->price_type, $this->user_role );
		if ( $is_exists ) {
			$result = Query::get_price( $this->object_type, $this->product_id, $this->price_type, $this->user_role );

			if ( isset( $result->ID ) ) {
				$this->ID            = $result->ID;
				$this->sale_price    = $result->sale_price;
				$this->regular_price = $result->regular_price;
				$this->object_type   = $result->object_type;
			}
		}
	}

	/**
	 * Fetches & Returns Regular Price.
	 *
	 * @param string $type How To Return The Value.
	 *
	 * @return float
	 */
	public function get_regular_price( $type = 'display' ) {
		return ( 'display' === $type ) ? floatval( $this->regular_price ) : $this->regular_price;
	}

	/**
	 * Fetches & Returns Regular Price.
	 *
	 * @param string $type How To Return The Value.
	 *
	 * @return float
	 */
	public function get_sale_price( $type = 'display' ) {
		return ( 'display' === $type ) ? floatval( $this->sale_price ) : $this->sale_price;
	}

	/**
	 * Common Function which can be used to set price dynamically.
	 *
	 * @param string|int $value
	 * @param string     $price_type
	 *
	 * @return $this
	 */
	public function set_price( $value, $price_type = 'regular_price' ) {
		return ( 'sale_price' === $price_type ) ? $this->set_sale_price( $value ) : $this->set_regular_price( $value );
	}

	/**
	 * Sets Regular Price.
	 *
	 * @param $price
	 *
	 * @return $this
	 */
	public function set_regular_price( $price ) {
		if ( 0 === $price || ! empty( $price ) ) {
			$this->regular_price = wc_format_decimal( $price );
		} else {
			$this->regular_price = '';
		}
		return $this;
	}

	/**
	 * Sets Sale Price.
	 *
	 * @param $price
	 *
	 * @return $this
	 */
	public function set_sale_price( $price ) {
		if ( 0 === $price || ! empty( $price ) ) {
			$this->sale_price = wc_format_decimal( $price );
		} else {
			$this->sale_price = '';
		}
		return $this;
	}

	/**
	 * Sets Product ID.
	 *
	 * @param $product
	 *
	 * @return $this
	 */
	public function set_product_id( $product ) {
		$this->object_type = 'product';
		$this->product_id  = WC_Compatibility::get_product_id( $product );
		return $this;
	}

	/**
	 * Sets User ID.
	 *
	 * @param $user_id
	 *
	 * @return $this
	 */
	public function set_user_id( $user_id ) {
		$this->object_type = 'user_' . $user_id;
		return $this;
	}

	/**
	 * Sets Users's Role.
	 *
	 * @param $role
	 *
	 * @return $this
	 */
	public function set_user_role( $role ) {
		$this->user_role = $role;
		return $this;
	}

	/**
	 * Sets Type.
	 *
	 * @param $type
	 *
	 * @return $this
	 */
	public function set_price_type( $type ) {
		$this->price_type = $type;
		return $this;
	}

	/**
	 * Validates if its new.
	 *
	 * @return bool
	 */
	protected function has_id() {
		return ( ! empty( $this->ID ) );
	}

	/**
	 * @param      $key
	 * @param      $value
	 * @param bool $prev_value
	 *
	 * @return bool
	 */
	public function update_meta( $key, $value, $prev_value = null ) {
		$this->set_option( $key, $value );
		return ( $this->has_id() ) ? wc_rbp_update_meta( $this->ID, $key, $value, $prev_value ) : true;
	}

	/**
	 * @param      $key
	 * @param      $value
	 * @param bool $unique
	 *
	 * @return bool|false|int
	 */
	public function add_meta( $key, $value, $unique = true ) {
		$this->set_option( $key, $value );
		return ( $this->has_id() ) ? wc_rbp_add_meta( $this->ID, $key, $value, $unique ) : true;
	}

	/**
	 * Deletes A Meta.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function delete_meta( $key ) {
		$this->remove_option( $key );
		return ( $this->has_id() ) ? wc_rbp_delete_meta( $this->ID, $key ) : true;
	}

	/**
	 * Fetches Meta
	 *
	 * @param      $key
	 * @param bool $single
	 *
	 * @return bool|mixed
	 */
	public function get_meta( $key, $single = true ) {
		if ( $this->has_option( $key ) ) {
			return $this->option( $key );
		}

		if ( $this->has_id() ) {
			$meta = wc_rbp_get_meta( $this->ID, $key, $single );
			$this->set_option( $key, $meta );
			return $meta;
		}
		return false;
	}

	/**
	 * Stores Values In Database.
	 *
	 * @return bool
	 */
	public function save() {
		if ( empty( $this->ID ) && empty( $this->regular_price ) && empty( $this->sale_price ) ) {
			return false;
		}

		if ( empty( $this->ID ) ) {
			$is_saved = Query::add( $this->object_type, $this->product_id, $this->price_type, $this->user_role, $this->regular_price, $this->sale_price );

			if ( $is_saved ) {
				$this->ID = $is_saved;
				$metas    = $this->option();
				if ( ! empty( $metas ) ) {
					foreach ( $metas as $id => $values ) {
						$this->add_meta( $id, $values );
					}
				}
			}
		} else {
			$is_saved = Query::update_prices_by_id( $this->ID, $this->regular_price, $this->sale_price );
		}

		return $is_saved;
	}
}
