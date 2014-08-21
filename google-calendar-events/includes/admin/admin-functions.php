<?php
/**
 * Admin helper functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

/**
 * Function to clear the cache out
 * 
 * @since 2.0.0
 */
function gce_clear_cache( $id ) {
	
	delete_transient( 'gce_feed_' . $id );
	
	add_settings_error( 'gce-notices', 'gce-cache-updated', __( 'Cache has been cleared for this feed.', 'gce' ), 'updated' );
}

function gce_default_editor_content( $content, $post ) {
	
	if( $post->post_type == 'gce_feed' ) {
		$content  = '<div class="gce-list-event gce-tooltip-event">[event-title]</div>' . "\n";
		$content .= '<div><span>Starts:</span> [start-time]</div>' . "\n";
		$content .= '<div><span>Ends:</span> [end-date] - [end-time]</div>' . "\n";
		$content .= '[if-location]<div><span>Location:</span> [location]</div>[/if-location]' . "\n";
		$content .= '[if-description]<div><span>Description:</span> [description]</div>[/if-description]' . "\n";
		$content .= '<div>[link newwindow="true"]More details...[/link]</div>' . "\n";
	}
	
	return $content;
}
add_filter( 'default_content', 'gce_default_editor_content', 10, 2 );
