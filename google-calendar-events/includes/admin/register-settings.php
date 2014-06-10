<?php

/**
 * Register all settings needed for the Settings API.
 *
 * @package    gce
 * @subpackage Includes
 * @author     Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Main function to register all of the plugin settings
 *
 * @since 2.0.0
 */
function gce_register_settings() {
	$gce_settings = array(

		/* General Settings */
		'general' => array(
			'custom_stylesheet' => array(
				'id'   => 'custom_stylesheet',
				'name' => 'Custom Stylesheet URL',
				'desc' => __( 'If you want to alter the default plugin styling, create a new stylesheet on your server (not in the google-calendar-events directory) and then enter its URL below.', 'gce' ),
				'type' => 'text'
			),
			'loading_text' => array(
				'id'   => 'loading_text',
				'name' => 'Loading Text',
				'desc' => __( 'Text to display while calendar data is loading (on AJAX requests).', 'gce' ),
				'type' => 'text',
				'std'  => _x( 'Loading...', 'default for Loading Text settings option', 'gce' )
			),
			'error_message' => array(
				'id'   => 'error_message',
				'name' => 'Error Message',
				'desc' => __( 'An error message to display to non-admin users if events cannot be displayed for any reason (admins will see a message indicating the cause of the problem).', 'gce' ),
				'type' => 'text',
				'std'  => _x( 'Events cannot currently be displayed, sorry! Please check back later.', 'default for Error Message settings option', 'gce' )
			),
			'optimize_retrieval' => array(
				'id'   => 'optimize_retrieval',
				'name' => 'Optimize Event Retrieval',
				'desc' => __( 'Use an experimental feature of the Google Data API, which can improve performance significantly, especially with large numbers of events.', 'gce' ) .
						'<p class="description">' . __( 'Google could potentially remove / change this feature at any time.', 'gce' ) . '</p>',
				'type' => 'checkbox'
			),
			'old_styles' => array(
				'id'   => 'old_styles',
				'name' => 'Use Old Styles',
				'desc' => __( "Add the old CSS (version .7) along with the main stylesheet.", 'gce' ) .
						'<p class="description">' . __( "You should consider updating your stylesheet so that you don't need this enabled.", 'gce' ) . '</p>',
				'type' => 'checkbox'
			)
		)
	);

	/* If the options do not exist then create them for each section */
	if ( false == get_option( 'gce_settings_general' ) ) {
		add_option( 'gce_settings_general' );
	}

	/* Add the General Settings section */
	add_settings_section(
		'gce_settings_general',
		__( 'General Settings', 'gce' ),
		'__return_false',
		'gce_settings_general'
	);

	foreach ( $gce_settings['general'] as $option ) {
		add_settings_field(
			'gce_settings_general[' . $option['id'] . ']',
			$option['name'],
			function_exists( 'gce_' . $option['type'] . '_callback' ) ? 'gce_' . $option['type'] . '_callback' : 'gce_missing_callback',
			'gce_settings_general',
			'gce_settings_general',
			gce_get_settings_field_args( $option, 'general' )
		);
	}

	/* Register all settings or we will get an error when trying to save */
	register_setting( 'gce_settings_general',         'gce_settings_general',         'gce_settings_sanitize' );

}
add_action( 'admin_init', 'gce_register_settings' );

/*
 * Return generic add_settings_field $args parameter array.
 *
 * @since     2.0.0
 *
 * @param   string  $option   Single settings option key.
 * @param   string  $section  Section of settings apge.
 * @return  array             $args parameter to use with add_settings_field call.
 */
function gce_get_settings_field_args( $option, $section ) {
	$settings_args = array(
		'id'      => $option['id'],
		'desc'    => $option['desc'],
		'name'    => $option['name'],
		'section' => $section,
		'size'    => isset( $option['size'] ) ? $option['size'] : null,
		'options' => isset( $option['options'] ) ? $option['options'] : '',
		'std'     => isset( $option['std'] ) ? $option['std'] : ''
	);

	// Link label to input using 'label_for' argument if text, textarea, password, select, or variations of.
	// Just add to existing settings args array if needed.
	if ( in_array( $option['type'], array( 'text', 'select', 'textarea', 'password', 'number' ) ) ) {
		$settings_args = array_merge( $settings_args, array( 'label_for' => 'gce_settings_' . $section . '[' . $option['id'] . ']' ) );
	}

	return $settings_args;
}


/*
 * Single checkbox callback function
 * 
 * @since 2.0.0
 * 
 */
function gce_checkbox_callback( $args ) {
	global $gce_options;

	$checked = isset( $gce_options[$args['id']] ) ? checked( 1, $gce_options[$args['id']], false ) : '';
	$html = "\n" . '<input type="checkbox" id="gce_settings_' . $args['section'] . '[' . $args['id'] . ']" name="gce_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>' . "\n";

	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) )
		$html .= '<label for="gce_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>' . "\n";

	echo $html;
}

/**
 * Textbox callback function
 * Valid built-in size CSS class values:
 * small-text, regular-text, large-text
 * 
 * @since 2.0.0
 * 
 */
function gce_text_callback( $args ) {
	global $gce_options;

	if ( isset( $gce_options[ $args['id'] ] ) )
		$value = $gce_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : '';
	$html = "\n" . '<input type="text" class="' . $size . '" id="gce_settings_' . $args['section'] . '[' . $args['id'] . ']" name="gce_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>' . "\n";

	// Render and style description text underneath if it exists.
	if ( ! empty( $args['desc'] ) )
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";

	echo $html;
}

/*
 * Function we can use to sanitize the input data and return it when saving options
 * 
 * @since 2.0.0
 * 
 */
function gce_settings_sanitize( $input ) {
	//add_settings_error( 'gce-notices', '', '', '' );
	return $input;
}

/*
 *  Default callback function if correct one does not exist
 * 
 * @since 2.0.0
 * 
 */
function gce_missing_callback( $args ) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'gce' ), $args['id'] );
}

/*
 * Function used to return an array of all of the plugin settings
 * 
 * @since 2.0.0
 * 
 */
function gce_get_settings() {
	
	$general_settings         = is_array( get_option( 'gce_settings_general' ) ) ? get_option( 'gce_settings_general' )  : array();

	return array_merge( $general_settings );
}
