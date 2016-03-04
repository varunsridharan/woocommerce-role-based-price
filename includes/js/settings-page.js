jQuery(document).ready(function(){
	
	
	if(jQuery('div.wc_rbp_addon_listing').size() > 0){
		jQuery('p.submit').remove();
	}
	jQuery('select.wc-rbp-enhanced-select').selectize({
		plugins: ['remove_button', 'restore_on_backspace'],
		persist: false,
		create: true,
	});
	
	if(jQuery('.wc_rbp_settings_submenu').size() > 0){
		var id = window.location.hash;
		jQuery('.wc_rbp_settings_submenu a').removeClass('current');
		jQuery('.wc_rbp_settings_submenu a[href="'+id+'" ]').addClass('current');
		if(id == ''){
			jQuery('.wc_rbp_settings_submenu a:first').addClass('current');
			id = jQuery('.wc_rbp_settings_submenu a:first').attr('href');
		}
		settings_showHash(id);
	}
	
	jQuery('.wc_rbp_settings_submenu a').click(function(){
		var id = jQuery(this).attr('href');
		jQuery('.wc_rbp_settings_submenu a').removeClass('current');
		jQuery(this).addClass('current');
		settings_showHash(id);
	});	

	
	jQuery('.wc-rbp-activate-now').click(function(){
		active_deactive_addon(jQuery(this),'.wc-rbp-deactivate-now')
	});
	
	jQuery('.wc-rbp-deactivate-now').click(function(){
		active_deactive_addon(jQuery(this),'.wc-rbp-activate-now')
	});
});

function settings_showHash(id){
	jQuery('div.wc_rbp_settings_content').hide();
	id = id.replace('#','#settings_');
	jQuery(id).show();
}

function active_deactive_addon(ref,oppo = '.wc-rbp-deactivate-now'){
	var clicked = ref;
	var slug = ref.parent().attr('data-pluginslug');
	console.log(slug);
	var parent_div = '.plugin-card-'+slug;
	var height = jQuery(parent_div).innerHeight();
	var width = jQuery(parent_div).innerWidth();
	jQuery(parent_div + ' .wc_rbp_ajax_overlay').css('height',height+'px').css('width',width+'px').fadeIn();
	clicked.attr('disabled','disable');
	var link = clicked.attr('href');
	jQuery.ajax({
		method:'GET',
		url : link,
	}).done(function(response){
		var status = response.success;
		jQuery(parent_div + ' .wc_rbp_ajax_overlay').fadeOut();
		clicked.removeAttr('disabled');
		if(status){
			clicked.hide();
			jQuery(parent_div).find(oppo).fadeIn();
		}

		jQuery(parent_div).find('.wc_rbp_ajax_response').hide().html(response.data.msg).fadeIn(function(){
			setTimeout(function(){ jQuery(parent_div).find('.wc_rbp_ajax_response').fadeOut(); }, 5000);
		});

	});


}

