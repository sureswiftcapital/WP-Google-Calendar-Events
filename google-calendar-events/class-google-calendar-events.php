<?php
/**
 * Plugin Name.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */


class Google_Calendar_Events {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.0.0-beta1';

	/**
	 * Unique identifier for the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'google-calendar-events';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$this->includes();
		$this->setup_constants();
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );
		
	}
	
	public function setup_constants() {
		if( ! defined( 'GCE_DIR' ) ) {
			define( 'GCE_DIR', dirname( __FILE__ ) );
		}
		
		if( ! defined( 'GCE_PLUGIN_SLUG' ) ) {
			define( 'GCE_PLUGIN_SLUG', $this->plugin_slug );
		}
	}
	
	public static function includes() {
		
		// First include common files between admin and public
		include_once( 'includes/gce-feed-cpt.php' );
		include_once( 'includes/class-gce-feed.php' );
		include_once( 'includes/class-gce-event.php' );
		include_once( 'includes/shortcodes.php' );
		include_once( 'includes/class-gce-display.php' );
		
		// Now include files specifically for public or admin
		if( is_admin() ) {
			// Admin includes
			
		} else {
			// Public includes
			include_once( 'views/public/public.php' );
			//include_once( 'includes/class-gce-feed.php' );
			//include_once( 'includes/class-gce-event.php' );
			//include_once( 'includes/class-gce-parser.php' );
		}
		
	}
	
	public function enqueue_public_scripts() {
		// OLD calendar scripts
		wp_enqueue_script( $this->plugin_slug . '-qtip', plugins_url( 'js/jquery-qtip.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
		wp_enqueue_script( $this->plugin_slug . '-public', plugins_url( 'js/gce-script.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-qtip' ), self::VERSION, true );
	}
	
	public function enqueue_public_styles() {
		// OLD calendar CSS
		wp_enqueue_style( $this->plugin_slug . '-public', plugins_url( 'css/gce-style.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
