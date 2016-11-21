(function($) {

	'use strict';

	$(document).ready(function () {
		bookingsAdmin.init();
	});
	
	var bookingsAdmin = {

		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.selectedTourDate = null;
			window.bookingRequest.selectedTourTime = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalTourOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;	

			if (window.tourId > 0) {
			
				window.bookingRequest.tourId = window.tourId;
				window.bookingRequest.tourScheduleId = window.tourScheduleId;

				bookingsAdmin.loadTourValues();

				if ($('#tour_date').val()) {
					bookingsAdmin.setSelectedTourDate(new Date($('#tour_date').val()).valueOf(), $('.tour_date_span').html());
				}
				
				if ($('#adults').val()) {
					window.bookingRequest.adults = parseInt($('#adults').val());
				}
				
				if ($('#children').val()) {
					window.bookingRequest.children = parseInt($('#children').val());
				}
				
			} else {
				window.bookingRequest.tourId = 0;
				window.bookingRequest.tourScheduleId = 0;
			}
		
			if ($.fn.datepicker) {

				if (typeof($('#booking_datepicker_tour_date')) !== 'undefined') {
				
					$('#booking_datepicker_tour_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#tour_date',
						hourMin: 6,
						hourMax: 18,
						minDate: 0,
						beforeShowDay: function(d) {

							var tUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()).valueOf();
							var today = new Date();
							var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
							var selectedTourTime = bookingsAdmin.getSelectedTourTime();
							var dateTextForCompare = '';					
						
							var datesArray = bookingsAdmin.getAvailableDates();
							if (datesArray) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								
								if (selectedTourTime && tUtc == selectedTourTime)
									return [false, 'dp-highlight'];								
								else if ($.inArray(dateTextForCompare, datesArray) == -1)
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								else if (todayUtc.valueOf() < tUtc && $.inArray(dateTextForCompare, datesArray) > -1)
									return [true, 'dp-highlight'];
							}
						
							return [false, 'dp-highlight'];
						},
						onSelect: function(dateText, inst) {
							
							var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);
							var tourDate = new Date(selectedTime);
							tourDate = tourDate.getFullYear() + "-" + (tourDate.getMonth() + 1) + "-" + tourDate.getDate(); 								

							bookingsAdmin.setSelectedTourDate(selectedTime, dateText);
							window.bookingRequest.tourScheduleId = bookingsAdmin.getTourScheduleId(window.tourId, tourDate);
							$('#tour_schedule_id').val(window.bookingRequest.tourScheduleId);
							bookingsAdmin.loadTourScheduleValues();
						},
						onChangeMonthYear: function (year, month, inst) {
						
							window.currentMonth = month;
							window.currentYear = year;
							window.currentDay = 1;
							
							bookingsAdmin.populateAvailableDays(bookingsAdmin.refreshDatepicker);
						},
					});
					
					if (typeof(window.datepickerTourDateValue) !== 'undefined' && window.datepickerTourDateValue !== null && window.datepickerTourDateValue.length > 0) {
						$('#booking_datepicker_tour_date').datepicker('setDate', window.datepickerTourDateValue);
					}
				}
			}
			
			if (typeof($('.booking_tours_select')) !== 'undefined' && $('.booking_tours_select').length > 0) {
				$('.booking_tours_select').on('change', function() {
					window.bookingRequest.tourId = $(this).val() !== null && $(this).val() !== '' ? parseInt($(this).val()) : 0;
					window.tourId = window.bookingRequest.tourId;
					bookingsAdmin.loadTourValues();
				});
			}
		},
		bindInnerControls : function() {
		
			var countOffset = window.bookingRequest.maxCount - window.bookingRequest.adults - window.bookingRequest.children;
			var maxAdultCount = window.bookingRequest.adults + countOffset;
			if (maxAdultCount < window.bookingRequest.adults) {
				maxAdultCount = parseInt(window.bookingRequest.adults);
			} else if (maxAdultCount > window.bookingRequest.maxCount) {
				maxAdultCount = parseInt(window.bookingRequest.adults);
			}		
		
			var adultsOptions = '';
			$('select.booking_select_adults').find('option').remove();
			for (var i=1;i <= maxAdultCount;i++) {
				adultsOptions += '<option value="'+ i + '" ' + ((window.bookingRequest !== null && window.bookingRequest.adults !== null && window.bookingRequest.adults == i) ? 'selected' : '') + '>' + i + '</option>'; 
			}
			$('select.booking_select_adults').append(adultsOptions);
		
			$('.booking_select_adults').unbind('change');
			$('.booking_select_adults').on('change', function() {
				window.bookingRequest.adults = parseInt($(this).val());
				bookingsAdmin.bindInnerControls();
				bookingsAdmin.calculateAndDisplayRates();
			});
			
			countOffset = window.bookingRequest.maxCount - window.bookingRequest.adults - window.bookingRequest.children;
			var maxChildrenCount = window.bookingRequest.children + countOffset;
			if (maxChildrenCount < window.bookingRequest.children) {
				maxChildrenCount = parseInt(window.bookingRequest.children);
			} else if (maxChildrenCount > window.bookingRequest.maxCount) {
				maxChildrenCount = parseInt(window.bookingRequest.children);
			}
			
			var childrenOptions = '';
			$('select.booking_select_children').find('option').remove();
			for (var j=0;j <= maxChildrenCount;j++) {
				childrenOptions += '<option value="'+ j + '" ' + ((window.bookingRequest !== null && window.bookingRequest.children !== null && window.bookingRequest.children == j) ? 'selected' : '') + '>' + j + '</option>'; 
			}
			$('select.booking_select_children').append(childrenOptions);
			
			$('.booking_select_children').unbind('change');
			$('.booking_select_children').on('change', function() {
				window.bookingRequest.children = parseInt($(this).val());
				bookingsAdmin.bindInnerControls();
				bookingsAdmin.calculateAndDisplayRates();
			});

			$('.booking_select_tour_schedule').unbind('change');
			$('.booking_select_tour_schedule').on('change', function() {
				window.bookingRequest.tourScheduleId = parseInt($(this).val());
				bookingsAdmin.displayControls();
				bookingsAdmin.calculateAndDisplayRates();
			});			
			
		},
		displayControls : function() {

			if (window.bookingRequest.tourId > 0) {
			
				$('.tour_selected.step_1').show();

				if (window.bookingRequest !== null && window.bookingRequest.selectedTourDate !== null) {
					$('.tour_selected.step_2').show();
				}
			
			} else {
				$('.tour_selected').hide();
			}
		},
		calculateAndDisplayRates : function() {

			var adults = window.bookingRequest.adults;
			var children = window.bookingRequest.children;
			var selectedTourDate = bookingsAdmin.getSelectedTourDate();
			var selectedTourTime = selectedTourDate.valueOf();
			
			if (selectedTourDate) {
			
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalTourOnlyPrice = 0;

				bookingsAdmin.calculateDailyTotal(selectedTourTime, adults, children);
				
				$('.total_tour_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalTourOnlyPrice));
				$('#total_tour_price').val(window.bookingRequest.totalTourOnlyPrice);
				$('.total_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalPrice));
				$('#total_price').val(window.bookingRequest.totalPrice);
			}		
		},
		calculateDailyTotal : function(fromTime, adults, children) { 

			var fromDate = new Date(fromTime);
			var pricePerDay = 0;
			var pricePerChild = 0;			
			var dateToCheck = (fromDate.getFullYear() + '-' + ("0" + (fromDate.getMonth() + 1)).slice(-2) + '-' +  ("0" + fromDate.getDate()).slice(-2));
			
			var datesArray = bookingsAdmin.getAvailableDates();
			var tourStartDayIndex = $.inArray(dateToCheck, datesArray);
			var tourDay = null;
			
			if (tourStartDayIndex > -1) {			
				
				tourDay = window.tourAvailableDays[tourStartDayIndex];
				
				pricePerDay = parseFloat(tourDay.price);
				
				pricePerChild = 0;

				if (!window.isPricePerGroup) {
					pricePerChild = parseFloat(tourDay.price_child);
				} 
				
				if (!window.isPricePerGroup) {
					children = children > 0 ? children : 0;
					pricePerDay = (pricePerDay * adults) + (pricePerChild * children);
				}
				
				window.bookingRequest.totalPrice += (pricePerDay);
				window.bookingRequest.totalTourOnlyPrice += (pricePerDay);
			}
		},
		getAvailableDates : function() {
		
			var datesArray = [];
			var i = 0;
			if (window.tourAvailableDays !== null && typeof(window.tourAvailableDays) !== 'undefined') {
				for (i = 0; i < window.tourAvailableDays.length; ++i) {
					if (window.tourAvailableDays[i].tour_date !== null) {
						datesArray.push(window.tourAvailableDays[i].tour_date);
					}
				}
			}
			
			return datesArray;				
		},
		populateAvailableDays : function(callDelegate) {
		
			window.tourAvailableDays = [];

			var dataObj = {
				'action':'tour_admin_available_days_ajax_request',
				'tour_id' : window.bookingRequest.tourId,
				'month' : window.currentMonth,
				'year' : window.currentYear,
				'day' : window.currentDay,
				'current_booking_id' : window.currentBookingId,				
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {

					if (json !== '') {
						window.tourAvailableDays = JSON.parse(json);
					}
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
					
					bookingsAdmin.loadTourScheduleValues();					
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		},
		loadTourValues : function() {

			var dataObj = {
				'action':'tour_admin_get_tour_fields_ajax_request',
				'tour_id' : window.bookingRequest.tourId,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					if (json !== '') {

						var fields = JSON.parse(json);				
				
						window.isPricePerGroup = fields.is_price_per_group == '1' ? true : false;

						bookingsAdmin.populateAvailableDays(bookingsAdmin.loadTourControls);
					}
				},
				error: function(errorThrown) {

				}
			});			
		},
		loadTourControls: function() {
		
			bookingsAdmin.refreshDatepicker();
			
			if (window.isPricePerGroup) {
				$('.per_person').hide();
			} else {
				$('.per_person').show();
			}

			bookingsAdmin.bindInnerControls();
			bookingsAdmin.displayControls();			
		},
		getTourScheduleId : function (tourId, date) {
		
			var scheduleId = 0;
			
			var dataObj = {
				'action':'tour_admin_available_schedule_id_request',
				'tour_id' : tourId,
				'tour_date' : date,
				'nonce' : $('#_wpnonce').val()
			};
			
			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					scheduleId = data;
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
			
			return scheduleId;
		},
		loadTourScheduleValues : function(callDelegate) {
		
			if (window.bookingRequest.tourScheduleId > 0) {

				var dataObj = {
					'action':'tour_admin_get_schedule_fields_ajax_request',
					'tour_schedule_id' : window.bookingRequest.tourScheduleId,
					'nonce' : $('#_wpnonce').val()
				};

				$.ajax({
					url: window.adminAjaxUrl,
					data: dataObj,
					success:function(json) {
						// This outputs the result of the ajax request
						if (json !== '') {

							var fields = JSON.parse(json);				
					
							window.bookingRequest.maxCount = fields.max_people;

							bookingsAdmin.bindInnerControls();
							bookingsAdmin.displayControls();
							
							if (typeof(callDelegate) !== 'undefined') {
								callDelegate();
							}
							
							bookingsAdmin.calculateAndDisplayRates();
						}
					},
					error: function(errorThrown) {

					}
				});			
			}
		},
		setSelectedTourDate : function(time, dateText) {
		
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalTourOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.selectedTourTime = time;
			window.bookingRequest.selectedTourDate = dateText;
			
		},
		getSelectedTourDate: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTourTime !== null) {
				return new Date(parseInt(window.bookingRequest.selectedTourTime));
			}
			return null;			
		},
		getSelectedTourTime: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTourTime !== null) {
				return parseInt(window.bookingRequest.selectedTourTime);
			}
			return null;
		},
		formatPrice: function( price ) {
			if (window.currencySymbolShowAfter)
				return price + ' ' + window.currencySymbol;
			else
				return window.currencySymbol + ' ' + price;
		},
		calculateDifferenceInDays : function( date1, date2) {
			return (Date.UTC(date2.getYear(), date2.getMonth(), date2.getDate()) - Date.UTC(date1.getYear(), date1.getMonth(), date1.getDate())) / 86400000;
		},
		convertLocalToUTC : function (date) { 
			return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
		},
		daysInMonth : function(month, year) {
			return new Date(year, month, 0).getDate();
		},
		refreshDatepicker : function() {
		
			if (typeof $('#booking_datepicker_tour_date') !== 'undefined') {
				$('#booking_datepicker_tour_date').datepicker( "refresh" );
			}			
		},
	};

})(jQuery);