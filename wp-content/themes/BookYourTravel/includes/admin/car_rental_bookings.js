(function($) {

	'use strict';

	$(document).ready(function () {
		bookingsAdmin.init();
	});
	
	var bookingsAdmin = {

		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.selectedDateFrom = null;
			window.bookingRequest.selectedTimeFrom = 0;
			window.bookingRequest.selectedDateTo = null;
			window.bookingRequest.selectedTimeTo = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCarRentalOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;	

			if (window.carRentalId > 0) {
			
				window.bookingRequest.carRentalId = window.carRentalId;
			
				bookingsAdmin.loadCarRentalValues();

				if ($('#date_from').val()) {
					window.bookingRequest.selectedDateFrom = bookingsAdmin.convertLocalToUTC(new Date($('#date_from').val()));
					window.bookingRequest.selectedTimeFrom = window.bookingRequest.selectedDateFrom.valueOf();
				}
				
				if ($('#date_to').val()) {
					window.bookingRequest.selectedDateTo = bookingsAdmin.convertLocalToUTC(new Date($('#date_to').val()));
					window.bookingRequest.selectedTimeTo = window.bookingRequest.selectedDateTo.valueOf();
				}
				
			} else {
				window.bookingRequest.carRentalId = 0;
			}
		
			if ($.fn.datepicker) {

				if (typeof($('#booking_datepicker_from')) !== 'undefined') {
				
					$('#booking_datepicker_from').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#date_from',
						hourMin: 6,
						hourMax: 18,
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
								$('#booking_datepicker_to').datepicker('option', 'minDate', d);
							}
						},
						beforeShowDay: function(d) {

							var tUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()).valueOf();
							var today = new Date();
							var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
							var selectedTimeFrom = bookingsAdmin.getSelectedTimeFrom();
							var dateTextForCompare = '';					
						
							if (window.carRentalBookedOutDays != null && typeof(window.carRentalBookedOutDays) !== 'undefined' && window.carRentalBookedOutDays.length > 0) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								
								if (selectedTimeFrom && tUtc == selectedTimeFrom)
									return [false, 'dp-highlight'];								
								else if ($.inArray(dateTextForCompare, window.carRentalBookedOutDays) > -1)
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								else if (todayUtc.valueOf() < tUtc && $.inArray(dateTextForCompare, window.carRentalBookedOutDays) == -1)
									return [true, 'dp-highlight'];
							}
						
							return [true, 'dp-highlight'];
						},
						onSelect: function(dateText, inst) {
							
							var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);
								
							bookingsAdmin.setCarRentalDateFrom(selectedTime, dateText);
						},
						onChangeMonthYear: function (year, month, inst) {
						
							window.currentMonth = month;
							window.currentYear = year;
							window.currentDay = 1;
							
							bookingsAdmin.populateCarRentalBookedOutDates(window.bookingRequest.carRentalId, window.currentMonth, window.currentYear, bookingsAdmin.refreshDatepickerFrom);
						},
					});
					
					if (typeof(window.datepickerDateFromValue) !== 'undefined' && window.datepickerDateFromValue !== null && window.datepickerDateFromValue.length > 0) {
						$('#booking_datepicker_from').datepicker('setDate', window.datepickerDateFromValue);
					}
				}
				
				if (typeof($('#booking_datepicker_to')) !== 'undefined') {
				
					$('#booking_datepicker_to').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#date_to',
						hourMin: 6,
						hourMax: 18,
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()-1);
								$('#booking_datepicker_from').datepicker('option', 'maxDate', d);
							}
						},
						beforeShowDay: function(d) {

							var tUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()).valueOf();
							var today = new Date();
							var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
							var selectedTimeFrom = bookingsAdmin.getSelectedTimeFrom();
							var dateTextForCompare = '';							
						
							if (window.carRentalBookedOutDays != null && typeof(window.carRentalBookedOutDays) !== 'undefined' && window.carRentalBookedOutDays.length > 0) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								
								if (selectedTimeFrom && tUtc == selectedTimeFrom)
									return [false, 'dp-highlight'];								
								else if ($.inArray(dateTextForCompare, window.carRentalBookedOutDays > -1))
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								else if (todayUtc.valueOf() < tUtc && $.inArray(dateTextForCompare, window.carRentalBookedOutDays) == -1)
									return [true, 'dp-highlight'];
							}						
							
							return [true, 'dp-highlight'];
						},
						onSelect: function(dateText, inst) {
						
							var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);
						
							bookingsAdmin.setCarRentalDateTo(selectedTime, dateText);
							bookingsAdmin.displayControls();
							bookingsAdmin.calculateAndDisplayRates();
						},
						onChangeMonthYear: function (year, month, inst) {
						
							window.currentMonth = month;
							window.currentYear = year;
							window.currentDay = 1;
							
							bookingsAdmin.populateCarRentalBookedOutDates(window.bookingRequest.carRentalId, window.currentMonth, window.currentYear, bookingsAdmin.refreshDatepickerTo);
						},
					});
					
					if (typeof(window.datepickerDateToValue) !== 'undefined' && window.datepickerDateToValue !== null && window.datepickerDateToValue.length > 0) {
						$('#booking_datepicker_to').datepicker('setDate', window.datepickerDateToValue);
					}
				}			
			}
			
			if (typeof($('.booking_car_rentals_select')) !== 'undefined' && $('.booking_car_rentals_select').length > 0) {
				$('.booking_car_rentals_select').on('change', function() {
					window.bookingRequest.carRentalId = $(this).val() !== null && $(this).val() !== '' ? parseInt($(this).val()) : 0;
					bookingsAdmin.loadCarRentalValues();
				});
			}
		},

		displayControls : function() {

			if (window.bookingRequest.carRentalId > 0) {
			
				$('.car_rental_selected.step_1').show();
				
				if (window.bookingRequest !== null && window.bookingRequest.selectedDateFrom !== null) {
					$('.car_rental_selected.step_2').show();
				}
				
				if (window.bookingRequest !== null && window.bookingRequest.selectedDateTo !== null) {
					$('.car_rental_selected.step_3').show();
				}
			
			} else {
				$('.car_rental_selected').hide();
			}
		},
		calculateAndDisplayRates : function() {

			var selectedDateFrom = bookingsAdmin.getSelectedDateFrom();
			var selectedDateTo = bookingsAdmin.getSelectedDateTo();
			var selectedTimeFrom = selectedDateFrom.valueOf();
			var selectedTimeTo = selectedDateTo.valueOf();
			
			if (selectedDateFrom && selectedDateTo) {
			
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalCarRentalOnlyPrice = 0;

				while (selectedTimeFrom < selectedTimeTo) {
					bookingsAdmin.calculateDailyTotal(selectedTimeFrom);
					selectedTimeFrom += 86400000;
				}
				
				$('.total_car_rental_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
				$('#total_car_rental_price').val(window.bookingRequest.totalCarRentalOnlyPrice);
				$('.total_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalPrice));
				$('#total_price').val(window.bookingRequest.totalPrice);
			}		
		},
		calculateDailyTotal : function(fromTime) { 

			var fromDate = new Date(fromTime);
			var pricePerDay = parseFloat(window.bookingRequest.pricePerDay);

			var dateToCheck = (fromDate.getFullYear() + '-' + ("0" + (fromDate.getMonth() + 1)).slice(-2) + '-' +  ("0" + fromDate.getDate()).slice(-2));
						
			var startDayIndex = $.inArray(dateToCheck, window.carRentalBookedOutDays);
			
			if (startDayIndex == -1) {			
				
				window.bookingRequest.totalPrice += (pricePerDay);
				window.bookingRequest.totalCarRentalOnlyPrice += (pricePerDay);
			}
				
		},
		loadCarRentalValues : function(callDelegate) {

			var dataObj = {
				'action':'car_rental_admin_get_fields_ajax_request',
				'carRentalId' : window.bookingRequest.carRentalId,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					if (json !== '') {

						var fields = JSON.parse(json);				
				
						window.bookingRequest.pricePerDay = fields.price_per_day;

						bookingsAdmin.displayControls();
					}
				},
				error: function(errorThrown) {

				}
			});			
		},
		setCarRentalDateFrom : function(time, dateText) {
		
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCarRentalOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.selectedTimeFrom = time;
			window.bookingRequest.selectedDateFrom = dateText;
			window.bookingRequest.selectedTimeTo = null;
			window.bookingRequest.selectedDateTo = null;
			
			$('#booking_datepicker_to').datepicker('setDate', null);
			$("#date_to").val(null);
			
			bookingsAdmin.displayControls();
		},
		setCarRentalDateTo: function(time, dateText) {

			window.bookingRequest.selectedTimeTo = time;			
			window.bookingRequest.selectedDateTo = dateText;		
		},
		getSelectedDateFrom: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTimeFrom !== null) {
				return bookingsAdmin.convertLocalToUTC(new Date(parseInt(window.bookingRequest.selectedTimeFrom)));
			}
			return null;			
		},
		getSelectedDateTo: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTimeTo !== null) {
				return bookingsAdmin.convertLocalToUTC(new Date(parseInt(window.bookingRequest.selectedTimeTo)));
			}
			return null;
		},
		getSelectedTimeFrom: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTimeFrom !== null) {
				return parseInt(window.bookingRequest.selectedTimeFrom);
			}
			return null;
		},
		getSelectedTimeTo: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedTimeTo !== null) {
				return parseInt(window.bookingRequest.selectedTimeTo);
			}
			return null;
		},
		populateCarRentalBookedOutDates : function (carRentalId, month, year, callDelegate) {
			
			window.carRentalBookedOutDays = [];

			var dataObj = {
				'action':'car_rental_booked_dates_request',
				'car_rental_id' : carRentalId,
				'month' : month,
				'year' : year,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					var bookedDates = JSON.parse(json);
					var i = 0;
					for (i = 0; i < bookedDates.length; ++i) {
						window.carRentalBookedOutDays.push(bookedDates[i].booking_date);
					}
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
				},
				error: function(errorThrown) {

				}
			});
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
		refreshDatepickerFrom : function() {
		
			if (typeof $('#booking_datepicker_from') !== 'undefined') {
				$('#booking_datepicker_from').datepicker( "refresh" );
			}			
		},
		refreshDatepickerTo : function() {
		
			if (typeof $('#booking_datepicker_to') !== 'undefined') {
				$('#booking_datepicker_to').datepicker( "refresh" );
			}			
		}
	};

})(jQuery);