<?php

function gce_clear_cache( $id ) {
	
	delete_transient( 'gce_feed_' . $id );
	
	add_settings_error( 'gce-notices', 'gce-cache-updated', __( 'Cache has been cleared for this feed.', 'gce' ), 'updated' );
}
