/**
 * Instagram Custom functions
 * Created by bogx on 28.05.2017
 */

;(function($, window) {
    "use strict";

    /**
     * Local private variables.
     */
    var $window = $(window),
        $body = $('body');
	
    /**
     * Instagram Zellenbreite des Feeds anpassen: load after emotion plugin is loaded
     *
     */
	$.subscribe("plugin/swEmotionLoader/onLoadEmotionFinished", function(me) {			


		//replace emeded svg graphic as img Tag to SVG Tag
		//$('img[src$=".svg"]').each(function() {
		$(".bogx--instagram-icon").each(function() {

			var $img = $(this);
			var imgURL = $img.attr('src');
			var attributes = $img.prop("attributes");

			$.get(imgURL, function(data) {
				// Get the SVG tag, ignore the rest
				var $svg = $(data).find('svg');

				// Remove any invalid XML tags
				$svg = $svg.removeAttr('xmlns:a');

				// Loop through IMG attributes and apply on SVG
				$.each(attributes, function() {
					$svg.attr(this.name, this.value);
				});

				// Replace IMG with SVG
				$img.replaceWith($svg);
			}, 'xml');
		});

    });	

    /**
     * Instagram Zellenbreite des Feeds anpassen: responsive beim Ändern der Display-Breite
     *
     */	
   	$window.on('resize', function() {

		var originCellWidth = $("#bogx_instagram_data").attr("data-cell_width");
		if (originCellWidth) {
			var originCellWidthStr = originCellWidth + "%";				
			//if ($window.width() < 1024) {
			if ($window.width() < 320) {
				$('li.bogx--instagram-cell').css('width',"");
			} else {
				$('li.bogx--instagram-cell').css('width', originCellWidthStr);
			}	
			var currentWidth = $('.bogx--instagram-cell').width();
			var currentHeight = $('.bogx--instagram-cell').height();
			if (currentWidth !== currentHeight) {
				var currentHeightStr = currentWidth + "px";
				$('.bogx--instagram-cell').css('height', currentHeightStr);					
			}
		}
	});	

	
})(jQuery, window);

