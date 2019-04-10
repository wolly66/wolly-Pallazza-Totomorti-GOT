<?php
/**
 * @package Wolly Pallazza
 * @author Paolo Valenti
 * @version 1.0 first release
 */
/*
Plugin Name: Wolly Pallazza
Plugin URI: https://goodpress.it
Description: This plugin has all the utility PALLAZZA GOT TOTOMORTI
Author: Paolo Valenti aka Wolly for WordPress Italy
Version: 1.0
Author URI: https://paolovalenti.info
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wolly-pallazza
Domain Path: /languages
*/
/*
	Copyright 2013  Paolo Valenti aka Wolly  (email : wolly66@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function wolly_pallazza_textdomain_init() {
  load_plugin_textdomain( 'wolly-pallazza', false, dirname( plugin_basename( __FILE__ ) ) ); 
}
add_action('plugins_loaded', 'wolly_pallazza_textdomain_init');

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}
	


class Wolly_Pallazza_Init{
	
	//A static member variable representing the class instance
	private static $instance;

	

	/**
	 * Wolly_Consulenze_Editoriali_Init::__construct()
	 * Locked down the constructor, therefore the class cannot be externally instantiated
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */
	 
	private $version = '0.1';

	
	
	public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wolly_Pallazza_Init ) ) {
				self::$instance = new Wolly_Pallazza_Init;

				

				self::$instance->setup_constants();
				self::$instance->includes();

				add_action( 'plugins_loaded', array(
					self::$instance,
					'setup_objects'
				), - 1 );

				

				//add_action( 'init', array(
				//	self::$instance,
				//	'init_settings'
				//) );

			}

			return self::$instance;
		}


	/**
	 * Wolly_Consulenze_Editoriali_Init::__clone()
	 * Prevent any object or instance of that class to be cloned
	 *
	 * @return
	 */
	public function __clone() {
		trigger_error( "Cannot clone instance of Singleton pattern ...", E_USER_ERROR );
	}

	/**
	 * Wolly_Consulenze_Editoriali_Init::__wakeup()
	 * Prevent any object or instance to be deserialized
	 *
	 * @return
	 */
	public function __wakeup() {
		trigger_error( 'Cannot deserialize instance of Singleton pattern ...', E_USER_ERROR );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since  1.0
	 * @return void
	 */
	 private function setup_constants() {
	    // Plugin version
	    if ( ! defined( 'WPIT_WOLPAL_PLUGIN_VERSION' ) ) {
	    	define( 'WPIT_WOLPAL_PLUGIN_VERSION', $this->version );
	    }
	    
	    if ( ! defined( 'WPIT_WOLPAL_PLUGIN_VERSION_NAME' ) ) {
	    	define( 'WPIT_WOLPAL_PLUGIN_VERSION_NAME', 'wpit_wollpal_version' );
	    }

	 
	    // Plugin Folder Path
	    if ( ! defined( 'WPIT_WOLPAL_PLUGIN_PATH' ) ) {
	    	define( 'WPIT_WOLPAL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	    }
	 
	    // Plugin Folder URL
	    if ( ! defined( 'WPIT_WOLPAL_PLUGIN_DIR' ) ) {
	    	define( 'WPIT_WOLPAL_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
	    }
	 
	    // Plugin Root File
	    if ( ! defined( 'WPIT_WOLPAL_PLUGIN_SLUG' ) ) {
	    	define( 'WPIT_WOLPAL_PLUGIN_SLUG', basename( dirname( __FILE__ ) ) );
	    }
	 
	    // Make sure CAL_GREGORIAN is defined.
	    if ( ! defined( 'CAL_GREGORIAN' ) ) {
	    	define( 'CAL_GREGORIAN', 1 );
	    }
	    
	    // Plugin version
	    if ( ! defined( 'WPIT_WOLPAL_POLL_OPEN' ) ) {
	    	define( 'WPIT_WOLPAL_POLL_OPEN', TRUE );
	    }
	}
	
	private function includes() {

			// Global used class
			// require_once WPIT_TCH_PLUGIN_DIR . 'includes/class-wpit-tch-classname.php';

			require_once WPIT_WOLPAL_PLUGIN_PATH . '/abstract/class-db.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-answer-db.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-cpt.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-options.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-poll.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-ranking.php';
			require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-pal-gdpr.php';
			//require_once WPIT_WOLPAL_PLUGIN_PATH . '/classes/wolly-class-coe-foreign-rights.php';
			// Admin only used class
			if ( is_admin() ) {

				// admin only

			} else {

				// Frontend only used class

				// require_once WPIT_TCH_PLUGIN_DIR . 'includes/class-wpit-tch-classname.php';

			}

	}
	
	public function setup_objects() {

			
			self::$instance->answers 		= new Wolly_Pal_Answers_Db();
			self::$instance->cpt 			= new Wolly_Pal_Cpt();
			self::$instance->got_poll		= new Wolly_Pal_Poll();
			self::$instance->ranking		= new Wolly_Pal_Ranking();
			self::$instance->gdpr			= new Wolly_Pal_Gdpr();
			//self::$instance->list_authors   = new Wolly_Coe_List_Authors();
			//self::$instance->foreign   		= new Wolly_Coe_Foreign_Rights();
			
			// Istantiate in admin only
			if ( is_admin() ) {


			}

			self::$instance->update_check();

			//// Enqueue scripts and styles in admin
			//add_action( 'admin_enqueue_scripts', array(
			//	self::$instance,
			//	'admin_enqueue_script'
			//) );
			//
			//add_action( 'wp_enqueue_scripts', array(
			//	self::$instance,
			//	'front_end_enqueue_script'
			//) );

			
		}


	
	
	/**
	 * update_UTILITY_check function.
	 *
	 * @access public
	 * @return void
	 */
	 public function update_check() {
	 // Do checks only in backend
	    if ( is_admin() ) {
	 
	    	if ( version_compare( get_option( WPIT_WOLPAL_PLUGIN_VERSION_NAME ), WPIT_WOLPAL_PLUGIN_VERSION ) != 0  ) {
	 
	    	$this->do_update();
	 
	    	}
	 
	 	} //end if only in the admin
	 }
		
	
		
		
	/**
	 * do_update function.
	 *
	 * @access private
	 *
	 */
	 public function do_update(){
	 
	    //DO NOTHING, BY NOW, MAYBE IN THE FUTURE
	 
	    //Update option
	 
	    update_option( WPIT_WOLPAL_PLUGIN_VERSION_NAME , WPIT_WOLPAL_PLUGIN_VERSION );
	 }
	 
	// public function admin_enqueue_script(){
	//	 
	//	 wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
	//	 wp_enqueue_style( 'jquery-ui' ); 
	//	 
	//	wp_enqueue_script('jquery-ui-datepicker');
	//	
	//	 wp_enqueue_script(
    //    	'field-date-js',
	//	 '' . WPIT_WOLPAL_PLUGIN_DIR . 'js/field-date.js',
	//	 array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
	//	 time(),
	//	 true );
	//	 
	//	wp_register_script( 'wolly_coe__relationship_autocomplete_book_authors', WPIT_WOLPAL_PLUGIN_DIR . '/js/wolly.autocomplete.book.author', array( 'jquery-ui-autocomplete' ), '1.0.0', TRUE );
	//	wp_enqueue_script( 'wolly_coe__relationship_autocomplete_book_authors' );
	//	 
	//	 
	// }
	// 
	// public function front_end_enqueue_script(){
	//	 
	//	
	//	 
	//	// wp_enqueue_script("jquery-ui-core"); 
	//	 wp_enqueue_script("jquery-ui-tabs");
	//	 
	//	 wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
	//	 wp_enqueue_style( 'jquery-ui' ); 
	//	 
	//	 wp_register_script( 'wolly-coe-tabs', WPIT_WOLPAL_PLUGIN_DIR . '/js/wolly.coe.tabs', array( 'jquery','jquery-ui-core', 'jquery-ui-tabs' ), '1.0.0', TRUE );
	//	 wp_enqueue_script("wolly-coe-tabs");
	//	 
	//	 wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
	// }

}

function wolly_pal() {
	return Wolly_Pallazza_Init::instance();
}

wolly_pal();
