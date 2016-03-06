jQuery(document).ready(function () {
	jQuery('body').on('click','button.wc_rbp_product_editor_btn',function () {
		var width = jQuery(this).attr('data-width');
		Custombox.open({
			target: jQuery(this).attr('data-href'),
			effect: 'fadein',
			position: ['center', 'top'],
			zIndex: 99999999,
			complete: function () {
				jQuery('div.wc_rbp_price_editor_fields').show();
				var render_tabs = render_price_edit_tabs(jQuery("div.wc-rbp-modal div.wc_rbp_tabs"));
				jQuery('.wc-rbp-modal').trigger('after_tab_setup');
				//if(render_tabs){}
				render_checkbox();

				//check_product_rbp_status();
			},
			loading: {
				parent: ['bar'],
			}
		});
	});

	jQuery('body').on('click', 'div.wc_rbp_price_editor_footer #update_price', function () {
		var clickedBtn = jQuery(this);
		clickedBtn.attr('disabled','disable');
		var height = jQuery('div.wc_rbp_price_editor_fields').innerHeight() + 'px';
		var width = jQuery('div.wc_rbp_price_editor_fields').width() + 'px';
		jQuery('div.wc_rbp_ajax_overlay').css('height', height).css('width', width).show();
		var form = jQuery('form#wc_rbp_price_editor_form');
		var action = form.attr('action');
		var method = form.attr('method');
		var data = form.serialize();
		jQuery.ajax({
			url: action,
			method: method,
			data: data,
		}).done(function (data) {
			clickedBtn.removeAttr('disabled');
			jQuery('div.wc_rbp_ajax_overlay').hide();
			jQuery('div.wc_rbp_hidden_fields').html(data.data.hidden_fields);
			jQuery('div.wc_rbp_price_editor_ajax_response').html(data.data.html).fadeIn('slow'); 
			setTimeout(2000,function(){
				jQuery('div.wc_rbp_price_editor_ajax_response').fadeOut('slow');
			});
		})
	});


	//jQuery('input#enable_role_based_price').click(function(){ check_product_rbp_status(); });

});

function check_product_rbp_status() {
	var status = jQuery('input#enable_role_based_price').is(':checked');

	if (status) {
		jQuery('.tab_container input[type=text],.tab_container input[type=number]').removeAttr('disabled');
	} else {
		jQuery('.tab_container input[type=text],.tab_container input[type=number]').attr('disabled', 'disabled');
	}
}