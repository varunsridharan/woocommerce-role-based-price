<?php
/**
 * Common Plugin Functions
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since      3.0
 */
if( ! defined('WPINC') ) {
    die;
}

global $wc_rbp_db_settins_values, $wc_rbp_db_array;
$wc_rbp_db_settins_values = array();
$wc_rbp_db_array          = array();
add_action('wc_rbp_loaded', 'wc_rbp_get_settings_from_db');

if( ! function_exists('wc_rbp_option') ) {
    function wc_rbp_option($key = '', $return_failure = '') {
        global $wc_rbp_db_settins_values;
        if( $key == '' ) {
            return $wc_rbp_db_settins_values;
        }

        if( isset($wc_rbp_db_settins_values[WC_RBP_DB . $key]) ) {
            return $wc_rbp_db_settins_values[WC_RBP_DB . $key];
        }

        return $return_failure;
    }

}

if( ! function_exists('wc_rbp_get_settings_from_db') ) {
    /**
     * Retrives All Plugin Options From DB
     */
    function wc_rbp_get_settings_from_db() {
        global $wc_rbp_db_settins_values;
        $section = array();
        $section = apply_filters('wc_rbp_settings_section', $section);
        $values  = array();

        foreach( $section as $settings ) {
            foreach( $settings as $set ) {
                $db_val = get_option(WC_RBP_DB . $set['id']);
                if( is_array($db_val) ) {
                    unset($db_val['section_id']);
                    $values = array_merge($db_val, $values);
                }
            }
        }
        $wc_rbp_db_settins_values = $values;
    }
}

if( ! function_exists('wc_rbp_is_request') ) {
    /**
     * What type of request is this?
     * string $type ajax, frontend or admin
     *
     * @return bool
     */
    function wc_rbp_is_request($type) {
        switch( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return ( ! is_admin() || defined('DOING_AJAX') ) && ! defined('DOING_CRON');
        }
    }
}

if( ! function_exists('wc_rbp_current_screen') ) {
    /**
     * Gets Current Screen ID from wordpress
     *
     * @return string [Current Screen ID]
     */
    function wc_rbp_current_screen() {
        $screen = get_current_screen();
        return $screen->id;
    }
}

if( ! function_exists('wc_rbp_get_screen_ids') ) {
    /**
     * Returns Predefined Screen IDS
     *
     * @return [Array]
     */
    function wc_rbp_get_screen_ids() {
        $screen_ids   = array();
        $screen_ids[] = 'woocommerce_page_woocommerce-role-based-price-settings';
        return $screen_ids;
    }
}

if( ! function_exists('wc_rbp_dependency_message') ) {
    /**
     * Returns the plugin dependency message
     */
    function wc_rbp_dependency_message() {
        $text = __(WC_RBP_NAME . ' requires <b> WooCommerce </b> To Be Installed..  <br/> <i>Plugin Deactivated</i> ', WC_RBP_TXT);
        return $text;
    }
}

if( ! function_exists('wc_rbp_custom_wp_user_roles') ) {
    function wc_rbp_custom_wp_user_roles() {
        $all_roles = array();
        if( function_exists('wp_roles') ) {
            $all_roles = wp_roles()->roles;
        }
        return $all_roles;
    }
}

if( ! function_exists('wc_rbp_get_wp_roles') ) {
    /**
     * Returns Registered WP User Roles
     *
     * @return [[Type]] [[Description]]
     */
    function wc_rbp_get_wp_roles() {
        $user_roles             = wc_rbp_custom_wp_user_roles();
        $user_roles['logedout'] = array( 'name' => __('Visitor / LogedOut User', WC_RBP_TXT) );
        $user_roles             = apply_filters('wc_rbp_wp_user_roles', $user_roles);
        return $user_roles;
    }

}

if( ! function_exists('wc_rbp_get_template') ) {
    function wc_rbp_get_template($name, $args = array(), $main_path = '', $theme_path = 'woocommerce') {
        if( empty($main_path) ) {
            $main_path = WC_RBP_PATH . '/templates/';
        }
        ob_start();
        wc_get_template($name, $args, $theme_path, $main_path);
        $return_value = ob_get_clean();
        ob_flush();

        return $return_value;
    }
}

if( ! function_exists('wc_rbp_get_user_roles_selectbox') ) {
    function wc_rbp_get_user_roles_selectbox() {
        $user_roles = wc_rbp_get_wp_roles();
        $list_roles = array();
        $roles      = array_keys($user_roles);
        foreach( $roles as $role ) {
            $list_roles[$role] = $user_roles[$role]['name'];
        }
        return $list_roles;
    }

}

if( ! function_exists('wc_rbp_get_current_user') ) {
    /**
     * Gets Current Logged in User Role / User Object.
     * returns user role if $userroleonly set to true
     * or returns the user object
     *
     * @param  [boolean] [$userroleonly = true / false]
     *
     * @return [string / object]
     */
    function wc_rbp_get_current_user($userroleonly = TRUE) {
        global $current_user;
        $user_role = $current_user;
        if( $userroleonly ) {
            $user_roles = $current_user->roles;
            $user_role  = array_shift($user_roles);
            if( $user_role == NULL ) {
                $user_role = 'logedout';
            }
        }

        return apply_filters('wc_rbp_active_user', $user_role, $userroleonly);
    }

}

if( ! function_exists('wc_rbp_get_oppo_metakey') ) {
    function wc_rbp_get_oppo_metakey($key) {
        if( $key == 'selling_price' ) {
            return 'regular_price';
        }
        return 'selling_price';
    }
}

if( ! function_exists('wc_rbp_get_userrole_by_id') ) {

    /**
     * Get user roles by user ID.
     *
     * @param  int $id
     *
     * @return array
     */
    function wc_rbp_get_userrole_by_id($id) {
        $user = new WP_User($id);
        if( empty ($user->roles) or ! is_array($user->roles) ) {
            return '';
        }
        foreach( $user->roles as $role ) {
            return $role;
        }
        return NULL;
    }
}

if( ! function_exists('wc_rbp_avaiable_price_type') ) {
    /**
     * Returns avaiable_price type with label
     *
     * @return [[Type]] [[Description]]
     */
    function wc_rbp_avaiable_price_type($key = '') {
        $avaiable_price                  = array();
        $avaiable_price['regular_price'] = __('Regular Price', WC_RBP_TXT);
        $avaiable_price['selling_price'] = __('Selling Price', WC_RBP_TXT);
        $avaiable_price                  = apply_filters('wc_rbp_avaiable_price', $avaiable_price);

        if( ! empty($key) ) {
            if( isset($avaiable_price[$key]) ) {
                return $avaiable_price[$key];
            }
        }

        return $avaiable_price;
    }
}

if( ! function_exists('wc_rbp_price_types') ) {
    function wc_rbp_price_types($key = '') {
        $price = wc_rbp_avaiable_price_type();
        foreach( $price as $price_id => $priceVal ) {
            $lable            = wc_rbp_option($price_id . '_label', $priceVal);
            $price[$price_id] = $lable;
        }

        if( ! empty($key) ) {
            if( isset($price[$key]) ) {
                return $price[$key];
            }
        }

        return $price;
    }
}

if( ! function_exists('wc_rbp_do_settings_sections') ) {
    /**
     * Prints out all settings sections added to a particular settings page
     *
     * Part of the Settings API. Use this in a settings page callback function
     * to output all the sections and fields that were added to that $page with
     * add_settings_section() and add_settings_field()
     *
     * @global       $wp_settings_sections Storage array of all settings sections added to admin pages
     * @global       $wp_settings_fields   Storage array of settings fields and info about their pages/sections
     * @since 2.7.0
     *
     * @param string $page                 The slug name of the page whose settings sections you want to output
     */
    function wc_rbp_do_settings_sections($page) {
        global $wp_settings_sections, $wp_settings_fields;

        if( ! isset($wp_settings_sections[$page]) )
            return;
        $section_count = count($wp_settings_sections[$page]);
        if( $section_count > 1 ) {
            echo '<ul class="subsubsub wc_rbp_settings_submenu">';
            foreach( (array) $wp_settings_sections[$page] as $section ) {
                echo '<li> <a href="#' . $section['id'] . '">' . $section['title'] . '</a> | </li>';
            }
            echo '</ul> <br/>';
        }

        foreach( (array) $wp_settings_sections[$page] as $section ) {
            if( $section_count > 1 ) {
                echo '<div id="settings_' . $section['id'] . '" class="hidden wc_rbp_settings_content">';
            }
            if( $section['title'] )
                echo "<h2>{$section['title']}</h2>\n";

            if( $section['callback'] )
                call_user_func($section['callback'], $section);

            if( ! isset($wp_settings_fields) || ! isset($wp_settings_fields[$page]) || ! isset($wp_settings_fields[$page][$section['id']]) )
                continue;
            echo '<table class="form-table">';
            do_settings_fields($page, $section['id']);
            echo '</table>';
            if( $section_count > 1 ) {
                echo '</div>';
            }
        }
    }
}

if( ! function_exists('wc_rbp_get_form_hidden_fields') ) {
    /**
     * Returns Required WC RBP hidden Fields
     */

    function wc_rbp_get_form_hidden_fields($action, $wp_nounce_name, $referer = TRUE) {
        $return = '<input type="hidden" name="wcrbp-action" value="' . $action . '" />';
        $return .= wp_nonce_field($action, $wp_nounce_name, $referer, FALSE);
        return $return;
    }
}

if( ! function_exists('wc_rbp_get_editor_fields') ) {
    function wc_rbp_get_editor_fields($type) {
        $fields = wc_rbp_get_form_hidden_fields('wc_rbp_save_product_prices', 'wc_rbp_nounce');
        $fields .= '<input type="hidden" name="type" value="' . $type . '" />';
        return $fields;
    }
}

if( ! function_exists('wc_rbp_get_ajax_overlay') ) {
    /**
     * Prints WC RBP Ajax Loading Code
     */
    function wc_rbp_get_ajax_overlay($echo = TRUE) {
        $return = '<div class="wc_rbp_ajax_overlay">
		<div class="sk-folding-cube">
		<div class="sk-cube1 sk-cube"></div>
		<div class="sk-cube2 sk-cube"></div>
		<div class="sk-cube4 sk-cube"></div>
		<div class="sk-cube3 sk-cube"></div>
		</div>
		</div>';
        if( $echo ) {
            echo $return;
        } else {
            return $return;
        }
    }
}

if( ! function_exists('wc_rbp_check_active_addon') ) {
    function wc_rbp_check_active_addon($slug) {
        $addons = wc_rbp_get_active_addons();
        if( in_array($slug, $addons) ) {
            return TRUE;
        }
        return FALSE;
    }
}

if( ! function_exists('wc_rbp_get_active_addons') ) {
    /**
     * Returns Active Addons List
     *
     * @return [[Type]] [[Description]]
     */
    function wc_rbp_get_active_addons() {
        $addons = get_option(WC_RBP_DB . 'active_addons', array());
        return $addons;
    }
}

if( ! function_exists('wc_rbp_update_active_addons') ) {
    /**
     * Returns Active Addons List
     *
     * @return [[Type]] [[Description]]
     */
    function wc_rbp_update_active_addons($addons) {
        update_option(WC_RBP_DB . 'active_addons', $addons);
        return TRUE;
    }
}

if( ! function_exists('wc_rbp_activate_addon') ) {
    function wc_rbp_activate_addon($slug) {
        $active_list = wc_rbp_get_active_addons();
        if( ! in_array($slug, $active_list) ) {
            $active_list[] = $slug;
            wc_rbp_update_active_addons($active_list);
            return TRUE;
        }
        return FALSE;
    }
}

if( ! function_exists('wc_rbp_deactivate_addon') ) {
    function wc_rbp_deactivate_addon($slug) {
        $active_list = wc_rbp_get_active_addons();
        if( in_array($slug, $active_list) ) {
            $key = array_search($slug, $active_list);
            unset($active_list[$key]);
            wc_rbp_update_active_addons($active_list);
            return TRUE;
        }
        return FALSE;
    }
}

if( ! function_exists('wc_rbp_admin_notice') ) {
    function wc_rbp_admin_notice($msg, $type = 'updated') {
        $notice = ' <div class="' . $type . ' settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p>' . $msg . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        return $notice;
    }
}

if( ! function_exists('wc_rbp_generate_tabs') ) {
    function wc_rbp_generate_tabs($tabs, $content, $args = array()) {
        $default_args = array(
            'show_image'  => TRUE,
            'tab_style'   => 'left', // 'default', 'box' or 'left'. Optional
            'tab_wrapper' => TRUE,

        );


        $args         = wp_parse_args($args, $default_args);
        $wraper_start = '<div class="wcrbp-tabs  wcrbp-tabs-' . $args['tab_style'] . '">';
        $wraper_end   = '</div>';

        $tabs_code = '<ul class="wcrbp-tab-nav">';
        $i         = 0;
        foreach( $tabs as $key => $tab_data ) {
            if( is_string($tab_data) ) {
                $tab_data = array( 'title' => $tab_data );
            }
            $tab_data = wp_parse_args($tab_data, array( 'icon' => '', 'title' => '', 'show_status' => TRUE ));
            if( filter_var($tab_data['icon'], FILTER_VALIDATE_URL) ) {
                $icon = '<img src="' . $tab_data['icon'] . '">';
            } else {
                if( FALSE !== strpos($tab_data['icon'], 'dashicons') ) {
                    $tab_data['icon'] .= ' dashicons';
                }
                $tab_data['icon'] = array_filter(array_map('trim', explode(' ', $tab_data['icon'])));
                $tab_data['icon'] = implode(' ', array_unique($tab_data['icon']));
                $icon             = $tab_data['icon'] ? '<i class="' . $tab_data['icon'] . '"></i>' : '';
            }

            $show_status = 'no';
            $status_tag  = '';
            if( $tab_data['show_status'] ) {
                $show_status = 'yes';
                $status_tag  = '<i class="wc-rbp-tab-status"></i>';
            }

            $class = "wcrbp-tab-$key";
            if( ! $i ) {
                $class .= ' wcrbp-tab-active';
            }
            $tabs_code .= sprintf('<li data-status="%s" class="%s" data-panel="%s"><a href="#">%s%s%s</a></li>', $show_status, $class, $key, $icon, $tab_data['title'], $status_tag);
            $i++;
        }

        $tabs_code .= '</ul>';


        $content_data = '<div class="wcrbp-tab-panels">';
        foreach( $content as $id => $data ) {
            $content_data .= '<div class="wcrbp-tab-panel wcrbp-tab-panel-' . $id . '">';
            $content_data .= $data;
            $content_data .= '</div>';
        }

        $content_data .= '</div>';

        $final = $wraper_start . $tabs_code . $content_data . $wraper_end;
        return $final;
    }
}

if( ! function_exists('wc_rbp_allowed_roles') ) {

    function wc_rbp_allowed_roles() {
        $roles = wc_rbp_option('allowed_roles');
        if( empty($roles) ) {
            $roles = array_keys(wc_rbp_get_user_roles_selectbox());
        }

        return $roles;
    }
}

if( ! function_exists('wc_rbp_allowed_price') ) {

    function wc_rbp_allowed_price() {
        $roles = wc_rbp_option('allowed_price');
        if( empty($roles) ) {
            $roles = array_keys(wc_rbp_avaiable_price_type());
        }

        return $roles;
    }
}

/** @public Below Function Are Used By The Plugin users */

if( ! function_exists('wc_rbp_update_role_based_price') ) {
    /**
     * Updates Products Role Based Price Array In DB
     *
     * @param  int   $post_id     Post ID To Update
     * @param  array $price_array Price List
     *
     * @return boolean  [[Description]]
     */
    function wc_rbp_update_role_based_price($post_id, $price_array, $force_update_parent = TRUE) {
        update_post_meta($post_id, '_role_based_price', $price_array);
        if( $force_update_parent ) {
            $parent = wp_get_post_parent_id($post_id);
            if( $parent !== FALSE ) {
                wc_rbp_update_variations_data($parent);
            }
        }
        return TRUE;
    }
}

if( ! function_exists('wc_rbp_get_variation_cache_key') ) {
    function wc_rbp_get_variation_cache_key($product_id = '', $user_role = '') {
        return '_wcrbp_p_' . $product_id . '_' . $user_role;
    }
}

if( ! function_exists('wc_rbp_delete_variation_data') ) {
    function wc_rbp_delete_variation_data($product_id = '', $user_role = '') {
        $old_cache_array = array(
            '_wcrbp_p_' . $product_id . '_' . $user_role,
            'wcrbp_p_' . $product_id . '_' . $user_role,
            wc_rbp_get_variation_cache_key($product_id, $user_role),
        );
        foreach( $old_cache_array as $key ) {
            delete_transient($key);
        }
    }
}

if( ! function_exists('wc_rbp_get_variation_data') ) {
    function wc_rbp_get_variation_data($product_id = '', $user_role = '') {
        $key    = wc_rbp_get_variation_cache_key($product_id, $user_role);
        $prices = get_transient($key);

        if( is_array($prices) && isset($prices['wc_rbp_version']) ) {
            if( WC_RBP_VARIABLE_VERSION == $prices['wc_rbp_version'] ) {
                return $prices;
            }
        }

        if( ! is_array($prices) && ! isset($prices['wc_rbp_version']) ) {
            wc_rbp_delete_variation_data($product_id, $user_role);
        }
        wc_rbp_update_variations_data($product_id, array( $user_role ));
        $prices = get_transient($key);
        return ( empty($prices) ) ? array() : $prices;
    }
}

if( ! function_exists("wc_rbp_update_variations_data") ) {
    function wc_rbp_update_variations_data($pid, $role = array(), $aprice = array()) {
        if( empty($role) || ! is_array($role) ) {
            $allowed_roles = array_keys(wc_rbp_get_user_roles_selectbox());
        } else {
            $allowed_roles = $role;
        }

        $allowed_price = array_keys(wc_rbp_avaiable_price_type());

        $product = wc_get_product($pid);
        if( ! $product ) {
            return;
        }
        $pricing = new WooCommerce_Role_Based_Price_Product_Pricing(FALSE);

        foreach( $allowed_roles as $_role ) {
            $cache_key = wc_rbp_get_variation_cache_key($pid, $_role);
            $prices    = array(
                'base_selling_price' => array(),
                'base_regular_price' => array(),
                'last_updated'       => time(),
                'wc_rbp_version'     => WC_RBP_VARIABLE_VERSION,
            );
            foreach( $allowed_price as $A_price ) {
                foreach( $product->get_children() as $vid ) {
                    $price = get_post_meta($vid, '_regular_price', TRUE);
                    if( $A_price == 'selling_price' ) {
                        $price = get_post_meta($vid, '_sale_price', TRUE);
                    }
                    $prices['base_' . $A_price][$vid] = $price;
                    $pprice                           = $pricing->get_product_price($price, $vid, $A_price, $_role);
                    if( $pprice === '' ) {
                        continue;
                    }

                    $prices[$A_price][$vid] = $pprice;
                }
            }

            set_transient($cache_key, $prices, 240 * HOUR_IN_SECONDS);
        }
    }
}

if( ! function_exists('wc_rbp_get_product_price') ) {

    /**
     * Gets Product price from DB
     * #TODO Integrate Wth product_rbp_price function to make it faster
     */
    function wc_rbp_get_product_price($post_id, $supress_filter = FALSE) {
        $price = get_post_meta($post_id, '_role_based_price');

        if( ! empty($price) ) {
            $price = $price[0];
        } else {
            $price = array();
        }
        if( ! $supress_filter )
            $price = apply_filters('wc_rbp_product_prices', $price);
        return $price;
    }

}

if( ! function_exists('wc_rbp_product_get_db') ) {
    function wc_rbp_product_get_db($post_id, $type = 'price', $function = '') {
        global $wc_rbp_db_array;

        $return_val                       = $function($post_id);
        $wc_rbp_db_array[$post_id][$type] = $return_val;
        $return_val                       = $wc_rbp_db_array[$post_id][$type];
        return $return_val;
    }
}

if( ! function_exists("wc_rbp_product_variable") ) {
    function wc_rbp_product_variable($post_id, $type = 'price', $function = '') {
        if( $type == 'price' ) {
            $function = 'wc_rbp_get_product_price';
        } else if( $type == 'status' ) {
            $function = 'wc_rbp_product_status';
        }
        return wc_rbp_product_get_db($post_id, $type, $function);
    }
}

if( ! function_exists('product_rbp_price') ) {
    /**
     * Gets product price from DB
     */
    function product_rbp_price($post_id) {
        $price = wc_rbp_product_variable($post_id, 'price');
        $price = apply_filters("product_rbp_price", $price, $post_id);
        return $price;
    }
}

if( ! function_exists('product_rbp_status') ) {
    /**
     * Returns Products Role Based Price Array In DB
     *
     * @param  int   $post_id     Post ID To Update
     * @param  array $price_array Price List
     *
     * @return boolean  [[Description]]
     * #TODO Integrate WC_RBP_PRODUCT_STATUS Function For Speed Outpu
     */
    function product_rbp_status($post_id) {
        global $product;
        $price = wc_rbp_product_variable($post_id, 'status');
        $price = apply_filters("product_rbp_status", $price, $post_id);
        return $price;
    }
}

if( ! function_exists('wc_rbp_settings_products_json') ) {
    function wc_rbp_settings_products_json($ids) {
        $json_ids = array();
        if( ! empty($ids) ) {
            if( is_string($ids) ) {
                $ids = explode(',', $ids);
            }

            foreach( $ids as $product_id ) {
                $product               = wc_get_product($product_id);
                $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
            }
        }
        return $json_ids;
    }
}

if( ! function_exists('wc_rbp_update_role_based_price_status') ) {

    /**
     * Updates Products Role Based Price Array In DB
     *
     * @param  int   $post_id     Post ID To Update
     * @param  array $price_array Price List
     *
     * @return boolean  [[Description]]
     */
    function wc_rbp_update_role_based_price_status($post_id, $status = TRUE) {
        update_post_meta($post_id, '_enable_role_based_price', $status);
        return TRUE;
    }
}

if( ! function_exists('wc_rbp_product_status') ) {
    /**
     * Retrives Status value from Databse.
     *
     * @param  [[Type]] $post_id [[Description]]
     *
     * @return [[Type]] [[Description]]
     * #TODO Integrate With product_rbp_status
     */
    function wc_rbp_product_status($post_id, $supress_filter = FALSE) {
        $cstatus = FALSE;
        $status  = get_post_meta($post_id, '_enable_role_based_price', TRUE);
        if( $status == '1' || $status == 'true' ) {
            $cstatus = TRUE;
        }

        if( ! $supress_filter )
            $cstatus = apply_filters('wc_rbp_product_status', $cstatus, $post_id);

        return $cstatus;
    }
}

if( ! function_exists('wc_rbp_price') ) {
    /**
     * Returns Price Based On Give Value
     *
     * @role  : enter role slug / use all to get all roles values
     * @price : use selling_price / regular_price or use all to get all values for the given role
     */

    function wc_rbp_price($post_id, $role, $price = 'regular_price', $args = array()) {
        $dbprice = product_rbp_price($post_id);
        $return  = FALSE;

        if( $price == 'all' && $role == 'all' ) {
            $return = $dbprice;
        } else if( $price == 'all' && $role !== 'all' ) {
            if( isset($dbprice[$role]) ) {
                $return = $dbprice[$role];
            }
        } else if( isset($dbprice[$role][$price]) ) {
            $return = $dbprice[$role][$price];
        }

        $return = apply_filters('wc_rbp_product_price', $return, $role, $price, $post_id, $args);
        return $return;
    }
}

if( ! function_exists('wc_rbp_active_price') ) {

    function wc_rbp_active_price($post_id, $role, $args = array(), $product = NULL) {
        $price = wc_rbp_price($post_id, $role, 'all', $args);
        if( isset($price['selling_price']) ) {
            if( ! empty($price['selling_price']) ) {
                return $price['selling_price'];
            }
        }

        return $price['regular_price'];
    }
}

if( ! function_exists('wc_rbp_sort_array_by_array') ) {
    function wc_rbp_sort_array_by_array(array $array, array $orderArray) {
        $ordered = array();
        foreach( $orderArray as $key ) {
            if( array_key_exists($key, $array) ) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }
}

if( ! function_exists('wc_rbp_remove_notice') ) {
    function wc_rbp_remove_notice($id) {
        WooCommerce_Plugin_Boiler_Plate_Admin_Notices::getInstance()
                                                     ->deleteNotice($id);
        return TRUE;
    }
}

if( ! function_exists('wc_rbp_notice') ) {
    function wc_rbp_notice($message, $type = 'update', $args = array()) {
        $notice   = '';
        $defaults = array( 'times' => 1, 'screen' => array(), 'users' => array(), 'wraper' => TRUE, 'id' => '' );
        $args     = wp_parse_args($args, $defaults);
        extract($args);

        if( $type == 'error' ) {
            $notice = new WooCommerce_Role_Based_Price_Admin_Error_Notice($message, $id, $times, $screen, $users);
        }

        if( $type == 'update' ) {
            $notice = new WooCommerce_Role_Based_Price_Admin_Updated_Notice($message, $id, $times, $screen, $users);
        }

        if( $type == 'upgrade' ) {
            $notice = new WooCommerce_Role_Based_Price_Admin_UpdateNag_Notice($message, $id, $times, $screen, $users);
        }

        $msgID   = $notice->getId();
        $message = str_replace('$msgID$', $msgID, $message);
        $notice->setContent($message);
        $notice->setWrapper($wraper);
        WooCommerce_Role_Based_Price_Admin_Notices::getInstance()
                                                  ->addNotice($notice);
    }
}

if( ! function_exists('wc_rbp_admin_error') ) {
    function wc_rbp_admin_error($message, $times = 1, $id, $screen = array(), $args = array()) {
        $args['id']     = $id;
        $args['times']  = $times;
        $args['screen'] = $screen;
        wc_rbp_notice($message, 'error', $args);
    }
}

if( ! function_exists('wc_rbp_admin_update') ) {
    function wc_rbp_admin_update($message, $times = 1, $id, $screen = array(), $args = array()) {
        $args['id']     = $id;
        $args['times']  = $times;
        $args['screen'] = $screen;
        wc_rbp_notice($message, 'update', $args);
    }
}

if( ! function_exists('wc_rbp_admin_upgrade') ) {
    function wc_rbp_admin_upgrade($message, $times = 1, $id, $screen = array(), $args = array()) {
        $args['id']     = $id;
        $args['times']  = $times;
        $args['screen'] = $screen;
        wc_rbp_notice($message, 'upgrade', $args);
    }
}

if( ! function_exists('wc_rbp_remove_link') ) {
    function wc_rbp_remove_link($attributes = '', $msgID = '$msgID$', $text = 'Remove Notice') {
        if( ! empty($msgID) ) {
            $removeKey = PLUGIN_DB . 'MSG';
            $url       = admin_url() . '?' . $removeKey . '=' . $msgID;
            //$url = wp_nonce_url($url, 'WCQDREMOVEMSG');
            $url = urldecode($url);
            $tag = '<a ' . $attributes . ' href="' . $url . '">' . __($text, WC_RBP_TXT) . '</a>';
            return $tag;
        }
    }
}

if( ! function_exists('wc_rbp_is_wc_v') ) {
    function wc_rbp_is_wc_v($compare = '>=', $version = '3.0') {
        $version = empty($version) ? WOOCOMMERCE_VERSION : $version;
        if( version_compare(WOOCOMMERCE_VERSION, $version, $compare) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}