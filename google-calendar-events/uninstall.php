<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove CPTs and transients
$feeds = get_posts( array( 
	'post_type' => 'gce_feed'
));

foreach( $feeds as $f ) {
	// delete the transient while we have the post ID available
	delete_transient( 'gce_feed_' . $f->ID );
	
	// Now delete the post
	wp_delete_post( $f->ID, true );
}

// Remove all post meta
delete_post_meta_by_key( 'gce_feed_url' );
delete_post_meta_by_key( 'gce_retrieve_from' );
delete_post_meta_by_key( 'gce_retrieve_until' );
delete_post_meta_by_key( 'gce_retrieve_max' );
delete_post_meta_by_key( 'gce_date_format' );
delete_post_meta_by_key( 'gce_time_format' );
delete_post_meta_by_key( 'gce_cache' );
delete_post_meta_by_key( 'gce_multi_day_events' );
delete_post_meta_by_key( 'gce_display_mode' );
delete_post_meta_by_key( 'gce_custom_from' );
delete_post_meta_by_key( 'gce_custom_until' );

// Remove options
delete_option( 'gce_upgrade_has_run' );
delete_option( 'gce_version' );
