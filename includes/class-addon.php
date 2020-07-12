<?php

namespace WC_RBP;

defined( 'ABSPATH' ) || exit;

/**
 * Class Addon
 *
 * @package WC_RBP
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
abstract class Addon extends \VSP\Core\Abstracts\Addon {
	/**
	 * Returns WCRBP Plugin's Class Instance.
	 *
	 * @return \WC_RBP
	 */
	public function plugin() {
		return wc_rbp();
	}
}
