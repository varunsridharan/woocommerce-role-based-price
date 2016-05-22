<?php 

if (class_exists('WP_All_Import_WooCommerce_Addon_Extender')) { return;}

class WP_All_Import_WooCommerce_Addon_Extender extends RapidAddon {

	public function __construct($name, $slug) { 
		parent::__construct($name, $slug);
	}


	public function add_wp_all_import_tab(){
		echo '<li class="options_tab '.$this->slug.'"> <a title="'.$this->name.'" href="javascript:void(0);" rel="'.$this->slug.'">'.$this->name.'</a></li>';
	}

	/**
	 * 
	 * Add-On Initialization
	 *
	 * @param array $conditions - list of supported themes and post types
	 *
	*/
	function run($conditions = array()) {
		if (empty($conditions)) { $this->when_to_run = "always"; }
		@$this->active_post_types = ( ! empty($conditions['post_types'])) ? $conditions['post_types'] : array();
		@$this->active_themes = ( ! empty($conditions['themes'])) ? $conditions['themes'] : array();
		@$this->active_plugins = ( ! empty($conditions['plugins'])) ? $conditions['plugins'] : array();			
		add_filter('pmxi_addons', array($this, 'wpai_api_register'));
		add_filter('wp_all_import_addon_parse', array($this, 'wpai_api_parse'));
		add_filter('wp_all_import_addon_import', array($this, 'wpai_api_import'));
		add_filter('wp_all_import_addon_saved_post', array($this, 'wpai_api_post_saved'));
		add_filter('pmxi_options_options', array($this, 'wpai_api_options'));
		add_filter('wp_all_import_image_sections', array($this, 'additional_sections'), 10, 1);
		add_action('pmwi_tab_header',array($this,'add_wp_all_import_tab'));
		add_action('pmwi_tab_content',array($this,'add_wp_all_import_page'));
		add_action('pmxi_extend_options_main',array($this,'get_template_fields'),10,2);
		add_action('admin_init', array($this, 'admin_notice_ignore'));			
	}	
	
	public function get_template_fields($ptype,$post){
        $this->ptype = $ptype;
        $this->tpost = $post;
    }
	
	public function add_wp_all_import_page(){
        $this->wpai_api_metabox($this->ptype,$this->tpost);
    }
 
	public function helper_metabox_top($name) {
			return ' <style type="text/css">  
			/* .wcrbp_pricing .form-field{
			display: inline-block;
    padding-left: 0 !important;
    width: 49%;
	}*/
			.wcrbp_pricing .input {
    padding: 0 !important;
}
.wcrbp_pricing h3 {
    margin: 0.3em 0;
}
.wcrbp_pricing .rad4 {
    padding-bottom: 5px;
	margin: 10px 0 !important;
}
.wcrbp_pricing .wpallimport-collapsed-content > div {
    padding: 15px 10px;
}
/*
.wcrbp_pricing .form-field label {
    display: block;
    font-weight: bold;
    text-align: center;
    width: calc(100% - 25%);
}*/

.options_group.wcrbp_pricing {
    display: block !important;
    padding: 10px !important;
    width: auto !important;
}
</style>
			<div class="panel woocommerce_options_panel" id="'.$this->slug.'" style="display:none;">
			<div class="options_group wcrbp_pricing">';
		}
	public function helper_metabox_bottom() {
		return '
		</div>
	</div>';

	}	

	public function wpai_api_metabox($post_type, $current_values) {
	    $visible_fields = 0; $counter = 0;
		
		echo $this->helper_metabox_top($this->name);

		foreach ($this->fields as $field_slug => $field_params) {
			if ($field_params['is_sub_field']) continue;
			$visible_fields++;
		}

		foreach ($this->fields as $field_slug => $field_params) {				
			if ($field_params['is_sub_field']) continue;		
			$counter++;		
			$this->render_field($field_params, $field_slug, $current_values, false);
		}

		echo $this->helper_metabox_bottom();

		if ( ! empty($this->image_sections) ){	
			$is_images_section_enabled = apply_filters('wp_all_import_is_images_section_enabled', true, $post_type);
			foreach ($this->image_sections as $k => $section) {
				$section_options = array();
				foreach ($this->image_options as $slug => $value) {
					$section_options[$section['slug'] . $slug] = $value;
				}										
				
				if ( ! $is_images_section_enabled and ! $k ){
					$section_options[$section['slug'] . 'is_featured'] = 1;
				}
				
				PMXI_API::add_additional_images_section($section['title'], $section['slug'], $current_values, '', true, false, $section['type']);
			}
		}

	}		
	

	public function render_field($field_params, $field_slug, $current_values, $in_bottom = false){
		
		if($field_params['type'] != 'accordion'){parent::render_field($field_params, $field_slug, $current_values, $in_bottom);}
		$field_params['tooltip'] = $field_params['tooltip'];
		$field_params['field_name'] = $this->slug.$field_slug;
		$field_params['field_key'] = $field_slug;							
		$field_params['addon_prefix'] = $this->slug;
		if(!isset($field_params['sub_fields'])){
			$field_params['sub_fields'] = $this->get_sub_fields($field_params, $field_slug, $current_values);	
		}
		
		$field_params['in_the_bottom'] = $in_the_bottom;
		
		?>
		<div class="input">
		<div class="wpallimport-collapsed closed wpallimport-sub-options wpallimport-full-with-not-bottom">
		<div class="wpallimport-content-section rad4">
			<div class="wpallimport-collapsed-header"> <h3 style="color:#40acad;"><?php echo $field_params['name']; ?></h3> </div>
			<div class="wpallimport-collapsed-content" style="padding: 0;">
				<div class="">

					<?php
		
					if ( ! empty($field_params['sub_fields']) ){
						
						$html = '';
						foreach ($field_params['sub_fields'] as $f) {

							if($f[0]['type'] == 'accordion'){
								$f[0]['params']['type'] = $f[0]['type'];
								$f[0]['params']['label'] = $f[0]['label'];
								$f[0]['params']['name'] = $f[0]['label'];
								
								$this->render_field($f[0]['params'],$f[0]['params']['field_key'], $f[0]['params']['label']);
							} else {
  
							$html .= '<p class="form-field" style="">';
							$html .= '<label for="'.$f[0]['params']['field_name'].'" >'.$f[0]['label'].'</label>';
								 
							$html .= '<input type="text" value="'.$f[0]['params']['field_value'].'"  class="short" id="'.$f[0]['params']['field_name'].'" name="'.$f[0]['params']['field_name'].'"/>';
								
							if(!empty($f[0]['params']['tooltip'])){
								$html .= '<span style="font-size: 11px ! important; clear: both; font-weight: bold; display: inline-block ! important; width: auto; margin: 10px auto ! important;">'.$f[0]['params']['tooltip'].'</span>';
							}
							$html .= '</p>';
									 
								
							} 
						}
						echo $html;
						
					}
					?>

				</div>
			</div>
			</div>
		</div>
		</div>
	<?php 
	}
}
?>