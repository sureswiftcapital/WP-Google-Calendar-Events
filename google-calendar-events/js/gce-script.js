
/**
 * Public JS functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */


function gce_ajaxify(target, feed_ids, title_text, type){

	//Add click event to change month links
	jQuery('#' + target + ' .gce-change-month').click(function(){
		//Extract month and year
		var month_year = jQuery(this).attr('name').split('-', 2);
		var paging = jQuery(this).attr('data-gce-grid-paging');
		
		//Add loading text to table caption
		jQuery('#' + target + ' caption').html('Loading...');
		//Send AJAX request
		jQuery.get(gce.ajaxurl,{
			action:'gce_ajax',
			gce_type:type,
			gce_feed_ids:feed_ids,
			gce_title_text:title_text,
			gce_widget_id:target,
			gce_month:month_year[0],
			gce_year:month_year[1],
			gce_paging:paging
		}, function(data){
			//Replace existing data with returned AJAX data
			if(type == 'widget'){
				jQuery('#' + target).html(data);
			}else{
				//console.log( 'Replacing content...' );
				jQuery('#' + target).replaceWith(data);
			}
			gce_tooltips('#' + target + ' .gce-has-events');
		});
	});
}

function gce_tooltips(target_items){
	jQuery(target_items).each(function(){
		//Add qtip to all target items
		jQuery(this).qtip({
			content: jQuery(this).children('.gce-event-info'),
			position: { corner: { target: 'center', tooltip: 'bottomLeft' }, adjust: { screen: true } },
			hide: { fixed: true, delay: 100, effect: { length: 0 } },
			show: { solo: true, delay: 0, effect: { length: 0 } },
			style: { padding: "0", classes: { tooltip: 'gce-qtip', tip: 'gce-qtip-tip', title: 'gce-qtip-title', content: 'gce-qtip-content', active: 'gce-qtip-active' }, border: { width: 0 } }
		});
	});
}

jQuery(document).ready(function($){
	gce_tooltips('.gce-has-events');

	$('.gce-page-list').on( 'click', '.gce-change-month-list', function(e) {
		
		e.preventDefault();
		
		var element = $(this);
		
		var start = $(this).parent().parent().data('gce-start');
		var grouped = $(this).parent().parent().data('gce-grouped');
		var title_text = $(this).parent().parent().data('gce-title');
		var feed_ids = $(this).parent().parent().data( 'gce-feeds');
		var sort = $(this).parent().parent().data('gce-sort');
		var paging = $(this).parent().parent().data('gce-paging');
		var paging_interval = $(this).parent().parent().data('gce-paging-interval');
		var paging_direction = $(this).data('gce-paging-direction');
		var start_offset = $(this).parent().parent().data('gce-start-offset');
		var paging_type = $(this).data('gce-paging-type');
		
		/*if( month > 12 ) {
			month = 1;
			year = year + 1;
		} 
		
		if( month < 1 ) {
			month = 12;
			year = year - 1;
		}*/
		
		//Add loading text to table caption
		$(this).parent().parent().find('.gce-month-title').html('Loading...');
		
		//Send AJAX request
		jQuery.get(gce.ajaxurl,{
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
			element.closest('.gce-page-list').html(data);
		});
	});
});