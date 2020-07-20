export function add_selectbox() {
	let $html = window.wc_role_based_price_option( 'metabox_sub_product_selector_html' );


	if( !window.wponion._.isEmpty( $html ) ) {
		jQuery( $html ).appendTo( '#' + window.wcrbp.metabox_id + ' .hndle span' );

		jQuery( '#' + window.wcrbp.metabox_id + ' select#wcrbp_sub_product_selector' ).selectize( {
			persist: false,
			create: false,
			onChange: function( value ) {
				block_metabox();
				//$.WCRBP.block( $( 'div#wc-rbp-product-editor div.inside' ) );
				var $select   = this;
				var $parentID = $( 'input#post_ID' ).val();
				/*$.ajax( {
					url: ajaxurl + '?action=wc_rbp_metabox_refersh&pid=' + value + '&parentID=' + $parentID,
					method: "GET",
					data: '',
				} ).done( function( response ) {
					if( response.success === true ) {
						$select.destroy();
						$( '.wcrbpvariationbx' ).remove();
						$( '#wc-rbp-product-editor .inside' ).html( response.data );
						$.WCRBP.render_wootabs();
						$.WCRBP.add_variation_selectbox();
						$( "#wc-rbp-product-editor .inside input.wc_rbp_checkbox" ).wcrbp_checkbox();
						$.WCRBP.unblock( $( 'div#wc-rbp-product-editor div.inside' ) );
						$.WCRBP.render_price_status();
					}
				} )*/
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

export function unblock() {
	window.wcrbp.metabox.unblock();
}
