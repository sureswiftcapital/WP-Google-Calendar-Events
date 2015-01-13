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
		$('#gce_events_per_page').on('change', function() {

			// Hide everything before showing what we want
			$('.gce_per_page_num_wrap, .gce_per_page_custom_wrap').hide();
			
			if( $(this).val() == 'custom' ) {
				$('.gce_per_page_custom_wrap').show();
			} 
			
			if( $(this).val() == 'days' || $(this).val() == 'events' ) {
				$('.gce_per_page_num_wrap').show();
			}
		});
		
		// Show the hidden text box if custom date is selected (Feed Start)
		$('#gce_feed_start').on('change', function() {

			// Hide everything before showing what we want
			$('.gce_feed_start_num_wrap, .gce_feed_start_custom_wrap').hide();
			
			if( $(this).val() == 'custom' ) {
				$('.gce_feed_start_custom_wrap').show();
			} else {
				$('.gce_feed_start_num_wrap').show();
			}
		});
		
		// Show the hidden text box if custom date is selected (Feed End)
		$('#gce_feed_end').on('change', function() {
			
			// Hide everything before showing what we want
			$('.gce_feed_end_num_wrap, .gce_feed_end_custom_wrap').hide();
			
			if( $(this).val() == 'custom' ) {
				$('.gce_feed_end_custom_wrap').show();
			} else {
				$('.gce_feed_end_num_wrap').show();
			}
		});
		
		
		
		// Add jQuery date picker to our custom date fields
		$('#gce_per_page_from').datepicker();
		$('#gce_per_page_to').datepicker();
		$('#gce_feed_start_custom').datepicker();
		$('#gce_feed_end_custom').datepicker();
	
	});
}(jQuery));


