<?php
$width = "width:50% !important;";
global $settings;
$WC_RBP_product_types = array('simple'=>__( 'Simple Product', lang_dom),'variable'=>__( 'Variable Product', lang_dom));
$settings = array(
            array(
                'name' => '',
                'type' => 'title',
                'desc' => '',
                'id' => rbp_key.'price_views_start'
            ), 			

           array(
                'name' => __('Hide Price For',lang_dom),
                'desc' => __('Use to hide product price for selected roles',lang_dom),
                'id' => rbp_key.'hide_price_role',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  WC_RBP()->admin()->get_selectbox_user_role()
            ), 	

    
            array(
                'name' => __('Hide Add To Cart Button For',lang_dom),
                'desc' => __('Use to hide product add to cart button link for selected roles',lang_dom),
                'id' => rbp_key.'hide_cart_button_role',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' => WC_RBP()->admin()->get_selectbox_user_role()
            ),   
    
            array(
                'name' => __('Message For Price',lang_dom),
                'desc' => __('use <code>[curr]</code> to replace the currency symbol',lang_dom),
                'id' => rbp_key.'replace_currency_symbol',
                'type' => 'textarea', 
                'class' =>'',
                'css'     => $width,
                'options' => WC_RBP()->admin()->get_selectbox_user_role()
            ), 
    
            array(
					'type' 	=> 'sectionend',
					'id' 	=> 'price_views_end'
				),
            
        );
?>