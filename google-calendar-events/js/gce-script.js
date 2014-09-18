
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
			gce_year:month_year[1]
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
	
	$('.gce-change-month-list').on( 'click', function(e) {
		
		e.preventDefault();
		
		var month = $(this).data('gce-month');
		var grouped = $(this).parent().parent().data('gce-grouped');
		var title_text = $(this).parent().parent().data('gce-title');
		var feed_ids = $(this).parent().parent().data( 'gce-feeds');
		var sort = $(this).parent().parent().data('gce-sort');
		
		//Add loading text to table caption
		$('.gce-month-title').html('Loading...');
		
		//Send AJAX request
		jQuery.get(gce.ajaxurl,{
			action:'gce_ajax_list',
			gce_feed_ids:feed_ids,
			gce_title_text:title_text,
			gce_month: month,
			gce_grouped: grouped,
			gce_sort: sort
		}, function(data){
			console.log( 'Data', data);
			$('.gce-page-list').html(data);
		});
	});
});