<?php

namespace WC_RBP\Admin;

class Price_Fields extends \VSP\Base {
	/**
	 * Stores WPOnion Builder.
	 *
	 * @var \WPO\Builder
	 */
	protected $builder;

	/**
	 * Price_Fields constructor.
	 *
	 * @param null|\WPO\Builder $builder
	 */
	public function __construct( $builder = null ) {
		$this->builder = ( empty( $builder ) ) ? wponion_builder() : $builder;
	}

	/**
	 * Genertes Fields.
	 */
	protected function setup() {
		$tab           = $this->builder->tab( 'roles_price' )->tab_style( 'style2' )->un_array( true )->css( '
		padding:0;
> .wpo-row {
	margin:0;
	> .wponion-fieldset{
		padding:0;
		> .wponion-tab-wrap{
			border:none;
		}
	}
}
		' );
		$allowed_roles = wc_rbp_allowed_roles();

		foreach ( $allowed_roles as $role_id ) {
			$section = $tab->section( $role_id, \VSP\Helper::user_role_title( $role_id, $role_id ), 'wpoic-user' );
			$this->setup_single_role_fields( $section, $role_id );
		}
	}

	/**
	 * This will be used to setup single role's fields.
	 *
	 * @param \WPO\Fields\Fieldset $section
	 * @param string               $role_id
	 *
	 * @since {NEWVERSION}
	 */
	protected function setup_single_role_fields( $section, $role_id ) {
		$allowed_price = wc_rbp_allowed_price();
		$is_single     = ( count( $allowed_price ) === 1 ) ? '' : 'wpo-col-xs-12 wpo-col-md-12 wpo-col-lg-6';
		foreach ( $allowed_price as $price_type ) {
			$label = wc_rbp_price_type_label( $price_type );
			$section->text( $price_type, wc_rbp_price_type_label( $price_type ) )
				->wrap_class( $is_single )
				->attribute( 'type', 'number' )
				->horizontal( true )
				->style( 'width:100%;' )
				->desc_field( sprintf( __( 'Enter Product\'s %1$s' ), $label ) );
		}
	}

	/**
	 * @return \WPO\Builder
	 */
	public function get() {
		$this->setup();
		return $this->builder;
	}
}
