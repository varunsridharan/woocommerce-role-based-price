jQuery(document).ready(function () {
    wc_rbp_render_checkbox();
});

jQuery(function ($) {
    'use strict';

    /* $('.wcrbp-tab-nav').on('click', 'a', function (e) {
         e.preventDefault();

         var $li = $(this).parent(),
             panel = $li.data('panel'),
             $wrapper = $li.closest('.wcrbp-tabs'),
             $panel = $wrapper.find('.wcrbp-tab-panel-' + panel);

         $li.addClass('wcrbp-tab-active').siblings().removeClass('wcrbp-tab-active');
         $panel.show().siblings().hide();
     });


     if (!$('.wcrbp-tab-active').is('visible')) {
         var activePane = $('.wcrbp-tab-panel[style*="block"]').index();

         if (activePane >= 0) {
             $('.wcrbp-tab-nav li').removeClass('wcrbp-tab-active');
             $('.wcrbp-tab-nav li').eq(activePane).addClass('wcrbp-tab-active');
         }
     }

     $('.wcrbp-tab-active a').trigger('click');
     $('.wcrbp-tabs-no-wrapper').closest('.postbox').addClass('wcrbp-tabs-no-controls');*/

    $(document.body).on('keyup change', '.wc_rbp_selling_price', function () {
        var sale_price_field = $(this),
            regular_price_field;

        regular_price_field = sale_price_field.parents('.wc_rbp_price_container').find('.wc_rbp_regular_price');

        var sale_price = parseFloat(window.accounting.unformat(sale_price_field.val(), woocommerce_admin.mon_decimal_point));
        var regular_price = parseFloat(window.accounting.unformat(regular_price_field.val(), woocommerce_admin.mon_decimal_point));

        if (sale_price >= regular_price) {
            $(document.body).triggerHandler('wc_add_error_tip', [$(this), 'i18_sale_less_than_regular_error']);
        } else {
            $(document.body).triggerHandler('wc_remove_error_tip', [$(this), 'i18_sale_less_than_regular_error']);
        }
    });
});



function wc_rbp_render_wootabs() {
    jQuery('.wcrbp-tab-nav').on('click', 'a', function (e) {
        e.preventDefault();

        var $li = jQuery(this).parent(),
            panel = $li.data('panel'),
            $wrapper = $li.closest('.wcrbp-tabs'),
            $panel = $wrapper.find('.wcrbp-tab-panel-' + panel);

        $li.addClass('wcrbp-tab-active').siblings().removeClass('wcrbp-tab-active');
        $panel.show().siblings().hide();
    });


    if (!jQuery('.wcrbp-tab-active').is('visible')) {
        var activePane = jQuery('.wcrbp-tab-panel[style*="block"]').index();

        if (activePane >= 0) {
            jQuery('.wcrbp-tab-nav li').removeClass('wcrbp-tab-active');
            jQuery('.wcrbp-tab-nav li').eq(activePane).addClass('wcrbp-tab-active');
        }
    }

    jQuery('.wcrbp-tab-active a').trigger('click');
    jQuery('.wcrbp-tabs-no-wrapper').closest('.postbox').addClass('wcrbp-tabs-no-controls');
}

function wc_rbp_render_checkbox() {
    jQuery('input.wc_rbp_checkbox').each(function () {
        var size = jQuery(this).attr('data-size');
        if (size == '' && size == undefined) {
            size = 'small'
        }
        var color = jQuery(this).attr('data-color');
        var secondaryColor = jQuery(this).attr('data-secondaryColor');
        var jackColor = jQuery(this).attr('data-jackColor');
        var jackSecondaryColor = jQuery(this).attr('data-jackSecondaryColor');
        var className = jQuery(this).attr('data-className');
        var disabled = jQuery(this).attr('data-disabled ');
        var disabledOpacity = jQuery(this).attr('data-disabledOpacity');
        var speed = jQuery(this).attr('data-speed');

        new Switchery(this, {
            size: size,
            color: color,
            secondaryColor: secondaryColor,
            jackColor: jackColor,
            jackSecondaryColor: jackSecondaryColor,
            className: className,
            disabled: disabled,
            disabledOpacity: disabledOpacity,
            speed: speed
        });
    });
}

function wc_rbp_render_price_edit_tabs(Elem) {
    wc_rbp_render_tabs(Elem);
    return true;
}

function wc_rbp_render_tabs(Elem) {
    var effect = Elem.attr('data-effect');
    var defaultTab = Elem.attr('data-defaultTab');
    var containerWidth = Elem.attr('data-containerWidth');
    var tabsPosition = Elem.attr('data-tabsPosition');
    var horizontalPosition = Elem.attr('data-horizontalPosition');
    var verticalPosition = Elem.attr('data-verticalPosition');
    var responsive = Elem.attr('data-responsive');
    var theme = Elem.attr('data-theme');
    var rtl = Elem.attr('data-rtl');

    if (effect == undefined) {
        effect = 'none';
    }
    if (defaultTab == undefined) {
        defaultTab = 1;
    }
    if (containerWidth == undefined) {
        containerWidth = '100%';
    }
    if (tabsPosition == undefined) {
        tabsPosition = 'horizontal';
    }
    if (horizontalPosition == undefined) {
        horizontalPosition = 'top';
    }
    if (verticalPosition == undefined) {
        verticalPosition = 'left';
    }
    if (responsive == undefined) {
        responsive = true;
    }
    if (theme == undefined) {
        theme = 'pws_theme_dark_grey';
    }
    if (rtl == undefined) {
        rtl = false;
    }

    Elem.pwstabs({
        effect: effect,
        defaultTab: defaultTab,
        containerWidth: containerWidth,
        tabsPosition: tabsPosition,
        horizontalPosition: horizontalPosition,
        verticalPosition: verticalPosition,
        responsive: responsive,
        theme: theme,
        rtl: rtl,
    });

    return true;
}

function wc_rbp_div_block(id) {
    jQuery(id).block({
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.6
        }
    });
}

function wc_rbp_div_unblock(id) {
    jQuery(id).unblock();
}

function wc_rbp_add_variation_selectbox() {    
    
    jQuery('.wcrbpvariationbx').appendTo('#wc-rbp-product-editor .hndle span');

    jQuery('.wcrbpvariationbx').selectize({
        plugins: ['remove_button', 'restore_on_backspace'],
        persist: false,
        create: false,
        onChange: function (val) {
            wc_rbp_div_block('div#wc-rbp-product-editor div.inside');
            var select = this;
            var parentID = jQuery('input#post_ID').val();
            jQuery.ajax({
                url: ajaxurl + '?action=wc_rbp_metabox_refersh&pid=' + val + '&parentID='+parentID ,
                method: 'GET',
                data: '',

            }).done(function (res) {
                if(res.success == true){
                    select.destroy();
                    jQuery('.wcrbpvariationbx').remove();
                    jQuery('#wc-rbp-product-editor .inside').html(res.data);
                    wc_rbp_render_wootabs();
                    wc_rbp_check_price_Status();
                    wc_rbp_render_checkbox();
                    wc_rbp_add_variation_selectbox();
                    wc_rbp_div_unblock('div#wc-rbp-product-editor div.inside');
                }
                
            })
        }
    });
}

function wc_rbp_check_price_Status() {
    jQuery('.wcrbp-tab-nav > li').each(function () {
        if (jQuery(this).attr('data-status') == 'yes') {
            var divClass = 'div.wcrbp-tab-panel-' + jQuery(this).attr('data-panel');
            var filled = 0;
            var unfilled = 0;
            var totalf = 0;
            jQuery(divClass).find('input').each(function () {
                if (jQuery(this).val() == '') {
                    unfilled = unfilled + 1;
                } else {
                    filled = filled + 1;
                }
            });

            totalf = filled + unfilled;

            jQuery(this).find('.wc-rbp-tab-status').removeClass('bgred');
            jQuery(this).find('.wc-rbp-tab-status').removeClass('bggreen');
            jQuery(this).find('.wc-rbp-tab-status').removeClass('bgblue');

            if (filled == 0 && unfilled > 0) {
                jQuery(this).find('.wc-rbp-tab-status').addClass('bgred');
            } else if (filled > 0 && unfilled > 0) {
                jQuery(this).find('.wc-rbp-tab-status').addClass('bgblue');
            } else if (filled > 0 && unfilled == 0) {
                jQuery(this).find('.wc-rbp-tab-status').addClass('bggreen');
            }
        }

    });
}

function wc_rbp_check_product_rbp_status() {
    var status = jQuery('input#enable_role_based_price').is(':checked');

    if (status) {
        jQuery('.tab_container input[type=text],.tab_container input[type=number]').removeAttr('disabled');
    } else {
        jQuery('.tab_container input[type=text],.tab_container input[type=number]').attr('disabled', 'disabled');
    }
}