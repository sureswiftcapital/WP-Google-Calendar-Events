<?php
/**
 * Admin helper functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

/**
 * Function to clear the cache out
 *
 * @since 2.0.0
 */
function gce_clear_cache( $id ) {

	delete_transient( 'gce_feed_' . $id );

	add_settings_error( 'gce-notices', 'gce-cache-updated', __( 'Cache has been cleared for this feed.', 'gce' ), 'updated' );
}

/**
 * Function that runs when a new Feed CPT is made
 * Adds the default editor content for the event builder
 * Adds default post meta options
 *
 * @since 2.0.0
 */

function gce_default_editor_content( $content, $post ) {

	if( $post->post_type == 'gce_feed' ) {
		$content  = '<div class="gce-list-event gce-tooltip-event">[event-title]</div>' . "\n";
		$content .= '<div><span>' . __( 'Starts:', 'gce' ) . '</span> [start-time]</div>' . "\n";
		$content .= '<div><span>' . __( 'Ends:', 'gce' ) . '</span> [end-date] - [end-time]</div>' . "\n";
		$content .= '[if-location]<div><span>' . __( 'Location:', 'gce' ) . '</span> [location]</div>[/if-location]' . "\n";
		$content .= '[if-description]<div><span>' . __( 'Description:', 'gce' ) . '</span> [description]</div>[/if-description]' . "\n";
		$content .= '<div>[link newwindow="true"]' . __( 'More details...', 'gce' ) . '[/link]</div>' . "\n";

		// Default Post Meta Options
		add_post_meta( $post->ID, 'gce_expand_recurring', 1 );
		add_post_meta( $post->ID, 'gce_retrieve_from', 'today' );
		add_post_meta( $post->ID, 'gce_retrieve_until', 'end_time' );
		add_post_meta( $post->ID, 'gce_cache', 43200 );
		add_post_meta( $post->ID, 'gce_paging', 1 );
		add_post_meta( $post->ID, 'gce_list_start_offset_num', '0' );
		add_post_meta( $post->ID, 'gce_feed_end_num', 2 );
		add_post_meta( $post->ID, 'gce_feed_start_num', 1 );
		add_post_meta( $post->ID, 'gce_feed_end', 'years' );
		add_post_meta( $post->ID, 'gce_feed_start', 'months' );
		add_post_meta( $post->ID, 'gce_show_tooltips', 1 );

		// Default Simple Display Options
		add_post_meta( $post->ID, 'gce_display_start', 'time' );
		add_post_meta( $post->ID, 'gce_display_start_text', __( 'Starts:', 'gce' ) );
		add_post_meta( $post->ID, 'gce_display_end', 'time-date' );
		add_post_meta( $post->ID, 'gce_display_end_text', __( 'Ends:', 'gce' ) );
		add_post_meta( $post->ID, 'gce_display_separator', ', ' );
		add_post_meta( $post->ID, 'gce_display_location_text', __( 'Location:', 'gce' ) );
		add_post_meta( $post->ID, 'gce_display_description_text', __( 'Description:', 'gce' ) );
		add_post_meta( $post->ID, 'gce_display_link', 1 );
		add_post_meta( $post->ID, 'gce_display_link_text', __( 'More Details', 'gce' ) );

	}

	return $content;
}
add_filter( 'default_content', 'gce_default_editor_content', 10, 2 );


function gce_add_cache_button() {
		global $post;

		if( $post->post_type == 'gce_feed' ) {
			$html = '<div id="gce-clear-cache">' .
					'<a href="' . esc_url( add_query_arg( array( 'clear_cache' => true ) ) ) . '">' . __( 'Clear Cache', 'gce' ) . '</a>' .
					'</div>';

			echo $html;
		}
}
add_action( 'post_submitbox_start', 'gce_add_cache_button' );

/**
 * Sanitize a variable of unknown type.
 *
 * Helper function to sanitize a variable from input,
 * which could also be a multidimensional array of variable depth.
 *
 * @param  mixed $var   Variable to sanitize.
 * @param  string $func Function to use for sanitizing text strings (default 'sanitize_text_field')
 *
 * @return array|string Sanitized variable
 */
function gce_sanitize_input( $var, $func = 'sanitize_text_field'  ) {

	if ( empty( $var ) || is_null( $var ) ) {
		return '';
	}

	if ( is_bool( $var ) ) {
		if ( $var === true ) {
			return 'yes';
		} else {
			return 'no';
		}
	}

	if ( is_string( $var ) || is_numeric( $var ) ) {
		$func = is_string( $func ) && function_exists( $func ) ? $func : 'sanitize_text_field';
		return call_user_func( $func, trim( strval( $var ) ) );
	}

	if ( is_object( $var ) ) {
		$var = (array) $var;
	}

	if ( is_array( $var ) ) {
		$array = array();
		foreach( $var as $k => $v ) {
			$array[$k] = gce_sanitize_input( $v );
		}
		return $array;
	}

	return '';
}
