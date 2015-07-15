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

	// Display an admin notice why the plugin can't work.
	function gce_plugin_requirements() {
		global $wp_version;
		echo '<div class="error"><p>' . sprintf( __( 'Google Events Calendar requires PHP 5.3.2 and WordPress 3.9.0 to function properly. PHP version found: %1$s. WordPress installed version: %2$s. Please upgrade to meet the minimum requirements. The plugin has been auto-deactivated.', 'gce' ), PHP_VERSION, $wp_version ) . '</p></div>';
		// Removes the activation notice if set.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
	add_action( 'admin_notices', 'gce_plugin_requirements' );

	// Deactivates the plugin.
    function gce_deactivate_self() {
	    deactivate_plugins( plugin_basename( __FILE__ ) );
    }
	add_action( 'admin_init', 'gce_deactivate_self' );

	// Halt the rest of the plugin execution.
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


