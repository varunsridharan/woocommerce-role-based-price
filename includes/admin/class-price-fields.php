<?php

namespace WC_RBP\Admin;

use VSP\Base;
use VSP\Helper;

class Price_Fields extends Base {
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
	 * @param array             $args
	 */
	public function __construct( $builder = null, $args = array() ) {
		$this->set_args( $args, array(
			'allowed_roles'  => wc_rbp_allowed_roles(),
			'allowed_prices' => wc_rbp_allowed_prices(),
			'product_id'     => wponion_get_var( 'wcrbp_product_id', false ),
			'sub_product_id' => wponion_get_var( 'wcrbp_sub_product_id', false ),
		) );
		$this->builder = ( empty( $builder ) ) ? wponion_builder() : $builder;
	}


	/**
	 * Genertes Fields.
	 */
	protected function setup() {
		global $post_ID;

		if ( empty( $this->option( 'product_id' ) ) && ! empty( $post_ID ) ) {
			$this->set_option( 'product_id', $post_ID );
		}

		$this->builder->hidden( 'product_id' )->name( 'wcrbp_product_id' );
		$this->builder->hidden( 'sub_product_id' )->name( 'wcrbp_sub_product_id' );

		$tab = $this->builder->tab( 'roles_price' )
			->tab_style( 'style2' )
			->un_array( true )
			->wrap_id( 'role-based-price-main-tab' );

		foreach ( $this->option( 'allowed_roles' ) as $role_id ) {
			$section = $tab->section( $role_id, Helper::user_role_title( $role_id, $role_id ), 'wpoic-user' );

			$this->do_action( 'price/editor/fields/role/before', $this->builder, $this );
			$this->do_action( "price/editor/fields/${role_id}/before", $this->builder, $this );

			$this->setup_single_role_fields( $section, $role_id );

			$this->do_action( "price/editor/fields/${role_id}", $this->builder, $role_id, $this );
			$this->do_action( 'price/editor/fields/role', $this->builder, $role_id, $this );
		}
	}

	/**
	 * This will be used to setup single role's fields.
	 *
	 * @param \WPO\Fields\Fieldset $section
	 * @param string               $role_id
	 */
	protected function setup_single_role_fields( $section, $role_id ) {
		$allowed_prices = $this->option( 'allowed_prices' );
		$is_single      = ( count( $allowed_prices ) === 1 ) ? '' : 'wpo-col-xs-12 wpo-col-md-12 wpo-col-lg-6';
		foreach ( $allowed_prices as $price_type ) {
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
