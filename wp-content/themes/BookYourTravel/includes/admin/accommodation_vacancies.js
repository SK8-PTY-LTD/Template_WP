(function($) {

	'use strict';

	$(document).ready(function () {
		vacanciesAdmin.init();
	});
	
	var vacanciesAdmin = {

		init: function () {
		
			$('#accommodations_filter').on('change', function(e) {
				var aId = $(this).val();
				document.location = 'edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php&accommodation_id=' + aId;
			});
		
			if (window.accommodationId > 0) {
				vacanciesAdmin.loadAccommodationValues();
			}
		
			if ($.fn.datepicker) {

				if (typeof($('#vacancy_datepicker_from')) !== 'undefined') {
				
					$('#vacancy_datepicker_from').datepicker({
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
								$('#vacancy_datepicker_to').datepicker('option', 'minDate', d);
							}
						},
						beforeShowDay: function(d) {
							if (window.accommodationCheckinWeekday.length > 0 && window.accommodationCheckinWeekday > -1) {
								var dayOfWeek = d.getDay();
								if (dayOfWeek == (window.accommodationCheckinWeekday)) {
									return [true, 'dp-highlight'];
								} else {
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								}
							}
							return [true, 'dp-highlight'];
						}
					});
					
					if (typeof(window.datepickerDateFromValue) !== 'undefined' && window.datepickerDateFromValue !== null && window.datepickerDateFromValue.length > 0) {
						$('#vacancy_datepicker_from').datepicker('setDate', window.datepickerDateFromValue);
					}
				}
				
				if (typeof($('#vacancy_datepicker_to')) !== 'undefined') {
				
					$('#vacancy_datepicker_to').datepicker({
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
								$('#vacancy_datepicker_from').datepicker('option', 'maxDate', d);
							}
						},
						beforeShowDay: function(d) {
							if (window.accommodationCheckoutWeekday.length > 0 && window.accommodationCheckoutWeekday > -1) {
								var dayOfWeek = d.getDay();
								if (dayOfWeek == (window.accommodationCheckoutWeekday)) {
									return [true, 'dp-highlight'];
								} else {
									return [false, 'ui-datepicker-unselectable ui-state-disabled'];
								}
							}
							return [true, 'dp-highlight'];
						}
					});
					
					if (typeof(window.datepickerDateToValue) !== 'undefined' && window.datepickerDateToValue !== null && window.datepickerDateToValue.length > 0) {
						$('#vacancy_datepicker_to').datepicker('setDate', window.datepickerDateToValue);
					}
				}				
		
			}
			
			if (typeof($('.vacancy_accommodations_select')) !== 'undefined' && $('.vacancy_accommodations_select').length > 0) {
				$('.vacancy_accommodations_select').on('change', function() {
					window.accommodationId = $(this).val() !== null && $(this).val() !== '' ? parseInt($(this).val()) : 0;
					vacanciesAdmin.loadAccommodationValues();		
				});
			}
			
		},
		displayControls : function() {

			if (window.accommodationId > 0) {
			
				if (window.disabledRoomTypes) {
					$('.accommodation_selected.step_1').show();
				} else {
					$('.accommodation_selected.step_0').show();
				}
				
				if (window.roomTypeId > 0) {
					$('.accommodation_selected.step_1').show();
				}
				
				if (window.disabledRoomTypes || window.roomTypeId > 0) {
					
					if (window.rentType == 0) {
						$('.daily_rent').show();
						$('.th_price .first').html(window.pricePerDayLabel);
						
						if (window.isPricePerPerson) {
							$('.per_person').show();
						} else {
							$('.per_person').hide();
						}
					} else {
						$('.daily_rent').hide();
						if (window.rentType == 1) {
							$('.th_price .first').html(window.pricePerWeekLabel);
							$('.th_price_per_child .first').html(window.pricePerWeekLabel);
						} else {
							$('.th_price .first').html(window.pricePerMonthLabel);
							$('.th_price_per_child .first').html(window.pricePerMonthLabel);
						}
						
						if (window.isPricePerPerson) {
							$('.per_person:not(.daily_rent)').show();
						} else {
							$('.per_person').hide();
						}
					}
				}
			
			} else {
				$('.accommodation_selected').hide();
			}
		},
		loadAccommodationValues : function(callDelegate) {

			var dataObj = {
				'action':'accommodation_admin_get_fields_ajax_request',
				'accommodationId' : window.accommodationId,
				'nonce' : $('#_wpnonce').val()
			};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				success:function(json) {
					// This outputs the result of the ajax request
					if (json !== '') {

						var fields = JSON.parse(json);				
				
						window.rentType = fields.rent_type > 0 ? parseInt(fields.rent_type) : 0;
						window.disabledRoomTypes = fields.disabled_room_types == '1' ? true : false;
						window.isPricePerPerson = fields.is_price_per_person == '1' ? true : false;
						window.accommodationCheckinWeekday = fields.checkin_week_day;
						window.accommodationCheckoutWeekday = fields.checkout_week_day;	
						window.accommodationMinDaysStay	= fields.min_days_stay;
						window.accommodationMaxDaysStay = fields.max_days_stay;
						window.countChildrenStayFree = fields.children_stay_free;
						window.roomTypes = fields.room_types;
						
						var roomTypeOptions = '';
						
						$('select#room_type_id').find('option:gt(0)').remove();
						
						if (!window.disabledRoomTypes && typeof(window.roomTypes) !== 'undefined' && window.roomTypes !== null && window.roomTypes.length > 0) {

							$.each(window.roomTypes,function(index) {
								var roomTypeId = parseInt(window.roomTypes[index].id);
								roomTypeOptions += '<option value="'+ window.roomTypes[index].id + '" ' + ((roomTypeId == window.roomTypeId) ? 'selected' : '') + '>' + window.roomTypes[index].name + '</option>'; 
							});

							$('select#room_type_id').append(roomTypeOptions);
							
							$('select#room_type_id').unbind('change');
							$('select#room_type_id').on('change', function() {
								window.roomTypeId = parseInt($(this).val());
								vacanciesAdmin.displayControls();
							});

							$('#room_types_row').show();
							$('#room_count_row').show();
							
						} else { 
							window.roomTypeId = 0;
							$('#room_types_row').hide();
							$('#room_count_row').hide();
						}
						
						vacanciesAdmin.displayControls();
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
		}
	};

})(jQuery);