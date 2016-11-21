(function($) {
	$(document).ready(function () {
		tours.init();
	});
	
	var tours = {
	
		init: function () {
		
			window.bookingRequest = {};
			window.bookingRequest.extraItems = {};
			window.bookingRequest.adults = 1;
			window.bookingRequest.children = 0;
			window.bookingRequest.extraItemsTotalPrice = 0;
			window.bookingRequest.totalPrice = 0;
			window.bookingRequest.totalTourOnlyPrice = 0;
			window.bookingRequest.totalDays = 1;
			
			$('.extra_items_total').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
			$('.total_price').html(tours.formatPrice(window.bookingRequest.totalPrice));		
			$('.reservation_total').html(tours.formatPrice(window.bookingRequest.totalTourOnlyPrice));
			
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
			
			if (window.tourIsReservationOnly || !window.useWoocommerceForCheckout) {
				
				$('.tour-booking-form').validate({
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
						tours.processTourBooking(); 
					}
				});

				$.each(window.bookingFormFields, function(index, field) {
				
					if (field.hide !== '1' && field.id !== null && field.id.length > 0) {
						var $input = null;
						if (field.type == 'text' || field.type == 'email') {
							$input = $('.tour-booking-form').find('input[name=' + field.id + ']');
						} else if (field.type == 'textarea') {
							$input = $('.tour-booking-form').find('textarea[name=' + field.id + ']');
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

			$('#booking_form_adults').uniform();
			$('#booking_form_children').uniform();
			$('.extra_item_quantity').uniform();

			$('.radio').bind('click.uniform',
				function (e) {
					if ($(this).find("span").hasClass('checked')) 
						$(this).find("input").attr('checked', true);
					else
						$(this).find("input").attr('checked', false);
				}
			);

			tours.bindGallery();	
			tours.bindResetButton();
			tours.bindNextButton();
			tours.bindCancelButton();
			
			if (window.enableExtraItems) {
				tours.bindExtraItemsQuantitySelect();
				tours.buildExtraItemsTable();
				tours.bindRequiredExtraItems();				
			}
			
			tours.populateTourScheduleEntries(window.tourId, window.currentDay, window.currentMonth, window.currentYear, tours.bindTourDatePicker);
		},	
		bindRequiredExtraItems: function() {
			if (typeof(window.requiredExtraItems) !== 'undefined' && window.requiredExtraItems.length > 0) {
				$.each( window.requiredExtraItems, function( index, extraItemId ){
					tours.updateExtraItemSelection(extraItemId, 1);
					$('#extra_item_quantity_' + extraItemId).val('1');
				});
			}			
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
		bindNextButton : function() {
		
			$('.book-tour-proceed').unbind('click');
			$('.book-tour-proceed').on('click', function(event) {
			
				if (!window.tourIsReservationOnly && window.useWoocommerceForCheckout) {
				
					tours.addProductToCart();
				
				} else {
			
					$('#wait_loading').show();
					
					tours.showTourBookingForm();
					
					$('#wait_loading').hide();
					
					$('body,html').animate({
						scrollTop: 0
					}, 800);
				
				}
				
				event.preventDefault();
			});
		},
		bindResetButton : function() {
		
			$('.book-tour-reset').unbind('click');
			$('.book-tour-reset').on('click', function(event) {
				
				window.bookingRequest = {};
				window.bookingRequest.extraItems = {};
				window.bookingRequest.adults = 1;
				window.bookingRequest.children = 0;
				window.bookingRequest.extraItemsTotalPrice = 0;
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalTourOnlyPrice = 0;
				window.bookingRequest.totalDays = 1;
				
				event.preventDefault();
				
				$(".extra_item_quantity").val("0");		
				$("#booking_form_adults").val("1");
				$("#booking_form_children").val("0");
				$("span.adults_text").html("1");
				$("span.children_text").html("0");
				$("#start_date_span").html('');
				$("#start_date").val('');
				$(".dates_row").hide();
				$(".price_row").hide();
				$('.booking-commands').hide();
				$('table.tour_price_breakdown thead').html('');
				$('table.tour_price_breakdown tbody').html('');
				$('table.tour_price_breakdown tfoot').html('');
				
				$('.reservation_total').html(tours.formatPrice(window.bookingRequest.totalTourOnlyPrice));

				$('.total_price').html(tours.formatPrice(window.bookingRequest.totalPrice));
				$('.confirm_total_price_p').html(tours.formatPrice(window.bookingRequest.totalPrice));

				$('.extra_items_total').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));						
				$('.extra_items_total').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));
				$('table.extra_items_price_breakdown tbody').html('');				
				
				tours.bindRequiredExtraItems();				
				
				tours.populateTourScheduleEntries(window.tourId, window.currentDay, window.currentMonth, window.currentYear, tours.refreshDatePicker);				
			});
		},
		bindCancelButton: function() {
			$('#cancel-tour-booking').unbind('click');
			$('#cancel-tour-booking').on('click', function(event) {
				tours.hideTourBookingForm();
				tours.showTourScreen();
				event.preventDefault();
			});	
		},
		showTourScreen : function () {
		
			$('.three-fourth .lSSlideOuter').show();
			$('.three-fourth .inner-nav').show();
			$('.three-fourth .tab-content').show();
			$(".tab-content").hide();
			$(".tab-content:first").show();
			$(".inner-nav li:first").addClass("active");
		},
		showTourBookingForm : function () {
		
			$('.booking_form_adults_p').html(window.bookingRequest.adults);
			$('.booking_form_children_p').html(window.bookingRequest.children);
			$('.booking_form_tour_date_p').html($("#start_date_span").html());
			$('.booking_form_tour_name_p').html(window.tourTitle);
			$('.booking_form_reservation_total_p').html(tours.formatPrice(window.bookingRequest.totalTourOnlyPrice));
			$('.booking_form_extra_items_total_p').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));
			$('.booking_form_total_p').html(tours.formatPrice(window.bookingRequest.totalPrice));
		
			$('.three-fourth .lSSlideOuter').hide();
			$('.three-fourth .inner-nav').hide();
			$('.three-fourth .tab-content').hide();
			
			$('#tour-booking-form').show();
		},		
		hideTourBookingForm : function () {
		
			$('#tour-booking-form').hide();
		},		
		showTourConfirmationForm : function () {
		
			$('#tour-confirmation-form').show();
		},		
		selectStartDate: function(time, dateText) {
		
			$(".price_row").show();
			$('.dates_row').show();
			$('.booking-commands').show();

			$('table.tour_price_breakdown thead').html('');
			$('table.tour_price_breakdown tbody').html('');
			$('table.tour_price_breakdown tfoot').html('');

			$("#start_date").val(time);
			$("#start_date_span").html(dateText);
			
			window.bookingRequest.selectedTime = time;			
			window.bookingRequest.selectedDate = dateText;
			
			var tourStartDate = tours.convertLocalToUTC(new Date(parseInt($("#start_date").val())));
			tourStartDate = tourStartDate.getFullYear() + "-" + (tourStartDate.getMonth() + 1) + "-" + tourStartDate.getDate(); 
			window.bookingRequest.scheduleId = tours.getTourScheduleId(window.tourId, tourStartDate);
			window.bookingRequest.maxCount = tours.getMaxPeople(window.bookingRequest.scheduleId);
			window.bookingRequest.maxCount = window.bookingRequest.maxCount > 0 ? window.bookingRequest.maxCount : 1;
			window.bookingRequest.totalDays = tours.getTourScheduleDurationDays(window.bookingRequest.scheduleId);
			
			$("#duration_days").val(window.bookingRequest.totalDays);
			$("#duration_days_span").html(window.bookingRequest.totalDays);
			
			tours.bindTourDropDowns();
		},
		getSelectedStartTime: function () {
			if ($("#start_date").val()) {
				return parseInt($("#start_date").val());
			}
			return null;
		},
		getSelectedStartDate: function () {
			if ($("#start_date").val()) {
				return tours.convertLocalToUTC(new Date(parseInt($("#start_date").val())));
			}
			return null;			
		},
		addProductToCart : function () {
			
			var tourDate = tours.getSelectedStartDate();
			tourDate = tourDate.getFullYear() + "-" + (tourDate.getMonth() + 1) + "-" + tourDate.getDate(); 
			var tourScheduleId = tours.getTourScheduleId(window.tourId, tourDate);
			window.bookingRequest.adults = $("#booking_form_adults").val();
			window.bookingRequest.children = $("#booking_form_children").val();
			
			var dataObj = {
				'action':'tour_booking_add_to_cart_ajax_request',
				'user_id' : window.currentUserId,
				'tour_id' : window.tourId,
				'tour_schedule_id' : tourScheduleId,
				'tour_date' : tourDate,
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
					tours.redirectToCart();
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});	
		},
		redirectToCart : function() {		
			top.location.href = window.wooCartPageUri;
		},
		bindTourRatesTable : function () {
			
			$(".price_row").show();
			
			$('table.tour_price_breakdown thead').html('');
			$('table.tour_price_breakdown tfoot').html('');
			$('table.tour_price_breakdown tbody').html('');
			
			var adults = $('#booking_form_adults').val();
			if (!adults)
				adults = 1;
				
			var children = $('#booking_form_children').val();
			if (!children)
				children = 0;
				
			var selectedStartDate = tours.getSelectedStartDate();
			var selectedStartTime = selectedStartDate.valueOf();				
			
			var colCount = 2;
			var headerRow = '<tr class="rates_head_row">';
			
			headerRow += '<th>' + window.dateLabel + '</th>';		
			
			if (!window.tourIsPricePerGroup) {
				headerRow += '<th>' + window.adultCountLabel + '</th>';
				headerRow += '<th>' + window.pricePerAdultLabel + '</th>';
				headerRow += '<th>' + window.childCountLabel + '</th>';
				headerRow += '<th>' + window.pricePerChildLabel + '</th>';
				colCount = 6;
			}
			
			headerRow += '<th>' + window.pricePerDayLabel + '</th>';		
			
			headerRow += '</tr>';
			$('table.tour_price_breakdown thead').append(headerRow);	
			
			var footerRow = '<tr>';
			footerRow += '<th colspan="' + (colCount - 1) + '">' + window.priceTotalLabel + '</th>';
			footerRow += '<td class="reservation_total">0</td>';
			footerRow += '</tr>';
			$('table.tour_price_breakdown tfoot').append(footerRow);
			
			if (selectedStartTime) {			
				window.bookingRequest.totalPrice = 0;
				window.bookingRequest.totalTourOnlyPrice = 0;
				
				tours.buildTourRateRow(selectedStartTime, adults, children);
			}
			
		},		
		buildTourRateRow : function (startTime, adults, children) {
			
			var d = new Date(startTime);
			var day = d.getDate();
			var month = d.getMonth() + 1;
			var year = d.getFullYear();
			
			var dateValue = day + "-" + month + "-" + year; 
			
			var dataObj = {
				'action':'tour_get_price_request',
				'tourId' : window.tourId,
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
					var pricePerTour = parseFloat(prices.price);
					var pricePerChild = 0;
					var totalPrice = 0;
					
					tableRow += '<tr>';
					tableRow += '<td>' + dateValue + '</td>';
					
					if (!window.tourIsPricePerGroup) {
						pricePerChild = parseFloat(prices.child_price);
						tableRow += '<td>' + adults + '</td>';
						tableRow += '<td>' + tours.formatPrice(pricePerTour) + '</td>';
						tableRow += '<td>' + children + '</td>';
						tableRow += '<td>' + tours.formatPrice(pricePerChild) + '</td>';
						totalPrice = (pricePerTour * adults) + (pricePerChild * children);
					} else {
						totalPrice = pricePerTour;
					}					
					
					tableRow += '<td>' + tours.formatPrice(totalPrice) + '</td>';
					window.bookingRequest.totalPrice = totalPrice + window.bookingRequest.extraItemsTotalPrice;
					window.bookingRequest.totalTourOnlyPrice = totalPrice;
					
					tableRow += '</tr>';
					
					$('table.tour_price_breakdown tbody').append(tableRow);
					
					$('.reservation_total').html(tours.formatPrice(window.bookingRequest.totalTourOnlyPrice));
					$('.total_price').html(tours.formatPrice(window.bookingRequest.totalPrice));
					$(".confirm_total_price_p").html(tours.formatPrice(window.bookingRequest.totalPrice));
					
					$('#datepicker_loading').hide();
					$("table.responsive").trigger('updated');
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		
		},
		bindTourDropDowns : function() {

			var countOffset = window.bookingRequest.maxCount - window.bookingRequest.adults - window.bookingRequest.children;
			var maxAdultCount = window.bookingRequest.adults + countOffset;
			if (maxAdultCount < window.bookingRequest.adults) {
				maxAdultCount = parseInt(window.bookingRequest.adults);
			} else if (maxAdultCount > window.bookingRequest.maxCount) {
				maxAdultCount = parseInt(window.bookingRequest.adults);
			}
			
			$('#booking_form_adults').unbind();			
			$('#booking_form_adults').find('option').remove();
			for ( var i = 1; i <= maxAdultCount; i++ ) {
				$('<option ' + (i == window.bookingRequest.adults ? 'selected' : '') + '>').val(i).text(i).appendTo('#booking_form_adults');
			}
			
			$('#booking_form_adults').change(function (e) {
				window.bookingRequest.adults = parseInt($(this).val());
				$('span.adults_text').html(window.bookingRequest.adults);

				tours.bindTourDropDowns();
				tours.bindTourRatesTable();
				tours.recalculateExtraItemTotals();
			});
			
			$.uniform.update("#booking_form_adults");
						
			countOffset = window.bookingRequest.maxCount - window.bookingRequest.adults - window.bookingRequest.children;
			var maxChildrenCount = window.bookingRequest.children + countOffset;
			if (maxChildrenCount < window.bookingRequest.children) {
				maxChildrenCount = parseInt(window.bookingRequest.children);
			} else if (maxChildrenCount > window.bookingRequest.maxCount) {
				maxChildrenCount = parseInt(window.bookingRequest.children);
			}

			$('#booking_form_children').unbind();
			$('#booking_form_children').find('option').remove();
			$('<option selected>').val(0).text(0).appendTo('#booking_form_children');
			for ( var j = 1; j <= maxChildrenCount; j++ ) {
				$('<option ' + (j == window.bookingRequest.children ? 'selected' : '') + '>').val(j).text(j).appendTo('#booking_form_children');
			}
			
			$('#booking_form_children').change(function (e) {
				window.bookingRequest.children = parseInt($(this).val());
				$('span.children_text').html(window.bookingRequest.children);
				tours.bindTourDropDowns();
				tours.bindTourRatesTable();
				tours.recalculateExtraItemTotals();
			});
			
			$.uniform.update("#booking_form_children");
		},
		recalculateExtraItemTotals: function() {
		
			if (Object.size(window.bookingRequest.extraItems) > 0) {
			
				if (window.bookingRequest.extraItemsTotalPrice > 0) {
					window.bookingRequest.totalPrice = window.bookingRequest.totalTourOnlyPrice;
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
				
				$('.extra_items_total').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(tours.formatPrice(window.bookingRequest.totalPrice));		
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
			footerRow += '<td class="extra_items_total">' + tours.formatPrice(0) + '</td>';
			footerRow += '</tr>';

			$('table.extra_items_price_breakdown tfoot').append(footerRow);
		},
		bindExtraItemsQuantitySelect: function() {

			$('select.extra_item_quantity').unbind('change');	
			$('select.extra_item_quantity').on('change', function(e) {

				var quantity = parseInt($(this).val());
				var extraItemId = $(this).attr('id').replace('extra_item_quantity_', '');
				
				tours.updateExtraItemSelection(extraItemId, quantity);
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
							pricingMethod = '(' + window.pricedPerDayPerPersonLabel + ')';
						else if (value.pricePerDay)
							pricingMethod = '(' + window.pricedPerDayLabel + ')';
						else if (value.pricePerPerson)
							pricingMethod = '(' + window.pricedPerPersonLabel + ')';
							
						extraItemRows += '<tr class="extra_item_row_' + value.Id + '"><td>' + value.quantity + ' x ' + value.title + ' ' + (pricingMethod) + ' </td><td>' + tours.formatPrice(value.summedPrice) + '</td></tr>';
					});
				}
				
				$('table.extra_items_price_breakdown tbody').html(extraItemRows);
				
				$('.extra_items_total').html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));		
				$('.total_price').html(tours.formatPrice(window.bookingRequest.totalPrice));		

				$.uniform.update(".extra_item_quantity");		
			}
		},		
		bindTourDatePicker : function  () {
		
			if (typeof $('.tour_schedule_datepicker') !== 'undefined') {
				$('.tour_schedule_datepicker').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: [2, 2],	
					minDate: 0,
					beforeShowDay: function(d) {
						var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
						
						var selectedTime = null;
					
						if ($("#start_date").val()) {
							selectedTime = parseInt($("#start_date").val());
						}
					
						if (window.tourScheduleEntries) {
							var dateTextForCompare = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
							var dateTextForCompare2 = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2) + ' 00:00:00';
						
							if (dUtc == selectedTime)
								return [false, 'dp-hightlight dp-highlight-selected'];								
							if ($.inArray(dateTextForCompare, window.tourScheduleEntries) == -1 && $.inArray(dateTextForCompare2, window.tourScheduleEntries) == -1)
								return [false, 'ui-datepicker-unselectable ui-state-disabled'];
						}
						
						return [true, "dp-highlight"];
					},
					onSelect: function(dateText, inst) {

						var selectedTime = Date.UTC(inst.currentYear, inst.currentMonth, inst.currentDay);					
					
						tours.selectStartDate(selectedTime, dateText);	
						tours.bindTourRatesTable();						
						tours.recalculateExtraItemTotals();
					},
					onChangeMonthYear: function (year, month, inst) {
					
						window.currentMonth = month;
						window.currentYear = year;
						window.currentDay = 1;
						
						tours.populateTourScheduleEntries(window.tourId, window.currentDay, window.currentMonth, window.currentYear,tours.refreshDatePicker);
					}
				});
			}
		
		},
		refreshDatePicker : function() {
		
			if (typeof $('.tour_schedule_datepicker') !== 'undefined') {
				$('.tour_schedule_datepicker').datepicker( "refresh" );
			}
			$('#wait_loading').hide();	
		},
		processTourBooking : function () {
		
			$('#wait_loading').show();
			
			var tourDate = tours.getSelectedStartDate();
			tourDate = tourDate.getFullYear() + "-" + (tourDate.getMonth() + 1) + "-" + tourDate.getDate(); 
			var tourScheduleId = tours.getTourScheduleId(window.tourId, tourDate);
			var tourStartDateText = $("#start_date_span").html();
			var adults = $("#booking_form_adults").val();
			var children = $("#booking_form_children").val();
			
			var cValS = $('#c_val_s_tour').val();
			var cVal1 = $('#c_val_1_tour').val();
			var cVal2 = $('#c_val_2_tour').val();
			
			var dataObj = {
				'action':'tour_process_booking_ajax_request',
				'user_id' : window.currentUserId,
				'tour_schedule_id' : tourScheduleId,
				'tour_date' : tourDate,
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
			
			$(".confirm_tour_date_p").html(tourStartDateText);
			$(".confirm_tour_title_p").html(window.tourTitle);
			$(".confirm_adults_p").html(adults);
			$(".confirm_children_p").html(children);
			$(".confirm_reservation_total_p").html(tours.formatPrice(window.bookingRequest.totalTourOnlyPrice));
			$(".confirm_extra_items_total_p").html(tours.formatPrice(window.bookingRequest.extraItemsTotalPrice));
			$(".confirm_total_price_p").html(tours.formatPrice(window.bookingRequest.totalPrice));
			
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
						
						tours.hideTourBookingForm();
						tours.showTourConfirmationForm();
					}
					$('#wait_loading').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		},
		getTourScheduleId : function (tourId, date) {
			var scheduleId = 0;
			var dataObj = {
				'action':'tour_available_schedule_id_request',
				'tourId' : tourId,
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
		getMaxPeople : function (tourScheduleId) {
		
			var tourStartDate = tours.getSelectedStartDate();
			tourStartDate = tourStartDate.getFullYear() + "-" + (tourStartDate.getMonth() + 1) + "-" + tourStartDate.getDate(); 
		
			var max_people = 0;
			var dataObj = {
				'action':'tour_max_people_ajax_request',
				'tourScheduleId' : tourScheduleId,
				'tourId' : window.tourId,
				'dateValue' : tourStartDate,
				'nonce' : BYTAjax.nonce
			};
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					max_people = data;
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
			
			return max_people;
		},
		getTourScheduleDurationDays : function (scheduleId) {
			
			var days = 0;
			var dataObj = {
				'action':'tour_get_schedule_duration_days_request',
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
		getTourIsReservationOnly : function (tourId) {
			var isReservationOnly = 0;
			var dataObj = {
				'action':'tour_is_reservation_only_request',
				'tour_id' : tourId,
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
		populateTourScheduleEntries : function (tourId, day, month, year, callDelegate) {
			
			var dateArray = [];
			
			var dataObj = {
				'action':'tour_schedule_dates_request',
				'tourId' : tourId,
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
					
					var i = 0;
					for (i = 0; i < scheduleDates.length; ++i) {
						if (scheduleDates[i].tour_date !== null) {
							dateArray.push(scheduleDates[i].tour_date);
						}
					}
					
					window.tourScheduleEntries = dateArray;
					
					if (typeof(callDelegate) !== 'undefined') {
						callDelegate();
					}
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
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