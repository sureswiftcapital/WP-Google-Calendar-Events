<?php
/**
 * Google Calendar Events
 *
 * @package         GCE
 * @author          Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license         GPL-2.0+
 * @link            http://philderksen.com
 * @copyright       2014-2015 Phil Derksen
 *
 * @wordpress-plugin
 * Plugin Name:     Google Calendar Events
 * Plugin URI:      https://wordpress.org/plugins/google-calendar-events/
 * Description:     Show off your Google calendar in grid (month) or list view, in a post, page or widget, and in a style that matches your site.
 * Version:         2.2.6
 * Author:          Phil Derksen
 * Author URI:      http://philderksen.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     gce
 * Domain Path:     /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Check for minimum PHP and WordPress versions.
 * - PHP: 5.3.2
 * - WordPress: 3.9.0
 */
require_once 'class-wp-requirements.php';
$requirements = new WP_Requirements( array( 'wp' => '3.9.0', 'php' => '5.3.2' ) );
if ( $requirements->pass() === false ) {

	// Should use `create_function` instead of anonymous function for PHP 5.2.4 really
	// but `create_function` is such an evil and can't even get to work.
	add_action( 'admin_notices', function() {
		if ( isset( $_GET['activate'] ) ) { unset( $_GET['activate'] ); }
		global $wp_version;
		echo '<div class="error"><p>' . sprintf( __( 'Google Events Calendar requires at leas PHP 5.3.2 and WordPress 3.9 to function properly. Detected PHP version: %1$s. Detected WordPress version: %2$s. Please upgrade. The plugin has been auto-deactivated.', 'gce' ), '<code>' . PHP_VERSION . '</code>', '<code>' . $wp_version . '</code>' ) . '</p></div>';
	} );

    add_action( 'admin_init', 'gce_deactivate_self' );
    function gce_deactivate_self() {
	    deactivate_plugins( plugin_basename( __FILE__ ) );
    }

    return;
}

/*
 * Include the main plugin file
 *
 * @since 2.0.0
 */
require_once( 'class-google-calendar-events.php' );

/**
 * Define constant pointing to this file
 * 
 * @since 2.0.0
 */
if( ! defined( 'GCE_MAIN_FILE' ) ) {
	define( 'GCE_MAIN_FILE', __FILE__ );
}

/*
 * Get instance of our plugin
 * 
 * @since 2.0.0
 */
add_action( 'plugins_loaded', array( 'Google_Calendar_Events', 'get_instance' ) );

/*
 * If we are in admin then load the Admin class
 * 
 * @since 2.0.0
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'class-google-calendar-events-admin.php' );
	
	// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
	register_activation_hook( __FILE__, array( 'Google_Calendar_Events_Admin', 'activate' ) );
	
	// Get plugin admin class instance
	add_action( 'plugins_loaded', array( 'Google_Calendar_Events_Admin', 'get_instance' ) );
}


