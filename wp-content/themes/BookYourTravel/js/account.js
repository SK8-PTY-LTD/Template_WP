(function($) {

	$(document).ready(function () {	
		bookyourtravel_account.init();
	});
	
	var bookyourtravel_account = {
		
		init : function () {
				
			$('#settings-first-name-form').validate({
				onkeyup: false,
				rules: { first_name: "required" },
				messages: { first_name: window.settingsFirstNameError },
				submitHandler: function() { bookyourtravel_account.processFirstNameSubmit(); },
				debug:true
			});
			
			$('#settings-last-name-form').validate({
				onkeyup: false,
				rules: { last_name: "required" },
				messages: { last_name: window.settingsLastNameError },
				submitHandler: function() { bookyourtravel_account.processLastNameSubmit(); },
				debug:true
			});
			
			$('#settings-email-form').validate({
				onkeyup: false,
				rules: { 
					email: {
						required: true,
						email: true
					}						
				},
				messages: { email: window.settingsEmailError },
				submitHandler: function() { bookyourtravel_account.processEmailSubmit(); },
				debug:true
			});
			
			$('#settings-password-form').validate({
				onkeyup: false,
				rules: { password: "required", old_password : "required" },
				messages: { password: window.settingsPasswordError },
				submitHandler: function() { bookyourtravel_account.processPasswordSubmit(); },
				debug:true
			});
		
			$('.edit_button').on('click', function(event) {
				$('div.edit_field').hide();
				$(this).parent().parent().find('td div.edit_field').show();
			});
			
			$('.hide_edit_field').on('click', function(event) {
				$('div.edit_field').hide();
			});
				
		},
		processFirstNameSubmit : function() {
			var first_name = $('#first_name').val();
			
			var dataObj = {
				'action':'settings_ajax_save_first_name', 
				'firstName' : first_name, 
				'userId' : window.currentUserId, 
				'nonce' : BYTAjax.nonce 
			};
				
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					$("#span_first_name").html(first_name);
					$('div.edit_field').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		},		
		processLastNameSubmit : function () {
			var last_name = $('#last_name').val();
			
			var dataObj = {
				'action':'settings_ajax_save_last_name',
				'lastName':last_name,
				'userId': window.currentUserId,
				'nonce' : BYTAjax.nonce
			};	
				
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					$("#span_last_name").html(last_name);
					$('div.edit_field').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		},		
		processEmailSubmit : function () {
			var email = $('#email').val();
			
			var dataObj = {
				'action':'settings_ajax_save_email',
				'email':email,
				'userId': window.currentUserId,
				'nonce' : BYTAjax.nonce
			};	
				
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					$("#span_email").html(email);
					$('div.edit_field').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		},		
		processPasswordSubmit : function () {
			var password = $('#password').val();
			var old_password = $('#old_password').val();
			$('div.edit_field').hide();
			
			var dataObj = {
				'action':'settings_ajax_save_password',
				'password':password,
				'oldPassword':old_password,
				'userId': window.currentUserId,
				'nonce' : BYTAjax.nonce
			};
				
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					$('div.edit_field').hide();
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			}); 
		}
	};
})(jQuery);