<?php


class GCE_Feed {
	
	private $id, $feed_url, $start, $end, $max, $date_format, $time_format, $timezone, $cache, $multiple_day_events, $display_url;
	
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
		$this->timezone            = get_post_meta( $this->id, 'gce_timezone', true );
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
		return print_r( $this->events, true );
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
								$start_time  = /*$this->iso_to_ts(*/ $event['gd$when'][0]['startTime']; /*);*/
								$end_time    = /*$this->iso_to_ts(*/ $event['gd$when'][0]['endTime']; /*);*/

								//Create a GCE_Event using the above data. Add it to the array of events
								$this->events[] = $id . ' ' . $title . ' ' . $description . ' ' . $location . ' ' . $start_time . ' ' . $end_time . ' ' . $link;
							}
						}
					} else {
						//json_decode failed
						$this->events = __( 'Some data was retrieved, but could not be parsed successfully. Please ensure your feed URL is correct.', 'gce' );
					}
				} else {
					//The response code wasn't 200, so generate a helpful(ish) error message depending on error code 
					switch ( $raw_data['response']['code'] ) {
						case 404:
							$this->events = __( 'The feed could not be found (404). Please ensure your feed URL is correct.', 'gce' );
							break;
						case 403:
							$this->events = __( 'Access to this feed was denied (403). Please ensure you have public sharing enabled for your calendar.', 'gce' );
							break;
						default:
							$this->events = sprintf( __( 'The feed data could not be retrieved. Error code: %s. Please ensure your feed URL is correct.', 'gce' ), $raw_data['response']['code'] );
					}
				}
			}else{
				//Generate an error message from the returned WP_Error
				$this->events = $raw_data->get_error_message() . ' Please ensure your feed URL is correct.';
			}
	}
}
