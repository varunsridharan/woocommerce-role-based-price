<?php

namespace WC_RBP\Traits;

use WC_RBP\Helper;

defined( 'ABSPATH' ) || exit;

trait Product_Info {
	/**
	 * Stores Product ID.
	 *
	 * @var string
	 */
	protected $product_id;

	/**
	 * Stores Sub Product ID.
	 *
	 * @var string
	 */
	protected $sub_product_id;

	/**
	 * Stores Sub Product Type.
	 *
	 * @var string
	 */
	protected $sub_product_type;

	/**
	 * @param bool $product_id
	 * @param bool $sub_product_id
	 * @param bool $sub_product_type
	 */
	public function setup_product_info( $product_id = false, $sub_product_id = false, $sub_product_type = false ) {
		$this->product_id       = Helper::get_product_id( $product_id );
		$this->sub_product_id   = Helper::get_sub_product_id( $sub_product_id );
		$this->sub_product_type = Helper::get_sub_product_type( $sub_product_type );
	}

	/**
	 * Returns $this->product_id value.
	 *
	 * @return string
	 */
	public function product_id() {
		return $this->product_id;
	}

	/**
	 * Returns $this->sub_product_id value.
	 *
	 * @return string
	 */
	public function sub_product_id() {
		return $this->sub_product_id;
	}

	/**
	 * Returns $this->sub_product_type value.
	 *
	 * @return string
	 */
	public function sub_product_type() {
		return $this->sub_product_type;
	}

	/**
	 * Sets Custom value With product_id
	 *
	 * @param $product_id
	 *
	 * @return $this
	 */
	public function set_product_id( $product_id ) {
		$this->product_id = $product_id;
		return $this;
	}

	/**
	 * Sets Custom value With sub_product_id
	 *
	 * @param $sub_product_id
	 *
	 * @return $this
	 */
	public function set_sub_product_id( $sub_product_id ) {
		$this->sub_product_id = $sub_product_id;
		return $this;
	}

	/**
	 * Sets Custom value With sub_product_type
	 *
	 * @param $sub_product_type
	 *
	 * @return $this
	 */
	public function set_sub_product_type( $sub_product_type ) {
		$this->sub_product_type = $sub_product_type;
		return $this;
	}
}
