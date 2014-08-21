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
		$content = 'This is the default text';
	}
	
	return $content;
}
add_filter( 'default_content', 'gce_default_editor_content', 10, 2 );
