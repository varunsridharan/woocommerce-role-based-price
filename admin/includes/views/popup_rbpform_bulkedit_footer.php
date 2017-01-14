    </div>
<?php
global $wc_rbp_thepostid;
foreach($variation_ids as $ids){
	echo '<input type="hidden" id="wc_rbp_bulk_variation_id[]" name="wc_rbp_bulk_variation_id[]" value="'.$ids.'" />';
}
$ajax_nonce = wp_create_nonce( WC_RBP_SLUG.'-product-bulk-edit-nounce' );
echo '<input type="hidden" id="wc_rbp_post_id" name="wc_rbp_post_id" value="'.$wc_rbp_thepostid.'" />';
echo '<input type="hidden" id="security" name="security" value="'.$ajax_nonce.'" />';
echo '<input type="hidden" id="action" name="action" value="'.WC_RBP_SLUG.'-product-bulk-edit-save" /> ';
?>
    <div class="wc_rbp_save_price">
        <p class="form-field ">
              <input type="button" class="button button-primary" value="<?php echo __('Save Price',WC_RBP_TXT); ?>"  id="save_wc_rbp_price"/>
        </p>
    </div>  
 </form>
</div> 