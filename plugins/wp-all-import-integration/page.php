<div class="panel woocommerce_options_panel" id="wc_rbp_integration" style="display:none;">
	
		<div class="options_group enable_wc_rbp">

		<p class="form-field">
			<label for="enable_role_based_price"> <?php _e('Enable Role Based Price',WC_RBP_TXT); ?> </label>
			<input id="enable_role_based_price" type="text" value="" name="<?php echo WC_RBP_DB.'integration'; ?>[_enable_role_based_price]" class="short">
			<br/><br>
			<strong><?php _e('To Enable Please Use [active,yes] Or To Disable Please leave empty ',WC_RBP_TXT); ?></strong>
		</p>

	</div>
	<div class="options_group wcrbp_pricing">
		<?php 
			$allowed_roles = wc_rbp_allowed_roles();
			$allowed_price = wc_rbp_allowed_price();
			$role_data = wc_rbp_get_wp_roles();
			$price_values = wc_rbp_avaiable_price_type();
			$curr = get_woocommerce_currency_symbol();
			foreach($allowed_roles as $key => $val){ 
				$name = isset($role_data[$val]['name']) ? $role_data[$val]['name'] : ''; 
				echo '<h4 style="margin: 0px; font-size: calc(100% + 4px); padding: 5px 20px;">'.$name.'</h4>';
				foreach($allowed_price as $price){
					echo  '<p class="form-field"> <label>'.$price_values[$price].' ('.$curr.') </label> <input type="text" value="" name="'.WC_RBP_DB.'integration['.$val.']['.$price.']" class="short"> </p>';
					/*<p class="form-field"> <label><?php echo $price_values[$price]; ?></label><input type="text" value="" name="wcrbpwpaii[<?php echo $key; ?>][<?php echo $price; ?>]" class="short"> </p>*/

				}
				echo '<hr/>';
			}    
		?>
	</div>
</div>