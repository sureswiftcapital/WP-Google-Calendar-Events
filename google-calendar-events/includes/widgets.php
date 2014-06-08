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
 *  Class functions for the Pin It Button widgets
 *
 * @since 2.0.0
 */
class GCE_Widget extends WP_Widget {
	
	/**
	* Initialize the widget
	*
	* @since 2.0.0
	*/
	public function __construct() {
		parent::__construct(
			'GCE_Widget',
			__( 'Google Calendar Events', 'gce' ),
			array(
				'description'	=>	__( 'Add a Google Calendar Event widget.', 'gce' )
			),
			// Widen widget admin area.
			array( 'width' => 400 )
		);
	}
	
	/**
	* Public facing widget code
	*
	* @since 2.0.0
	*/
	public function widget( $args, $instance ) {
      
		extract( $args );
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );		
        
        $message = $instance['message'];
		
		echo $before_widget;
        
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
        }
		
		echo $message;
		
		echo $after_widget;
	}
	
	/**
	* Update the widget settings from user input
	*
	* @since 2.0.0
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']                     = strip_tags($new_instance['title']);
		$instance['message']                   = strip_tags($new_instance['message']);
        
		return $instance;
	}
	
	/**
	* Display widget settings in admin
	*
	* @since 2.0.0
	*/
	public function form( $instance ) {
		
		$default = array(
			'title'                     => '',
			'message'                   => '',
		);
        
		$instance = wp_parse_args( (array) $instance, $default );
		
		$title                     = strip_tags($instance['title']);
		$message = strip_tags( $instance['message'] );
		
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional)', 'gce' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'message' ); ?>"><?php _e( 'Message', 'gce' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'message' ); ?>" name="<?php echo $this->get_field_name( 'message' ); ?>" type="text" value="<?php echo esc_attr( $message ); ?>" />
		</p>
		
        <?php
	}
}
add_action( 'widgets_init', create_function( '', 'register_widget("GCE_Widget");' ) );
