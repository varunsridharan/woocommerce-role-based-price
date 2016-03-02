jQuery(document).ready(function(){
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
});

function settings_showHash(id){
	jQuery('div.wc_rbp_settings_content').hide();
	id = id.replace('#','#settings_');
	jQuery(id).show();
}

