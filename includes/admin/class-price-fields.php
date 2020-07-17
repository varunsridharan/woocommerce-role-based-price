<?php

namespace WC_RBP\Admin;

use VSP\Base;

class Price_Fields extends Base {
	/**
	 * Stores WPOnion Builder.
	 *
	 * @var \WPO\Builder
	 */
	protected $builder;

	/**
	 * Stores Allowed User Roles.
	 *
	 * @var bool|array
	 */
	protected $roles = false;

	/**
	 * Stores Allowed Price Types.
	 *
	 * @var bool|array
	 */
	protected $price_types = false;

	/**
	 * Price_Fields constructor.
	 *
	 * @param null|\WPO\Builder $builder
	 * @param array             $args
	 */
	public function __construct( $builder = null, $args = array() ) {
		$args = wponion_parse_args( $args, array(
			'allowed_roles'     => wc_rbp_allowed_roles(),
			'allowed_prices'    => wc_rbp_allowed_price(),
			'product_id'        => false,
			'parent_product_id' => false,
		) );

		$this->builder = ( empty( $builder ) ) ? wponion_builder() : $builder;
	}

	/**
	 * Returns A Simple CSS For Tab.
	 *
	 * @return string
	 */
	protected function tab_css() {
		return <<<SCSS
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
SCSS;

	}

	/**
	 * Genertes Fields.
	 */
	protected function setup() {
		$tab = $this->builder->tab( 'roles_price' )->tab_style( 'style2' )->un_array( true )->css( $this->tab_css() );

		foreach ( $this->roles as $role_id ) {
			$section = $tab->section( $role_id, \VSP\Helper::user_role_title( $role_id, $role_id ), 'wpoic-user' );
			$this->setup_single_role_fields( $section, $role_id );
		}
	}

	/**
	 * This will be used to setup single role's fields.
	 *
	 * @param \WPO\Fields\Fieldset $section
	 * @param string               $role_id
	 */
	protected function setup_single_role_fields( $section, $role_id ) {
		$is_single = ( count( $this->allowed_prices ) === 1 ) ? '' : 'wpo-col-xs-12 wpo-col-md-12 wpo-col-lg-6';
		foreach ( $this->allowed_prices as $price_type ) {
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
