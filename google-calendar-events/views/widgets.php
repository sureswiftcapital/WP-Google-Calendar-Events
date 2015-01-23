<?php

/**
 * Widget functions / views
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Class functions for the GCE widgets
 *
 * @since 2.0.0
 */
class GCE_Widget extends WP_Widget {
	
	function GCE_Widget() {
		parent::__construct(
			false, // Adding a name here doesn't seem to affect the upgrade. If widget stuff starts acting weird then check this first though.
			$name = __( 'Google Calendar Events', 'gce' ),
			array( 'description' => __( 'Display a list or calendar grid of events from one or more Google Calendar feeds you have added', 'gce' ) )
		);
		
		if ( is_active_widget( false, false, $this->id_base ) ) {
			// Call action to load CSS for widget
            add_action( 'wp_enqueue_scripts', array( $this, 'gce_widget_add_styles' ) );

			// Load JS
			wp_enqueue_script( 'google-calendar-events-public' );
		}
	}
	
	function gce_widget_add_styles() {
		// Load CSS
		wp_enqueue_style( 'google-calendar-events-public' );
	}

	/**
	 * Widget HTML output
	 * 
	 * @since 2.0.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		//Output before widget stuff
		echo $before_widget;
		
		$paging     = ( isset( $instance['paging'] ) ? $instance['paging'] : null );
		$max_num    = ( isset( $instance['gce_per_page_num'] ) ? $instance['gce_per_page_num'] : null );
		$max_length = ( isset( $instance['gce_events_per_page'] ) ? $instance['gce_events_per_page'] : null );
		$max_events = null;
		$display_mode = $instance['gce_display_mode'];
		
		// Start offset
		$offset_num       = ( isset( $instance['list_start_offset_num'] ) ? $instance['list_start_offset_num'] : 0 );
		$offset_length    = 86400;
		$offset_direction = ( isset( $instance['list_start_offset_direction'] ) ? $instance['list_start_offset_direction'] : null );
		
		// Get custom date range if set
		if( 'date-range' == $display_mode ) {
			$range_start = ( isset( $instance['gce_feed_range_start'] ) ? $instance['gce_feed_range_start'] : null );
			$range_end   = ( isset( $instance['gce_feed_range_end'] ) ? $instance['gce_feed_range_end'] : null );
			
			if( $range_start !== null && ! empty( $range_start ) ) {
				$range_start = gce_date_unix( $range_start );
			}
			
			if( $range_end !== null && ! empty( $range_end ) ) {
				$range_end = gce_date_unix( $range_end );
			}
		}
		
		if( $offset_direction == 'back' ) {
			$offset_direction = -1;
		} else { 
			$offset_direction = 1;
		}
		
		$start_offset = $offset_num * $offset_length * $offset_direction;
		
		$paging_interval = null;
		
		if( $display_mode == 'date-range' ) {
			$max_length = 'date-range';
		} 
		
		if( $max_length == 'days' ) {
			$paging_interval = $max_num * 86400;
		} else if( $max_length == 'events' ) {
			$max_events = $max_num;
		} else if( $max_length == 'week' ) {
			$paging_interval = 604800;
		} else if( $max_length == 'month' ) {
			$paging_interval = 2629743;
		}
		
		// Check whether any feeds have been added yet
		if( wp_count_posts( 'gce_feed' )->publish > 0 ) {
			//Output title stuff
			$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}

			$no_feeds_exist = true;
			$feed_ids = array();

			if ( ! empty( $instance['id'] ) ) {
				//Break comma delimited list of feed ids into array
				$feed_ids = explode( ',', str_replace( ' ', '', $instance['id'] ) );

				//Check each id is an integer, if not, remove it from the array
				foreach ( $feed_ids as $key => $feed_id ) {
					if ( 0 == absint( $feed_id ) )
						unset( $feed_ids[$key] );
				}

				//If at least one of the feed ids entered exists, set no_feeds_exist to false
				foreach ( $feed_ids as $feed_id ) {
					if ( false !== get_post_meta( $feed_id ) )
						$no_feeds_exist = false;
				}
				
				foreach( $feed_ids as $feed_id ) {
					if( $paging ) {
						update_post_meta( $feed_id, 'gce_paging_widget', true );
					} else { 
						delete_post_meta( $feed_id, 'gce_paging_widget' );
					}
					
					update_post_meta( $feed_id, 'gce_widget_paging_interval', $paging_interval );
				}
			} else {
				if ( current_user_can( 'manage_options' ) ) {
					_e( 'No valid Feed IDs have been entered for this widget. Please check that you have entered the IDs correctly in the widget settings (Appearance > Widgets), and that the Feeds have not been deleted.', 'gce' );
				} 
			}

			//Check that at least one valid feed id has been entered
			if ( ! empty( $feed_ids ) ) {
				//Turns feed_ids back into string or feed ids delimited by '-' ('1-2-3-4' for example)
				$feed_ids = implode( '-', $feed_ids );

				$title_text = ( ! empty( $instance['display_title_text'] )  ? $instance['display_title_text'] : null );
				$sort_order = ( isset( $instance['order'] ) ) ? $instance['order'] : 'asc';
				
				$args = array(
					'title_text'   => $title_text,
					'sort'         => $sort_order,
					'month'        => null,
					'year'         => null,
					'widget'       => 1,
					'max_events'   => $max_events,
					'start_offset' => $start_offset,
					'paging_type'  => $max_length,
					'max_num'      => $max_num
				);
				
				if( 'list-grouped' == $display_mode ) {
					$args['grouped'] = 1;
				}
				
				if( 'date-range' == $display_mode ) {
					$args['range_start'] = $range_start;
					$args['max_events'] = abs( ( $range_end - $range_start ) / 86400 ) + 1;
					$args['max_num'] = abs( ( $range_end - $range_start ) / 86400 ) + 1;
				}
				
				$markup = gce_print_calendar( $feed_ids, $display_mode, $args, true );
				
				echo $markup;
			}
		} else {
			if( current_user_can( 'manage_options' ) ) {
				_e( 'You have not added any feeds yet.', 'gce' );
			} else {
				return;
			}
		}

		//Output after widget stuff
		echo $after_widget;
	}
	
	/**
	 * Update settings when saved
	 * 
	 * @since 2.0.0
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance                                = $old_instance;
		$instance['title']                       = esc_html( $new_instance['title'] );
		$instance['id']                          = esc_html( $new_instance['id'] );
		$instance['gce_display_mode']            = esc_html( $new_instance['gce_display_mode'] );
		$instance['order']                       = ( 'asc' == $new_instance['order'] ) ? 'asc' : 'desc';
		$instance['display_title_text']          = esc_html( $new_instance['display_title_text'] );
		$instance['paging']                      = ( isset( $new_instance['paging'] ) ? 1 : 0 );
		$instance['list_max_num']                = $new_instance['list_max_num'];
		$instance['list_max_length']             = $new_instance['list_max_length'];
		$instance['list_start_offset_num']       = $new_instance['list_start_offset_num'];
		$instance['list_start_offset_direction'] = $new_instance['list_start_offset_direction'];
		$instance['gce_per_page_num']            = $new_instance['gce_per_page_num'];
		$instance['gce_events_per_page']         = $new_instance['gce_events_per_page'];
		$instance['gce_feed_range_start']        = $new_instance['gce_feed_range_start'];
		$instance['gce_feed_range_end']          = $new_instance['gce_feed_range_end'];
		
		
		return $instance;
	}
	
	/**
	 * 
	 * @param type $instanceDisplay widget form in admin
	 * 
	 * @since 2.0.0
	 */
	function form( $instance ) {
		
		// Check for existing feeds and if there are none then display a message and return
		if( wp_count_posts( 'gce_feed' )->publish <= 0 ) {
			echo '<p>' . __( 'There are no feeds created yet.', 'gce' ) . 
					' <a href="' . admin_url( 'edit.php?post_type=gce_feed' ) . '">' . __( 'Add your first feed!', 'gce' ) . '</a>' . 
					'</p>';
			return;
		}
		
		$title                       = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		$ids                         = ( isset( $instance['id'] ) ) ? $instance['id'] : '';
		$gce_display_mode            = ( isset( $instance['gce_display_mode'] ) ) ? $instance['gce_display_mode'] : 'grid';
		$order                       = ( isset( $instance['order'] ) ) ? $instance['order'] : 'asc';
		$display_title               = ( isset( $instance['display_title'] ) ) ? $instance['display_title'] : true;
		$title_text                  = ( isset( $instance['display_title_text'] ) ) ? $instance['display_title_text'] : __( 'Events on', 'gce' );
		$paging                      = ( isset( $instance['paging'] ) ? $instance['paging'] : 1 );
		
		// TODO
		$gce_per_page_num            = ( isset( $instance['gce_per_page_num'] ) ? $instance['gce_per_page_num'] : 7 );
		$gce_events_per_page         = ( isset( $instance['gce_events_per_page'] ) ? $instance['gce_events_per_page'] : 'days' );
		$gce_feed_range_start        = ( isset( $instance['gce_feed_range_start'] ) ? $instance['gce_feed_range_start'] : '' );
		$gce_feed_range_end          = ( isset( $instance['gce_feed_range_end'] ) ? $instance['gce_feed_range_end'] : '' );
		
		$list_start_offset_num       = ( isset( $instance['list_start_offset_num'] ) ? $instance['list_start_offset_num'] : 0 );
		$list_start_offset_direction = ( isset( $instance['list_start_offset_direction'] ) ? $instance['list_start_offset_direction'] : 'back' );
		
		$use_range = ( selected( $gce_display_mode, 'date-range', false ) ? true : false );
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'gce' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>">
				<?php _e( 'Feeds to Display (comma separated list - i.e. 101,102,103):', 'gce' ); ?>
			</label>
			<input type="text" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $ids; ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'gce_display_mode' ); ?>"><?php _e( 'Display Events as:', 'gce' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'gce_display_mode' ); ?>" name="<?php echo $this->get_field_name( 'gce_display_mode' ); ?>" class="widefat">
				<option value="grid" <?php selected( $gce_display_mode, 'grid' ); ?>><?php _e( 'Grid (Month view)', 'gce' ); ?></option>
				<option value="list" <?php selected( $gce_display_mode, 'list' ); ?>><?php _e( 'List', 'gce' ); ?></option>
				<option value="list-grouped" <?php selected( $gce_display_mode, 'list-grouped' );?>><?php _e( 'Grouped List', 'gce' ); ?></option>
				<option value="date-range" <?php selected( $gce_display_mode, 'date-range' );?>><?php _e( 'Custom Date Range (List view)', 'gce' ); ?></option>
			</select>
		</p>
		
		<p class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
			<label for="<?php echo $this->get_field_id( 'paging' ); ?>"><?php _e( 'Show Paging Links:', 'gce' ); ?></label><br>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'paging' ); ?>" name="<?php echo $this->get_field_name( 'paging' ); ?>" class="widefat"  value="1" <?php checked( $paging, 1 ); ?>>
			<?php _e( 'Check this option to display Next and Back navigation links.', 'gce' ); ?>
		</p>

		<p class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sort Order (List View only):', 'gce' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat">
				<option value="asc" <?php selected( $order, 'asc' ); ?>><?php _e( 'Ascending', 'gce' ); ?></option>
				<option value="desc" <?php selected( $order, 'desc' ); ?>><?php _e( 'Descending', 'gce' ); ?></option>
			</select>
		</p>
		
		<p class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
			<select id="<?php echo $this->get_field_id( 'gce_events_per_page' ); ?>" name="<?php echo $this->get_field_name( 'gce_events_per_page' ); ?>">
				<option value="days" <?php selected( $gce_events_per_page, 'days', true ); ?>><?php _e( 'Number of Days', 'gce' ); ?></option>
				<option value="events" <?php selected( $gce_events_per_page, 'events', true ); ?>><?php _e( 'Number of Events', 'gce' ); ?></option>
				<option value="week" <?php selected( $gce_events_per_page, 'week', true ); ?>><?php _e( 'One Week', 'gce' ); ?></option>
				<option value="month" <?php selected( $gce_events_per_page, 'month', true ); ?>><?php _e( 'One Month', 'gce' ); ?></option>
			</select>
			<span class="gce_per_page_num_wrap <?php echo ( $gce_events_per_page != 'days' && $gce_events_per_page != 'events' ? 'gce-admin-hidden' : '' ); ?>">
				<input type="number" min="0" step="1" class="small-text" name="<?php echo $this->get_field_name( 'gce_per_page_num' ); ?>" id="<?php echo $this->get_field_id( 'gce_per_page_num' ); ?>" value="<?php echo $gce_per_page_num; ?>" />
			</span>
		</p>
		
		<p class="gce-custom-range <?php echo ( $use_range == true ? '' : 'gce-admin-hidden' ); ?>">
			<span>
				<input type="text" name="<?php echo $this->get_field_name( 'gce_feed_range_start' ); ?>" id="<?php echo $this->get_field_id( 'gce_feed_range_start' ); ?>" value="<?php echo $gce_feed_range_start; ?>" />
				<?php _ex( 'to', 'separator between custom date range fields', 'gce' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'gce_feed_range_end' ); ?>" name="<?php echo $this->get_field_name( 'gce_feed_range_end' ); ?>" value="<?php echo $gce_feed_range_end; ?>" />
				<br>
				<span class="description"><?php _e( 'Set how far in the future to retrieve events regardless of initial display.', 'gce' ); ?></span>
			</span>
		</p>
		
		<p class="gce-display-option <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
			<label for="<?php echo $this->get_field_id( 'list_start_offset_num' ); ?>"><?php _e( 'Display Start Date Offset (List View only):', 'gce' ); ?></label><br>
			<input type="number" min="0" step="1" class="small-text" id="<?php echo $this->get_field_id( 'list_start_offset_num' ); ?>" name="<?php echo $this->get_field_name( 'list_start_offset_num' ); ?>" value="<?php echo $list_start_offset_num; ?>" />
			<?php _e( 'Days', 'gce' ); ?>
			<select name="<?php echo $this->get_field_name( 'list_start_offset_direction' ); ?>" id="<?php echo $this->get_field_id( 'list_start_offset_direction' ); ?>">
				<option value="back" <?php selected( $list_start_offset_direction, 'back', true ); ?>><?php _e( 'Back', 'gce' ); ?></option>
				<option value="ahead" <?php selected( $list_start_offset_direction, 'ahead', true ); ?>><?php _e( 'Ahead', 'gce' ); ?></option>
			</select>
		</p>
		
		<p class="gce-display-option gce-display-control <?php echo ( $use_range == true ? 'gce-admin-hidden' : '' ); ?>">
			<label for="<?php echo $this->get_field_id( 'display_title' ); ?>"><?php _e( 'Display Title on Tooltip/List Item (e.g. \'Events on 7th March\'). Grouped lists always have a title displayed.', 'gce' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'display_title_text' ); ?>" name="<?php echo $this->get_field_name( 'display_title_text' ); ?>" value="<?php echo $title_text; ?>" />
		</p>
			
	<?php 
	}
}
add_action( 'widgets_init', create_function( '', 'register_widget("GCE_Widget");' ) );

