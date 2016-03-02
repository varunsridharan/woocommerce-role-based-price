<?php
global $send_fields;

if (empty($send_fields[WC_RBP_DB.'payment_gateway'])) {
    add_settings_error(WC_RBP_DB.'payment_gateway','', __( 'Error: Please Select Atlest 1 Payment Gateway.', WC_RBP_TXT ),'error');
}