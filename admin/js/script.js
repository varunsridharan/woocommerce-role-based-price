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
var wc_rbp_min_spinner = '<div class="spinner-loader"> Loading… </div>';

jQuery(document).ready(function () {      
    jQuery(dialog_id).html(wc_rbp_spinner).dialog({
            bgiframe: true,
            autoOpen: false, 
            height : 250, 
            width:500,  
            modal: true, 
            close: function( event, ui ) {
                    jQuery(dialog_id).dialog({ 
                        height : 250, 
                        width:500 
                    }).html(wc_rbp_spinner);
            }
    });
    
    
    jQuery("body").on("click", ".role_based_price_editor_btn", function () {
        
        jQuery(dialog_id).dialog('open').load(jQuery(this).attr('data-target'),function(){
            
            jQuery(dialog_id + ' ' + tab_id).tabs();
               jQuery(dialog_id).dialog({
                    position:{ my: "center", at: "center", of: window },
                    width:'auto',
                    height : "auto",
                });
            
                
        }).dialog('open') ;
    });
    
    
    jQuery("body").on("change", enable_rbp_id, function () { 
        jQuery(dialog_id + ' ' + tab_id + ' input[type=text]').attr('disabled',!this.checked) 
    });
    
    jQuery("body").on("click", dialog_id +' '+ save_rbp_id, function () {
        jQuery(this).after(wc_rbp_min_spinner);
        jQuery(this).attr('disabled',true);
        jQuery.post(ajaxurl, jQuery( "#wc_rbp_product_edit_form" ).serialize(), function(response) {
            jQuery(dialog_id).html(response).dialog({
                    position:{ my: "center", at: "center", of: window },
                    width:'auto',
                    height : "auto",
                });
		});
    });
    
});




 