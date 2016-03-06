jQuery(document).ready(function () {
	render_checkbox();


});

function render_checkbox() {
	jQuery('input.wc_rbp_checkbox').each(function () {
		var size = jQuery(this).attr('data-size'); if(size == '' && size == undefined){size = 'small'}
		var color               = jQuery(this).attr('data-color'); 
		var secondaryColor =  jQuery(this).attr('data-secondaryColor'); 
		var jackColor           = jQuery(this).attr('data-jackColor'); 
		var jackSecondaryColor =  jQuery(this).attr('data-jackSecondaryColor'); 
		var className           = jQuery(this).attr('data-className'); 
		var disabled            = jQuery(this).attr('data-disabled '); 
		var disabledOpacity =  jQuery(this).attr('data-disabledOpacity'); 
		var speed               = jQuery(this).attr('data-speed'); 
		
		new Switchery(this, {
			size:size,
			color : color,
			secondaryColor : secondaryColor,
			jackColor : jackColor,
			jackSecondaryColor : jackSecondaryColor,
			className : className,
			disabled : disabled,
			disabledOpacity : disabledOpacity,
			speed : speed
		});
	});
}

function render_price_edit_tabs(Elem){
	var effect =  Elem.attr('data-effect');
	var defaultTab =  Elem.attr('data-defaultTab');
	var containerWidth =  Elem.attr('data-containerWidth');
	var tabsPosition =  Elem.attr('data-tabsPosition');
	var horizontalPosition =  Elem.attr('data-horizontalPosition');
	var verticalPosition =  Elem.attr('data-verticalPosition');
	var responsive = Elem.attr('data-responsive');
	var theme =  Elem.attr('data-theme');
	var rtl =  Elem.attr('data-rtl');

	if(effect == undefined){effect = 'scale';}
	if(defaultTab == undefined){defaultTab = 1;}
	if(containerWidth == undefined){containerWidth = '100%';}
	if(tabsPosition == undefined){tabsPosition = 'horizontal';}
	if(horizontalPosition == undefined){horizontalPosition = 'top';}
	if(verticalPosition == undefined){verticalPosition = 'left';}
	if(responsive == undefined){responsive = true;}
	if(theme == undefined){theme = 'pws_theme_dark_grey';}
	if(rtl == undefined){rtl = false;}

	Elem.pwstabs({
	  effect:effect,
	  defaultTab:defaultTab,
	  containerWidth:containerWidth,
	  tabsPosition:tabsPosition,
	  horizontalPosition:horizontalPosition,
	  verticalPosition:verticalPosition,
	  responsive:responsive,
	  theme:theme,
	  rtl:rtl,
	}); 
	
	return true;
}