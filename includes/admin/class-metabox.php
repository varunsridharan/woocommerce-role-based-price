<?php

class WooCommerce_Role_Based_Price_Product_Metabox{
    
    public function __construct() {
        add_action( 'add_meta_boxes_product', array($this,'add_metabox'));
    }
    
    public function add_metabox($post){
        add_meta_box('wc-rbp-product-editor', __( 'WC Role Based Price Editor' , WC_RBP_TXT),  array($this,'render_price_editor_metabox'), 'product', 'advanced', 'high');
    }
    
    public function render_price_editor_metabox($post){
        
        if(is_object($post)){ 
            $id = $post->ID;
        } else {
            $id = $post;
        }
        
        $this->allowed_roles = wc_rbp_allowed_roles();
        $this->registered_roles = wc_rbp_get_wp_roles();

		if ( $terms = wp_get_object_terms( $id, 'product_type' ) ) {
			$product_type = sanitize_title( current( $terms )->name );
		} else {
			$product_type = apply_filters( 'default_product_type', 'simple' );
		}
        
        if($product_type == 'variable'){
            $this->generate_variation_selectbox($id);
        }
        
        $prod = wc_get_product($id);
        $prodType = $prod->product_type;
        $tabs = $this->get_metabox_tabs($id,$prodType);
        $content = $this->get_metabox_content($id,$tabs,$prod,$prodType);
        $base_price = $this->get_base_price($id);
        
        $url = admin_url('admin-ajax.php?action=wc_rbp_save_product_prices'); 
        echo '<div class="wc-rbp-metabox-container" method="POST" action="'.$url.'" > ';
        echo wc_rbp_get_ajax_overlay(); 
        do_action('wc_rbp_before_metabox_content',$prod,$prodType);
        echo wc_rbp_generate_tabs($tabs,$content);
        echo wc_rbp_get_editor_fields('single');
        
        echo '<input type="hidden" id="wc_rbp_product_id" name="product_id" value="'.$id.'" /> ';
        echo '
        <h2 class="" style="margin: 0px -12px -12px; border-top: 1px solid #eee; text-align:right;">
            
            <span class="wc_rbp_base_product_price">'.$base_price.'</span>
        
            <button type="button" id="wc_rbp_update_price" class="button button-primary">'.__('Save Price',WC_RBP_TXT).'</button></h2>
        ';
        do_action('wc_rbp_after_metabox_content',$prod,$prodType);        
        echo '</div>';
    }
    
    public function get_base_price($id){
        $pro = wc_get_product($id);
        $price = array();
        $this->hook_filter(true);
        $price['regular_price'] = wc_rbp_price_types('regular_price').' : ';
        $price['regular_price'] .= wc_price($pro->get_regular_price());

        $price['selling_price'] = wc_rbp_price_types('selling_price').' : ';
        $price['selling_price'] .= wc_price($pro->get_sale_price());  
        $this->hook_filter(false);
        $price = implode(' | ',$price);
        $head = '<span class="headTxt">'.__("WC Product Price : ").'</span>'.$price;
        return $head;
    }
    
    public function hook_filter($hook = true){
        if($hook == true){
            add_filter('role_based_price_status',array($this,'base_price_return_false'));
        }
        if(! $hook == true){
            remove_filter('role_based_price_status',array($this,'base_price_return_false'));
        }
    }
    
    public function base_price_return_false($s){ return false;}
    
    public function get_metabox_content($id,$tabs,$prod,$prodType){
        $content = array() ;
        $registered_roles = $this->registered_roles;
        
        foreach ($tabs as $tab_id => $val){
            ob_start();
            do_action('wc_rbp_price_edit_tab_'.$tab_id.'_before',$id,$prodType,$prod,$tab_id);
            do_action('wc_rbp_price_edit_tab_'.$tab_id,$id,$prodType,$prod,$tab_id);
            do_action('wc_rbp_price_edit_tab_'.$tab_id.'_after',$id,$prodType,$prod,$tab_id);

            $content[$tab_id] = ob_get_contents();
            ob_end_clean();
        }
        
        return $content;
    }
    
    public function get_metabox_tabs($id,$prodType){
        $tabs = apply_filters('wc_rbp_before_default_product_tabs',array(),$id,$prodType); 
        $registered_roles = $this->registered_roles;

        foreach($this->allowed_roles as $role){
            if(isset($registered_roles[$role])){
                $icon = 'dashicons dashicons-admin-users';
                $tabs[$role] = array('title' => $registered_roles[$role]['name'],'icon' => $icon, 'show_status' => true);
            }
        }
        
        $tabs = apply_filters('wc_rbp_after_default_product_tabs',$tabs,$id,$prodType);
        return $tabs;
    }
    
    public function generate_variation_selectbox($id,$selected = ''){
        $selBox_PlaceHolder = __("Select A Variation : ",WC_RBP_TXT);
        $return = '<select id="wc_rbp_variation_select" style="width: 30%; display: inline-block; vertical-align: middle; margin-left: 10px;" name="wc_rbp_variation_select" class="wcrbpvariationbx">
        <option value="">'.$selBox_PlaceHolder.'</option>
        ';
        $args = array(
			'post_type'      => 'product_variation',
			'post_status'    => array( 'private', 'publish' ),
			'posts_per_page' => -1, 
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
			'post_parent'    => $id,
            'fields' => 'ids'
		);

		$variations = get_children( $args );
        $return .= ' <optgroup  label="'.__("Variations",WC_RBP_TXT).'"> ';
        foreach($variations as $ids){
            $prod = wc_get_product($ids);
            $name = '#'.$ids.' | ';
            $name .= ' '.$prod->get_formatted_variation_attributes(true);
            $selecteds = '';
            if($selected == $ids){$selecteds = 'selected';}
            $return .= '<option value="'.$ids.'" '.$selecteds.'>'.$name.'</option>';
        }
        $return .= ' </optgroup> ';
        
        $return .= '</select>';
        echo $return;
    }
    
}

return new WooCommerce_Role_Based_Price_Product_Metabox;