<?php
	
	global $post;
	
	$gce_feed_url         = get_post_meta( $post->ID, 'gce_feed_url', true );
	$gce_retrieve_from    = get_post_meta( $post->ID, 'gce_retrieve_from', true );
	$gce_retrieve_until   = get_post_meta( $post->ID, 'gce_retrieve_until', true );
	$gce_retrieve_max     = get_post_meta( $post->ID, 'gce_retrieve_max', true );
	$gce_date_format      = get_post_meta( $post->ID, 'gce_date_format', true );
	$gce_time_format      = get_post_meta( $post->ID, 'gce_time_format', true );
	$gce_timezone         = get_post_meta( $post->ID, 'gce_timezone', true );
	$gce_cache            = get_post_meta( $post->ID, 'gce_cache', true );
	$gce_multi_day_events = get_post_meta( $post->ID, 'gce_multi_day_events', true );
?>

<div class="gce-meta-control">
	<strong>Feed ID:</strong> {ID}<br />
	<strong>Feed Shortcode:</strong> {shortcode}
</div>

<div class="gce-meta-control">
	<label>Feed URL</label>
	<input type="text" class="" name="gce_feed_url" id="gce_feed_url" value="<?php echo $gce_feed_url; ?>" />
</div>

<div class="gce-meta-control">
	<label>Retrieve Events From</label>
	<select>
		<option name="" id="" value="">Now</option>
		<option name="" id="" value="">00:00 Today</option>
		<option name="" id="" value="">Start of current week</option>
		<option name="" id="" value="">Start of current month</option>
		<option name="" id="" value="">End of current month</option>
		<option name="" id="" value="">The beginning of time</option>
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
	<select>
		<option name="" id="" value="">Now</option>
		<option name="" id="" value="">00:00 Today</option>
		<option name="" id="" value="">Start of current week</option>
		<option name="" id="" value="">Start of current month</option>
		<option name="" id="" value="">End of current month</option>
		<option name="" id="" value="">The end of time</option>
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
	<input type="text" class="" name="gce_timezone" id="gce_timezone" value="<?php echo $gce_timezone; ?>" />
</div>

<div class="gce-meta-control">
	<label>Cache Duration</label>
	<input type="text" class="" name="gce_cache" id="gce_cache" value="<?php echo $gce_cache; ?>" />
</div>

<div class="gce-meta-control">
	<label>Multiple Day Events</label>
	<input type="checkbox" name="gce_multi_day_events" id="gce_multi_day_events" value="yes" <?php checked( $gce_multi_day_events, 'yes' ); ?> /> Show on each day
</div>
