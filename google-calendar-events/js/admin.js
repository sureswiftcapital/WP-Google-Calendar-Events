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
		
		$('.gce_update').click(function(e) {
			e.preventDefault();
			$('#publish').click();
		});
	
	});
}(jQuery));

