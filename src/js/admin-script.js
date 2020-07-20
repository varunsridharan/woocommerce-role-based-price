import { add_selectbox } from './metabox-handler';

( ( window, document, wp, $ ) => {
	$( () => {
		if( typeof window.wcrbp === 'undefined' ) {
			window.wcrbp = {
				metabox_id: 'role-based-price-editor',
				metabox: $( 'div#role-based-price-editor' ),
			};
		}
	} );

	$( window ).on( 'load', () => {
		if( window.wcrbp.metabox.length > 0 ) {
			add_selectbox();
		}
	} );

} )( window, document, window.wp, jQuery );
