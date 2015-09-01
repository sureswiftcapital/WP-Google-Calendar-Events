<?php

/**
 * Class GCE_Feed
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

class GCE_Feed {

	public $id,
		   $calendar_id,
		   $feed_url,
		   $date_format,
		   $time_format,
		   $cache,
		   $multiple_day_events,
		   $display_url,
		   $search_query,
		   $expand_recurring,
		   $title,
		   $feed_start,
		   $feed_end,
		   $builder;

	public $events = array();

	// Google API Key
	private $api_key = 'AIzaSyAssdKVved1mPVY0UJCrx96OUOF9u17AuY';

	/**
	 * Class constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct( $id ) {
		// Set the ID
		$this->id = $id;

		$this->calendar_id = get_post_meta( $this->id, 'gce_feed_url', true );

		// Set up all other data based on the ID
		$this->setup_attributes();

		// Now create the Feed
		$this->create_feed();
	}

	/**
	 * Set the transient to cache the events
	 *
	 * @since 2.0.0
	 */
	private function cache_events() {
		set_transient( 'gce_feed_' . $this->id, $this->events, $this->cache );
	}

	/**
	 * Set all of the feed attributes from the post meta options
	 *
	 * @since 2.0.0
	 */
	private function setup_attributes() {
		$date_format = get_post_meta( $this->id, 'gce_date_format', true );
		$time_format = get_post_meta( $this->id, 'gce_time_format', true );

		$this->feed_url            = get_post_meta( $this->id, 'gce_feed_url', true );
		$this->date_format         = ( ! empty( $date_format ) ? $date_format : get_option( 'date_format' ) );
		$this->time_format         = ( ! empty( $time_format ) ? $time_format : get_option( 'time_format' ) );
		$this->cache               = get_post_meta( $this->id, 'gce_cache', true );
		$this->multiple_day_events = get_post_meta( $this->id, 'gce_multi_day_events', true );
		$this->search_query        = get_post_meta( $this->id, 'gce_search_query', true );
		$this->expand_recurring    = get_post_meta( $this->id, 'gce_expand_recurring', true );
		$this->title               = get_the_title( $this->id );
		$this->feed_start          = $this->get_feed_start();
		$this->feed_end            = $this->get_feed_end();
	}

	/**
	 * Create the feed URL
	 *
	 * @since 2.0.0
	 */
	private function create_feed() {
		//Break the feed URL up into its parts (scheme, host, path, query)

		global $gce_options;

		if( empty( $this->feed_url ) ) {
			if( current_user_can( 'manage_options' ) ) {
				echo '<p>' . __( 'The Google Calendar ID has not been set. Please make sure to set it correctly in the Feed settings.', 'gce' ) . '</p>';
			}

			return;
		}

		$args = array();

		if( ! empty( $gce_options['api_key'] ) ) {
			$api_key = urlencode( $gce_options['api_key'] );
		} else {
			$api_key = $this->api_key;
		}

		$query = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendar_id . '/events';

		// Set API key
		$query .= '?key=' . $api_key;

		// Timezone.
		$timezone_option = esc_attr( get_post_meta( $this->id, '_feed_timezone_setting', true ) );
		$timezone = gce_get_wp_timezone();
		if ( 'use_site' == $timezone_option ) {
			$args['timeZone'] = $timezone;
		}

		// Time boundaries.
		if ( version_compare( PHP_VERSION, '5.3.0' ) === -1 ) {

			$ts = $this->feed_start;
			$time = new DateTime( "@$ts" );
			if ( 'use_site' == $timezone_option ) {
				$time->setTimezone( new DateTimeZone( $timezone ) );
			}
			$args['timeMin'] = urlencode( $time->format( 'c' ) );

			$ts = $this->feed_end;
			$time = new DateTime( "@$ts" );
			if ( 'use_site' == $timezone_option ) {
				$time->setTimezone( new DateTimeZone( $timezone ) );
			}
			$args['timeMax'] = urlencode( $time->format( 'c' ) );

		} else {

			$time = new DateTime();

			if ( 'use_site' == $timezone_option ) {
				$time->setTimezone( new DateTimeZone( $timezone ) );
			}

			$time->setTimestamp( $this->feed_start );
			$args['timeMin'] = urlencode( $time->format( 'c' ) );
			$time->setTimestamp( $this->feed_end );
			$args['timeMax'] = urlencode( $time->format( 'c' ) );

		}

		// Max no. of events.
		$args['maxResults'] = 2500;

		// Google search query terms.
		if ( ! empty( $this->search_query ) ) {
			$args['q'] = rawurlencode( $this->search_query );
		}

		// Show recurring.
		if( ! empty( $this->expand_recurring ) ) {
			$args['singleEvents'] = 'true';
		}

		$query = esc_url_raw( add_query_arg( $args, $query ) );

		$this->display_url = $query;

		if( isset( $_GET['gce_debug'] ) && $_GET['gce_debug'] == true ) {
			echo '<pre>' . $this->display_url . '</pre><br>';
		}

		$this->get_feed_data( $query );
	}

	/**
	 * Make remote call to get the feed data
	 *
	 * @since 2.0.0
	 */
	private function get_feed_data( $url ) {

		// First check for transient data to use
		if( false !== get_transient( 'gce_feed_' . $this->id ) ) {
			$this->events = get_transient( 'gce_feed_' . $this->id );
		} else {
			$raw_data = wp_remote_get( $url, array( 'timeout' => 10 ) );
			//If $raw_data is a WP_Error, something went wrong
			if ( ! is_wp_error( $raw_data ) ) {
					//Attempt to convert the returned JSON into an array
					$raw_data = json_decode( $raw_data['body'], true );

					if( ! isset( $raw_data['error'] ) ) {
						//If decoding was successful
						if ( ! empty( $raw_data ) ) {
							//If there are some entries (events) to process
								//Loop through each event, extracting the relevant information
								foreach ( $raw_data['items'] as $event ) {
									$id          = ( isset( $event['id'] ) ? esc_html( $event['id'] ) : '' );
									$title       = ( isset( $event['summary'] ) ? esc_html( $event['summary'] ) : '' );
									$description = ( isset( $event['description'] ) ? $event['description'] : '' );
									$link        = ( isset( $event['htmlLink'] ) ? esc_url( $event['htmlLink'] ) : '' );
									$location    = ( isset( $event['location'] ) ? esc_html( $event['location'] ) : '' );

									if( isset( $event['start']['dateTime'] ) ) {
										$start_time  = $this->iso_to_ts( $event['start']['dateTime'] );
									} else if( isset( $event['start']['date'] ) ) {
										$start_time  = $this->iso_to_ts( $event['start']['date'] );
									} else {
										$start_time = null;
									}

									if( isset( $event['end']['dateTime'] ) ) {
										$end_time  = $this->iso_to_ts( $event['end']['dateTime'] );
									} else if( isset( $event['end']['date'] ) ) {
										$end_time  = $this->iso_to_ts( $event['end']['date'] );
									} else {
										$end_time = null;
									}

									//Create a GCE_Event using the above data. Add it to the array of events
									$this->events[] = new GCE_Event( $this, $id, $title, $description, $location, $start_time, $end_time, $link );
								}
						} else {
							//json_decode failed
							$this->error = __( 'Some data was retrieved, but could not be parsed successfully. Please ensure your feed settings are correct.', 'gce' );
						}
					} else {
						$this->error = __( 'An error has occured.', 'gce' );
						$this->error .= '<pre>' . $raw_data['error']['message'] . '</pre>';
					}
			} else{
				//Generate an error message from the returned WP_Error
				$this->error = $raw_data->get_error_message() . __( ' Please ensure your calendar ID is correct.', 'gce' );
			}
		}

		if( ! empty( $this->error ) ) {
			if( current_user_can( 'manage_options' ) ) {
				echo $this->error;
				return;
			}
		} else {
			if( $this->cache > 0 && false === get_transient( 'gce_feed_' . $this->id ) ) {
				$this->cache_events();
			}
		}
	}

	/**
	 * Convert an ISO date/time to a UNIX timestamp
	 *
	 * @since 2.0.0
	 */
	private function iso_to_ts( $iso ) {
		sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );
		return mktime( $hour, $minute, $second, $month, $day, $year );
	}

	/**
	 * @return int
	 */
	private function get_feed_start() {

		$range = get_post_meta( $this->id, 'gce_display_mode', true );
		$use_range = ( ( $range == 'date-range-list' || $range == 'date-range-grid' ) ? true : false );

		if( $use_range ) {
			$start = get_post_meta( $this->id, 'gce_feed_range_start', true );

			$start = gce_date_unix( $start );

			$interval = 'date-range';

		} else {
			$start    = get_post_meta( $this->id, 'gce_feed_start_num', true );
			$interval = get_post_meta( $this->id, 'gce_feed_start', true );

			if( empty( $start ) && $start !== '0' ) {
				$start = 1;
			}
		}

		switch( $interval ) {
			case 'days':
				return time() - ( $start * 86400 );
			case 'months':
				return time() - ( $start * 2629743 );
			case 'years':
				return time() - ( $start * 31556926 );
			case 'date-range':
				return $start;
		}

		// fall back just in case. Falls back to 1 year ago
		return time() - 31556926;
	}

	/**
	 * @return int
	 */
	private function get_feed_end() {

		$range = get_post_meta( $this->id, 'gce_display_mode', true );
		$use_range = ( ( $range == 'date-range-list' || $range == 'date-range-grid' ) ? true : false );

		if( $use_range ) {
			$end = get_post_meta( $this->id, 'gce_feed_range_end', true );

			$end = gce_date_unix( $end );

			$interval = 'date-range';

		} else {
			$end    = get_post_meta( $this->id, 'gce_feed_end_num', true );
			$interval = get_post_meta( $this->id, 'gce_feed_end', true );

			if( empty( $end ) && $end !== '0' ) {
				$end = 1;
			}
		}

		switch( $interval ) {
			case 'days':
				return time() + ( $end * 86400 );
			case 'months':
				return time() + ( $end * 2629743 );
			case 'years':
				return time() + ( $end * 31556926 );
			case 'date-range':
				return mktime( 23, 59, 59, date( 'n', $end ), date( 'j', $end ), date( 'Y', $end ) );
		}

		// Falls back to 1 year ahead just in case
		return time() + 31556926;
	}

	function get_builder() {

		$this->builder = get_post( $this->id )->post_content;

		return $this->builder;
	}
}
