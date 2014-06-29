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
		
		$display = new GCE_Display( explode( ',', $id ) );

		return '<div class="gce-page-grid" id="gce-page-grid">' . $display->get_grid() . '</div>';
	}
	
	return '';
}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );
