<?php

namespace WC_RBP\Admin;

use VSP\Base;
use VSP\WC_Compatibility;
use WC_RBP\Traits\Product_Info;

defined( 'ABSPATH' ) || exit;

/**
 * Class Metabox_Sub_Product_Selector
 *
 * @package WC_RBP\Admin
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Metabox_Sub_Product_Selector extends Base {
	use Product_Info;

	/**
	 * Stores Selected Product.
	 *
	 * @var string
	 */
	protected $selected_product;

	/**
	 * Metabox_Sub_Product_Selector constructor.
	 *
	 * @param bool|string $product_id
	 * @param bool|string $sub_product_id
	 * @param bool|string $sub_product_type
	 * @param bool|string $selected_product
	 */
	public function __construct( $product_id = false, $sub_product_id = false, $sub_product_type = false, $selected_product = false ) {
		$this->setup_product_info( $product_id, $sub_product_id, $sub_product_type );
		$this->selected_product = ( empty( $selected_product ) ) ? wponion_get_var( 'wcrbp_sub_product_selector', false ) : $selected_product;
		$this->setup_variable();
		$this->run_hook();
	}

	/**
	 * Fetches Variation Products.
	 */
	protected function setup_variable() {
		if ( 'variable' === WC_Compatibility::get_product_type( $this->product_id ) ) {
			$this->settings = wponion_cast_array( $this->settings );
			$products       = array();
			/**
			 * @var \WC_Product_Variable $product
			 */
			$product    = wc_get_product( $this->product_id );
			$variations = $product->get_children();

			foreach ( $variations as $variation ) {
				$variation                        = wc_get_product( $variation );
				$products[ $variation->get_id() ] = '#' . $variation->get_id() . ' ' . strip_tags( $variation->get_formatted_name() );
			}
			$this->settings ['variations'] = array(
				'title'   => __( 'Variations' ),
				'options' => $products,
			);
		}
	}

	/**
	 * Triggers Hook And Generates Selectbox Data.
	 */
	protected function run_hook() {
		/**
		 * @param 1 settings array
		 * @param 2 current product id
		 * @param 3 current parent product id
		 * @param 4 selected product type
		 */
		$this->settings = $this->apply_filter( 'metabox/sub_product_selector', $this->settings, $this->product_id, $this->sub_product_id, $this->selected_product );

		if ( ! empty( $this->settings ) ) {
			$select_html = array();
			wc_rbp()->localizer()->add( 'metabox_sub_product_selector_raw', $this->settings, true, false );
			foreach ( $this->settings as $slug => $values ) {
				foreach ( $values['options'] as $id => $title ) {
					if ( ! is_array( $title ) ) {
						$title = array( 'label' => $title );
					}
					unset( $values['options'][ $id ] );
					$title['attributes']                                = ( wponion_is_set( $title, 'attributes' ) ) ? $title['attributes'] : array();
					$title['attributes']['data-wcrbp-sub-product-type'] = $slug;
					$values['options'][ $slug . '/' . $id ]             = $title;
				}
				$select_html[ $values['title'] ] = $values['options'];
			}

			$html = wpo_field( 'select', 'wcrbp_sub_product_selector' )
				->name( 'wcrbp_sub_product_selector' )
				->attribute( 'id', 'wcrbp_sub_product_selector' )
				->options( $select_html )
				->only_field( true )
				->render( $this->selected_product );
			$html = '<div id="wcrbp-metabox-sub-product-selector" class="selectize-default">' . $html . '</div>';
			wc_rbp()->localizer()->add( 'metabox_sub_product_selector_html', $html, false, false );
		}
	}
}
