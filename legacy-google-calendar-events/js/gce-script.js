/**
 * Public JS functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

/* global jQuery, gce, gce_grid */

(function($) {
	'use strict';

	$(function() {

		var $body = $( 'body'),
			grids    = $body.find('.gce-page-grid, .gce-widget-grid'),
			gce_grid = [ grids.each( function( e, i ) { return $( i ).data( 'feed' ); } ) ],
			tooltip_elements = '';

		$body.find('.gce-page-grid, .gce-widget-grid').each( function( e, i ) {

			var id = $( this ).attr('id'),
				gce_grid = $( this ).data( 'feed' );

			if( gce_grid[id].show_tooltips == 'true' || gce_grid[id].show_tooltips == true ) {
				tooltip_elements += '#' + gce_grid[id].target_element + ' .gce-has-events,';
			}
		});

		tooltip_elements = tooltip_elements.substring( 0, tooltip_elements.length - 1 );

		gce_tooltips(tooltip_elements);

		// Month nav link click for Grid view.
		// TODO Unbind other attached clicks here?
		$body.on( 'click.gceNavLink', '.gce-change-month', function( event ) {

			event.preventDefault();

			var navLink = $(this);

			var grid = navLink.closest('.gce-page-grid'),
				id = grid.attr('id');

			if( typeof id == 'undefined' ) {
				grid = navLink.closest('.gce-widget-grid'),
				id = grid.attr('id');
			}

			var gce_grid = grid.data( 'feed' );

			//Extract month and year
			var month_year = navLink.attr('name').split('-', 2);
			var paging = navLink.attr('data-gce-grid-paging');

			//Add loading text to table caption
			$body.find('#' + gce_grid[id].target_element + ' caption').html(gce.loadingText);

			//Send AJAX request
			$.post(gce.ajaxurl,{
				action:'gce_ajax',
				gce_uid: id,
				gce_type: gce_grid[id].type,
				gce_feed_ids: gce_grid[id].feed_ids,
				gce_title_text: gce_grid[id].title_text,
				gce_widget_id: gce_grid[id].target_element,
				gce_month: month_year[0],
				gce_year: month_year[1],
				gce_paging: paging

			}, function(data) {

				//Replace existing data with returned AJAX data.
				var targetEle = $body.find('#' + gce_grid[id].target_element);

				if (gce_grid[id].type == 'widget') {
					targetEle.html(data);
				} else {
					targetEle.replaceWith(data);
				}

				gce_tooltips(tooltip_elements);

			}).fail(function(data) {
				console.log( data );
			});
		});


		// Month nav link click for List view.
		// TODO Unbind other attached clicks here?
		$body.on( 'click.gceNavLink', '.gce-change-month-list', function( event ) {

			event.preventDefault();

			var navLink = $(this);

			var list = navLink.closest('.gce-list');

			var start = list.data('gce-start');
			var grouped = list.data('gce-grouped');
			var title_text = list.data('gce-title');
			var feed_ids = list.data( 'gce-feeds');
			var sort = list.data('gce-sort');
			var paging = list.data('gce-paging');
			var paging_interval = list.data('gce-paging-interval');
			var paging_direction = navLink.data('gce-paging-direction');
			var start_offset = list.data('gce-start-offset');
			var paging_type = navLink.data('gce-paging-type');

			//Add loading text to table caption
			navLink.parents('.gce-navbar').find('.gce-month-title').html(gce.loadingText);

			//Send AJAX request
			$.post(gce.ajaxurl,{
				action:'gce_ajax_list',
				gce_feed_ids:feed_ids,
				gce_title_text:title_text,
				gce_start: start,
				gce_grouped: grouped,
				gce_sort: sort,
				gce_paging: paging,
				gce_paging_interval: paging_interval,
				gce_paging_direction: paging_direction,
				gce_start_offset: start_offset,
				gce_paging_type: paging_type

			}, function(data){
				navLink.parents('.gce-list').replaceWith(data);

			}).fail(function(data) {
				console.log( data );
			});
		});

		// Tooltip config using qTip2 jQuery plugin.
		function gce_tooltips(target_items) {

			$(target_items).each(function() {

				//Add qtip to all target items
				$(this).qtip({
					content: $(this).find('.gce-event-info'),
					position: {
						my: 'bottom left',
						at: 'center',
						viewport: true,
						adjust: {
							method: 'shift',
							scroll: false
						}
					},
					show: {
						solo: true,
						effect: function(offset) {
							$(this).fadeIn(50);
						}
					},
					hide: {
						fixed: true
					},
					style: {
						classes: 'qtip-light qtip-shadow qtip-rounded'
					}
				});
			});
		}
	});
}(jQuery));
