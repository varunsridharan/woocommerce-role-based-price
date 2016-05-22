jQuery(document).ready(function ($) {
    
    $( function() {
		// Prevent inputs in meta box headings opening/closing contents
		$( '#wc-rbp-product-editor' ).find( '.hndle' ).unbind( 'click.postboxes' );

		jQuery( '#wc-rbp-product-editor' ).on( 'click', '.hndle', function( event ) {

			// If the user clicks on some form input inside the h3 the box should not be toggled
			if ( $( event.target ).filter( 'input, option, label, select,  div' ).length ) {
				return;
			}

			$( '#wc-rbp-product-editor' ).toggleClass( 'closed' );
		});
	});
    
        
    
    wc_rbp_add_variation_selectbox();
    wc_rbp_render_wootabs();

    
    /* wc_rbp_render_price_edit_tabs(jQuery("div.wc_rbp_tabs")); 
	jQuery('body').on('click','.wc_rbp_product_editor_btn',function () {
		var width = jQuery(this).attr('data-width');
		Custombox.open({
			target: jQuery(this).attr('data-href'),
			effect: 'fadein',
			position: ['center', 'top'],
			zIndex: 99999999,
			complete: function () {
				jQuery('div.wc_rbp_price_editor_fields').show();
                
				wc_rbp_render_price_edit_tabs(jQuery("div.wc-rbp-modal div.wc_rbp_tabs")); 
                wc_rbp_render_price_edit_tabs(jQuery("div.wc-rbp-modal div.wc_rbp_inner_tabs")); 
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
				//if(wc_rbp_render_tabs){}
				render_checkbox();

				//wc_rbp_check_product_rbp_status();
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
        //wcrbp-action
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
	});*/

    jQuery('body').on('click', 'div#wc-rbp-product-editor #wc_rbp_update_price', function () {
        var clickedBtn = jQuery(this);
        clickedBtn.attr('disabled', 'disable');
        wc_rbp_div_block('div#wc-rbp-product-editor div.inside');
        var data = jQuery('div#wc-rbp-product-editor :input').serialize();

        var form = jQuery('.wc-rbp-metabox-container');
        var action = form.attr('action');
        var method = form.attr('method');

        jQuery.ajax({
            url: action,
            method: method,
            data: data,
        }).done(function (data) {
            clickedBtn.removeAttr('disabled');
            wc_rbp_div_unblock('div#wc-rbp-product-editor div.inside');
            jQuery('div.wc_rbp_hidden_fields').html(data.data.hidden_fields);
            jQuery('div.wc_rbp_price_editor_ajax_response').html(data.data.html).fadeIn('slow');
            setTimeout(2000, function () {
                jQuery('div.wc_rbp_price_editor_ajax_response').fadeOut('slow');
            });
        })
    });
    //data-status
    wc_rbp_check_price_Status();

   jQuery('body').on('blur', '#wc-rbp-product-editor :input',function () {
        wc_rbp_check_price_Status();
    })
});

