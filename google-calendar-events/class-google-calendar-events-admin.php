<?php
/**
 * Google Calendar Events Admin
 *
 * @package   GCE Admin
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @link      http://philderksen.com
 * @copyright 2014 Phil Derksen
 */


class Google_Calendar_Events_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		$plugin = Google_Calendar_Events::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		
		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		
		// Setup admin side constants
		add_action( 'init', array( $this, 'define_admin_constants' ) );
		
		// Add admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
		// Add admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		
	}
	
	public static function enqueue_admin_scripts() {
		wp_enqueue_script( 'gce-admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), null, true );
	}
	
	public static function enqueue_admin_styles() {
		wp_enqueue_style( 'gce-admin', plugins_url( 'css/admin.css', __FILE__ ) );
	}
	
	public static function define_admin_constants() {
		if( ! defined( 'GCE_DIR' ) ) {
			define( 'GCE_DIR', dirname( __FILE__ ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
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

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    2.0.0
	 */
	public function display_admin_page() {
		include_once( 'views/admin/admin.php' );
	}
	
	/**
	 * Return plugin name
	 * 
	 * @since 2.0.0
	 */
	function get_plugin_title() {
		return __( 'Google Calendar Events', 'gce' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    2.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}
}
