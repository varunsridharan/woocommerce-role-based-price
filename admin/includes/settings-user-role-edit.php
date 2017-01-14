<?php
$width = "width:50% !important;";
global $settings;

$settings[] = array(
    'name' => __('Custom Name For User Roles',WC_RBP_TXT),
    'type' => 'title',
    'desc' => '',
    'id' => rbp_key.'user_role_edit_start'
);
foreach(WC_RBP()->get_allowed_roles() as $role => $name){
    $settings[] = array(
        'name' => __($name['name'],WC_RBP_TXT),
        'desc' => '',
        'id' => rbp_key.'role_name['.$role.']',
        'type' => 'text', 
        'class' =>'',
        'css'     => 'width:25% !important;'
    ) ;
}

$settings[] =  array(
    'type' => 'sectionend',
    'id' => rbp_key.'user_role_edit_end'
);
?>