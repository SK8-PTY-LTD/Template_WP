(function($) {

	$(document).ready(function () {
		frontend_submit.init();
	});
	
	String.prototype.filename=function(extension){
		var s= this.replace(/\\/g, '/');
		s= s.substring(s.lastIndexOf('/')+ 1);
		return extension? s.replace(/[?#].+$/, ''): s.split('.')[0];
	};
	
	String.prototype.rtrim = function(chr) {
	  var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$');
	  return this.replace(rgxtrim, '');
	};
	
	String.prototype.ltrim = function(chr) {
	  var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+');
	  return this.replace(rgxtrim, '');
	};
	
	var frontend_submit = {

		init: function () {
		
			Dropzone.autoDiscover = false;		
			
			var nonce = $('#_wpnonce').val();
			var entryId = $('#fes_entry_id').val();
			var contentType = $('#fes_content_type').val();
		
			var featuredDropzone = $("#featured-image-uploader").dropzone({
			
				url: window.adminAjaxUrl + '?action=frontend_featured_upload&_wpnonce=' + nonce + '&entry_id=' + entryId + '&content_type=' + contentType,
				acceptedFiles: 'image/*',
				success: function (file, response) {
				
					file.previewElement.classList.add("dz-success");
					file.image_id = response; // push the id for future reference

					$('#featured-image-id').val(response);
				},
				error: function (file, response) {
					file.previewElement.classList.add("dz-error");
				},
				// update the following section is for removing image from library
				addRemoveLinks: true,
				uploadMultiple: false,
				maxFiles:1,
				removedfile: function(file) {
				
					var imageId = file.image_id;        
					
					$.ajax({
						type: 'POST',
						url: window.adminAjaxUrl + '?action=frontend_delete_featured_image',
						data: {
							image_id : imageId,
							entry_id : $('#fes_entry_id').val(),
							_wpnonce : $('#_wpnonce').val(),
							content_type : $('#fes_content_type').val()
						},
						success:function(data) {
						},
						error: function(errorThrown){
							console.log(errorThrown);
						}
					});
					var _ref;
					return (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
				},
				init: function() {
					this.on("addedfile", function() {
						if (this.files.length > 1 && this.files[1] !== null && this.files[1] !== undefined){
							this.removeFile(this.files[0]);
						}
					});
					this.on("maxfilesexceeded", function(file){
						this.removeFile(file);
					});
					
					if (window.featuredImageUri) {
					
						var featuredFileName = window.featuredImageUri.filename();
						var myDropzone = this;
						var mockFile = { 
							size: 12345,
							name: featuredFileName,
							status: Dropzone.ADDED, 
							accepted: true,
							url: window.featuredImageUri,
							image_id: window.featuredImageId
						};

						myDropzone.emit("addedfile", mockFile);
						myDropzone.emit("complete", mockFile);
						myDropzone.emit("thumbnail", mockFile, window.featuredImageUri);
						myDropzone.files.push(mockFile);			
					}
				}
			});
			
			$("#gallery-image-uploader").dropzone({
			
				url: window.adminAjaxUrl + '?action=frontend_gallery_upload&_wpnonce=' + $('#_wpnonce').val() + '&entry_id=' + $('#fes_entry_id').val() + '&content_type=' + $('#fes_content_type').val(),
				acceptedFiles: 'image/*',
				parallelUploads: 1,
				success: function (file, response) {
				
					file.previewElement.classList.add("dz-success");
					file.image_id = response; // push the id for future reference

					var imageIds = '';
					if ($('#gallery-image-ids').val() !== undefined && $('#gallery-image-ids').val() !== '') {
						imageIds = $('#gallery-image-ids').val() + ',';
					}
					imageIds +=  response;
					
					$('#gallery-image-ids').val(imageIds);
				},
				error: function (file, response) {
					file.previewElement.classList.add("dz-error");
				},
				// update the following section is for removing image from library
				addRemoveLinks: true,
				removedfile: function(file) {
				
					var imageId = file.image_id;   
					
					$.ajax({
						type: 'POST',
						url: window.adminAjaxUrl + '?action=frontend_delete_gallery_image',
						data: {
							image_id : imageId,
							entry_id : $('#fes_entry_id').val(),
							_wpnonce : $('#_wpnonce').val(),
							content_type : $('#fes_content_type').val()
						},
						success:function(data) {
						},
						error: function(errorThrown){
							console.log(errorThrown);
						}
					});
					
					var imageIds = $('#gallery-image-ids').val();
					// remove from middle
					imageIds = imageIds.replace(',' + imageId + ',', ',');
					// remove from left
					imageIds = imageIds.ltrim(imageId + ',');
					// remove from right
					imageIds = imageIds.rtrim(',' + imageId);
					
					if (imageIds == imageId) {
						imageIds = '';
					}
					
					$('#gallery-image-ids').val(imageIds);
					
					var _ref;
					return (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
				},
				init: function() {
					if (window.galleryImageUris !== null && window.galleryImageUris !== undefined && window.galleryImageUris.length > 0) {

						var myDropzone = this;
						
						$.each(window.galleryImageUris, function(index, image) {

							if (image.image_uri !== null) {
								var fileName = image.image_uri.filename();
								var mockFile = { 
									size: 12345,
									name: fileName,
									status: Dropzone.ADDED, 
									accepted: true,
									url: image.image_uri,
									image_id : image.image_id
								};

								myDropzone.emit("addedfile", mockFile);
								myDropzone.emit("complete", mockFile);
								myDropzone.emit("thumbnail", mockFile, image.image_uri);
								myDropzone.files.push(mockFile);
							}
						});		
					}
				}
			});
	
			$.validator.addMethod( "greaterThan", 
				function(value, element, params) {
					if (!/Invalid|NaN/.test(new Date(value))) {
						return new Date(value) > new Date($(params).val());
					}
					return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val())); 
				}, ''
			);

			if ($( '.fes-upload-form.fes-form-room_type' ).length > 0 ) {
				$( '.fes-upload-form.fes-form-room_type' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
	
			if ($( '.fes-upload-form.fes-form-accommodation' ).length > 0) {
				$( '.fes-upload-form.fes-form-accommodation' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ($( '.fes-upload-form.fes-form-tour' ).length > 0) {
				$( '.fes-upload-form.fes-form-tour' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ($( '.fes-upload-form.fes-form-cruise' ).length > 0) {
				$( '.fes-upload-form.fes-form-cruise' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ($( '.fes-upload-form.fes-form-cabin_type' ).length > 0) {
				$( '.fes-upload-form.fes-form-cabin_type' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ($( '.fes-upload-form.fes-form-car_rental' ).length > 0) {
				$( '.fes-upload-form.fes-form-car_rental' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
	
			if ( $( '.fes-upload-form.fes-form-vacancy' ).length > 0 ) {
				$( '.fes-upload-form.fes-form-vacancy' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ( $( '.fes-upload-form.fes-form-tour_schedule' ).length > 0 ) {
				$( '.fes-upload-form.fes-form-tour_schedule' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if ( $( '.fes-upload-form.fes-form-cruise_schedule' ).length > 0 ) {
				$( '.fes-upload-form.fes-form-cruise_schedule' ).validate({
					submitHandler: function(form) {
						form.submit();
					}
				});
			}
			
			if (typeof($('.fes-upload-form.fes-form-vacancy #fes_start_date')) != 'undefined' && $('.fes-upload-form.fes-form-vacancy #fes_start_date').length > 0) {	
				$('.fes-upload-form.fes-form-vacancy #fes_start_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#start_date',
					altFormat: window.datepickerAltFormat,					
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-vacancy #fes_end_date").datepicker("option", "minDate", d);
						}
					},
					beforeShowDay: function(d) {
						var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
						var today = new Date();
						var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());

						if (typeof(window.accommodationCheckinWeekday) !== 'undefined' && window.accommodationCheckinWeekday.length > 0 && window.accommodationCheckinWeekday > -1) {
							var dayOfWeek = d.getDay();
							if (dayOfWeek == (window.accommodationCheckinWeekday)) {
								return [true, ''];
							} else {
								return [false, 'ui-datepicker-unselectable ui-state-disabled'];
							}
						}
						if ( todayUtc.valueOf() > dUtc )
							return [false,  "ui-datepicker-unselectable ui-state-disabled"];
						else 
							return [true, "dp-highlight"];
					}
				});
				
				if (typeof(window.datepickerVacancyStartDate) !== 'undefined' && window.datepickerVacancyStartDate !== null && window.datepickerVacancyStartDate.length > 0) {
					$('.fes-upload-form.fes-form-vacancy #fes_start_date').datepicker('setDate', window.datepickerVacancyStartDate);
				}

				$('.fes-upload-form.fes-form-vacancy #fes_end_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#end_date',
					altFormat: window.datepickerAltFormat,					
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-vacancy #fes_start_date").datepicker("option", "maxDate", d);
						}
					},
					beforeShowDay: function(d) {
						var dUtc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
						var today = new Date();
						var todayUtc = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
						
						if (typeof(window.accommodationCheckoutWeekday) !== 'undefined' && window.accommodationCheckoutWeekday.length > 0 && window.accommodationCheckoutWeekday > -1) {
							var dayOfWeek = d.getDay();
							if (dayOfWeek == (window.accommodationCheckoutWeekday)) {
								return [true, ''];
							} else {
								return [false, 'ui-datepicker-unselectable ui-state-disabled'];
							}
						}
						if ( todayUtc.valueOf() > dUtc )
							return [false,  "ui-datepicker-unselectable ui-state-disabled"];
						else 
							return [true, "dp-highlight"];
					}
				});
				
				if (typeof(window.datepickerVacancyEndDate) !== 'undefined' && window.datepickerVacancyEndDate !== null && window.datepickerVacancyEndDate.length > 0) {
					$('.fes-upload-form.fes-form-vacancy #fes_end_date').datepicker('setDate', window.datepickerVacancyEndDate);
				}
			}
			
			if (typeof($('.fes-upload-form.fes-form-tour_schedule #fes_start_date')) != 'undefined' && $('.fes-upload-form.fes-form-tour_schedule #fes_start_date').length > 0) {	
				$('.fes-upload-form.fes-form-tour_schedule #fes_start_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#start_date',
					altFormat: window.datepickerAltFormat,		
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-tour_schedule #fes_end_date").datepicker("option", "minDate", d);
						}
					},
				});
								
				if (typeof(window.datepickerScheduleStartDate) !== 'undefined' && window.datepickerScheduleStartDate !== null && window.datepickerScheduleStartDate.length > 0) {
					$('.fes-upload-form.fes-form-tour_schedule #fes_start_date').datepicker('setDate', window.datepickerScheduleStartDate);
				}

				$('.fes-upload-form.fes-form-tour_schedule #fes_end_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#end_date',
					altFormat: window.datepickerAltFormat,		
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-tour_schedule #fes_start_date").datepicker("option", "maxDate", d);
						}
					},
				});
				
				if (typeof(window.datepickerScheduleEndDate) !== 'undefined' && window.datepickerScheduleEndDate !== null && window.datepickerScheduleEndDate.length > 0) {
					$('.fes-upload-form.fes-form-tour_schedule #fes_end_date').datepicker('setDate', window.datepickerScheduleEndDate);
				}
			}
			
			if (typeof($('.fes-upload-form.fes-form-cruise_schedule #fes_start_date')) != 'undefined' && $('.fes-upload-form.fes-form-cruise_schedule #fes_start_date').length > 0) {	
				$('.fes-upload-form.fes-form-cruise_schedule #fes_start_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#start_date',
					altFormat: window.datepickerAltFormat,		
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-cruise_schedule #fes_end_date").datepicker("option", "minDate", d);
						}
					},
				});
								
				if (typeof(window.datepickerScheduleStartDate) !== 'undefined' && window.datepickerScheduleStartDate !== null && window.datepickerScheduleStartDate.length > 0) {
					$('.fes-upload-form.fes-form-cruise_schedule #fes_start_date').datepicker('setDate', window.datepickerScheduleStartDate);
				}

				$('.fes-upload-form.fes-form-cruise_schedule #fes_end_date').datepicker({
					dateFormat: window.datepickerDateFormat,
					numberOfMonths: 1,
					minDate: 0,
					showOn: 'button',
					altField: '#end_date',
					altFormat: window.datepickerAltFormat,		
					buttonImage: window.themePath + '/images/ico/calendar.png',
					buttonImageOnly: true,
					onClose: function (selectedDate) {
						var d = $.datepicker.parseDate(window.datepickerDateFormat, selectedDate);
						if (d !== null && typeof(d) !== 'undefined') {
							d = new Date(d.getFullYear(), d.getMonth(), d.getDate()+1);
							$(".fes-upload-form.fes-form-cruise_schedule #fes_start_date").datepicker("option", "maxDate", d);
						}
					},
				});
				
				if (typeof(window.datepickerScheduleEndDate) !== 'undefined' && window.datepickerScheduleEndDate !== null && window.datepickerScheduleEndDate.length > 0) {
					$('.fes-upload-form.fes-form-cruise_schedule #fes_end_date').datepicker('setDate', window.datepickerScheduleEndDate);
				}
			}
			
			if ($( '.fes-upload-form.fes-form-accommodation #fes_accommodation_is_price_per_person' ).is(":checked")) {
				$('.per_person').show();
			} else {
				$('.per_person').hide();
			}
			
			if ($( '.fes-upload-form.fes-form-cruise #fes_cruise_is_price_per_person' ).is(":checked")) {
				$('.per_person').show();
			} else {
				$('.per_person').hide();
			}
			
			$( '.fes-upload-form.fes-form-accommodation #fes_accommodation_is_price_per_person' ).on('change', function(e) {
				if(this.checked) {
					$('.per_person').show();
				} else {
					$('.per_person').hide();
				}		
			});
			
			$( '.fes-upload-form.fes-form-cruise #fes_cruise_is_price_per_person' ).on('change', function(e) {
				if(this.checked) {
					$('.per_person').show();
				} else {
					$('.per_person').hide();
				}		
			});
			
			$('.button-delete-cruise-schedule').on('click', function(e) {

				var _wpnonce = ($(this).closest('div').find('#_wpnonce')).val();
				var scheduleId = ($(this).closest('div').find('.delete_cruise_schedule_id')).val();
				
				var dataObj = {
						'action':'frontend_delete_cruise_schedule_ajax_request',
						'schedule_id' : scheduleId,
						'nonce' : _wpnonce
					}				  

				$.ajax({
					url: BYTAjax.ajaxurl,
					data: dataObj,
					async: false,
					success:function(data) {
						// This outputs the result of the ajax request
						$('.article_cruise_schedule_' + scheduleId).remove();
					},
					error: function(errorThrown){
						console.log(errorThrown);
					}
				}); 			
			
				e.preventDefault();
			});				
			
			$('.button-delete-tour-schedule').on('click', function(e) {

				var _wpnonce = ($(this).closest('div').find('#_wpnonce')).val();
				var scheduleId = ($(this).closest('div').find('.delete_tour_schedule_id')).val();
				
				var dataObj = {
						'action':'frontend_delete_tour_schedule_ajax_request',
						'schedule_id' : scheduleId,
						'nonce' : _wpnonce
					}				  

				$.ajax({
					url: BYTAjax.ajaxurl,
					data: dataObj,
					async: false,
					success:function(data) {
						// This outputs the result of the ajax request
						$('.article_tour_schedule_' + scheduleId).remove();
					},
					error: function(errorThrown){
						console.log(errorThrown);
					}
				}); 			
			
				e.preventDefault();
			});			
			
			$('.button-delete-vacancy').on('click', function(e) {

				var _wpnonce = ($(this).closest('div').find('#_wpnonce')).val();
				var vacancyId = ($(this).closest('div').find('.delete_vacancy_id')).val();
				
				var dataObj = {
						'action':'frontend_delete_accommodation_vacancy_ajax_request',
						'vacancy_id' : vacancyId,
						'nonce' : _wpnonce
					}				  

				$.ajax({
					url: BYTAjax.ajaxurl,
					data: dataObj,
					async: false,
					success:function(data) {
						// This outputs the result of the ajax request
						$('.article_vacancy_' + vacancyId).remove();
					},
					error: function(errorThrown){
						console.log(errorThrown);
					}
				}); 			
			
				e.preventDefault();
			});
			
			if ($( '.fes-upload-form.fes-form-accommodation #fes_accommodation_disabled_room_types' ).is(":checked")) {
				$('.room_types').hide();
				$('.not_room_types').show();
			} else {
				$('.room_types').show();
				$('.not_room_types').hide();
			}
			
			$( '.fes-upload-form.fes-form-accommodation #fes_accommodation_disabled_room_types' ).on('change', function(e) {
				if(this.checked) {
					$('.room_types').hide();
					$('.not_room_types').show();
				} else {
					$('.room_types').show();
					$('.not_room_types').hide();
				}		
			});
			
			window.accommodationId = $( '.fes-upload-form.fes-form-vacancy select#fes_accommodation_id' ).val();
			if (window.accommodationId > 0) {
				window.disabledRoomTypes = frontend_submit.accommodationDisableRoomTypes(window.accommodationId);
				if (window.disabledRoomTypes) {
					$('.room_types').hide();
					$('.room_types').removeClass('required');
				} else {
					$('.room_types').addClass('required');
					$('.room_types').show();
				}
				
				window.isPricePerPerson = frontend_submit.accommodationIsPricePerPerson(window.accommodationId);
				window.rentType = frontend_submit.getAccommodationRentType(window.accommodationId);
				window.accommodationCheckinWeekday = frontend_submit.accommodationGetCheckinWeekday(window.accommodationId);
				window.accommodationCheckoutWeekday = frontend_submit.accommodationGetCheckoutWeekday(window.accommodationId);
				
				if (window.rentType == 0) {
					$('.daily_rent').show();
					$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerDayLabel;
					$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerDayChildLabel;
					
					if (window.isPricePerPerson) {
						$('.per_person').show();
						$('.per_person').addClass('required');
					} else {
						$('.per_person').hide();
						$('.per_person').removeClass('required');
					}
				} else {
					$('.daily_rent').hide();
					
					if (window.rentType == 1) {
						$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerWeekLabel;
						$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerWeekChildLabel;
					} else {
						$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerMonthLabel;
						$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerMonthChildLabel;
					}
					
					if (window.isPricePerPerson) {
						$('.per_person:not(.daily_rent)').show();
						$('.per_person:not(.daily_rent)').addClass('required');
					} else {
						$('.per_person').hide();
						$('.per_person').removeClass('required');
					}
				}
			}
			
			$( '.fes-upload-form.fes-form-vacancy select#fes_accommodation_id' ).on('change', function(e) {
				window.accommodationId = $(this).val()
				window.disabledRoomTypes = frontend_submit.accommodationDisableRoomTypes(window.accommodationId);
				
				if (window.disabledRoomTypes) {
					$('.room_types').hide();
					$('.room_types').removeClass('required');
				} else {
				
					var roomTypes = frontend_submit.listAccommodationRoomTypes(window.accommodationId);
					
					$('.fes-upload-form.fes-form-vacancy select#fes_room_type_id').find('option:gt(0)').remove();
					
					var roomTypeOptions = "";

					$.each(roomTypes,function(index){
						roomTypeOptions += '<option value="'+ roomTypes[index].id +'">' + roomTypes[index].name + '</option>'; 
					});

					$('.fes-upload-form.fes-form-vacancy select#fes_room_type_id').append(roomTypeOptions);
					
					$('.room_types').addClass('required');
					$('.room_types').show();
				}
				
				window.isPricePerPerson = frontend_submit.accommodationIsPricePerPerson(window.accommodationId);
				window.rentType = frontend_submit.getAccommodationRentType(window.accommodationId);
				window.accommodationCheckinWeekday = frontend_submit.accommodationGetCheckinWeekday(window.accommodationId);
				window.accommodationCheckoutWeekday = frontend_submit.accommodationGetCheckoutWeekday(window.accommodationId);
				
				if (window.rentType == 0) {
					$('.daily_rent').show();
					$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerDayLabel;
					$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerDayChildLabel;
					
					if (window.isPricePerPerson) {
						$('.per_person').show();
						$('.per_person').addClass('required');
					} else {
						$('.per_person').hide();
						$('.per_person').removeClass('required');
					}
				} else {
					$('.daily_rent').hide();
					
					if (window.rentType == 1) {
						$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerWeekLabel;
						$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerWeekChildLabel;
					} else {
						$('label[for="fes_price_per_day"]').contents()[0].nodeValue = window.pricePerMonthLabel;
						$('label[for="fes_price_per_day_child"]').contents()[0].nodeValue = window.pricePerMonthChildLabel;
					}
					
					if (window.isPricePerPerson) {
						$('.per_person:not(.daily_rent)').show();
						$('.per_person:not(.daily_rent)').addClass('required');
					} else {
						$('.per_person').hide();
						$('.per_person').removeClass('required');
					}
				}
			});
			
			$( '.fes-upload-form.fes-form-cruise_schedule select#fes_cruise_id' ).on('change', function(e) {
				var cruiseId = $(this).val()
				
				var cabinTypes = frontend_submit.listCruiseCabinTypes(cruiseId);
				
				$('.fes-upload-form.fes-form-cruise_schedule select#fes_cabin_type_id').find('option:gt(0)').remove();
				
				var cabinTypeOptions = "";

				$.each(cabinTypes,function(index){
					cabinTypeOptions += '<option value="'+ cabinTypes[index].id +'">' + cabinTypes[index].name + '</option>'; 
				});

				$('.fes-upload-form.fes-form-cruise_schedule select#fes_cabin_type_id').append(cabinTypeOptions);
				
				$('.cabin_types').addClass('required');
				$('.cabin_types').show();
				
				var isPricePerPerson = frontend_submit.cruiseIsPricePerPerson(cruiseId);
				if (isPricePerPerson) {
					$('.per_person').show();
					$('.per_person').addClass('required');
				} else {
					$('.per_person').hide();
					$('.per_person').removeClass('required');
				}
				
				var cruiseTypeIsRepeated = frontend_submit.cruiseTypeIsRepeated(cruiseId);
				if (!cruiseTypeIsRepeated) {
					$('.is_repeated').hide();
					$('.is_repeated').removeClass('required');
				} else {
					$('.is_repeated').addClass('required');
					$('.is_repeated').show();
				}
			});
			
			var cruiseId = $( '.fes-upload-form.fes-form-cruise_schedule select#fes_cruise_id' ).val();
			if (cruiseId > 0) {
				var isPricePerPerson = frontend_submit.cruiseIsPricePerPerson(cruiseId);
				if (isPricePerPerson) {
					$('.per_person').show();
					$('.per_person').addClass('required');
				} else {
					$('.per_person').hide();
					$('.per_person').removeClass('required');
				}
				
				var cruiseTypeIsRepeated = frontend_submit.cruiseTypeIsRepeated(cruiseId);
				if (!cruiseTypeIsRepeated) {
					$('.is_repeated').hide();
					$('.is_repeated').removeClass('required');
				} else {
					$('.is_repeated').addClass('required');
					$('.is_repeated').show();
				}
			}
			
			$( '.fes-upload-form.fes-form-tour_schedule select#fes_tour_id' ).on('change', function(e) {
				var tourId = $(this).val()
				
				var isPricePerGroup = frontend_submit.tourIsPricePerGroup(tourId);
				if (isPricePerGroup) {
					$('.per_person').hide();
					$('.per_person').removeClass('required');
				} else {
					$('.per_person').show();
					$('.per_person').addClass('required');
				}
				
				var tourTypeIsRepeated = frontend_submit.tourTypeIsRepeated(tourId);
				if (!tourTypeIsRepeated) {
					$('.is_repeated').hide();
					$('.is_repeated').removeClass('required');
				} else {
					$('.is_repeated').addClass('required');
					$('.is_repeated').show();
				}
			});
			
			var tourId = $( '.fes-upload-form.fes-form-tour_schedule select#fes_tour_id' ).val();
			if (tourId > 0) {
				var isPricePerGroup = frontend_submit.tourIsPricePerGroup(tourId);
				if (isPricePerGroup) {
					$('.per_person').hide();
					$('.per_person').removeClass('required');
				} else {
					$('.per_person').show();
					$('.per_person').addClass('required');
				}
				
				var tourTypeIsRepeated = frontend_submit.tourTypeIsRepeated(tourId);
				if (!tourTypeIsRepeated) {
					$('.is_repeated').hide();
					$('.is_repeated').removeClass('required');
				} else {
					$('.is_repeated').addClass('required');
					$('.is_repeated').show();
				}
			}
		},
		accommodationDisableRoomTypes : function (accommodationId) {
			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_disabled_room_types_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		listAccommodationRoomTypes : function(accommodationId) {
			
			var retVal = null;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_list_room_types_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(json) {
					// This outputs the result of the ajax request
					retVal = JSON.parse(json);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal;			
		},
		getAccommodationRentType : function(accommodationId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_get_rent_type_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		accommodationIsPricePerPerson : function(accommodationId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_is_price_per_person_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		accommodationGetCheckinWeekday : function(accommodationId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_checkin_weekday_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		accommodationGetCheckoutWeekday : function(accommodationId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'accommodation_checkout_weekday_ajax_request',
					'accommodationId' : accommodationId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		cruiseIsPricePerPerson : function(cruiseId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'cruise_is_price_per_person_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
		listCruiseCabinTypes : function(cruiseId) {
			
			var retVal = null;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'cruise_list_cabin_types_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(json) {
					// This outputs the result of the ajax request
					retVal = JSON.parse(json);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal;			
		},
		cruiseTypeIsRepeated : function(cruiseId) {
			
			var retVal = null;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'cruise_type_is_repeated_ajax_request',
					'cruiseId' : cruiseId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(json) {
					// This outputs the result of the ajax request
					retVal = JSON.parse(json);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal;			
		},		
		tourTypeIsRepeated : function(tourId) {
			
			var retVal = null;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'tour_type_is_repeated_ajax_request',
					'tourId' : tourId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(json) {
					// This outputs the result of the ajax request
					retVal = JSON.parse(json);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal;			
		},
		tourIsPricePerGroup : function(tourId) {

			var retVal = 0;
			var _wpnonce = $('#_wpnonce').val();
				
			var dataObj = {
					'action':'tour_is_price_per_group_ajax_request',
					'tourId' : tourId,
					'nonce' : _wpnonce
				}				  

			$.ajax({
				url: window.adminAjaxUrl,
				data: dataObj,
				async: false,
				success:function(data) {
					// This outputs the result of the ajax request
					retVal = data;
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			}); 
			
			return retVal ? parseInt(retVal) : 0;
		},
	};
})(jQuery);