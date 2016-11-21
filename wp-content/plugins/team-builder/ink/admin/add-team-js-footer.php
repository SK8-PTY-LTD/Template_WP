<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script>
var j = 1000;
	function add_new_content(){
	var output = 	'<li class="wpsm_ac-panel single_color_box" >'+
			'<div class="col-md-8">'+
				'<div class="col-md-4">'+
					'<img style="margin-bottom:15px" class="team-img-responsive" src="<?php echo wpshopmart_team_b_directory_url.'assets/images/team.jpg'; ?>" />'+
					'<input style="margin-bottom:15px" type="button" id="upload-background" name="upload-background" value="Upload Member Photo" class="button-primary rcsp_media_upload btn-block" onclick="wpsm_media_upload(this)" />'+
					'<input style="display:block;width:100%" type="hidden"  name="mb_photo[]" class="wpsm_ac_label_text"  value="<?php echo wpshopmart_team_b_directory_url.'assets/images/team.jpg'; ?>"  readonly="readonly" placeholder="No Media Selected" />'+
					'<span>Please upload square-cropped photos with a minimum dimension of 500px</span>'+
				'</div>'+
				'<div class="col-md-8">'+
					'<span class="ac_label"><?php _e('Member Name',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_name[]" value="" placeholder="Enter Member Name Here" class="wpsm_ac_label_text">'+
					'<span class="ac_label"><?php _e('Member Designation',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_pos[]" value="" placeholder="Enter Member Designation Title Here" class="wpsm_ac_label_text">'+
					'<span class="ac_label"><?php _e('Member Small Description',wpshopmart_team_b_text_domain); ?></span>'+
					'<textarea  name="mb_desc[]"  placeholder="Enter Member Small Description Here" class="wpsm_ac_label_text"></textarea>'+
				'</div>'+
			'</div>'+
			'<div class="col-md-4">'+
					'<span class="ac_label"><?php _e('Facebook Profile Url',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_fb_url[]" value="" placeholder="Enter Member Facebook profile url with http://" class="wpsm_ac_label_text">'+
					'<span class="ac_label"><?php _e('Twitter Profile Url',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_twit_url[]" value="" placeholder="Enter Member Twitter profile url with http://" class="wpsm_ac_label_text">'+
					'<span class="ac_label"><?php _e('Linkedin Profile Url',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_lnkd_url[]" value="" placeholder="Enter Member Linkedin profile url with http://" class="wpsm_ac_label_text">'+
					'<span class="ac_label"><?php _e('GooglePlus Profile Url',wpshopmart_team_b_text_domain); ?></span>'+
					'<input type="text"  name="mb_gp_url[]" value="" placeholder="Enter Member Google+ profile url with http://" class="wpsm_ac_label_text">'+
					'<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>'+
			'</div>'+
		'</li>';
	jQuery(output).hide().appendTo("#wpsm_team_panel").slideDown("slow");
	j++;
	
	}
	jQuery(document).ready(function(){

	  jQuery('#wpsm_team_panel').sortable({
	  
	   revert: true,
	     
	  });
	});
	
	
</script>
<script>
	jQuery(function(jQuery)
		{
			var colorbox = 
			{
				wpsm_team_panel: '',
				init: function() 
				{
					this.wpsm_team_panel = jQuery('#wpsm_team_panel');

					this.wpsm_team_panel.on('click', '.remove_button', function() {
					if (confirm('Are you sure you want to delete this?')) {
						jQuery(this).closest('li').slideUp(600, function() {
							jQuery(this).remove();
						});
					}
					return false;
					});
					 jQuery('#delete_all_colorbox').on('click', function() {
						if (confirm('Are you sure you want to delete all the Colorbox?')) {
							jQuery(".single_color_box").slideUp(600, function() {
								jQuery(".single_color_box").remove();
							});
							jQuery('html, body').animate({ scrollTop: 0 }, 'fast');
							
						}
						return false;
					});
					
			   }
			};
		colorbox.init();
	});
</script>


<script>
	
	
	function open_editor(id){
		

		var value = jQuery("#"+id).closest('li').find('textarea').val();
		jQuery("#get_text-html").click();
		jQuery("#get_text").val(value);
		jQuery("#get_id").val(jQuery("#"+id).attr('id'));
	 }
	
	
	function insert_html(){
		jQuery("#get_text-html").click();
		var html_text = jQuery("#get_text").val();
		var id = jQuery("#get_id").val();
		jQuery("#"+id).closest('li').find('textarea').val(html_text);
			
	}
	
	
</script>