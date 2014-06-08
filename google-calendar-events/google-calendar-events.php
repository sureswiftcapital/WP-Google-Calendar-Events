<?php
/**
 * Google Calendar Events
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @link      http://philderksen.com
 * @copyright 2014 Phil Derksen
 *
 * @wordpress-plugin
 * Plugin Name:       Google Calendar Events
 * Plugin URI:        @TODO
 * Description:       @TODO
 * Version:           2.0.0-beta1
 * Author:            Phil Derksen
 * Author URI:        @TODO
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include shared admin and public files
//require( plugin_dir_path( __FILE__ ) . 'includes/widgets.php' );


/*
 * Include the main plugin file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-google-calendar-events.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 * 
 */
//register_activation_hook( __FILE__, array( 'Plugin_Name', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'Plugin_Name', 'deactivate' ) );

/*
 * Get instance of our plugin
 */
add_action( 'plugins_loaded', array( 'Google_Calendar_Events', 'get_instance' ) );



/*
 * If we are2 in admin then load the Admin class
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-google-calendar-events-admin.php' );
	add_action( 'plugins_loaded', array( 'Google_Calendar_Events_Admin', 'get_instance' ) );

}
