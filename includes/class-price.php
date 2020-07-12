<?php

namespace WC_RBP;

use WC_RBP\Price\Getter;

defined( 'ABSPATH' ) || exit;

class Price extends Getter {
	/**
	 * Stores Price Type.
	 *
	 * @var string
	 */
	protected $type = 'core';

	public function __construct() {
	}

	/**
	 * Fetches & Returns Regular Price.
	 *
	 * @return float
	 */
	public function get_regular_price() {
		return floatval( $this->regular_price );
	}

	/**
	 * Fetches & Returns Regular Price.
	 *
	 * @return float
	 */
	public function get_sale_price() {
		return floatval( $this->sale_price );
	}
}
