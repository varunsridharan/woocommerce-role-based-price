<?php
class WC_RBP_Activation{
    public $plugin_slug;
    public $plugin_url;
    public $page_html;
    
    public function  __construct($args = array()){
		$default_args = array(
			'dbslug' => 'wp_plugin_welcome',
			'welcome_slug' => 'welcome-screen-about',
			'wp_plugin_slug' => '',
			'template' => 'page-html.php',
			'menu_name' => 'Welcome To Plugin Welcome Page',
			'plugin_file' => '',
			'plugin_name' => 'WC User Role Based Coupon',
			'version' => '0.1',
			'txt_lang' => '',

			'wp_plugin_url' => 'http://varunsridharan.in',
			
			'tweet_text' => 'What A Aswome Plugin',
			'twitter_user' => 'varunsridharan2',
			'twitter_hash' => 'WCRBP',
			'gitub_user' => 'technofreaky',
			'github_repo' => 'woocommerce-quick-donation',
			
			'show_change_log' => true,
			'show_decs' => true,
			'show_downloads' => true,
		);
			
		$args = wp_parse_args( $args, $default_args );
		$this->txt_lang = $args['txt_lang'];
        $this->plugin_slug = $args['dbslug'];
		$this->wp_plugin_slug = $args['wp_plugin_slug'];
        $this->plugin_url = $args['welcome_slug'];
        $this->page_html = $args['template'];
        $this->Menu_NAME = $args['menu_name'];
		$this->wp_plugin_url = $args['wp_plugin_url'];
		$this->tweet_text = $args['tweet_text'];
		$this->twitter_user = $args['twitter_user'];
		$this->twitter_hash = $args['twitter_hash'];
		$this->gitub_user = $args['gitub_user'];
		$this->github_repo = $args['github_repo'];
		$this->plugin_name = $args['plugin_name'];
		$this->version = $args['version'];
		
		$this->show_change_log = $args['show_change_log'];
		$this->show_decs = $args['show_decs'];
		$this->show_downloads = $args['show_downloads'];
		
		add_action( 'admin_init', array($this,'activation_redirect') );
        add_action( 'admin_menu', array($this,'welcome_screen_pages'));
        add_action( 'admin_head', array($this,'welcome_screen_remove_menus' ));
        register_activation_hook($args['plugin_file'], array($this,'welcome_screen_activate' ) );
    }
    
    /**
     * Registers Activation Hook
     */
    function welcome_screen_activate(){ 
        $this->activate();
    }    
    /**
     * Sets Transient For Activation hook To Redirect To Welcome Page
     */
    public function activate(){ 
        set_transient( $this->plugin_slug.'_welcome_screen_activation_redirect', true, 30 );
    } 
    
    /**
     * Checks For Active Transient And Redirect To Welcome Page
     */
    public function activation_redirect(){
        if ( ! get_transient( $this->plugin_slug.'_welcome_screen_activation_redirect' ) ) { return; }
        delete_transient( $this->plugin_slug.'_welcome_screen_activation_redirect' );        
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) { return; }        
        wp_safe_redirect( add_query_arg( array( 'page' => $this->plugin_url ), admin_url( 'index.php' ) ) );
    }
    
    /**
     * Adds Welcome Page
     */
    public function welcome_screen_pages() {
        add_dashboard_page($this->Menu_NAME,'','read',$this->plugin_url, array($this,'welcome_screen_content'));
    }
    
    /**
     * Welcome Page html
     */
    public function welcome_screen_content() {
        $this->wp_api(); 
		require($this->page_html);	
    }
    
    /**
     * Remove Dashboard Welcome Page
     */
    public function welcome_screen_remove_menus() {
        remove_submenu_page( 'index.php', $this->plugin_url );
    }    
	
	public function get_decs($return = false){
		$data = '';
		
		if($this->show_decs){
			$data = '<div class="change_log_list"> '.$this->decs.'</div>'; 
		}
		if(!$return){echo $data;}
		return $data;
	}

		
	public function get_change_log($return = false){
		$data = '';
		if($this->show_change_log){
			$data = '<div class="change_log_list"> <h1>'.__('Change Log', $this->txt_lang) .'</h1>'.$this->change_log.'</div>'; 
		}
		if(!$return){echo $data;}
		return $data;
	}
	

		
	public function get_downloads($return = false){
		$data = '';
		if($this->show_downloads){
			$data = sprintf('<p class="downloads_count"> <span> %s </span> '.__(' Download\'s so far. help use reach more audience by sharing' , $this->txt_lang) .'</p>',$this->downloads); 
		}
		if(!$return){echo $data;}
		return $data;
	}	
	
	
	/**
	 * Gets Remote Data From WP API
	 */
	public function wp_api(){
		$args = (object) array( 'slug' => $this->wp_plugin_slug );
		$request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => serialize( $args) );
		$url = 'http://api.wordpress.org/plugins/info/1.0/';
		$response = wp_remote_post( $url, array( 'body' => $request ) );
		$plugin_info = unserialize( $response['body'] );
		$downloads = ''; $decs = ''; $change_log = '';
		if($plugin_info){
			$downloads = $plugin_info->downloaded;
			$decs = $plugin_info->sections['description'];
			$change_log = $plugin_info->sections['changelog'];
		}

		$this->decs = $decs;
		$this->change_log = $change_log;
		$this->downloads = $downloads;
	}
}
?>