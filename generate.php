<?php
if(isset($_REQUEST['makePOT'])){
	$current_dir = __DIR__;
	$file_name = basename($current_dir);
	$lang_dir = $current_dir."/$file_name.pot";
	$php_path = 'C:\xampp\php\php.exe';
	$makePotFile = 'C:\xampp\htdocs\wptools\makepot.php';
	$project = 'wp-plugin';
	exec($php_path. ' '.$makePotFile.' '.$project.' '.$current_dir.' '.$lang_dir);
}


if(isset($_REQUEST['change'])){
	$files_check = array();
	get_php_files(__DIR__);
	foreach ($files_check as $f){
		$file = file_get_contents($f);
		$file = str_replace('WooCommerce_Plugin_Boiler_Plate', 'Advanced_Product_Reviews_WooCommerce', $file);
		$file = str_replace('WooCommerce Plugin Boiler Plate', 'Advanced Product Reviews WooCommerce', $file);
		$file = str_replace('woocommerce-plugin-boiler-plate', 'advanced-product-reviews-wooCommerce', $file);
		$file = str_replace('PLUGIN_NAME', 'APR_NAME', $file);
		$file = str_replace('PLUGIN_SLUG', 'APR_SLUG', $file);
		$file = str_replace('PLUGIN_TXT', 'APR_TXT', $file);
		$file = str_replace('PLUGIN_DB', 'APR_DB', $file);
		$file = str_replace('PLUGIN_V', 'APR_V', $file);
		$file = str_replace('PLUGIN_PATH', 'APR_PATH', $file);
		$file = str_replace('PLUGIN_LANGUAGE_PATH', 'APR_LANGUAGE_PATH', $file);
		$file = str_replace('PLUGIN_INC', 'APR_INC', $file);
		$file = str_replace('PLUGIN_ADMIN', 'APR_ADMIN', $file);
		$file = str_replace('PLUGIN_SETTINGS', 'APR_SETTINGS', $file);
		$file = str_replace('PLUGIN_URL', 'APR_URL', $file);
		$file = str_replace('PLUGIN_CSS', 'APR_CSS', $file);
		$file = str_replace('PLUGIN_IMG', 'APR_IMG', $file);
		$file = str_replace('PLUGIN_JS', 'APR_JS', $file);
		$file = str_replace('PLUGIN_FILE', 'APR_FILE', $file);
		$file = str_replace('wc_pbp', 'apr_wc', $file);		
		file_put_contents($f,$file); 
	}
}


function get_php_files($dir = __DIR__){
	global $files_check;
	$files = scandir($dir); 
	foreach($files as $file) {
		if($file == '' || $file == '.' || $file == '..' ){continue;}
		if(is_dir($dir.'/'.$file)){
			get_php_files($dir.'/'.$file);
		} else {
			if(pathinfo($dir.'/'.$file, PATHINFO_EXTENSION) == 'php' || pathinfo($dir.'/'.$file, PATHINFO_EXTENSION) == 'txt'){
				if($file == 'generate.php'){continue;}
				$files_check[$file] = $dir.'/'.$file;
			}
		}
	}
}
?>