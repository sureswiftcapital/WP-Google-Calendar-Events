<?php
	
function gce_setup_cpt() {

	$labels = array(
		'name'               => __( 'Feeds', 'your-plugin-textdomain' ),
		'singular_name'      => __( 'Feed', 'your-plugin-textdomain' ),
		'menu_name'          => __( 'Google Calendar Events', 'your-plugin-textdomain' ),
		'name_admin_bar'     => __( 'Feed', 'your-plugin-textdomain' ),
		'add_new'            => __( 'Add New', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Feed', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Feed', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Feed', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Feed', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Feeds', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Feeds', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No feeds found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No feeds found in Trash.', 'your-plugin-textdomain' )
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
		'supports'           => array( 'title' )
	);
	
	register_post_type( 'gce_feed', $args );
}
add_action( 'init', 'gce_setup_cpt' );

