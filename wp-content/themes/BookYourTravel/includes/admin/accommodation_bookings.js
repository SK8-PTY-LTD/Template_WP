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
			window.bookingRequest.totalAccommodationsOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.roomCount = 1;
			window.bookingRequest.extraItemsTotalPrice = 0;	

			if (window.accommodationId > 0) {
			
				window.bookingRequest.accommodationId = window.accommodationId;
			
				if (window.roomTypeId > 0) {
					window.bookingRequest.roomTypeId = window.roomTypeId;
				}
				
				bookingsAdmin.loadAccommodationValues();

				if ($('#date_from').val()) {
					window.bookingRequest.selectedDateFrom = bookingsAdmin.convertLocalToUTC(new Date($('#date_from').val()));
					window.bookingRequest.selectedTimeFrom = window.bookingRequest.selectedDateFrom.valueOf();
				}
				
				if ($('#date_to').val()) {
					window.bookingRequest.selectedDateTo = bookingsAdmin.convertLocalToUTC(new Date($('#date_to').val()));
					window.bookingRequest.selectedTimeTo = window.bookingRequest.selectedDateTo.valueOf();
				}
				
				if ($('#adults').val()) {
					window.bookingRequest.adults = parseInt($('#adults').val());
				}
				
				if ($('#children').val()) {
					window.bookingRequest.children = parseInt($('#children').val());
				}
				
				if ($('#room_count').val()) {
					window.bookingRequest.roomCount = parseInt($('#room_count').val());
				}
				
			} else {
				window.bookingRequest.accommodationId = 0;
				window.bookingRequest.roomTypeId = 0;
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
						
							if (window.accommodationVacancyStartDays) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								var datesArray = bookingsAdmin.getAccommodationVacancyStartDates();
								
								if (selectedTimeFrom && tUtc == selectedTimeFrom)
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
								
							bookingsAdmin.setAccommodationDateFrom(selectedTime, dateText);
							bookingsAdmin.populateAvailableEndDates(bookingsAdmin.displayControls);
						},
						onChangeMonthYear: function (year, month, inst) {
						
							window.currentMonth = month;
							window.currentYear = year;
							
							bookingsAdmin.populateAvailableStartDays(bookingsAdmin.refreshDatepickerFrom);
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
						
							if (window.accommodationVacancyEndDates) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								
								if (selectedTimeFrom && tUtc == selectedTimeFrom)
									return [false, 'dp-highlight'];								
								else if ($.inArray(dateTextForCompare, window.accommodationVacancyEndDates) == -1)
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								else if (todayUtc.valueOf() < tUtc && $.inArray(dateTextForCompare, window.accommodationVacancyEndDates) > -1)
									return [true, 'dp-highlight'];
							}						
							
							return [false, 'dp-highlight'];
						},
						onSelect: function(dateText, inst) {
						
							var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);
						
							bookingsAdmin.setAccommodationDateTo(selectedTime, dateText);
							bookingsAdmin.displayControls();
							bookingsAdmin.calculateAndDisplayRates();
						}
					});
					
					if (typeof(window.datepickerDateToValue) !== 'undefined' && window.datepickerDateToValue !== null && window.datepickerDateToValue.length > 0) {
						$('#booking_datepicker_to').datepicker('setDate', window.datepickerDateToValue);
					}
				}			
			}
			
			if (typeof($('.booking_accommodations_select')) !== 'undefined' && $('.booking_accommodations_select').length > 0) {
				$('.booking_accommodations_select').on('change', function() {
					window.bookingRequest.accommodationId = $(this).val() !== null && $(this).val() !== '' ? parseInt($(this).val()) : 0;
					bookingsAdmin.loadAccommodationValues();
				});
			}
		},
		bindInnerControls : function() {
		
			var adultsOptions = '';
			$('select.booking_select_adults').find('option').remove();
			for (var i=1;i <= window.accommodationMaxAdultCount;i++) {
				adultsOptions += '<option value="'+ i + '" ' + ((window.bookingRequest !== null && window.bookingRequest.adults !== null && window.bookingRequest.adults == i) ? 'selected' : '') + '>' + i + '</option>'; 
			}
			$('select.booking_select_adults').append(adultsOptions);
		
			$('.booking_select_adults').unbind('change');
			$('.booking_select_adults').on('change', function() {
				window.bookingRequest.adults = parseInt($(this).val());
				bookingsAdmin.calculateAndDisplayRates();
			});
			
			var childrenOptions = '';
			$('select.booking_select_children').find('option').remove();
			for (var j=0;j <= window.accommodationMaxChildCount;j++) {
				childrenOptions += '<option value="'+ j + '" ' + ((window.bookingRequest !== null && window.bookingRequest.children !== null && window.bookingRequest.children == j) ? 'selected' : '') + '>' + j + '</option>'; 
			}
			$('select.booking_select_children').append(childrenOptions);
			
			$('.booking_select_children').unbind('change');
			$('.booking_select_children').on('change', function() {
				window.bookingRequest.children = parseInt($(this).val());
				bookingsAdmin.calculateAndDisplayRates();
			});
			
			var roomCountOptions = '';
			$('select.booking_select_room_count').find('option').remove();
			for (var k=1;k <= window.accommodationMaxRoomCount;k++) {
				roomCountOptions += '<option value="'+ k + '" ' + ((window.bookingRequest !== null && window.bookingRequest.roomCount !== null && window.bookingRequest.roomCount == k) ? 'selected' : '') + '>' + k + '</option>'; 
			}
			$('select.booking_select_room_count').append(roomCountOptions);
			
			$('.booking_select_room_count').unbind('change');
			$('.booking_select_room_count').on('change', function() {
				window.bookingRequest.roomCount = parseInt($(this).val());
				bookingsAdmin.calculateAndDisplayRates();
			});
		},

		displayControls : function() {

			if (window.bookingRequest.accommodationId > 0) {
			
				if (window.disabledRoomTypes) {
					$('.accommodation_selected.step_1').show();
				} else {
					$('.accommodation_selected.step_0').show();
				}
				
				if (window.bookingRequest.roomTypeId > 0) {
					$('.accommodation_selected.step_1').show();
				}
				
				if (window.bookingRequest !== null && window.bookingRequest.selectedDateFrom !== null) {
					$('.accommodation_selected.step_2').show();
				}
				
				if (window.bookingRequest !== null && window.bookingRequest.selectedDateTo !== null) {
					$('.accommodation_selected.step_3').show();
				}
			
			} else {
				$('.accommodation_selected').hide();
			}
		},
		calculateAndDisplayRates : function() {

			var roomCount = window.bookingRequest.roomCount;
			var adults = window.bookingRequest.adults;
			var children = window.bookingRequest.children;
			var selectedDateFrom = bookingsAdmin.getSelectedDateFrom();
			var selectedDateTo = bookingsAdmin.getSelectedDateTo();
			var selectedTimeFrom = selectedDateFrom.valueOf();
			var selectedTimeTo = selectedDateTo.valueOf();
			
			if (selectedDateFrom && selectedDateTo) {
			
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalAccommodationsOnlyPrice = 0;

				while (selectedTimeFrom < selectedTimeTo) {
					bookingsAdmin.calculateDailyTotal(selectedTimeFrom, adults, children, roomCount);
					if (window.accommodationRentType == 1) {
						// weekly
						selectedTimeFrom += (86400000 * 7);
					} else if (window.accommodationRentType == 2) {
						// monthly
						var lastDayOfMonth = bookingsAdmin.daysInMonth(selectedDateFrom.getMonth() + 1, selectedDateFrom.getFullYear());
						selectedTimeFrom += (86400000 * lastDayOfMonth);
					} else {
						// daily
						selectedTimeFrom += 86400000;
					}
				}
				
				$('.total_accommodation_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalAccommodationsOnlyPrice));
				$('#total_accommodation_price').val(window.bookingRequest.totalAccommodationsOnlyPrice);
				$('.total_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalPrice));
				$('#total_price').val(window.bookingRequest.totalPrice);
			}		
		},
		calculateDailyTotal : function(fromTime, adults, children, roomCount) { 

			var fromDate = new Date(fromTime);
			var pricePerDay = 0;
			var pricePerChild = 0;			
			var dateToCheck = (fromDate.getFullYear() + '-' + ("0" + (fromDate.getMonth() + 1)).slice(-2) + '-' +  ("0" + fromDate.getDate()).slice(-2));
						
			var datesArray = bookingsAdmin.getAccommodationVacancyDates();
			var vacancyStartDayIndex = $.inArray(dateToCheck, datesArray);
			var vacancyDay = null;
			
			if (vacancyStartDayIndex > -1) {			
				
				vacancyDay = window.accommodationVacancyDays[vacancyStartDayIndex];
				
				if (vacancyDay.is_weekend && vacancyDay.weekend_price_per_day && vacancyDay.weekend_price_per_day > 0) {
					pricePerDay = parseFloat(vacancyDay.weekend_price_per_day);
				} else {
					pricePerDay = parseFloat(vacancyDay.price_per_day);
				}		
				
				pricePerChild = 0;

				if (window.isPricePerPerson) {
					if (vacancyDay.is_weekend && vacancyDay.weekend_price_per_day_child && vacancyDay.weekend_price_per_day_child > 0) {
						pricePerChild = parseFloat(vacancyDay.weekend_price_per_day_child);
					} else {
						pricePerChild = parseFloat(vacancyDay.price_per_day_child);
					}
				} 
				
				if (window.isPricePerPerson) {
					children = children - window.countChildrenStayFree;
					children = children > 0 ? children : 0;
					pricePerDay = (pricePerDay * adults) + (pricePerChild * children);
				} else {
					pricePerDay = pricePerDay;
				}
				
				window.bookingRequest.totalPrice += (pricePerDay * roomCount);
				window.bookingRequest.totalAccommodationsOnlyPrice += (pricePerDay * roomCount);

			}
				
		},
		getAccommodationVacancyDates : function () {

			var accommodationVacancyDates = [];
			
			if (window.accommodationVacancyDays) {
				$.each(window.accommodationVacancyDays, function(index, day) {
					accommodationVacancyDates.push(day.single_date);				
				});
			}
			
			return accommodationVacancyDates;
		},
		getAccommodationVacancyStartDates: function() {
			var accommodationVacancyStartDates = [];
			
			if (window.accommodationVacancyStartDays) {
				$.each(window.accommodationVacancyStartDays, function(index, day) {
					accommodationVacancyStartDates.push(day.single_date);				
				});
			}
			
			return accommodationVacancyStartDates;
		},
		populateAvailableStartDays : function(callDelegate) {
			
			window.accommodationVacancyStartDays = [];
			
			var dataObj = {
				'action':'accommodation_admin_available_start_days_ajax_request',
				'accommodation_id' : window.bookingRequest.accommodationId,
				'room_type_id' : window.bookingRequest.roomTypeId,
				'month' : window.currentMonth,
				'year' : window.currentYear,
				'nonce' : $('#_wpnonce').val()
			};
			
			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					
					if (json !== '') {
						window.accommodationVacancyStartDays = JSON.parse(json);
					}
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
					
					bookingsAdmin.populateAvailableDays();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		},
		populateAvailableEndDates: function(callDelegate) {
			
			var selectedDateFrom = bookingsAdmin.getSelectedDateFrom();
			window.accommodationVacancyEndDates = [];

			var dataObj = {
				'action':'accommodation_admin_available_end_dates_ajax_request',
				'accommodation_id' : window.bookingRequest.accommodationId,
				'room_type_id' : window.bookingRequest.roomTypeId,
				'start_date' : (selectedDateFrom.getFullYear() + "-" + (selectedDateFrom.getMonth() + 1) + "-" + selectedDateFrom.getDate()),
				'year' : selectedDateFrom.getFullYear(),
				'month' : (selectedDateFrom.getMonth() + 1),
				'day' : selectedDateFrom.getDate(),
				'nonce' : $('#_wpnonce').val()
			};	

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					
					var availableDates = [];
					
					if (json !== '') {
						availableDates = JSON.parse(json);
					}
					 
					var i = 0;
					for (i = 0; i < availableDates.length; ++i) {
						window.accommodationVacancyEndDates.push(availableDates[i].single_date);
					}
					
					var selectedDateFrom = bookingsAdmin.getSelectedDateFrom();
					var year = selectedDateFrom.getFullYear();
					var month = selectedDateFrom.getMonth() + 1;
					var daysInMonth = bookingsAdmin.daysInMonth(month, year);
					
					if (daysInMonth < selectedDateFrom.getDate() || window.accommodationVacancyEndDates.length === 0) {
					
						$("#date_from").val("");
						$("#date_to").val("");							
					
						bookingsAdmin.populateAvailableStartDays(bookingsAdmin.refreshDatepickerFrom);
					}
					
					$('#booking_datepicker_to').datepicker( "refresh" );
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
					
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});		
		},
		populateAvailableDays : function() {
		
			window.accommodationVacancyDays = [];

			var dataObj = {
				'action':'accommodation_admin_available_days_ajax_request',
				'accommodation_id' : window.bookingRequest.accommodationId,
				'room_type_id' : window.bookingRequest.roomTypeId,
				'month' : window.currentMonth,
				'year' : window.currentYear,
				'current_booking_id' : window.currentBookingId,				
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {

					if (json !== '') {
						window.accommodationVacancyDays = JSON.parse(json);
					}
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		},
		loadAccommodationValues : function(callDelegate) {

			var dataObj = {
				'action':'accommodation_admin_get_fields_ajax_request',
				'accommodationId' : window.bookingRequest.accommodationId,
				'roomTypeId' : window.bookingRequest.roomTypeId,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					if (json !== '') {

						var fields = JSON.parse(json);				
				
						window.disabledRoomTypes = fields.disabled_room_types == '1' ? true : false;
						window.isPricePerPerson = fields.is_price_per_person == '1' ? true : false;
						window.accommodationRentType = fields.rent_type > 0 ? fields.rent_type : 0;
						window.accommodationCheckinWeekday = fields.checkin_week_day;
						window.accommodationCheckoutWeekday = fields.checkout_week_day;	
						window.accommodationMinDaysStay	= fields.min_days_stay;
						window.accommodationMaxDaysStay = fields.max_days_stay;
						window.countChildrenStayFree = fields.children_stay_free;
						window.accommodationMinAdultCount = fields.min_adult_count;
						window.accommodationMaxAdultCount = fields.max_adult_count;
						window.accommodationMinChildCount = fields.min_child_count;
						window.accommodationMaxChildCount = fields.max_child_count;
						window.accommodationMaxRoomCount = 1;
						window.roomTypes = fields.room_types;
						
						var roomTypeOptions = '';
						
						$('select#room_type_id').find('option:gt(0)').remove();
						
						if (!window.disabledRoomTypes && typeof(window.roomTypes) !== undefined && window.roomTypes !== null && window.roomTypes.length > 0) {

							$.each(window.roomTypes,function(index) {
								var roomTypeId = parseInt(window.roomTypes[index].id);
								roomTypeOptions += '<option value="'+ window.roomTypes[index].id + '" ' + ((roomTypeId == window.bookingRequest.roomTypeId) ? 'selected' : '') + '>' + window.roomTypes[index].name + '</option>'; 
							});

							$('select#room_type_id').append(roomTypeOptions);
							
							$('select#room_type_id').unbind('change');
							$('select#room_type_id').on('change', function() {
								window.bookingRequest.roomTypeId = parseInt($(this).val());
								bookingsAdmin.displayControls();
								bookingsAdmin.populateAvailableStartDays(bookingsAdmin.loadAccommodationValues);
							});

							$('#room_types_row').show();
							$('#room_count_row').show();
							
						} else { 
							window.bookingRequest.roomTypeId = 0;
							$('#room_types_row').hide();
							$('#room_count_row').hide();
							bookingsAdmin.populateAvailableStartDays();
						}
						
						if (window.isPricePerPerson) {
							$('.per_person').show();
						} else {
							$('.per_person').hide();
						}

						bookingsAdmin.bindInnerControls();
						bookingsAdmin.displayControls();
					}
				},
				error: function(errorThrown) {

				}
			});			
		},
		setAccommodationDateFrom : function(time, dateText) {
		
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalAccommodationsOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.roomCount = 1;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.selectedTimeFrom = time;
			window.bookingRequest.selectedDateFrom = dateText;
			window.bookingRequest.selectedTimeTo = null;
			window.bookingRequest.selectedDateTo = null;
			
			$('#booking_datepicker_to').datepicker('setDate', null);
			$("#date_to").val(null);
		},
		setAccommodationDateTo: function(time, dateText) {

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