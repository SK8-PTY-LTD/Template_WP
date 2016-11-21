(function($) {

	$(document).ready(function () {
		inquiry.init();
	});
	
	var inquiry = {

		init: function () {
	
			$('.contact-' + window.postType).on('click', function(event) {
				inquiry.showInquiryForm();
				event.preventDefault();
			});	

			$('.cancel-' + window.postType + '-inquiry').on('click', function(event) {
				inquiry.hideInquiryForm();
				event.preventDefault();
			});	
			
			$('.' + window.postType + '-inquiry-form').validate({
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
				submitHandler: function() { inquiry.processInquiry(); }
			});
			
			$.each(window.inquiryFormFields, function(index, field) {
				if (field.hide !== '1' && field.id !== null && field.id.length > 0) {
					var $input = null;
					if (field.type == 'text' || field.type == 'email') {
						$input = $('.' + window.postType + '-inquiry-form').find('input[name=' + field.id + ']');
					} else if (field.type == 'textarea') {
						$input = $('.' + window.postType + '-inquiry-form').find('textarea[name=' + field.id + ']');
					}
					
					if ($input !== null && typeof($input) !== 'undefined') {
						if (field.required == '1') {
							$input.rules('add', {
								required: true,
								messages: {
									required: window.inquiryFormRequiredError
								}
							});
						}
						if (field.type == 'email') {
							$input.rules('add', {
								email: true,
								messages: {
									required: window.inquiryFormEmailError
								}
							});
						}
					}
				}
			});
		},		
		showInquiryForm : function () {
			$('.three-fourth').hide();
			$('.right-sidebar').hide();
			$('.full-width.' + window.postType + '-inquiry-section').show();
		},
		hideInquiryForm : function () {
			$('.three-fourth').show();
			$('.right-sidebar').show();
			$('.full-width.' + window.postType + '-inquiry-section').hide();
		},
		processInquiry : function () {
			
			var cValS = $('#c_val_s_inq').val();
			var cVal1 = $('#c_val_1_inq').val();
			var cVal2 = $('#c_val_2_inq').val();
			
			var dataObj = {
				'action':'inquiry_ajax_request',
				'userId' : window.currentUserId,
				'postId' : window.postId,
				'c_val_s' : cValS,
				'c_val_1' : cVal1,
				'c_val_2' : cVal2,
				'nonce' : BYTAjax.nonce
			};
			
			$.each(window.inquiryFormFields, function(index, field) {
				if (field.hide !== '1') {
					dataObj[field.id] = $('#' + field.id).val();
				}
			});
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					if (data == 'captcha_error') {
						$("div.error div p").html(window.InvalidCaptchaMessage);
						$("div.error").show();
					} else {
						$("div.error div p").html('');
						$("div.error").hide();
						$('.contact-' + window.postType).hide(); // hide the button
						inquiry.hideInquiryForm();
						$('.inquiry-form-thank-you').show();
					}
				},
				error: function(errorThrown) {

				}
			}); 
		}
	};

})(jQuery);