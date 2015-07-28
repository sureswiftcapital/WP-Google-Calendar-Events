/* global jQuery */
( function ($) {
	"use strict";

	$( document ).ready( function() {

		// ThickBox hack: https://core.trac.wordpress.org/ticket/17249
		$( '#gce-insert-shortcode-button' ).on( 'click', function() {

			// ThickBox creates a div which is not immediately available.
			setTimeout( function() {
				var thickBox = document.getElementById( 'TB_window');
				if ( thickBox != 'undefined' ) {
					thickBox.classList.add( 'gce-insert-shortcode-modal' );
				}
				var thickBoxTitle = document.getElementById( 'TB_title' );
				if ( thickBoxTitle != 'undefined' ) {
					thickBoxTitle.classList.add( 'gce-insert-shortcode-modal-title' );
				}
			}, 120 );

		} );

		// Add shortcode in WordPress post editor.
		$( '#gce-insert-shortcode').on( 'click', function(e) {

			e.preventDefault();

			var feedId = $( '#gce-choose-gce-feed').val();
			wp.media.editor.insert( '[gcal id="' + feedId + '"] ' );

			// Close Thickbox.
			tb_remove();

		} );

	});

}( jQuery ));
