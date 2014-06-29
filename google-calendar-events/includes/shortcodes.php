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
		//$feed = new GCE_Feed( $id );
		
		/*if( empty( $display ) ) {
			$display_mode = get_post_meta( $id, 'gce_display_mode', true );
			$display = ( ! empty( $display_mode ) ? $display_mode : 'grid' );
		}*/
		
		//$display = new GCE_Display( explode( ',', $id ) );
		
		$id = explode( ',', $id );
		$id = implode( '-', $id );
		
		return gce_print_grid( $id, null, 25 );
		
		// TODO with new code need to have a different display function so we can wrap the output correctly
		//return '<div class="gce-page-grid" id="gce-page-grid">' . $display->get_grid() . '</div>';
	}
	
	return '';
}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );



function gce_print_grid( $feed_ids, $title_text, $max_events, $month = null, $year = null ) {
		//require_once 'inc/gce-parser.php';

		$ids = explode( '-', $feed_ids );
		
		//echo 'FEED IDs: ' . $feed_ids . '<br>';

		//Create new GCE_Parser object, passing array of feed id(s) returned from gce_get_feed_ids()
		$grid = new GCE_Display( $ids, $title_text, $max_events );

		//$num_errors = $grid->get_num_errors();

		//If there are less errors than feeds parsed, at least one feed must have parsed successfully so continue to display the grid
		//if ( $num_errors < count( $ids ) ) {
			$feed_ids = esc_attr( $feed_ids );
			$title_text = isset( $title_text ) ? esc_html( $title_text) : 'null';

			$markup = '<div class="gce-page-grid" id="gce-page-grid-' . $feed_ids . '">';

			//Add AJAX script if required
			//if ( $ajaxified )
				$markup .= '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-page-grid-' . $feed_ids . '", "' . $feed_ids . '", "' . absint( $max_events ) . '", "' . $title_text . '", "page");});</script>';

			$markup .= $grid->get_grid( $year, $month, true ) . '</div>';

			//If there was at least one error, return the grid markup with an error message (for admins only)
			//if ( $num_errors > 0 && current_user_can( 'manage_options' ) )
			//	return $grid->error_messages() . $markup;

			//Otherwise just return the grid markup
			return $markup;
		//} else {
			//If current user is an admin, display an error message explaining problem. Otherwise, display a 'nice' error messsage
		//	if ( current_user_can( 'manage_options' ) ) {
		//		return $grid->error_messages();
		//	} else {
		//		$options = get_option( GCE_GENERAL_OPTIONS_NAME );
		//		return wp_kses_post( $options['error'] );
		//	}
		//}
	}
