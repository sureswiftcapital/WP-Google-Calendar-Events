<?php

/*
 * Function to display the calendar to the screen
 * 
 * @since 2.0.0
 */
function gce_print_calendar( $feed_ids, $display = 'grid', $args = array(), $widget = false ) {
	
	$defaults = array( 
			'title_text' => '',
			'sort'       => 'asc',
			'grouped'    => 0,
			'month'      => null,
			'year'       => null,
			'widget'     => 0
		);
	
	$args = array_merge( $defaults, $args );
	
	extract( $args );
	
	$ids = explode( '-', $feed_ids );
	
	//Create new display object, passing array of feed id(s)
	$d = new GCE_Display( $ids, $title_text, $sort );
	$markup = '';
	
	if( 'grid' == $display ) {
		
		$markup = '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("' . ( $widget == 1 ? 'gce-widget-' : 'gce-page-grid-' ) . $feed_ids 
					. '", "' . $feed_ids . '", "' . $title_text . '", "' . ( $widget == 1 ? 'widget' : 'page' ) . '");});</script>';
		
		if( $widget == 1 ) {
			$markup .= '<div class="gce-widget-grid" id="gce-widget-' . $feed_ids . '">';
		} else {
			$markup .= '<div class="gce-page-grid" id="gce-page-grid-' . $feed_ids . '">';
		}
		
		$markup .= $d->get_grid( $year, $month, $widget );
		$markup .= '</div>';
		
	} else if( 'list' == $display || 'list-grouped' == $display ) {
		$markup = '<div class="gce-page-list">' . $d->get_list( $grouped ) . '</div>';
	}
	
	return $markup;
}

/**
* AJAX function for grid pagination
* 
* @since 2.0.0
*/
function gce_ajax() {
   if ( isset( $_GET['gce_feed_ids'] ) ) {
	   $ids   = $_GET['gce_feed_ids'];
	   $title = $_GET['gce_title_text'];
	   $month = $_GET['gce_month'];
	   $year  = $_GET['gce_year'];

	   $title = ( 'null' == $title ) ? null : $title;

	   $args = array(
		   'title_text' => $title,
		   'month'      => $month,
		   'year'       => $year,
	   );

	   if ( 'page' == $_GET['gce_type'] ) {
		   echo gce_print_calendar( $ids, 'grid', $args );
	   } elseif ( 'widget' == $_GET['gce_type'] ) {
		   $args['widget'] = 1;
		   echo gce_print_calendar( $ids, 'grid', $args );
	   }
   }
   die();
}
add_action( 'wp_ajax_nopriv_gce_ajax', 'gce_ajax' );
add_action( 'wp_ajax_gce_ajax', 'gce_ajax' );


/**
* AJAX function for grid pagination
* 
* @since 2.0.0
*/
function gce_ajax_list() {
  
	$grouped = $_GET['gce_grouped'];
	$start   = $_GET['gce_month'];
	$end     = $start + 1;
	$ids     = $_GET['gce_feed_ids'];
	$title_text   = $_GET['gce_title_text'];
	$sort = $_GET['gce_sort'];
	
	$d = new GCE_Display( explode( '-', $ids ), $title_text, $sort );

	echo $d->get_list( $grouped, $start, $end );
	   
	die();
}
add_action( 'wp_ajax_nopriv_gce_ajax_list', 'gce_ajax_list' );
add_action( 'wp_ajax_gce_ajax_list', 'gce_ajax_list' );

function gce_feed_content( $content ) {
	global $post;
	
	if( $post->post_type == 'gce_feed' ) {
		$content = '[gcal id="' . $post->ID . '"]';
	}
	
	return $content;
}
add_filter( 'the_content', 'gce_feed_content' );

/**
 * Google Analytics campaign URL.
 *
 * @since   2.0.0
 *
 * @param   string  $base_url Plain URL to navigate to
 * @param   string  $source   GA "source" tracking value
 * @param   string  $medium   GA "medium" tracking value
 * @param   string  $campaign GA "campaign" tracking value
 * @return  string  $url      Full Google Analytics campaign URL
 */
function gce_ga_campaign_url( $base_url, $source, $medium, $campaign ) {
	// $medium examples: 'sidebar_link', 'banner_image'

	$url = add_query_arg( array(
		'utm_source'   => $source,
		'utm_medium'   => $medium,
		'utm_campaign' => $campaign
	), $base_url );

	return $url;
}
