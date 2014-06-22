<?php


function gce_feed_shortcode( $attr ) {
	
	extract( shortcode_atts( array(
					'id' => null
				), $attr, 'gce_feed' ) );
	
	if( ! empty( $id ) ) {
		$feed = new GCE_Feed( $id );
		
		wp_localize_script( GCE_PLUGIN_SLUG . '-public', 'gce', array( 'url' => 'https://www.google.com/calendar/feeds/qs39fk8m91po76l92norrgr2b8%40group.calendar.google.com/public/basic' ) );
		
		return $feed->display();
	}
	
	return '';

}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );