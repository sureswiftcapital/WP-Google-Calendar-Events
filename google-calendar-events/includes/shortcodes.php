<?php

/**
 * Adds the 'gce-feed' shortcode
 * 
 * @since 2.0.0
 */
function gce_gcal_shortcode( $attr ) {

	extract( shortcode_atts( array(
					'id' => null,
					'display' => 'grid',
					'max' => 0,
					'order' => 'asc',
					'title' => null,
					// OLD options that need to be supported still
					//'id' => '',
					'type' => null,
					//'title' => null,
					//'max' => 0,
					//'order' => 'asc'
				), $attr, 'gce_feed' ) );
	
	// If the ID is empty we can't pull any data so we skip all this and return nothing
	if( ! empty( $id ) ) {
		
		// Port over old options
		if( $type != null ) {
			$display = $type;
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
		
		// TODO clean up this code! Make this more DRY somehow
		
		// check for a comma for multiple feeds
		/*if( strpos( $id, ',' ) === false ) {
			if( ! empty ( $display ) ) {
				if( $display == 'list' ) {
					return gce_print_list( $id, $title, $max, $order, false );
				} else if ( $display == 'list-grouped' ) {
					return gce_print_list( $id, $title, $max, $order, true );
				} else {
					return gce_print_grid( $id, $title, $max );
				}
			} else {
				
				$display = get_post_meta( $id, 'gce_display_mode', true );
				
				if( $display == 'list' ) {
					return gce_print_list( $id, $title, $max, $order, false );
				} else if ( $display == 'list-grouped' ) {
					return gce_print_list( $id, $title, $max, $order, true );
				} else {
					return gce_print_grid( $id, $title, $max );
				}
			}
		} else {
		
			$id = explode( ',', $id );
			$id = implode( '-', $id );

			if( $display == 'list' ) {
				return gce_print_list( $id, $title, $max, $order, false );
			} else if ( $display == 'list-grouped' ) {
				return gce_print_list( $id, $title, $max, $order, true );
			} else {
				return gce_print_grid( $id, $title, $max );
			}
		}*/
	}
	
	return '';
}
add_shortcode( 'gcal', 'gce_gcal_shortcode' );
add_shortcode( 'google-calendar-events', 'gce_gcal_shortcode' );


/*
 * @array $args
 			'title_text' => $title,
			'max_events' => $max,
			'sort'       => $order,
			'grouped'    => 0,
			'month'      => null,
			'year'       => null,
			'widget'     => 1
 */
function gce_print_calendar( $feed_ids, $display = 'grid', $args = array() ) {
	
	extract( $args );
	
	$ids = explode( '-', $feed_ids );
	
	//Create new display object, passing array of feed id(s)
	$d = new GCE_Display( $ids, $title_text, $max_events );
	$markup = '';
	
	if( 'grid' == $display ) {
		
		$markup = '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("' . ( $widget == 1 ? 'gce-widget-' : 'gce-page-grid-' ) . $feed_ids 
					. '", "' . $feed_ids . '", "' . absint( $max_events ) . '", "' . $title_text . '", "' . ( $widget == 1 ? 'widget' : 'page' ) . '");});</script>';
		
		if( $widget == 1 ) {
			$markup .= '<div class="gce-widget-grid" id="gce-widget-' . $feed_ids . '">';
		} else {
			$markup .= '<div class="gce-page-grid" id="gce-page-grid-' . $feed_ids . '">';
		}
		
		$markup .= $d->get_grid();
		$markup .= '</div>';
		
	} else if( 'list' == $display || 'list-grouped' == $display ) {
		$markup = '<div class="gce-page-list">' . $d->get_list( $grouped ) . '</div>';
	}
	
	return $markup;
}
