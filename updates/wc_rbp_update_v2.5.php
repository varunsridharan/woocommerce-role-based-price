<?php
$old_version =  get_option(WC_RBP_DB_KEY.'version');
if(! $old_version) { $old_version = '2.3';}
if(version_compare($old_version, WC_RBP_VERSION, '<' )) {
    //$activated_plugins = get_option(WC_RBP_DB_KEY.'activated_plugin');
    //$active_plugin = array_keys($activated_plugins);
    update_option(WC_RBP_DB_KEY.'activated_plugin','');
    update_option(WC_RBP_DB_KEY.'version',WC_RBP_VERSION);
}
?>