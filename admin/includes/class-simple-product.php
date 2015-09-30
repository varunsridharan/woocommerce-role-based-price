<?php
/**
 * Simple Product Role Based Price Settings
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 * @since      0.1
 * @package    WooCommerce_Role_Based_Price
 * @subpackage WooCommerce_Role_Based_Price/admin
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Role_Based_Price_Admin_Product_Page { 
    private $status;
    
	/**
     * Load The Class
     * @since 0.1
	 */
	public function __construct(){
        add_action('admin_footer', array($this,'wc_rbp_popup_div'));
		add_action('woocommerce_product_options_pricing',array($this,'add_rbp_edit_button'));
        add_action('woocommerce_product_after_variable_attributes',array($this,'add_rbp_variable_edit_button'),1,3);
        add_action( 'wp_ajax_role_based_price_edit', array($this,'price_form') );
        add_action( 'wp_ajax_'.WC_RBP_SLUG.'-product-edit-save',array($this,'save_price'));
 	}
     
    
	/**
	 * Adds Edit Button After Pricing In General Only For Simple Product 
	 * @since 0.1
	 * @filter_use woocommerce_product_options_pricing
	 */
	public function add_rbp_edit_button(){
        global $thepostid; 
            $notice = '';
            $disabled = '';
            if($thepostid == null) {
                $disabled = 'disabled="disabled"';
                $notice = __('<span class="wc_rbp_error"> <strong> Please Save Or Publish This Product To Add Role Based Price </strong> </span>',lang_dom);
            }
            echo '<p class="form-field role_based_price_ajax show_if_simple ">
                    <label for="role_based_price_ajax">'.__('Role Based Price',lang_dom).'</label>
                     <a  
                    href="'.admin_url('admin-ajax.php?action=role_based_price_edit&type=simple&post_id='.$thepostid).'"  
                    class="button button-primary role_based_price_editor_btn" 
                  '.$disabled.' > '.__('Add / Edit Role Pricing',lang_dom).' </a>
                    
                    
                       
                       '.$notice.'
                    </p>  
        ';  
	} 
    /*<input type="button" 
                    data-target="'.admin_url('admin-ajax.php?action=role_based_price_edit&type=simple&post_id='.$thepostid).'"  
                    class="button button-primary role_based_price_editor_btn" 
                    value="'.__('Add / Edit Role Pricing',lang_dom).'" '.$disabled.'   /> */

   /**
	 * Adds Edit Button After Pricing In General Only For Simple Product 
	 * @since 0.1
	 * @filter_use woocommerce_product_options_pricing
	 */
	public function add_rbp_variable_edit_button($var1,$var2,$var3){
            $notice = '';
            $disabled = '';
            if($var3->ID == null) {
                $disabled = 'disabled="disabled"';
                $notice = __('<span class="wc_rbp_error"> <strong> Please Save Or Publish This Product To Add Role Based Price </strong> </span>',lang_dom);
            }
            echo '<p class="form-row   form-row-full  role_based_price_ajax show_if_variable  "> 
            
            <a  
                    href="'.admin_url('admin-ajax.php?action=role_based_price_edit&type=simple&post_id='.$thepostid).'"  
                    class="button button-primary role_based_price_editor_btn" 
                  '.$disabled.' > '.__('Add / Edit Role Pricing',lang_dom).' </a>
                    
                    
                       
                       '.$notice.'
                    </p>  
        ';  
	}  
    /*<input type="button" 
                    data-target="'.admin_url('admin-ajax.php?action=role_based_price_edit&type=simple&post_id='.$var3->ID).'"  
                    class="button button-primary role_based_price_editor_btn" 
                    value="'.__('Add / Edit Role Pricing',lang_dom).'" '.$disabled.'   /> **/
    
    public function wc_rbp_popup_div(){
        if(in_array(WC_RBP()->admin()->current_screen() ,  WC_RBP()->admin()->get_screen_ids())) {
            echo '<div id="wc_rpb_dialog" > </div>';
        }
    } 
    
    
    public function save_price(){

        $msg = '<div class="wc_rbp_alert wc_rbp_alert-error">
  <a class="wc_rbp_close wc_rbp_pop_up_close" data-dismiss="alert">×</a>  <strong>Oh Snap!</strong> '.__('Unable To Save Product Role Price. Please Try Again.',lang_dom).' </div>';
        if(check_ajax_referer(WC_RBP_SLUG.'-product-edit-nounce' , 'security' )){
            $msg = '';
            if(isset($_POST['wc_rbp_post_id']) && !empty($_POST['wc_rbp_post_id'])){
                $post_id = $_POST['wc_rbp_post_id'];
                
                if(isset($_POST['enable_role_based_price'])){
                    update_post_meta($post_id,'_enable_role_based_price', 'true');
                } else {
                    update_post_meta($post_id,'_enable_role_based_price', 'false');
                }
                
           
            
                if(isset($_POST['role_based_price']) && is_array($_POST['role_based_price'])){ 
                    $prices = $_POST['role_based_price'];
                    update_post_meta($post_id,'_role_based_price', $prices);
                } 
                
                do_action('woocommerce_role_based_price_data_save',$post_id);
            }
             
           
            $msg = ' <div class="wc_rbp_alert wc_rbp_alert-success"> <a class="wc_rbp_close wc_rbp_pop_up_close" data-dismiss="alert">×</a> <strong>Well done!</strong> '.__('Product\'s Role Based Pricing Updated',lang_dom).' </div>';
        }
        wp_die($msg);
    }
    
 	/**
	 * Adds Form In WC Product Edit Page
	 * @since 0.1
	 * @filter_use woocommerce_product_data_panels 
	 */
	public function price_form(){ 
        $type = $_REQUEST['type'];
        $post_id = $_REQUEST['post_id'];
        $this-> simple_price_form($post_id);
        wp_die(); 
	}
    
	/**
	 * Adds Form In WC Product Edit Page
	 * @since 0.1
	 * @filter_use woocommerce_product_data_panels 
	 */
	public function simple_price_form($post_id){ 
        global $user_role_key,$name,$regular_price,$selling_price,$wc_rbp_thepostid,$wc_rbp_enable_status; 
        $wc_rbp_thepostid = $post_id;
        
        $regular_price = WC_RBP()->get_allowed_price('regular');
        $selling_price = WC_RBP()->get_allowed_price('sale');
        WC_RBP()->sp_function()->get_db_price($post_id);
        $this->status = WC_RBP()->sp_function()->get_status($post_id);
        
        $wc_rbp_enable_status = '';
        $display = 'hidden';
        
        if($this->status == true){ $wc_rbp_enable_status = 'checked'; $display = ''; }
        
        include(WC_RBP_ADMIN_PATH.'includes/views/popup_rbform_header.php');
        include(WC_RBP_ADMIN_PATH.'includes/views/popup_rbform_tab.php');
        
        foreach(WC_RBP()->get_allowed_roles() as $user_role_key => $val){
             $name = WC_RBP()->get_mod_name($user_role_key);
             include(WC_RBP_ADMIN_PATH.'includes/views/popup_rbform_content.php');
        }
        
        include(WC_RBP_ADMIN_PATH.'includes/views/popup_rbform_footer.php');
         
	}
	      
}

new WooCommerce_Role_Based_Price_Admin_Product_Page;
?>