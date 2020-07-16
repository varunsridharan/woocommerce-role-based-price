<?php

namespace WC_RBP;

defined( 'ABSPATH' ) || exit;

class Cache extends \VSP\Cache {
	/**
	 * Cache Key Prefix.
	 *
	 * @var string
	 */
	protected static $prefix = 'wcrbp';
}
