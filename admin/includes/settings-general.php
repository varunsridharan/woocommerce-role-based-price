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
                'name' => __('Allowed User Roles',WC_RBP_TXT),
                'desc' => __('User Roles To List In Product Edit Page',WC_RBP_TXT),
                'id' => rbp_key.'list_roles',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  WC_RBP()->admin()->get_selectbox_user_role()
            ), 	

            array(
                'name' => __('Allowed Product Pricing',WC_RBP_TXT),
                'desc' => __('Price Fields To List In Product Edit Page',WC_RBP_TXT),
                'id' => rbp_key.'allowed_price',
                'type' => 'multiselect', 
                'class' =>'chosen_select',
                'css'     => $width,
                'options' =>  array('regular' => __('Regular Price',WC_RBP_TXT),'sale' => __('Sale Price',WC_RBP_TXT))
            ),    
            array(
					'type' 	=> 'sectionend',
					'id' 	=> 'general_start'
				),
            
        );
?>