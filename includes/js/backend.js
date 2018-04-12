;
( function ($, window, document) {
    'use strict';

    $.WCRBP = $.WCRBP || {};


    /**
     * Global Functions
     */
    $.WCRBP.validate_selling_price = function (e) {
        var sale_price_field = $(this),
            regular_price_field;

        regular_price_field = sale_price_field.parents('.wc_rbp_price_container').find('.wc_rbp_regular_price');

        var sale_price = parseFloat(window.accounting.unformat(sale_price_field.val(), woocommerce_admin.mon_decimal_point));
        var regular_price = parseFloat(window.accounting.unformat(regular_price_field.val(), woocommerce_admin.mon_decimal_point));


        if ( sale_price >= regular_price ) {
            $(document.body).triggerHandler('wc_add_error_tip', [$(this), 'i18_sale_less_than_regular_error']);
            if ( 'change' === e.type ) {
                if ( sale_price >= regular_price ) {
                    $(this).val('');
                    $('div#wc-rbp-product-editor #wc_rbp_update_price').removeAttr('disabled');
                }
            } else {
                $('div#wc-rbp-product-editor #wc_rbp_update_price').attr('disabled', 'disabled');
            }
        } else {
            $(document.body).triggerHandler('wc_remove_error_tip', [$(this), 'i18_sale_less_than_regular_error']);
            $('div#wc-rbp-product-editor #wc_rbp_update_price').removeAttr('disabled');
        }
    };

    $.WCRBP.tab_navigation = function (e) {
        e.preventDefault();

        var $li = jQuery(this).parent(),
            panel = $li.data('panel'),
            $wrapper = $li.closest('.wcrbp-tabs'),
            $panel = $wrapper.find('.wcrbp-tab-panel-' + panel);

        $li.addClass('wcrbp-tab-active').siblings().removeClass('wcrbp-tab-active');
        $panel.show().siblings().hide();
    };

    $.WCRBP.render_wootabs = function () {
        $(".wcrbp-tab-nav").on("click", 'a', $.WCRBP.tab_navigation);

        if ( !$('.wcrbp-tab-active').is('visible') ) {
            var activePane = $('.wcrbp-tab-panel[style*="block"]').index();

            if ( activePane >= 0 ) {
                $('.wcrbp-tab-nav li').removeClass('wcrbp-tab-active');
                $('.wcrbp-tab-nav li').eq(activePane).addClass('wcrbp-tab-active');
            }
        }

        $('.wcrbp-tab-active a').trigger('click');
        $('.wcrbp-tabs-no-wrapper').closest('.postbox').addClass('wcrbp-tabs-no-controls');


    };

    $.WCRBP.block = function ($elem) {
        $elem.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    };

    $.WCRBP.unblock = function ($elem) {
        $elem.unblock();
    };

    $.WCRBP.render_price_status = function () {
        $('.wcrbp-tab-nav > li').each(function () {
            if ( $(this).attr('data-status') == 'yes' ) {
                var $this = $(this),
                    divClass = 'div.wcrbp-tab-panel-' + $this.attr('data-panel'),
                    filled = 0,
                    unfilled = 0,
                    totalf = 0;

                $(divClass).find('input[type=text]').each(function () {
                    if ( $(this).hasClass("include_count") || !$(this).hasClass("exclude_count") ) {
                        if ( $(this).val() == '' ) {
                            unfilled = unfilled + 1;
                        } else {
                            filled = filled + 1;
                        }
                    }
                });

                totalf = filled + unfilled;

                $this.find('.wc-rbp-tab-status').removeClass('bgred');
                $this.find('.wc-rbp-tab-status').removeClass('bggreen');
                $this.find('.wc-rbp-tab-status').removeClass('bgblue');

                if ( filled == 0 && unfilled > 0 ) {
                    $this.find('.wc-rbp-tab-status').addClass('bgred');
                } else if ( filled > 0 && unfilled > 0 ) {
                    $this.find('.wc-rbp-tab-status').addClass('bgblue');
                } else if ( filled > 0 && unfilled == 0 ) {
                    $this.find('.wc-rbp-tab-status').addClass('bggreen');
                }
            }

        });
    };

    $.WCRBP.add_variation_selectbox = function () {
        if ( $(".wcrbpvariationbx").size() > 0 ) {
            $(".wcrbpvariationbx").appendTo("#wc-rbp-product-editor .hndle span");
        }

        if ( jQuery('.wcrbpvariationbx').size() > 0 ) {
            jQuery('.wcrbpvariationbx').selectize({
                plugins: ['remove_button', 'restore_on_backspace'],
                persist: false,
                create: false,
                onChange: function (value) {
                    $.WCRBP.block($('div#wc-rbp-product-editor div.inside'));
                    var $select = this;
                    var $parentID = $('input#post_ID').val();
                    $.ajax({
                        url: ajaxurl + '?action=wc_rbp_metabox_refersh&pid=' + value + '&parentID=' + $parentID,
                        method: "GET",
                        data: '',
                    }).done(function (response) {
                        if ( response.success === true ) {
                            $select.destroy();
                            $('.wcrbpvariationbx').remove();
                            $('#wc-rbp-product-editor .inside').html(response.data);
                            $.WCRBP.render_wootabs();
                            $.WCRBP.add_variation_selectbox();
                            $("#wc-rbp-product-editor .inside input.wc_rbp_checkbox").wcrbp_checkbox();
                            $.WCRBP.unblock($('div#wc-rbp-product-editor div.inside'));
                            $.WCRBP.render_price_status();
                        }
                    })
                }
            });
        }
    };

    $.WCRBP.move_selectbox_metabox = function () {
        $('#wc-rbp-product-editor').find('.hndle').unbind('click');

        $('#wc-rbp-product-editor').on('click', '.hndle', function (event) {
            event.preventDefault();
            if ( $(event.target).filter('input, option, label, select,  div, span').size() ) {
                jQuery('#wc-rbp-product-editor').toggleClass('closed');
            }

            return;
        });
    };

    $.WCRBP.action_save_product_prices = function () {
        var $clickedBtn = $(this),
            $data = $('div#wc-rbp-product-editor :input').serialize(),
            $form = $('.wc-rbp-metabox-container'),
            $action = $form.attr('action'),
            $method = $form.attr('method');

        $clickedBtn.attr('disabled', 'disable');

        $.WCRBP.block($('div#wc-rbp-product-editor div.inside'));

        $.ajax({
            url: $action,
            method: $method,
            data: $data,
        }).done(function (data) {
            $clickedBtn.removeAttr('disabled');
            $.WCRBP.unblock($('div#wc-rbp-product-editor div.inside'));
            $('div.wc_rbp_hidden_fields').html(data.data.hidden_fields);
            $('div.wc_rbp_price_editor_ajax_response').html(data.data.html).fadeIn('slow');
            setTimeout(2000, function () {
                jQuery('div.wc_rbp_price_editor_ajax_response').fadeOut('slow');
            });
        })
    };

    $.WCRBP.action_clear_product_prices = function () {
        var $clickedBtn = $(this);
        $clickedBtn.attr('disabled', 'disable');

        $.WCRBP.block($('div#wc-rbp-product-editor div.inside'));

        $.ajax({
            url: ajaxurl,
            method: 'post',
            data: {post_id: $("#post_ID").val(), action: "wc_rbp_clear_variation_cache"}
        }).done(function (data) {
            $clickedBtn.removeAttr('disabled');
            $.WCRBP.unblock($('div#wc-rbp-product-editor div.inside'));
            $('div.wc_rbp_price_editor_ajax_response').html(data.data.html).fadeIn('slow');
            setTimeout(2000, function () {
                jQuery('div.wc_rbp_price_editor_ajax_response').fadeOut('slow');
            });
        })
    };

    $.WCRBP.basic_init_metabox = function () {
        $(document.body).on('keyup change', '.wc_rbp_selling_price', $.WCRBP.validate_selling_price);

        if ( $("input.wc_rbp_checkbox").size() > 0 ) {
            $("input.wc_rbp_checkbox").wcrbp_checkbox();
        }

        if ( $(".wcrbpvariationbx").size() > 0 ) {
            $.WCRBP.add_variation_selectbox();
        }

        if ( $(".wcrbp-tabs").size() > 0 ) {
            $.WCRBP.block($('div#wc-rbp-product-editor div.inside'));
            $.WCRBP.render_wootabs();
            $.WCRBP.move_selectbox_metabox();
            $.WCRBP.render_price_status();
        }

        $('body').on('blur', '#wc-rbp-product-editor :input', function () {
            $.WCRBP.render_price_status();
        });

        $("body").on("click", 'div#wc-rbp-product-editor #wc_rbp_update_price', $.WCRBP.action_save_product_prices);
        $("body").on("click", 'div#wc-rbp-product-editor #wc_rbp_clear_trasient', $.WCRBP.action_clear_product_prices);

    };

    /**
     * Internal Functions
     */
    $.fn.wcrbp_checkbox = function () {
        return this.each(function () {
            var $this = $(this),
                size = $this.attr('data-size'),
                color = $this.attr('data-color'),
                secondaryColor = $this.attr('data-secondaryColor'),
                jackColor = $this.attr("data-jackColor"),
                jackSecondaryColor = $this.attr("data-jackSecondaryColor"),
                className = $this.attr("data-className"),
                disabled = $this.attr('data-disabled'),
                disabledOpacity = $this.attr("data-disabledOpacity"),
                speed = $this.attr("data-speed");

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


        })
    };

    $(document).ready(function () {
        $.WCRBP.basic_init_metabox();
    });

    $(window).load(function () {
        if ( $('#wc-rbp-product-editor').size() > 0 ) {
            $.WCRBP.move_selectbox_metabox();
            $.WCRBP.unblock($('div#wc-rbp-product-editor div.inside'));
        }
    })

} )(jQuery, window, document);


