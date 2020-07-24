export function product_type_selector() {
	return window.wcrbp.metabox.find( 'select#wcrbp_sub_product_selector' );
}


export function product_id() {
	if( window.wcrbp.metabox.find( 'input[name="wcrbp_product_id"]' ).length > 0 ) {
		return window.wcrbp.metabox.find( 'input[name="wcrbp_product_id"]' ).val();
	}
	return '';
}


export function sub_product_id() {
	if( window.wcrbp.metabox.find( 'input[name="wcrbp_sub_product_id"]' ).length > 0 ) {
		return window.wcrbp.metabox.find( 'input[name="wcrbp_sub_product_id"]' );
	}
	return '';
}


export function sub_product_type() {
	if( window.wcrbp.metabox.find( 'input[name="wcrbp_sub_product_type"]' ).length > 0 ) {
		return window.wcrbp.metabox.find( 'input[name="wcrbp_sub_product_type"]' );
	}
	return '';
}

export function add_selectbox() {
	let $html = window.wc_role_based_price_option( 'metabox_sub_product_selector_html' );


	if( !window.wponion._.isEmpty( $html ) ) {
		jQuery( $html ).appendTo( '#' + window.wcrbp.metabox_id + ' .hndle span' );

		jQuery( '#' + window.wcrbp.metabox_id + ' select#wcrbp_sub_product_selector' ).selectize( {
			persist: false,
			create: false,
			onChange: function( value ) {
				value = value.split( '/' );
				sub_product_id().val( value[ 1 ] );
				sub_product_type().val( value[ 0 ] );
				block_metabox();

				window.wc_rbp_ajax_post( 'reload-metabox' ).send( {
					data: {
						wcrbp_product_id: product_id(),
						wcrbp_sub_product_id: sub_product_id().val(),
						wcrbp_sub_product_type: sub_product_type().val()
					},
					success: ( res ) => {
						window.wpo_core.handle_ajax_response( res );
						let $base = window.wcrbp.metabox.find( '> .inside' );
						$base.html( res.html );
						let $elem = $base.find( '.wponion-framework' );
						window.wponion_field_reload_all( $elem );
						window.wponion_init_theme( $elem );
					},
					error: ( res ) => window.wponion_error_swal( res ).fire(),
					always: () => unblock_metabox()
				} );
			}
		} );

		window.wcrbp.metabox.find( '.hndle' ).unbind( 'click' ).on( 'click', '.hndle', function( event ) {
			event.preventDefault();
			if( $( event.target ).filter( 'input, option, label, select,  div, span' ).size() ) {
				window.wcrbp.metabox.toggleClass( 'closed' );
			}
			return true;
		} );
	}
}

export function block_metabox() {
	window.wcrbp.metabox.block( {
		message: null,
		overlayCSS: {
			background: '#000',
			opacity: 0.6
		}
	} );
}

export function unblock_metabox() {
	window.wcrbp.metabox.unblock();
}

