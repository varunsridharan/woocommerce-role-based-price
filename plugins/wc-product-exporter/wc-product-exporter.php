<?php
/**
 * Plugin Name: WooCommerce Product Export
 * Plugin URI:
 * Version: 1.0
 * Description: This Addon Integration With Default Product Exporter In WooCommerce And Provides Options To Export Role
 * Based Prices Author: Varun Sridharan Author URI: http://varunsridharan.in Last Update: 2017-07-07 Category:
 * Tools,Price Export
 */

if( ! defined('WC_RBP_PLUGIN') ) {
    die;
}

class wc_product_price_exporter {

    public function __construct() {
        $this->is_wcrbp_export = FALSE;
        $this->key_vals();
        add_filter("woocommerce_product_export_product_default_columns", array( $this, 'add_option' ));
        add_filter("woocommerce_product_export_row_data", array( $this, 'add_wcrbp_prices' ), 1, 2);
        add_action("wp_ajax_woocommerce_do_ajax_product_export", array( $this, 'check_wcrbp_export' ), 1);
    }

    public function key_vals() {
        $allowed_user_roles = wc_rbp_allowed_roles();
        $allowed_prices     = wc_rbp_allowed_price();
        $active_cols        = array( 'wcrbp_status' => '' );
        $user_roles         = array();
        foreach( $allowed_user_roles as $role_id ) {
            if( ! isset($user_roles['wcrbp_' . $role_id]) ) {
                $user_roles['wcrbp_' . $role_id] = array();
            }
            foreach( $allowed_prices as $price_id ) {
                if( ! isset($active_cols['wcrbp_' . $role_id . '_' . $price_id]) ) {
                    $user_roles['wcrbp_' . $role_id]['wcrbp_' . $role_id . '_' . $price_id] = '';
                    $active_cols['wcrbp_' . $role_id . '_' . $price_id]                     = array( 'role'  => $role_id,
                                                                                                     'price' => $price_id,
                    );
                }
            }
        }

        $this->active_cols = $active_cols;
        $this->user_roles  = $user_roles;
        return $this->active_cols;
    }

    public function check_wcrbp_export() {

        if( isset($_POST['selected_columns']) ) {
            if( is_array($_POST['selected_columns']) ) {
                if( in_array('wcrbp_price', $_POST['selected_columns']) ) {
                    $this->remove_post_data('wcrbp_price');
                    $_POST['selected_columns'] = array_merge($_POST['selected_columns'], array_keys($this->key_vals()));
                    $this->is_wcrbp_export     = TRUE;

                } else {
                    foreach( array_keys($this->user_roles) as $role ) {
                        if( in_array($role, $_POST['selected_columns']) ) {
                            $this->remove_post_data($role);
                            $_POST['selected_columns'] = array_merge($_POST['selected_columns'], array_keys($this->user_roles[$role]));
                            $this->is_wcrbp_export     = TRUE;
                        }
                    }
                }
            }
        }

        if( $this->is_wcrbp_export ) {
            add_filter("woocommerce_product_export_column_names", array( $this, 'custom_col' ));
        }
    }

    public function remove_post_data($key) {
        foreach( $_POST['selected_columns'] as $id => $vl ) {
            if( $vl == $key ) {
                unset($_POST['selected_columns'][$id]);
            }
        }
    }

    public function add_wcrbp_prices($row, $product) {
        if( $this->is_wcrbp_export ) {
            $prices = wc_rbp_price($product->get_id(), 'all', 'all');
            if( isset($row['wcrbp_status']) )
                $row['wcrbp_status'] = wc_rbp_product_status($product->get_id(), TRUE);

            if( ! empty($prices) ) {
                foreach( $prices as $user => $price_types ) {
                    foreach( $price_types as $type => $price ) {
                        if( isset($row['wcrbp_' . $user . '_' . $type]) ) {
                            $row['wcrbp_' . $user . '_' . $type] = $price;
                        }
                    }
                }
            }
        }

        return $row;
    }

    public function custom_col($cols) {
        $new_cols           = $this->active_cols;
        $allowed_user_roles = wc_rbp_allowed_roles();
        $allowed_prices     = wc_rbp_allowed_price();
        foreach( $allowed_user_roles as $user_role_id ) {
            foreach( $allowed_prices as $price ) {
                if( ! isset($cols['wcrbp_' . $user_role_id . '_' . $price]) ) {
                    $cols['wcrbp_' . $user_role_id . '_' . $price] = $user_role_id . '_' . $price;
                }
            }
        }
        $cols['wcrbp_status'] = 'wcrbp_status';
        return $cols;
    }

    public function add_option($options) {
        $options['wcrbp_price'] = __("WC Role Based Price All", WC_RBP_TXT);

        $allowed_user_roles      = wc_rbp_allowed_roles();
        $user_roles              = wc_rbp_get_wp_roles();
        $options['wcrbp_status'] = __("WC RBP Status", WC_RBP_TXT);
        foreach( $user_roles as $user_role_id => $user_role_name ) {
            if( in_array($user_role_id, $allowed_user_roles) ) {
                if( ! isset($options['wcrbp_' . $user_role_id]) ) {
                    $options['wcrbp_' . $user_role_id] = __('WC Role Based ', WC_RBP_TXT) . $user_role_name['name'] . __(' Price ', WC_RBP_TXT);
                }
            }
        }
        return $options;
    }
}

return new wc_product_price_exporter;