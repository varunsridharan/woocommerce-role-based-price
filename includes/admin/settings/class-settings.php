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

	protected function fields() {
		$this->general_settings();
	}

	protected function general_settings() {
		$page = $this->builder->container( 'general', __( 'General' ), 'wpoic-gear' );
		$page->select( 'enabled_roles', __( 'Enabled User Roles' ) )
			->multiple( true )
			->select_framework( 'selectize', array(
				'plugins' => [ 'remove_button', 'restore_on_backspace', 'drag_drop' ],
				'persist' => false,
				'create'  => false,
			) )
			->desc_field( __( 'Role Based Price Will Be Enabled For Selected User Roles' ) )
			->options( array( '\VSP\Helper', 'user_roles_lists' ) );
	}
}
