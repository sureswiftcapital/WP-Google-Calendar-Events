<?php

/**
 * Run our upgrade
 * 
 * TODO
 * Need to set an option to check the version
 * Need to only run if the version is less than 2.0.0
 * Setup code in a way to be easily expanded to other versions
 * Run this before all else
 * Old Feed Option(s) to CPT feeds
 * Will the widget settings transfer as is?
 */

add_action( 'init', 'gce_upgrade', 20 );

function gce_upgrade() {
	
	//delete_option( 'gce_upgrade_has_run' );
	
	$version = get_option( 'gce_version' );
	
	if( version_compare( $version, '2.0.0', '<' ) && false === get_option( 'gce_upgrade_has_run' ) ) {
		gce_v2_upgrade();
	}
}

function gce_v2_upgrade() {
	$old_options = get_option( 'gce_options' );
	
	//echo '<p>Dumping Old Options Data</p>';
	//echo '<pre>' . print_r( $old_options, true ) . '</pre>';
	
	// Options we need to actually convert over
	/*
	 * id [id] - This is going to be tricky since the CPT uses a different system for setting the feed ID from the old version
	 * title - 
	 * url
	 * retrieve_from
	 * retrieve_until
	 * retrieve_from_value*
	 * retrieve_until_value*
	 * max_events
	 * date_format*
	 * time_format*
	 * timezone
	 * cache_duration
	 * multiple_day
	 */
	
	
	foreach( $old_options as $key => $value ) {
		convert_to_cpt_posts( $value );
		
		//echo $value['title'] . '<br>';
	}
	
	
	add_option( 'gce_upgrade_has_run', 1 );
}

function convert_to_cpt_posts( $args ) {
	$post = array(
				'post_name'      => $args['title'],
				'post_title'     => $args['title'],
				'post_status'    => 'publish',
				'post_type'      => 'gce_feed'
			);
	
	$post_id = wp_insert_post( $post );
	
	create_cpt_meta( $post_id, $args );
}

function create_cpt_meta( $id, $args ) {
	
	// Convert the dropdown values to the new values for "Retrieve Events From"
	switch( $args['retrieve_from'] ) {
		case 'now':
		case 'today':
			$from = 'today';
			break;
		case 'week':
			$from = 'start_week';
			break;
		case 'month-start':
			$from = 'start_month';
			break;
		case 'month-end':
			$from = 'end_month';
			break;
		case 'date':
			$from = 'custom_date';
			break;
		default: 
			$from = 'start_time';
			break;
	}
	
	// Convert the dropdown values to the new values for "Retrieve Events Until"
	switch( $args['retrieve_until'] ) {
		case 'now':
		case 'today':
			$until = 'today';
			break;
		case 'week':
			$until = 'start_week';
			break;
		case 'month-start':
			$until = 'start_month';
			break;
		case 'month-end':
			$until = 'end_month';
			break;
		case 'date':
			$until = 'custom_date';
			break;
		default: 
			$until = 'end_time';
			break;
	}
	
	// An array to hold all of our post meta ids so we can run them through a loop
	$post_meta_fields = array(
		'gce_feed_url'         => $args['url'],
		'gce_retrieve_from'    => $from,
		'gce_retrieve_until'   => $until,
		'gce_retrieve_max'     => $args['max_events'],
		'gce_date_format'      => $args['date_format'],
		'gce_time_format'      => $args['time_format'],
		'gce_timezone_offset'  => $args['timezone'],
		'gce_cache'            => $args['cache_duration'],
		'gce_multi_day_events' => ( $args['multiple_day'] == true ? 1 : 0 ),
		'gce_display_mode'     => 'grid',
		'gce_custom_from'      => $args['retrieve_from_value'],
		'gce_custom_until'     => $args['retrieve_until_value']
	);
	
	foreach( $post_meta_fields as $k => $v ) {
		update_post_meta( $id, $k, $v );
		echo 'ID: ' . $id . ', Key: ' . $k . ', Value: ' . $v . '<br>';
	}
}
