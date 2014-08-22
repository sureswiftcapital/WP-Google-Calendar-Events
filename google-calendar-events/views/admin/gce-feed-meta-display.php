<?php

/**
 * Display for Feed Custom Post Types
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

	global $post;
	
	$post_id = $post->ID;
	
	// Clear the cache if the button was clicked to do so
	if( isset( $_GET['clear_cache'] ) && $_GET['clear_cache'] == 1 ) {
		gce_clear_cache( $post_id );
	}
	
	// Load up all post meta data
	$gce_feed_url         = get_post_meta( $post->ID, 'gce_feed_url', true );
	$gce_retrieve_from    = get_post_meta( $post->ID, 'gce_retrieve_from', true );
	$gce_retrieve_until   = get_post_meta( $post->ID, 'gce_retrieve_until', true );
	$gce_retrieve_max     = get_post_meta( $post->ID, 'gce_retrieve_max', true );
	$gce_date_format      = get_post_meta( $post->ID, 'gce_date_format', true );
	$gce_time_format      = get_post_meta( $post->ID, 'gce_time_format', true );
	$gce_cache            = get_post_meta( $post->ID, 'gce_cache', true );
	$gce_multi_day_events = get_post_meta( $post->ID, 'gce_multi_day_events', true );
	$gce_display_mode     = get_post_meta( $post->ID, 'gce_display_mode', true );
	$gce_custom_from      = get_post_meta( $post->ID, 'gce_custom_from', true );
	$gce_custom_until     = get_post_meta( $post->ID, 'gce_custom_until', true );
	$gce_search_query     = get_post_meta( $post->ID, 'gce_search_query', true );
	$gce_expand_recurring = get_post_meta( $post->ID, 'gce_expand_recurring', true );
?>


<div class="gce-meta-cotrol">
	<a href="<?php echo add_query_arg( array( 'clear_cache' => true ) ); ?>" class="button-secondary"><?php _e( 'Clear Cache', 'gce' ); ?></a>
</div>
<br>
<div class="gce-meta-control">
	<strong><?php _e( 'Feed ID:', 'gce' ); ?></strong> <?php echo $post_id; ?><br />
	<strong><?php _e( 'Feed Shortcode:', 'gce' ); ?></strong> [gcal id="<?php echo $post_id; ?>"]
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Feed URL', 'gce' ); ?></label>
	<input type="text" class="large-text" name="gce_feed_url" id="gce_feed_url" value="<?php echo $gce_feed_url; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Search Query', 'gce' ); ?></label>
	<input type="text" class="" name="gce_search_query" id="gce_search_query" value="<?php echo $gce_search_query; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Expand Recurring Events?', 'gce' ); ?></label>
	<input type="checkbox" name="gce_expand_recurring" id="gce_expand_recurring" value="1" <?php checked( $gce_expand_recurring, '1' ); ?> /> <?php _e( 'Yes', 'gce' ); ?>
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Retrieve Events From', 'gce' ); ?></label>
	<select name="gce_retrieve_from" id="gce_retrieve_from">
		<option value="today" <?php selected( $gce_retrieve_from, 'today', true ); ?>><?php _e( 'Today', 'gce' ); ?></option>
		<option value="start_week" <?php selected( $gce_retrieve_from, 'start_week', true ); ?>><?php _e( 'Start of current week', 'gce' ); ?></option>
		<option value="start_month" <?php selected( $gce_retrieve_from, 'start_month', true ); ?>><?php _e( 'Start of current month', 'gce' ); ?></option>
		<option value="end_month" <?php selected( $gce_retrieve_from, 'end_month', true ); ?>><?php _e( 'End of current month', 'gce' ); ?></option>
		<option value="start_time" <?php selected( $gce_retrieve_from, 'start_time', true ); ?>><?php _e( 'The beginning of time', 'gce' ); ?></option>
		<option value="custom_date" <?php selected( $gce_retrieve_from, 'custom_date', true ); ?>><?php _e( 'Specific date', 'gce' ); ?></option>
	</select>
	<input type="text" <?php echo ( $gce_retrieve_from != 'custom_date' ? 'class="gce-admin-hidden" ' : ' ' ); ?> name="gce_custom_from" id="gce_custom_from" value="<?php echo $gce_custom_from; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Retrieve Events Until', 'gce' ); ?></label>
	<select name="gce_retrieve_until" id="gce_retrieve_until">
		<option value="today" <?php selected( $gce_retrieve_until, 'today', true ); ?>><?php _e( 'Today', 'gce' ); ?></option>
		<option value="start_week" <?php selected( $gce_retrieve_until, 'start_week', true ); ?>><?php _e( 'Start of current week', 'gce' ); ?></option>
		<option value="start_month" <?php selected( $gce_retrieve_until, 'start_month', true ); ?>><?php _e( 'Start of current month', 'gce' ); ?></option>
		<option value="end_month" <?php selected( $gce_retrieve_until, 'end_month', true ); ?>><?php _e( 'End of current month', 'gce' ); ?></option>
		<option value="end_time" <?php selected( $gce_retrieve_until, 'end_time', true ); ?>><?php _e( 'The end of time', 'gce' ); ?></option>
		<option value="custom_date" <?php selected( $gce_retrieve_until, 'custom_date', true ); ?>><?php _e( 'Specific date', 'gce' ); ?></option>
	</select>
	<input type="text" <?php echo ( $gce_retrieve_until != 'custom_date' ? 'class="gce-admin-hidden" ' : ' ' ); ?> name="gce_custom_until" id="gce_custom_until" value="<?php echo $gce_custom_until; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Maximum Number of Events to Retrieve', 'gce' ); ?></label>
	<input type="text" class="" name="gce_retrieve_max" id="gce_retrieve_max" value="<?php echo $gce_retrieve_max; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Date Format', 'gce' ); ?></label>
	<input type="text" class="" name="gce_date_format" id="gce_date_format" value="<?php echo $gce_date_format; ?>" />
	<span class="description"><?php _x( '(Leave blank to use the default)', 'References the Date Format option', 'gce' ); ?></span>
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Time Format', 'gce' ); ?></label>
	<input type="text" class="" name="gce_time_format" id="gce_time_format" value="<?php echo $gce_time_format; ?>" />
	<span class="description"><?php _x( '(Leave blank to use the default)', 'References the Time Format option', 'gce' ); ?></span>
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Timezone Adjustment', 'gce' ); ?></label>
	<?php echo gce_add_timezone_field(); ?>
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Cache Duration', 'gce' ); ?></label>
	<input type="text" class="" name="gce_cache" id="gce_cache" value="<?php echo $gce_cache; ?>" />
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Multiple Day Events', 'gce' ); ?></label>
	<input type="checkbox" name="gce_multi_day_events" id="gce_multi_day_events" value="1" <?php checked( $gce_multi_day_events, '1' ); ?> /> <?php _e( 'Show on each day', 'gce' ); ?>
</div>

<div class="gce-meta-control">
	<label><?php _e( 'Display Mode', 'gce' ); ?></label>
	<select name="gce_display_mode">
		<option value="grid" <?php selected( $gce_display_mode, 'grid', true ); ?>><?php _e( 'Grid', 'gce' ); ?></option>
		<option value="list" <?php selected( $gce_display_mode, 'list', true ); ?>><?php _e( 'List', 'gce' ); ?></option>
		<option value="list-grouped" <?php selected( $gce_display_mode, 'list-grouped', true ); ?>><?php _e( 'Grouped List', 'gce' ); ?></option>
	</select>
</div>


<?php
/**
 * Since we have a huge list of Timezones we use this to grab them 
 * 
 * @since 2.0.0
 */
function gce_add_timezone_field() {
	global $post;
	
	$gce_timezone_offset  = get_post_meta( $post->ID, 'gce_timezone_offset', true );
	
	require_once( 'timezone-choices.php' );
	
	$timezone_list = gce_get_timezone_choices();
	
	//Set selected="selected" for default option
	if( ! empty( $gce_timezone_offset ) ) {
		$timezone_list = str_replace(('<option value="' . $gce_timezone_offset . '"'), ('<option value="' . $gce_timezone_offset . '" selected="selected"'), $timezone_list);
	} else {
		$timezone_list = str_replace( '<option value="default">Default</option>', '<option value="default" selected="selected">Default</option>', $timezone_list );
	}
	
	return $timezone_list;

}


