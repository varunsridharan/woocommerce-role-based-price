<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

class WooCommerce_Role_Based_Price_Product_Pricing {
    public $already_given_prices = NULL;

    public function __construct($is_hook = TRUE) {
        $this->already_given_prices = array();
        if( $is_hook == TRUE ) {
            add_action('woocommerce_init', array( $this, 'wc_init' ));
        }
    }

    public function wc_init() {
        if( wc_rbp_is_wc_v('>=', '3.0.1') ) {
            add_filter('woocommerce_product_get_regular_price', array( &$this, 'get_regular_price' ), 99, 2);
            add_filter('woocommerce_product_get_sale_price', array( &$this, 'get_selling_price' ), 99, 2);
            add_filter('woocommerce_product_get_price', array( &$this, 'get_price' ), 99, 2);
            add_filter('woocommerce_product_variation_get_regular_price', array( &$this, 'get_regular_price' ), 99, 2);
            add_filter('woocommerce_product_variation_get_sale_price', array( &$this, 'get_selling_price' ), 99, 2);
            add_filter('woocommerce_product_variation_get_price', array( &$this, 'get_price' ), 99, 2);
        } else {
            add_filter('woocommerce_get_regular_price', array( &$this, 'get_regular_price' ), 99, 2);
            add_filter('woocommerce_get_sale_price', array( &$this, 'get_selling_price' ), 99, 2);
            add_filter('woocommerce_get_price', array( &$this, 'get_price' ), 99, 2);
        }

        add_filter('woocommerce_get_variation_regular_price', array( &$this, 'get_variation_regular_price' ), 99, 4);
        add_filter('woocommerce_get_variation_price', array( &$this, 'get_variation_price' ), 99, 4);
        add_filter('woocommerce_get_price_html', array( &$this, 'get_price_html' ), 99, 2);
    }

    /**
     * @param $price
     * @param $product
     *
     * @return mixed|string
     */
    public function get_selling_price($price, $product) {
        $price = $this->get_product_price($price, $product, 'selling_price');
        $price = apply_filters("wc_rbp_product_selling_price", $price, $product, $this);
        return $price;
    }

    public function get_product_price($base_price, $product, $price_meta_key = 'regular_price', $current_user = '') {
        if( ! apply_filters('role_based_price_status', TRUE) ) {
            return $base_price;
        }
        $wc_rbp_price = FALSE;

        $opposite_key = 'selling_price';
        if( $price_meta_key == 'selling_price' ) {
            $opposite_key = 'regular_price';
        }
        $product_id = $this->check_product_get_id($product);

        if( empty($current_user) ) {
            $current_user = wc_rbp_get_current_user();
        }


        $product_type = wp_get_post_terms($product_id, 'product_type', array( 'fields' => 'names' ));
        if( in_array('variable', $product_type) ) {
            return $base_price;
        }
        $key = md5($base_price . '-' . $product_id . '-' . $price_meta_key . '-' . $opposite_key . '-' . $current_user);


        if( isset($this->already_given_prices[$key]) ) {
            $wc_rbp_price = $this->already_given_prices[$key];
        } else {
            $wc_rbp_status = product_rbp_status($product_id);
            $allowed_roles = wc_rbp_allowed_roles();

            if( in_array($current_user, $allowed_roles) ) {
                $rbp_price = wc_rbp_price($product_id, $current_user, 'all', array());

                if( $wc_rbp_status ) {
                    if( $rbp_price === FALSE ) {
                        $wc_rbp_price = $base_price;
                    } else {
                        if( $price_meta_key == 'all' ) {
                            $wc_rbp_price = $rbp_price[$price_meta_key];
                        }

                        if( isset($rbp_price[$price_meta_key]) && isset($rbp_price[$opposite_key]) ) {
                            if( $rbp_price[$price_meta_key] === "" && $rbp_price[$opposite_key] === "" ) {
                                $wc_rbp_price = $base_price;
                            }
                            if( ( $rbp_price[$price_meta_key] === 0 || $rbp_price[$price_meta_key] === '0' ) || ( $rbp_price[$opposite_key] === 0 || $rbp_price[$opposite_key] === '0' ) ) {
                                $wc_rbp_price = 0;
                            } else if( $rbp_price[$price_meta_key] === "" && $rbp_price[$opposite_key] !== "" ) {
                                $wc_rbp_price = $rbp_price[$opposite_key];
                            } else if( $rbp_price[$price_meta_key] !== "" && $rbp_price[$opposite_key] === "" ) {
                                $wc_rbp_price = $rbp_price[$price_meta_key];
                            } else if( $rbp_price[$price_meta_key] !== "" ) {
                                $wc_rbp_price = $rbp_price[$price_meta_key];
                            }
                        } else if( isset($rbp_price[$price_meta_key]) && ! isset($rbp_price[$opposite_key]) ) {
                            if( $rbp_price[$price_meta_key] === "" ) {
                                $wc_rbp_price = $base_price;
                            } else if( $rbp_price[$price_meta_key] === "0" || $rbp_price[$price_meta_key] === 0 ) {
                                $wc_rbp_price = 0;
                            } else if( $rbp_price[$price_meta_key] !== "" ) {
                                $wc_rbp_price = $rbp_price[$price_meta_key];
                            }
                        } else if( isset($rbp_price[$opposite_key]) && ! isset($rbp_price[$price_meta_key]) ) {
                            if( $rbp_price[$opposite_key] === "" ) {
                                $wc_rbp_price = $base_price;
                            }
                            if( $rbp_price[$opposite_key] === "0" || $rbp_price[$opposite_key] === 0 ) {
                                $wc_rbp_price = 0;
                            } else if( $rbp_price[$opposite_key] !== "" ) {
                                $wc_rbp_price = $rbp_price[$opposite_key];
                            }
                        }
                    }

                } else {
                    $wc_rbp_price = $base_price;
                }
            } else {
                $wc_rbp_price = $base_price;
            }
        }

        //$return = apply_filters('wc_rbp_product_price_value',$return,$price,$product_id,$product,$price_meta_key,$current_user);
        $wc_rbp_price = apply_filters('wc_rbp_product_price_value', $wc_rbp_price, $base_price, $product_id, $product, $price_meta_key, $current_user);

        if( $wc_rbp_price !== '' ) {
            $this->already_given_prices[$key] = $wc_rbp_price;
        }


        $return = wc_format_decimal($wc_rbp_price);

        $wpml_integration_status = wc_rbp_option('enable_wpml_integration');

        if( $wpml_integration_status == 'on' ) {
            if( class_exists('woocommerce_wpml') ) {
                $return = apply_filters('wcml_raw_price_amount', $return);
            }
        }

        return $return;
    }

    public function check_product_get_id($product) {
        $product_id = 0;

        if( wc_rbp_is_wc_v('>=', '3.0.1') ) {

            if( is_numeric($product) ) {
                return $product;
            } else if( $this->is_simple_product($product) ) {
                $product_id = $product->get_id();
            } else if( $this->is_variable_product($product) ) {
                $product_id = $product->get_id();
            } else if( $this->is_variation_product($product) ) {
                $product_id = $product->get_id();
            }

        } else {

            if( is_numeric($product) ) {
                return $product;
            } else if( $this->is_simple_product($product) ) {
                $product_id = $product->id;
            } else if( $this->is_variable_product($product) ) {
                $product_id = $product->id;
            } else if( $this->is_variation_product($product) ) {
                $product_id = $product->variation_id;
            }
        }


        return $product_id;
    }

    private function is_simple_product($product) {
        $class   = $this->get_product_class($product);
        $classes = apply_filters("wc_rbp_simple_product_class", array( 'WC_Product_Simple', 'WC_Product_Yith_Bundle' ));
        if( in_array($class, $classes) ) {
            return TRUE;
        }
        return FALSE;
    }

    private function get_product_class($product) {
        $class = get_class($product);
        $class = str_replace('_RBP', '', $class);
        return $class;
    }

    private function is_variable_product($product) {
        $class = $this->get_product_class($product);
        if( $class == 'WC_Product_Variable' ) {
            return TRUE;
        }
        return FALSE;
    }

    private function is_variation_product($product) {
        $class = $this->get_product_class($product);
        if( $class == 'WC_Product_Variation' ) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Returns the product's active price.
     *
     * @param string $price
     * @param string $product
     *
     * @return string price
     */
    public function get_price($price = '', $product = '') {
        if( empty($product) ) {
            return $price;
        }
        $sale_price  = $product->get_sale_price();
        $wcrbp_price = ( $sale_price !== '' && $sale_price > 0 ) ? $sale_price : $this->get_regular_price($price, $product);
        $wcrbp_price = wc_format_decimal($wcrbp_price);
        $wcrbp_price = apply_filters("wc_rbp_product_get_price", $wcrbp_price, $product, $this);
        return $wcrbp_price;
    }

    /**
     * Returns the product's regular price
     *
     * @param $price
     * @param $product
     *
     * @return string price
     */
    public function get_regular_price($price, $product) {
        $price = $this->get_product_price($price, $product);
        $price = apply_filters("wc_rbp_product_regular_price", $price, $product, $this);
        return $price;
    }

    /**
     * Get the min or max variation active price.
     *
     * @param          $price
     * @param          $product
     * @param  string  $min_or_max - min or max
     * @param  boolean $display    Whether the value is going to be displayed
     *
     * @return string price
     */
    public function get_variation_price($price, $product, $min_or_max, $display) {
        return $this->get_variation_regular_price($price, $product, $min_or_max, $display, 'selling_price');
    }

    /**
     * Get the min or max variation regular price.
     *
     * @param  string  $min_or_max - min or max
     * @param  boolean $display    Whether the value is going to be displayed
     *
     * @return string price
     */
    public function get_variation_regular_price($price, $product, $min_or_max, $display, $price_meta_key = 'regular_price') {
        $return  = $price;
        $display = array();
        $pid     = 0;

        if( wc_rbp_is_wc_v('>=', '3.0') ) {
            $pid = $product->get_id();
        } else {
            $pid = $product->id;
        }

        $role    = wc_rbp_get_current_user();
        $opp_key = wc_rbp_get_oppo_metakey($price_meta_key);
        $prices  = wc_rbp_get_variation_data($pid, $role);


        foreach( $prices as $id => $arr ) {
            if( ! is_array($arr) ) {
                continue;
            }
        }

        if( empty($prices[$price_meta_key]) && empty($prices[$opp_key]) ) {
            if( empty($prices['base_' . $price_meta_key]) && ! empty($prices['base_' . $opp_key]) ) {
                $prices = $prices['base_' . $opp_key];
            } else {
                $prices = $prices['base_' . $price_meta_key];
            }
        } else if( empty($prices[$price_meta_key]) && ! empty($prices[$opp_key]) ) {
            $prices = $prices[$opp_key];
        } else {
            $prices = $prices[$price_meta_key];
        }


        if( $min_or_max == 'min' ) {
            asort($prices);
        } else {
            arsort($prices);
        }

        if( $display ) {
            $variation_id = key($prices);
            $return       = $display[$variation_id];
        } else {
            $return = current($prices);
        }

        return $return;
    }

    public function get_price_html($price = '', $product) {
        if( 'WC_Product_Variable' == get_class($product) ) {
            $product_id    = $this->check_product_get_id($product);
            $wc_rbp_status = $this->product_rbp_status($product_id, $product);
            if( ! $wc_rbp_status ) {
                return $price;
            }

            if( wc_rbp_is_wc_v('>=', '3.0') ) {
                return $this->get_price_html_wc3($price, $product);
            } else {
                return $this->get_price_html_below_wc3($price, $product);
            }
        }

        return $price;
    }

    public function product_rbp_status($id, $product) {
        $type = $product->get_type();
        if( $type == 'variable' ) {
            $variations = $product->get_children();

            if( is_array($variations) ) {
                foreach( $variations as $i ) {
                    $status = product_rbp_status($i);
                    if( $status ) {
                        return TRUE;
                    }
                }
            }
        } else {
            $status = product_rbp_status($id);
            if( $status ) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function get_price_html_wc3($price = '', $product) {

        $prices = $product->get_variation_prices(TRUE);

        if( empty($prices['price']) ) {
            return apply_filters('woocommerce_variable_empty_price_html', '', $this);
        }

        $prices                  = array();
        $prices['min_price']     = $product->get_variation_price('min', FALSE);
        $prices['max_price']     = $product->get_variation_price('max', FALSE);
        $prices['min_reg_price'] = $product->get_variation_regular_price('min', TRUE);
        $prices['max_reg_price'] = $product->get_variation_regular_price('max', TRUE);
        $prices                  = $this->get_variation_tax_status_price($prices, $product);

        /*if( $prices['min_price'] !== $prices['max_price'] ) {
            $price = apply_filters('woocommerce_variable_price_html', wc_format_price_range($prices['min_price'], $prices['max_price']) . $product->get_price_suffix(), $product);
        } else if( $product->is_on_sale() && $prices['min_reg_price'] === $prices['max_reg_price'] ) {
            $price = apply_filters('woocommerce_variable_price_html', wc_format_sale_price(wc_price($prices['max_reg_price']), wc_price($prices['min_price'])) . $product->get_price_suffix(), $product);
        } else {
            $price = apply_filters('woocommerce_variable_price_html', wc_price($prices['min_price']) . $product->get_price_suffix(), $product);
        }*/

        if( $prices['min_price'] !== $prices['max_price'] ) {
            $price = wc_format_price_range($prices['min_price'], $prices['max_price']);
        } else if( $product->is_on_sale() && $prices['min_reg_price'] === $prices['max_reg_price'] ) {
            $price = wc_format_sale_price(wc_price($prices['max_reg_price']), wc_price($prices['min_price']));
        } else {
            $price = wc_price($prices['min_price']);
        }

        return $price;

    }

    public function get_variation_tax_status_price($prices, $product) {
        if( 'incl' === get_option('woocommerce_tax_display_shop') ) {
            if( wc_rbp_is_wc_v('>=', '3.0') ) {
                $prices['min_price'] = '' === $prices['min_price'] ? '' : wc_get_price_including_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['min_price'],
                ));
                $prices['max_price'] = '' === $prices['max_price'] ? '' : wc_get_price_including_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['max_price'],
                ));

                $prices['min_reg_price'] = '' === $prices['min_reg_price'] ? '' : wc_get_price_including_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['min_reg_price'],
                ));
                $prices['max_reg_price'] = '' === $prices['max_reg_price'] ? '' : wc_get_price_including_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['max_reg_price'],
                ));

            } else {
                $prices[0] = '' === $prices[0] ? '' : $product->get_price_including_tax(1, $prices[0]);
                $prices[1] = '' === $prices[1] ? '' : $product->get_price_including_tax(1, $prices[1]);
            }

        } else {
            if( wc_rbp_is_wc_v('>=', '3.0') ) {

                $prices['min_price'] = '' === $prices['min_price'] ? '' : wc_get_price_excluding_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['min_price'],
                ));
                $prices['max_price'] = '' === $prices['max_price'] ? '' : wc_get_price_excluding_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['max_price'],
                ));

                $prices['min_reg_price'] = '' === $prices['min_reg_price'] ? '' : wc_get_price_excluding_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['min_reg_price'],
                ));
                $prices['max_reg_price'] = '' === $prices['max_reg_price'] ? '' : wc_get_price_excluding_tax($product, array(
                    'qty'   => 1,
                    'price' => $prices['max_reg_price'],
                ));

            } else {
                $prices[0] = '' === $prices[0] ? '' : $product->get_price_including_tax(1, $prices[0]);
                $prices[1] = '' === $prices[1] ? '' : $product->get_price_including_tax(1, $prices[1]);
            }
        }

        return $prices;
    }

    /**
     * Returns the price in html format.
     *
     * @access public
     *
     * @param string $price (default: '')
     *
     * @return string
     */
    public function get_price_html_below_wc3($price = '', $product) {

        // Ensure variation prices are synced with variations
        if( $product->get_variation_regular_price('min') === FALSE || $product->get_variation_price('min') === FALSE || $product->get_variation_price('min') === '' || $product->get_price() === '' ) {
            $product->variable_product_sync($product->get_id());
        }
        // Get the price
        if( $product->get_price() === '' ) {
            $price = apply_filters('woocommerce_variable_empty_price_html', '', $product);
        } else {

            // Main price
            $prices = array( $product->get_variation_price('min', FALSE), $product->get_variation_price('max', FALSE) );

            $prices = $this->get_variation_tax_status_price($prices, $product);

            $price = $prices[0] !== $prices[1] ? sprintf(_x(' % 1$s & ndash;%2$s', 'Price range: from - to', 'woocommerce'), wc_price($prices[0]), wc_price($prices[1])) : wc_price($prices[0]);
            // Sale
            $prices = array(
                $product->get_variation_regular_price('min', TRUE),
                $product->get_variation_regular_price('max', TRUE),
            );
            sort($prices);

            $prices = $this->get_variation_tax_status_price($prices, $product);

            $saleprice = $prices[0] !== $prices[1] ? sprintf(_x(' % 1$s & ndash;%2$s', 'Price range: from - to', 'woocommerce'), wc_price($prices[0]), wc_price($prices[1])) : wc_price($prices[0]);


            if( $prices[0] == 0 && $prices[1] == 0 ) {
                $price = __('Free! ', 'woocommerce');
                $price = apply_filters('woocommerce_variable_free_price_html', $price, $product);
            } else if( $price !== $saleprice ) {
                if( wc_rbp_is_wc_v(' >= ', '3.0') ) {
                    $price = apply_filters('woocommerce_variable_sale_price_html', wc_format_price_range($saleprice, $price) . $product->get_price_suffix(), $product);
                } else {
                    $price = apply_filters('woocommerce_variable_sale_price_html', $product->get_price_html_from_to($saleprice, $price) . $product->get_price_suffix(), $product);
                }
            } else {
                $price = apply_filters('woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product);
            }
        }

        return $price;
    }
}