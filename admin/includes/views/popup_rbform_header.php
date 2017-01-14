<?php global $wc_rbp_enable_status; ?>
<div class="wc_rbp_pop_container">
    <form method="post" id="wc_rbp_product_edit_form">
    <div class="enable_field_container">
        <p class="form-field ">
            <label class="enable_text"><?php echo  __('Enable Role Based Pricing',WC_RBP_TXT); ?> </label>
            <label class="wc_rbp_switch wc_rbp_switch-green">
                <input type="checkbox" class="switch-input" id="enable_role_based_price" name="enable_role_based_price" <?php echo $wc_rbp_enable_status; ?>/>
                <span class="switch-label" data-on="<?php echo __('on',WC_RBP_TXT); ?>" data-off="<?php echo __('off',WC_RBP_TXT); ?>"></span>
                <span class="switch-handle"></span>
            </label>
        </p>
    </div>  
    <div class="wc_rbp_update_message"> </div>
    <div id="wc_rbp_pop_tabs">