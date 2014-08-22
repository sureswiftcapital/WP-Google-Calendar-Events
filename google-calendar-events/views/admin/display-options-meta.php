<?php

/**
 * Display Options Post Meta Output
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

	global $post;
	
	$post_id = $post->ID;
	
	// Load up all post meta data
	$gce_display_start            = get_post_meta( $post->ID, 'gce_display_start', true );
	$gce_display_start_text       = get_post_meta( $post->ID, 'gce_display_start_text', true );
	$gce_display_end              = get_post_meta( $post->ID, 'gce_display_end', true );
	$gce_display_end_text         = get_post_meta( $post->ID, 'gce_display_end_text', true );
	$gce_display_separator        = get_post_meta( $post->ID, 'gce_display_separator', true );
	$gce_display_location         = get_post_meta( $post->ID, 'gce_display_location', true );
	$gce_display_location_text    = get_post_meta( $post->ID, 'gce_display_location_text', true );
	$gce_display_description      = get_post_meta( $post->ID, 'gce_display_description', true );
	$gce_display_description_text = get_post_meta( $post->ID, 'gce_display_description_text', true );
	$gce_display_description_max  = get_post_meta( $post->ID, 'gce_display_description_max', true );
	$gce_display_link             = get_post_meta( $post->ID, 'gce_display_link', true );
	$gce_display_link_tab         = get_post_meta( $post->ID, 'gce_display_link_tab', true );
	$gce_display_link_text        = get_post_meta( $post->ID, 'gce_display_link_text', true );
	$gce_display_simple           = get_post_meta( $post->ID, 'gce_display_simple', true );
	
?>


<div id="gce-display-options-wrap">
	<div class="gce-meta-control">
		<p><input type="checkbox" name="gce_display_simple" value="1" <?php checked( $gce_display_simple, '1' ); ?> />Use the simple display options below instead of the Event Builder</p>
	</div>
	
	<div class="gce-meta-control">
		<label>Start date / time display</label>
		<span class="description">Select how to display the start date / time.</span>
		<select name="gce_display_start">
			<option value="none" <?php selected( $gce_display_start, 'none', true ); ?>>None</option>
			<option value="time" <?php selected( $gce_display_start, 'time', true ); ?>>Start time</option>
			<option value="date" <?php selected( $gce_display_start, 'date', true ); ?>>Start date</option>
			<option value="time-date" <?php selected( $gce_display_start, 'time-date', true ); ?>>Start time and date</option>
			<option value="date-time" <?php selected( $gce_display_start, 'date-time', true ); ?>>Start date and time</option>
		</select>
		<span class="description">Text to display before the start time.</span>
		<input type="text" name="gce_display_start_text" value="<?php echo $gce_display_start_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>End time/date display</label>
		<span class="description">Select how to display the end date / time.</span>
		<select name="gce_display_end">
			<option value="none" <?php selected( $gce_display_end, 'none', true ); ?>>None</option>
			<option value="time" <?php selected( $gce_display_end, 'time', true ); ?>>End time</option>
			<option value="date" <?php selected( $gce_display_end, 'date', true ); ?>>End date</option>
			<option value="time-date" <?php selected( $gce_display_end, 'time-date', true ); ?>>End time and date</option>
			<option value="date-time" <?php selected( $gce_display_end, 'date-time', true ); ?>>End date and time</option>
		</select>
		<span class="description">Text to display before the end time.</span>
		<input type="text" name="gce_display_end_text" value="<?php echo $gce_display_end_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>Separator</label>
		<span class="description">If you have chosen to display both the time and date above, enter the text / characters to display between the time and date here (including any spaces).</span>
		<input type="text" name="gce_display_separator" value="<?php echo $gce_display_separator; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>Location</label>
		<p><input type="checkbox" name="gce_display_location" value="1" <?php checked( $gce_display_location, '1' ); ?> />Show the location of events?</p>
		<span class="description">Text to display before the location.</span>
		<input type="text" name="gce_display_location_text" value="<?php echo $gce_display_location_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>Description</label>
		<p><input type="checkbox" name="gce_display_description" value="1" <?php checked( $gce_display_description, '1' ); ?> />Show the description of events? (URLs in the description will be made into links).</p>
		<span class="description">Text to display before the description.</span>
		<input type="text" name="gce_display_description_text" value="<?php echo $gce_display_description_text; ?>" />
		<span class="description">Maximum number of words to show from description. Leave blank for no limit.</span>
		<input type="text" name="gce_display_description_max" value="<?php echo $gce_display_description_max; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>Event Link</label>
		<p><input type="checkbox" name="gce_display_link" value="1" <?php checked( $gce_display_link, '1' ); ?> />Show a link to the Google Calendar page for an event?</p>
		<p><input type="checkbox" name="gce_display_link_tab" value="1" <?php checked( $gce_display_link_tab, '1' ); ?> />Links open in a new window / tab?</p>
		<span class="description">The link text to be displayed.</span>
		<input type="text" name="gce_display_link_text" value="<?php echo $gce_display_link_text; ?>" />
	</div>
</div>
