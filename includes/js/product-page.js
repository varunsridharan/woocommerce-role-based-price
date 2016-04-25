jQuery(document).ready(function () {
    
    render_price_edit_tabs(jQuery("div.wc_rbp_tabs")); 
	jQuery('body').on('click','.wc_rbp_product_editor_btn',function () {
		var width = jQuery(this).attr('data-width');
		Custombox.open({
			target: jQuery(this).attr('data-href'),
			effect: 'fadein',
			position: ['center', 'top'],
			zIndex: 99999999,
			complete: function () {
				jQuery('div.wc_rbp_price_editor_fields').show();
                
				render_price_edit_tabs(jQuery("div.wc-rbp-modal div.wc_rbp_tabs")); 
                render_price_edit_tabs(jQuery("div.wc-rbp-modal div.wc_rbp_inner_tabs")); 
                //jQuery("div.wc-rbp-modal div.wc_rbp_inner_tabs").each(function(){
                //     
                //    jQuery(this).pwstabs({
                //      effect:'scale',
                //      defaultTab:1,
                //      containerWidth:'100%',
                //      tabsPosition:'horizontal',
                //      horizontalPosition:'top',
                //      verticalPosition:'left',
                //      responsive:false,
                //      theme:'pws_theme_dark_grey',
                //      rtl:false,
                //    }); 
                //});
                    
                
                
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
    
    
    jQuery('body').on('click', 'div#wc-rbp-product-editor #wc_rbp_update_price', function () {
		var clickedBtn = jQuery(this);
		clickedBtn.attr('disabled','disable');
        wc_rbp_div_block('div#wc-rbp-product-editor div.inside');
        var data = jQuery('div#wc-rbp-product-editor :input').serialize();
		
		jQuery.ajax({
			url: ajaxurl,
			method: "POST",
			data: data,
		}).done(function (data) {
			clickedBtn.removeAttr('disabled');
			wc_rbp_div_unblock('div#wc-rbp-product-editor div.inside');
			jQuery('div.wc_rbp_hidden_fields').html(data.data.hidden_fields);
			jQuery('div.wc_rbp_price_editor_ajax_response').html(data.data.html).fadeIn('slow'); 
			setTimeout(2000,function(){
				jQuery('div.wc_rbp_price_editor_ajax_response').fadeOut('slow');
			});
		})
	});
//data-status
    check_price_Status();
    
    jQuery('#wc-rbp-product-editor :input').blur(function(){check_price_Status();})
});

function check_price_Status(){
    jQuery('.wcrbp-tab-nav > li').each(function(){
        if(jQuery(this).attr('data-status') == 'yes'){
            var divClass = 'div.wcrbp-tab-panel-' + jQuery(this).attr('data-panel');
            var filled = 0;
            var unfilled = 0;
            var totalf = 0;
            jQuery(divClass).find('input').each(function(){
                if(jQuery(this).val() == ''){ unfilled = unfilled + 1; } else { filled  = filled + 1; }
            });

            totalf = filled + unfilled;
            
            jQuery(this).find('.wc-rbp-tab-status').removeClass('bgred');
            jQuery(this).find('.wc-rbp-tab-status').removeClass('bggreen');
            jQuery(this).find('.wc-rbp-tab-status').removeClass('bgblue');
            
            if(filled == 0 && unfilled > 0){
                 jQuery(this).find('.wc-rbp-tab-status').addClass('bgred');
            } else if(filled > 0 && unfilled > 0) {
                jQuery(this).find('.wc-rbp-tab-status').addClass('bgblue');
            } else if(filled > 0 && unfilled == 0) {
                jQuery(this).find('.wc-rbp-tab-status').addClass('bggreen');
            }
        }

    });
}

function check_product_rbp_status() {
	var status = jQuery('input#enable_role_based_price').is(':checked');

	if (status) {
		jQuery('.tab_container input[type=text],.tab_container input[type=number]').removeAttr('disabled');
	} else {
		jQuery('.tab_container input[type=text],.tab_container input[type=number]').attr('disabled', 'disabled');
	}
}