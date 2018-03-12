;
( function ($, window, document, undefined) {
    'use strict';

    $.WCRBP_SETTINGS = $.WCRBP_SETTINGS || {};

    $.WCRBP_SETTINGS.addons_html = '';

    $.WCRBP_SETTINGS.http_reffer = '';

    $.WCRBP_SETTINGS.handle_settings_page_url = function () {
        var id = window.location.hash;
        jQuery('.wc_rbp_settings_submenu a').removeClass('current');
        jQuery('.wc_rbp_settings_submenu a[href="' + id + '" ]').addClass('current');
        if ( id == '' ) {
            jQuery('.wc_rbp_settings_submenu a:first').addClass('current');
            id = jQuery('.wc_rbp_settings_submenu a:first').attr('href');
        }
        $.WCRBP_SETTINGS.http_reffer = jQuery('input[name=_wp_http_referer').val();

        $.WCRBP_SETTINGS.show_settings(id);
    }

    $.WCRBP_SETTINGS.show_settings = function (elem) {
        jQuery('div.wc_rbp_settings_content').hide();
        elem = elem.replace('#', '#settings_');
        jQuery(elem).show();
    }

    $.WCRBP_SETTINGS.settings_click_handler = function () {
        var id = jQuery(this).attr('href');
        jQuery('.wc_rbp_settings_submenu a').removeClass('current');
        jQuery(this).addClass('current');
        $.WCRBP_SETTINGS.show_settings(id);
        jQuery('input[name=_wp_http_referer').val($.WCRBP_SETTINGS.http_reffer + id);
    }

    $.WCRBP_SETTINGS.handle_activate_deactivate_addons = function (elem, $class) {
        if ( typeof ( $class ) === 'undefined' ) $class = '.wc-rbp-deactivate-now';
        var clicked = elem;
        var slug = elem.parent().attr('data-pluginslug');
        var parent_div = '.plugin-card-' + slug;
        var height = jQuery(parent_div).innerHeight();
        var width = jQuery(parent_div).innerWidth();
        jQuery(parent_div + ' .wc_rbp_ajax_overlay').css('height', height + 'px').css('width', width + 'px').fadeIn();
        clicked.attr('disabled', 'disable');
        var link = clicked.attr('href');
        jQuery.ajax({
            method: 'GET',
            url: link,
        }).done(function (response) {
            var status = response.success;
            jQuery(parent_div + ' .wc_rbp_ajax_overlay').fadeOut();
            clicked.removeAttr('disabled');
            if ( status ) {
                clicked.hide();
                jQuery(parent_div).find($class).fadeIn();
            }

            jQuery(parent_div).find('.wc_rbp_ajax_response').hide().html(response.data.msg).fadeIn(function () {
                setTimeout(function () {
                    jQuery(parent_div).find('.wc_rbp_ajax_response').fadeOut();
                }, 5000);
            });

            jQuery.ajax({
                method: 'GET',
                url: ajaxurl + '?action=wc_rbp_get_addons_html',
            }).done(function (response) {
                addons_html = jQuery(response);
            });

        });
    }


    $(document).ready(function () {
        if ( jQuery('div.wc_rbp_addon_listing').size() > 0 ) {
            jQuery('p.submit').remove();
        }

        jQuery('select.wc-rbp-enhanced-select').selectize({
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop'],
            persist: false,
            create: true,
        });

        jQuery('.wc_rbp_settings_submenu a').click($.WCRBP_SETTINGS.settings_click_handler);

        if ( jQuery('.wc_rbp_settings_submenu').size() > 0 ) {
            $.WCRBP_SETTINGS.handle_settings_page_url();
        }

        jQuery('.wc_rbp_addon_listing').on('click', '.wc-rbp-activate-now', function () {
            $.WCRBP_SETTINGS.handle_activate_deactivate_addons(jQuery(this), '.wc-rbp-deactivate-now')
        });

        jQuery('.wc_rbp_addon_listing').on('click', '.wc-rbp-deactivate-now', function () {
            $.WCRBP_SETTINGS.handle_activate_deactivate_addons(jQuery(this), '.wc-rbp-activate-now')
        });

        $.WCRBP_SETTINGS.addons_html = jQuery('.wc_rbp_addon_listing').clone();

        jQuery('ul.wc_rbp_addons_category li a:first').addClass('current');

        jQuery('ul.wc_rbp_addons_category li a').click(function () {
            var cat = jQuery(this).attr('data-category');
            var NewDis = 'div.wc-rbp-addon-' + cat;
            jQuery('ul.wc_rbp_addons_category li a').removeClass('current');
            jQuery(this).addClass('current');
            jQuery('.wc_rbp_addon_listing').html($.WCRBP_SETTINGS.addons_html.find(NewDis).clone());
        });

        jQuery('ul.wc_rbp_addons_category li a').each(function () {
            var category = jQuery(this).attr('data-category');
            var catCount = jQuery('.wc-rbp-addon-' + category).size();
            jQuery(this).append(' <span class="catCount"> (' + catCount + ') </span>');

        });

        jQuery('div.addons-search-form input.wp-filter-search').keyup(function () {
            var val = jQuery(this).val();
            var html_source = $.WCRBP_SETTINGS.addons_html.clone();
            if ( val == '' ) {
                jQuery('.wc_rbp_addon_listing').html(html_source);
                jQuery('.wc-rbp-addon-all').show();
            } else {
                html_source = jQuery(html_source).find(".plugin-card:contains('" + val + "')").not().remove();
                jQuery('.wc_rbp_addon_listing').html(html_source);
            }
        })

    });

} )(jQuery, window, document);
