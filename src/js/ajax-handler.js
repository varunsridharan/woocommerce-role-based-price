export default function() {
	/**
	 * Custom Ajax Handler. For WCRBP.
	 * @param $method
	 * @param $action
	 * @return {*}
	 */
	window.wc_rbp_ajax = ( $method = 'GET', $action = false ) => {
		return window.wponion_ajax( {
			type: $method,
			data: {
				action: 'wcrbp',
				wcrbp: $action,
			},
			success: false,
			error: false,
			always: false,
			action: false,
		} );
	};

	/**
	 * Ajax Send
	 * @param $action
	 * @return {*}
	 */
	window.wc_rbp_ajax_get = ( $action ) => {
		return window.wc_rbp_ajax( 'GET', $action );
	};

	/**
	 * Ajax Send
	 * @param $action
	 * @return {*}
	 */
	window.wc_rbp_ajax_post = ( $action ) => {
		return window.wc_rbp_ajax( 'POST', $action );
	};
}
