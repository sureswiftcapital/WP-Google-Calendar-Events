<?php

/**
 * Register Google Calendar Events custom post type
 * 
 * @since 2.0.0
 */
function gce_setup_cpt() {

	$labels = array(
		'name'               => __( 'Feeds', 'gce' ),
		'singular_name'      => __( 'Feed', 'gce' ),
		'menu_name'          => __( 'Google Calendar Events', 'gce' ),
		'name_admin_bar'     => __( 'Feed', 'gce' ),
		'add_new'            => __( 'Add New', 'gce' ),
		'add_new_item'       => __( 'Add New Feed', 'gce' ),
		'new_item'           => __( 'New Feed', 'gce' ),
		'edit_item'          => __( 'Edit Feed', 'gce' ),
		'view_item'          => __( 'View Feed', 'gce' ),
		'all_items'          => __( 'All Feeds', 'gce' ),
		'search_items'       => __( 'Search Feeds', 'gce' ),
		'not_found'          => __( 'No feeds found.', 'gce' ),
		'not_found_in_trash' => __( 'No feeds found in Trash.', 'gce' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor' )
	);
	
	register_post_type( 'gce_feed', $args );
}
add_action( 'init', 'gce_setup_cpt' );


/**
 * Add post meta to tie in with the Google Calendar Events custom post type
 * 
 * @since 2.0.0
 */
function gce_cpt_meta() {
	add_meta_box( 'gce_feed_meta', 'Feed Settings', 'gce_display_meta', 'gce_feed', 'advanced', 'core' );
}
add_action( 'add_meta_boxes', 'gce_cpt_meta' );


function gce_display_meta() {
	include_once( GCE_DIR . '/views/admin/gce-feed-meta-display.php' );
}