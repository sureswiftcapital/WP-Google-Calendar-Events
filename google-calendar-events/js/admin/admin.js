
/**
 * Public JS functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */
(function($) {
	'use strict';

	$(function() {
		
		$('#gce-auth').on('click', function() {
			
			var data = {
				action: 'gce_auth',
				auth_action: 'authorize',
				auth_code: $('.auth_code').val()
			}

			$.post( ajaxurl, data, function(response) {
				if( response == 'success' ) {
					location.reload(true);
				}
			});
		});
		
		$('#gce-clear-auth').on('click', function() {
			var data = {
				action: 'gce_auth',
				auth_action: 'clear_auth'
			}

			$.post( ajaxurl, data, function(response) {
				if( response == 'success' ) {
					location.reload(true);
				}
			});
		});
		
	});
}(jQuery))