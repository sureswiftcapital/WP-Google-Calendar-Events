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
					'id'      => null,
					'display' => 'grid',
					'max'     => 0,
					'order'   => 'asc',
					'title'   => null,
					'type'    => null,
				), $attr, 'gce_feed' ) );
	
	// If the ID is empty we can't pull any data so we skip all this and return nothing
	if( ! empty( $id ) ) {
		
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
			'max_events' => $max,
			'sort'       => $order,
			'grouped'    => ( $display == 'list-grouped' ? 1 : 0 ),
			'month'      => null,
			'year'       => null,
			'widget'     => 0
		);
		
		return gce_print_calendar( $id, $display, $args );
	}
	
	return '';
}
add_shortcode( 'gcal', 'gce_gcal_shortcode' );
add_shortcode( 'google-calendar-events', 'gce_gcal_shortcode' );


/*
 * Function to display the calendar to the screen
 * 
 * @since 2.0.0
 */
function gce_print_calendar( $feed_ids, $display = 'grid', $args = array() ) {

	$defaults = array( 
			'title_text' => '',
			'max_events' => 25,
			'sort'       => 'asc',
			'grouped'    => 0,
			'month'      => null,
			'year'       => null,
			'widget'     => 0
		);
	
	$args = array_merge( $defaults, $args );
	
	extract( $args );
	
	$ids = explode( '-', $feed_ids );
	
	//Create new display object, passing array of feed id(s)
	$d = new GCE_Display( $ids, $title_text, $max_events, $sort );
	$markup = '';
	
	if( 'grid' == $display ) {
		
		$markup = '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("' . ( $widget == 1 ? 'gce-widget-' : 'gce-page-grid-' ) . $feed_ids 
					. '", "' . $feed_ids . '", "' . absint( $max_events ) . '", "' . $title_text . '", "' . ( $widget == 1 ? 'widget' : 'page' ) . '");});</script>';
		
		if( $widget == 1 ) {
			$markup .= '<div class="gce-widget-grid" id="gce-widget-' . $feed_ids . '">';
		} else {
			$markup .= '<div class="gce-page-grid" id="gce-page-grid-' . $feed_ids . '">';
		}
		
		$markup .= $d->get_grid( $year, $month );
		$markup .= '</div>';
		
	} else if( 'list' == $display || 'list-grouped' == $display ) {
		$markup = '<div class="gce-page-list">' . $d->get_list( $grouped ) . '</div>';
	}
	
	return $markup;
}
