(function($){

	$(document).ready(function () {
		metaboxes.init();
	});
	
	var metaboxes = {

		init: function () {
			
			$('#media-items').bind('DOMNodeInserted',function(){
				$('input[value="Insert into Post"]').each(function(){
						$(this).attr('value','Use This Image');
				});
			});
			
			$('.custom_upload_image_button').click(function() {
				var formfield = $(this).siblings('.custom_upload_image');
				var preview = $(this).siblings('.custom_preview_image');
				tb_show('', 'media-upload.php?type=image&TB_iframe=true');
				window.send_to_editor = function(html) {
					var imgurl = $('img',html).attr('src');
					var classes = $('img', html).attr('class');
					var id = classes.replace(/(.*?)wp-image-/, '');
					formfield.val(id);
					preview.attr('src', imgurl);
					tb_remove();
				};
				return false;
			});
			
			$('.custom_clear_image_button').click(function() {
				var defaultImage = $(this).parent().siblings('.custom_default_image').text();
				$(this).parent().siblings('.custom_upload_image').val('');
				$(this).parent().siblings('.custom_preview_image').attr('src', defaultImage);
				return false;
			});
			
			$('.repeatable-add').click(function() {
				var field = $(this).closest('td').find('.custom_repeatable li:last').clone(true);
				var fieldLocation = $(this).closest('td').find('.custom_repeatable li:last');
				$('input', field).val('').attr('name', function(index, name) {
					return name.replace(/(\d+)/, function(fullMatch, n) {
						return Number(n) + 1;
					});
				});
				field.insertAfter(fieldLocation, $(this).closest('td'));
				return false;
			});
			
			$('.repeatable-remove').click(function(){
				$(this).parent().remove();
				return false;
			});
				
			$('.custom_repeatable').sortable({
				opacity: 0.6,
				revert: true,
				cursor: 'move',
				handle: '.sort'
			});

		}		
	};

})(jQuery);