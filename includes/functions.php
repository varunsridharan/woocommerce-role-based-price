<?php
/**
 * Common Plugin Functions
 * 
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/core
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

global $wc_rbp_db_settins_values;
$wc_rbp_db_settins_values = array();
add_action('wc_rbp_loaded','wc_rbp_get_settings_from_db');


if(!function_exists('wc_rbp_option')){
	function wc_rbp_option($key = '',$return_failure = ''){
		global $wc_rbp_db_settins_values;
		if($key == ''){return $wc_rbp_db_settins_values;}
        
		if(isset($wc_rbp_db_settins_values[WC_RBP_DB.$key])){
			return $wc_rbp_db_settins_values[WC_RBP_DB.$key];
		} 
		
		return $return_failure;
	}

}

if(!function_exists('wc_rbp_get_settings_from_db')){
	/**
	 * Retrives All Plugin Options From DB
	 */
	function wc_rbp_get_settings_from_db(){
		global $wc_rbp_db_settins_values;
		$section = array();
		$section = apply_filters('wc_rbp_settings_section',$section);
		$values = array();

		foreach($section as $settings){
			foreach($settings as $set){
				$db_val = get_option(WC_RBP_DB.$set['id']);
				if(is_array($db_val)){ unset($db_val['section_id']); $values = array_merge($db_val,$values); }
			}
		}        
		$wc_rbp_db_settins_values = $values;
	}
}

if(!function_exists('wc_rbp_is_request')){
    /**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
    function wc_rbp_is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }
}

if(!function_exists('wc_rbp_current_screen')){
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    function wc_rbp_current_screen(){ 
       $screen =  get_current_screen();
       return $screen->id;
    }
}

if(!function_exists('wc_rbp_get_screen_ids')){
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    function wc_rbp_get_screen_ids(){
        $screen_ids = array();
		$screen_ids[] = 'woocommerce_page_woocommerce-role-based-price-settings';
        return $screen_ids;
    }
}

if(!function_exists('wc_rbp_dependency_message')){
	/**
	 * Returns the plugin dependency message
	 */
	function wc_rbp_dependency_message(){
		$text = __( WC_RBP_NAME . ' requires <b> WooCommerce </b> To Be Installed..  <br/> <i>Plugin Deactivated</i> ', WC_RBP_TXT);
		return $text;
	}
}

if(!function_exists('wc_rbp_get_settings_sample')){
	/**
	 * Retunrs the sample array of the settings framework
	 * @param [string] [$type = 'page' | 'section' | 'field'] [[Description]]
	 */
	function wc_rbp_get_settings_sample($type = 'page'){
		$return = array();
		
		if($type == 'page'){
			$return = array( 
				'id'=>'settings_general', 
				'slug'=>'general', 
				'title'=>__('General',WC_RBP_TXT),
				'multiform' => 'false / true',
				'submit' => array( 
					'text' => __('Save Changes',WC_RBP_TXT), 
					'type' => 'primary / secondary / delete', 
					'name' => 'submit'
				)
			);
			
		} else if($type == 'section'){
			$return['page_id'][] = array(
				'id'=>'general',
				'title'=>'general', 
				'desc' => 'general',
				'submit' => array(
					'text' => __('Save Changes',WC_RBP_TXT), 
					'type' => 'primary / secondary / delete', 
					'name' => 'submit'
				)
			);
		} else if($type == 'field'){
			$return['page_id']['section_id'][] = array(
				'id' => '',
				'type' => 'text, textarea, checkbox, multicheckbox, radio, select, field_row, extra',
				'label' => '',
				'options' => 'Only required for type radio, select, multicheckbox [KEY Value Pair]',
				'desc' => '',
				'size' => '',
				'default' => '',
				'attr' => "Key Value Pair",
				'before' => 'Content before the field label',
				'after' => 'Content after the field label',
				'content' => 'Content used for type extra' ,
				'text_type' => "Set the type for text input field (e.g. 'hidden' )",
			);
		}
	}
}

if(!function_exists('wc_rbp_get_edit_button')){
    function wc_rbp_get_edit_button($post_ID,$type = 'simple',$args = array()){
        
        $default_args = array(
            'attrs' => '',
            'text' => 'Add / Edit Role Pricing',
            'button_type' => 'button',
            'action' => 'wc_rbp_product_editor',
            'tag' => 'button',
            'class' => 'button button-primary',
            'id' => '',
        );
        $args = wp_parse_args( $args, $default_args );
        extract($args);
        
        $return = '<'.$tag.' id="'.$id.'" type="'.$button_type.'" class=" '.$class.' wc_rbp_product_editor_btn"  ';
        $return .= ' data-href="'.admin_url('admin-ajax.php?action='.$action.'&type='.$type.'&post_id='.$post_ID).'" ';
        $return .= $attrs;
        $return .= '>'.__($text,WC_RBP_TXT).' </'.$tag.'>';
        return $return;
    }   
}

if(!function_exists('wc_rbp_get_tab_pos')){
    function wc_rbp_get_tab_pos($tab_pos = ''){
        $horizontalPosition = '';
        $verticalPosition = '';

        if($tab_pos == 'horizontal_top'){ $tab_pos  = 'horizontal'; $horizontalPosition  = 'top';  }
        else if($tab_pos == 'horizontal_bottom'){ $tab_pos  = 'horizontal'; $horizontalPosition  = 'bottom'; }
        else if($tab_pos == 'vertical_left'){ $tab_pos  = 'vertical'; $verticalPosition = 'left'; }
        else if($tab_pos == 'vertical_right'){ $tab_pos  = 'vertical'; $verticalPosition = 'right';}
        return array('horizontalPosition' => $horizontalPosition, 'verticalPosition' => $verticalPosition,'tab_pos' => $tab_pos);
    }
}


if(!function_exists('wc_rbp_custom_wp_user_roles')){
    function wc_rbp_custom_wp_user_roles(){ 
        $all_roles = array();
        if(function_exists('wp_roles')){ $all_roles = wp_roles()->roles;  }
        return $all_roles;
    }
}

if(!function_exists('wc_rbp_get_wp_roles')){
	/**
	 * Returns Registered WP User Roles
	 * @return [[Type]] [[Description]]
	 */
	function wc_rbp_get_wp_roles(){
		$user_roles = wc_rbp_custom_wp_user_roles();
		$user_roles['logedout'] = array('name' => __('Visitor / LogedOut User',WC_RBP_TXT));  
		$user_roles = apply_filters('wc_rbp_wp_user_roles',$user_roles);
		return $user_roles;
	}

}

if(!function_exists('wc_rbp_get_template')){
	function wc_rbp_get_template($name,$args = array(),$main_path = '',$theme_path = 'woocommerce'){ 
        if(empty($main_path)){$main_path = WC_RBP_PATH.'/templates/';}
        ob_start();
		wc_get_template( $name, $args ,$theme_path, $main_path);
        $return_value = ob_get_clean(); 
        ob_flush();
        
        return $return_value;
	}
}

if(!function_exists('wc_rbp_get_user_roles_selectbox')){
	function wc_rbp_get_user_roles_selectbox(){
		$user_roles = wc_rbp_get_wp_roles();
		$list_roles = array();
		$roles = array_keys($user_roles);
		foreach($roles as $role){
			$list_roles[$role] = $user_roles[$role]['name'];
		}
		return $list_roles;
	}

}

if(!function_exists('wc_rbp_get_current_user')){
	/**
	 * Gets Current Logged in User Role / User Object.
	 * returns user role if $userroleonly set to true
	 * or returns the user object
	 * @param  [boolean] [$userroleonly = true / false]
	 * @return [string / object]
	 */
	function wc_rbp_get_current_user($userroleonly = true){
		global $current_user;
		$user_role = $current_user;
		if($userroleonly){
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
			if($user_role == null){ $user_role = 'logedout'; }
		}
        
		return apply_filters('wc_rbp_active_user',$user_role,$userroleonly);
	}
	
}

if(!function_exists('wc_rbp_get_oppo_metakey')){
    function wc_rbp_get_oppo_metakey($key){
        if($key == 'selling_price'){return 'regular_price';}
        return 'selling_price';
    }
}

if(!function_exists('wc_rbp_get_userrole_by_id')){
    
    /**
     * Get user roles by user ID.
     *
     * @param  int $id
     * @return array
     */
    function wc_rbp_get_userrole_by_id( $id ){
        $user = new WP_User( $id );

        if ( empty ( $user->roles ) or ! is_array( $user->roles ) ) {return '';}
            
        foreach ( $user->roles as $role ) {
            return $role;
        }

        return null;
    }
}

if(!function_exists('wc_rbp_avaiable_price_type')){
	/**
	 * Returns avaiable_price type with label
	 * @return [[Type]] [[Description]]
	 */
	function wc_rbp_avaiable_price_type($key = ''){
		$avaiable_price = array();
		$avaiable_price['regular_price'] = __('Regular Price',WC_RBP_TXT);
		$avaiable_price['selling_price'] = __('Selling Price',WC_RBP_TXT);
		$avaiable_price = apply_filters('wc_rbp_avaiable_price',$avaiable_price);
        
        if(!empty($key)){
            if(isset($avaiable_price[$key])){
                return $avaiable_price[$key];
            }
        }
        
		return $avaiable_price;
	}
} 

if(!function_exists('wc_rbp_price_types')){
    function wc_rbp_price_types($key = ''){
        $price = wc_rbp_avaiable_price_type(); 
        foreach($price as $price_id => $priceVal){
            $lable = wc_rbp_option($price_id.'_label',$priceVal);
            $price[$price_id] = $lable;
        }
        
        if(!empty($key)){
            if(isset($price[$key])){
                return $price[$key];
            }
        }
        
        return $price;
    }
}

 
if(!function_exists('wc_rbp_modal_template')){
    /**
     * returns modal templaate code
     * @param  [[Type]] [$title ='']    [[Description]]
     * @param  [[Type]] [$content = ''] [[Description]]
     * @return string   [[Description]]
     */
    function wc_rbp_modal_template($title ='',$content = ''){
        return '<div class="wc-rbp-modal wc-rbp-modal-ajax" style="display: block;">
            <button type="button" class="close" onclick="Custombox.close();"> <span>&times;</span></button>
            <h4 class="title">'.$title.'</h4>
            <div class="wc-rbp-modal-content text"> '.$content.'</div>
        </div>';
    }
}

if(!function_exists('wc_rbp_modal_header')){
	/**
	 * Includes Ajax Modal Header File
	 */
	function wc_rbp_modal_header(){
		include(WC_RBP_ADMIN.'views/ajax-modal-header.php');
	}
}

if(!function_exists('wc_rbp_modal_footer')){
	/**
	 * Includes Ajax Modal Footer File
	 */
	function wc_rbp_modal_footer(){
		include(WC_RBP_ADMIN.'views/ajax-modal-footer.php');
	}
}

if(!function_exists('wc_rbp_do_settings_sections')){
	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 2.7.0
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output
	 */
	function wc_rbp_do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;
		$section_count = count($wp_settings_sections[$page]);
		if($section_count > 1){
			echo '<ul class="subsubsub wc_rbp_settings_submenu">';
			foreach ( (array) $wp_settings_sections[$page] as $section ) {
				echo '<li> <a href="#'.$section['id'].'">'.$section['title'].'</a> | </li>';
			}	
			echo '</ul> <br/>';
		}
		
		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if($section_count > 1){ echo '<div id="settings_'.$section['id'].'" class="hidden wc_rbp_settings_content">'; }
				if ( $section['title'] )
					echo "<h2>{$section['title']}</h2>\n";

				if ( $section['callback'] )
					call_user_func( $section['callback'], $section );

				if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
					continue;
				echo '<table class="form-table">';
				do_settings_fields( $page, $section['id'] );
				echo '</table>';
			if($section_count > 1){echo '</div>';}
		}
	}
}

if(!function_exists('wc_rbp_get_form_hidden_fields')){
	/**
	 * Returns Required WC RBP hidden Fields
	 */
	
	function wc_rbp_get_form_hidden_fields($action,$wp_nounce_name,$referer = true){
		$return = '<input type="hidden" name="wcrbp-action" value="'.$action.'" />';
		$return .= wp_nonce_field( $action, $wp_nounce_name, $referer, false ) ;
		return $return;
	}
}

if(!function_exists('wc_rbp_get_editor_fields')){
	function wc_rbp_get_editor_fields($type){
        $fields = wc_rbp_get_form_hidden_fields('wc_rbp_save_product_prices','wc_rbp_nounce');
        $fields .= '<input type="hidden" name="type" value="'.$type.'" />';
        return $fields;
	}
}

if(!function_exists('wc_rbp_get_ajax_overlay')){
	/**
	 * Prints WC RBP Ajax Loading Code
	 */
	function wc_rbp_get_ajax_overlay($echo = true){
		$return = '<div class="wc_rbp_ajax_overlay">
		<div class="sk-folding-cube">
		<div class="sk-cube1 sk-cube"></div>
		<div class="sk-cube2 sk-cube"></div>
		<div class="sk-cube4 sk-cube"></div>
		<div class="sk-cube3 sk-cube"></div>
		</div>
		</div>';
		if($echo){echo $return;}
		else{return $return;}
	}
}

if(!function_exists('wc_rbp_check_active_addon')){
	function wc_rbp_check_active_addon($slug){
		$addons = wc_rbp_get_active_addons();
		if(in_array($slug,$addons)){ return true; }
		return false;
	}
}

if(!function_exists('wc_rbp_get_active_addons')){
	/**
	 * Returns Active Addons List
	 * @return [[Type]] [[Description]]
	 */
	function wc_rbp_get_active_addons(){
		$addons = get_option(WC_RBP_DB.'active_addons',array()); 
		return $addons;
	}
}

if(!function_exists('wc_rbp_update_active_addons')){
	/**
	 * Returns Active Addons List
	 * @return [[Type]] [[Description]]
	 */
	function wc_rbp_update_active_addons($addons){
		update_option(WC_RBP_DB.'active_addons',$addons); 
		return true;
	}
}

if(!function_exists('wc_rbp_activate_addon')){
	function wc_rbp_activate_addon($slug){
		$active_list = wc_rbp_get_active_addons();
		if(!in_array($slug,$active_list)){
			$active_list[] = $slug;
			wc_rbp_update_active_addons($active_list);
			return true;
		}
		return false;
	}
}

if(!function_exists('wc_rbp_deactivate_addon')){
	function wc_rbp_deactivate_addon($slug){
		$active_list = wc_rbp_get_active_addons();
		if(in_array($slug,$active_list)){
			$key = array_search($slug, $active_list);
			unset($active_list[$key]);
			wc_rbp_update_active_addons($active_list);
			return true;
		}
		return false;
	}
}

if(!function_exists('wc_rbp_admin_notice')){
    function wc_rbp_admin_notice($msg , $type = 'updated'){
        $notice = ' <div class="'.$type.' settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p>'.$msg.'</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        return $notice;
    }
}

if(!function_exists('wc_rbp_generate_tabs')){
    function wc_rbp_generate_tabs($tabs,$content,$args = array()){
        $default_args = array(
            'show_image' => true,
            'tab_style' => 'left', // 'default', 'box' or 'left'. Optional
            'tab_wrapper' => true,
            
        );
        
        
        $args = wp_parse_args($args,$default_args);
        $wraper_start = '<div class="wcrbp-tabs  wcrbp-tabs-'.$args['tab_style'].'">';
        $wraper_end = '</div>';
        
        $tabs_code = '<ul class="wcrbp-tab-nav">';
        $i = 0;
        foreach ( $tabs as $key => $tab_data ) {
            if ( is_string( $tab_data ) ) { $tab_data = array( 'title' => $tab_data ); }
            $tab_data = wp_parse_args( $tab_data, array( 'icon'  => '', 'title' => '','show_status' =>  true));
            if ( filter_var( $tab_data['icon'], FILTER_VALIDATE_URL ) ) {
                $icon = '<img src="' . $tab_data['icon'] . '">';
            } else {
                if ( false !== strpos( $tab_data['icon'], 'dashicons' ) ) {
                    $tab_data['icon'] .= ' dashicons';
                }
                $tab_data['icon'] = array_filter( array_map( 'trim', explode( ' ', $tab_data['icon'] ) ) );
                $tab_data['icon'] = implode( ' ', array_unique( $tab_data['icon'] ) );
                $icon = $tab_data['icon'] ? '<i class="' . $tab_data['icon'] . '"></i>' : '';
            }
            
            $show_status = 'no';
            $status_tag = '';
            if($tab_data['show_status']){
                $show_status = 'yes';
                $status_tag = '<i class="wc-rbp-tab-status"></i>';
            }

            $class = "wcrbp-tab-$key";
            if ( ! $i ){$class .= ' wcrbp-tab-active';}
            $tabs_code .= sprintf('<li data-status="%s" class="%s" data-panel="%s"><a href="#">%s%s%s</a></li>', $show_status,$class, $key, $icon, $tab_data['title'],$status_tag);
            $i ++;
        }
        
        $tabs_code .= '</ul>';
        
        
        $content_data  = '<div class="wcrbp-tab-panels">';
		foreach ($content as $id  => $data ) {
			$content_data .= '<div class="wcrbp-tab-panel wcrbp-tab-panel-' . $id . '">';
			$content_data .= $data;
			$content_data .= '</div>';
		}
        
		$content_data .= '</div>';
        
        $final = $wraper_start .$tabs_code .$content_data .$wraper_end;
        return $final;
    }
}

if(!function_exists('wc_rbp_allowed_roles')){
    
    function wc_rbp_allowed_roles(){
        $roles = wc_rbp_option('allowed_roles');
        if(empty($roles)){
            $roles = array_keys(wc_rbp_get_user_roles_selectbox());
        }
        
        return $roles;
    }
}

if(!function_exists('wc_rbp_allowed_price')){
    
    function wc_rbp_allowed_price(){
        $roles = wc_rbp_option('allowed_price');
        if(empty($roles)){
            $roles = array_keys(wc_rbp_avaiable_price_type());
        }
        
        return $roles;
    }
}



/**
 * @public Below Function Are Used By The Plugin users 
 */
if(!function_exists('wc_rbp_update_role_based_price')){
	/**
	 * Updates Products Role Based Price Array In DB
	 * @param  int $post_id     Post ID To Update
	 * @param  array $price_array Price List
	 * @return boolean  [[Description]]
	 */
	function wc_rbp_update_role_based_price($post_id,$price_array){
		update_post_meta($post_id,'_role_based_price', $price_array);
		return true;
	}
}

if(!function_exists('wc_rbp_get_product_price')){

	/**
	 * Gets Product price from DB
	 * #TODO Integrate Wth product_rbp_price function to make it faster
	 */
	function wc_rbp_get_product_price($post_id,$supress_filter = false){
		$price = get_post_meta($post_id,'_role_based_price');
		if(!empty($price)) {$price = $price[0];}
		else if(empty($price)) {$price = array();}
		if(!$supress_filter)
			$price = apply_filters('wc_rbp_product_prices',$price);
		return $price;
	}
	
}

if(!function_exists('product_rbp_price')){
	/**
	 * Gets product price from DB
	 */
	function product_rbp_price($post_id,$productOBJ = null){
		global $product; 
        
        
        if(is_null($product) && is_null($productOBJ) ){
            $price = wc_rbp_get_product_price($post_id);
			return $price;
        }
        
       if(!is_null($productOBJ)){
             if($productOBJ->id == $post_id){
                 if(isset($productOBJ->post->wc_rbp) && !empty($productOBJ->post->wc_rbp)) {
                    return $productOBJ->post->wc_rbp;   
                 }
             }
        } 
        
        if(!is_null($product)){
            if($product->id == $post_id){
                if(isset($product->post->wc_rbp) && !empty($product->post->wc_rbp)) {
                    return $product->post->wc_rbp;    
                }
            }
        }
        
		$price = wc_rbp_get_product_price($post_id);
        
        return $price;
	}
}

if(!function_exists('product_rbp_status')){
	/**
	 * Returns Products Role Based Price Array In DB
	 * @param  int $post_id     Post ID To Update
	 * @param  array $price_array Price List
	 * @return boolean  [[Description]]
	 * #TODO Integrate WC_RBP_PRODUCT_STATUS Function For Speed Outpu
	 */
	function product_rbp_status($post_id,$productOBJ = null){
		global $product;  
        
        if(is_null($product) && is_null($productOBJ) ){
            $price = wc_rbp_product_status($post_id);
			return $price;
        }
        
        if(!is_null($productOBJ)){
            if($productOBJ->id == $post_id){
               if(isset($productOBJ->post->wc_rbp) && ! empty($productOBJ->post->wc_rbp) ){ return $productOBJ->post->wc_rbp_status; }
            }
        }
        
        if(!is_null($product) ){
            if($product->id == $post_id){
                if(isset($product->post->wc_rbp) && ! empty($product->post->wc_rbp) ){ return $product->post->wc_rbp_status; }
            }
        } 

		$price = wc_rbp_product_status($post_id);
        return $price;
	}
}

if(!function_exists('wc_rbp_settings_products_json')){
    function wc_rbp_settings_products_json($ids){
        $json_ids    = array();
        if(!empty($ids)){
            $ids = explode(',',$ids);
            foreach ( $ids as $product_id ) {
                $product = wc_get_product( $product_id );
                $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
            }   
        }
        return $json_ids;
    }
}

if(!function_exists('wc_rbp_update_role_based_price_status')){

	/**
	 * Updates Products Role Based Price Array In DB
	 * @param  int $post_id     Post ID To Update
	 * @param  array $price_array Price List
	 * @return boolean  [[Description]]
	 */
	function wc_rbp_update_role_based_price_status($post_id,$status = true){
		update_post_meta($post_id,'_enable_role_based_price', $status);
		return true;
	}
}

if(!function_exists('wc_rbp_product_status')){
	/**
	 * Retrives Status value from Databse.
	 * @param  [[Type]] $post_id [[Description]]
	 * @return [[Type]] [[Description]]
	 * #TODO Integrate With product_rbp_status
	 */
	function wc_rbp_product_status($post_id){
		$status = get_post_meta($post_id,'_enable_role_based_price',true); 
		if($status == '1' || $status == 'true'){return true;}
		return false;
	}
}

if(!function_exists('wc_rbp_price')){
	/**
	 * Returns Price Based On Give Value
	 * @role : enter role slug / use all to get all roles values
	 * @price : use selling_price / regular_price or use all to get all values for the given role
	 */
	
	function wc_rbp_price($post_id,$role,$price = 'regular_price',$args = array(),$product = null){ 
		$dbprice = product_rbp_price($post_id,$product); 
		$return = false; 
		
		if($price == 'all' && $role == 'all'){
			$return = $dbprice;
		} else if($price == 'all' && $role !== 'all'){
			if(isset($dbprice[$role])){
				$return = $dbprice[$role];
			}			
		} else if(isset($dbprice[$role][$price])){
			$return = $dbprice[$role][$price];
		}
		$return = apply_filters('wc_rbp_product_price',$return,$role,$price,$post_id,$args);
		return $return;
	}
}

if(!function_exists('wc_rbp_active_price')){
    
    function wc_rbp_active_price($post_id,$role,$args = array(),$product = null){
        $price = wc_rbp_price($post_id,$role,'all',$args,$product);
        if(isset($price['selling_price'])){
            if(!empty($price['selling_price'])){
                return $price['selling_price'];
            }
        }
        
        return $price['regular_price'];
    }
}