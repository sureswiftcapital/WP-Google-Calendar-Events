<?php

/**
 * Adds the 'gce-feed' shortcode
 * 
 * @since 2.0.0
 */
function gce_feed_shortcode( $attr ) {

	extract( shortcode_atts( array(
					'id' => null,
					'display' => null
				), $attr, 'gce_feed' ) );
	
	// If the ID is empty we can't pull any data so we skip all this and return nothing
	if( ! empty( $id ) ) {
		
		// TODO clean up this code! Make this more DRY somehow
		
		// check for a comma for multiple feeds
		if( strpos( $id, ',' ) === false ) {
			echo 'hit';
			if( ! empty ( $display ) ) {
				if( $display == 'list' ) {
					return gce_print_list( $id, null, 25, 'asc', false );
				} else if ( $display == 'list-grouped' ) {
					return gce_print_list( $id, null, 25, 'asc', true );
				} else {
					return gce_print_grid( $id, null, 25 );
				}
			} else {
				
				$display = get_post_meta( $id, 'gce_display_mode', true );
				
				if( $display == 'list' ) {
					return gce_print_list( $id, null, 25, 'asc', false );
				} else if ( $display == 'list-grouped' ) {
					return gce_print_list( $id, null, 25, 'asc', true );
				} else {
					return gce_print_grid( $id, null, 25 );
				}
			}
		} else {
		
			$id = explode( ',', $id );
			$id = implode( '-', $id );

			if( $display == 'list' ) {
				return gce_print_list( $id, null, 25, 'asc', false );
			} else if ( $display == 'list-grouped' ) {
				return gce_print_list( $id, null, 25, 'asc', true );
			} else {
				return gce_print_grid( $id, null, 25 );
			}
		}
	}
	
	return '';
}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );



function gce_print_grid( $feed_ids, $title_text, $max_events, $month = null, $year = null ) {
	
	// TODO Add error code checking back in eventually?
	
	$ids = explode( '-', $feed_ids );

	//Create new display object, passing array of feed id(s)
	$grid = new GCE_Display( $ids, $title_text, $max_events );

	
	$feed_ids = esc_attr( $feed_ids );
	$title_text = isset( $title_text ) ? esc_html( $title_text) : 'null';

	$markup = '<div class="gce-page-grid" id="gce-page-grid-' . $feed_ids . '">';

	// Automatically adding JS for AJAX now
	$markup .= '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-page-grid-' . $feed_ids . '", "' . $feed_ids . '", "' . absint( $max_events ) . '", "' . $title_text . '", "page");});</script>';
	
	// Get the actual calendar grid html
	$markup .= $grid->get_grid( $year, $month, true ) . '</div>';

	return $markup;
}


function gce_print_list( $feed_ids, $title_text, $max_events, $sort_order, $grouped = false ) {
	//require_once 'inc/gce-parser.php';
	
	//echo 'hit 1';
	
	$ids = explode( '-', $feed_ids );

	//Create new GCE_Parser object, passing array of feed id(s)
	$list = new GCE_Display( $ids, $title_text, $max_events, $sort_order );

	//$num_errors = $list->get_num_errors();

	//If there are less errors than feeds parsed, at least one feed must have parsed successfully so continue to display the list
	//if ( $num_errors < count( $ids ) ) {
		$markup = '<div class="gce-page-list">' . $list->get_list( $grouped ) . '</div>';

		//If there was at least one error, return the list markup with error messages (for admins only)
		//if ( $num_errors > 0 && current_user_can( 'manage_options' ) )
			//return $list->error_messages() . $markup;

		//Otherwise just return the list markup
		return $markup;
	//} else {
		//If current user is an admin, display an error message explaining problem(s). Otherwise, display a 'nice' error messsage
	//	if ( current_user_can( 'manage_options' ) ) {
	//		return $list->error_messages();
	//	} else {
	//		$options = get_option( GCE_GENERAL_OPTIONS_NAME );
	//		return wp_kses_post( $options['error'] );
	//	}
	//}
}
