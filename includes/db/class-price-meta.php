<?php

namespace WC_RBP\DB;

defined( 'ABSPATH' ) || exit;

use Varunsridharan\WordPress\DB_Table;

class Price_Meta extends DB_Table {
	/**
	 * Returns A Valid Table Name.
	 *
	 * @return mixed|string
	 */
	public function table_name() {
		return 'wc_role_based_pricemeta';
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
meta_id BIGINT UNSIGNED NOT NULL auto_increment,
wc_role_based_price_id BIGINT UNSIGNED NOT NULL,
meta_key varchar(255) default NULL,
meta_value longtext NULL,
PRIMARY KEY  (meta_id),
KEY wc_role_based_price_id (wc_role_based_price_id),
KEY meta_key (meta_key(32))
SQL;

	}

	protected function table_version() {
		return '4.0';
	}
}
