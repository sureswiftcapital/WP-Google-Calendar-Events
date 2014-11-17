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
		   $title;
	
	public $events = array();
	
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
				echo '<p>' . __( 'The feed URL has not been set. Please make sure to set it correctly in the Feed settings.', 'gce' ) . '</p>';
			}
			
			return;
		}
		
		$args = array();
		
		$args['orderBy'] = 'startTime';
		
		$args['timeMin'] = $this->get_feed_start();
		
		$args['timeMax'] = $this->get_feed_end();
		
		$args['maxResults'] = 10000;
		
		if ( ! empty( $this->search_query ) ) {
			$args['q'] = rawurlencode( $this->search_query );
		}
		
		$args['singleEvents'] = true;
		
		$this->get_feed_data( $args );
	}
	
	/**
	 * Make remote call to get the feed data
	 * 
	 * @since 2.0.0
	 */
	private function get_feed_data( $args ) {	

		// First check for transient data to use
		if( false !== get_transient( 'gce_feed_' . $this->id ) ) {
			$this->events = get_transient( 'gce_feed_' . $this->id );
		} else {
			global $gce_options;
			
			$token = $gce_options['auth_token'];

			GCal::set_token( $token );
			
			try {
				$this->service = new Google_Service_Calendar( GCal::get_client() );

				$events = $this->service->events->listEvents( $this->calendar_id, $args );
				
				while( true ) {
					foreach ( $events->getItems() as $event ) {
						$id          = $event->id;
						$title       = $event->summary;
						$description = $event->description;
						$link        = $event->htmlLink;
						$location    = $event->location;
						$start_time  = $this->iso_to_ts( $event->start['dateTime'] );
						$end_time    = $this->iso_to_ts( $event->end['dateTime'] );

						//Create a GCE_Event using the above data. Add it to the array of events
						$this->events[] = new GCE_Event( $this, $id, $title, $description, $location, $start_time, $end_time, $link );
					}
					
					$pageToken = $events->getNextPageToken();
					
					if( $pageToken ) {
						$args['pageToken'] = $pageToken;
						$events = $this->service->events->listEvents( $this->calendar_id, $args );
					} else {
						break;
					}
				}
			} catch( Exception $e ) {
				
				if( current_user_can( 'manage_options' ) ) {
					_e( 'An error has occured. Please make sure your feed settings are properly set.', 'gce' );
				}
			}
			
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
	
	private function get_feed_start() {
		
		$start    = get_post_meta( $this->id, 'gce_feed_start', true );
		$interval = get_post_meta( $this->id, 'gce_feed_start_interval', true );
		
		switch( $interval ) {
			case 'days':
				return date( 'c', time() - ( $start * 86400 ) );
			case 'months':
				return date( 'c', time() - ( $start * 2629743 ) );
			case 'years':
				return date( 'c', time() - ( $start * 31556926 ) );
		}
		
		// fall back just in case. Falls back to 1 year ago
		return date( 'c', time() - 31556926 );
	}
	
	private function get_feed_end() {
		
		$end    = get_post_meta( $this->id, 'gce_feed_end', true );
		$interval = get_post_meta( $this->id, 'gce_feed_end_interval', true );
		
		switch( $interval ) {
			case 'days':
				return date( 'c', time() + ( $end * 86400 ) );
			case 'months':
				return date( 'c', time() + ( $end * 2629743 ) );
			case 'years':
				return date( 'c', time() + ( $end * 31556926 ) );
		}
		
		// Falls back to 1 year ahead just in case
		return date( 'c', time() + 31556926 );
	}
	
	function get_builder() {
		
		$this->builder = get_post( $this->id )->post_content;
		
		return $this->builder;
	}
}
