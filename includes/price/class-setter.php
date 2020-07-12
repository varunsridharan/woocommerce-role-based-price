<?php

namespace WC_RBP\Price;

use VSP\Base;
use VSP\WC_Compatibility;
use WC_RBP\DB\Price;

defined( 'ABSPATH' ) || exit;

/**
 * Class Setter
 *
 * @package WC_RBP\Price
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Setter {
	/**
	 * Stores ID
	 *
	 * @var int
	 */
	protected $ID;

	/**
	 * Stores product_id
	 *
	 * @var int
	 */
	protected $product_id;

	/**
	 * Stores product
	 *
	 * @var \WC_Product
	 */
	protected $product;

	/**
	 * Stores type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Stores user_role
	 *
	 * @var string
	 */
	protected $user_role;

	/**
	 * Stores regular_price
	 *
	 * @var int|string
	 */
	protected $regular_price;

	/**
	 * Stores sale_price
	 *
	 * @var int|string
	 */
	protected $sale_price;

	/**
	 * Sets Regular Price.
	 *
	 * @param $price
	 *
	 * @return $this
	 */
	public function set_regular_price( $price ) {
		$this->regular_price = wc_format_decimal( $price );
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
		$this->sale_price = wc_format_decimal( $price );
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
		$this->product_id = WC_Compatibility::get_product_id( $product );
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
	public function set_type( $type ) {
		$this->type = $type;
		return $this;
	}

	public function save() {
		$db = Price::instance();
		if ( empty( $this->ID ) ) {
			$is_saved = $db->insert( array(
				'wc_product'    => $this->product_id,
				'type'          => $this->type,
				'user_role'     => $this->user_role,
				'regular_price' => $this->regular_price(),
				'sale_price'    => $this->sale_price(),
			) );
		} else {

		}

		return ( 0 === $is_saved || empty( $is_saved ) ) ? false : true;
	}
}
