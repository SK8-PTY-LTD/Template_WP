(function($) {

	'use strict';

	$(document).ready(function () {
		bookYourTravelAdmin.init();
	});
	
	var bookYourTravelAdmin = {

		init: function () {	
		
			$('#cruises_filter').on('change', function(e) {
				var id = $(this).val();
				document.location = 'edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&cruise_id=' + id;
			});
			
			$('#tours_filter').on('change', function(e) {
				var id = $(this).val();
				document.location = 'edit.php?post_type=tour&page=theme_tour_schedule_admin.php&tour_id=' + id;
			});
		
			$('.tour_type_repeat_type').on('change', function(e) {
				
				var val = parseInt($(this).val(), 10);
					
				if (val === 3 && $(this).hasClass('display_block')) {
					document.getElementById('tr_tour_type_day_of_week').style.display = 'block';
				} else if (val === 3 && $(this).hasClass('display_table_row')) {
					document.getElementById('tr_tour_type_day_of_week').style.display = 'table-row';
				} else {
					document.getElementById('tr_tour_type_day_of_week').style.display = 'none';
				}
				
				if (val === 4 && $(this).hasClass('display_block')) {
					document.getElementById('tr_tour_type_days_of_week').style.display = 'block';
				} else if (val === 4 && $(this).hasClass('display_table_row')) {
					document.getElementById('tr_tour_type_days_of_week').style.display = 'table-row';
				} else {
					document.getElementById('tr_tour_type_days_of_week').style.display = 'none';
				}
			});
			
			$('.cruise_type_repeat_type').on('change', function(e) {
				
				var val = parseInt($(this).val(), 10);
					
				if (val === 3 && $(this).hasClass('display_block')) {
					document.getElementById('tr_cruise_type_day_of_week').style.display = 'block';
				} else if (val === 3 && $(this).hasClass('display_table_row')) {
					document.getElementById('tr_cruise_type_day_of_week').style.display = 'table-row';
				} else {
					document.getElementById('tr_cruise_type_day_of_week').style.display = 'none';
				}
				
				if (val === 4 && $(this).hasClass('display_block')) {
					document.getElementById('tr_cruise_type_days_of_week').style.display = 'block';
				} else if (val === 4 && $(this).hasClass('display_table_row')) {
					document.getElementById('tr_cruise_type_days_of_week').style.display = 'table-row';
				} else {
					document.getElementById('tr_cruise_type_days_of_week').style.display = 'none';
				}
			});			

			if (typeof ($('#accommodation_disabled_room_types')) !== 'undefined' && $('#accommodation_disabled_room_types').length > 0) {
				bookYourTravelAdmin.showHideRoomTypes($('#accommodation_disabled_room_types').is(':checked'));
				$('#accommodation_disabled_room_types').change(function() {
					bookYourTravelAdmin.showHideRoomTypes($(this).is(':checked'));
				});
			}
			
			if (typeof ($('#accommodation_is_price_per_person')) !== 'undefined' && $('#accommodation_is_price_per_person').length > 0) {
				bookYourTravelAdmin.showHideCountChildrenStayFree($('#accommodation_is_price_per_person').is(':checked'));
				$('#accommodation_is_price_per_person').change(function() {
					bookYourTravelAdmin.showHideCountChildrenStayFree($(this).is(':checked'));
				});
			}
		
			if ($.fn.datepicker) {
				if (typeof($('#datepicker_tour_date')) !== 'undefined' && $('#datepicker_tour_date') !== null) {
					$('#datepicker_tour_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#tour_date',
						minDate: 0,
					});
					if (typeof(window.datepickerTourDateValue) !== 'undefined' && window.datepickerTourDateValue !== null && window.datepickerTourDateValue.length > 0) {
						$('#datepicker_tour_date').datepicker('setDate', window.datepickerTourDateValue);
					}
				}
				
				if (typeof($('#datepicker_start_date')) !== 'undefined') {
					$('#datepicker_start_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#start_date',
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
								$('#datepicker_end_date').datepicker('option', 'minDate', d);
							}
						}			
					});
					if (typeof(window.datepickerStartDateValue) !== 'undefined' && window.datepickerStartDateValue !== null && window.datepickerStartDateValue.length > 0) {
						$('#datepicker_start_date').datepicker('setDate', window.datepickerStartDateValue);
					}
				}
				
				if (typeof($('#datepicker_end_date')) !== 'undefined') {
					$('#datepicker_end_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#end_date',
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()-1);
								$('#datepicker_start_date').datepicker('option', 'maxDate', d);
							}
						}			
					});
					if (typeof(window.datepickerEndDateValue) !== 'undefined' && window.datepickerEndDateValue !== null && window.datepickerEndDateValue.length > 0) {
						$('#datepicker_end_date').datepicker('setDate', window.datepickerEndDateValue);
					}
				}

				if (typeof($('#datepicker_cruise_date')) !== 'undefined') {
					$('#datepicker_cruise_date').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#cruise_date',
						minDate: 0,
					});
					if (typeof(window.datepickerCruiseDateValue) !== 'undefined' && window.datepickerCruiseDateValue !== null && window.datepickerCruiseDateValue.length > 0) {
						$('#datepicker_cruise_date').datepicker('setDate', window.datepickerCruiseDateValue);
					}
				}
				
				if (typeof($('#datepicker_from_day')) !== 'undefined') {
					$('#datepicker_from_day').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#from_day',
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
								$('#datepicker_to_day').datepicker('option', 'minDate', d);
							}
						}			
					});
					if (typeof(window.datepickerFromDayValue) !== 'undefined' && window.datepickerFromDayValue !== null && window.datepickerFromDayValue.length > 0) {
						$('#datepicker_from_day').datepicker('setDate', window.datepickerFromDayValue);
					}
				}
				
				if (typeof($('#datepicker_to_day')) !== 'undefined') {
					$('#datepicker_to_day').datepicker({
						dateFormat: window.datepickerDateFormat,
						altFormat: window.datepickerAltFormat,
						altField: '#to_day',
						minDate: 0,
						onClose: function (selectedDate) {
							var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
							if (d && typeof(d) !== 'undefined') {
								d = new Date(d.getFullYear(), d.getMonth(), d.getDate()-1);
								$('#datepicker_from_day').datepicker('option', 'maxDate', d);
							}
						}			
					});
					if (typeof(window.datepickerToDayValue) !== 'undefined' && window.datepickerToDayValue !== null && window.datepickerToDayValue.length > 0) {
						$('#datepicker_to_day').datepicker('setDate', window.datepickerToDayValue);
					}
				}
				
			}
						
			$('#tours_select').on('change', function() {

				var tourId = $(this).val(),
					isPricePerGroup = bookYourTravelAdmin.adminTourIsPricePerGroup(tourId),
					tourTypeIsRepeated = bookYourTravelAdmin.adminTourTypeIsRepeated(tourId);
				
				if (isPricePerGroup) {
					$('.per_person').hide();
					$('.per_group').show();
					$('#price_child').val(0);
				} else {
					$('.per_person').show();
					$('.per_group').hide();
				}
				
				if (tourTypeIsRepeated > 0) {
					$('.is_repeated').show();		
				} else {
					$('.is_repeated').hide();		
				}
				
			});
			
			$('#cruises_select').on('change', function() {

				var cruiseId = $(this).val(),
					isPricePerPerson = bookYourTravelAdmin.adminCruiseIsPricePerPerson(cruiseId),
					cruiseTypeIsRepeated = bookYourTravelAdmin.adminCruiseTypeIsRepeated(cruiseId),
					cabinTypes = bookYourTravelAdmin.listCruiseCabinTypes(cruiseId),
					cabinTypeOptions = '';
				
				$('select#cruise_types_select').find('option:gt(0)').remove();

				$.each(cabinTypes,function(index) {
					cabinTypeOptions += '<option value="'+ cabinTypes[index].id +'">' + cabinTypes[index].name + '</option>'; 
				});

				$('select#cabin_types_select').append(cabinTypeOptions);
				
				$('#cabin_types_row').show();
				$('#cabin_count_row').show();
				
				if (isPricePerPerson) {
					$('.per_person').show();
				} else {
					$('.per_person').hide();
					$('#price_child').val(0);
				}
				
				if (cruiseTypeIsRepeated > 0) {
					$('.is_repeated').show();		
				} else {
					$('.is_repeated').hide();		
				}		
			});
		
		},
		adminTourIsPricePerGroup : function (tourId) {

			var retVal = 0,
				dataObj = {
					'action':'tour_is_price_per_group_ajax_request',
					'tourId' : tourId,
					'nonce' : $('#_wpnonce').val()
				};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown) {

				}
			}); 
			
			return parseInt(retVal, 10);
		},
		adminTourTypeIsRepeated : function (tourId) {

			var retVal = 0,
				dataObj = {
					'action':'tour_type_is_repeated_ajax_request',
					'tourId' : tourId,
					'nonce' : $('#_wpnonce').val()
				};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown) {

				}
			}); 
			
			return parseInt(retVal, 10);
		},
		adminCruiseTypeIsRepeated : function (cruiseId) {

			var retVal = 0,
				dataObj = {
					'action':'cruise_type_is_repeated_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : $('#_wpnonce').val()
				};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown) {

				}
			}); 
			
			return parseInt(retVal, 10);
		},
		adminCruiseIsPricePerPerson : function (cruiseId) {

			var retVal = 0,
				dataObj = {
					'action':'cruise_is_price_per_person_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : $('#_wpnonce').val()
				};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown) {

				}
			}); 
			
			return parseInt(retVal, 10);
		},
		listCruiseCabinTypes : function (cruiseId) {
			
			var retVal = null,
				dataObj = {
					'action':'cruise_list_cabin_types_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : $('#_wpnonce').val() 
				};

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(json) {
					// This outputs the result of the ajax request
					retVal = JSON.parse(json);
				},
				error: function(errorThrown) {
					
				}
			}); 
			
			return retVal;	
		},
		showHideRoomTypes : function (checked) {
			if (checked) {
				$('label[for="room_types"]').closest('tr').hide();
				$('[name="accommodation_max_count"]').closest('tr').show();
				$('[name="accommodation_max_child_count"]').closest('tr').show();
				$('[name="accommodation_min_count"]').closest('tr').show();
				$('[name="accommodation_min_child_count"]').closest('tr').show();
			} else {
				$('label[for="room_types"]').closest('tr').show();
				$('[name="accommodation_max_count"]').closest('tr').hide();
				$('[name="accommodation_max_child_count"]').closest('tr').hide();
				$('[name="accommodation_min_count"]').closest('tr').hide();
				$('[name="accommodation_min_child_count"]').closest('tr').hide();
			}
		},
		showHideCountChildrenStayFree : function (checked) {
			if (checked) {
				$('[name="accommodation_count_children_stay_free"]').closest('tr').show();
			} else {
				$('[name="accommodation_count_children_stay_free"]').closest('tr').hide();
			}
		},		
	};

})(jQuery);

function confirmDelete (form_id, message) {
	var answer = confirm(message);
	if (answer) {
		document.getElementById(form_id.replace('#', '')).submit();
		return true;
	}
	return false;  
}