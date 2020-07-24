<?php

namespace WC_RBP\Admin\Save_Handler;

defined( 'ABSPATH' ) || exit;

class Variations extends General {
	/**
	 * Returns Product ID.
	 *
	 * @return string
	 */
	public function product_id() {
		return $this->sub_product_id();
	}

	/**
	 * Returns Parent Product ID.
	 *
	 * @return string
	 */
	public function parent_product_id() {
		return $this->product_id;
	}
}
