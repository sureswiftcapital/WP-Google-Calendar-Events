<?php

/**
 * Custom Post Type functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */


/**
 * Register Google Calendar Events custom post type
 *
 * @since 2.0.0
 */
function gce_setup_cpt() {

	$labels = array(
		'name'               => __( 'Google Calendar Feeds', 'gce' ),
		'singular_name'      => __( 'Feed', 'gce' ),
		'menu_name'          => __( 'GCal Events', 'gce' ),
		'name_admin_bar'     => __( 'Feed', 'gce' ),
		'add_new'            => __( 'Add New', 'gce' ),
		'add_new_item'       => __( 'Add New Feed', 'gce' ),
		'new_item'           => __( 'New Feed', 'gce' ),
		'edit_item'          => __( 'Edit Feed', 'gce' ),
		'view_item'          => __( 'View Feed', 'gce' ),
		'all_items'          => __( 'All GCal Feeds', 'gce' ),
		'search_items'       => __( 'Search GCal Feeds', 'gce' ),
		'not_found'          => __( 'No feeds found.', 'gce' ),
		'not_found_in_trash' => __( 'No feeds found in Trash.', 'gce' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true, // This removes the 'view' and 'preview' links from what I can tell
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_icon'          => plugins_url( '/assets/gcal-icon-16x16.png', GCE_MAIN_FILE ),
		'supports'           => array( 'title', 'editor' )
	);

	register_post_type( 'gce_feed', $args );

	// MUST ONLY RUN ONCE!
	if( false === get_option( 'gce_cpt_setup' ) ) {
		flush_rewrite_rules();
		update_option( 'gce_cpt_setup', 1 );
	}

}
add_action( 'init', 'gce_setup_cpt' );

/**
 * Messages for Feed actions
 *
 * @since 2.0.0
 */
function gce_feed_messages( $messages ) {
	global $post, $post_ID;

	$url1 = '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">';
	$url2 = __( 'feed', 'gce' );
	$url3 = '</a>';
	$s1   = __( 'Feed', 'gce' );

	$messages['gce_feed'] = array(
		1  => sprintf( __( '%4$s updated. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 ),
		4  => sprintf( __( '%4$s updated. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 ),
		6  => sprintf( __( '%4$s published. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 ),
		7  => sprintf( __( '%4$s saved. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 ),
		8  => sprintf( __( '%4$s submitted. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 ),
		10 => sprintf( __( '%4$s draft updated. %1$sView %2$s%3$s', 'gce' ), $url1, $url2, $url3, $s1 )
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'gce_feed_messages' );


/**
 * Add post meta to tie in with the Google Calendar Events custom post type.
 * Also render sidebar meta boxes.
 *
 * @since 2.0.0
 */
function gce_cpt_meta() {
	add_meta_box( 'gce_feed_meta', __( 'Feed Settings', 'gce' ), 'gce_display_meta', 'gce_feed', 'advanced', 'core' );

	add_meta_box( 'gce_feed_sidebar_help', __( 'Resources', 'gce' ), 'gce_feed_sidebar_help', 'gce_feed', 'side', 'core' );
	add_meta_box( 'gce_display_options_meta', __( 'Display Options', 'gce' ), 'gce_display_options_meta', 'gce_feed', 'side', 'core' );
}
add_action( 'add_meta_boxes', 'gce_cpt_meta' );

/**
 * Include view to display post meta.
 *
 * @since 2.0.0
 */
function gce_display_meta() {
	include_once( GCE_DIR . '/views/admin/gce-feed-meta-display.php' );
}

/**
 * Include view to display help in sidebar.
 *
 * @since 2.0.0
 */
function gce_feed_sidebar_help() {
	include_once( GCE_DIR . '/views/admin/gce-feed-sidebar-help.php' );
}

/**
 * Include view to display options in sidebar.
 *
 * @since 2.0.0
 */
function gce_display_options_meta() {
	include_once( GCE_DIR . '/views/admin/display-options-meta.php' );
}

/**
 * Function to save post meta for the feed CPT
 *
 * @since 2.0.0
 *
 * @param  int     $post_id
 * @param  WP_Post $post
 *
 * @return int
 */
function gce_save_meta( $post_id, $post ) {

	if ( 'gce_feed' != $post->post_type ) {
		return $post_id;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $post_id;
	}

	if( isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// An array to hold all of our post meta ids so we can run them through a loop
	$post_meta_fields = array(
		'gce_feed_url',
		'gce_date_format',
		'gce_time_format',
		'gce_cache',
		'gce_multi_day_events',
		'gce_display_mode',
		'gce_custom_from',
		'gce_custom_until',
		'gce_search_query',
		'gce_expand_recurring',
		'gce_show_tooltips',
		'gce_paging',
		'gce_events_per_page',
		'gce_per_page_num',
		'gce_per_page_from',
		'gce_per_page_to',
		'gce_list_start_offset_num',
		'gce_list_start_offset_direction',
		'gce_feed_start',
		'gce_feed_start_num',
		'gce_feed_start_custom',
		'gce_feed_end',
		'gce_feed_end_num',
		'gce_feed_end_custom',
		'gce_feed_use_range',
		'gce_feed_range_start',
		'gce_feed_range_end',
		'_feed_timezone_setting',
		// Display options
		'gce_display_start',
		'gce_display_start_text',
		'gce_display_end',
		'gce_display_end_text',
		'gce_display_separator',
		'gce_display_location',
		'gce_display_location_text',
		'gce_display_description',
		'gce_display_description_text',
		'gce_display_description_max',
		'gce_display_link',
		'gce_display_link_tab',
		'gce_display_link_text',
		'gce_display_simple'
	);

	$post_meta_fields = apply_filters( 'gce_feed_meta', $post_meta_fields );

	if ( current_user_can( 'edit_post', $post_id ) ) {
		// Loop through our array and make sure it is posted and not empty in order to update it, otherwise we delete it
		foreach ( $post_meta_fields as $pmf ) {
			if ( isset( $_POST[$pmf] ) && ( ! empty( $_POST[$pmf] ) || $_POST[$pmf] == 0 ) ) {
				if( $pmf == 'gce_feed_url' ) {

					$id = $_POST[$pmf];

					// convert from URL if user enters a URL link (like the old versions required)
					if ( strpos( $id, 'https://www.google.com/calendar/feeds/' ) !== false ) {
						$id = str_replace( 'https://www.google.com/calendar/feeds/', '', $id );
						$id = str_replace( '/public/basic', '', $id );
						$id = str_replace( '%40', '@', $id );
					}

					// decode first before re-encoding it
					$id = urldecode( $id );
					$id = trim( $id );

					$at = strpos( $id, '@' );

					if ( $at !== false ) {
						$id = substr_replace( $id, urlencode( substr( $id, 0, $at ) ), 0, $at );
					}

					update_post_meta( $post_id, $pmf, $id );
				} elseif( $pmf == 'gce_time_format' || $pmf == 'gce_date_format' || '_feed_timezone_setting' ) {
					update_post_meta( $post_id, $pmf, sanitize_text_field( $_POST[ $pmf ] ) );
				} else {
					update_post_meta( $post_id, $pmf, stripslashes( $_POST[ $pmf ] ) );
				}
			} else {
				delete_post_meta( $post_id, $pmf );
			}
		}
	}

	return $post_id;
}
add_action( 'save_post', 'gce_save_meta', 10, 2 );


/**
 * Delete feed ids transient if a feed post type is deleted.
 *
 * @param int $id The id of the deleted post.
 */
function gce_delete_post( $id ) {
	$feeds = get_transient( 'gce_feed_ids' );
	if ( $feeds && is_array( $feeds ) ) {
		if ( in_array( $id, array_keys( $feeds ) ) ) {
			delete_transient( 'gce_feed_ids' );
		}
	}
}
add_action( 'delete_post', 'gce_delete_post', 10, 1 );


/**
 * Add column headers to our "All Feeds" CPT page
 *
 * @since 2.0.0
 */
function gce_add_column_headers( $defaults ) {

	$new_columns = array(
		'cb'           => $defaults['cb'],
		'feed-id'      => __( 'Feed ID', 'gce' ),
		'feed-sc'      => __( 'Feed Shortcode', 'gce' ),
		'display-type' => __( 'Display Type', 'gce' )
	);

	return array_merge( $defaults, $new_columns );
}
add_filter( 'manage_gce_feed_posts_columns', 'gce_add_column_headers' );


/**
 * Fill out the columns
 *
 * @since 2.0.0
 */
function gce_column_content( $column_name, $post_ID ) {

	switch ( $column_name ) {

		case 'feed-id':
			echo $post_ID;
			break;
		case 'feed-sc':
			?>
			<input
				name="gce_shortcode"
				class="gce-shortcode"
			    readonly="readonly"
			    value='[gcal id="<?php echo $post_ID; ?>"]'
				onclick="this.select();"
				/>
			<?php
			break;
		case 'display-type':
			$display = get_post_meta( $post_ID, 'gce_display_mode', true );

			if ( $display == 'grid' ) {
				echo __( 'Grid', 'gce' );
			} elseif ( $display == 'list' ) {
				echo __( 'List', 'gce' );
			} elseif ( $display == 'list-grouped' ) {
				echo __( 'Grouped List', 'gce' );
			} elseif ( $display == 'date-range' ) {
				echo __( 'Custom Date Range', 'gce' );
			}

			break;
	}
}
add_action( 'manage_gce_feed_posts_custom_column', 'gce_column_content', 10, 2 );


/**
 * Add the "Clear Cache" action to the CPT action links
 *
 * @since 2.0.0
 */
function gce_cpt_actions( $actions, $post ) {
	if( $post->post_type == 'gce_feed' ) {
		$actions['clear_cache'] = '<a href="' . esc_url( add_query_arg( array( 'clear_cache' => $post->ID ) ) ) . '">' . __( 'Clear Cache', 'gce' ) . '</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'gce_cpt_actions', 10, 2 );

/**
 * Function to clear cache if on the post listing page.
 *
 * @since 2.0.0
 */
function gce_clear_cache_link() {
	if( isset( $_REQUEST['clear_cache'] ) ) {
		$post_id = absint( $_REQUEST['clear_cache'] );

		gce_clear_cache( $post_id );

		settings_errors( 'gce-notices' );
	}
}
add_action( 'admin_init', 'gce_clear_cache_link' );

/**
 * Clear cache on post save.
 *
 * @param int $post_id
 */
function gce_clear_cache_on_save( $post_id ) {
	// Transient with calendar feed data.
	delete_transient( 'gce_feed_' . $post_id );
	// Transient with an associative array list of feed ids and their titles.
	delete_transient( 'gce_feed_ids' );
}
add_action( 'save_post_gce_feed', 'gce_clear_cache_on_save' );

/**
 * Adds a 'clear cache' option to bulk actions.
 *
 * It's done through jQuery since one can't write into bulk actions yet.
 * @link https://core.trac.wordpress.org/ticket/16031
 * @link https://www.skyverge.com/blog/add-custom-bulk-action/
 */
function gce_clear_cache_bulk_action_option() {

	global $post_type;

	if ( $post_type == 'gce_feed' ) {

		?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('<option>')
					.val('clear_cache')
					.text('<?php _e( 'Clear Cache', 'gce' ); ?>')
					.appendTo("select[name='action']");
			});
		</script>
		<?php

	}

}
add_action( 'admin_footer-edit.php', 'gce_clear_cache_bulk_action_option' );

/**
 * Clear cache bulk action.
 *
 * @see gce_clear_cache_bulk_action_option()
 */
function gce_clear_cache_bulk_action() {

	global $typenow;
	$post_type = $typenow;

	if ( 'gce_feed' == $post_type ) {

		$send_back = remove_query_arg( array( 'cleared' ), wp_get_referer() );
		if ( ! $send_back ) {
			$send_back = admin_url( 'edit.php?post_type=' . $post_type );
		}

		// Get the bulk action.
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action = $wp_list_table->current_action();
		if ( $action == 'clear_cache' ) {

			// Security check (the referer is right).
			check_admin_referer( 'bulk-posts' );

			// This is based on wp-admin/edit.php.
			$send_back = remove_query_arg(
				array( 'cleared', 'untrashed', 'deleted', 'ids' ),
				$send_back
			);

			// Proceed if there are post ids selected.
			$post_ids = isset( $_REQUEST['post'] ) ? array_map( 'intval', $_REQUEST['post'] ) : '';
			if ( $post_ids ) {

				// Add page num to query arg.
				$page_num  = $wp_list_table->get_pagenum();
				$send_back = add_query_arg( 'paged', $page_num, $send_back );

				switch ( $action ) {
					case 'clear_cache' :
						$cleared = 0;
						foreach ( $post_ids as $post_id ) {
							gce_clear_cache( $post_id );
							$cleared ++;
						}
						$send_back = add_query_arg( array(
							'cleared' => $cleared,
							'ids'     => join( ',', $post_ids )
						),
							$send_back
						);
						break;
					default:
						return;
						break;
				}

				$send_back = remove_query_arg(
					array( 'action', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ),
					$send_back
				);

				wp_redirect( $send_back );
				exit();
			}
		}
	}
}
add_action( 'load-edit.php', 'gce_clear_cache_bulk_action' );

/**
 * Display an admin notice when the cache is cleared.
 */
function gce_clear_cache_bulk_action_notice() {
	global $post_type, $pagenow;
	if ( $pagenow == 'edit.php' && $post_type == 'gce_feed' && isset( $_REQUEST['cleared'] ) && (int) $_REQUEST['cleared'] ) {
		$message = sprintf( _n( 'Feed cache cleared.', 'Cleared cache for %s feeds.', $_REQUEST['cleared'], 'gce' ), number_format_i18n( $_REQUEST['cleared'] ) );
		echo '<div class="updated notice is-dismissible"><p>' . $message . '</p></div>';
	}
}
add_action( 'admin_notices', 'gce_clear_cache_bulk_action_notice' );
