<?php

/**
 * Shortcode functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */


/**
 * Adds support for the new [gcal] shortcode
 * 
 * Supports the old [google-calendar-events] shortcode
 * 
 * @since 2.0.0
 */
function gce_gcal_shortcode( $attr ) {

	extract( shortcode_atts( array(
					'id'                    => null,
					'display'               => '',
					'order'                 => 'asc',
					'title'                 => null,
					'type'                  => null,
					'paging'                => '',
					'interval'              => null,
					'interval_count'        => null,
					'offset_interval'       => null,
					'offset_interval_count' => null,
					'offset_direction'      => null
				), $attr, 'gce_feed' ) );
	
	// If no ID is specified then return
	if( empty( $id ) ) {
		return;
	}
	
	$paging_interval = null;
	$max_events = null;
	
	$feed_ids = explode( ',', $id );

	foreach( $feed_ids as $k => $v ) {
		// Check for an old ID attached to this feed ID first
		$q = new WP_Query( "post_type=gce_feed&meta_key=old_gce_id&meta_value=$v&order=ASC" );

		if( $q->have_posts() ) {
			$q->the_post();
			// Set our ID to the old ID if found
			$feed_ids[$k] = get_the_ID();
			$v = get_the_ID();
		}

		if( empty( $display ) ) {
			$display = get_post_meta( $v, 'gce_display_mode', true );
		}
		
		if( $interval == null ) {
			$interval = get_post_meta( $v, 'gce_list_max_length', true );
		}
		
		if( $interval_count == null ) {
			$interval_count = get_post_meta( $v, 'gce_list_max_num', true );
		}
		
		if( $offset_interval == null ) {
			$offset_interval = get_post_meta( $v, 'gce_list_start_offset_length', true );
		}
		
		if( $offset_interval_count == null ) {
			$offset_interval_count = get_post_meta( $v, 'gce_list_start_offset_num', true );
		}
		
		if( $offset_direction == null ) {
			$offset_direction = get_post_meta( $v, 'gce_list_start_offset_direction', true );
		}
		
		if( ! empty( $paging ) ) {
			update_post_meta( $v, 'gce_paging', ( $paging == 'true' ? 1 : 0 ) );
		}
	}
	
	if( $offset_direction == 'back' ) {
		$offset_direction = -1;
	} else {
		$offset_direction = 1;
	}
	
	if( $offset_interval == 'days' ) {
		$start_offset = $offset_interval_count * 86400 * $offset_direction;
	} else if( $offset_interval == 'events' ) {
		$max_events = $offset_interval_count;
		$paging_type = 'events';
	}
	
	if( $interval == 'days' ) {
		$paging_interval = $interval_count * 86400;
	} else if( $interval == 'events' ) {
		$max_events = $interval_count;
	}

	// Port over old options
	if( $type != null ) {
		if( 'ajax' == $type ) {
			$display = 'grid';
		} else {
			$display = $type;
		}
	}

	$args = array(
		'title_text' => $title,
		'sort'       => $order,
		'grouped'    => ( $display == 'list-grouped' ? 1 : 0 ),
		'month'      => null,
		'year'       => null,
		'widget'     => 0,
		'paging_interval' => $paging_interval,
		'max_events' => $max_events
	);
	
	if( ! empty( $start_offset ) ) {
		$args['start_offset'] = $start_offset;
	}
	
	if( ! empty( $paging_type ) ) {
		$args['paging_type'] = $paging_type;
	}
	
	$feed_ids = implode( '-', $feed_ids );

	return gce_print_calendar( $feed_ids, $display, $args );
	
}
add_shortcode( 'gcal', 'gce_gcal_shortcode' );
add_shortcode( 'google-calendar-events', 'gce_gcal_shortcode' );
