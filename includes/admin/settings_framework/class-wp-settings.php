<?php
/**
 * Class for registering settings and sections and for display of the settings form(s).
 * For detailed instructions see: https://github.com/keesiemeijer/WP-Settings
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package    WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/WordPress/Settings
 * @since      3.0
 * @version    2.0
 * @author     keesiemeijer
 */
if( ! class_exists('WooCommerce_Role_Based_Price_WP_Settings') ) {
    class WooCommerce_Role_Based_Price_WP_Settings {

        /**
         * Version of WP_Settings_Settings class
         *
         * @since 2.0
         * @var
         */
        public $version = 2.0;

        /**
         * Current settings page.
         *
         * @since 2.0
         * @var array
         */
        public $current_page = array();

        /**
         * Debug errors and notices.
         *
         * @since 2.0
         * @var string
         */
        public $debug = '';

        /**
         * Admin pages.
         *
         * @since 2.0
         * @var array
         */
        private $pages = array();

        /**
         * Admin pages.
         *
         * @since 2.0
         * @var array
         */
        private $fields;

        /**
         * Unique plugin admin page hook suffix.
         *
         * @since 2.0
         * @var array
         */
        private $page_hook;

        /**
         * Fields that need Javascript. (e.g. colorpicker)
         *
         * @since 2.0
         * @var array
         */
        private $script_types;

        /**
         * Fields that need the label_argument in add_settings_field()
         *
         * @since 2.0
         * @var array
         */
        private $label_for = array( 'text', 'select', 'textarea' );

        /**
         * array of Javascrips needed for the current settings page
         *
         * @since 2.0
         * @var array
         */
        private $load_scripts = array();

        /**
         * Multiple forms on one settings page.
         *
         * @since 2.0
         * @var bool
         */
        private $multiple_forms = FALSE;

        /**
         * valid admin pages and fields arrays.
         *
         * @since 2.0
         * @var bool
         */
        private $valid_pages = FALSE;


        /**
         * Registers settings using the WorPres settings Api.
         *
         * @uses  WP_Settings_Settings_Fields class
         * @since 2.0   *
         *
         * @param array  $pages     Array with admin pages.
         * @param string $page_hook Unique plugin admin page hook suffix.
         */
        public function init($pages, $page_hook = '') {

            $this->pages     = (array) $pages;
            $this->page_hook = trim(sanitize_title((string) $page_hook));

            // Debug strings don't use Gettext functions for translation.

            if( ! class_exists('WooCommerce_Role_Based_Price_Settings_WP_Fields') ) {
                $this->debug .= "Error: class WooCommerce_Role_Based_Price_Settings_WP_Fields doesn't exist<br/>";
            }

            if( '' === $this->page_hook ) {
                $this->debug .= "Error: parameter 'page_hook' not provided in settings_admin_init()<br/>";
            }

            // Let external scripts do debugging .
            $this->debug .= apply_filters("{$this->page_hook}_debug", $this->debug, $this->pages);

            if( $this->debug ) {
                return $this->valid_pages = FALSE; // Don't display the form and navigation.
            }

            // Passed validation (required to show form and navigation).
            $this->valid_pages = TRUE;

            $this->current_page = $this->get_current_admin_page();

            if( isset($this->current_page['multiform']) && $this->current_page['multiform'] ) {
                $this->multiple_forms = ( count($this->current_page['sections']) > 1 ) ? TRUE : FALSE;
            }

            // Instanciate the form fields.
            $this->fields = new WooCommerce_Role_Based_Price_Settings_WP_Fields(get_settings_errors());

            // Array of fields that needs the 'label_for' parameter (add_settings_field()).
            $this->label_for = apply_filters("{$this->page_hook}_label_for", $this->label_for);

            // Array of fields that needs javascript (e.g. 'color_picker').
            $this->script_types = apply_filters("{$this->page_hook}_script_field_types", array());

            $this->add_settings_sections();

            // Register all the settings.
            $this->register_settings();

            // only load javascript if it's needed for the current admin page
            if( ! empty($this->load_scripts) ) {
                $this->load_scripts = array_unique($this->load_scripts);
                add_action('admin_print_scripts-' . $this->page_hook, array( $this, 'enqueue_scripts' ));
            }

        } // admin_init()


        /**
         * Adds setting sections
         *
         * @since 2.0
         * @return void
         */
        private function add_settings_sections() {

            foreach( $this->current_page['sections'] as $section ) {

                $section_description = '__return_false';

                if( isset($section['desc']) && $section['desc'] ) {
                    $section_description = array( $this, 'render_section_description' );
                }

                $title = ( isset($section['title']) ) ? $section['title'] : ''; // optional

                // Add page hook to sections and page ids.
                $page_id    = ( $this->multiple_forms ) ? $section['id'] : $this->current_page['id'];
                $page_id    = $this->page_hook . '' . $page_id;
                $section_id = $this->page_hook . '' . $section['id'];

                // Add database option(s) to debug messages.
                $this->debug .= ( '' === $this->debug ) ? 'Database option(s) created for this page:<br/>' : '';
                $this->debug .= "database option: " . $section_id . '<br/>'; // database option name

                add_settings_section($section_id, $title, $section_description, $page_id);

                if( isset($section['fields']) && ! empty($section['fields']) ) {

                    // Add fields to sections.
                    $this->add_settings_fields($section_id, $section['fields'], $page_id);
                }
            }
        }


        /**
         * Adds all fields to a settings section.
         *
         * @since 2.0
         *
         * @param string $sections_id  ID of section to add fields to.
         * @param array  $fields       Array with section fields
         * @param string $page_id      Page id.
         * @param bool   $use_defaults Use default values for the settings fields.
         */
        private function add_settings_fields($sections_id, $fields, $page_id) {

            $opt_defaults = array();
            $defaults     = array(
                'section' => $sections_id,
                'id'      => '',
                'type'    => '',
                'label'   => '',
                'desc'    => '',
                'size'    => FALSE,
                'options' => '',
                'default' => '',
                'content' => '',
                'attr'    => FALSE,
                'before'  => '',
                'after'   => '',
                '_type'   => '',
            );

            // Check if database option exist (use defaults if it doesn't).
            $use_defaults = ( FALSE === get_option($sections_id) ) ? TRUE : FALSE;

            foreach( $fields as $field ) {

                // Field (rows) can be added by external scripts.
                $multiple = ( isset($field['fields']) && $field['fields'] ) ? TRUE : FALSE;
                $options  = ( $multiple ) ? (array) $field['fields'] : array( $field );

                foreach( $options as $key => $opt ) {

                    $args = wp_parse_args($opt, $defaults);

                    $args['default']          = ( $use_defaults ) ? $args['default'] : '';
                    $opt_defaults[$opt['id']] = $args['default'];

                    if( in_array($args['type'], $this->script_types) ) {
                        $this->load_scripts[] = $args['type']; // field needs javascript
                    }

                    if( in_array($args['type'], $this->label_for) ) {
                        $args['label_for'] = $sections_id . '' . $args['id'];
                    }

                    if( $multiple ) {
                        $field['fields'][$key] = $args;
                    }
                }

                if( $multiple ) {
                    $args = $field;
                }

                // ability to add fields with an action hook
                if( ! method_exists($this->fields, 'callback_' . $field['type']) ) {
                    $args['callback']  = $field['type'];
                    $args['page_hook'] = $this->page_hook;
                    $field['type']     = 'extra_field';
                }

                if( method_exists($this->fields, 'callback_' . $field['type']) ) {
                    add_settings_field($sections_id . '[' . $field['id'] . ']', isset($args['label']) ? $args['label'] : '', array(
                            $this->fields,
                            'callback_' . $field['type'],
                        ), $page_id, $sections_id, $args);
                }
            }

            // add the option or validation errors show twice on the first submit (todo: Why?).
            if( $use_defaults ) {
                add_option($sections_id, $opt_defaults);
            }
        }


        /**
         * Registers settings
         *
         * @since 2.0
         * @return void
         */
        private function register_settings() {
            foreach( $this->pages as $page ) {
                foreach( $page['sections'] as $section ) {

                    // Use section ids for multiple forms.
                    if( isset($page['multiform']) && $page['multiform'] ) {
                        $page['id'] = ( count($page['sections']) > 1 ) ? $section['id'] : $page['id'];
                    }

                    $page_id     = $this->page_hook . '' . $page['id'];
                    $sections_id = $this->page_hook . '' . $section['id'];

                    if( isset($section['validate_callback']) && $section['validate_callback'] ) {
                        register_setting($page_id, $sections_id, $section['validate_callback']);
                    } else {
                        register_setting($page_id, $sections_id);
                    }
                }
            }
        }


        /**
         * Gets all settings from all sections
         *
         * @since 2.0
         * @return array Array with settings.
         */
        public function get_settings($section = '') {
            $settings = array();

            if( ! empty($section) ) {
                return get_option($this->page_hook . '' . $section);
            }

            foreach( (array) $this->pages as $page ) {
                if( ! isset($page['sections']) ) {
                    continue;
                }

                foreach( $page['sections'] as $section ) {
                    if( ! isset($section['id']) ) {
                        continue;
                    }

                    $option = get_option($this->page_hook . '' . $section['id']);
                    if( $option ) {
                        unset($option['section_id']);
                        $settings[$section['id']] = $option;
                    }
                }
            }

            return $settings;
        }


        /**
         * Returns the current settings page.
         *
         * @since 2.0
         *
         * @param array $admin_pages . Array of settings pages.
         *
         * @return array   Current settings page.
         */
        public function get_current_admin_page() {

            foreach( (array) $this->pages as $page ) {
                if( isset($_GET['tab']) && $_GET['tab'] ) {
                    if( ( $_GET['tab'] === $page['id'] ) || ( $_GET['tab'] === $page['slug'] ) )
                        $current_page = $page;
                }
            }

            // Set the first settings page as current if it's not a tab.
            if( empty($current_page) ) {
                $current_page = $this->pages[0];
            }

            return $current_page;
        }


        /**
         * Adds a admin page.
         *
         * @since 2.0
         *
         * @param array $page Page array.
         *
         * @return array Admin pages array with the page added.
         */
        public function add_page($page) {
            return $this->pages[] = $page;
        }


        /**
         * Adds multiple admin pages.
         *
         * @since 2.0
         *
         * @param array $pages Array with pages.
         *
         * @return array Admin pages array with the pages added.
         */
        public function add_pages($pages) {
            foreach( $pages as $page ) {
                $this->add_page($page);
            }
            return $this->pages;
        }


        /**
         * Adds a section to an admin page.
         *
         * @since 2.0
         *
         * @param string $page    Page id.
         * @param array  $section Section array.
         *
         * @return array Admin pages array with the section added.
         */
        public function add_section($page, $section) {

            foreach( $this->pages as $key => $_page ) {

                if( $page !== $_page['id'] ) {
                    continue;
                }

                if( isset($this->pages[$key][$page]['sections']) ) {
                    $this->pages[$key]['sections'] = array();
                }

                $this->pages[$key]['sections'][] = $section;
            }

            return $this->pages;
        }


        /**
         * Adds multiple sections to an admin page.
         *
         * @since 2.0
         *
         * @param array $string   Page id
         * @param array $sections Array with sections.
         *
         * @return array Admin pages array with the sections added.
         */
        public function add_sections($page, $sections) {
            foreach( $sections as $section ) {
                $this->pages = $this->add_section($page, $section);
            }
            return $this->pages;
        }


        /**
         * Adds a form field to a section.
         *
         * @since 2.0
         *
         * @param string $page    Page id.
         * @param string $section Section id.
         * @param array  $field   Field array.
         *
         * @return array Admin pages array with the field added.
         */
        public function add_field($page, $section, $field) {

            foreach( $this->pages as $key => $_page ) {

                if( $page !== $_page['id'] ) {
                    continue;
                }

                if( ! isset($this->pages[$key]['sections']) ) {
                    continue;
                }

                $_sections = $this->pages[$key]['sections'];

                foreach( $_sections as $_key => $_section ) {

                    if( $section !== $_section['id'] ) {
                        continue;
                    }

                    if( ! isset($this->pages[$key]['sections'][$_key]['fields']) ) {
                        $this->pages[$key]['sections'][$_key]['fields'] = array();
                    }

                    $this->pages[$key]['sections'][$_key]['fields'][] = $field;
                }
            }

            return $this->pages;
        }


        /**
         * Adds multiple form fields to a section.
         *
         * @since 2.0
         *
         * @param string $page    Page id.
         * @param string $section Section id.
         * @param array  $fields  Array with fields.
         *
         * @return array Admin pages array with the fields added.
         */
        public function add_fields($page, $section, $fields) {
            foreach( $fields as $field ) {
                $this->pages = $this->add_field($page, $section, $field);
            }
            return $this->pages;
        }


        /**
         * Enqueue javascript for fields that need them.
         *
         * @since 2.0
         * @return void
         */
        public function enqueue_scripts() {
            $screen = get_current_screen();

            // Only load scripts on plugin pages.
            if( $screen->id === $this->page_hook ) {
                do_action("{$this->page_hook}_scripts", $this->load_scripts);
            }
        }


        /**
         * Display the description of a section.
         *
         * @since 2.0
         *
         * @param array $section Description of section.
         *
         * @return void
         */
        public function render_section_description($section) {
            foreach( $this->current_page['sections'] as $setting ) {
                if( $this->page_hook . '' . $setting['id'] === $section['id'] )
                    echo sprintf('<p class="section_desc">%s</p>', $setting['desc']);
            }
        }


        /**
         * Display Plugin Title and if needed tabbed navigation.
         *
         * @since 2.0
         *
         * @param string $plugin_title Plugin title.
         * @param string $tab_id       Page id. Manually set the active tab.
         *
         * @return void
         */
        public function render_header($plugin_title = '', $tab_id = FALSE) {

            if( ! empty($plugin_title) )
                echo get_screen_icon() . '<h2>' . (string) $plugin_title . '</h2>';

            // if ( !$this->valid_pages )
            //  return;

            $page_title_count = 0;

            foreach( $this->pages as $page ) {
                if( isset($page['title']) && $page['title'] )
                    ++$page_title_count;
            }

            $html       = '';
            $current    = $this->current_page;
            $page_ids   = wp_list_pluck($this->pages, 'id');
            $cur_tab_id = ( $tab_id ) ? (string) $tab_id : $current['id'];
            $cur_tab_id = ( in_array($cur_tab_id, $page_ids) ) ? $cur_tab_id : $current['id'];
            $i          = 0;

            foreach( $this->pages as $page ) {

                if( ( isset($page['title']) && $page['title'] ) ) {
                    if( $page_title_count > 1 ) {
                        $html .= ( 0 === $i ) ? '<h2 class="nav-tab-wrapper">' : '';

                        $active = '';
                        if( $cur_tab_id === $page['id'] )
                            $active = ' nav-tab-active';

                        // Get the url of the current settings page.
                        $tab_url = remove_query_arg(array( 'tab', 'settings-updated' ));

                        // Add query arg 'tab' if it's not the first settings page.
                        if( $this->pages[0]['id'] !== $page['id'] )
                            $tab_url = add_query_arg('tab', $page['slug'], $tab_url);

                        $html .= sprintf('<a href="%1$s" class="nav-tab%2$s" id="%3$s-tab">%4$s</a>', esc_url($tab_url), $active, esc_attr($page['id']), $page['title']);

                        $html .= ( ++$i === $page_title_count ) ? '</h3>' : '';
                    }

                    if( $page_title_count === 1 ) {
                        if( isset($current['title']) && $current['title'] === $page['title'] ) {
                            $html .= '<h3>' . $page['title'] . '</h3>';
                            break;
                        }
                    }

                }
            }

            echo $html;
        } // render_header()


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
        function do_settings_sections($page) {
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
                echo '<table class="wc_rbp_settings_table">';
                $this->do_settings_fields($page, $section['id']);
                echo '</table>';
                if( $section_count > 1 ) {
                    echo '</div>';
                }
            }
        }

        /**
         * Print out the settings fields for a particular settings section
         *
         * Part of the Settings API. Use this in a settings page to output
         * a specific section. Should normally be called by do_settings_sections()
         * rather than directly.
         *
         * @global       $wp_settings_fields Storage array of settings fields and their pages/sections
         *
         * @since 2.7.0
         *
         * @param string $page               Slug title of the admin page who's settings fields you want to show.
         * @param string $section            Slug title of the settings section who's fields you want to show.
         */
        function do_settings_fields($page, $section) {
            global $wp_settings_fields;

            if( ! isset($wp_settings_fields[$page][$section]) )
                return;

            foreach( (array) $wp_settings_fields[$page][$section] as $field ) {
                $class = '';

                if( ! empty($field['args']['class']) ) {
                    $class = ' class="' . esc_attr($field['args']['class']) . '"';
                }

                echo "<tr{$class}>";
                $desc = sprintf('<p class="description">%s</p>', $field['args']['desc']);
                if( ! empty($field['args']['label_for']) ) {
                    echo '<th scope="row"><label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . $desc . '</label></th>';
                } else {
                    echo '<th scope="row">' . $field['title'] . $desc . '</th>';
                }

                echo '<td>';
                call_user_func($field['callback'], $field['args']);
                echo '</td>';
                echo '</tr>';
            }
        }


        /**
         * Displays the form(s) and sections.
         *
         * @since 2.0
         * @return void
         */
        public function render_form() {

            if( ! $this->valid_pages ) {
                return;
            }

            $page = $this->current_page;

            if( ! empty($page) ) {

                $ids   = wp_list_pluck($page['sections'], 'id');
                $forms = ( $this->multiple_forms ) ? $page['sections'] : array( $page );

                // Section ids hidden inputs.
                $section_ids = '';
                foreach( $ids as $id ) {
                    $section_ids .= "<input id='{$this->page_hook}_{$id}_section_id' type='hidden' value='{$id}' name='{$this->page_hook}_{$id}[section_id]'>";
                }

                foreach( $forms as $form ) {

                    echo '<form method="post" action="options.php">';
                    // print hidden section_id fields
                    echo $section_ids;

                    // lets you add additional fields
                    echo apply_filters("{$this->page_hook}_form_fields", '', $form['id'], $form);

                    settings_fields($this->page_hook . '' . $form['id']);
                    $this->do_settings_sections($this->page_hook . '' . $form['id']);

                    $submit = ( isset($form['submit']) && $form['submit'] ) ? $form['submit'] : '';

                    if( ( '' === $submit ) && isset($page['submit']) && $page['submit'] ) {
                        $submit = $page['submit'];
                    }

                    $text             = isset($submit['text']) ? $submit['text'] : NULL;
                    $type             = isset($submit['$type']) ? $submit['text'] : 'primary';
                    $name             = isset($submit['$name']) ? $submit['name'] : 'submit';
                    $other_attributes = array( 'id' => $form['id'] );

                    submit_button($text, $type, $name, TRUE, $other_attributes);
                    echo '</form>';
                }
            }
        } // render_form()

    } // class
} // class exists