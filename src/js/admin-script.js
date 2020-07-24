import { add_selectbox } from './metabox-handler';
import ajax_handler from './ajax-handler';

( ( window, document, wp, $ ) => {
	$( () => {
		ajax_handler();
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
