<?php
$width = "width:50% !important;";
global $settings;

$settings = array(
            array(
                'name' => '',
                'type' => 'title',
                'desc' => '',
                'id' => rbp_key.'general_start'
            ), 			

           array(
                'name' => __('Allowed User Roles',lang_dom),
                'desc' => __('User Roles To List In Product Edit Page',lang_dom),
                'id' => rbp_key.'list_roles',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  WC_RBP()->admin()->get_selectbox_user_role()
            ), 	

            array(
                'name' => __('Allowed Product Pricing',lang_dom),
                'desc' => __('Price Fields To List In Product Edit Page',lang_dom),
                'id' => rbp_key.'allowed_price',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  array('regular' => __('Regular Price',lang_dom),'sale' => __('Sale Price',lang_dom))
            ),    
            array(
					'type' 	=> 'sectionend',
					'id' 	=> 'general_start'
				),
            
        );
?>