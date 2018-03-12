<div class="wc_rbp_popup_section acs_popup_section">

    <div class="wc_rbp_pop_field"><h3><?php _e('Aelia Currency Switcher', WC_RBP_TXT); ?></h3></div>

    <div class="wc_rbp_pop_field">
        <?php
        $tabs        = array();
        $content     = array();
        $output_html = '';

        foreach( $allowed_currency as $currency ) {
            if( $this->base_currency == $currency ) {
                continue;
            }
            $symbol = get_woocommerce_currency_symbol($currency);
            $symbol = ! empty($symbol) ? ' (' . $symbol . ') ' : ' (' . $currency . ') ';

            $tabs[$tab_id . '-' . $currency] = array( 'title' => $currency . ' ' . $symbol );

            $output_html = '<div class="wc_rbp_price_container wc_rbp_popup_section wc_rbp_popup_section_' . $tab_id . '_' . $currency . '">';
            foreach( $allowed_price as $price ) {
                $value    = wc_rbp_acs_price($product_id, $tab_id, $currency, $price);
                $text     = __('Enter Product\'s %s For %s Currency', WC_RBP_TXT);
                $field_id = 'wc_rbp_acs[' . $tab_id . '][' . $currency . '][' . $price . ']';
                $defaults = array(
                    'type'              => 'text',
                    'label'             => $ex_price[$price] . $symbol,
                    'description'       => sprintf($text, $ex_price[$price], $currency),
                    'class'             => array(),
                    'label_class'       => array(),
                    'input_class'       => array( 'wc_input_price', $price, 'wc_rbp_' . $price ),
                    'return'            => TRUE,
                    'custom_attributes' => array(),
                );

                $output_html .= '<div class="wc_rbp_pop_field_50 wc_rbp_pop_field_' . $price . '">';
                $output_html .= woocommerce_form_field($field_id, $defaults, $value);
                $output_html .= '</div>';

            }
            $output_html                        .= '</div>';
            $content[$tab_id . '-' . $currency] = $output_html;
        }

        echo wc_rbp_generate_tabs($tabs, $content, array( 'tab_style' => 'default' ));
        ?>
    </div>

</div>