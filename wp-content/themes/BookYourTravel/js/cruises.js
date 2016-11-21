(function($) {

	$(document).ready(function () {
		cruises.init();
	});
	
	var cruises = {

		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.extraItems = {};
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalCruiseOnlyPrice = 0;
			window.bookingRequest.totalDays = 1;
			
			$('.extra_items_total').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
			$('.total_price').html(cruises.formatPrice(window.bookingRequest.totalPrice));		
			$('.reservation_total').html(cruises.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
				
			if (window.cruiseIsReservationOnly || !window.useWoocommerceForCheckout) {

				$('.cruise-booking-form').validate({
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
						cruises.processCruiseBooking(); 
					}
				});
				
				$.each(window.bookingFormFields, function(index, field) {
				
					if (field.hide !== '1' && field.id !== null && field.id.length > 0) {
						var $input = null;
						if (field.type == 'text' || field.type == 'email') {
							$input = $('.cruise-booking-form').find('input[name=' + field.id + ']');
						} else if (field.type == 'textarea') {
							$input = $('.cruise-booking-form').find('textarea[name=' + field.id + ']');
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
			
			cruises.bindGallery();	
			cruises.bindSelectDatesButton();
			cruises.bindCancelButton();
			
			$('.radio').bind('click.uniform',
				function (e) {
					if ($(this).find("span").hasClass('checked')) 
						$(this).find("input").attr('checked', true);
					else
						$(this).find("input").attr('checked', false);
				}
			);
		},
		bindRequiredExtraItems: function() {
			if (typeof(window.requiredExtraItems) !== 'undefined' && window.requiredExtraItems.length > 0) {
				$.each( window.requiredExtraItems, function( index, extraItemId ){
					cruises.updateExtraItemSelection(extraItemId, 1);
					$('#extra_item_quantity_' + extraItemId).val('1');					
				});
			}			
		},
		bindCancelButton : function() {
			$('#cancel-cruise-booking').on('click', function(event) {
				cruises.hideCruiseBookingForm();
				cruises.showCruiseScreen();
				event.preventDefault();
			});	
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
		bindSelectDatesButton : function () {
		
			$('.book-cruise-select-dates').unbind('click');
			$('.book-cruise-select-dates').on('click', function(event) {
				
				event.preventDefault();
				$('.book-cruise-select-dates').show();
				$(this).hide();
				
				$('#wait_loading').show();
				
				var prevCabinTypeId = window.cabinTypeId;
				
				$("#start_date_span").html("");
				$("#start_date").val("");
				$(".dates_row").hide();
				$(".price_row").hide();
				$('.booking-commands').hide();
				
				var buttonId = $(this).attr('id');
				window.cabinTypeId = buttonId.replace('book-cruise-', '');
				window.cabinTypeTitle = $('#cabin_type_' + window.cabinTypeId + ' .cabin_type h3').html();
				$('.cabin_type_span').html(window.cabinTypeTitle);
			
				if (prevCabinTypeId > 0) {
					$('.cruise_schedule_datepicker').datepicker('destroy');
					$("#cabin_type_" + prevCabinTypeId + " .booking_form_controls").html('');
					$("#cabin_type_" + prevCabinTypeId + " .booking_form_controls").show();
				}

				$("#cabin_type_" + window.cabinTypeId + " .booking_form_controls").html($(".booking_form_controls_holder").html());
				$("#cabin_type_" + window.cabinTypeId + " .booking_form_controls").show();
				
				$("#cabin_type_" + window.cabinTypeId + " .booking_form_controls .datepicker_holder").addClass('cruise_schedule_datepicker');
					
				cruises.populateCruiseScheduleEntries(window.cruiseId, window.cabinTypeId, window.currentDay, window.currentMonth, window.currentYear, cruises.bindCruiseDatePicker);
					
				cruises.bindNextButton();
				cruises.bindResetButton();
				
				if (window.enableExtraItems) {
					cruises.bindExtraItemsQuantitySelect();
					cruises.buildExtraItemsTable();
					cruises.bindRequiredExtraItems();					
				}
			
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
			});
		},
		bindResetButton : function() {
		
			$('.book-cruise-reset').unbind('click');
			$('.book-cruise-reset').on('click', function(event) {

				event.preventDefault();
				$('.book-cruise-select-dates').show();
				
				$("#cabin_type_" + window.cabinTypeId + " .booking_form_controls").html('');
				$("#cabin_type_" + window.cabinTypeId + " .booking_form_controls").show();				
				
				window.cabinTypeId = 0;
				$("#start_date_span").html("");
				$("#start_date").val("");
				$(".dates_row").hide();
				$(".price_row").hide();
				$('.booking-commands').hide();
				$(".extra_item_quantity").val("0");		
				$("#booking_form_adults").val("1");
				$("#booking_form_children").val("0");
				$("span.adults_text").html("1");
				$("span.children_text").html("0" + (window.cruiseCountChildrenStayFree > 0 ? " *" : ""));				
				
				$('table.cruise_price_breakdown thead').html('');
				$('table.cruise_price_breakdown tbody').html('');
				$('table.cruise_price_breakdown tfoot').html('');
				
				window.bookingRequest = {};
				window.bookingRequest.extraItems = {};
				window.bookingRequest.adults = 1;
				window.bookingRequest.children = 0;
				window.bookingRequest.extraItemsTotalPrice = 0;
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalCruiseOnlyPrice = 0;
				window.bookingRequest.totalDays = 1;
				
				$('.reservation_total').html(cruises.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
				$('.total_price').html(cruises.formatPrice(window.bookingRequest.totalPrice));
				$('.confirm_total_price_p').html(cruises.formatPrice(window.bookingRequest.totalPrice));

				$('.extra_items_total').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));						
				$('.extra_items_total').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));
				$('table.extra_items_price_breakdown tbody').html('');
				cruises.bindRequiredExtraItems();						
				
				cruises.populateCruiseScheduleEntries(window.cruiseId, window.cabinTypeId, window.currentDay, window.currentMonth, window.currentYear, cruises.refreshDatePicker);
			});
		},
		bindNextButton : function () {

			$('.book-cruise-next').unbind('click');
			$('.book-cruise-next').on('click', function(event) {

				$('#wait_loading').show();
			
				if (!window.cruiseIsReservationOnly && window.useWoocommerceForCheckout) {
					cruises.addProductToCart();
				} else {
					
					cruises.showCruiseBookingForm();
				}

				$('body,html').animate({
					scrollTop: 0
				}, 800);

				$('#wait_loading').hide();
				
				event.preventDefault();
			});
			
		},
		showCruiseScreen : function () {
			$('.three-fourth .lSSlideOuter').show();
			$('.three-fourth .inner-nav').show();
			$('.three-fourth .tab-content').show();
			$(".tab-content").hide();
			$(".tab-content:first").show();
			$(".inner-nav li:first").addClass("active");
		},
		showCruiseBookingForm : function () {
		
			$('.booking_form_adults_p').html(window.bookingRequest.adults);
			$('.booking_form_children_p').html(window.bookingRequest.children);
			$('.booking_form_cruise_date_p').html($("#start_date_span").html());
			$('.booking_form_cruise_name_p').html(window.cruiseTitle);
			$('.booking_form_reservation_total_p').html(cruises.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
			$('.booking_form_extra_items_total_p').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));
			$('.booking_form_total_p').html(cruises.formatPrice(window.bookingRequest.totalPrice));
		
			$('#cruise-booking-form').show();
			$('.three-fourth .lSSlideOuter').hide();
			$('.three-fourth .inner-nav').hide();
			$('.three-fourth .tab-content').hide();			
		},
		hideCruiseBookingForm : function () {
			$('#cruise-booking-form').hide();
		},
		showCruiseConfirmationForm : function () {
			$('#cruise-confirmation-form').show();
		},
		selectStartDate: function(time, dateText) {
		
			$(".price_row").show();
			$('.dates_row').show();
			$('.booking-commands').show();

			$('table.cruise_price_breakdown thead').html('');
			$('table.cruise_price_breakdown tbody').html('');
			$('table.cruise_price_breakdown tfoot').html('');

			$("#start_date").val(time);
			$("#start_date_span").html(dateText);
			
			window.bookingRequest.selectedTime = time;			
			window.bookingRequest.selectedDate = dateText;
			
			var cruiseStartDate = cruises.convertLocalToUTC(new Date(parseInt($("#start_date").val())));
			cruiseStartDate = cruiseStartDate.getFullYear() + "-" + (cruiseStartDate.getMonth() + 1) + "-" + cruiseStartDate.getDate(); 
			window.bookingRequest.scheduleId = cruises.getCruiseScheduleId(window.cruiseId, window.cabinTypeId, cruiseStartDate);
			window.bookingRequest.maxAdultCount = parseInt($('#cabin_type_' + window.cabinTypeId + ' .max_count').val());
			window.bookingRequest.maxAdultCount = window.bookingRequest.maxAdultCount > 0 ? window.bookingRequest.maxAdultCount : 1;
			window.bookingRequest.maxChildCount = parseInt($('#cabin_type_' + window.cabinTypeId + ' .max_child_count').val());
			window.bookingRequest.maxChildCount = window.bookingRequest.maxChildCount > 0 ? window.bookingRequest.maxChildCount : 1;
			window.bookingRequest.totalDays = cruises.getCruiseScheduleDurationDays(window.bookingRequest.scheduleId);
			
			$("#duration_days").val(window.bookingRequest.totalDays);
			$("#duration_days_span").html(window.bookingRequest.totalDays);
			
			cruises.bindCruiseDropDowns();
			
		},
		getSelectedStartTime: function () {
			if ($("#start_date").val()) {
				return parseInt($("#start_date").val());
			}
			return null;
		},
		getSelectedStartDate: function () {
			if ($("#start_date").val()) {
				return cruises.convertLocalToUTC(new Date(parseInt($("#start_date").val())));
			}
			return null;			
		},
		addProductToCart : function () {
			
			var cruiseDate = cruises.getSelectedStartDate();
			cruiseDate = cruiseDate.getFullYear() + "-" + (cruiseDate.getMonth() + 1) + "-" + cruiseDate.getDate(); 
			var cruiseScheduleId = cruises.getCruiseScheduleId(window.cruiseId, window.cabinTypeId, cruiseDate);
			window.bookingRequest.adults = $("#booking_form_adults").val();
			window.bookingRequest.children = $("#booking_form_children").val();
			
			var dataObj = {
				'action':'cruise_booking_add_to_cart_ajax_request',
				'user_id' : window.currentUserId,
				'cruise_id' : window.cruiseId,
				'cabin_type_id' : window.cabinTypeId,
				'cruise_schedule_id' : cruiseScheduleId,
				'cruise_date' : cruiseDate,
				'extra_items' : window.bookingRequest.extraItems,
				'adults' : window.bookingRequest.adults,
				'children' : window.bookingRequest.children,
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
					cruises.redirectToCart();
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});	
		},
		redirectToCart : function() {		
			top.location.href = window.wooCartPageUri;
		},
		bindCruiseRatesTable : function () {
			
			$(".price_row").show();
			$(".command-bittons").show();

			$('table.cruise_price_breakdown thead').html('');
			$('table.cruise_price_breakdown tfoot').html('');
			$('table.cruise_price_breakdown tbody').html('');

			var adults = $('#booking_form_adults').val();
			if (!adults) {
				adults = 1;
			}
				
			var children = $('#booking_form_children').val();
			if (!children) {
				children = 0;
			}
			
			var selectedStartDate = cruises.getSelectedStartDate();
			var selectedStartTime = selectedStartDate.valueOf();	
				
			var colCount = 2;
			var headerRow = '<tr class="rates_head_row">';
			
			headerRow += '<th>' + window.dateLabel + '</th>';		
			
			if (window.cruiseIsPricePerPerson) {
				headerRow += '<th>' + window.adultCountLabel + '</th>';
				headerRow += '<th>' + window.pricePerAdultLabel + '</th>';
				headerRow += '<th>' + window.childCountLabel + '</th>';
				headerRow += '<th>' + window.pricePerChildLabel + '</th>';
				colCount = 6;
			}
			
			headerRow += '<th>' + window.pricePerDayLabel + '</th>';		
			
			headerRow += '</tr>';

			$('table.cruise_price_breakdown thead').append(headerRow);	
			
			var footerRow = '<tr>';
			footerRow += '<th colspan="' + (colCount - 1) + '">' + window.priceTotalLabel + '</th>';
			footerRow += '<td class="reservation_total">0</td>';
			footerRow += '</tr>';

			$('table.cruise_price_breakdown tfoot').append(footerRow);
			
			if (selectedStartTime) {
			
				$('#datepicker_loading').show();			
				cruises.buildCruiseRateRow(selectedStartTime, adults, children);
			}
			
		},
		buildCruiseRateRow : function (startTime, adults, children) {
			
			var d = new Date(startTime);
			var day = d.getDate();
			var month = d.getMonth() + 1;
			var year = d.getFullYear();
			var dateValue = day + "-" + month + "-" + year; 

			var dataObj = {
				'action':'cruise_get_price_request',
				'cruiseId' : window.cruiseId,
				'cabinTypeId' : window.cabinTypeId,
				'dateValue' : dateValue,
				'nonce' : BYTAjax.nonce
			};	

			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				dataType: 'json',
				success:function(prices) {
					var tableRow = '';
					// This outputs the result of the ajax request
					window.rateTableRowIndex++;
					var pricePerCruise = parseFloat(prices.price);
					var pricePerChild = 0;
					var totalPrice = 0;
					
					tableRow += '<tr>';
					tableRow += '<td>' + dateValue + '</td>';
					
					if (window.cruiseIsPricePerPerson) {
						pricePerChild = parseFloat(prices.child_price);
						tableRow += '<td>' + adults + '</td>';
						tableRow += '<td>' + cruises.formatPrice( pricePerCruise ) + '</td>';
						tableRow += '<td>' + children + '</td>';
						tableRow += '<td>' + cruises.formatPrice( pricePerChild ) + '</td>';
						children = children - window.cruiseCountChildrenStayFree;
						children = children > 0 ? children : 0;
						totalPrice = (pricePerCruise * adults) + (pricePerChild * children);
					} else {
						totalPrice = pricePerCruise;
					}					
					
					tableRow += '<td>' + cruises.formatPrice(totalPrice) + '</td>';
					
					window.bookingRequest.totalCruiseOnlyPrice = totalPrice;
					window.bookingRequest.totalPrice = totalPrice + window.bookingRequest.extraItemsTotalPrice;
					
					tableRow += '</tr>';
					
					$('table.cruise_price_breakdown tbody').append(tableRow);

					$('.total_price').html(cruises.formatPrice(window.bookingRequest.totalPrice));
					$("#confirm_cruise_total").html(cruises.formatPrice(window.bookingRequest.totalPrice));
					$('.reservation_total').html(cruises.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
					
					$("table.responsive").trigger('updated');
					$('#datepicker_loading').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});

		},
		bindCruiseDropDowns : function() {

			$('#booking_form_adults').unbind();			
			$('#booking_form_adults').find('option').remove();
			for ( var i = 1; i <= window.bookingRequest.maxAdultCount; i++ ) {
				$('<option ' + (i == window.bookingRequest.adults ? 'selected' : '') + '>').val(i).text(i).appendTo('#booking_form_adults');
			}
			
			$('#booking_form_adults').change(function (e) {
				window.bookingRequest.adults = parseInt($(this).val());
				$('span.adults_text').html(window.bookingRequest.adults);

				cruises.bindCruiseDropDowns();
				cruises.bindCruiseRatesTable();
				cruises.recalculateExtraItemTotals();
			});
			
			$('#booking_form_children').unbind();
			$('#booking_form_children').find('option').remove();
			$('<option selected>').val(0).text(0).appendTo('#booking_form_children');
			for ( var j = 1; j <= window.bookingRequest.maxChildCount; j++ ) {
				$('<option ' + (j == window.bookingRequest.children ? 'selected' : '') + '>').val(j).text(j).appendTo('#booking_form_children');
			}
			
			$('#booking_form_children').change(function (e) {
				window.bookingRequest.children = parseInt($(this).val());
				$('span.children_text').html(window.bookingRequest.children);
				cruises.bindCruiseDropDowns();
				cruises.bindCruiseRatesTable();
				cruises.recalculateExtraItemTotals();
			});
			
			$('#booking_form_adults').uniform();
			$('#booking_form_children').uniform();
			$('.extra_item_quantity').uniform();
		},
		bindCruiseDatePicker : function  () {	
		
			if (typeof $('.cruise_schedule_datepicker') !== 'undefined') {

				$('.cruise_schedule_datepicker').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: [2, 2],	
					minDate: 0,
					beforeShowDay: function(d) {

						var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
						
						var selectedTime = null;
					
						if ($("#start_date").val()) {
							selectedTime = parseInt($("#start_date").val());
						}

						if (window.cruiseScheduleEntries) {
						
							var dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
							var dateTextForCompare2 = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2) + ' 00:00:00';
						
							if (dUtc == selectedTime)
								return [false, 'dp-hightlight dp-highlight-selected'];
							if ($.inArray(dateTextForCompare, window.cruiseScheduleEntries) == -1 && $.inArray(dateTextForCompare2, window.cruiseScheduleEntries) == -1)
								return [false, 'ui-datepicker-unselectable ui-state-disabled'];
						}
						
						return [true, "dp-highlight"];
					},
					onSelect: function(dateText, inst) {

						var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);					
					
						cruises.selectStartDate(selectedTime, dateText);	
						cruises.bindCruiseRatesTable();						
						cruises.recalculateExtraItemTotals();
					},
					onChangeMonthYear: function (year, month, inst) {
					
						window.currentMonth = month;
						window.currentYear = year;
						window.currentDay = 1;
						cruises.populateCruiseScheduleEntries(window.cruiseId, window.cabinTypeId, window.currentDay, window.currentMonth, window.currentYear,cruises.refreshDatePicker);
					}
				});
			}

		},
		recalculateExtraItemTotals: function() {
		
			if (Object.size(window.bookingRequest.extraItems) > 0) {
			
				if (window.bookingRequest.extraItemsTotalPrice > 0) {
					window.bookingRequest.totalPrice = window.bookingRequest.totalCruiseOnlyPrice;
					window.bookingRequest.extraItemsTotalPrice = 0;
				}
			
				$.each(window.bookingRequest.extraItems, function( id, extraItem ){

					var extraItemPrice = extraItem.price;
					
					if (extraItem.pricePerPerson) {
						var adjustedChildren = window.bookingRequest.children;
						adjustedChildren = adjustedChildren > 0 ? adjustedChildren : 0;
						extraItemPrice = (window.bookingRequest.adults * extraItemPrice) + (adjustedChildren * extraItemPrice);
					}
					
					if (extraItem.pricePerDay) {
						extraItemPrice = extraItemPrice * window.bookingRequest.totalDays;
					}
					
					extraItem.summedPrice = extraItem.quantity * extraItemPrice;
					
					window.bookingRequest.totalPrice += extraItem.summedPrice;
					window.bookingRequest.extraItemsTotalPrice += extraItem.summedPrice;
				});
				
				$('.extra_items_total').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(cruises.formatPrice(window.bookingRequest.totalPrice));		
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
			footerRow += '<td class="extra_items_total">' + cruises.formatPrice(0) + '</td>';
			footerRow += '</tr>';

			$('table.extra_items_price_breakdown tfoot').append(footerRow);
		},
		bindExtraItemsQuantitySelect: function() {

			$('select.extra_item_quantity').unbind('change');	
			$('select.extra_item_quantity').on('change', function(e) {

				var quantity = parseInt($(this).val());
				var extraItemId = $(this).attr('id').replace('extra_item_quantity_', '');
				
				cruises.updateExtraItemSelection(extraItemId, quantity);
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
						var adjustedChildren = window.bookingRequest.children;
						adjustedChildren = adjustedChildren > 0 ? adjustedChildren : 0;
						extraItemPrice = (window.bookingRequest.adults * extraItemPrice) + (adjustedChildren * extraItemPrice);
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
							
						extraItemRows += '<tr class="extra_item_row_' + value.Id + '"><td>' + value.quantity + ' x ' + value.title + ' (' + (pricingMethod) + ')</td><td>' + cruises.formatPrice(value.summedPrice) + '</td></tr>';
					});
				}
				
				$('table.extra_items_price_breakdown tbody').html(extraItemRows);
				
				$('.extra_items_total').html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(cruises.formatPrice(window.bookingRequest.totalPrice));		

				$.uniform.update(".extra_item_quantity");
			}				
		},
		refreshDatePicker : function() {
		
			if (typeof $('.cruise_schedule_datepicker') !== 'undefined') {
				$('.cruise_schedule_datepicker').datepicker( "refresh" );
			}
			$('#wait_loading').hide();	
		},
		getCruiseIsReservationOnly : function (cruiseId) {
			var isReservationOnly = 0;

			var dataObj = {
				'action':'cruise_is_reservation_only_request',
				'cruise_id' : cruiseId,
				'nonce' : BYTAjax.nonce
			};

			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					isReservationOnly = parseInt(data);
				},
				error: function(errorThrown) {

				}
			});

			return isReservationOnly;
		},
		getCruiseScheduleDurationDays : function (scheduleId) {
			
			var days = 0;
			var dataObj = {
				'action':'cruise_get_schedule_duration_days_request',
				'schedule_id' : scheduleId,
				'nonce' : BYTAjax.nonce
			};
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					days = parseInt(data);
				},
				error: function(errorThrown) {
				}
			});
			
			return days;
		},
		processCruiseBooking : function () {

			$('#wait_loading').show();
			
			var cruiseDate = cruises.convertLocalToUTC(new Date(parseInt($("#start_date").val())));
			cruiseDate = cruiseDate.getFullYear() + "-" + (cruiseDate.getMonth() + 1) + "-" + cruiseDate.getDate(); 
			var cruiseStartDateText = $("#start_date_span").html();
			var cruiseScheduleId = cruises.getCruiseScheduleId(window.cruiseId, window.cabinTypeId, cruiseDate);
			
			var adults = $("#booking_form_adults").val();
			var children = $("#booking_form_children").val();
			
			var cValS = $('#c_val_s_cru').val();
			var cVal1 = $('#c_val_1_cru').val();
			var cVal2 = $('#c_val_2_cru').val();

			var dataObj = {
				'action':'cruise_process_booking_ajax_request',
				'user_id' : window.currentUserId,
				'cruise_schedule_id' : cruiseScheduleId,
				'cruise_date' : cruiseDate,
				'extra_items' : window.bookingRequest.extraItems,
				'adults' : window.bookingRequest.adults,
				'children' : window.bookingRequest.children,
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
			
			$(".confirm_cruise_date_p").html(cruiseStartDateText);
			$(".confirm_cruise_title_p").html(window.cruiseTitle);
			$(".confirm_adults_p").html(adults);
			$(".confirm_children_p").html(children);
			$(".confirm_reservation_total_p").html(cruises.formatPrice(window.bookingRequest.totalCruiseOnlyPrice));
			$(".confirm_extra_items_total_p").html(cruises.formatPrice(window.bookingRequest.extraItemsTotalPrice));
			$(".confirm_total_price_p").html(cruises.formatPrice(window.bookingRequest.totalPrice));
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					// This outputs the result of the ajax request
					if (data == 'captcha_error') {
						$("div.error div p").html(window.InvalidCaptchaMessage);
						$("div.error").show();
					} else {
						$("div.error div p").html('');
						$("div.error").hide();
						
						cruises.hideCruiseBookingForm();
						cruises.showCruiseConfirmationForm();
					}
					
					$('#wait_loading').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		},
		getCruiseScheduleId : function (cruiseId, cabinTypeId, date) {

			var scheduleId = 0;

			var dataObj = {
				'action':'cruise_available_schedule_id_request',
				'cruiseId' : cruiseId,
				'cabinTypeId' : cabinTypeId,
				'dateValue' : date,
				'nonce' : BYTAjax.nonce
			};	

			$.ajax({
				url: BYTAjax.ajaxurl,
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
		populateCruiseScheduleEntries : function (cruiseId, cabinTypeId, day, month, year, callDelegate) {

			var dataObj = {
				'action':'cruise_schedule_dates_request',
				'cruiseId' : cruiseId,
				'cabinTypeId' : cabinTypeId,
				'month' : month,
				'year' : year,
				'day' : day,
				'nonce' : BYTAjax.nonce
			};

			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				async: true,
				success:function(json) {
					// This outputs the result of the ajax request
					var scheduleDates = JSON.parse(json);
					var dateArray = [];
					var i = 0;
					for (i = 0; i < scheduleDates.length; ++i) {
						if (scheduleDates[i].cruise_date !== null) {
							dateArray.push(scheduleDates[i].cruise_date);
						}
					}
					
					window.cruiseScheduleEntries = dateArray;
					
					if (typeof (callDelegate) !== 'undefined') {
						callDelegate();
					}
					
					$('#wait_loading').hide();
				},
				error: function(errorThrown) {

				}
			});
		},
		formatPrice: function( price ) {
			if (window.currencySymbolShowAfter)
				return price.toFixed(window.priceDecimalPlaces) + ' ' + window.currencySymbol;
			else
				return window.currencySymbol + ' ' + price.toFixed(window.priceDecimalPlaces);
		},
		convertLocalToUTC : function (date) { 
			return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
		}
	};

})(jQuery);	