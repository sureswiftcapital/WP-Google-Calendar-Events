<?php
/**
 * Plugin Name: Google Calendar Events
 * Plugin URI: https://wordpress.org/plugins/google-calendar-events/
 * Description: Show off your Google calendar in grid (month) or list view, in a post, page or widget, and in a style that matches your site.
 * Author: Moonstone Media
 * Author URI: http://moonstonemediagroup.com
 * Version: 2.3.2
 * Text Domain: gce
 * Domain Path: /languages/
 *
 * Copyright 2014 Moonstone Media/Phil Derksen. All rights reserved.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Set the plugin PHP and WP requirements.
$gce_requires = array( 'wp' => '3.9.0', 'php' => '5.2.4' );
// Constants before PHP 5.6 can't store arrays.
define( 'GCE_REQUIREMENTS', serialize( $gce_requires ) );
// Checks if the requirements are met.
require_once dirname( __FILE__ ) . '/gce-requirements.php';
$gce_requirements = new GCE_Requirements( $gce_requires );
if ( $gce_requirements->pass() === false ) {

	// Display an admin notice explaining why the plugin can't work.
	function gce_plugin_requirements() {
		$required = unserialize( GCE_REQUIREMENTS );
		if ( isset( $required['wp'] ) && isset( $required['php'] ) ) {
			global $wp_version;
			echo '<div class="error"><p>' . sprintf( __( 'Google Events Calendar requires PHP %1$s and WordPress %2$s to function properly. PHP version found: %3$s. WordPress installed version: %4$s. Please upgrade to meet the minimum requirements.', 'gce' ), $required['php'], $required['wp'], PHP_VERSION, $wp_version ) . '</p></div>';
		}
	}
	add_action( 'admin_notices', 'gce_plugin_requirements' );

	$gce_fails = $gce_requirements->failures();
	if ( isset( $gce_fails['php'] ) ) {
		// Halt the rest of the plugin execution if PHP check fails.
		return;
	}

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
	register_deactivation_hook( __FILE__, array( 'Google_Calendar_Events_Admin', 'deactivate' ) );

	// Get plugin admin class instance
	add_action( 'plugins_loaded', array( 'Google_Calendar_Events_Admin', 'get_instance' ) );
}


