<?php


class GCE_Feed {
	
	public $id, $feed_url, $start, $end, $max, $date_format, $time_format, $timezone_offset, $cache, $multiple_day_events, $display_url;
	
	private $events = array();
	
	private $feed_start = 0;
	private $feed_end = 2145916800;
	
	public function __construct( $id ) {
		// Set the ID
		$this->id = $id;
		
		// Set up all other data based on the ID
		$this->setup_attributes();
		
		// Now create the Feed
		$this->create_feed();
		
		
	}
	
	private function setup_attributes() {
		
		$this->feed_url            = get_post_meta( $this->id, 'gce_feed_url', true );
		$this->start               = get_post_meta( $this->id, 'gce_retrieve_from', true );
		$this->end                 = get_post_meta( $this->id, 'gce_retrieve_until', true );
		$this->max                 = get_post_meta( $this->id, 'gce_retrieve_max', true );
		$this->date_format         = get_post_meta( $this->id, 'gce_date_format', true );
		$this->time_format         = get_post_meta( $this->id, 'gce_time_format', true );
		$this->timezone_offset     = get_post_meta( $this->id, 'gce_timezone_offset', true );
		$this->cache               = get_post_meta( $this->id, 'gce_cache', true );
		$this->multiple_day_events = get_post_meta( $this->id, 'gce_multi_day_events', true );
	}
	
	private function create_feed() {
		
		//Break the feed URL up into its parts (scheme, host, path, query)
		//echo $this->feed_url;
		
		$url_parts = parse_url( $this->feed_url );

		$scheme_and_host = $url_parts['scheme'] . '://' . $url_parts['host'];

		//Remove the exisitng projection from the path, and replace it with '/full-noattendees'
		$path = substr( $url_parts['path'], 0, strrpos( $url_parts['path'], '/' ) ) . '/full-noattendees';

		//Add the default parameters to the querystring (retrieving JSON, not XML)
		$query = '?alt=json&singleevents=true&sortorder=ascending';

		//$gmt_offset = $this->timezone * 3600;

		//Append the feed specific parameters to the querystring
		$query .= '&start-min=' . date( 'Y-m-d\TH:i:s', $this->feed_start );
		$query .= '&start-max=' . date( 'Y-m-d\TH:i:s', $this->feed_end );
		$query .= '&max-results=' . $this->max;

		//if ( ! empty( $this->timezone ) )
		//	$query .= '&ctz=' . $this->timezone;

		//If enabled, use experimental 'fields' parameter of Google Data API, so that only necessary data is retrieved. This *significantly* reduces amount of data to retrieve and process
		//$general_options = get_option( GCE_GENERAL_OPTIONS_NAME );
		//if ( $general_options['fields'] )
		//	$query .= '&fields=entry(title,link[@rel="alternate"],content,gd:where,gd:when,gCal:uid)';

		//Put the URL back together
		$this->display_url = $scheme_and_host . $path . $query;
		
		$this->get_feed_data( $this->display_url );
		
		
	}
	
	public function display() {
		return '<div class="gce-page-grid" id="gce-page-grid">' . $this->get_grid() . '</div>';
	}

	
	private function get_feed_data( $url ) {
		$raw_data = wp_remote_get( $url, array(
				'sslverify' => false, //sslverify is set to false to ensure https URLs work reliably. Data source is Google's servers, so is trustworthy
				'timeout'   => 10     //Increase timeout from the default 5 seconds to ensure even large feeds are retrieved successfully
			) );
		
		//$this->events[] = $raw_data;

			//If $raw_data is a WP_Error, something went wrong
			if ( ! is_wp_error( $raw_data ) ) {
				//If response code isn't 200, something went wrong
				if ( 200 == $raw_data['response']['code'] ) {
					//Attempt to convert the returned JSON into an array
					$raw_data = json_decode( $raw_data['body'], true );

					//If decoding was successful
					if ( ! empty( $raw_data ) ) {
						//If there are some entries (events) to process
						if ( isset( $raw_data['feed']['entry'] ) ) {
							//Loop through each event, extracting the relevant information
							foreach ( $raw_data['feed']['entry'] as $event ) {
								$id          = esc_html( substr( $event['gCal$uid']['value'], 0, strpos( $event['gCal$uid']['value'], '@' ) ) );
								$title       = esc_html( $event['title']['$t'] );
								$description = esc_html( $event['content']['$t'] );
								$link        = esc_url( $event['link'][0]['href'] );
								$location    = esc_html( $event['gd$where'][0]['valueString'] );
								$start_time  = $this->iso_to_ts( $event['gd$when'][0]['startTime'] );
								$end_time    = $this->iso_to_ts( $event['gd$when'][0]['endTime'] );

								//Create a GCE_Event using the above data. Add it to the array of events
								$this->events[] = new GCE_Event( $this, $id, $title, $description, $location, $start_time, $end_time, $link );
							}
						}
					} else {
						//json_decode failed
						$this->error = __( 'Some data was retrieved, but could not be parsed successfully. Please ensure your feed URL is correct.', 'gce' );
					}
				} else {
					//The response code wasn't 200, so generate a helpful(ish) error message depending on error code 
					switch ( $raw_data['response']['code'] ) {
						case 404:
							$this->error = __( 'The feed could not be found (404). Please ensure your feed URL is correct.', 'gce' );
							break;
						case 403:
							$this->error = __( 'Access to this feed was denied (403). Please ensure you have public sharing enabled for your calendar.', 'gce' );
							break;
						default:
							$this->error = sprintf( __( 'The feed data could not be retrieved. Error code: %s. Please ensure your feed URL is correct.', 'gce' ), $raw_data['response']['code'] );
					}
				}
			}else{
				//Generate an error message from the returned WP_Error
				$this->error = $raw_data->get_error_message() . ' Please ensure your feed URL is correct.';
			}
	}
	
	//Returns grid markup
	private function get_grid ( $year = null, $month = null, $ajaxified = false ) {
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
		
		
		//Generate the calendar markup and return it
		return gce_generate_calendar( $year, $month, $event_days, 1, null, 0, $pn );
	}
	
	//Returns array of days with events, with sub-arrays of events for that day
	private function get_event_days() {
		$event_days = array();

		//Total number of events retrieved
		$count = count( $this->events );

		//If maximum events to display is 0 (unlimited) set $max to 1, otherwise use maximum of events specified by user
		$max = ( 0 == $this->max ) ? 1 : $this->max;

		//Loop through entire array of events, or until maximum number of events to be displayed has been reached
		for ( $i = 0; $i < $count && $max > 0; $i++ ) {
			$event = $this->events[$i];

			//Check that event ends, or starts (or both) within the required date range. This prevents all-day events from before / after date range from showing up.
			//if ( $event->get_end_time() > $event->get_feed()->get_feed_start() && $event->get_start_time() < $event->get_feed()->get_feed_end() ) {
				foreach ( $event->get_days() as $day ) {
					$event_days[$day][] = $event;
				}

				//If maximum events to display isn't 0 (unlimited) decrement $max counter
				if ( 0 != $this->max )
					$max--;
			//}
		}

		return $event_days;
	}
	
	//Convert an ISO date/time to a UNIX timestamp
	private function iso_to_ts( $iso ) {
		sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );
		return mktime( $hour, $minute, $second, $month, $day, $year );
	}
}
