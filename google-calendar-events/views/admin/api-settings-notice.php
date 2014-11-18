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
	#gce-install-notice .button-primary,
	#gce-install-notice .button-secondary {
		margin-left: 15px;
	}
</style>

<div id="gce-install-notice" class="updated">
	<p>
		<?php echo 'TODO: Content'; ?>
		
		<a href="<?php echo admin_url( 'edit.php?post_type=gce_feed&page=google-calendar-events_general_settings' ); ?>" class="button-primary"><?php _e( 'Setup your API Settings now', 'gce' ); ?></a>
		<a href="<?php echo add_query_arg( 'gce-dismiss-install-nag', 1 ); ?>" class="button-secondary"><?php _e( 'Hide this', 'gce' ); ?></a>
	</p>
</div>