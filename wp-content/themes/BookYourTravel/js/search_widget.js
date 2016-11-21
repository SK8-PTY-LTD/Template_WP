(function($) {

	$(document).ready(function () {
		search_widget.init();
	});

	var search_widget = {

		init: function () {
		
			$('.search_widget_star').raty({
				scoreName: 'stars',
				score    : window.searchWidgetStars
			});
			
			$( "#search_widget_rating_slider" ).slider({
				range: "min",
				value:window.searchWidgetRating,
				min: 0,
				max: 10,
				step: 1
			});
			
			$("#search_widget_rating_slider").on("slidechange", function(event, ui) {
			   $('input#search_widget_rating').val(ui.value);
			});
		
			$('.spinner input').spinner({ min: 0 });
			
			$('#search_widget_date_from').datepicker({
				dateFormat: window.datepickerDateFormat,
				altFormat: window.datepickerAltFormat,
				altField: "#from",
				showOn: 'button',
				minDate: 0,
				buttonImage: window.themePath + '/images/ico/calendar.png',
				buttonImageOnly: true,
				onClose: function (selectedDate) {
					var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
					if (d !== null && typeof(d) !== 'undefined') {
						d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
						$("#search_widget_date_to").datepicker("option", "minDate", d);
					}
				}
			});
			if (typeof(window.datePickerFromValue) != 'undefined' && window.datePickerFromValue.length > 0) {
				$('#search_widget_date_from').datepicker("setDate", new Date(window.datePickerFromValue));
			}
			
			$('#search_widget_date_to').datepicker({
				dateFormat: window.datepickerDateFormat,
				altFormat: window.datepickerAltFormat,
				altField: "#to",
				showOn: 'button',
				minDate: 0,
				buttonImage: window.themePath + '/images/ico/calendar.png',
				buttonImageOnly: true,
				onClose: function (selectedDate) {
					var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
					if (d !== null && typeof(d) !== 'undefined') {
						d = new Date(d.getFullYear(), d.getMonth(), d.getDate()-1);
						$("#search_widget_date_from").datepicker("option", "maxDate", d);
					}
				}
			});
			if (typeof(window.datePickerToValue) != 'undefined' && window.datePickerToValue.length > 0) {
				$('#search_widget_date_to').datepicker("setDate", new Date(window.datePickerToValue));
			}			
			
			$('.widget-search input[name=what]').on('click', function() {
				window.activeSearchableNumber = parseInt($(this).val());			
				search_widget.setupExtraControls();
			
				$('.widget-search .dt').each(function() {
					$(this).next('dd:not(.what)').hide().css('height','auto').slideUp();
				});
				
				$('.widget-search input[name=what]').prop('checked', false);
				$(this).prop('checked', 'checked');
				$.uniform.update(); 
			});
			
			search_widget.setupExtraControls();
		},
		setupExtraControls : function() {
			switch(window.activeSearchableNumber) {
				case 1:
					$("label[for='search_widget_date_from']").html(window.searchAccommodationDateFromLabel);				
					$("label[for='search_widget_date_to']").html(window.searchAccommodationDateToLabel);
					$(".dt.price_per").html(window.searchWidgetPricePerNightLabel);
					$(".dt.where").html(window.searchAccommodationLocationLabel);
					search_widget.toggleHotelControlsVisibility();
					break;
				case 2:
					$(".dt.price_per").html(window.searchWidgetPricePerDayLabel);
					$("label[for='search_widget_date_from']").html(window.searchCarRentalDateFromLabel);				
					$("label[for='search_widget_date_to']").html(window.searchCarRentalDateToLabel);
					$(".dt.where").html(window.searchCarRentalLocationLabel);					
					search_widget.toggleCarRentalControlsVisibility();
					break;
				case 3:
					$(".dt.price_per").html(window.searchWidgetPricePerPersonLabel);
					$("label[for='search_widget_date_from']").html(window.searchTourDateFromLabel);	
					$(".dt.where").html(window.searchTourLocationLabel);					
					search_widget.toggleTourControlsVisibility();
					break;
				case 4:
					$(".dt.price_per").html(window.searchWidgetPricePerPersonLabel);
					$("label[for='search_widget_date_from']").html(window.searchCruiseDateFromLabel);
					search_widget.toggleCruiseControlsVisibility();
					break;
				default:
					$(".dt.price_per").html(window.searchWidgetPricePerNightLabel);
					$("label[for='search_widget_date_from']").html(window.searchAccommodationDateFromLabel);				
					$("label[for='search_widget_date_to']").html(window.searchAccommodationDateToLabel);
					$(".dt.where").html(window.searchAccommodationLocationLabel);
					
					search_widget.toggleHotelControlsVisibility();
					break;
			}	
		},
		toggleHotelControlsVisibility : function () {
			search_widget.toggleDateFromVisibility(true);
			search_widget.toggleDateToVisibility(true);

			search_widget.toggleRoomsVisibility(true);
			search_widget.toggleRatingControlsVisibility(true);
			search_widget.toggleAccommodationTypeVisibility(true);
			search_widget.toggleGuestsVisibility(false);
			search_widget.toggleCabinsVisibility(false);
			search_widget.toggleDriverAgeVisibility(false);
			search_widget.toggleCarTypeVisibility(false);
			search_widget.toggleTourTypeVisibility(false);
			search_widget.toggleWhereVisibility(true);
			search_widget.toggleCruiseTypeVisibility(false);
		},
		toggleTourControlsVisibility : function() {			
			search_widget.toggleDateFromVisibility(true);
			search_widget.toggleDateToVisibility(false);

			search_widget.toggleRoomsVisibility(false);
			search_widget.toggleRatingControlsVisibility(true);
			search_widget.toggleAccommodationTypeVisibility(false);
			search_widget.toggleGuestsVisibility(true);
			search_widget.toggleCabinsVisibility(false);
			search_widget.toggleDriverAgeVisibility(false);
			search_widget.toggleCarTypeVisibility(false);
			search_widget.toggleTourTypeVisibility(true);
			search_widget.toggleWhereVisibility(true);
			search_widget.toggleCruiseTypeVisibility(false);
		},
		toggleCarRentalControlsVisibility : function() {			
			search_widget.toggleDateFromVisibility(true);
			search_widget.toggleDateToVisibility(true);
			search_widget.toggleRoomsVisibility(false);
			search_widget.toggleRatingControlsVisibility(false);
			search_widget.toggleAccommodationTypeVisibility(false);
			search_widget.toggleGuestsVisibility(false);
			search_widget.toggleCabinsVisibility(false);
			search_widget.toggleDriverAgeVisibility(true);
			search_widget.toggleCarTypeVisibility(true);
			search_widget.toggleTourTypeVisibility(false);
			search_widget.toggleWhereVisibility(true);
			search_widget.toggleCruiseTypeVisibility(false);
		},
		toggleCruiseControlsVisibility : function() {			
			search_widget.toggleDateFromVisibility(true);
			search_widget.toggleDateToVisibility(false);

			search_widget.toggleRoomsVisibility(false);
			search_widget.toggleRatingControlsVisibility(true);
			search_widget.toggleAccommodationTypeVisibility(false);
			search_widget.toggleGuestsVisibility(false);
			search_widget.toggleDriverAgeVisibility(false);
			search_widget.toggleCarTypeVisibility(false);	
			search_widget.toggleTourTypeVisibility(false);
			search_widget.toggleWhereVisibility(true);
			search_widget.toggleCabinsVisibility(true);
			search_widget.toggleCruiseTypeVisibility(true);
		},
		toggleWhereVisibility : function(show) {
			if (show) {
				$(".dt.where").show();
				$(".dd.where").show();
				$("#search_widget_term").prop('disabled', '');
			} else {
				$(".dt.where").hide();
				$(".dd.where").hide();
				$("#search_widget_term").prop('disabled', true);
			}
		},
		toggleRatingControlsVisibility : function (show) {
			if (show) {
				$(".dt.star_rating").show();
				$(".dd.star_rating").show();
				$(".dt.user_rating").show();
				$(".dd.user_rating").show();
				$("input[name='stars']").prop('disabled', '');
				$("input[name='rating']").prop('disabled', '');
			} else {
				$(".dt.star_rating").hide();
				$(".dd.star_rating").hide();
				$(".dt.user_rating").hide();
				$(".dd.user_rating").hide();
				$("input[name='stars']").prop('disabled', true);
				$("input[name='rating']").prop('disabled', true);
			}
		},
		toggleDateToVisibility : function(show) {
			if (show) {
				$("#search_widget_date_to").parent().parent().show();
				$("#search_widget_date_to").prop('disabled', '');
			} else {
				$("#search_widget_date_to").parent().parent().hide();
				$("#search_widget_date_to").prop('disabled', true);
			}
		},
		toggleDateFromVisibility : function(show) {
			if (show) {
				$("#search_widget_date_from").parent().parent().show();
				$("#search_widget_date_from").prop('disabled', '');
			} else {
				$("#search_widget_date_from").parent().parent().hide();
				$("#search_widget_date_from").prop('disabled', true);
			}
		},
		toggleRoomsVisibility : function(show) {
			if (show) {
				$(".dt.rooms").show();
				$(".dd.rooms").show();
				$("#search_widget_rooms").prop('disabled', '');
			} else {
				$(".dt.rooms").hide();
				$(".dd.rooms").hide();
				$("#search_widget_rooms").prop('disabled', true);
			}
		},
		toggleGuestsVisibility : function(show) {
			if (show) {
				$(".dt.guests").show();
				$(".dd.guests").show();
				$("#search_widget_guests").prop('disabled', '');
			} else {
				$(".dt.guests").hide();
				$(".dd.guests").hide();
				$("#search_widget_guests").prop('disabled', true);
			}
		},
		toggleCabinsVisibility : function(show) {
			if (show) {
				$(".dt.cabin_type").show();
				$(".dd.cabin_type").show();
				$(".dt.cabins").show();
				$(".dd.cabins").show();
				$("input[name='cabin_types[]']").prop('disabled', '');
				$("#search_widget_cabins").prop('disabled', '');
			} else {
				$(".dt.cabin_type").hide();
				$(".dd.cabin_type").hide();
				$(".dt.cabins").hide();
				$(".dd.cabins").hide();
				$("input[name='cabin_types[]']").prop('disabled', true);
				$("#search_widget_cabins").prop('disabled', true);
			}
		},
		toggleDriverAgeVisibility : function(show) {
			if (show) {
				$(".dt.age").show();
				$(".dd.age").show();
				$("#search_widget_drivers_age").prop('disabled', '');
			} else {
				$(".dt.age").hide();
				$(".dd.age").hide();
				$("#search_widget_drivers_age").prop('disabled', true);
			}
		},
		toggleTourTypeVisibility : function(show) {
			if (show) {
				$(".dt.tour_type").show();
				$(".dd.tour_type").show();
				$("input[name='tour_types[]']").prop('disabled', '');
			} else {
				$(".dt.tour_type").hide();
				$(".dd.tour_type").hide();
				$("input[name='tour_types[]']").prop('disabled', true);
			}
		},
		toggleCarTypeVisibility : function(show) {
			if (show) {
				$(".dt.car_type").show();
				$(".dd.car_type").show();
				$("input[name='car_types[]']").prop('disabled', '');
			} else {
				$(".dt.car_type").hide();
				$(".dd.car_type").hide();
				$("input[name='car_types[]']").prop('disabled', true);
			}
		},
		toggleCruiseTypeVisibility : function(show) {
			if (show) {
				$(".dt.cruise_type").show();
				$(".dd.cruise_type").show();
				$("input[name='cruise_types[]']").prop('disabled', '');
			} else {
				$(".dt.cruise_type").hide();
				$(".dd.cruise_type").hide();
				$("input[name='cruise_types[]']").prop('disabled', true);
			}
		},
		toggleAccommodationTypeVisibility : function(show) {
			if (show) {
				$(".dt.accommodation_type").show();
				$(".dd.accommodation_type").show();
				$("input[name='accommodation_types[]']").prop('disabled', '');
			} else {
				$(".dt.accommodation_type").hide();
				$(".dd.accommodation_type").hide();
				$("input[name='accommodation_types[]']").prop('disabled', true);
			}
		}		
	};

})(jQuery);