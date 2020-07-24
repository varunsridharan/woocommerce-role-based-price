<?php

namespace WC_RBP\Abstracts;

use VSP\Base;
use WC_RBP\Traits\Product_Info;

/**
 * Class DB_Handler
 *
 * @package WC_RBP\Abstracts
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
abstract class DB_Handler extends Base {
	use Product_Info;

	/**
	 * DB_Handler constructor.
	 *
	 * @param bool|string $product_id
	 * @param bool|string $sub_product_id
	 * @param bool|string $sub_product_type
	 */
	public function __construct( $product_id = false, $sub_product_id = false, $sub_product_type = false ) {
		$this->setup_product_info( $product_id, $sub_product_id, $sub_product_type );
	}

	/**
	 * Used To Fetch Values From DB.
	 *
	 * @return mixed
	 */
	abstract public function get();

	/**
	 * Used To Store Values In DB.
	 *
	 * @param $values
	 *
	 * @return mixed
	 */
	abstract public function save( $values );

}
