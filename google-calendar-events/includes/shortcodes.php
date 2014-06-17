<?php


function gce_feed_shortcode( $attr ) {
	
	extract( shortcode_atts( array(
					'id' => null
				), $attr, 'gce_feed' ) );
	
	if( ! empty( $id ) ) {
		$feed = new GCE_Feed( $id );
		
		return '<pre>' . $feed->display() . '</pre>';
	}
	
	return '';

}
add_shortcode( 'gce-feed', 'gce_feed_shortcode' );