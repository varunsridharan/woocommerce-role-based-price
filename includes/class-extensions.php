<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Role_Based_Price_Extensions {
    
    public function __construct() {
    	add_action(WC_RBP_DB.'_form_fields',array($this,'list_addons'),10,2);
    }
	
	public function list_addons($none,$form_id){
		if($form_id != 'addons'){return;}
		$this->plugins_data = $this->get_plugins();
		$this->generate_view();
	}
	
	
	
	public function generate_view(){
		include(WC_RBP_ADMIN.'views/addons-header.php');
		//sanitize_html_class
		foreach($this->plugins_data as $addon_slug => $data){
			global $wc_rbp_plugin_data;
			$wc_rbp_plugin_data = $data; 
			include(WC_RBP_ADMIN.'views/addons-single.php');
			unset($wc_rbp_plugin_data);
		}
		
		
		include(WC_RBP_ADMIN.'views/addons-footer.php');
	}
	
	
	
	
	
	/**
	 * Check the plugins directory and retrieve all plugin files with plugin data.
	 * The file with the plugin data is the file that will be included and therefore
	 * needs to have the main execution for the plugin. This does not mean
	 * everything must be contained in the file and it is recommended that the file
	 * be split for maintainability. Keep everything in one file for extreme
	 * optimization purposes.
	 * @since 1.5.0
	 * @param string $plugin_folder Optional. Relative path to single plugin folder.
	 * @return array Key is the plugin file path and the value is an array of the plugin data.
	 */
	function get_plugins($plugin_folder = '') {
		$wp_plugins = array ();
		$plugin_root = WC_RBP_PLUGIN;
		if ( !empty($plugin_folder) ){ $plugin_root = $plugin_folder;}
		$plugins_dir = @ opendir( $plugin_root);
		$plugin_files = array();

		if ( $plugins_dir ) {
			while (($file = readdir( $plugins_dir ) ) !== false ) {
				if ( substr($file, 0, 1) == '.' ) {	continue;}
				if ( is_dir( $plugin_root.'/'.$file ) ) {
					$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
					if ( $plugins_subdir ) {
						while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' ) {continue;}
							if ( substr($subfile, -4) == '.php' ) {$plugin_files[] = "$file/$subfile";}
						}
						closedir( $plugins_subdir );
					}
				} else {
					if ( substr($file, -4) == '.php' ) {$plugin_files[] = $file;}
				}
			}
			closedir( $plugins_dir );
		}

		if ( empty($plugin_files) ) {return $wp_plugins;}
		foreach ( $plugin_files as $plugin_file ) {
			if ( !is_readable( "$plugin_root/$plugin_file" ) ) {continue;}
			$plugin_data = $this->get_plugin_data( "$plugin_root/$plugin_file", false, false ); 
			if ( empty ( $plugin_data['name'] ) ) { continue;} 
			$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
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
	 *	* Plugin Name: Name of the plugin
	 *	* Plugin Icon: (Icon URL / DATA Code)
	 *	* Description: some Description about plugin
	 *	* Version: 01
	 *	* Author: Author Name
	 *	* Author URL: Author URL
	 *	* Last Update: YYYY-MM-DD
	 *	* Required Plugins: plugin-folder/plugin-file.php | Version , plugin-folder/plugin-file.php | 2.0*     /*
	 *    
	 * Some users have issues with opening large files and manipulating the contents
	 * for want is usually the first 1kiB or 2kiB. This function stops pulling in
	 * the plugin contents when it has all of the required plugin data.
	 * The first 8kiB of the file will be pulled in and if the plugin data is not
	 * within that first 8kiB, then the plugin author should correct their plugin
	 * and move the plugin data headers to the top.
	 * The plugin file is assumed to have permissions to allow for scripts to read
	 * the file. This is not checked however and the file is only opened for
	 * reading.
	 *
	 * @since 3.0
	 * @param string $plugin_file Path to the plugin file
	 * @param bool   $markup      Optional. If the returned data should have HTML markup applied. Default true.
	 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
	 */
	function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
		$default_headers = array(
			'name' => 'Plugin Name',
			'icon' => 'Plugin Icon',
			'decs' => 'Description',
			'version' => 'Version',
			'author' => 'Author',
			'author_link' => 'Author URL',
			'last_update' => 'Last Update',
			'rplugins' => 'Required Plugins',
		);

		$plugin_data = get_file_data( $plugin_file, $default_headers, 'wc_rbp_plugin' );
		if ( $markup || $translate ) {
			$plugin_data = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
		} else {
			$plugin_data['Title']      = $plugin_data['name'];
			$plugin_data['AuthorName'] = $plugin_data['author'];
		}
		return $plugin_data;
	}
	
	
}
?>