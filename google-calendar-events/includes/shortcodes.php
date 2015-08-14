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
 *
 * @param  array $attr
 * @return string
 */
function gce_gcal_shortcode( $attr ) {

	extract( shortcode_atts( array(
					'id'                    => null,
					'display'               => '',
					'order'                 => 'asc',
					'title'                 => null,
					'type'                  => null,
					'paging'                => null,
					'interval'              => null,
					'interval_count'        => null,
					'offset_interval_count' => null,
					'offset_direction'      => null,
					'show_tooltips'         => null
				), $attr, 'gce_feed' ) );

	if ( ! $id || is_null( $id ) ) {
		return '';
	}

	$invalid_id = false;

	$paging_interval = null;
	$max_events = null;

	$feed_ids = explode( ',', str_replace( ' ', '', $id ) );

	foreach( $feed_ids as $k => $v ) {
		// Check for an old ID attached to this feed ID first
		$q = new WP_Query( "post_type=gce_feed&meta_key=old_gce_id&meta_value=$v&order=ASC" );

		if( $q->have_posts() ) {
			$q->the_post();
			// Set our ID to the old ID if found
			$feed_ids[$k] = get_the_ID();
			$v = get_the_ID();
		}

		wp_reset_postdata();

		if( empty( $display ) ) {
			$display = get_post_meta( $v, 'gce_display_mode', true );
		}

		if( $interval == null ) {
			$interval = get_post_meta( $v, 'gce_events_per_page', true );
		}

		if( $interval_count == null ) {
			$interval_count = get_post_meta( $v, 'gce_per_page_num', true );
		}

		if( $offset_interval_count == null ) {
			$offset_interval_count = get_post_meta( $v, 'gce_list_start_offset_num', true );
		}

		if( $offset_direction == null ) {
			$offset_direction = get_post_meta( $v, 'gce_list_start_offset_direction', true );
		}

		if( $paging == null ) {
			$paging = get_post_meta( $v, 'gce_paging', true );
		}

		if( $show_tooltips == null ) {
			$show_tooltips = get_post_meta( $v, 'gce_show_tooltips', true );
		}

		if( ! ( 'publish' == get_post_status( $v ) ) ) {
			$invalid_id = true;
		}
	}

	if( $invalid_id ) {
		if( current_user_can( 'manage_options' ) ) {
			return '<p>' . __( 'There was a problem with one or more of your feed IDs. Please check your shortcode settings and make sure they are correct.', 'gce' ) . '</p>';
		}
	}

	if( $paging == 'false' ) {
		$paging = 0;
	} else if( $paging == 'true' ) {
		$paging = 1;
	}


	if( $offset_direction == 'back' ) {
		$offset_direction = -1;
	} else {
		$offset_direction = 1;
	}

	$start_offset = $offset_interval_count * 86400 * $offset_direction;

	if( $interval == 'days' ) {
		$paging_interval = $interval_count * 86400;
		$paging_type = 'days';
	} else if( $interval == 'events' ) {
		$max_events = $interval_count;
		$paging_type = 'events';
	} else if( $interval == 'week' ) {
		$paging_interval = 604800;
	} else if( $interval == 'month' ) {
		$paging_interval = 2629743;
	}

	// Port over old options
	if( $type != null ) {
		if( 'ajax' == $type ) {
			$display = 'grid';
		} else {
			$display = $type;
		}
	}

	if( $display == 'grouped-list' ) {
		$display = 'list-grouped';
	}

	$args = array(
		'title_text'      => $title,
		'sort'            => $order,
		'grouped'         => ( $display == 'list-grouped' ? 1 : 0 ),
		'month'           => null,
		'year'            => null,
		'widget'          => 0,
		'paging_interval' => $paging_interval,
		'max_events'      => $max_events,
		'paging'          => $paging,
		'show_tooltips'   => $show_tooltips
	);

	$args['start_offset'] = $start_offset;

	if( ! empty( $paging_type ) ) {
		$args['paging_type'] = $paging_type;
	}

	$feed_ids = implode( '-', $feed_ids );

	return gce_print_calendar( $feed_ids, $display, $args );

}
add_shortcode( 'gcal', 'gce_gcal_shortcode' );
add_shortcode( 'google-calendar-events', 'gce_gcal_shortcode' );
