jQuery(document).ready(function(){
    if(jQuery('body').hasClass('post-type-product')){
        jQuery('#enable_simple_role_based_price').change(function(){
            jQuery('#'+jQuery(this).attr('data-target')).slideToggle();
        });
        //jQuery('.enable_variable_role_based_price').change(function(){
        //    jQuery('#'+jQuery(this).attr('data-target')).slideToggle();
        //});
    }
    jQuery('.woocommerce_variations').on( "change", function() {
        jQuery('.enable_variable_role_based_price').unbind('change').change(function(){
            jQuery('#'+jQuery(this).attr('data-target')).slideToggle();
        });
    });
    //jQuery('.woocommerce_variation .enable_variable_role_based_price').on( "change", function() {
    //    jQuery('#'+jQuery(this).attr('data-target')).slideToggle();
    //});
});
