<?php
/**
 * Plugin Name: WordPress Bulk Install
 * Description: Install your most used plugins and themes with one click.
 * Author: Rutger van Wijngaarden
 * Version: 1.0.1
 * Text Domain: wpbi
 *
 * Copyright: (c) 2020 Rutger van Wijngaarden
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    Rutger van Wijngaarden
 * @copyright Copyright: (c) 2020 Rutger van Wijngaardne
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base class
 */
 if ( !class_exists( 'WordPress_Bulk_Install' ) ) {
	 
	final class WordPress_Bulk_Install {
		
		/**
		 * The single instance of the class
		 */
		protected static $_instance = null;
	
		/**
		 * Default properties
		 */
		public static $plugin_version;
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $plugin_basefile_path;
		public static $plugin_text_domain;
		
		/**
		 * Sub class instances
		 */
		public $settings;


		/**
		 * Main Instance
		 */
		 public static function instance() {
			if( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}


		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();
			$this->init_hooks();
			
			// Send out the load action
			do_action( 'wpbi_load');
		}

		/**
		 * Hook into actions and filters
		 */ 
		public function init_hooks() {
			add_action( 'init', array( $this, 'load' ) );
		}

		/**
		 * Define WC Constants
		 */
		private function define_constants() {
			self::$plugin_version = '1.0.1';
			self::$plugin_prefix = 'wpbi_';
			self::$plugin_basefile_path = __FILE__;
			self::$plugin_basefile = plugin_basename( self::$plugin_basefile_path );
			self::$plugin_url = plugin_dir_url( self::$plugin_basefile );
			self::$plugin_path = trailingslashit( dirname( self::$plugin_basefile_path ) );	
			self::$plugin_text_domain = trim( dirname( self::$plugin_basefile ) );
		}

		/**
		 * Include the main plugin classes and functions
		 */
		public function include_classes() {
			include_once( 'includes/class-wpbi-ajax.php' );
			include_once( 'includes/class-wpbi-settings.php' );
		}

		/**
		 * Load the main plugin classes and functions
		 */
		public function load() {
				
			// Include the classes	
			$this->include_classes();
			
			// Create the instances
			$this->settings = new WordPress_Bulk_Install_Ajax();
			$this->settings = new WordPress_Bulk_Install_Settings();
			
			// Send out the init action
			do_action( 'wpbi_init');
		}

	}
}



/**
 * Returns the main instance of teh plugin to prevent the need to use globals
 */
function WPBI() {
	return WordPress_Bulk_Install::instance();
}

/**
 * Global for backwards compatibility
 */
$GLOBALS['wpbi'] = WPBI();