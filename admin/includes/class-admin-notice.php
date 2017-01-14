<?php
/*
 * Name:        WordPress Admin Notice Handler
 * URI:         https://github.com/technofreaky/WordPress-Admin-Notice
 * Version:     1.0
 * Author:      Varun Sridharan
 * Author URI:  http://varunsridharan.in
 * License:     GPLv2
 *
 * Copyright 2015 Varun Sridharan (email : varunsridharan23@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) { die( 'Access denied.' ); }

if ( ! class_exists( 'WooCommerce_Role_Based_Price_Admin_Notice' ) ) {

	class WooCommerce_Role_Based_Price_Admin_Notice {
		// Declare variables and constants
		protected static $_instance;
		protected $notices, $notices_were_updated;
        protected static $db_key;
        
		/**
		 * Constructor
		 */
		public function __construct() {
            self::$db_key = 'wc_role_based_admin_notice';
			add_action( 'init',          array( $this, 'init' ), 1 );
			add_action( 'admin_notices', array( $this, 'print_notices' ) );
			add_action( 'shutdown',      array( $this, 'shutdown' ) );
		}

		/**
		 * Provides access to a single instances of the class using the singleton pattern
		 * @return object
		 */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
		 * Initializes variables
		 */
		public function init() {
			$default_notices             = array( 'update' => array(), 'error' => array() );
			$this->notices               = array_merge( $default_notices, WC_RBP()->get_option( self::$db_key, array() ) );
			$this->notices_were_updated  = false;
		}

		/**
		 * Queues up a message to be displayed to the user
		 * @param string $message The text to show the user
		 * @param string $type    'update' for a success or notification message, or 'error' for an error message
		 */
		public function add_notice( $message, $type = 'update' ) {
            if(isset($this->notices[ $type ]) && ! empty($this->notices[ $type ]) ){
                if ( in_array( $message, array_values( $this->notices[ $type ] ) ) ) {
                    return;
                }
            }
			
			$this->notices[ $type ][]   = (string) $message;
			$this->notices_were_updated = true;
		}
        
 
		/**
		 * Queues up a message to be displayed to the user
		 * @param string $message The text to show the user
		 * @param string $type    'update' for a success or notification message, or 'error' for an error message
		 */
		public function add( $message, $type = 'update' ) {
            $this->add_notice($message,$type);
		}       

		/**
		 * Displays updates and errors
		 */
		public function print_notices() {
			foreach ( array( 'update', 'error' ) as $type ) { 
				if ( isset($this->notices[ $type ]) &&  count( $this->notices[ $type ] ) ) {
					$class = 'update' == $type ? 'updated' : 'error';
                    echo '<div class="'.$class.'">';
                        foreach ( $this->notices[ $type ] as $notice ) : 
                            echo '<p>'.wp_kses($notice, wp_kses_allowed_html('post')).'</p>'; endforeach;
                    echo '</div>';
					$this->notices[ $type ]      = array();
					$this->notices_were_updated  = true;
				}
			}
		}

		/**
		 * Writes notices to the database
		 */
		public function shutdown() {
			if ( $this->notices_were_updated ) {
				update_option(self::$db_key, $this->notices );
			}
		}
    } 
}
?>