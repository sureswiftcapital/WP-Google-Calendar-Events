<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO uninstall actions

// Remove CPTs
$feeds = get_posts( array( 
	'post_type' => 'gce_feed'
));

foreach( $feeds as $f ) {
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
