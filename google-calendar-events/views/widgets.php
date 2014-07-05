<?php

/**
 * Represents the view for the widget component of the plugin.
 *
 * @package    PIB
 * @subpackage Includes
 * @author     Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * General Widget TODOs
 * 
 * Support multiple Feed IDs being inserted.
 * Get max number of events working - currently is set to the same as the Feed, is that acceptable? (Going to go this route for now - removing the max events option from widget)
 * Get sort order working
 * Get tooltip title working
 * Check on AJAX grid after fixing that issue elsewhere and make sure it works
 * 
 * Removing AJAX option for now and combining with grid display
 * Removed Checkbox for display tooltip title. I think it should just be a text box and if it is empty then we don't show anything, otherwise we do. Need to 
 * make sure that this feature actually works though.
 */


/**
 *  Class functions for the Pin It Button widgets
 *
 * @since 2.0.0
 */
class GCE_Widget extends WP_Widget {
	
	function GCE_Widget() {
		parent::__construct(
			false, // TODO Add a name here, or will that throw off upgrades?
			$name = __( 'Google Calendar Events', 'gce' ),
			array( 'description' => __( 'Display a list or calendar grid of events from one or more Google Calendar feeds you have added', 'gce' ) )
		);
	}

	function widget( $args, $instance ) {
		extract( $args );

		//Output before widget stuff
		echo $before_widget;

		// Check whether any feeds have been added yet
		if( wp_count_posts( 'gce_feed' )->publish > 0 ) {
			//Output title stuff
			$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}

			$no_feeds_exist = true;
			$feed_ids = array();

			if ( '' != $instance['id'] ) {
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
			} else {
				foreach ( $options as $feed ) {
					$feed_ids[] = $feed['id'];
				}

				$no_feeds_exist = false;
			}

			//Check that at least one valid feed id has been entered
			if ( empty( $feed_ids ) || $no_feeds_exist ) {
				if ( current_user_can( 'manage_options' ) ) {
					_e( 'No valid Feed IDs have been entered for this widget. Please check that you have entered the IDs correctly in the widget settings (Appearance > Widgets), and that the Feeds have not been deleted.', 'gce' );
				} else {
					$options = get_option( GCE_GENERAL_OPTIONS_NAME );
					echo $options['error'];
				}
			} else {
				//Turns feed_ids back into string or feed ids delimited by '-' ('1-2-3-4' for example)
				$feed_ids = implode( '-', $feed_ids );

				$title_text = ( ! empty( $instance['display_title_text'] )  ? $instance['display_title_text'] : null );
				$max_events = ( isset( $instance['max_events'] ) ) ? $instance['max_events'] : 0;
				$sort_order = ( isset( $instance['order'] ) ) ? $instance['order'] : 'asc';
				
				// Set our feed object
				//$feed = new GCE_Feed( $feed_ids );
				$display = new GCE_Display( explode( '-', $feed_ids ) );

				//Output correct widget content based on display type chosen
				/*switch ( $instance['display_type'] ) {
					case 'grid':
						echo '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-widget-' . $feed->id . '-container", "' . $feed_ids . '", "' . $max_events . '", "' . $title_text .'", "widget");});</script>';
						echo $feed->display( 'widget-grid', null, null, true );
						break;
					//case 'ajax':
					//	echo '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-widget-' . $feed->id . '-container", "' . $feed_ids . '", "' . $max_events . '", "' . $title_text .'", "widget");});</script>';
					//	echo $feed->display( 'widget-grid', null, null, true );
					//	break;
					case 'list':
						echo $feed->display( 'widget-list' );
						break;
					case 'list-grouped':
						echo $feed->display( 'widget-list-grouped' );
						break;
				}*/
				$markup = '<script type="text/javascript">jQuery(document).ready(function($){gce_ajaxify("gce-widget-' . $feed_ids . '", "' . $feed_ids . '", "' . $max_events . '", "' . $title_text .'", "widget");});</script>';
				$markup .= '<div class="gce-widget-grid" id="gce-widget-' . $feed_ids . '">';
				$markup .= $display->get_grid( null, null, true );
				$markup .= '</div>';
				
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

	function update( $new_instance, $old_instance ) {
		
		$instance                       = $old_instance;
		$instance['title']              = esc_html( $new_instance['title'] );
		$instance['id']                 = esc_html( $new_instance['id'] );
		$instance['display_type']       = esc_html( $new_instance['display_type'] );
		//$instance['max_events']         = absint( $new_instance['max_events'] );
		$instance['order']              = ( 'asc' == $new_instance['order'] ) ? 'asc' : 'desc';
		//$instance['display_title']      = ( 'on' == $new_instance['display_title'] ) ? true : false;
		$instance['display_title_text'] = wp_filter_kses( $new_instance['display_title_text'] );
		
		return $instance;
	}

	function form( $instance ) {
		// TODO Old GCE Plugin displayed a message if there were no feeds created yet, add that in here eventtually
		
		$title         = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		$ids           = ( isset( $instance['id'] ) ) ? $instance['id'] : '';
		$display_type  = ( isset( $instance['display_type'] ) ) ? $instance['display_type'] : 'grid';
		//$max_events    = ( isset( $instance['max_events'] ) ) ? $instance['max_events'] : 0;
		$order         = ( isset( $instance['order'] ) ) ? $instance['order'] : 'asc';
		$display_title = ( isset( $instance['display_title'] ) ) ? $instance['display_title'] : true;
		$title_text    = ( isset( $instance['display_title_text'] ) ) ? $instance['display_title_text'] : 'Events on';
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>">
				<?php _e( 'Feeds to display, as a comma separated list (e.g. 1, 2, 4). Leave blank to display all feeds:', 'gce' ); ?>
			</label>
			<input type="text" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $ids; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display_type' ); ?>"><?php _e( 'Display events as:', 'gce' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'display_type' ); ?>" name="<?php echo $this->get_field_name( 'display_type' ); ?>" class="widefat">
				<option value="grid"<?php selected( $display_type, 'grid' ); ?>><?php _e( 'Grid', 'gce' ); ?></option>
				<!-- <option value="ajax"<?php selected( $display_type, 'ajax' ); ?>><?php _e( 'Calendar Grid - with AJAX', 'gce' ); ?></option> -->
				<option value="list"<?php selected( $display_type, 'list' ); ?>><?php _e( 'List', 'gce' ); ?></option>
				<option value="list-grouped"<?php selected( $display_type, 'list-grouped' );?>><?php _e( 'Grouped List', 'gce' ); ?></option>
			</select>
		</p>
		<!--
		<p>
			<label for="<?php echo $this->get_field_id( 'max_events' ); ?>"><?php _e( 'Maximum no. events to display. Enter 0 to show all retrieved.' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" value="<?php echo $max_events; ?>" class="widefat" />
		</p>
		-->
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sort order (only applies to lists):' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat">
				<option value="asc"<?php selected( $order, 'asc' ); ?>><?php _e( 'Ascending', 'gce' ); ?></option>
				<option value="desc"<?php selected( $order, 'desc' ); ?>><?php _e( 'Descending', 'gce' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display_title' ); ?>"><?php _e( 'Display title on tooltip / list item (e.g. \'Events on 7th March\') Grouped lists always have a title displayed.', 'gce' ); ?></label>
			<br />
			<!--<input type="checkbox" id="<?php echo $this->get_field_id( 'display_title' ); ?>" name="<?php echo $this->get_field_name( 'display_title' ); ?>"<?php checked( $display_title, true ); ?> value="on" />-->
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'display_title_text' ); ?>" name="<?php echo $this->get_field_name( 'display_title_text' ); ?>" value="<?php echo $title_text; ?>" />
		</p>
			
	<?php 
	}
}
add_action( 'widgets_init', create_function( '', 'register_widget("GCE_Widget");' ) );

