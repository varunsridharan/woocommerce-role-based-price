<?php

class WooCommerce_Role_Based_Price_Product_Metabox {

    public function __construct() {
        add_action('add_meta_boxes_product', array( $this, 'add_metabox' ));
    }

    public function add_metabox($post) {
        add_meta_box('wc-rbp-product-editor', __('WC Role Based Price Editor', WC_RBP_TXT), array(
            $this,
            'render_price_editor_metabox',
        ), 'product', 'advanced', 'high');
    }

    public function render_price_editor_metabox($post) {
        if( is_object($post) ) {
            $id = $post->ID;
        } else {
            $id = $post;
        }

        $prod     = NULL;
        $prodType = $this->get_post_type($id);

        $url         = admin_url('admin-ajax.php?action=wc_rbp_save_product_prices');
        $render_info = '<div class="wc-rbp-metabox-container" method="POST" action="' . $url . '" > ';
        $render_info .= wc_rbp_get_ajax_overlay();

        $this->allowed_roles    = wc_rbp_allowed_roles();
        $this->registered_roles = wc_rbp_get_wp_roles();

        $args                   = array();
        $args['render_default'] = TRUE;
        $args['html']           = '';
        $args['postid']         = $id;
        $args['mb']             = $this;
        $args['parentID']       = isset($_REQUEST['parentID']) ? $_REQUEST['parentID'] : $id;
        $args['selectedID']     = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : $id;

        $args = apply_filters_ref_array('wc_rbp_metabox_render', array( &$args ));

        if( $args['render_default'] ) {
            $render_info .= $this->render_default_metabox($id, $post, $args);
        } else {
            $render_info .= $args['html'];
        }


        $render_info .= '</div>';

        echo $render_info;
    }

    public function get_post_type($id) {
        $product_type = '';

        if( $terms = wp_get_object_terms($id, 'product_type') ) {
            $product_type = sanitize_title(current($terms)->name);
        } else {
            $product_type = apply_filters('default_product_type', 'simple');
        }
        return $product_type;
    }

    public function render_default_metabox($id, $post, $args, $type = 'default') {
        $product_type = $this->get_post_type($id);

        $render_info = '';
        $post_type   = get_post_type($id);

        if( $product_type == 'variable' || $post_type == 'product_variation' ) {
            $render_info .= $this->generate_variation_selectbox($args['parentID'], $id);
        } else {
            ob_start();
            do_action("wc_rbp_metabox_header", $id, $product_type, $post, $this);
            $render_info .= ob_get_contents();
            ob_end_clean();
        }

        $prod     = wc_get_product($id);
        $prodType = 'simple';
        if( is_object($prod) ) {
            if( wc_rbp_is_wc_v('>=', '3.0.0') ) {
                $prodType = $prod->get_type();
            } else {
                $prodType = $prod->product_type;
            }
        }

        ob_start();
        do_action('wc_rbp_before_metabox_content', $prod, $prodType);
        $render_info .= ob_get_contents();
        ob_end_clean();

        $tabs    = $this->get_metabox_tabs($id, $prodType);
        $content = $this->get_metabox_content($id, $tabs, $prod, $prodType);

        $render_info .= wc_rbp_generate_tabs($tabs, $content);
        $render_info .= wc_rbp_get_editor_fields($type);
        $render_info .= '<input type="hidden" id="wc_rbp_product_id" name="product_id" value="' . $id . '" /> ';

        ob_start();
        do_action('wc_rbp_after_metabox_content', $prod, $prodType);
        $render_info .= ob_get_contents();
        ob_end_clean();

        $render_info .= $this->render_metabox_footer($id);

        return $render_info;
    }

    public function generate_variation_selectbox($id, $selected = '') {

        $header = $this->render_selectbox_header();
        $args   = array(
            'post_type'      => 'product_variation',
            'post_status'    => array( 'private', 'publish' ),
            'posts_per_page' => -1,
            'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
            'post_parent'    => $id,
            'fields'         => 'ids',
        );

        $variations = get_children($args);
        $return     = ' <optgroup  label="' . __("Variations", WC_RBP_TXT) . '"> ';
        foreach( $variations as $ids ) {
            $prod = wc_get_product($ids);
            $name = '#' . $ids . ' | ';

            if( wc_rbp_is_wc_v('>=', '3.0') ) {
                $name .= ' ' . wc_get_formatted_variation($prod, TRUE);
            } else {
                $name .= ' ' . $prod->get_formatted_variation_attributes(TRUE);
            }


            $selecteds = '';
            if( $selected == $ids ) {
                $selecteds = 'selected';
            }
            $return .= '<option data-type="variation" value="' . $ids . '" ' . $selecteds . '>' . $name . '</option>';
        }
        $return .= ' </optgroup> ';

        $footer = $this->render_selectbox_footer();
        $return = apply_filters("role_based_price_metabox_variation_select", $return, $selected, $id);
        $return = $header . $return . $footer;
        return $return;
    }

    public function render_selectbox_header($placeholder = '') {
        if( empty($placeholder) ) {
            $placeholder = __("Select A Variation : ", WC_RBP_TXT);
        }
        $header = '<select id="wc_rbp_variation_select" style="width: 30%; display: inline-block; vertical-align: middle; margin-left: 10px;" name="wc_rbp_variation_select" class="wcrbpvariationbx"> <option value="">' . $placeholder . '</option>';
        return apply_filters("role_based_price_admin_selectbox_header", $header);
    }

    public function render_selectbox_footer() {
        return apply_filters("role_based_price_admin_selectbox_header", '</select>');
    }

    public function get_metabox_tabs($id, $prodType) {
        $tabs             = apply_filters('wc_rbp_before_default_product_tabs', array(), $id, $prodType);
        $registered_roles = $this->registered_roles;

        foreach( $this->allowed_roles as $role ) {
            if( isset($registered_roles[$role]) ) {
                $icon        = 'dashicons dashicons-admin-users';
                $tabs[$role] = array(
                    'title'       => $registered_roles[$role]['name'],
                    'icon'        => $icon,
                    'show_status' => TRUE,
                );
            }
        }

        $tabs = apply_filters('wc_rbp_after_default_product_tabs', $tabs, $id, $prodType);
        return $tabs;
    }

    public function get_metabox_content($id, $tabs, $prod, $prodType) {
        $content = array();

        foreach( $tabs as $tab_id => $val ) {
            ob_start();
            do_action('wc_rbp_price_edit_tab_' . $tab_id . '_before', $id, $prodType, $prod, $tab_id);
            do_action('wc_rbp_price_edit_tab_' . $tab_id, $id, $prodType, $prod, $tab_id);
            do_action('wc_rbp_price_edit_tab_' . $tab_id . '_after', $id, $prodType, $prod, $tab_id);

            $content[$tab_id] = ob_get_contents();
            ob_end_clean();
        }

        return $content;
    }

    public function render_metabox_footer($id) {
        $base_price   = $this->get_base_price($id);
        $clbtn        = '';
        $product_type = wp_get_post_terms($id, 'product_type', array( 'fields' => 'names' ));

        if( in_array('variable', $product_type) || get_post_type($id) == 'product_variation' ) {
            $clbtn = '<button style="float:left;" type="button" id="wc_rbp_clear_trasient" class="button button-secondary">' . __('Clear Cache', WC_RBP_TXT) . '</button>';
        }

        return ' <h2 class="" style="margin: 0px -12px -12px; border-top: 1px solid #eee; text-align:right;">
                <span class="wc_rbp_base_product_price">' . $base_price . '</span>
                ' . $clbtn . '
                <button type="button" id="wc_rbp_update_price" class="button button-primary">' . __('Save Price', WC_RBP_TXT) . '</button></h2> ';
    }

    public function get_base_price($id) {
        $pro   = wc_get_product($id);
        $price = '';
        if( is_object($pro) ) {
            $price = array();
            $this->hook_filter(TRUE);
            $price['regular_price'] = wc_rbp_price_types('regular_price') . ' : ';
            $price['regular_price'] .= wc_price($pro->get_regular_price());

            $price['selling_price'] = wc_rbp_price_types('selling_price') . ' : ';
            $price['selling_price'] .= wc_price($pro->get_sale_price());
            $this->hook_filter(FALSE);
            $price = implode(' | ', $price);
        }
        $head = '<span class="headTxt">' . __("WC Product Price : ") . '</span>' . $price;
        return $head;
    }

    public function hook_filter($hook = TRUE) {
        if( $hook == TRUE ) {
            add_filter('role_based_price_status', array( $this, 'base_price_return_false' ));
        }
        if( ! $hook == TRUE ) {
            remove_filter('role_based_price_status', array( $this, 'base_price_return_false' ));
        }
    }

    public function base_price_return_false($s) {
        return FALSE;
    }

}

return new WooCommerce_Role_Based_Price_Product_Metabox;