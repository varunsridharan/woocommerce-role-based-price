<?php

namespace WC_RBP\Admin\Settings;
defined( 'ABSPATH' ) || exit;


use VSP\Core\Abstracts\Plugin_Settings;

/**
 * Class Settings
 *
 * @package WC_RBP\Admin\Settings
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Settings extends Plugin_Settings {
	/**
	 * Generates Fields Data.
	 */
	protected function fields() {
		$this->general_settings();
	}

	/**
	 * Generates Fields HTML
	 */
	protected function general_settings() {
		$price_types = wc_rbp_avaiable_price_type();
		$page        = $this->builder->container( 'general', __( 'General' ), 'wpoic-gear' );
		$page->select( 'allowed_roles', __( 'Enabled User Roles' ) )
			->style( 'width:50%;' )
			->multiple( true )
			->select_framework( 'selectize', array(
				'plugins' => [ 'remove_button', 'restore_on_backspace', 'drag_drop' ],
				'persist' => false,
				'create'  => false,
			) )
			->desc_field( __( 'Role Based Price Will Be Enabled For Selected User Roles' ) )
			->options( array( '\VSP\Helper', 'user_roles_lists' ) );

		$page->select( 'allowed_prices', __( 'Enabled Product Pricing' ) )
			->options( $price_types )
			->style( 'width:25%;' )
			->desc_field( __( 'Price Types To Be Enabled.' ) )
			->multiple( true )
			->select_framework( 'selectize', array(
				'plugins' => [ 'remove_button', 'restore_on_backspace' ],
				'persist' => false,
				'create'  => false,
			) );

		foreach ( $price_types as $id => $label ) {
			$page->text( $id . '_label', $label . ' ' . __( 'Custom Label' ) )->field_default( $label );
		}
	}
}
