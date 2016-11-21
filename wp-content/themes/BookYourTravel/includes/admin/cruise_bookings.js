(function($) {

	'use strict';

	$(document).ready(function () {
		bookingsAdmin.init();
	});
	
	var bookingsAdmin = {

		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.selectedCruiseDate = null;
			window.bookingRequest.selectedCruiseTime = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCruiseOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;	

			if (window.cruiseId > 0) {
			
				window.bookingRequest.cruiseId = window.cruiseId;
				window.bookingRequest.cruiseScheduleId = window.cruiseScheduleId;
				
				if (window.cabinTypeId > 0) {
					window.bookingRequest.cabinTypeId = window.cabinTypeId;		
				}

				bookingsAdmin.loadCruiseValues();

				if ($('#cruise_date').val()) {
					bookingsAdmin.setSelectedCruiseDate(new Date($('#cruise_date').val()).valueOf(), $('.cruise_date_span').html());
				}
				
				if ($('#adults').val()) {
					window.bookingRequest.adults = parseInt($('#adults').val());
				}
				
				if ($('#children').val()) {
					window.bookingRequest.children = parseInt($('#children').val());
				}
				
			} else {
				window.bookingRequest.cruiseId = 0;
				window.bookingRequest.cruiseScheduleId = 0;
			}
		
			if ($.fn.datepicker) {

				if (typeof($('#booking_datepicker_cruise_date')) !== 'undefined') {
				
					$('#booking_datepicker_cruise_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#cruise_date',
						hourMin: 6,
						hourMax: 18,
						minDate: 0,
						beforeShowDay: function(d) {

							var tUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()).valueOf();
							var today = new Date();
							var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
							var selectedCruiseTime = bookingsAdmin.getSelectedCruiseTime();
							var dateTextForCompare = '';					
						
							var datesArray = bookingsAdmin.getAvailableDates();
							if (datesArray) {
							
								dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
								
								if (selectedCruiseTime && tUtc == selectedCruiseTime)
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
							var cruiseDate = new Date(selectedTime);
							cruiseDate = cruiseDate.getFullYear() + "-" + (cruiseDate.getMonth() + 1) + "-" + cruiseDate.getDate(); 								

							bookingsAdmin.setSelectedCruiseDate(selectedTime, dateText);
							window.bookingRequest.cruiseScheduleId = bookingsAdmin.getCruiseScheduleId(window.bookingRequest.cruiseId, window.bookingRequest.cabinTypeId, cruiseDate);
							$('#cruise_schedule_id').val(window.bookingRequest.cruiseScheduleId);
							bookingsAdmin.loadCruiseScheduleValues();
						},
						onChangeMonthYear: function (year, month, inst) {
						
							window.currentMonth = month;
							window.currentYear = year;
							window.currentDay = 1;
							
							bookingsAdmin.populateAvailableDays(bookingsAdmin.refreshDatepicker);
						},
					});
					
					if (typeof(window.datepickerCruiseDateValue) !== 'undefined' && window.datepickerCruiseDateValue !== null && window.datepickerCruiseDateValue.length > 0) {
						$('#booking_datepicker_cruise_date').datepicker('setDate', window.datepickerCruiseDateValue);
					}
				}
			}
			
			if (typeof($('.booking_cruises_select')) !== 'undefined' && $('.booking_cruises_select').length > 0) {
				$('.booking_cruises_select').on('change', function() {
					window.bookingRequest.cruiseId = $(this).val() !== null && $(this).val() !== '' ? parseInt($(this).val()) : 0;
					window.bookingRequest.cabinTypeId = 0;
					window.bookingRequest.totalPrice = 0;
					window.bookingRequest.totalCruiseOnlyPrice = 0;
					window.bookingRequest.totalDays = 0;
					window.bookingRequest.adults = 1;
					window.bookingRequest.children = 0;
					window.bookingRequest.extraItemsTotalPrice = 0;
					window.bookingRequest.selectedCruiseTime = null;
					window.bookingRequest.selectedCruiseDate = null;
					window.cruiseId = window.bookingRequest.cruiseId;
					bookingsAdmin.loadCruiseValues();
				});
			}
		},
		bindInnerControls : function() {
		
			var adultsOptions = '';
			$('select.booking_select_adults').find('option').remove();
			for (var i=1;i <= window.cruiseMaxAdultsCount;i++) {
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
			for (var j=0;j <= window.cruiseMaxChildrenCount;j++) {
				childrenOptions += '<option value="'+ j + '" ' + ((window.bookingRequest !== null && window.bookingRequest.children !== null && window.bookingRequest.children == j) ? 'selected' : '') + '>' + j + '</option>'; 
			}
			$('select.booking_select_children').append(childrenOptions);
			
			$('.booking_select_children').unbind('change');
			$('.booking_select_children').on('change', function() {
				window.bookingRequest.children = parseInt($(this).val());
				bookingsAdmin.calculateAndDisplayRates();
			});

			$('.booking_select_cruise_schedule').unbind('change');
			$('.booking_select_cruise_schedule').on('change', function() {
				window.bookingRequest.cruiseScheduleId = parseInt($(this).val());
				bookingsAdmin.displayControls();
				bookingsAdmin.calculateAndDisplayRates();
			});			
			
		},
		displayControls : function() {

			if (window.bookingRequest.cruiseId > 0) {
			
				$('.cruise_selected.step_1').show();

				if (window.bookingRequest !== null && window.bookingRequest.cabinTypeId !== null && window.bookingRequest.cabinTypeId > 0) {
					$('.cruise_selected.step_2').show();
				}
				
				if (window.bookingRequest !== null && window.bookingRequest.selectedCruiseDate !== null) {
					$('.cruise_selected.step_3').show();
				}
			
			} else {
				$('.cruise_selected').hide();
			}
		},
		calculateAndDisplayRates : function() {

			var adults = window.bookingRequest.adults;
			var children = window.bookingRequest.children;
			var selectedCruiseDate = bookingsAdmin.getSelectedCruiseDate();
			var selectedCruiseTime = selectedCruiseDate.valueOf();
			
			if (selectedCruiseDate) {
			
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalCruiseOnlyPrice = 0;

				bookingsAdmin.calculateDailyTotal(selectedCruiseTime, adults, children);
				
				$('.total_cruise_price').html(bookingsAdmin.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
				$('#total_cruise_price').val(window.bookingRequest.totalCruiseOnlyPrice);
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
			var cruiseStartDayIndex = $.inArray(dateToCheck, datesArray);
			var cruiseDay = null;
			
			if (cruiseStartDayIndex > -1) {			
				
				cruiseDay = window.cruiseAvailableDays[cruiseStartDayIndex];
				
				pricePerDay = parseFloat(cruiseDay.price);
				
				pricePerChild = 0;

				if (window.isPricePerPerson) {
					children = children - window.countChildrenStayFree;
					children = children > 0 ? children : 0;

					pricePerChild = parseFloat(cruiseDay.price_child);
					pricePerDay = (pricePerDay * adults) + (pricePerChild * children);
				}
				
				window.bookingRequest.totalPrice += (pricePerDay);
				window.bookingRequest.totalCruiseOnlyPrice += (pricePerDay);
			}
		},
		getAvailableDates : function() {
		
			var datesArray = [];
			var i = 0;
			if (window.cruiseAvailableDays !== null && typeof(window.cruiseAvailableDays) !== 'undefined') {
				for (i = 0; i < window.cruiseAvailableDays.length; ++i) {
					if (window.cruiseAvailableDays[i].cruise_date !== null) {
						datesArray.push(window.cruiseAvailableDays[i].cruise_date);
					}
				}
			}
			
			return datesArray;				
		},
		populateAvailableDays : function(callDelegate) {
		
			window.cruiseAvailableDays = [];

			var dataObj = {
				'action':'cruise_admin_available_days_ajax_request',
				'cruise_id' : window.bookingRequest.cruiseId,
				'cabin_type_id' : window.bookingRequest.cabinTypeId,
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
						window.cruiseAvailableDays = JSON.parse(json);
					}
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
					
					bookingsAdmin.loadCruiseScheduleValues();					
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		},
		loadCruiseValues : function() {

			var dataObj = {
				'action':'cruise_admin_get_cruise_fields_ajax_request',
				'cruise_id' : window.bookingRequest.cruiseId,
				'cabin_type_id' : window.bookingRequest.cabinTypeId,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					if (json !== '') {

						var fields = JSON.parse(json);				
				
						window.isPricePerPerson = fields.is_price_per_person == '1' ? true : false;
						window.cabinTypes = fields.cabin_types;
						window.cruiseMaxAdultsCount = fields.max_adult_count;
						window.cruiseMaxChildrenCount = fields.max_child_count;
						
						var cabinTypeOptions = '';
						
						$('select#cabin_type_id').find('option:gt(0)').remove();
						
						if (typeof(window.cabinTypes) !== undefined && window.cabinTypes !== null && window.cabinTypes.length > 0) {

							$.each(window.cabinTypes,function(index) {
								var cabinTypeId = parseInt(window.cabinTypes[index].id);
								cabinTypeOptions += '<option value="'+ window.cabinTypes[index].id + '" ' + ((cabinTypeId == window.bookingRequest.cabinTypeId) ? 'selected' : '') + '>' + window.cabinTypes[index].name + '</option>'; 
							});

							$('select#cabin_type_id').append(cabinTypeOptions);
							
							$('select#cabin_type_id').unbind('change');
							$('select#cabin_type_id').on('change', function() {
								window.bookingRequest.cabinTypeId = parseInt($(this).val());
								bookingsAdmin.populateAvailableDays(bookingsAdmin.loadCruiseValues);
							});
						}
						
						bookingsAdmin.loadCruiseControls();
					}
				},
				error: function(errorThrown) {

				}
			});			
		},
		loadCruiseControls: function() {
		
			bookingsAdmin.refreshDatepicker();
			
			if (window.isPricePerPerson) {
				$('.per_person').show();
			} else {
				$('.per_person').hide();
			}

			bookingsAdmin.bindInnerControls();
			bookingsAdmin.displayControls();			
		},
		getCruiseScheduleId : function (cruiseId, cabinTypeId, date) {
		
			var scheduleId = 0;
			
			var dataObj = {
				'action':'cruise_admin_available_schedule_id_request',
				'cruise_id' : cruiseId,
				'cabin_type_id' : cabinTypeId,
				'cruise_date' : date,
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
		loadCruiseScheduleValues : function(callDelegate) {
		
			if (window.bookingRequest.cruiseScheduleId > 0) {

				var dataObj = {
					'action':'cruise_admin_get_schedule_fields_ajax_request',
					'cruise_schedule_id' : window.bookingRequest.cruiseScheduleId,
					'nonce' : $('#_wpnonce').val()
				};

				$.ajax({
					url: window.adminAjaxUrl,
					data: dataObj,
					success:function(json) {
						// This outputs the result of the ajax request
						if (json !== '') {

							var fields = JSON.parse(json);				
					
							window.totalDays = fields.duration_days;
							window.price = fields.price;
							window.priceChild = fields.price_child;

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
		setSelectedCruiseDate : function(time, dateText) {
		
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCruiseOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.selectedCruiseTime = time;
			window.bookingRequest.selectedCruiseDate = dateText;
			
		},
		getSelectedCruiseDate: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedCruiseTime !== null) {
				return new Date(parseInt(window.bookingRequest.selectedCruiseTime));
			}
			return null;			
		},
		getSelectedCruiseTime: function () {
			if (window.bookingRequest !== null && window.bookingRequest.selectedCruiseTime !== null) {
				return parseInt(window.bookingRequest.selectedCruiseTime);
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
		
			if (typeof $('#booking_datepicker_cruise_date') !== 'undefined') {
				$('#booking_datepicker_cruise_date').datepicker( "refresh" );
			}			
		},
	};

})(jQuery);