<?php


	global $post;
	
	$post_id = $post->ID;
	
	if( isset( $_GET['clear_cache'] ) && $_GET['clear_cache'] == 1 ) {
		delete_transient( 'gce_feed_' . $post_id );
		echo 'The cache for this feed has been cleared.'; // TODO Turn this into an admin notice message if I can
	}
	
	$gce_feed_url         = get_post_meta( $post->ID, 'gce_feed_url', true );
	$gce_retrieve_from    = get_post_meta( $post->ID, 'gce_retrieve_from', true );
	$gce_retrieve_until   = get_post_meta( $post->ID, 'gce_retrieve_until', true );
	$gce_retrieve_max     = get_post_meta( $post->ID, 'gce_retrieve_max', true );
	$gce_date_format      = get_post_meta( $post->ID, 'gce_date_format', true );
	$gce_time_format      = get_post_meta( $post->ID, 'gce_time_format', true );
	//$gce_timezone_offset  = get_post_meta( $post->ID, 'gce_timezone_offset', true );
	$gce_cache            = get_post_meta( $post->ID, 'gce_cache', true );
	$gce_multi_day_events = get_post_meta( $post->ID, 'gce_multi_day_events', true );
	$gce_display_mode     = get_post_meta( $post->ID, 'gce_display_mode', true );
?>


<div class="gce-meta-cotrol">
	<a href="<?php echo add_query_arg( array( 'clear_cache' => true ) ); ?>" class="button-secondary">Clear Cache</a>
</div>
<br>
<div class="gce-meta-control">
	<strong>Feed ID:</strong> <?php echo $post_id; ?><br />
	<strong>Feed Shortcode:</strong> [gce-feed id="<?php echo $post_id; ?>"]
</div>

<div class="gce-meta-control">
	<label>Feed URL</label>
	<input type="text" class="" name="gce_feed_url" id="gce_feed_url" value="<?php echo $gce_feed_url; ?>" />
</div>

<div class="gce-meta-control">
	<label>Retrieve Events From</label>
	<select name="gce_retrieve_from">
		<option value="now" <?php selected( $gce_retrieve_from, 'now', true ); ?>>Now</option>
		<option value="today" <?php selected( $gce_retrieve_from, 'today', true ); ?>>00:00 Today</option>
		<option value="start_week" <?php selected( $gce_retrieve_from, 'start_week', true ); ?>>Start of current week</option>
		<option value="start_month" <?php selected( $gce_retrieve_from, 'start_month', true ); ?>>Start of current month</option>
		<option value="end_month" <?php selected( $gce_retrieve_from, 'end_month', true ); ?>>End of current month</option>
		<option value="start_time" <?php selected( $gce_retrieve_from, 'start_time', true ); ?>>The beginning of time</option>
		<!-- maybe take out the specific date / time option?
		<option name="" id="" value="">Specific date / time</option>
		-->
	</select>
	<!-- If we take out specific date/time option then we don't need this input box
	<input type="text" class="" name="gce_retrieve_from" id="gce_retrieve_from" value="<?php echo $gce_retrieve_from; ?>" />
	-->
</div>

<div class="gce-meta-control">
	<label>Retrieve Events Until</label>
	<select name="gce_retrieve_until">
		<option value="now" <?php selected( $gce_retrieve_until, 'now', true ); ?>>Now</option>
		<option value="today" <?php selected( $gce_retrieve_until, 'today', true ); ?>>00:00 Today</option>
		<option value="start_week" <?php selected( $gce_retrieve_until, 'start_week', true ); ?>>Start of current week</option>
		<option value="start_month" <?php selected( $gce_retrieve_until, 'start_month', true ); ?>>Start of current month</option>
		<option value="end_month" <?php selected( $gce_retrieve_until, 'end_month', true ); ?>>End of current month</option>
		<option value="end_time" <?php selected( $gce_retrieve_until, 'end_time', true ); ?>>The end of time</option>
		<!-- maybe take out the specific date / time option?
		<option name="" id="" value="">Specific date / time</option>
		-->
	</select>
	<!-- If we take out specific date/time option then we don't need this input box
	<input type="text" class="" name="gce_retrieve_until" id="gce_retrieve_until" value="<?php echo $gce_retrieve_until; ?>" />
	-->
</div>

<div class="gce-meta-control">
	<label>Maximum Number of Events to Retrieve</label>
	<input type="text" class="" name="gce_retrieve_max" id="gce_retrieve_max" value="<?php echo $gce_retrieve_max; ?>" />
</div>

<div class="gce-meta-control">
	<label>Date Format</label>
	<input type="text" class="" name="gce_date_format" id="gce_date_format" value="<?php echo $gce_date_format; ?>" />
</div>

<div class="gce-meta-control">
	<label>Time Format</label>
	<input type="text" class="" name="gce_time_format" id="gce_time_format" value="<?php echo $gce_time_format; ?>" />
</div>

<div class="gce-meta-control">
	<label>Timezone Adjustment</label>
	<?php echo gce_add_timezone_field(); ?>
</div>

<div class="gce-meta-control">
	<label>Cache Duration</label>
	<input type="text" class="" name="gce_cache" id="gce_cache" value="<?php echo $gce_cache; ?>" />
</div>

<div class="gce-meta-control">
	<label>Multiple Day Events</label>
	<input type="checkbox" name="gce_multi_day_events" id="gce_multi_day_events" value="1" <?php checked( $gce_multi_day_events, '1' ); ?> /> Show on each day
</div>

<div class="gce-meta-control">
	<label>Display Mode</label>
	<select name="gce_display_mode">
		<option value="grid" <?php selected( $gce_display_mode, 'grid', true ); ?>>Grid</option>
		<option value="list" <?php selected( $gce_display_mode, 'list', true ); ?>>List</option>
		<option value="list-grouped" <?php selected( $gce_display_mode, 'list-grouped', true ); ?>>Grouped List</option>
	</select>
</div>


<?php
//Timezone offset
function gce_add_timezone_field() {
	global $post;
	
	$gce_timezone_offset  = get_post_meta( $post->ID, 'gce_timezone_offset', true );
	
	require_once 'timezone-choices.php';
	$timezone_list = gce_get_timezone_choices();
	//Set selected="selected" for default option
	if( ! empty( $gce_timezone_offset ) ) {
		$timezone_list = str_replace(('<option value="' . $gce_timezone_offset . '"'), ('<option value="' . $gce_timezone_offset . '" selected="selected"'), $timezone_list);
	} else {
		$timezone_list = str_replace( '<option value="default">Default</option>', '<option value="default" selected="selected">Default</option>', $timezone_list );
	}
	
	return $timezone_list;

}


