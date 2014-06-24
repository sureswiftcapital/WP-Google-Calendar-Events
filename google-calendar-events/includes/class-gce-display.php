<?php

class GCE_Display {
	
	
	// Class for display functions?
	
	// List
	// Grid
	// Ajax
	// Grouped Grid
	
	// Do we really need a class for this?
	
	public function __construct( $id, GCE_Feed $feed ) {
		$this->id = $id;
		$this->feed = $feed;
	}
	
	
	public function get_ajax() {
		return $this->get_grid( null, null, true );
	}
	
	
	//Returns array of days with events, with sub-arrays of events for that day
	private function get_event_days() {
		$event_days = array();

		//Total number of events retrieved
		$count = count( $this->feed->events );

		//If maximum events to display is 0 (unlimited) set $max to 1, otherwise use maximum of events specified by user
		$max = ( 0 == $this->feed->max ) ? 1 : $this->feed->max;

		//Loop through entire array of events, or until maximum number of events to be displayed has been reached
		for ( $i = 0; $i < $count && $max > 0; $i++ ) {
			$event = $this->feed->events[$i];

			//Check that event ends, or starts (or both) within the required date range. This prevents all-day events from before / after date range from showing up.
			//if ( $event->get_end_time() > $event->get_feed()->get_feed_start() && $event->get_start_time() < $event->get_feed()->get_feed_end() ) {
				foreach ( $event->get_days() as $day ) {
					$event_days[$day][] = $event;
				}

				//If maximum events to display isn't 0 (unlimited) decrement $max counter
				if ( 0 != $this->feed->max )
					$max--;
			//}
		}

		return $event_days;
	}
	
	public function get_grid ( $year = null, $month = null, $ajaxified = false ) {
		require_once 'php-calendar.php';

		$time_now = current_time( 'timestamp' );

		//If year and month have not been passed as paramaters, use current month and year
		if( ! isset( $year ) )
			$year = date( 'Y', $time_now );

		if( ! isset( $month ) )
			$month = date( 'm', $time_now );

		//Get timestamps for the start and end of current month
		$current_month_start = mktime( 0, 0, 0, date( 'm', $time_now ), 1, date( 'Y', $time_now ) );
		$current_month_end = mktime( 0, 0, 0, date( 'm', $time_now ) + 1, 1, date( 'Y', $time_now ) );

		//Get timestamps for the start and end of the month to be displayed in the grid
		$display_month_start = mktime( 0, 0, 0, $month, 1, $year );
		$display_month_end = mktime( 0, 0, 0, $month + 1, 1, $year );

		//It should always be possible to navigate to the current month, even if it doesn't have any events
		//So, if the display month is before the current month, set $nav_next to true, otherwise false
		//If the display month is after the current month, set $nav_prev to true, otherwise false
		$nav_next = ( $display_month_start < $current_month_start );
		$nav_prev = ( $display_month_start >= $current_month_end );

		//Get events data
		$event_days = $this->get_event_days();

		//If event_days is empty, then there are no events in the feed(s), so set ajaxified to false (Prevents AJAX calendar from allowing to endlessly click through months with no events)
		if ( empty( $event_days ) )
			$ajaxified = false;

		$today = mktime( 0, 0, 0, date( 'm', $time_now ), date( 'd', $time_now ), date( 'Y', $time_now ) );

		$i = 1;
		
		//echo '<pre>Event Days: ' . print_r( $event_days, true ) . '</pre>';

		foreach ( $event_days as $key => $event_day ) {
			//If event day is in the month and year specified (by $month and $year)
			if ( $key >= $display_month_start && $key < $display_month_end ) {
				//Create array of CSS classes. Add gce-has-events
				$css_classes = array( 'gce-has-events' );

				//Create markup for display
				$markup = '<div class="gce-event-info">';

				//If title option has been set for display, add it
				if ( isset( $this->title ) )
					$markup .= '<div class="gce-tooltip-title">' . esc_html( $this->title ) . ' ' . date_i18n( $event_day[0]->get_feed()->get_date_format(), $key ) . '</div>';

				$markup .= '<ul>';

				foreach ( $event_day as $num_in_day => $event ) {
					$feed_id = absint( $this->id );
					$markup .= '<li class="gce-tooltip-feed-' . $feed_id . '">' . $event->get_event_markup( 'tooltip', $num_in_day, $i ) . '</li>';

					//Add CSS class for the feed from which this event comes. If there are multiple events from the same feed on the same day, the CSS class will only be added once.
					$css_classes['feed-' . $feed_id] = 'gce-feed-' . $feed_id;

					$i++;
				}

				$markup .= '</ul></div>';

				//If number of CSS classes is greater than 2 ('gce-has-events' plus one specific feed class) then there must be events from multiple feeds on this day, so add gce-multiple CSS class
				if ( count( $css_classes ) > 2 )
					$css_classes[] = 'gce-multiple';

				//If event day is today, add gce-today CSS class, otherwise add past or future class
				if ( $key == $today )
					$css_classes[] = 'gce-today gce-today-has-events';
				elseif ( $key < $today )
					$css_classes[] = 'gce-day-past';
				else
					$css_classes[] = 'gce-day-future';

				//Change array entry to array of link href, CSS classes, and markup for use in gce_generate_calendar (below)
				$event_days[$key] = array( null, implode( ' ', $css_classes ), $markup );
			} elseif ( $key < $display_month_start ) {
				//This day is before the display month, so set $nav_prev to true. Remove the day from $event_days, as it's no use for displaying this month
				$nav_prev = true;
				unset( $event_days[$key] );
			} else {
				//This day is after the display month, so set $nav_next to true. Remove the day from $event_days, as it's no use for displaying this month
				$nav_next = true;
				unset( $event_days[$key] );
			}
		}

		//Ensures that gce-today CSS class is added even if there are no events for 'today'. A bit messy :(
		if ( ! isset( $event_days[$today] ) )
			$event_days[$today] = array( null, 'gce-today gce-today-no-events', null );

		$pn = array();

		//Only add previous / next functionality if AJAX grid is enabled
		if ( $ajaxified ) {
			//If there are events to display in a previous month, add previous month link
			$prev_key = ( $nav_prev ) ? '&laquo;' : '&nbsp;';
			$prev = ( $nav_prev ) ? date( 'm-Y', mktime( 0, 0, 0, $month - 1, 1, $year ) ) : null;

			//If there are events to display in a future month, add next month link
			$next_key = ( $nav_next ) ? '&raquo;' : '&nbsp;';
			$next = ( $nav_next ) ? date( 'm-Y', mktime( 0, 0, 0, $month + 1, 1, $year ) ) : null;

			//Array of previous and next link stuff for use in gce_generate_calendar (below)
			$pn = array( $prev_key => $prev, $next_key => $next );
		}
		
		
		if( $ajaxified ) {
		//Generate the calendar markup and return it
			$markup = '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-page-grid-' . $this->id . '", "' . $this->id . '", "' . absint( $this->feed->max ) . '", "' . 'Test Title Placeholder' . '", "page");});</script>';
			return $markup . gce_generate_calendar( $year, $month, $event_days, 1, null, 0, $pn );
		} else {
			return $markup . gce_generate_calendar( $year, $month, $event_days, 1, null, 0, $pn );
		}
	}
}
