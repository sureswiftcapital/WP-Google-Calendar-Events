<?php

class GCal {
	
	public static $client, $app_name, $scope;
	
	protected static $instance = null;
	
	public function __construct() {
		self::$client = new Google_Client();
		self::default_setup();
	}
	
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	public static function default_setup() {
		self::set_access( 'offline' );
		self::set_app_name( 'Google Calendar Test' );
		self::set_scope( 'https://www.googleapis.com/auth/calendar.readonly' );
		self::set_secret( '9nODcoWlgnvpPxsubsyJ8_sV' );
		self::set_redirect( 'urn:ietf:wg:oauth:2.0:oob' );
	}
	
	public static function set_app_name( $name ) {
		self::$client->setApplicationName( $name );
		self::$client->setClientId( '584787384161-vo0muhir0h6ropa6fj0prp8is7dpd7l3.apps.googleusercontent.com' );
	}
	
	public static function set_scope( $scope ) {
		self::$client->setScopes( $scope );
	}
	
	public static function set_redirect( $redirect ) {
		self::$client->setRedirectUri ( $redirect );
	}
	
	public static function set_secret( $secret ) {
		self::$client->setClientSecret( $secret );
	}
	
	public static function set_access( $type ) {
		self::$client->setAccessType( $type );
	}
	
	public static function get_calendar_data( $id ) {
		
	}
	
	public static function request_access() {
		return self::$client->createAuthUrl();
	}
	
	public static function do_auth( $auth_code ) {
		self::$client->authenticate( $auth_code );
	}
	
	public static function get_client() {
		return self::$client;
	}
	
	public static function get_token() {
		return self::$client->getAccessToken();
	}
	
	public static function set_token( $token ) {
		self::$client->setAccessToken( $token );
	}
}