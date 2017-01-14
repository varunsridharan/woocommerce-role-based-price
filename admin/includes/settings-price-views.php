<?php
$width = "width:50% !important;";
global $settings;
$WC_RBP_product_types = array('simple'=>__( 'Simple Product', WC_RBP_TXT),'variable'=>__( 'Variable Product', WC_RBP_TXT));
$settings = array(
            array(
                'name' => '',
                'type' => 'title',
                'desc' => '',
                'id' => rbp_key.'price_views_start'
            ), 			

           array(
                'name' => __('Hide Price For',WC_RBP_TXT),
                'desc' => __('Use to hide product price for selected roles',WC_RBP_TXT),
                'id' => rbp_key.'hide_price_role',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  WC_RBP()->admin()->get_selectbox_user_role()
            ), 	

    
            array(
                'name' => __('Hide Add To Cart Button For',WC_RBP_TXT),
                'desc' => __('Use to hide product add to cart button link for selected roles',WC_RBP_TXT),
                'id' => rbp_key.'hide_cart_button_role',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' => WC_RBP()->admin()->get_selectbox_user_role()
            ),   
    
            array(
                'name' => __('Products To Hide',WC_RBP_TXT),
                'desc' => __('For Which Products To Apply The Above Settings',WC_RBP_TXT),
                'id' => rbp_key.'products_hide_settings',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' => array('simple' => __('Simple Product',WC_RBP_TXT),'variable' => __('Variable Product',WC_RBP_TXT))
            ),    
    
            array(
                'name' => __('Variable Product Settings',WC_RBP_TXT),
                'desc' => __(' ',WC_RBP_TXT),
                'id' => rbp_key.'products_variable_settings',
                'type' => 'select', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' => array('show' => __('Show Variations',WC_RBP_TXT),'hide' => __('Hide Variations',WC_RBP_TXT))
            ),    
            array(
                'name' => __('Message For Price',WC_RBP_TXT),
                'desc' => __('use <code>[curr]</code> to replace the currency symbol',WC_RBP_TXT),
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