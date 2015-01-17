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
			$('.gce_per_page_num_wrap').hide();
			
			if( $(this).val() == 'days' || $(this).val() == 'events' ) {
				$('.gce_per_page_num_wrap').show();
			}
		});
		
		$('#gce_feed_use_range').on('change', function() {

			// Hide everything before showing what we want
			$('.gce-display-option').toggle();
			$('.gce-custom-range').toggle();
		});
		
		
		// Add jQuery date picker to our custom date fields
		$('#gce_feed_range_start').datepicker();
		$('#gce_feed_range_end').datepicker();
	
	});
}(jQuery));


