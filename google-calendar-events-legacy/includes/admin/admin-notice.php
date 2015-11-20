<?php

/**
 * Show notice for API Settings
 *
 * @package    GCE
 * @subpackage admin/views
 * @author     Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<style>
	#gce-install-notice .button-secondary {
		margin-left: 15px;
	}
</style>

<div id="gce-install-notice" class="updated">
	<p>
		<h3><?php _e( 'GCal API Key Notice', 'gce' ); ?></h3>
	
		<?php _e( 'GCal Events uses the Google Calendar API version 3. By default this plugin uses a public shared key across all plugin users.', 'gce' ); ?>
		<br/><br/>
		<?php _e( 'This key is limited to 500,000 requests per day and 5 requests per second. To avoid running into any potential limits you can use your own Google API key.', 'gce' ); ?>
	</p>
	<p>
		<a href="<?php echo admin_url( 'edit.php?post_type=gce_feed&page=google-calendar-events_general_settings' ); ?>" class="button-primary"><?php _e( 'Enter your GCal API key', 'gce' ); ?></a>
		<a href="<?php echo admin_url( 'edit.php?post_type=gce_feed' ); ?>" class="button-secondary"><?php _e( 'Configure GCal feeds', 'gce' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'gce-dismiss-install-nag', 1 ) ); ?>" class="button-secondary"><?php _e( 'Hide this', 'gce' ); ?></a>
	</p>
</div>