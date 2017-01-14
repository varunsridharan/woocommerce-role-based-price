var dialog_id = '#wc_rpb_dialog';
var tab_id = '#wc_rbp_pop_tabs';
var enable_rbp_id = '#enable_role_based_price';
var save_rbp_id =  '#save_wc_rbp_price';
var wc_rbp_spinner = '<div class="progress_container">' + 
'  <h1 class="text-center">Loading.. <small>Please Wait</small></h1>' + 
'  <div class="progress">' + 
'    <div class="progress-bar">' + 
'      <div class="progress-shadow"></div>' + 
'    </div>' + 
'  </div>' + 
'</div>' ;
var wc_rbp_min_spinner = '<div id="wc_rbp_small_spinner" class="spinner-loader"> Loading… </div>';

jQuery(document).ready(function () {      
    jQuery("body").on("click", ".role_based_price_editor_btn", function () {
        jQuery(this).colorbox({
            opacity:"0.65",
            overlayClose:false,
            width:"100%",
            innerWidth:"95%",
            maxWidth:"1024px",
            height:"auto",
            innerHeight:"95%",
            maxHeight:"500px",
            //right:"10%",
            //fixed:true,
           //top : "25px",
            onComplete:function(){jQuery(tab_id).tabs(); }
        });
     });
 
    
    jQuery("body").on("change", enable_rbp_id, function () { 
        jQuery(tab_id + ' input[type=text]').attr('disabled',!this.checked) 
    });
    
    jQuery("body").on("click", save_rbp_id, function () {
        var button_click = jQuery(this);
        jQuery(this).after(wc_rbp_min_spinner);
        jQuery(this).attr('disabled',true);
        jQuery.post(ajaxurl, jQuery( "#wc_rbp_product_edit_form" ).serialize(), function(response) {
            jQuery('div#wc_rbp_small_spinner').fadeOut(function(){ jQuery(this).remove(); })
            jQuery('div.wc_rbp_update_message').hide().html(response).fadeIn();
            
            jQuery('.wc_rbp_pop_up_close').click(function(){
                jQuery(this).parent().fadeOut(function(){
                    jQuery(this).remove();
                });
            });
            
            button_click.removeAttr('disabled');
            /*jQuery(dialog_id).html(response).dialog({
                    position:{ my: "center", at: "center", of: window },
                    width:'auto',
                    height : "auto",
                });*/
		});
    }); 
    
});

function role_Based_bulk_edit(ref){
	var clicked = jQuery(ref);
	var post_id = woocommerce_admin_meta_boxes_variations.post_id; 
	jQuery.colorbox({
		href:ajaxurl + '?action=role_based_price_edit&type=bulkEdit&post_id=' + post_id ,
		opacity:"0.65",
		overlayClose:false,
		width:"100%",
		innerWidth:"95%",
		maxWidth:"1024px",
		height:"auto",
		innerHeight:"95%",
		maxHeight:"500px", 
		onClosed:function(){clicked.parent().next().click();},
		onComplete:function(){jQuery(tab_id).tabs(); }
	});	
	
}