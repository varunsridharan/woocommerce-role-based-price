<?php

namespace WC_RBP\DB;

defined( 'ABSPATH' ) || exit;

use Varunsridharan\WordPress\DB_Table;

class Price extends DB_Table {
	/**
	 * Returns A Valid Table Name.
	 *
	 * @return mixed|string
	 */
	public function table_name() {
		return 'wc_role_based_price';
	}

	/**
	 * Upgrade Callback.
	 */
	protected function upgrade() {
	}

	/**
	 * Things To Do After Table Creation.
	 */
	protected function after_table_created() {
	}

	/**
	 * Price Table Schema
	 *
	 * @return string
	 */
	protected function set_schema() {
		return <<<SQL
`ID` bigint(20) NOT NULL AUTO_INCREMENT,
`wc_product` bigint(20) NOT NULL,
`type` varchar(255) NOT NULL,
`user_role` varchar(255) NOT NULL,
`regular_price` decimal(10,10) DEFAULT NULL,
`selling_price` decimal(10,10) DEFAULT NULL,
PRIMARY KEY (`ID`),
UNIQUE KEY `ID` (`ID`),
KEY `wc_product` (`wc_product`)
SQL;

	}

	protected function table_version() {
		return '4.0';
	}
}
