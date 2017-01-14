<?php

if (!class_exists('RapidAddon')) {
	
	class RapidAddon {

		public $name;
		public $slug;
		public $fields;
		public $options = array();
		public $accordions = array();
		public $image_sections = array();
		public $import_function;
		public $notice_text;
		public $logger = null;
		public $when_to_run = false;
		public $image_options = array(
			'download_images' => 'yes', 
			'download_featured_delim' => ',', 
			'download_featured_image' => '',
			'featured_image' => '',
			'featured_delim' => ',', 
			'search_existing_images' => 1,
			'is_featured' => 0,
			'create_draft' => 'no',
			'set_image_meta_title' => 0,
			'image_meta_title_delim' => ',',
			'image_meta_title' => '',
			'set_image_meta_caption' => 0,
			'image_meta_caption_delim' => ',',
			'image_meta_caption' => '',
			'set_image_meta_alt' => 0,
			'image_meta_alt_delim' => ',',
			'image_meta_alt' => '',
			'set_image_meta_description' => 0,
			'image_meta_description_delim' => ',',
			'image_meta_description' => '',
			'auto_rename_images' => 0,
			'auto_rename_images_suffix' => '',
			'auto_set_extension' => 0,
			'new_extension' => ''
		);	

		function __construct($name, $slug) {

			$this->name = $name;
			$this->slug = $slug;

		}

		function set_import_function($name) {
			$this->import_function = $name;
		}

		function is_active_addon($post_type = null) {
			
			$addon_active = false;

			if ($post_type != null) {
				if (@in_array($post_type, $this->active_post_types) or empty($this->active_post_types)) {
					$addon_active = true;
				}
			}			

			if ($addon_active){
				
				$current_theme = wp_get_theme();
				$theme_name = $current_theme->get('Name');
				
				$addon_active = (@in_array($theme_name, $this->active_themes) or empty($this->active_themes)) ? true : false;
				
				if ( $addon_active and ! empty($this->active_plugins) ){

					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

					foreach ($this->active_plugins as $plugin) {
						if ( ! is_plugin_active($plugin) ) {
							$addon_active = false;
							break;
						}
					}					
				}

			}

			if ($this->when_to_run == "always") {
				return true;
			}

			return $addon_active;
		}
		
		/**
		* 
		* Add-On Initialization
		*
		* @param array $conditions - list of supported themes and post types
		*
		*/
		function run($conditions = array()) {

			if (empty($conditions)) {
				$this->when_to_run = "always";
			}

			@$this->active_post_types = ( ! empty($conditions['post_types'])) ? $conditions['post_types'] : array();
			@$this->active_themes = ( ! empty($conditions['themes'])) ? $conditions['themes'] : array();
			@$this->active_plugins = ( ! empty($conditions['plugins'])) ? $conditions['plugins'] : array();			

			add_filter('pmxi_addons', array($this, 'wpai_api_register'));
			add_filter('wp_all_import_addon_parse', array($this, 'wpai_api_parse'));
			add_filter('wp_all_import_addon_import', array($this, 'wpai_api_import'));
			add_filter('pmxi_options_options', array($this, 'wpai_api_options'));
			add_filter('wp_all_import_image_sections', array($this, 'additional_sections'), 10, 1);
			add_action('pmxi_extend_options_featured',  array($this, 'wpai_api_metabox'), 10, 1);
			add_action('admin_init', array($this, 'admin_notice_ignore'));
           
		}

		function parse($data) {

			$parsedData = $this->helper_parse($data, $this->options_array());
			return $parsedData;

		}


		function add_field($field_slug, $field_name, $field_type, $enum_values = null, $tooltip = "") {

			$field =  array("name" => $field_name, "type" => $field_type, "enum_values" => $enum_values, "tooltip" => $tooltip, "is_sub_field" => false, "is_main_field" => false, "slug" => $field_slug);

			$this->fields[$field_slug] = $field;

			if ( ! empty($enum_values) ){
				foreach ($enum_values as $key => $value) {
					if (is_array($value))
					{
						if ($field['type'] == 'accordion')
						{ 
							$this->fields[$value['slug']]['is_sub_field'] = true;
						}
						else
						{
							foreach ($value as $n => $param) {							
								if (is_array($param) and ! empty($this->fields[$param['slug']])){
									$this->fields[$param['slug']]['is_sub_field'] = true;								
								}
							}
						}
					}
				}
			}

			return $field;

		}

		/**
		* 
		* Add an option to WP All Import options list
		*
		* @param string $slug - option name
		* @param string $default_value - default option value
		*
		*/
		function add_option($slug, $default_value = ''){
			$this->options[$slug] = $default_value;
		}

		function options_array() {

			$options_list = array();

			foreach ($this->fields as $field_slug => $field_params) {
				if (in_array($field_params['type'], array('title', 'plain_text'))) continue;
				$options_list[$field_slug] = '';
			}

			if ( ! empty($this->options) ){
				foreach ($this->options as $slug => $value) {
					$options_arr[$slug] = $value;
				}
			}

			$options_arr[$this->slug] = $options_list;

			return $options_arr;

		}

		function wpai_api_options($all_options) {

			$all_options = $all_options + $this->options_array();

			return $all_options;

		}


		function wpai_api_register($addons) {

			if (empty($addons[$this->slug])) {
				$addons[$this->slug] = 1;
			}

			return $addons;

		}


		function wpai_api_parse($functions) {

			$functions[$this->slug] = array($this, 'parse');
			return $functions;

		}


		function wpai_api_import($functions) {

			$functions[$this->slug] = array($this, 'import');
			return $functions;

		}



		function import($importData, $parsedData) {

			if (!$this->is_active_addon($importData['post_type'])) {
				return;
			}

			$import_options = $importData['import']['options'][$this->slug];

	//		echo "<pre>";
	//		print_r($import_options);
	//		echo "</pre>";

			if ( ! empty($parsedData) )	{

				$this->logger = $importData['logger'];

				$post_id = $importData['pid'];
				$index = $importData['i'];

				foreach ($this->fields as $field_slug => $field_params) {

					if (in_array($field_params['type'], array('title', 'plain_text'))) continue;

					if ($field_params['type'] == 'image') {

						// import the specified image, then set the value of the field to the image ID in the media library

						$image_url_or_path = $parsedData[$field_slug][$index];

						$download = $import_options['download_image'][$field_slug];

						$uploaded_image = PMXI_API::upload_image($post_id, $image_url_or_path, $download, $importData['logger'], true);

						$data[$field_slug] = array(
							"attachment_id" => $uploaded_image,
							"image_url_or_path" => $image_url_or_path,
							"download" => $download
						);

					} else {

						// set the field data to the value of the field after it's been parsed
						$data[$field_slug] = $parsedData[$field_slug][$index];

					}

					// apply mapping rules if they exist
					if ($import_options['mapping'][$field_slug]) {
						$mapping_rules = json_decode($import_options['mapping'][$field_slug], true);

						if (!empty($mapping_rules) and is_array($mapping_rules)) {
							foreach ($mapping_rules as $rule_number => $map_to) {
								if (!empty($map_to[trim($data[$field_slug])])){
									$data[$field_slug] = trim($map_to[trim($data[$field_slug])]);
									break;
								}
							}
						}
					}
					// --------------------


				}

				call_user_func($this->import_function, $post_id, $data, $importData['import']);
			}

		}


		function wpai_api_metabox($post_type) {

			if (!$this->is_active_addon($post_type)) {
				return;
			}

			echo $this->helper_metabox_top($this->name);

			$current_values = $this->helper_current_field_values();

			$visible_fields = 0;

			foreach ($this->fields as $field_slug => $field_params) {
				if ($field_params['is_sub_field']) continue;
				$visible_fields++;
			}

			$counter = 0;

			foreach ($this->fields as $field_slug => $field_params) {				

				// do not render sub fields
				if ($field_params['is_sub_field']) continue;		

				$counter++;		

				$this->render_field($field_params, $field_slug, $current_values, $visible_fields == $counter);										

				//if ( $field_params['type'] != 'accordion' ) echo "<br />";				

			}

			echo $this->helper_metabox_bottom();

			if ( ! empty($this->image_sections) ){				

				foreach ($this->image_sections as $section) {
					$section_options = array();
					foreach ($this->image_options as $slug => $value) {
						$section_options[$section['slug'] . $slug] = $value;
					}										
					PMXI_API::add_additional_images_section($section['title'], $section['slug'], $this->helper_current_field_values($section_options), '', true, false, $section['type']);
				}
			}

		}		

		function render_field($field_params, $field_slug, $current_values, $in_the_bottom = false){

			if ($field_params['type'] == 'text') {

				PMXI_API::add_field(
					'simple',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug]
					)
				);

			} else if ($field_params['type'] == 'textarea') {

				PMXI_API::add_field(
					'textarea',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug]
					)
				);

			} else if ($field_params['type'] == 'image') {

				PMXI_API::add_field(
					'image',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug],
						'download_image' => $current_values[$this->slug]['download_image'][$field_slug],
						'field_key' => $field_slug,
						'addon_prefix' => $this->slug

					)
				);

			} else if ($field_params['type'] == 'radio') {					

				PMXI_API::add_field(
					'enum',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug],
						'enum_values' => $field_params['enum_values'],
						'mapping' => true,
						'field_key' => $field_slug,
						'mapping_rules' => $current_values[$this->slug]['mapping'][$field_slug],
						'xpath' => $current_values[$this->slug]['xpaths'][$field_slug],
						'addon_prefix' => $this->slug,
						'sub_fields' => $this->get_sub_fields($field_params, $field_slug, $current_values)
					)
				);

			} else if($field_params['type'] == 'accordion') {

				PMXI_API::add_field(
					'accordion',
					$field_params['name'],
					array(						
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",																
						'field_key' => $field_slug,								
						'addon_prefix' => $this->slug,
						'sub_fields' => $this->get_sub_fields($field_params, $field_slug, $current_values),
						'in_the_bottom' => $in_the_bottom						
					)
				);

			} else if($field_params['type'] == 'title'){

				?>
				<h4 class="wpallimport-add-on-options-title"><?php _e($field_params['name'], 'wp_all_import_plugin'); ?><?php if ( ! empty($field_params['tooltip'])) { ?><a href="#help" class="wpallimport-help" title="<?php echo $field_params['tooltip']; ?>" style="position:relative; top: -1px;">?</a><?php } ?></h4>
				<?php

			} else if($field_params['type'] == 'plain_text'){

				?>
				<p style="margin: 0 0 12px 0;"><?php echo $field_params['name'];?></p>
				<?php

			}


		}
			/**
			*
			* Helper function for nested radio fields
			*
			*/
			function get_sub_fields($field_params, $field_slug, $current_values){
				$sub_fields = array();	
				if ( ! empty($field_params['enum_values']) ){										
					foreach ($field_params['enum_values'] as $key => $value) {					
						$sub_fields[$key] = array();	
						if (is_array($value)){
							if ($field_params['type'] == 'accordion'){								
								$sub_fields[$key][] = $this->convert_field($value, $current_values);
							}
							else
							{
								foreach ($value as $k => $sub_field) {								
									if (is_array($sub_field) and ! empty($this->fields[$sub_field['slug']]))
									{									
										$sub_fields[$key][] = $this->convert_field($sub_field, $current_values);									
									}								
								}
							}
						}
					}
				}
				return $sub_fields;
			}			

			function convert_field($sub_field, $current_values){
				$field = array();
				switch ($this->fields[$sub_field['slug']]['type']) {
					case 'text':
						$field = array(
							'type'   => 'simple',
							'label'  => $this->fields[$sub_field['slug']]['name'],
							'params' => array(
								'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
								'field_name' => $this->slug."[".$sub_field['slug']."]",
								'field_value' => $current_values[$this->slug][$sub_field['slug']],
								'is_main_field' => $sub_field['is_main_field']
							)
						);
						break;
					case 'textarea':
						$field = array(
							'type'   => 'textarea',
							'label'  => $this->fields[$sub_field['slug']]['name'],
							'params' => array(
								'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
								'field_name' => $this->slug."[".$sub_field['slug']."]",
								'field_value' => $current_values[$this->slug][$sub_field['slug']],
								'is_main_field' => $sub_field['is_main_field']
							)
						);
						break;
					case 'image':
						$field = array(
							'type'   => 'image',
							'label'  => $this->fields[$sub_field['slug']]['name'],
							'params' => array(
								'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
								'field_name' => $this->slug."[".$sub_field['slug']."]",
								'field_value' => $current_values[$this->slug][$sub_field['slug']],
								'download_image' => $current_values[$this->slug]['download_image'][$sub_field['slug']],
								'field_key' => $sub_field['slug'],
								'addon_prefix' => $this->slug,
								'is_main_field' => $sub_field['is_main_field']
							)
						);
						break;
					case 'radio':
						$field = array(
							'type'   => 'enum',
							'label'  => $this->fields[$sub_field['slug']]['name'],
							'params' => array(
								'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
								'field_name' => $this->slug."[".$sub_field['slug']."]",
								'field_value' => $current_values[$this->slug][$sub_field['slug']],
								'enum_values' => $this->fields[$sub_field['slug']]['enum_values'],
								'mapping' => true,
								'field_key' => $sub_field['slug'],
								'mapping_rules' => $current_values[$this->slug]['mapping'][$sub_field['slug']],
								'xpath' => $current_values[$this->slug]['xpaths'][$sub_field['slug']],
								'addon_prefix' => $this->slug,
								'sub_fields' => $this->get_sub_fields($this->fields[$sub_field['slug']], $sub_field['slug'], $current_values),
								'is_main_field' => $sub_field['is_main_field']
							)
						);
						break;
					case 'accordion':
						$field = array(
							'type'   => 'accordion',
							'label'  => $this->fields[$sub_field['slug']]['name'],
							'params' => array(
								'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
								'field_name' => $this->slug."[".$sub_field['slug']."]",																
								'field_key' => $sub_field['slug'],								
								'addon_prefix' => $this->slug,
								'sub_fields' => $this->get_sub_fields($this->fields[$sub_field['slug']], $sub_field['slug'], $current_values),
								'in_the_bottom' => false
							)
						);						
						break;
					default:
						# code...
						break;
				}
				return $field;
			}

			

		/* Get values of the add-ons fields for use in the metabox */
		
		function helper_current_field_values($default = array()) {

			if (empty($default)){

				$options = array(
					'mapping' => array(),
					'xpaths' => array()
				);

				foreach ($this->fields as $field_slug => $field_params) {
					$options[$field_slug] = '';
					if (!empty($field_params['enum_values'])){
						foreach ($field_params['enum_values'] as $key => $value) {
							$options[$field_slug] = $key;
							break;
						}
					}
				}

				$default = array($this->slug => $options);				

			}			

			$input = new PMXI_Input();

			$id = $input->get('id');

			$import = new PMXI_Import_Record();			
			if ( ! $id or $import->getById($id)->isEmpty()) { // specified import is not found
				$post = $input->post(			
					$default			
				);
			}
			else {
				$post = $input->post(
					$import->options
					+ $default			
				);		
			}
			
			$is_loaded_template = (!empty(PMXI_Plugin::$session->is_loaded_template)) ? PMXI_Plugin::$session->is_loaded_template : false;		

			$load_options = $input->post('load_template');

			if ($load_options) { // init form with template selected
				
				$template = new PMXI_Template_Record();
				if ( ! $template->getById($is_loaded_template)->isEmpty()) {	
					$post = (!empty($template->options) ? $template->options : array()) + $default;				
				}
				
			} elseif ($load_options == -1){
				
				$post = $default;
								
			}

			return $post;

		}

		/**
		* 
		* Add accordion options
		*
		*
		*/
		function add_options( $main_field = false, $title = '', $fields = array() ){
			
			if ( ! empty($fields) )
			{				
				
				if ($main_field){

					$main_field['is_main_field'] = true;
					$fields[] = $main_field;

				}

				return $this->add_field('accordion_' . $fields[0]['slug'], $title, 'accordion', $fields);							
			
			}

		}			

		function add_title($title = '', $tooltip = ''){

			if (empty($title)) return;

			return $this->add_field(sanitize_key($title) . time(), $title, 'title', null, $tooltip);			

		}		

		function add_text($text = ''){

			if (empty($text)) return;

			return $this->add_field(sanitize_key($text) . time(), $text, 'plain_text');			

		}			

		function helper_metabox_top($name) {

			return '
			<style type="text/css">
				.wpallimport-plugin .wpallimport-addon div.input {
					margin-bottom: 15px;
				}
				.wpallimport-plugin .wpallimport-addon .custom-params tr td.action{
					width: auto !important;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-custom-fields-actions{
					right:0 !important;
				}
				.wpallimport-plugin .wpallimport-addon table tr td.wpallimport-enum-input-wrapper{
					width: 80%;
				}
				.wpallimport-plugin .wpallimport-addon table tr td.wpallimport-enum-input-wrapper input{
					width: 100%;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-custom-fields-actions{
					float: right;	
					right: 30px;
					position: relative;				
					border: 1px solid #ddd;
					margin-bottom: 10px;
				}
				
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options {
					margin-bottom: 15px;				
					margin-top: -16px;	
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-content-section{
					padding-bottom: 8px;
					margin:0; 
					border: none;
					padding-top: 1px;
					background: #f1f2f2;				
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-collapsed-header{
					padding-left: 13px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-collapsed-header h3{
					font-size: 14px;
					margin: 6px 0;
				}

				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width{
					bottom: -40px;
					margin-bottom: 0;
					margin-left: -25px;
					margin-right: -25px;
					position: relative;						
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width .wpallimport-content-section{					
					margin:0; 					
					border-top:1px solid #ddd; 
					border-bottom: none; 
					border-right: none; 
					border-left: none; 
					background: #f1f2f2;									
				}					
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width .wpallimport-collapsed-header h3{					
					margin: 14px 0;
				}

				.wpallimport-plugin .wpallimport-addon .wpallimport-dependent-options{
					margin-left: 1px;
					margin-right: -1px;					
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-dependent-options .wpallimport-content-section{
					border: 1px solid #ddd;
					border-top: none;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-bottom{
					margin-left: -25px; 
					margin-right: -25px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-not-bottom{
					margin: 25px -1px 25px 1px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-not-bottom .wpallimport-content-section{
					border: 1px solid #ddd;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-add-on-options-title{
					font-size: 14px;
  					margin: 45px 0 15px 0;
				}
			</style>
			<div class="wpallimport-collapsed wpallimport-section wpallimport-addon '.$this->slug.' closed">
				<div class="wpallimport-content-section">
					<div class="wpallimport-collapsed-header">
						<h3>'.__($name,'pmxi_plugin').'</h3>	
					</div>
					<div class="wpallimport-collapsed-content" style="padding: 0;">
						<div class="wpallimport-collapsed-content-inner">
							<table class="form-table" style="max-width:none;">
								<tr>
									<td colspan="3">';
		}

		function helper_metabox_bottom() {

			return '				</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>';

		}

		/**
		*
		* simply add an additional section for attachments
		*
		*/
		function import_files( $slug, $title ){
			$this->import_images( $slug, $title, 'files');
		}

		/**
		*
		* simply add an additional section 
		*
		*/
		function import_images( $slug, $title, $type = 'images' ){
			
			if ( empty($title) or empty($slug) ) return;

			$section_slug = 'pmxi_' . $slug; 

			$this->image_sections[] = array(
				'title' => $title,
				'slug'  => $section_slug,
				'type'  => $type
			);			
			
			foreach ($this->image_options as $option_slug => $value) {
				$this->add_option($section_slug . $option_slug, $value);
			}

			if (count($this->image_sections) > 1){
				add_filter('wp_all_import_is_show_add_new_images', array($this, 'filter_is_show_add_new_images'), 10, 2);
			}

			add_filter('wp_all_import_is_allow_import_images', array($this, 'is_allow_import_images'), 10, 2);			
			
			if (function_exists($slug)) add_action( $section_slug, $slug, 10, 4);
		}			
			/**
			*
			* filter to allow import images for free edition of WP All Import
			*
			*/
			function is_allow_import_images($is_allow, $post_type){
				return ($this->is_active_addon($post_type)) ? true : $is_allow;
			}

		/**
		*
		* filter to control additional images sections
		*
		*/
		function additional_sections($sections){
			if ( ! empty($this->image_sections) ){
				foreach ($this->image_sections as $add_section) {
					$sections[] = $add_section;
				}
			}
			
			return $sections;
		}
			/**
			*
			* remove the 'Don't touch existing images, append new images' when more than one image section is in use.
			*
			*/
			function filter_is_show_add_new_images($is_show, $post_type){
				return ($this->is_active_addon($post_type)) ? false : $is_show;
			}

		/**
		*
		* disable the default images section
		*
		*/		
		function disable_default_images($post_type = false){
									
			add_filter('wp_all_import_is_images_section_enabled', array($this, 'is_enable_default_images_section'), 10, 2);

		}
			function is_enable_default_images_section($is_enabled, $post_type){						
				
				return ($this->is_active_addon($post_type)) ? false : true;
								
			}

		function helper_parse($parsingData, $options) {

			extract($parsingData);

			$data = array(); // parsed data

			if ( ! empty($import->options[$this->slug])){

				$this->logger = $parsingData['logger'];

				$cxpath = $xpath_prefix . $import->xpath;

				$tmp_files = array();

				foreach ($options[$this->slug] as $option_name => $option_value) {

					if ( ! empty($import->options[$this->slug][$option_name]) ) {

						if ($import->options[$this->slug][$option_name] == "xpath") {
							if ($import->options[$this->slug]['xpaths'][$option_name] == ""){
								$count and $this->data[$option_name] = array_fill(0, $count, "");
							} else {
								$data[$option_name] = XmlImportParser::factory($xml, $cxpath, $import->options[$this->slug]['xpaths'][$option_name], $file)->parse($records);
								$tmp_files[] = $file;						
							}
						} else {
							$data[$option_name] = XmlImportParser::factory($xml, $cxpath, $import->options[$this->slug][$option_name], $file)->parse();
							$tmp_files[] = $file;
						}


					} else {
						$data[$option_name] = array_fill(0, $count, "");
					}

				}

				foreach ($tmp_files as $file) { // remove all temporary files created
					unlink($file);
				}

			}

			return $data;
		}


		function can_update_meta($meta_key, $import_options) {

			//echo "<pre>";
			//print_r($import_options['options']);
			//echo "</pre>";
			
			$import_options = $import_options['options'];

			if ($import_options['update_all_data'] == 'yes') return true;

			if ( ! $import_options['is_update_custom_fields'] ) return false;			

			if ($import_options['update_custom_fields_logic'] == "full_update") return true;
			if ($import_options['update_custom_fields_logic'] == "only" and ! empty($import_options['custom_fields_list']) and is_array($import_options['custom_fields_list']) and in_array($meta_key, $import_options['custom_fields_list']) ) return true;
			if ($import_options['update_custom_fields_logic'] == "all_except" and ( empty($import_options['custom_fields_list']) or ! in_array($meta_key, $import_options['custom_fields_list']) )) return true;

			return false;

		}

		function can_update_image($import_options) {

			$import_options = $import_options['options'];

			if ($import_options['update_all_data'] == 'yes') return true;

			if (!$import_options['is_update_images']) return false;			

			if ($import_options['is_update_images']) return true;			

			return false;
		}


		function admin_notice_ignore() {
			if (isset($_GET[$this->slug.'_ignore']) && '0' == $_GET[$this->slug.'_ignore'] ) {
				update_option($this->slug.'_ignore', 'true');
			}
		}

		function display_admin_notice() {


			if ($this->notice_text) {
				$notice_text = $this->notice_text;
			} else {
				$notice_text = $this->name.' requires WP All Import <a href="http://www.wpallimport.com/" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>.';
			}

			if (!get_option($this->slug.'_ignore')) {

				?>

	    		<div class="error">
	    		    <p><?php _e(
		    		    	sprintf(
	    			    		$notice_text.' | <a href="%1$s">Hide Notice</a>',
	    			    		'?'.$this->slug.'_ignore=0'
	    			    	), 
	    		    		'rapid_addon_'.$this->slug
	    		    	); ?></p>
			    </div>

				<?php

			}

		}

		/*
		*
		* $conditions - array('themes' => array('Realia'), 'plugins' => array('plugin-directory/plugin-file.php', 'plugin-directory2/plugin-file.php')) 
		*
		*/
		function admin_notice($notice_text = '', $conditions = array()) {

			$is_show_notice = false;

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( ! is_plugin_active('wp-all-import-pro/wp-all-import-pro.php') and ! is_plugin_active('wp-all-import/plugin.php') ){
				$is_show_notice = true;
			}

			// Supported Themes
			if ( ! $is_show_notice and ! empty($conditions['themes']) ){

				$themeInfo    = wp_get_theme();
				$currentTheme = $themeInfo->get('Name');
				
				$is_show_notice = in_array($currentTheme, $conditions['themes']) ? false : true;				

			}

			// Required Plugins
			if ( ! $is_show_notice and ! empty($conditions['plugins']) ){				

				$requires_counter = 0;
				foreach ($conditions['plugins'] as $plugin) {
					if ( is_plugin_active($plugin) ) $requires_counter++;
				}

				if ($requires_counter != count($conditions['plugins'])){ 					
					$is_show_notice = true;			
				}

			}

			if ( $is_show_notice ){

				if ( $notice_text != '' ) {
					$this->notice_text = $notice_text;
				}

				add_action('admin_notices', array($this, 'display_admin_notice'));
			}

		}

		function log( $m = false){		

			$m and $this->logger and call_user_func($this->logger, $m);

		}
	}	

}




?>