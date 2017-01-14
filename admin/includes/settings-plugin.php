<?php
    
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



class WC_RBP_PLUGINS extends WP_List_Table {
    
    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     * 
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     * 
     * @var array 
     **************************************************************************/
    
    var $example_data = array(
            array(
                'title'     => 'WP All Importer Integration',
                'description'    => 'Adds Option To Import Products With Role Based Pricing In WP All Importer <br/>
<a href="http://www.wpallimport.com/" >Go To Plugin Website -> </a> ',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'WP All Import - WooCommerce Add-On Pro',
                'actions' => 'wpai-woocommerce-add-on/wpai-woocommerce-add-on.php',
                'update' => '',
                'file' => 'class-wp-all-import-pro-Integration.php',
                'slug' => 'wpallimport',
                'testedupto' => 'V 4.1.6'
            ),
            array(
                'title'     => 'ACS Currency Switcher Integration',
                'description'    => 'Adds Option Set Product Price Based On Currency Choosen <br/> <a href="https://aelia.co/shop/currency-switcher-woocommerce/" >Go To Plugin Website -> </a>',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'Aelia Currency Switcher for WooCommerce',
                'actions' => 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php',
                'update' => '16th SEP 2015',
                'file' => 'class-aelia-currencyswitcher-Integration.php',
                'slug' => 'aeliacurrency',
                'testedupto' => 'V 3.8.4'
            ),
            array(
                'title'     => 'ACS Integration With [WP ALL Import]',
                'description'    => 'Intergates Aelia Currency Switcher With WP All Import Plugin',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => array('Aelia Currency Switcher','WP All Import - WooCommerce Add-On Pro'),
                'actions' => array('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php','wpai-woocommerce-add-on/wpai-woocommerce-add-on.php'),
                'update' => '',
                'file' => 'class-wc-rbp-wp-all-import-aelia-Integration.php',
                'slug' => 'aeliacurrency_wpallimport',
                'testedupto' => 'ACS : V 3.8.4 <br/> WPALLIMPORT : V 4.1.6'
            )
        );

    
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'wcrpbplugin',     //singular name of the listed records
            'plural'    => 'wcrpbplugins',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    function column_default($item, $column_name){ 
        return $item[$column_name];
    }

    function column_title($item){
        return sprintf('<strong> %1$s </strong>',
        /*$1%s*/ $item['title']
        );
    } 

    function column_required($item){
        $name = '';
        if(is_array($item['required'])){
            $name = implode(', <br/>',$item['required']);
        } else {
            $name = $item['required'];
        }
        return sprintf('<strong> %1$s </strong>',
        /*$1%s*/ $name
        );
    }
    
    function column_actions($item){
        $action = '<span style="color:red"> <strong> Install The Required Plugin First</strong> </span>';
        if(is_array($item['actions'])){
            $active_file = 0;
            
            foreach($item['actions'] as $plugin_file){
                if(is_plugin_active($plugin_file)){ 
                   $active_file++;  
                } else { 
                    $action = '<span style="color:red"> <strong> Install The Required Plugin First </strong> </span>';
                }
            }
            
            if($active_file == count($item['actions'])){
                $action =  $this->check_plugin_action($item);
            }
            
        } else {
            if(is_plugin_active($item['actions'])){ $action =  $this->check_plugin_action($item); } 
            else { $action = '<span style="color:red"> <strong>  Install The Required Plugin First </strong> </span>'; }
        }
        return $action;
    }
    

    
    function check_plugin_action($item){
        $action = '';
        if(in_array($item['slug'],WC_RBP()->get_activated_plugin())){
            
            $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=deactivate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button"> De-Activate </a>';
        } else {
            $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=activate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button button-primary">Activate </a>';
        }
        return $action;
    }
    

    function get_columns(){
        $columns = array( 
            'title'     => 'Title',
            'description'    => 'Description',
            'author'  => 'Author',
            'required' => 'Required Plugins',
            'testedupto' => 'Tested Upto',
            'update' => 'Last Update',
            'actions' => 'Actions'
        );
  
        return $columns;
    }


    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',true),     //true means it's already sorted
            'status'    => array('status',false),
        );
        return $sortable_columns;
    }


    
    function get_bulk_actions() {
        $actions = array();
        return $actions;
    }

    function process_bulk_action() {
        if('activate_plugin' === $this->current_action()){
            $activate_plugin = WC_RBP()->get_activated_plugin();

            if(empty($activate_plugin)){
                $activate_plugin[] = $_REQUEST['ps'];
            } else {
                $array_key = array_search($_REQUEST['ps'],$activate_plugin);
                if(! $array_key){
                    $activate_plugin[] = $_REQUEST['ps'];
                }
            }
           
            update_option(rbp_key.'activated_plugin',$activate_plugin);
        }
        
        if('deactivate_plugin' ===  $this->current_action()){
            
            $activate_plugin = WC_RBP()->get_activated_plugin();
            $i = 0;
            
            $count = count($activate_plugin); 
            $array_key = array_search($_REQUEST['ps'],$activate_plugin);
            if(isset($activate_plugin[$array_key]) && $activate_plugin[$array_key] == $_REQUEST['ps']){
                
                unset($activate_plugin[$array_key]);
            } 
            
            update_option(rbp_key.'activated_plugin',$activate_plugin);
        }
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }

 
    function prepare_items() {
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = WC_RBP()->get_plugins_list();
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);        $this->items = $data;
 
    }


}
 
    
    $testListTable = new WC_RBP_PLUGINS();
    $testListTable->prepare_items();

    ?>
    <div class="wrap">
            <?php $testListTable->display() ?>
    </div> 
<style>
    div.tablenav {display:none;}
</style>