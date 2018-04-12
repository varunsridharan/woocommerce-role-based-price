<?php
/**
 * Plugin Name: WooCommerce Product Importer
 * Plugin URI:
 * Version: 1.0
 * Description: This Addon Integration With Default  Product Importer In WooCommerce And Provides Options To Import,
 * Role Based Prices
 * Author: Varun Sridharan
 * Author URI: http://varunsridharan.in
 * Last Update: 2017-07-07
 * Category: Tools,Price Import
 */

if( ! defined('WC_RBP_PLUGIN') ) {
    die;
}

class wc_product_price_importer {

    public function __construct() {
        add_filter("woocommerce_csv_product_import_mapping_options", array( $this, 'add_import_options' ));
        add_action("woocommerce_product_import_inserted_product_object", array( $this, 'save_price' ), 10, 2);
    }

    public function save_price($product, $data) {
        $this->generate_options_data();
        $obj         = $this->options_product_save;
        $obk         = array_keys($obj);
        $final_price = array();
        $status      = FALSE;
        foreach( $data as $id => $value ) {
            if( $id == 'wcrbp_status' ) {
                if( $value == 'no' ) {
                    $status = FALSE;
                } else if( $value == 'yes' ) {
                    $status = TRUE;
                } else if( floatval($value) == 0 ) {
                    $status = FALSE;
                } else if( floatval($value) == 1 ) {
                    $status = TRUE;
                }

            } else if( in_array($id, $obk) ) {
                $attribute  = $obj[$id];
                $role       = $attribute['role'];
                $price_type = $attribute['price'];
                if( $value != '' ) {
                    $final_price[$role][$price_type] = wc_format_decimal($value);
                }
            }
        }
        if( ! empty($final_price) )
            wc_rbp_update_role_based_price($product->get_id(), $final_price);

        wc_rbp_update_role_based_price_status($product->get_id(), $status);

    }

    public function generate_options_data() {
        if( isset($this->options_updated) ) {
            return $this->options_updated;
        }
        $allowed_user_roles = wc_rbp_allowed_roles();
        $allowed_prices     = wc_rbp_allowed_price();
        $options            = array( "wcrbp_status" => __("Price Status") );
        $options2           = array( "wcrbp_status" => __("Price Status") );
        foreach( wc_rbp_get_wp_roles() as $role_id => $data ) {
            if( in_array($role_id, $allowed_user_roles) ) {
                foreach( $allowed_prices as $price_id ) {
                    $options[$role_id . '_' . $price_id]  = ' WC Role Based ' . $data['name'] . ' - ' . wc_rbp_price_types($price_id);
                    $options2[$role_id . '_' . $price_id] = array( 'role' => $role_id, 'price' => $price_id );
                }
            }
        }
        $this->options_updated      = $options;
        $this->options_product_save = $options2;
        return $options;
    }

    public function add_import_options($options) {
        $options['wcrbp_options'] = array(
            'name'    => __("WC Role Based Price"),
            'options' => $this->generate_options_data(),
        );
        return $options;
    }
}

return new wc_product_price_importer;