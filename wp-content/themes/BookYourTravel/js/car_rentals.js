(function($) {

	$(document).ready(function () {
		car_rentals.init();
	});
	
	var car_rentals = {

		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.extraItems = {};
			window.bookingRequest.people = 1;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.totalCarRentalOnlyPrice = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalDays = 1;

			$('.extra_items_total').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
			$('.total_price').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));		
			$('.reservation_total').html(car_rentals.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
			
			$('.toggle_breakdown').unbind('click');
			$('.toggle_breakdown').on('click', function(e) {
				if ($('.price_breakdown_row').hasClass('hidden')) {
					$('.price_breakdown_row').removeClass('hidden');
					if (window.enableExtraItems) {
						$('.price_breakdown_row').show();
					} else {
						$('.price_breakdown_row:not(.extra_items_breakdown_row)').show();
					}
					$('.toggle_breakdown').html(window.hidePriceBreakdownLabel);
				} else {
					$('.price_breakdown_row').addClass('hidden');				
					$('.price_breakdown_row').hide();
					$('.toggle_breakdown').html(window.showPriceBreakdownLabel);
				}
				
				e.preventDefault();
			});
			
			if (window.carRentalIsReservationOnly || !window.useWoocommerceForCheckout) {

				$('#car_rental-booking-form').validate({
					onkeyup: false,
					ignore: [],
					invalidHandler: function(e, validator) {
						var errors = validator.numberOfInvalids();
						if (errors) {
							var message = errors == 1 ? window.formSingleError : window.formMultipleError.format(errors);
							$("div.error div p").html(message);
							$("div.error").show();
						} else {
							$("div.error").hide();
						}
					},
					submitHandler: function() { 
						car_rentals.processBooking(); 
					}
				});				
				$.each(window.bookingFormFields, function(index, field) {
				
					if (field.hide !== '1' && field.id !== null && field.id.length > 0) {
						var $input = null;
						if (field.type == 'text' || field.type == 'email') {
							$input = $('.car_rental-booking-form').find('input[name=' + field.id + ']');
						} else if (field.type == 'textarea') {
							$input = $('.car_rental-booking-form').find('textarea[name=' + field.id + ']');
						}
						
						if ($input !== null && typeof($input) !== 'undefined') {
							if (field.required == '1') {
								$input.rules('add', {
									required: true,
									messages: {
										required: window.bookingFormRequiredError
									}
								});
							}
							if (field.type == 'email') {
								$input.rules('add', {
									email: true,
									messages: {
										required: window.bookingFormEmailError
									}
								});
							}
						}
					}
				});
			}
			
			$('.booking-commands').hide();

			$('.extra_item_quantity').uniform();
			$('.booking_form_drop_off').uniform();
			
			$('.radio').bind('click.uniform',
				function (e) {
					if ($(this).find("span").hasClass('checked')) 
						$(this).find("input").attr('checked', true);
					else
						$(this).find("input").attr('checked', false);
				}
			);
			
			car_rentals.bindGallery();	
			car_rentals.bindResetButton();
			car_rentals.bindNextButton();
			car_rentals.bindCancelButton();

			if (window.enableExtraItems) {
				car_rentals.bindExtraItemsQuantitySelect();
				car_rentals.buildExtraItemsTable();
			}
			
			car_rentals.populateCarRentalBookedOutDates(window.carRentalId, window.currentMonth, window.currentYear, car_rentals.bindCarRentalDatePicker);
		},
		bindRequiredExtraItems: function() {
			if (typeof(window.requiredExtraItems) !== 'undefined' && window.requiredExtraItems.length > 0) {
				$.each( window.requiredExtraItems, function( index, extraItemId ){
					car_rentals.updateExtraItemSelection(extraItemId, 1);
					$('#extra_item_quantity_' + extraItemId).val('1');					
				});
			}			
		},		
		bindNextButton : function() {
		
			$('.book-car_rental-proceed').unbind('click');
			$('.book-car_rental-proceed').on('click', function(event) {
			
				if (!window.carRentalIsReservationOnly && window.useWoocommerceForCheckout) {
				
					car_rentals.addProductToCart();
				
				} else {
			
					$('#wait_loading').show();
					
					car_rentals.showCarRentalBookingForm();
					
					$('#wait_loading').hide();
					
					$('body,html').animate({
						scrollTop: 0
					}, 800);
				}
				
				event.preventDefault();
			});
		},
		bindResetButton : function() {
		
			$('.book-car_rental-reset').unbind('click');
			$('.book-car_rental-reset').on('click', function(event) {
				
				window.bookingRequest = {};
				window.bookingRequest.extraItems = {};
				window.bookingRequest.people = 1;
				window.bookingRequest.extraItemsTotalPrice = 0;
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalCarRentalOnlyPrice = 0;
				window.bookingRequest.totalDays = 1;
				
				event.preventDefault();

				$(".extra_item_quantity").val("0");				
				$("#start_date_span").html('');
				$("#start_date").val('');
				$("#end_date_span").html('');
				$("#end_date").val('');
				$(".dates_row").hide();
				$(".price_row").hide();
				$('.booking-commands').hide();
				$('table.car_rental_price_breakdown thead').html('');
				$('table.car_rental_price_breakdown tbody').html('');
				$('table.car_rental_price_breakdown tfoot').html('');
				
				$('.reservation_total').html(car_rentals.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
				$('.extra_items_total').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));
				$('.confirm_total_price_p').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));
				
				$('.extra_items_total').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));
				$('table.extra_items_price_breakdown tbody').html('');	
				
				car_rentals.populateCarRentalBookedOutDates(window.carRentalId, window.currentMonth, window.currentYear, car_rentals.refreshDatePicker);
			});
		},
		bindCancelButton: function() {
			$('#cancel-car_rental-booking').unbind('click');
			$('#cancel-car_rental-booking').on('click', function(event) {
				car_rentals.hideCarRentalBookingForm();
				car_rentals.showCarRentalScreen();
				$('.error').hide();
				event.preventDefault();
			});	
		},
		showCarRentalScreen : function () {
		
			$('.three-fourth .lSSlideOuter').show();
			$('.three-fourth .inner-nav').show();
			$('.three-fourth .tab-content').show();
			$(".tab-content").hide();
			$(".tab-content:first").show();
			$(".inner-nav li:first").addClass("active");
		},
		showCarRentalBookingForm : function () {
		
			$('.booking_form_car_name_p').html(window.carRentalTitle);
			$('.booking_form_car_type_p').html(window.carRentalCarType);
			$('.booking_form_pick_up_from_p').html(window.carRentalPickUp);
			$('.booking_form_drop_off_p').html($('#booking_form_drop_off').val());			
			$('.booking_form_date_from_p').html(window.bookingRequest.selectedDateFrom);
			$('.booking_form_date_to_p').html(window.bookingRequest.selectedDateTo);
			$('.booking_form_reservation_total_p').html(car_rentals.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
			$('.booking_form_extra_items_total_p').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));
			$('.booking_form_total_p').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));
		
			$('.three-fourth .lSSlideOuter').hide();
			$('.three-fourth .inner-nav').hide();
			$('.three-fourth .tab-content').hide();
			
			$('#car_rental-booking-form').show();
		},		
		hideCarRentalBookingForm : function () {
		
			$('#car_rental-booking-form').hide();
		},		
		showCarRentalConfirmationForm : function () {
		
			$('#car_rental-confirmation-form').show();
		},
		bindGallery : function() {
		
			$("#gallery").lightSlider({
				item:1,
				rtl: (window.enableRtl ? true : false),
				slideMargin:0,
				auto:true,
				loop:true,
				speed:1500,
				pause:window.pauseBetweenSlides,
				keyPress:true,
				gallery:true,
				thumbItem:8,
				galleryMargin:3,
				onSliderLoad: function() {
					$('#gallery').removeClass('cS-hidden');
				}  
			});
		},
		bindCarRentalDatePicker : function () {
		
			if (typeof $('.car_booking_form_datepicker') !== 'undefined') {

				$('.car_booking_form_datepicker').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: [2, 2],	
					minDate: 0,
					beforeShowDay: function(d) {
					
						var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
						var today = new Date();
						var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
						var selectedTimeFrom = car_rentals.getSelectedTimeFrom();
						var selectedTimeTo = car_rentals.getSelectedTimeTo();
						
						if (window.carRentalBookedOutDays) {
						
							var dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
							var dateTextForCompare2 = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2) + " 00:00:00";
							
							if (selectedTimeFrom && dUtc == selectedTimeFrom)
								return [false, 'ui-datepicker-unselectable ui-state-disabled dp-highlight dp-highlight-selected'];								
							if ($.inArray(dateTextForCompare, window.carRentalBookedOutDays) > -1 || $.inArray(dateTextForCompare2, window.carRentalBookedOutDays) > -1)
								return [false, 'ui-datepicker-unselectable ui-state-disabled'];
						}
						
						if (selectedTimeFrom && ((dUtc == selectedTimeFrom) || (selectedTimeTo && dUtc >= selectedTimeFrom && dUtc <= selectedTimeTo)))
							return [false,  "ui-datepicker-unselectable ui-state-disabled dp-highlight dp-highlight-selected"];
						else if ( todayUtc.valueOf() > dUtc )
							return [false,  "ui-datepicker-unselectable ui-state-disabled"];
						else 
							return [true, "dp-highlight"];
					},
					onSelect: function(dateText, inst) {

						var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay),
							selectedDate = car_rentals.convertLocalToUTC(new Date(selectedTime)),
							selectedDateFrom = car_rentals.getSelectedDateFrom(),
							selectedDateTo = car_rentals.getSelectedDateTo(),
							dateTest = true;
						
						if (!selectedDateFrom || selectedDateTo || (selectedDate < selectedDateFrom) || (selectedDateFrom.toString() === selectedDate.toString())) {
							car_rentals.selectDateFrom(selectedTime, dateText);														
						} else {

							for (var d = selectedDateFrom; d <= selectedDate; d.setDate(d.getDate() + 1)) {
								var dateToCheck = (d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' +  ("0" + d.getDate()).slice(-2));
								var dateToCheck2 = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2) + " 00:00:00";								
								var datesArray = window.carRentalBookedOutDays;
								if ($.inArray(dateToCheck, datesArray) > -1) {
									dateTest = false;
									break;
								}
								if ($.inArray(dateToCheck2, datesArray) > -1) {
									dateTest = false;
									break;
								}								
							}
							
							if (!dateTest) {							
								car_rentals.selectDateFrom(selectedTime, dateText);								
							} else {
							
								$("div.error.step1_error div p").html('');
								$("div.error.step1_error").hide();

								var totalDays = car_rentals.calculateDifferenceInDays(car_rentals.getSelectedDateFrom(), selectedDate);
								
								window.bookingRequest.totalDays = totalDays;
								car_rentals.selectDateTo(selectedTime, dateText);
								car_rentals.buildRatesTable();
								car_rentals.recalculateExtraItemTotals();								
							}
						}
					},
					onChangeMonthYear: function (year, month, inst) {
						
						window.currentMonth = month;
						window.currentYear = year;
						window.currentDay = 1;
						
						car_rentals.populateCarRentalBookedOutDates(window.carRentalId, window.currentMonth, window.currentYear, car_rentals.refreshDatePicker);
					}
				});
			}

		},
		recalculateExtraItemTotals: function() {
		
			if (Object.size(window.bookingRequest.extraItems) > 0) {
			
				if (window.bookingRequest.extraItemsTotalPrice > 0) {
					window.bookingRequest.totalPrice = window.bookingRequest.totalCarRentalOnlyPrice;
					window.bookingRequest.extraItemsTotalPrice = 0;
				}
			
				$.each(window.bookingRequest.extraItems, function( id, extraItem ){

					var extraItemPrice = extraItem.price;
					
					if (extraItem.pricePerPerson) {
						extraItemPrice = (window.bookingRequest.people * extraItemPrice);
					}
					
					if (extraItem.pricePerDay) {
						extraItemPrice = extraItemPrice * window.bookingRequest.totalDays;
					}
					
					extraItem.summedPrice = extraItem.quantity * extraItemPrice;
					
					window.bookingRequest.totalPrice += extraItem.summedPrice;
					window.bookingRequest.extraItemsTotalPrice += extraItem.summedPrice;
				});
				
				$('.extra_items_total').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));		
			}
		},
		buildExtraItemsTable : function() {
		
			$('table.extra_items_price_breakdown thead').html('');
			$('table.extra_items_price_breakdown tfoot').html('');
			$('table.extra_items_price_breakdown tbody').html('');
			
			var headerRow = '';
			headerRow += '<tr class="rates_head_row">';
			headerRow += '<th>' + window.itemLabel + '</th>';		
			headerRow += '<th>' + window.priceLabel + '</th>';
			headerRow += '</tr>';

			$('table.extra_items_price_breakdown thead').append(headerRow);	

			var footerRow = '';
			footerRow += '<tr>';
			footerRow += '<th>' + window.priceTotalLabel + '</th>';
			footerRow += '<td class="extra_items_total">' + car_rentals.formatPrice(0) + '</td>';
			footerRow += '</tr>';

			$('table.extra_items_price_breakdown tfoot').append(footerRow);
		},
		bindExtraItemsQuantitySelect: function() {

			$('select.extra_item_quantity').unbind('change');	
			$('select.extra_item_quantity').on('change', function(e) {

				var quantity = parseInt($(this).val());
				var extraItemId = $(this).attr('id').replace('extra_item_quantity_', '');
				
				car_rentals.updateExtraItemSelection(extraItemId, quantity);
			});		
		},
		updateExtraItemSelection: function(extraItemId, quantity) {

			if (extraItemId > 0) {
				
				var extraItemPrice = parseFloat($('#extra_item_price_' + extraItemId).val());
				var extraItemTitle = $('#extra_item_title_' + extraItemId).html();
				var extraItemPricePerPerson = parseInt($('#extra_item_price_per_person_' + extraItemId).val());
				var extraItemPricePerDay = parseInt($('#extra_item_price_per_day_' + extraItemId).val());
				var oldExtraItem = null;
				var extraItem = {};
				var extraItemRows = '';
				var pricingMethod = '';
				
				// reduce total by old item summed price.
				if (extraItemId in window.bookingRequest.extraItems) {
					oldExtraItem = window.bookingRequest.extraItems[extraItemId];
					window.bookingRequest.totalPrice -= parseFloat(oldExtraItem.summedPrice);	
					window.bookingRequest.extraItemsTotalPrice -= parseFloat(oldExtraItem.summedPrice);
					delete window.bookingRequest.extraItems[extraItemId];
				}
				
				$('table.extra_items_price_breakdown tbody').html('');
				
				if (quantity > 0) {
				
					extraItem.quantity = quantity;
					extraItem.id = extraItemId;
					extraItem.price = extraItemPrice;
					extraItem.pricePerPerson = extraItemPricePerPerson;
					extraItem.pricePerDay = extraItemPricePerDay;
					
					if (extraItem.pricePerPerson) {
						extraItemPrice = (window.bookingRequest.people * extraItemPrice);
					}
					
					if (extraItem.pricePerDay) {
						extraItemPrice = extraItemPrice * window.bookingRequest.totalDays;
					}
					
					extraItem.summedPrice = extraItem.quantity * extraItemPrice;
					extraItem.title = extraItemTitle;
					
					window.bookingRequest.totalPrice += extraItem.summedPrice;
					window.bookingRequest.extraItemsTotalPrice += extraItem.summedPrice;
					window.bookingRequest.extraItems[extraItemId] = extraItem;
				}
				
				if (Object.size(window.bookingRequest.extraItems) > 0) {
					$.each( window.bookingRequest.extraItems, function( index, value ){
					
						if (value.pricePerDay && value.pricePerPerson)
							pricingMethod = window.pricedPerDayPerPersonLabel;
						else if (value.pricePerDay)
							pricingMethod = window.pricedPerDayLabel;
						else if (value.pricePerPerson)
							pricingMethod = window.pricedPerPersonLabel;
							
						extraItemRows += '<tr class="extra_item_row_' + value.Id + '"><td>' + value.quantity + ' x ' + value.title + ' (' + (pricingMethod) + ')</td><td>' + car_rentals.formatPrice(value.summedPrice) + '</td></tr>';
					});
				}
				
				$('table.extra_items_price_breakdown tbody').html(extraItemRows);
				
				$('.extra_items_total').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));		

				$.uniform.update(".extra_item_quantity");
			}			
		},
		populateCarRentalBookedOutDates : function (carRentalId, month, year, callDelegate) {
			
			window.carRentalBookedOutDays = [];

			var dataObj = {
				'action':'car_rental_booked_dates_request',
				'car_rental_id' : carRentalId,
				'month' : month,
				'year' : year,
				'nonce' : BYTAjax.nonce
			};

			$.ajax({
				url: BYTAjax.ajaxurl,
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
		processBooking : function() {
		
			if (typeof(window.bookingRequest) !== 'undefined') {
			
				$('#wait_loading').show();
			
				var selectedDateFrom = new Date(window.bookingRequest.selectedTimeFrom);
				var selectedDateTo = new Date(window.bookingRequest.selectedTimeTo);
				var dateFrom = selectedDateFrom.getFullYear() + "-" + (selectedDateFrom.getMonth() + 1) + "-" + selectedDateFrom.getDate(); 
				var dateTo = selectedDateTo.getFullYear() + "-" + (selectedDateTo.getMonth() + 1) + "-" + selectedDateTo.getDate(); 
				var dropOff = $('#booking_form_drop_off option:selected').val();			
				var dropOffText = $('#booking_form_drop_off option:selected').text();
			
				var cValS = $('#c_val_s_cr').val();
				var cVal1 = $('#c_val_1_cr').val();
				var cVal2 = $('#c_val_2_cr').val();
				
				var dataObj = {
					'action':'car_rental_process_booking_ajax_request',
					'user_id' : window.currentUserId,
					'car_rental_id' : window.carRentalId,
					'extra_items' : window.bookingRequest.extraItems,
					'people' : window.bookingRequest.people,
					'date_from' : dateFrom,
					'date_to' : dateTo,
					'drop_off' : dropOff,
					'c_val_s' : cValS,
					'c_val_1' : cVal1,
					'c_val_2' : cVal2,
					'nonce' : BYTAjax.nonce
				};
				
				$.each(window.bookingFormFields, function(index, field) {
					if (field.hide !== '1') {
						dataObj[field.id] = $('#' + field.id).val();
						$('.confirm_' + field.id + '_p').html($('#' + field.id).val());
					}
				});
				
				$('.confirm_pick_up_p').html(window.carRentalPickUp);
				$('.confirm_drop_off_p').html(dropOffText);
				$('.confirm_car_rental_name_p').html(carRentalTitle);
				$('.confirm_car_rental_type_p').html(carRentalTitle);
				$('.confirm_date_from_p').html(window.bookingRequest.selectedDateFrom);
				$('.confirm_date_to_p').html(window.bookingRequest.selectedDateTo);
				if ($('.confirm_reservation_total_p').length > 0) {
					$('.confirm_reservation_total_p').html(car_rentals.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
				}
				if ($('.confirm_extra_items_total_p').length > 0) {
					$('.confirm_extra_items_total_p').html(car_rentals.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				}
				$('.confirm_total_price_p').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));
							
				$.ajax({
					url: BYTAjax.ajaxurl,
					data: dataObj,
					success:function(data) {
					
						// This outputs the result of the ajax request
						if (data == 'captcha_error') {
						
							$("div.error div p").html(window.invalidCaptchaMessage);
							$("div.error").show();
						} else {
						
							$("div.error div p").html('');
							$("div.error").hide();
							
							car_rentals.hideCarRentalBookingForm();
							car_rentals.showCarRentalConfirmationForm();
						}
						
						$('#wait_loading').hide();
					},
					error: function(errorThrown) {
						console.log(errorThrown);
					}
				}); 
			}
		},
		buildRatesTable: function() {

			var people = window.bookingRequest.people;
			var headerRow = '';
			var footerRow = '';
			var colCount = 2;
			var selectedTimeFrom = car_rentals.getSelectedTimeFrom();
			var selectedTimeTo = car_rentals.getSelectedTimeTo();
			
			$(".price_row").show();
			$.uniform.update(".extra_item_quantity");

			$('table.car_rental_price_breakdown thead').html('');
			$('table.car_rental_price_breakdown tfoot').html('');
			$('table.car_rental_price_breakdown tbody').html('');
			
			headerRow += '<tr class="rates_head_row">';
			headerRow += '<th>' + window.dateLabel + '</th>';		
			headerRow += '<th>' + window.pricePerDayLabel + '</th>';		
			headerRow += '</tr>';

			$('table.car_rental_price_breakdown thead').append(headerRow);	

			footerRow += '<tr>';
			footerRow += '<th colspan="' + (colCount - 1) + '">' + window.priceTotalLabel + '</th>';
			footerRow += '<td class="reservation_total">' + car_rentals.formatPrice(0) + '</td>';
			footerRow += '</tr>';

			$('table.car_rental_price_breakdown tfoot').append(footerRow);
			
			if (selectedTimeFrom && selectedTimeTo) {
			
				$('#datepicker_loading').show();
				
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalCarRentalOnlyPrice = 0;
				window.rateTableRowIndex = 0;
				
				while (selectedTimeFrom < selectedTimeTo) {
					car_rentals.buildRateRow(selectedTimeFrom, people, colCount);
					selectedTimeFrom += 86400000;
				}
				
				$('.reservation_total').html(car_rentals.formatPrice(window.bookingRequest.totalCarRentalOnlyPrice));
				$('.total_price').html(car_rentals.formatPrice(window.bookingRequest.totalPrice));
				
				$('.booking-commands .book-car_rental-next').show();
				
				car_rentals.bindNextButton();
				car_rentals.bindCancelButton();
				
				$('#datepicker_loading').hide();
			}		
		},
		buildRateRow : function(fromTime, people, colCount) {
		
			var fromDate = new Date(fromTime);
			var tableRow = '';
			var pricePerDay = parseFloat(window.carRentalPrice);
			var dateToCheck = (fromDate.getFullYear() + '-' + ("0" + (fromDate.getMonth() + 1)).slice(-2) + '-' +  ("0" + fromDate.getDate()).slice(-2));
						
			var bookedOutDayIndex = $.inArray(dateToCheck, window.carRentalBookedOutDays);
			
			if (bookedOutDayIndex == -1) {			
				
				// This outputs the result of the ajax request
				window.rateTableRowIndex++;
				
				tableRow += '<tr>';
				tableRow += '<td>' + $.datepicker.formatDate(window.datepickerDateFormat, fromDate) + '</td>';

				window.bookingRequest.totalPrice += pricePerDay;
				window.bookingRequest.totalCarRentalOnlyPrice += pricePerDay;
				
				tableRow += '<td>' + car_rentals.formatPrice(pricePerDay) + '</td>';		
				
				tableRow += '</tr>';
				
				$('table.car_rental_price_breakdown tbody').append(tableRow);
				
				if (window.rateTableRowIndex == window.bookingRequest.totalDays) {
					
					if ($("table.car_rental_price_breakdown").data('tablesorter') === null || typeof($("table.car_rental_price_breakdown").data('tablesorter')) == 'undefined') {
						$("table.car_rental_price_breakdown").tablesorter({
							debug:false,
							dateFormat: window.datepickerDateFormat, // 'ddmmyyyy',
							sortList: [[0,0]]
						});
					}
					
					$("table.car_rental_price_breakdown").trigger("update");
					$("table.car_rental_price_breakdown").trigger("sorton", [[[0,0]]]);

					$("table.responsive").trigger('updated');
				}
			}
		
		},
		addProductToCart : function () {
			
			var selectedDateFrom = new Date(car_rentals.getSelectedTimeFrom());
			var selectedDateTo = new Date(car_rentals.getSelectedTimeTo());
			var dateFrom = selectedDateFrom.getFullYear() + "-" + (selectedDateFrom.getMonth() + 1) + "-" + selectedDateFrom.getDate(); 
			var dateTo = selectedDateTo.getFullYear() + "-" + (selectedDateTo.getMonth() + 1) + "-" + selectedDateTo.getDate(); 
			var dropOff = $('#booking_form_drop_off option:selected').val();			

			window.bookingRequest.people = 1;
			
			var dataObj = {
				'action':'car_rental_booking_add_to_cart_ajax_request',
				'user_id' : window.currentUserId,
				'car_rental_id' : window.carRentalId,
				'extra_items' : window.bookingRequest.extraItems,
				'date_from' : dateFrom,
				'date_to' : dateTo,
				'drop_off' : dropOff,
				'nonce' : BYTAjax.nonce
			};
			
			$.each(window.bookingFormFields, function(index, field) {
				if (field.hide !== '1') {
					dataObj[field.id] = $('#' + field.id).val();
				}
			});
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					car_rentals.redirectToCart();
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});	
		},
		selectDateFrom : function(time, dateText) {
		
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCarRentalOnlyPrice = 0;
			window.bookingRequest.totalDays = 0;
			window.bookingRequest.people = 1;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.selectedTimeFrom = time;
			window.bookingRequest.selectedDateFrom = dateText;
			window.bookingRequest.selectedTimeTo = null;
			window.bookingRequest.selectedDateTo = null;

			$('.price_breakdown').hide();

			$("#selected_date_from").val(time);
			$("#selected_date_to").val(null);
			$(".date_from_text").html(dateText);
			$(".date_to_text").html(window.defaultDateToText);
			
			$(".booking-commands").show();
			$(".booking-commands .book-car_rental-reset").show();
			$(".booking-commands .book-car_rental-next").hide();
			
			$('.reservation_total').html(car_rentals.formatPrice(0));
			$('.total_price').html(car_rentals.formatPrice(0));
			$('.extra_items_total').html(car_rentals.formatPrice(0));					
			
			$(".dates_row").show();
			$(".price_row").hide();
		},
		selectDateTo: function(time, dateText) {

			$('.price_breakdown').show();
		
			$('table.car_rental_price_breakdown thead').html('');
			$('table.car_rental_price_breakdown tbody').html('');
			$('table.car_rental_price_breakdown tfoot').html('');

			$(".date_to_text").html(dateText);
			$("#selected_date_to").val(time);
			
			car_rentals.bindRequiredExtraItems();				
			
			window.bookingRequest.selectedTimeTo = time;			
			window.bookingRequest.selectedDateTo = dateText;
		},
		getSelectedDateFrom: function () {
			if ($("#selected_date_from").val()) {
				return car_rentals.convertLocalToUTC(new Date(parseInt($("#selected_date_from").val())));
			}
			return null;			
		},
		getSelectedDateTo: function () {
			if ($("#selected_date_to").val()) {
				return car_rentals.convertLocalToUTC(new Date(parseInt($("#selected_date_to").val())));
			}
			return null;
		},
		getSelectedTimeFrom: function () {
			if ($("#selected_date_from").val()) {
				return parseInt($("#selected_date_from").val());
			}
			return null;
		},
		getSelectedTimeTo: function () {
			if ($("#selected_date_to").val()) {
				return parseInt($("#selected_date_to").val());
			}
			return null;
		},
		refreshDatePicker : function() {
		
			if (typeof $('.car_booking_form_datepicker') !== 'undefined') {
				$('.car_booking_form_datepicker').datepicker( "refresh" );
			}
			$('#wait_loading').hide();	
		},
		redirectToCart : function() {		
			top.location.href = window.wooCartPageUri;
		},
		calculateDifferenceInDays : function( date1, date2) {
			return (Date.UTC(date2.getYear(), date2.getMonth(), date2.getDate()) - Date.UTC(date1.getYear(), date1.getMonth(), date1.getDate())) / 86400000;
		},
		daysInMonth : function(month, year) {
			return new Date(year, month, 0).getDate();
		},
		convertLocalToUTC : function (date) { 
			return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
		},
		formatPrice: function( price ) {
			if (window.currencySymbolShowAfter)
				return price.toFixed(window.priceDecimalPlaces) + ' ' + window.currencySymbol;
			else
				return window.currencySymbol + ' ' + price.toFixed(window.priceDecimalPlaces);
		},
	};

})(jQuery);