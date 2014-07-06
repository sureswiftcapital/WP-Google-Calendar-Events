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
		
		// AJAX
		add_action( 'wp_ajax_no_priv_gce_ajax', array( $this, 'gce_ajax' ) );
		add_action( 'wp_ajax_gce_ajax', array( $this, 'gce_ajax' ) );
		
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
		
		// TODO make sure these are all accurate and up to date
		// Rearrange files according so we only run files when we absolutely need to
		
		// First include common files between admin and public
		include_once( 'includes/gce-feed-cpt.php' );
		include_once( 'includes/class-gce-feed.php' );
		include_once( 'includes/class-gce-event.php' );
		include_once( 'includes/shortcodes.php' );
		include_once( 'includes/class-gce-display.php' );
		
		include_once( 'views/widgets.php' );
		
		// Now include files specifically for public or admin
		if( is_admin() ) {
			// Admin includes
			include_once( 'includes/admin/admin-functions.php' );
		} else {
			// Public includes
			include_once( 'views/public/public.php' );
		}
		
	}
	
	public function enqueue_public_scripts() {
		// OLD calendar scripts
		wp_enqueue_script( $this->plugin_slug . '-qtip', plugins_url( 'js/jquery-qtip.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
		wp_enqueue_script( $this->plugin_slug . '-public', plugins_url( 'js/gce-script.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-qtip' ), self::VERSION, true );
		
		wp_localize_script( $this->plugin_slug . '-public', 'gce', 
				array( 
					//'url' => 'https://www.google.com/calendar/feeds/qs39fk8m91po76l92norrgr2b8%40group.calendar.google.com/public/basic',
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				) );
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
	
	function gce_ajax() {
		//global $post;
		
		// TODO Get widget working with new multi-feed code
		
		if ( isset( $_GET['gce_feed_ids'] ) ) {
			$ids   = $_GET['gce_feed_ids'];
			$title = $_GET['gce_title_text'];
			$max   = $_GET['gce_max_events'];
			$month = $_GET['gce_month'];
			$year  = $_GET['gce_year'];

			$title = ( 'null' == $title ) ? null : $title;
			
			$args = array(
				'title_text' => $title,
				'max_events' => $max,
				'month'      => $month,
				'year'       => $year,
			);
			
			if ( 'page' == $_GET['gce_type'] ) {
				//The page grid markup to be returned via AJAX
				//echo gce_print_grid( $ids, null, 25, $month, $year );
				
				echo gce_print_calendar( $ids, 'grid', $args );
				
			} elseif ( 'widget' == $_GET['gce_type'] ) {
				//$widget = esc_html( $_GET['gce_widget_id'] );

				//The widget grid markup to be returned via AJAX
				//gce_widget_content_grid( $ids, $title, $max, $widget, true, $month, $year );
				//echo gce_print_grid( $ids, null, 25, $month, $year );
				
				$args['widget'] = 1;
				
				echo gce_print_calendar( $ids, 'grid', $args );
			}
		}
		die();
	}
}
