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
	
	protected $version = '';

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
		
		$this->version = $plugin->get_plugin_version();
		
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . $this->plugin_slug . '.php' ), array( $this, 'add_action_links' ) );
		
		// Setup admin side constants
		add_action( 'init', array( $this, 'define_admin_constants' ) );
		
		// Add admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
		// Add admin JS
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ), 2 );
		
		// Add admin notice after plugin activation. Also check if should be hidden.
		add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );

		// Add media button for adding a shortcode.
		add_action( 'media_buttons', array( $this, 'add_shortcode_button' ), 100 );
		add_action( 'edit_form_after_editor', array( $this, 'add_shortcode_panel' ), 100 );
	}
	
	/**
	 * Show notice after plugin install/activate
	 * Also check if user chooses to hide it.
	 *
	 * @since   2.1.0
	 */
	public function show_admin_notice() {
		// Exit all of this is stored value is false/0 or not set.
		if ( false == get_option( 'gce_show_admin_install_notice' ) ) {
			return;
		}
		
		$screen = get_current_screen();

		// Delete stored value if "hide" button click detected (custom querystring value set to 1).
		if ( ! empty( $_REQUEST['gce-dismiss-install-nag'] ) ||  in_array( $screen->id, $this->plugin_screen_hook_suffix ) || $this->viewing_this_plugin() ) {
			delete_option( 'gce_show_admin_install_notice' );
			return;
		}

		// At this point show install notice. Show it only on the plugin screen.
		if( get_current_screen()->id == 'plugins' ) {
			include_once( 'includes/admin/admin-notice.php' );
		}
	}
	
	/**
	 * Check if viewing one of this plugin's admin pages.
	 *
	 * @since   2.1.0
	 *$this->viewing_this_plugin()
	 * @return  bool
	 */
	private function viewing_this_plugin() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return false;
		}
		
		$screen = get_current_screen();

		if ( $screen->id == 'edit-gce_feed' || $screen->id == 'gce_feed' || in_array( $screen->id, $this->plugin_screen_hook_suffix ) || $screen->id == 'widgets' ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    2.1.0
	 */
	public static function activate() {
		flush_rewrite_rules();
		update_option( 'gce_show_admin_install_notice', 1 );
	}

	/**
	 * Fired when the plugin is deactivated.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
	
	public function add_plugin_admin_menu() {
		// Add Help submenu page
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			'edit.php?post_type=gce_feed',
			__( 'General Settings', 'gce' ),
			__( 'General Settings', 'gce' ),
			'manage_options',
			$this->plugin_slug . '_general_settings',
			array( $this, 'display_admin_page' )
		);
	}
	
	public function display_admin_page() {
		include_once( 'views/admin/admin.php' );
	}
	
	 /**
	 * Enqueue JS for the admin area
	 * 
	 * @since 2.0.0
	 */
	public function enqueue_admin_scripts() {

		// Script for the add shortcode media button.
		wp_enqueue_script( 'gce-admin-add-shortcode', plugins_url( 'js/gce-admin-shortcode.js', __FILE__ ), array( 'jquery', 'thickbox' ), $this->version, true);

		if( $this->viewing_this_plugin() ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'gce-admin', plugins_url( 'js/gce-admin.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		}
	}
	
	/**
	 * Enqueue styles for the admin area
	 * 
	 * @since 2.0.0
	 */
	public function enqueue_admin_styles() {

		// Style for the add shortcode media button.
		wp_enqueue_style( 'gce-admin-shortcode', plugins_url( 'css/admin-shortcode.css', __FILE__ ), array(), $this->version, 'all' );

		if( $this->viewing_this_plugin() ) {
			global $wp_scripts;

			// get the jquery ui object
			$queryui = $wp_scripts->query( 'jquery-ui-datepicker' );

			// Use minified CSS from CDN referenced at https://code.jquery.com/ui/
			wp_enqueue_style( 'jquery-ui-smoothness', '//code.jquery.com/ui/' . $queryui->ver . '/themes/smoothness/jquery-ui.min.css', array(), $this->version );
 			
 			wp_enqueue_style( 'gce-admin', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version, 'all' );
 		}
	}
	
	/**
	 * Define constants that will be used throughout admin specific code
	 * 
	 * @since 2.0.0
	 */
	public function define_admin_constants() {
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
	 * Return plugin name
	 * 
	 * @since 2.0.0
	 */
	public function get_plugin_title() {
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
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=gce_feed&page=google-calendar-events_general_settings' ) . '">' . __( 'General Settings', 'gce' ) . '</a>',
				'feeds'    => '<a href="' . admin_url( 'edit.php?post_type=gce_feed' ) . '">' . __( 'Feeds', 'gce' ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Add a shortcode button.
	 *
	 * Adds a button to add a calendar shortcode in WordPress content editor.
	 *
	 * @see http://codex.wordpress.org/ThickBox
	 */
	public function add_shortcode_button() {
		// Thickbox will ignore height and width, will adjust these in js.
		// @see https://core.trac.wordpress.org/ticket/17249
		?>
		<a href="#TB_inline?height=250&width=500&inlineId=gce-insert-shortcode-panel" id="gce-insert-shortcode-button" class="thickbox button insert-calendar add_calendar">
			<span class="wp-media-buttons-icon"></span> <?php _e( 'Add Calendar', 'gce' ); ?>
		</a>
		<?php
	}

	/**
	 * Panel for the add shortcode media button.
	 *
	 * Prints the panel for choosing a calendar to insert as a shortcode in a page or post.
	 */
	public function add_shortcode_panel() {

		$feeds = get_transient( 'gce_feed_ids' );
		if ( ! $feeds ) {

			$query = get_posts( array(
				'post_type' => 'gce_feed',
				'orderby'   => 'title',
				'order'     => 'ASC',
				'nopaging'  => true
			) );

			$results = array();
			if ( $query && is_array( $query ) ) {
				foreach ( $query as $feed ) {
					$results[ $feed->ID ] = $feed->post_title;
				}
				set_transient( 'gce_feed_ids', $results, 604800 );
			}
			$feeds = $results;
		}

		?>
		<div id="gce-insert-shortcode-panel" style="display:none;">
			<div class="gce-insert-shortcode-panel">
				<h1><?php _e( 'Add Calendar', 'gce'); ?></h1>
				<p><?php _e( 'Add a calendar feed to your post.', 'gce' ); ?></p>
				<?php if ( ! empty( $feeds ) ) : ?>
					<label for="gce-choose-gce-feed">
						<select id="gce-choose-gce-feed" name="">
							<?php foreach ( $feeds as $id => $title ) : ?>
								<option value="<?php echo $id ?>"><?php echo $title ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<br />
					<input type="button" value="<?php _e( 'Insert Calendar', 'gce' ); ?>" id="gce-insert-shortcode" class="button button-primary button-large" name="" />
				<?php else : ?>
					<p><em><?php _e( 'Could not find any calendars to add to this post.', 'gce' ); ?></em></p>
					<p><strong><a href="post-new.php?post_type=gce_feed"><?php _e( 'Please add a new calendar feed first.', 'gce' ); ?></a></strong>
				<?php endif; ?>
			</div>
		</div>
		<?php

	}

}
