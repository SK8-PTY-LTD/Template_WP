(function($) {

	$(document).ready(function () {
		reviews.init();
	});
	
	var reviews = {

		init: function () {

			$('.review-' + window.postType).on('click', function(event) {
				reviews.showReviewForm();
				event.preventDefault();
			});	
			
			$('.cancel-' + window.postType + '-review').on('click', function(event) {
				reviews.hideReviewForm();
				event.preventDefault();
			});	
			
			$('.review-' + window.postType + '-form').validate({
				onkeyup: false,
				rules: {
					likes: "required",
					dislikes: "required"
				},
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
				messages: {
					likes: window.reviewFormLikesError,
					dislikes: window.reviewFormDislikesError
				},
				submitHandler: function() { reviews.processReview(); }
			});	
		},		
		showReviewForm : function () {
			$('.three-fourth').hide();
			$('.right-sidebar').hide();
			$('.full-width.review-' + window.postType + '-section').show();
		},			
		hideReviewForm : function () {
			$('.three-fourth').show();
			$('.right-sidebar').show();
			$('.full-width.review-' + window.postType + '-section').hide();
		},					
		processReview : function () {
			var likes = $('#likes').val();
			var dislikes = $('#dislikes').val();

			var dataObj = {
				'action':'review_ajax_request',
				'likes' : likes,
				'dislikes' : dislikes,
				'userId' : window.currentUserId,
				'postId' : window.postId,
				'nonce' : BYTAjax.nonce
			};
			
			for (var i = 0; i < window.reviewFields.length; i++) {
				var slug = window.reviewFields[i];
				dataObj["reviewField_" + slug] = $("input[type='radio'][name='reviewField_" + slug + "']:checked").val();
			}
			
			$.ajax({
				url: BYTAjax.ajaxurl,
				data: dataObj,
				success:function(data) {
					// This outputs the result of the ajax request
					$('.review-' + window.postType).hide(); // hide the button
					$('.review-form-thank-you').show(); // show thank you message
					reviews.hideReviewForm();
				},
				error: function(errorThrown) {

				}
			}); 
		}
	};
	
})(jQuery);