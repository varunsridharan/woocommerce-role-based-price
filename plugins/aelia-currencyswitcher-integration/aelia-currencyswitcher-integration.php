<?php
/**
 * Plugin Name: Aelia Currency Switcher Integration
 * Plugin URI:
 * Version: 1.0
 * Description: Works With Aelia Currency Switcher Integration And Allows you to set product's role based price on
 * currency based Author: Varun Sridharan Author URI: http://varunsridharan.in Created: 2016-03-04 Last Update:
 * 2017-03-23 Required Plugins: [ Name : Aelia Currency Switcher for WooCommerce | URL :
 * http://aelia.co/shop/currency-switcher-woocommerce/ | Version : 3.8.2 | Slug :
 * woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php] Category:Integration,Currency Switcher
 */
if( ! defined('WC_RBP_PLUGIN') ) {
    die;
}

if( ! class_exists('WC_Aelia_CurrencySwitcher') ) {
    return;
}

require_once( __DIR__ . '/functions.php' );

class Aelia_Currency_Switcher_Integration_WC_RBP {
    public function __construct() {
        $this->ss_id      = 'aeliacurrencyswitcher_integration';
        $this->field_slug = 'wc_rbp_acs';
        add_filter('wc_rbp_addon_sections', array( $this, 'add_settings_section' ));
        add_filter('wc_rbp_addon_fields', array( $this, 'add_fields' ));
        add_action('wc_rbp_init', array( $this, 'render' ));
        add_action('wc_rbp_addon_styles', array( $this, 'add_style' ));
        add_action('wc_rbp_product_save_default', array( $this, 'save_product_price' ));
        add_action('wc_rbp_product_class_attribute', array( $this, 'add_acs_price_data_simple' ));
        add_action('wc_rbp_product_price_value', array( $this, 'change_wc_rbp_price' ), 50, 6);
        add_filter('wc_rbp_product_price', array( $this, 'change_wc_rbp_product_price' ), 10, 20);
        if( wc_rbp_is_request('frontend') ) {
            add_filter('wc_aelia_currencyswitcher_product_currency_prices', array( $this, 'my_custom_prices' ), 15, 3);
        }
    }

    public function add_acs_price_data_simple(&$product) {
        $product->wc_rbp_acs = wc_rbp_get_acs_product_price($product->ID);
    }

    public function add_style() {
        echo '.acs_popup_section .wc_rbp_acs_tabs_container { padding-left: 5px; padding-right: 5px !important; }';
    }

    public function save_product_price($posted_values) {
        if( ! isset($posted_values[$this->field_slug]) ) {
            return;
        }
        $product_id        = $posted_values['product_id'];
        $wc_rbp_acs_values = $posted_values[$this->field_slug];
        wc_rbp_update_acs_role_based_price($product_id, $wc_rbp_acs_values);
    }

    public function render() {
        $this->base_currency = get_option('woocommerce_currency');
        $allowed_roles       = wc_rbp_allowed_roles();
        foreach( $allowed_roles as $role ) {
            add_action('wc_rbp_price_edit_tab_' . $role . '_after', array( $this, 'render_fields' ), 10, 4);
        }
    }

    public function render_fields($post_id, $prodType, $prod, $tab_id) {
        $product_id       = $post_id;
        $allowed_currency = wc_rbp_option('acs_allowed_currencies');
        $allowed_price    = wc_rbp_allowed_price();
        $ex_price         = wc_rbp_price_types();
        if( empty($allowed_currency) ) {
            $allowed_currency = $this->get_enabled_currencies();
        }
        include( __DIR__ . '/wc_rbp_fields.php' );
    }

    private function get_enabled_currencies() {
        return apply_filters('wc_aelia_cs_enabled_currencies', array( get_option('woocommerce_currency') ));
    }

    public function add_settings_section($section) {
        $section[] = array(
            'id'    => 'aeliacurrencyswitcher_integration',
            'title' => __('Aelia Currency Switcher', WC_RBP_TXT),
            'desc'  => '',
        );
        return $section;
    }

    public function add_fields($fields) {
        $fields[$this->ss_id][] = array(
            'id'       => WC_RBP_DB . 'acs_allowed_currencies',
            'multiple' => 'true',
            'type'     => 'select',
            'label'    => __('Allowed Currency', WC_RBP_TXT),
            'desc'     => __('Allowed Currency To Show In Role Based Price Listing', WC_RBP_TXT),
            'options'  => $this->get_currency_key_val(),
            'attr'     => array(
                'class'    => 'wc-rbp-enhanced-select',
                'multiple' => 'multiple',
            ),
        );
        return $fields;
    }

    private function get_currency_key_val() {
        $wc_currencies    = get_woocommerce_currencies();
        $enabled_currency = $this->get_enabled_currencies();
        $return_currency  = array();
        foreach( $enabled_currency as $curr ) {
            if( isset($wc_currencies[$curr]) ) {
                $symbol                 = get_woocommerce_currency_symbol($curr);
                $symbol                 = ! empty($symbol) ? ' [ ' . $symbol . ' ] ' : '';
                $return_currency[$curr] = $wc_currencies[$curr] . $symbol;
            }
        }
        return $return_currency;
    }

    public function change_wc_rbp_price($return, $price, $product_id, $product, $price_meta_key, $current_user) {
        $status = product_rbp_status($product_id, $product);
        if( ! $status ) {
            return $return;
        }

        if( empty($current_user) ) {
            $current_user = wc_rbp_get_current_user();
        }
        return $this->get_acs_role_price($return, $product_id, $current_user, $price_meta_key);
    }


    public function get_acs_role_price($return, $product_id, $userrole, $price_meta_key = 'regular_price', $from_currency = NULL, $to_currency = NULL) {


        $allowed_price = wc_rbp_allowed_price();
        $allowed_roles = wc_rbp_allowed_roles();

        if( in_array($userrole, $allowed_roles) ) {

            if( in_array($price_meta_key, $allowed_price) ) {
                $send_currency    = array();
                $allowed_currency = wc_rbp_option('acs_allowed_currencies');

                if( empty($allowed_currency) ) {
                    $allowed_currency = $this->get_enabled_currencies();
                }
                if( empty($from_currency) ) {
                    $from_currency = get_option('woocommerce_currency');
                }
                if( empty($to_currency) ) {
                    $to_currency = get_woocommerce_currency();
                }

                $wc_rbp_status = product_rbp_status($product_id, NULL);

                if( ! $wc_rbp_status ) {
                    return $return;
                }


                if( $this->base_currency == $to_currency ) {
                    $price = wc_rbp_price($product_id, $userrole, $price_meta_key);
                    if( ! empty($price) ) {
                        return $price;
                    }
                } else {
                    $rbp_price    = wc_rbp_acs_price($product_id, $userrole, $to_currency, 'currency');
                    $price        = '';
                    $opposite_key = 'selling_price';
                    if( $price_meta_key == 'selling_price' ) {
                        $opposite_key = 'regular_price';
                    }


                    if( isset($rbp_price[$price_meta_key]) && isset($rbp_price[$opposite_key]) ) {
                        if( $rbp_price[$price_meta_key] == "" && $rbp_price[$opposite_key] == "" ) {
                            $price = $return;
                        } else if( $rbp_price[$price_meta_key] == "" && $rbp_price[$opposite_key] != "" ) {
                            $price = $rbp_price[$opposite_key];
                        } else if( $rbp_price[$price_meta_key] != "" && $rbp_price[$opposite_key] == "" ) {
                            $price = $rbp_price[$price_meta_key];
                        } else if( $rbp_price[$price_meta_key] != "" ) {
                            $price = $rbp_price[$price_meta_key];
                        }
                    } else if( isset($rbp_price[$price_meta_key]) && ! isset($rbp_price[$opposite_key]) ) {
                        if( $rbp_price[$price_meta_key] == "" ) {
                            $price = $base_price;
                        } else if( $rbp_price[$price_meta_key] != "" ) {
                            $price = $rbp_price[$price_meta_key];
                        }
                    } else if( isset($rbp_price[$opposite_key]) && ! isset($rbp_price[$price_meta_key]) ) {
                        if( $rbp_price[$opposite_key] == "" ) {
                            $price = $base_price;
                        } else if( $rbp_price[$opposite_key] != "" ) {
                            $price = $rbp_price[$opposite_key];
                        }
                    }

                    return $price;
                }
            } else {
                $price_meta_key = wc_rbp_get_oppo_metakey($price_meta_key);
                $price          = $this->get_acs_role_price($return, $product_id, $userrole, $price_meta_key, $from_currency, $to_currency);
                if( ! empty($price) ) {
                    return $price;
                }
            }
        } else {
            $this->hook_function_disableenable(TRUE);


            $product = new WC_Product($product_id);
            if( $price_meta_key == 'regular_price' ) {
                $return = $product->get_regular_price();
            }
            if( $price_meta_key == 'selling_price' ) {
                $return = $product->get_sale_price();
            }

            $this->hook_function_disableenable(FALSE);
        }

        return apply_filters('wc_aelia_cs_convert', $return, $from_currency, $to_currency);
    }

    public function hook_function_disableenable($disable = FALSE) {
        if( $disable ) {
            add_filter('role_based_price_status', array( $this, 'disable_rbp_price' ));
        } else {
            remove_filter('role_based_price_status', array( $this, 'disable_rbp_price' ));
        }
    }

    public function disable_rbp_price() {
        return FALSE;
    }

    public function change_wc_rbp_product_price($return, $role, $price, $post_id, $args) {
        if( ! isset($args['currency']) ) {
            return $return;
        }
        $allowed_price = wc_rbp_allowed_price();
        if( $role == 'all' && $price == 'all' ) {
            $allowed_roles = wc_rbp_allowed_roles();
            foreach( $allowed_roles as $aroles ) {
                foreach( $allowed_price as $aprice ) {
                    $return[$aroles][$aprice] = $this->get_acs_role_price($return[$aroles][$aprice], $post_id, $aroles, $aprice, NULL, $args['currency']);
                }
            }
        } else if( $role != 'all' && $price == 'all' ) {
            foreach( $allowed_price as $aprice ) {
                $return[$aprice] = $this->get_acs_role_price($return[$aprice], $post_id, $role, $aprice, NULL, $args['currency']);
            }
        } else {
            if( in_array($type, $allowed_price) ) {
                $return = $this->get_acs_role_price($return, $post_id, $role, $price, NULL, $args['currency']);
            } else {
                $price  = wc_rbp_get_oppo_metakey($price);
                $return = $this->get_acs_role_price($return, $post_id, $role, $price, NULL, $args['currency']);
            }

        }
        return $return;
    }

    /**
     * Replaces the product prices with custom ones.
     *
     * @param array product_prices An array of product prices.
     * @param int product_id The product ID.
     * @param int price_type The price types to be replaced (e.g. regular prices, sale prices, etc).
     *
     * @return array An array of currency => price entries
     * @author Aelia <support@aelia.co>
     * @link   http://aelia.co/about
     */
    public function my_custom_prices($product_prices, $product_id, $price_type) {
        /*
        $price_type can have one of the following values:
        - '_regular_currency_prices' -> Product's regular prices
        - '_sale_currency_prices' -> Product's sale prices
        - 'variable_regular_currency_prices' -> Variation's regular prices
        - 'variable_sale_currency_prices' -> Variation's simple prices
        Using $price_type, load the appropriate prices (regular or sale), and store
        them in an array, using the currency as the key. If you don't have one of the
        prices (e.g. GBP), don't add it to the array. Example:
        */

        $status = product_rbp_status($product_id, NULL);
        if( ! $status ) {
            return $product_prices;
        }
        $type             = "selling_price";
        $price            = '';
        $allowed_currency = wc_rbp_option('acs_allowed_currencies');
        $allowed_price    = wc_rbp_allowed_price();
        $current_user     = wc_rbp_get_current_user();
        $send_currency    = array();
        if( empty($allowed_currency) ) {
            $allowed_currency = $this->get_enabled_currencies();
        }
        if( $price_type == 'variable_regular_currency_prices' || $price_type == '_regular_currency_prices' ) {
            $type = "regular_price";
        }


        $allowed_roles = wc_rbp_allowed_roles();

        if( in_array($current_user, $allowed_roles) ) {
            if( ! in_array($type, $allowed_price) ) {
                $send_currency = array();
            } else {
                foreach( $allowed_currency as $currency ) {
                    if( $this->base_currency == $currency ) {
                        $price = wc_rbp_price($product_id, $current_user, $type);
                        if( $price !== FALSE ) {
                            if( ! empty($price) )
                                $send_currency[$currency] = $price;
                        }
                    } else {
                        $price                    = wc_rbp_acs_price($product_id, $current_user, $currency, $type);
                        $send_currency[$currency] = $price;
                    }

                }
            }
        }

        $product_prices = array_merge($product_prices, $send_currency);
        return $product_prices;
    }
}

return new Aelia_Currency_Switcher_Integration_WC_RBP;