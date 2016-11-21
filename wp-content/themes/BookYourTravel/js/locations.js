(function($) {

	$(document).ready(function () {
		locations.init();
	});
	
	var locations = {

		init: function () {

			$("#gallery").lightSlider({
				item:1,
				rtl: (window.enableRtl ? true : false),
				slideMargin:0,
				auto:true,
				loop:true,
				speed:1500,
				pause:3000,
				keyPress:true,
				gallery:true,
				thumbItem:8,
				galleryMargin:3,
				onSliderLoad: function() {
					$('#gallery').removeClass('cS-hidden');
				}  
			});
		}
	};
	
})(jQuery);