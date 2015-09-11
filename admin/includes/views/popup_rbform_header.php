<?php global $wc_rbp_enable_status; ?>
<div class="wc_rbp_pop_container">
    <form method="post" id="wc_rbp_product_edit_form">
    <div class="enable_field_container">
        <p class="form-field ">
            <label class="enable_text"><?php echo  __('Enable Role Based Pricing',lang_dom); ?> </label>
            <label class="wc_rbp_switch wc_rbp_switch-green">
                <input type="checkbox" class="switch-input" id="enable_role_based_price" name="enable_role_based_price" <?php echo $wc_rbp_enable_status; ?>/>
                <span class="switch-label" data-on="<?php echo __('on',lang_dom); ?>" data-off="<?php echo __('off',lang_dom); ?>"></span>
                <span class="switch-handle"></span>
            </label>
        </p>
    </div>  
    <div id="wc_rbp_pop_tabs">