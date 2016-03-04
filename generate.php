<?php 
if(isset($_REQUEST['change'])){
	$files_check = array();
	get_php_files(__DIR__);
	foreach ($files_check as $f){
		$file = file_get_contents($f);
		
		
		$file = str_replace('WooCommerce Plugin Boiler Plate','WooCommerce Role Based Price',$file);
		$file = str_replace('woocommerce-plugin-boiler-plate','woocommerce-role-based-price',$file);
		$file = str_replace('WooCommerce_Plugin_Boiler_Plate','WooCommerce_Role_Based_Price',$file);
		//$file = str_replace('https://wordpress.org/plugins/woocommerce-plugin-boiler-plate/', '' , $file ); 
		$file = str_replace('[version]', '3.0' , $file ); 
		$file = str_replace('[package]', 'WooCommerce Role Based Price' , $file ); 
		$file = str_replace('[plugin_name]', 'WooCommerce Role Based Price' , $file ); 
		$file = str_replace('[plugin_url]', 'https://wordpress.org/plugins/woocommerce-role-based-price/' , $file ); 
		$file = str_replace('wc_pbp_','wc_rbp',$file);
		$file = str_replace('PLUGIN_FILE', 'WC_RBP_FILE' , $file);
		$file = str_replace('PLUGIN_PATH', 'WC_RBP_PATH' , $file);
		$file = str_replace('PLUGIN_INC', 'WC_RBP_INC' , $file);
		$file = str_replace('PLUGIN_DEPEN', 'WC_RBP_DEPEN' , $file);
		$file = str_replace('PLUGIN_NAME', 'WC_RBP_NAME' , $file);
		$file = str_replace('PLUGIN_SLUG', 'WC_RBP_SLUG' , $file);
		$file = str_replace('PLUGIN_TXT', 'WC_RBP_TXT' , $file);
		$file = str_replace('PLUGIN_DB', 'WC_RBP_DB' , $file);
		$file = str_replace('PLUGIN_V', 'WC_RBP_V' , $file);
		$file = str_replace('PLUGIN_LANGUAGE_PATH', 'WC_RBP_LANGUAGE_PATH' , $file);
		$file = str_replace('PLUGIN_ADMIN', 'WC_RBP_ADMIN' , $file);
		$file = str_replace('PLUGIN_SETTINGS', 'WC_RBP_SETTINGS' , $file);
		$file = str_replace('PLUGIN_URL', 'WC_RBP_URL' , $file);
		$file = str_replace('PLUGIN_CSS', 'WC_RBP_CSS' , $file);
		$file = str_replace('PLUGIN_IMG', 'WC_RBP_IMG' , $file);
		$file = str_replace('PLUGIN_JS', 'WC_RBP_JS' , $file);		
		
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


