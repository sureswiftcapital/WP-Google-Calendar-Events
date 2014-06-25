<?php


function gce_feed_shortcode( $attr ) {

	extract( shortcode_atts( array(
					'id' => null,
					'display' => null
				), $attr, 'gce_feed' ) );
	
	if( ! empty( $id ) ) {
		$feed = new GCE_Feed( $id );
		
		wp_localize_script( GCE_PLUGIN_SLUG . '-public', 'gce', 
				array( 
					//'url' => 'https://www.google.com/calendar/feeds/qs39fk8m91po76l92norrgr2b8%40group.calendar.google.com/public/basic',
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				) );
		
		if( empty( $display ) ) {
			
			$display_mode = get_post_meta( $id, 'gce_display_mode', true );
					
			$display = ( ! empty( $display_mode ) ? $display_mode : 'grid' );
		}
		
		//echo 'DISPLAY: ' . $display . '<br>';
		return $feed->display( $display );
	}
	
	return '';

}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );