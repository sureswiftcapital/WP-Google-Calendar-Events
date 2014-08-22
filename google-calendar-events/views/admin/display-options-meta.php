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
		<p>
			<input type="checkbox" name="gce_display_simple" value="1" <?php checked( $gce_display_simple, '1' ); ?> />
			<?php _e( 'Use the simple display options below instead of the Event Builder', 'gce' ); ?>
		</p>
	</div>
	
	<div class="gce-meta-control">
		<label><?php _e( 'Start date / time display', 'gce' ); ?></label>
		<span class="description"><?php _e( 'Select how to display the start date / time.', 'gce' ); ?></span>
		<select name="gce_display_start">
			<option value="none" <?php selected( $gce_display_start, 'none', true ); ?>><?php _e( 'None', 'gce' ); ?></option>
			<option value="time" <?php selected( $gce_display_start, 'time', true ); ?>><?php _e( 'Start time', 'gce' ); ?></option>
			<option value="date" <?php selected( $gce_display_start, 'date', true ); ?>><?php _e( 'Start date', 'gce' ); ?></option>
			<option value="time-date" <?php selected( $gce_display_start, 'time-date', true ); ?>><?php _e( 'Start time and date', 'gce' ); ?></option>
			<option value="date-time" <?php selected( $gce_display_start, 'date-time', true ); ?>><?php _e( 'Start date and time', 'gce' ); ?></option>
		</select>
		<span class="description"><?php _e( 'Text to display before the start time.', 'gce' ); ?></span>
		<input type="text" name="gce_display_start_text" value="<?php echo $gce_display_start_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label><?php _e( 'End time/date display', 'gce' ); ?></label>
		<span class="description"><?php _e( 'Select how to display the end date / time.', 'gce' ); ?></span>
		<select name="gce_display_end">
			<option value="none" <?php selected( $gce_display_end, 'none', true ); ?>><?php _e( 'None', 'gce' ); ?></option>
			<option value="time" <?php selected( $gce_display_end, 'time', true ); ?>><?php _e( 'End time', 'gce' ); ?></option>
			<option value="date" <?php selected( $gce_display_end, 'date', true ); ?>><?php _e( 'End date', 'gce' ); ?></option>
			<option value="time-date" <?php selected( $gce_display_end, 'time-date', true ); ?>><?php _e( 'End time and date', 'gce' ); ?></option>
			<option value="date-time" <?php selected( $gce_display_end, 'date-time', true ); ?>><?php _e( 'End date and time', 'gce' ); ?></option>
		</select>
		<span class="description"><?php _e( 'Text to display before the end time.', 'gce' ); ?></span>
		<input type="text" name="gce_display_end_text" value="<?php echo $gce_display_end_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label><?php _e( 'Separator', 'gce' ); ?></label>
		<span class="description">
			<?php _e( 'If you have chosen to display both the time and date above, enter the text / characters to display between the time and date here (including any spaces).' , 'gce' ); ?>
		</span>
		<input type="text" name="gce_display_separator" value="<?php echo $gce_display_separator; ?>" />
	</div>

	<div class="gce-meta-control">
		<label>Location</label>
		<p><input type="checkbox" name="gce_display_location" value="1" <?php checked( $gce_display_location, '1' ); ?> />Show the location of events?</p>
		<span class="description">Text to display before the location.</span>
		<input type="text" name="gce_display_location_text" value="<?php echo $gce_display_location_text; ?>" />
	</div>

	<div class="gce-meta-control">
		<label><?php _e( 'Description', 'gce' ); ?></label>
		<p>
			<input type="checkbox" name="gce_display_description" value="1" <?php checked( $gce_display_description, '1' ); ?> />
			<?php _e( 'Show the description of events? (URLs in the description will be made into links).', 'gce' ); ?>
		</p>
		<span class="description"><?php _e( 'Text to display before the description.', 'gce' ); ?></span>
		<input type="text" name="gce_display_description_text" value="<?php echo $gce_display_description_text; ?>" />
		<span class="description"><?php _e( 'Maximum number of words to show from description. Leave blank for no limit.', 'gce' ); ?></span>
		<input type="text" name="gce_display_description_max" value="<?php echo $gce_display_description_max; ?>" />
	</div>

	<div class="gce-meta-control">
		<label><?php _e( 'Event Link', 'gce' ); ?></label>
		<p>
			<input type="checkbox" name="gce_display_link" value="1" <?php checked( $gce_display_link, '1' ); ?> />
			<?php _e( 'Show a link to the Google Calendar page for an event?', 'gce' ); ?>
		</p>
		<p>
			<input type="checkbox" name="gce_display_link_tab" value="1" <?php checked( $gce_display_link_tab, '1' ); ?> />
			<?php _e( 'Links open in a new window / tab?', 'gce' ); ?>
		</p>
		<span class="description"><?php _e( 'The link text to be displayed.', 'gce' ); ?></span>
		<input type="text" name="gce_display_link_text" value="<?php echo $gce_display_link_text; ?>" />
	</div>
</div>
