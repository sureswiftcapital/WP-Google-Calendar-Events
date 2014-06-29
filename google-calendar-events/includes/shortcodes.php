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
		
		$id = explode( ',', $id );
		$id = implode( '-', $id );
		
		return gce_print_grid( $id, null, 25 );
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
