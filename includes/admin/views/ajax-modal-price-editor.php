<?php global $type, $product_id; ?>

<div class="wc_rbp_price_editor_fields" style="display:none;">
    <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" id="wc_rbp_price_editor_form">
        <div class="wc_rbp_hidden_fields">
            <?php echo wc_rbp_get_editor_fields($type); ?>
        </div>
        <div class="hide hidden wc_rbp_price_editor_ajax_response"></div>
        <?php

        $tabs             = array();
        $allowed_roles    = wc_rbp_allowed_roles();
        $registered_roles = wc_rbp_get_wp_roles();

        foreach( $allowed_roles as $role ) {
            if( isset($registered_roles[$role]) ) {
                $tabs[$role] = $registered_roles[$role]['name'];
            }
        }

        $tabs    = apply_filters('wc_rbp_price_editor_tabs', $tabs, $product_id, $type);
        $tab_pos = wc_rbp_option('price_editor_tab_pos');
        extract(wc_rbp_get_tab_pos($tab_pos));

        if( $type == 'simple' ) {
            echo '<input type="hidden" name="product_id" value="' . $product_id . '" /> ';
        }

        do_action('wc_rbp_price_edit_top', $product_id, $type);
        echo '<div class="tab_container">';
        echo '<div class="wc_rbp_tabs" data-tabsPosition="' . $tab_pos . '" 
data-horizontalPosition="' . $horizontalPosition . '"
data-verticalPosition="' . $verticalPosition . '">';

        foreach( $tabs as $tabID => $name ) {
            echo '<div data-pws-tab-name="' . $name . '" data-pws-tab="' . $tabID . '" class="pws_hide pws_tab_single" data-pws-tab-id="' . $tabID . '">';
            do_action('wc_rbp_price_edit_tab_before', $product_id, $type, $tabID);
            do_action('wc_rbp_price_edit_tab_' . $tabID . '_before', $product_id, $type, $tabID);
            do_action('wc_rbp_price_edit_tab_' . $tabID, $product_id, $type, $tabID);
            do_action('wc_rbp_price_edit_tab_' . $tabID . '_after', $product_id, $type, $tabID);
            do_action('wc_rbp_price_edit_tab_after', $product_id, $type, $tabID);
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        do_action('wc_rbp_price_edit_bottom', $product_id, $type);
        ?>
    </form>
</div>