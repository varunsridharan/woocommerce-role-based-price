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

class WooCommerce_Role_Based_Price_Addons {

    public function __construct() {
        add_action('wp_ajax_wc_rbp_get_addons_html', array( $this, 'list_addons_ajax' ));
        add_action(WC_RBP_DB . '_form_fields', array( $this, 'list_addons' ), 10, 2);
        add_action('wp_ajax_wc_rbp_activate_addon', array( $this, 'activate_plugin' ));
        add_action('wp_ajax_wc_rbp_deactivate_addon', array( $this, 'deactivate_plugin' ));
    }

    public function list_addons_ajax() {
        $this->list_addons('', 'addons');
    }

    public function list_addons($none, $form_id) {
        if( $form_id != 'addons' ) {
            return;
        }
        $this->plugins_data = $this->search_and_get_addons();
        $this->generate_view();
    }

    public function search_and_get_addons() {
        $search_dirs     = apply_filters('wc_rbp_addons_dir', array());
        $addons_others   = array();
        $internal_addons = $this->get_plugins(WC_RBP_PLUGIN);

        if( ! empty($search_dirs) ) {
            foreach( $search_dirs as $dir ) {
                $dir_addons    = $this->get_plugins($dir);
                $addons_others = array_merge($addons_others, $dir_addons);
                unset($dir_addons);
            }
        }

        $return = array_merge($internal_addons, $addons_others);
        return $return;
    }

    /**
     * Check the plugins directory and retrieve all plugin files with plugin data.
     * The file with the plugin data is the file that will be included and therefore
     * needs to have the main execution for the plugin. This does not mean
     * everything must be contained in the file and it is recommended that the file
     * be split for maintainability. Keep everything in one file for extreme
     * optimization purposes.
     *
     * @since 1.5.0
     *
     * @param string $plugin_folder Optional. Relative path to single plugin folder.
     *
     * @return array Key is the plugin file path and the value is an array of the plugin data.
     */
    public function get_plugins($plugin_folder = '') {
        $wp_plugins  = array();
        $plugin_root = WC_RBP_PLUGIN;
        if( ! empty($plugin_folder) ) {
            $plugin_root = $plugin_folder;
        }
        $plugins_dir  = @ opendir($plugin_root);
        $plugin_files = array();

        if( $plugins_dir ) {
            while( ( $file = readdir($plugins_dir) ) !== FALSE ) {
                if( substr($file, 0, 1) == '.' ) {
                    continue;
                }
                if( is_dir($plugin_root . '/' . $file) ) {
                    $plugins_subdir = @ opendir($plugin_root . '/' . $file);
                    if( $plugins_subdir ) {
                        while( ( $subfile = readdir($plugins_subdir) ) !== FALSE ) {
                            if( substr($subfile, 0, 1) == '.' ) {
                                continue;
                            }
                            if( substr($subfile, -4) == '.php' ) {
                                $plugin_files[] = "$file/$subfile";
                            }
                        }
                        closedir($plugins_subdir);
                    }
                } else {
                    if( substr($file, -4) == '.php' ) {
                        $plugin_files[] = $file;
                    }
                }
            }
            closedir($plugins_dir);
        }

        if( empty($plugin_files) ) {
            return $wp_plugins;
        }
        foreach( $plugin_files as $plugin_file ) {
            if( ! is_readable("$plugin_root/$plugin_file") ) {
                continue;
            }
            $plugin_data = $this->get_plugin_data("$plugin_root/$plugin_file", FALSE, TRUE);

            $plugin_base = $plugin_root . dirname($plugin_file);


            if( empty ($plugin_data['Name']) ) {
                continue;
            }
            $is_active                = wc_rbp_check_active_addon("$plugin_file");
            $plugin_data["is_active"] = $is_active;
            $plugin_data["installed"] = TRUE;

            $plugin_data["addon_root"]                 = $plugin_root . dirname($plugin_file) . '/';
            $plugin_data["addon_url"]                  = plugin_dir_url("$plugin_root/$plugin_file");
            $plugin_data["addon_slug"]                 = sanitize_title(dirname($plugin_file));
            $plugin_data["addon_folder"]               = dirname($plugin_file) . '/';
            $plugin_data['screenshots']                = glob($plugin_base . '/screenshot*.*');
            $wp_plugins[plugin_basename($plugin_file)] = $plugin_data;
        }

        return $wp_plugins;
    }

    /**
     * Parses the plugin contents to retrieve plugin's metadata.
     * The metadata of the plugin's data searches for the following in the plugin's
     * header. All plugin data must be on its own line. For plugin description, it
     * must not have any newlines or only parts of the description will be displayed
     * and the same goes for the plugin data. The below is formatted for printing.
     *
     *    ** Plugin Name: Name of the plugin
     *    ** Plugin Icon: (Icon URL / DATA Code)
     *    ** Description: some Description about plugin
     *    ** Version: 01
     *    ** Author: Author Name
     *    ** Author URL: Author URL
     *    ** Last Update: YYYY-MM-DD
     *    ** Required Plugins: plugin-folder/plugin-file.php | Version , plugin-folder/plugin-file.php | 2.0*
     *
     * @param string $plugin_file Path to the plugin file
     * @param bool   $markup      Optional. If the returned data should have HTML markup applied. Default true.
     * @param bool   $translate   Optional. If the returned data should be translated. Default true.
     */
    public function get_plugin_data($plugin_file, $markup = TRUE, $translate = TRUE) {
        $default_headers = array(
            'Name'        => 'Plugin Name',
            'PluginURI'   => 'Plugin URI',
            'icon'        => 'Plugin Icon',
            'Version'     => 'Version',
            'Description' => 'Description',
            'Author'      => 'Author',
            'AuthorURI'   => 'Author URI',
            'last_update' => 'Last Update',
            'rplugins'    => 'Required Plugins',
            'Category'    => 'Category',
        );

        $plugin_data = get_file_data($plugin_file, $default_headers, 'wc_rbp_plugin');

        if( empty($plugin_data['TextDomain']) ) {
            $plugin_data['TextDomain'] = WC_RBP_TXT;
        }
        if( empty($plugin_data['DomainPath']) ) {
            $plugin_data['DomainPath'] = FALSE;
        }
        if( empty($plugin_data['Category']) ) {
            $plugin_data['Category'] = 'general';
        }

        $cat = explode(',', $plugin_data['Category']);

        $plugin_data['Category'] = array();
        foreach( $cat as $c ) {
            $key                           = sanitize_key($c);
            $plugin_data['Category'][$key] = $c;
        }

        if( $markup || $translate ) {
            $plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, $markup, $translate);
        } else {
            $plugin_data['Title']      = $plugin_data['name'];
            $plugin_data['AuthorName'] = $plugin_data['author'];
        }

        return $plugin_data;
    }

    public function generate_view() {
        $category = $this->get_addon_category();
        $category = $this->get_html_addon_category($category);
        include( WC_RBP_ADMIN . 'views/addons-header.php' );
        foreach( $this->plugins_data as $addon_slug => $data ) {
            $wc_rbp_plugin_data = $data;
            $wc_rbp_plugin_slug = $addon_slug;
            $required_plugins   = $this->extract_required_plugins($wc_rbp_plugin_data);
            include( WC_RBP_ADMIN . 'views/addons-single.php' );
            unset($wc_rbp_plugin_data);
        }
        include( WC_RBP_ADMIN . 'views/addons-footer.php' );
    }

    public function get_addon_category() {
        $category             = array();
        $category['all']      = __('All', WC_RBP_TXT);
        $category['active']   = __('Active', WC_RBP_TXT);
        $category['inactive'] = __('InActive', WC_RBP_TXT);
        foreach( $this->plugins_data as $data ) {
            $cat = $data['Category'];
            foreach( $cat as $id => $c ) {
                if( ! in_array($c, $category) ) {
                    $category[$id] = $c;
                }
            }

        }
        return $category;
    }

    public function get_html_addon_category($cats) {
        $label  = __("Search Addons", WC_RBP_TXT);
        $output = '<div class="wp-filter"> <ul class="filter-links wc_rbp_addons_category addons_category">';

        foreach( $cats as $cat => $catv ) {
            $output .= '<li  id="' . $cat . '" class="' . $cat . ' category"><a href="javascript:void(0);" data-category="' . $cat . '">' . $catv . '</a> |  </li>';
        }
        $output .= '</ul>';

        $output .= '<div class="addons-search-form">';
        $output .= '<input type="search" placeholder="' . $label . '" class="wp-filter-search" value="" name="s" />';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public function extract_required_plugins($wc_rbp_plugin_data) {
        $plugins        = $wc_rbp_plugin_data['rplugins'];
        $plugins_return = array();
        $plugins        = explode(',', $plugins);
        $default_args   = array( 'Name' => '', 'URL' => '', 'Version' => '', 'Slug' => '' );

        foreach( $plugins as $plugin ) {
            if( empty($plugin) ) {
                continue;
            }
            $tmp_arr = array();
            $plugin  = str_replace(array( '[', ']' ), '', $plugin);
            $plug    = explode('|', $plugin);

            foreach( $plug as $p ) {

                $s              = preg_split("/ : /", $p);
                $s[0]           = isset($s[0]) ? trim($s[0]) : "";
                $s[1]           = isset($s[1]) ? trim($s[1]) : "";
                $tmp_arr[$s[0]] = $s[1];
            }

            if( ! empty($tmp_arr) ) {
                $tmp_arr          = wp_parse_args($tmp_arr, $default_args);
                $plugins_return[] = $tmp_arr;
            }
        }
        return $plugins_return;
    }

    public function deactivate_plugin() {
        $status = $this->addon_actions('deactivate');
        if( $status === 'invalidcode' ) {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_error">' . __('Unable to process you request. please try again later', WC_RBP_TXT) . '</span>' ));
        } else if( $status === 'verifyfailed' ) {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_ajaxerror">' . __('Unable To De-Activate Addon. Please Try Again Later', WC_RBP_TXT) . '</span>' ));
        } else if( $status === TRUE ) {
            wp_send_json_success(array( 'msg' => '<span class="wc_rbp_ajax_success">' . __('Addon De-Activated', WC_RBP_TXT) . '</span>' ));
        } else if( $status === 'alreadyactive' ) {
            wp_send_json_success(array( 'msg' => '<span class="wc_rbp_ajax_success">' . __('Addon Already De-Activated', WC_RBP_TXT) . '</span>' ));
        } else {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_error">' . __('Unable To De-Activate Addon. Please Try Again Later', WC_RBP_TXT) . '</span>' ));
        }
        wp_die();
    }

    public function addon_actions($action = 'activate') {
        if( ! isset($_REQUEST['wc_rbp_security_code']) ) {
            return 'invalidcode';
        }
        $nonce_action = 'wc_rbp_' . $action . '_addon';
        $verify       = wp_verify_nonce($_REQUEST['wc_rbp_security_code'], $nonce_action);
        if( ! $verify ) {
            return 'verifyfailed';
        }
        $function_call = 'wc_rbp_' . $action . '_addon';
        $status        = $function_call($_REQUEST['addon_slug']);
        if( $status ) {
            return TRUE;
        } else if( ! $status ) {
            return 'alreadyactive';
        }
        return FALSE;
    }

    public function activate_plugin() {
        $status = $this->addon_actions();
        if( $status === 'invalidcode' ) {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_error">' . __('Unable to process you request. please try again later', WC_RBP_TXT) . '</span>' ));
        } else if( $status === 'verifyfailed' ) {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_ajaxerror">' . __('Unable To Activate Addon. Please Try Again Later', WC_RBP_TXT) . '</span>' ));
        } else if( $status === TRUE ) {
            wp_send_json_success(array( 'msg' => '<span class="wc_rbp_ajax_success">' . __('Addon Activated', WC_RBP_TXT) . '</span>' ));
        } else if( $status === 'alreadyactive' ) {
            wp_send_json_success(array( 'msg' => '<span class="wc_rbp_ajax_success">' . __('Addon Already Activated', WC_RBP_TXT) . '</span>' ));
        } else {
            wp_send_json_error(array( 'msg' => '<span class="wc_rbp_ajax_error">' . __('Unable To Activate Addon. Please Try Again Later', WC_RBP_TXT) . '</span>' ));
        }
        wp_die();
    }

    public function get_addon_action_button($plugin_slug, $required_plugins) {

        $is_active              = wc_rbp_check_active_addon($plugin_slug);
        $extraClass             = '';
        $activate_button_text   = __('Activate', WC_RBP_TXT);
        $inactivate_button_text = __('Deactivate', WC_RBP_TXT);
        $requried_satisfied     = $this->check_if_requried_satisfied($required_plugins);
        $activate_button_url    = $this->get_addon_action_link($plugin_slug);
        $inactivate_button_url  = $this->get_addon_action_link($plugin_slug, 'deactivate');
        $activate_button_html   = '<button type="button" data-slug="' . $plugin_slug . '" ';
        $inactivate_button_html = '<button type="button" data-slug="' . $plugin_slug . '" ';
        $activate_button_html   .= 'class="wc-rbp-activate-now button button-primary ';
        $inactivate_button_html .= 'class="wc-rbp-deactivate-now button button-secondary ';

        if( $is_active ) {
            $activate_button_html   .= ' hidden hide "';
            $inactivate_button_html .= '"';
        } else {
            $activate_button_html   .= '"';
            $inactivate_button_html .= ' hidden hide "';

        }

        if( ! $requried_satisfied ) {
            $activate_button_html .= ' disabled="disabled" ';
        }

        $activate_button_html   .= ' href="' . $activate_button_url . '" >' . $activate_button_text . '</button>';
        $inactivate_button_html .= ' href="' . $inactivate_button_url . '" >' . $inactivate_button_text . '</button>';
        $html_btn               = $activate_button_html . $inactivate_button_html;
        return $html_btn;

    }

    public function check_if_requried_satisfied($requireds) {
        $success = 0;
        $failed  = 0;
        foreach( $requireds as $plugin ) {
            $plugin_status = $this->check_plugin_status($plugin['Slug']);
            if( $plugin_status === TRUE ) {
                $success++;
            } else {
                $failed++;
            }
        }

        if( $success == count($requireds) ) {
            return TRUE;
        }
        return FALSE;
    }

    public function check_plugin_status($slug) {
        $val_plugin = validate_plugin($slug);
        if( is_wp_error($val_plugin) ) {
            return 'notexist';
        } else if( is_plugin_active($slug) ) {
            return TRUE;
        } else if( is_plugin_inactive($slug) ) {
            return FALSE;
        }
        return FALSE;
    }

    public function get_addon_action_link($plugin_slug, $type = "active") {
        $is_active = wc_rbp_check_active_addon($plugin_slug);
        $action    = 'wc_rbp_activate_addon';
        if( $type == 'deactivate' ) {
            $action = 'wc_rbp_deactivate_addon';
        }
        $url = admin_url('admin-ajax.php?action=' . $action . '&addon_slug=' . $plugin_slug);
        $url = wp_nonce_url($url, $action, 'wc_rbp_security_code');
        return $url;
    }

    public function get_addon_icon($data, $echo = TRUE) {
        $icon = WC_RBP_IMG . 'addon_icon.jpg';

        if( file_exists($data['addon_root'] . 'icon.png') ) {
            $icon = $data['addon_url'] . 'icon.png';
        } else if( file_exists($data['addon_root'] . 'icon.jpg') ) {
            $icon = $data['addon_url'] . 'icon.jpg';
        } else if( file_exists($data['addon_root'] . $data['addon_slug'] . '-icon.png') ) {
            $icon = $data['addon_url'] . $data['addon_slug'] . '-icon.png';
        } else if( file_exists($data['addon_root'] . $data['addon_slug'] . '-icon.jpg') ) {
            $icon = $data['addon_url'] . $data['addon_slug'] . '-icon.jpg';
        } else if( isset($data['icon']) ) {
            if( filter_var($data['icon'], FILTER_VALIDATE_URL) !== FALSE ) {
                $icon = $data['icon'];
            }
        }

        $icon = '<img src="' . $icon . '" class="plugin-icon" />';
        if( $echo ) {
            echo $icon;
        } else {
            return $icon;
        }
    }

}