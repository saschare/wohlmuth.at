/* @version $Id: jquery.relativeSize.js 17663 2010-07-21 13:30:22Z akm $ */
/* @author Andreas Kummer, w3concepts AG */

(function($) {

	$.fn.relativeSize = function() {
		
		var selObjects = this;
		var selector = arguments[0];
		var widthExpr = arguments[1];
		var heightExpr = null;
		
		if (arguments.length > 2) {
			heightExpr = arguments[2];
		}

		if (arguments.length < 4) {
			$(selector).resize(function(){
				selObjects.relativeSize(selector, widthExpr, heightExpr, false);
			});
		}

 		this.each(function() {       		
       		if (widthExpr != null) {
       			$(this).width(eval($(selector).width() + widthExpr));
       		}
       		if (heightExpr != null) {
       			$(this).height(eval($(selector).height() + heightExpr));
       		}
		});
 
		return this;
	};
 })(jQuery);
 
(function($) {
 	
 	$.fn.equalHeights = function() {
 	
 		var targetHeight = 0;
 	
 		this.each(function() {
 			if ($(this).height() > targetHeight) targetHeight = $(this).height();
 		});
 	
 		this.each(function() {
 			$(this).height(targetHeight);
 		});
 	
 		return this;
 	};
 })(jQuery);

(function($) {
 	
 	$.fn.equalWidths = function() {
 	
 		var targetWidth = 0;
 	
 		this.each(function() {
 			if ($(this).width() > targetWidth) targetWidth = $(this).width();
 		});
 	
 		this.each(function() {
 			$(this).width(targetWidth);
 		});
 	
 		return this;
 	};
 })(jQuery);
