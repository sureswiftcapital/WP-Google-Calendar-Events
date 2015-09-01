<?php
/**
 * Google Calendar Events Main Class
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */


class Google_Calendar_Events {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	protected $version = '2.3.2';

	/**
	 * Unique identifier for the plugin.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'google-calendar-events';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

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

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		// Load files.
		$this->includes();

		$old = get_option( 'gce_version' );

		if( version_compare( $old, $this->version, '<' ) ) {
			delete_option( 'gce_upgrade_has_run' );
		}

		if( false === get_option( 'gce_upgrade_has_run' ) ) {
			$this->upgrade();
		}

		// Init plugin.
		$this->setup_constants();
		$this->plugin_textdomain();

		// Register scripts.
		add_action( 'init', array( $this, 'register_public_scripts' ) );
		add_action( 'init', array( $this, 'register_public_styles' ) );

		// Load scripts when posts load so we know if we need to include them or not
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_compatibility' ), 1000 );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain(
			'gce',
			false,
			dirname( plugin_basename( GCE_MAIN_FILE ) ) . '/languages/'
		);
	}

	/**
	 * Load public facing scripts
	 *
	 * @since 2.0.0
	 */
	public function register_public_scripts() {

		// DON'T include ImagesLoaded JS library recommended by qTip2 yet since we don't use "complex content that contains images" (yet).
		// http://qtip2.com/guides#gettingstarted.imagesloaded
		// We WERE doing this between 2.1.6 & 2.2.5 (taken out as of 2.2.6).
		// AND this was probably causing issues with themes including the Isotope jQuery library.
		// http://qtip2.com/guides#integration.isotope

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( $this->plugin_slug . '-qtip', plugins_url( 'js/jquery.qtip' . $min . '.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_slug . '-public', plugins_url( 'js/gce-script.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-qtip' ), $this->version, true );
	}

	/*
	 * Load public facing styles
	 *
	 * @since 2.0.0
	 */
	public function register_public_styles() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( $this->plugin_slug . '-qtip', plugins_url( 'css/jquery.qtip' . $min . '.css', __FILE__ ), array(), $this->version );
		wp_register_style( $this->plugin_slug . '-public', plugins_url( 'css/gce-style.css', __FILE__ ), array( $this->plugin_slug . '-qtip' ), $this->version );
	}

	/**
	 * Load scripts conditionally.
	 */
	public function load_scripts() {

		global $gce_options, $post;
		$post_type = isset( $post->post_type ) ? $post->post_type : null;
		$content   = isset( $post->post_content ) ? $post->post_content : '';

		$conditions = array(
			has_shortcode( $content, 'gcal' ),
			'gce_feed' == $post_type,
			isset( $gce_options['always_enqueue'] ),
			is_active_widget( false, false, 'gce_widget', true )
		);

		if ( in_array( true, $conditions ) ) {

			if ( ! isset( $gce_options['disable_css'] ) ) {
				wp_enqueue_style( $this->plugin_slug . '-public' );
			}

			wp_enqueue_script( $this->plugin_slug . '-public' );
			wp_localize_script( $this->plugin_slug . '-public', 'gce',	array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'loadingText' => __( 'Loading...', 'gce' ),
			) );
		}

	}

	public function theme_compatibility() {
		if ( wp_script_is( $this->plugin_slug . '-public', 'enqueued' )  ) {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				'gce-imagesloaded',
				plugins_url( 'js/imagesloaded.pkgd' . $min . '.js', __FILE__ ),
				array( $this->plugin_slug . '-qtip' ),
				'3.1.8',
				true
			);
		}
	}

	/**
	 * Load the upgrade file
	 *
	 * @since 2.0.0
	 */
	public function upgrade() {
		include_once( 'includes/admin/upgrade.php' );
	}

	/**
	 * Setup public constants
	 *
	 * @since 2.0.0
	 */
	public function setup_constants() {
		if( ! defined( 'GCE_DIR' ) ) {
			define( 'GCE_DIR', dirname( __FILE__ ) );
		}

		if( ! defined( 'GCE_PLUGIN_SLUG' ) ) {
			define( 'GCE_PLUGIN_SLUG', $this->plugin_slug );
		}
	}

	/**
	 * Include all necessary files
	 *
	 * @since 2.0.0
	 */
	public static function includes() {

		global $gce_options;

		// Front facing side.
		include_once( 'includes/misc-functions.php' );
		include_once( 'includes/gce-feed-cpt.php' );
		include_once( 'includes/class-gce-display.php' );
		include_once( 'includes/class-gce-event.php' );
		include_once( 'includes/class-gce-feed.php' );
		include_once( 'includes/shortcodes.php' );
		include_once( 'views/widgets.php' );

		// Admin.
		if ( is_admin() ) {
			include_once( 'includes/admin/admin-functions.php' );
		}

		// Setup our main settings options.
		include_once( 'includes/register-settings.php' );

		$gce_options = gce_get_settings();
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    2.0.0
	 *
	 * @return string Plugin version variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the plugin version.
	 *
	 * @since    1.0.0
	 *
	 * @return string Plugin slug variable.
	 */
	public function get_plugin_version() {
		return $this->version;
	}

}
