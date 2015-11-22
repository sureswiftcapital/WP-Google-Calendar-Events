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
	$gce_feed_url                    = get_post_meta( $post->ID, 'gce_feed_url', true );
	$gce_date_format                 = get_post_meta( $post->ID, 'gce_date_format', true );
	$gce_time_format                 = get_post_meta( $post->ID, 'gce_time_format', true );
	$gce_cache                       = get_post_meta( $post->ID, 'gce_cache', true );
	$gce_multi_day_events            = get_post_meta( $post->ID, 'gce_multi_day_events', true );
	$gce_display_mode                = get_post_meta( $post->ID, 'gce_display_mode', true );
	$gce_search_query                = get_post_meta( $post->ID, 'gce_search_query', true );
	$gce_expand_recurring            = get_post_meta( $post->ID, 'gce_expand_recurring', true );
	$gce_paging                      = get_post_meta( $post->ID, 'gce_paging', true );
	$gce_events_per_page             = get_post_meta( $post->ID, 'gce_events_per_page', true );
	$gce_per_page_num                = get_post_meta( $post->ID, 'gce_per_page_num', true );
	$gce_list_start_offset_num       = get_post_meta( $post->ID, 'gce_list_start_offset_num', true );
	$gce_list_start_offset_direction = get_post_meta( $post->ID, 'gce_list_start_offset_direction', true );
	$gce_feed_start                  = get_post_meta( $post->ID, 'gce_feed_start', true );
	$gce_feed_start_num              = get_post_meta( $post->ID, 'gce_feed_start_num', true );
	$gce_feed_end                    = get_post_meta( $post->ID, 'gce_feed_end', true );
	$gce_feed_end_num                = get_post_meta( $post->ID, 'gce_feed_end_num', true );
	$gce_feed_range_start            = get_post_meta( $post->ID, 'gce_feed_range_start', true );
	$gce_feed_range_end              = get_post_meta( $post->ID, 'gce_feed_range_end', true );

	$range = selected( $gce_display_mode, 'date-range-list', false ) || selected( $gce_display_mode, 'date-range-grid', false );
	$use_range = ( $range ? true : false );

	if( empty( $gce_events_per_page ) ) {
		$gce_events_per_page = 'days';
	}

	$gce_show_tooltips               = get_post_meta( $post->ID, 'gce_show_tooltips', true );

	if( empty( $gce_list_start_offset_num ) ) {
		$gce_list_start_offset_num = 0;
	}

	if( empty( $gce_feed_start ) ) {
		$gce_feed_start = 'years';
	}

	if( empty( $gce_feed_end ) ) {
		$gce_feed_end = 'years';
	}

	if( empty( $gce_per_page_num ) ) {
		$gce_per_page_num = 7;
	}
?>

<div id="gce-admin-promo">
	<?php echo __( 'Want to be in the know?', 'gce' ); ?>
	<strong>
		<a href="https://www.getdrip.com/forms/9434542/submissions/new" target="_blank">
			<?php echo __( 'Get notified of important updates', 'gce' ); ?>
		</a>
	</strong>
</div>

<table class="form-table">
	<tr>
		<th scope="row"><?php _e( 'Feed Shortcode', 'gce' ); ?></th>
		<td>
			<input
				name="gce_shortcode"
				class="gce-shortcode"
			    readonly="readonly"
			    value='[gcal id="<?php echo $post_id; ?>"]'
				onclick="this.select();"
				/>
			<span class="gce-shortcode-copied"></span>
			<p class="description">
				<?php _e( 'Copy and paste this shortcode to display this Google Calendar feed on any post or page.', 'gce' ); ?>
				<?php _e( 'To avoid display issues, make sure to paste the shortcode in the Text tab of the post editor.', 'gce' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="gce_feed_url"><?php _e( 'Google Calendar ID', 'gce' ); ?></label></th>
		<td>
			<input type="text" class="regular-text" style="width: 30em;" name="gce_feed_url" id="gce_feed_url" value="<?php echo esc_attr( $gce_feed_url ); ?>" />
			<p class="description">
				<?php _e( 'The Google Calendar ID.', 'gce' ); ?> <?php _e( 'Example', 'gce' ); ?>:<br/>
				<code>umsb0ekhivs1a2ubtq6vlqvcjk@group.calendar.google.com</code><br/>
				<a href="<?php echo gce_ga_campaign_url( 'http://wpdocs.philderksen.com/google-calendar-events/getting-started/find-calendar-id/', 'gce_lite', 'settings_link', 'docs' ); ?>" target="_blank"><?php _e( 'How to find your Google Calendar ID', 'gce' ); ?></a>
			</p>
		</td>
	</tr>

	<?php

	$timezone_wordpress = gce_get_wp_timezone();
	$timezone_default = $timezone_wordpress ? $timezone_wordpress : 'UTC';
	$timezone_setting = esc_attr( get_post_meta( $post->ID, '_feed_timezone_setting', true ) );

	?>
	<tr>
		<th scope="row"><label for="gce_feed_url"><?php _e( 'Timezone', 'gce' ); ?></label></th>
		<td>
			<select name="_feed_timezone_setting" id="_feed_timezone_setting">
				<option value="use_calendar" <?php selected( 'use_calendar', $timezone_setting, true ); ?>><?php _ex( 'Calendar default', 'Use the calendar default setting', 'gce' ); ?></option>
				<option value="use_site" <?php selected( 'use_site', $timezone_setting, true ); ?>><?php printf( _x( 'Site default', 'Use this site default setting', 'gce' ) . ' (%s)', $timezone_default ); ?></option>
			</select>
			<p class="description">
				<?php _e( 'It is recommended to use the calendar default timezone.', 'gce' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="gce_search_query"><?php _e( 'Search Query', 'gce' ); ?></label></th>
		<td>
			<input type="text" class="" name="gce_search_query" id="gce_search_query" value="<?php echo esc_attr( $gce_search_query ); ?>" />
			<p class="description"><?php _e( 'Find and show events based on a search query.', 'gce' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_expand_recurring"><?php _e( 'Expand Recurring Events?', 'gce' ); ?></label></th>
		<td>
			<input type="checkbox" name="gce_expand_recurring" id="gce_expand_recurring" value="1" <?php checked( $gce_expand_recurring, '1' ); ?> /> <?php _e( 'Yes', 'gce' ); ?>
			<p class="description"><?php _e( 'Display recurring events each time they occur. If disabled, events will be displayed only the first time they occur.', 'gce' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_multi_day_events"><?php _e( 'Multiple Day Events', 'gce' ); ?></label></th>
		<td>
			<input type="checkbox" name="gce_multi_day_events" id="gce_multi_day_events" value="1" <?php checked( $gce_multi_day_events, '1' ); ?> /> <?php _e( 'Show on each day', 'gce' ); ?>
			<p class="description"><?php _e( 'Display multiple day events on each day that they span. If disabled, multiple day events will be displayed only on the first day they occur.', 'gce' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_display_mode"><?php _e( 'Display Mode', 'gce' ); ?></label></th>
		<td>
			<select name="gce_display_mode" id="gce_display_mode">
				<option value="grid" <?php selected( $gce_display_mode, 'grid', true ); ?>><?php _e( 'Grid (Month view)', 'gce' ); ?></option>
				<option value="list" <?php selected( $gce_display_mode, 'list', true ); ?>><?php _e( 'List', 'gce' ); ?></option>
				<option value="list-grouped" <?php selected( $gce_display_mode, 'list-grouped', true ); ?>><?php _e( 'Grouped List', 'gce' ); ?></option>
				<option value="date-range-list" <?php selected( $gce_display_mode, 'date-range-list', true ); ?>><?php _e( 'Custom Date Range (List)', 'gce' ); ?></option>
				<option value="date-range-grid" <?php selected( $gce_display_mode, 'date-range-grid', true ); ?>><?php _e( 'Custom Date Range (Grid)', 'gce' ); ?></option>
			</select>
			<p class="description"><?php _e( 'Select how to display this feed.', 'gce' ); ?></p>
		</td>
	</tr>

	<tr class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
		<th scope="row"><label for="gce_events_per_page"><?php _e( 'Events per Page', 'gce' ); ?></label></th>
		<td>
			<select id="gce_events_per_page" name="gce_events_per_page">
				<option value="days" <?php selected( $gce_events_per_page, 'days', true ); ?>><?php _e( 'Number of Days', 'gce' ); ?></option>
				<option value="events" <?php selected( $gce_events_per_page, 'events', true ); ?>><?php _e( 'Number of Events', 'gce' ); ?></option>
				<option value="week" <?php selected( $gce_events_per_page, 'week', true ); ?>><?php _e( 'One Week', 'gce' ); ?></option>
				<option value="month" <?php selected( $gce_events_per_page, 'month', true ); ?>><?php _e( 'One Month', 'gce' ); ?></option>
			</select>
			<span class="gce_per_page_num_wrap <?php echo ( $gce_events_per_page != 'days' && $gce_events_per_page != 'events' ? 'gce-admin-hidden' : '' ); ?>">
				<input type="number" min="0" step="1" class="small-text" name="gce_per_page_num" id="gce_per_page_num" value="<?php echo esc_attr( $gce_per_page_num ); ?>" />
			</span>
			<p class="description"><?php _e( 'How many events to display per page (List View only).', 'gce' ); ?></p>
		</td>
	</tr>

	<tr class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
		<th scope="row"><label for="gce_list_start_offset_num"><?php _e( 'Display Start Date Offset', 'gce' ); ?></label></th>
		<td>
			<select name="gce_list_start_offset_direction" id="gce_list_start_offset_direction">
				<option value="back" <?php selected( $gce_list_start_offset_direction, 'back', true ); ?>><?php _e( 'Number of Days Back', 'gce' ); ?></option>
				<option value="ahead" <?php selected( $gce_list_start_offset_direction, 'ahead', true ); ?>><?php _e( 'Number of Days Forward', 'gce' ); ?></option>
			</select>
			<input type="number" min="0" step="1" class="small-text" id="gce_list_start_offset_num" name="gce_list_start_offset_num" value="<?php echo esc_attr( $gce_list_start_offset_num ); ?>" />
			<p class="description"><?php _e( 'Change to initially display events on a date other than today (List View only).', 'gce' ); ?></p>
		</td>
	</tr>

	<tr class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
		<th scope="row"><label for="gce_feed_start"><?php _e( 'Earliest Feed Event Date', 'gce' ); ?></label></th>
		<td>
			<select id="gce_feed_start" name="gce_feed_start">
				<option value="days" <?php selected( $gce_feed_start, 'days', true ); ?>><?php _e( 'Number of Days Back', 'gce' ); ?></option>
				<option value="months" <?php selected( $gce_feed_start, 'months', true ); ?>><?php _e( 'Number of Months Back', 'gce' ); ?></option>
				<option value="years" <?php selected( $gce_feed_start, 'years', true ); ?>><?php _e( 'Number of Years Back', 'gce' ); ?></option>
			</select>
			<span class="gce_feed_start_num_wrap <?php echo ( $gce_feed_start == 'custom' ? 'gce-admin-hidden' : '' ); ?>">
				<input type="number" min="0" step="1" class="small-text" id="gce_feed_start_num" name="gce_feed_start_num" value="<?php echo esc_attr( $gce_feed_start_num ); ?>" />
			</span>
			<p class="description">
				<?php _e( 'Set how far back to retrieve events regardless of initial display.', 'gce' ); ?>
				<br>
				<?php _e( '<strong>Note:</strong> Total events are currently limited to 2,500 by the Google Calendar API.', 'gce' ); ?>
			</p>
		</td>
	</tr>

	<tr class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
		<th scope="row"><label for="gce_feed_end"><?php _e( 'Latest Feed Event Date', 'gce' ); ?></label></th>
		<td>
			<select id="gce_feed_end" name="gce_feed_end">
				<option value="days" <?php selected( $gce_feed_end, 'days', true ); ?>><?php _e( 'Number of Days Forward', 'gce' ); ?></option>
				<option value="months" <?php selected( $gce_feed_end, 'months', true ); ?>><?php _e( 'Number of Months Forward', 'gce' ); ?></option>
				<option value="years" <?php selected( $gce_feed_end, 'years', true ); ?>><?php _e( 'Number of Years Forward', 'gce' ); ?></option>
			</select>
			<span class="gce_feed_end_num_wrap <?php echo ( $gce_feed_end == 'custom' ? 'gce-admin-hidden' : '' ); ?>">
				<input type="number" min="0" step="1" class="small-text" id="gce_feed_end_num" name="gce_feed_end_num" value="<?php echo esc_attr( $gce_feed_end_num ); ?>" />
			</span>
			<p class="description"><?php _e( 'Set how far in the future to retrieve events regardless of initial display.', 'gce' ); ?></p>
		</td>
	</tr>

	<tr class="gce-custom-range <?php echo ( $use_range == true ? '' : 'gce-admin-hidden' ); ?>">
		<th scope="row"><label for="gce_feed_use_range"><?php _e( 'Use Custom Date Range', 'gce' ); ?></label></th>
		<td>
			<span>
				<input type="text" name="gce_feed_range_start" id="gce_feed_range_start" value="<?php echo esc_attr( $gce_feed_range_start ); ?>" />
				<?php _ex( 'to', 'separator between custom date range fields', 'gce' ); ?>
				<input type="text" id="gce_feed_range_end" name="gce_feed_range_end" value="<?php echo esc_attr( $gce_feed_range_end ); ?>" />
				<p class="description"><?php _e( 'Set a specific range of events to retrieve. Leaving either field blank will set the date to the current day.', 'gce' ); ?></p>
			</span>
		</td>
	</tr>

	<tr class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
		<th scope="row"><label for="gce_paging"><?php _e( 'Show Paging Links', 'gce' ); ?></label></th>
		<td>
			<input type="checkbox" name="gce_paging" id="gce_paging" value="1" <?php checked( $gce_paging, '1' ); ?> />
			<?php _e( 'Display Next and Back navigation links.', 'gce' ); ?>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_show_tooltips"><?php _e( 'Show Tooltips', 'gce' ); ?></label></th>
		<td>
			<input type="checkbox" name="gce_show_tooltips" id="gce_show_tooltips" value="1" <?php checked( $gce_show_tooltips, '1' ); ?> />
			<?php _e( 'Display tooltips when hovering over events (Grid View only).', 'gce' ); ?>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_date_format"><?php _e( 'Date Format', 'gce' ); ?></label></th>
		<td>
			<input type="text" class="" name="gce_date_format" id="gce_date_format" value="<?php echo esc_attr( $gce_date_format ); ?>" />
			<p class="description">
				<?php printf( __( 'Use %sPHP date formatting%s.', 'gce' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank">', '</a>' ); ?>
				<?php _e( 'Leave blank to use the default.', 'gce' ); ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_time_format"><?php _e( 'Time Format', 'gce' ); ?></label></th>
		<td>
			<input type="text" class="" name="gce_time_format" id="gce_time_format" value="<?php echo esc_attr( $gce_time_format ); ?>" />
			<p class="description">
				<?php printf( __( 'Use %sPHP date formatting%s.', 'gce' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank">', '</a>' ); ?>
				<?php _e( 'Leave blank to use the default.', 'gce' ); ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="gce_cache"><?php _e( 'Cache Duration', 'gce' ); ?></label></th>
		<td>
			<input type="text" class="" name="gce_cache" id="gce_cache" value="<?php echo esc_attr( $gce_cache ); ?>" />
			<p class="description"><?php _e( 'The length of time, in seconds, to cache the feed (43200 = 12 hours). If this feed changes regularly, you may want to reduce the cache duration.', 'gce' ); ?></p>
		<td>
	</tr>

	<tr>
		<td colspan="2">
			<input type="button" class="button button-primary button-large gce-feed-update-button" value="Save Changes">
		</td>
	</tr>
</table>
