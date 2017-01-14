<?php
global $user_role_key;
global $name;
global $regular_price;
global $selling_price; 
global $wc_rbp_thepostid;
global $thepostid;
$thepostid = $wc_rbp_thepostid;
?>

<div class="<?php echo $user_role_key; ?>_role_price" id="<?php echo $user_role_key; ?>"> 
    
    <div class="wc_rbp_plugin_field_container"> 
    <?php 
    if($regular_price){
        
        echo '<p class="form-field regular_price_'.$user_role_key.'_field form-row-first">
                <label for="regular_price_'.$user_role_key.'">'.__( 'Regular Price', WC_RBP_TXT).'</label>
                <input type="text" value="'.WC_RBP()->sp_function()->get_selprice($user_role_key,'regular_price').'" id="regular_price_'.$user_role_key.'" name="role_based_price['.$user_role_key.'][regular_price]" class="short wc_input_price">
                </p>';
    }

    if($selling_price){

        echo '<p class="form-field selling_price_'.$user_role_key.'_field form-row-last">
                <label for="selling_price_'.$user_role_key.'">'.__( 'Selling Price', WC_RBP_TXT).'</label>
                <input type="text" value="'.WC_RBP()->sp_function()->get_selprice($user_role_key,'selling_price').'" id="selling_price_'.$user_role_key.'" name="role_based_price['.$user_role_key.'][selling_price]" class="short wc_input_price">
                </p>';
    }
    ?>
    </div>

    <?php     
        do_action ('woocommerce_role_based_price_fields',$regular_price,$selling_price,$thepostid,$user_role_key,$name);
    ?>

</div> 