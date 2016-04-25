<?php

class WooCommerce_Role_Based_Price_Product_Metabox{
    
    public function __construct() {
        add_action( 'add_meta_boxes_product', array($this,'add_metabox'));
    }
    
    public function add_metabox($post){
        $this->allowed_roles = wc_rbp_option('allowed_roles');
        $this->registered_roles = wc_rbp_get_wp_roles();
        
        add_meta_box('wc-rbp-product-editor', __( 'WC Role Based Price Editor' , WC_RBP_TXT),  array($this,'render_my_meta_box'), 'product', 'advanced', 'high');
    }
    
    public function render_my_meta_box($post){ 
        $id = $post->ID;
        $title = $post->post_title;
        $prod = wc_get_product($id);
        $prodType = $prod->product_type;
        $tabs = $this->get_metabox_tabs($id,$prodType);
        $content = $this->get_metabox_content($id,$tabs,$prod,$prodType);
        echo wc_rbp_get_ajax_overlay(); 
        do_action('wc_rbp_before_metabox_content',$prod,$prodType);
        echo wc_rbp_generate_tabs($tabs,$content);
        echo wc_rbp_get_editor_fields('single');
        
        echo '<input type="hidden" id="wc_rbp_product_id" name="product_id" value="'.$id.'" /> ';
        echo '
        <h2 class="hndle ui-sortable-handle" style="margin: 0px -12px -12px; border-top: 1px solid #eee; text-align:right;">
            <button type="button" id="wc_rbp_update_price" class="button button-primary">'.__('Save Price',WC_RBP_TXT).'</button></h2>
        
        ';
        do_action('wc_rbp_after_metabox_content',$prod,$prodType);        
    }
    
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
    
    
    
}

return new WooCommerce_Role_Based_Price_Product_Metabox;