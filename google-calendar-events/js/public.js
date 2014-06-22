(function($) {
    'use strict';

    $(function() {
		
		$('#calendar').fullCalendar({
		
			// US Holidays
			events: gce.url,
			
			eventClick: function(event) {
				// opens events in a popup window
				window.open(event.url, 'gcalevent', 'width=700,height=600');
				return false;
			},
			
			loading: function(bool) {
				$('#loading').toggle(bool);
			}
			
		});

    });

}(jQuery));
