<?php
$old_version =  get_option(WC_RBP_DB_KEY.'version');
if(! $old_version) { $old_version = '2.3';}
if(version_compare($old_version, WC_RBP_VERSION, '<' )) {
    update_option(WC_RBP_DB_KEY.'products_variable_settings','hide');
    update_option(WC_RBP_DB_KEY.'products_hide_settings','a:2:{i:0;s:6:"simple";i:1;s:8:"variable";}');
}
?>

