jQuery(document).ready(function($){
	
	$(".sgmb-js-delete-link").bind('click',function() {
		var request = confirm("Are you sure?");
		if(!request) {
			return false;
		}
		var button_id = $(this).attr("data-sgmb-button-id");
		var csrf_token =  $(this).attr("data-sgmb-csrf-token");
		var data = {
			action: 'delete_button',
			_ajax_nonce: csrf_token,
			button_id: button_id
		}
		$.post(ajaxurl, data, function(response,d) {
			location.reload();
		});
	});

	$(".sgmb-js-clone-link").bind('click',function() {
		var request = confirm("Are you sure?");
		if(!request) {
			return false;
		}
		var button_id = $(this).attr("data-sgmb-button-id");
		var csrf_token =  $(this).attr("data-sgmb-csrf-token");
		var data = {
			action: 'clone_button',
			_ajax_nonce: csrf_token,
			button_id: button_id
		}
		$.post(ajaxurl, data, function(response,d) {
			location.reload();
		});
	});
});