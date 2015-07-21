/* global jQuery */

/**
 * Admin JavaScript functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

(function ($) {
	"use strict";

	$(function () {

		// Show the hidden text box if custom date is selected  (Events per Page)
		$('body').on('change', 'select[id*=events_per_page]', function() {

			// Hide everything before showing what we want
			$(this).parent().children('.gce_per_page_num_wrap').hide();
			
			if( $(this).val() == 'days' || $(this).val() == 'events' ) {
				$(this).parent().children('.gce_per_page_num_wrap').show();
			}
		});
		
		$('body').on('change', 'select[id*=display_type]', function() {

			if( $(this).val() == 'date-range-list' || $(this).val() == 'date-range-grid' ) {
				$(this).parent().parent().children('.gce-display-option').hide();
				$(this).parent().parent().children('.gce-custom-range').show();
			} else {
				$(this).parent().parent().children('.gce-display-option').show();
				$(this).parent().parent().children('.gce-custom-range').hide();
			}
		});
		
		// For main settings page
		$('body').on('change', 'select[id*=gce_display_mode]', function() {
			if( $(this).val() == 'date-range-list' || $(this).val() == 'date-range-grid' ) {
				$('.gce-display-option').hide();
				$('.gce-custom-range').show();
			} else {
				$('.gce-display-option').show();
				$('.gce-custom-range').hide();
			}
		});
		
		// Add jQuery date picker to our custom date fields
		// We have to do it this way because the widget will break after clicking "Save" and this method fixes this problem
		// REF: http://stackoverflow.com/a/10433307/3578774
		$('body').on('focus', 'input[id*=feed_range_start]', function(){
			$(this).datepicker();
		});
		
		$('body').on('focus', 'input[id*=feed_range_end]', function(){
			$(this).datepicker();
		});

		// Trigger CPT Publish/Update click from another button.
		// Form post trigger still pops up JS warning on new CPT.
		$('.gce-feed-update-button').click(function(e) {
			e.preventDefault();

			$('#publish').click();
		});

		// Automatically change the width of shortcode pseudo-inputs.
		$( 'input.gce-shortcode' ).each( function() {
			$( this ).attr( 'size', $( this ).val().length );
		} );
	
	});
}(jQuery));
