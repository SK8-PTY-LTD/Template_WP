<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
	.checked_temp {
		position: absolute;
		background: #FF2300;
		color: #fff;
		top: -14px;
		right: 5px;
		border-radius: 50%;
		width: 35px;
		height: 35px;
		text-align: center;
		line-height: 29px;
		z-index: 999;
		font-size: 18px;
		border: 3px solid #fff;
	}
	.wpsm_team_admin_title{
		font-size:25px !important;
	}
	.wpsm_home_portfolio_showcase{
		position: relative;
    width: 100%;
    overflow: hidden;
	}
	</style>
	<h2 class="wpsm_team_admin_title">Select Your Design</h2>
	<?php
		
		$De_Settings = unserialize(get_option('Team_B_default_Settings'));
  $PostId = $post->ID;
  $Settings = unserialize(get_post_meta( $PostId, 'Team_B_Settings', true));
  
  if(isset($Settings['design'])) 
  {
	$templates = $Settings['design'];
  }else{
	   $templates = 1;
  }
	?>
<div style=" overflow: hidden;padding: 20px;margin-bottom:30px;">
	<?php for($i=1;$i<=2;$i++){ ?>
		<div class="col-md-4">
			<div class="demoftr">	
				<span class="checked_temp" id="checked_temp_<?php echo $i; ?>" <?php if($templates!=$i) { ?>  style="display:none" <?php } ?> ><i style="line-height:30px;" class="fa fa-check"></i></span>
				<div class="">
					<div class="wpsm_home_portfolio_showcase">
						<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_team_b_directory_url.'assets/images/team-'.$i.'.png'?>">
						<span style="position: absolute;
    bottom: 0px;
    width: 30%;
    overflow: hidden;
    text-align: center;
    background: rgba(0,0,0,0.8);
    padding-top: 5px;
    padding-bottom: 5px;
    color: #fff;
    border-top-left-radius: 15px;
    font-size: 13px;
    right: 0;"><a style="color:#fff;text-decoration:none" target="_blank" href="http://demo.wpshopmart.com/team-builder/demo-<?php echo $i; ?>/">Demo</a></span>
					</div>
				</div>
				<div style="padding:13px;overflow:hidden; background: #EFEFEF; border-top: 1px dashed #ccc;">
					<h3 class="text-center pull-left" style="margin-top: 10px;margin-bottom: 10px;font-weight:900">Design <?php echo $i; ?></h3>
					
					<button type="button" <?php if($templates==$i) { ?> disabled="disabled" style="background:#F50000;border-color:#F50000;" <?php } ?> class="pull-right btn btn-primary design_btn" id="templates_btn<?php echo $i; ?>" onclick="select_template('<?php echo $i; ?>')"><?php if($templates==$i){  echo "Selected"; } else { echo "Select"; } ?></button>
					
					<input type="radio" name="design" id="design<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($templates==$i){  echo "checked"; } ?> style="display:none">
				</div>		
			</div>	
		</div>
	<?php } ?>
	<script>

	function select_template(id)
	{
		
		jQuery(".design_btn").attr('style','');
		jQuery(".design_btn").prop("disabled", false);
		jQuery(".design_btn").text("Select");
		
		jQuery(".checked_temp").hide();
		jQuery("#checked_temp_"+id).show();
		if(id==8){
			jQuery(".top_border_color_group").show();
			
		}
		else{
			jQuery(".top_border_color_group").hide();
			
		}
		
		
		jQuery("#templates_btn"+id).attr('disabled','disabled');
		jQuery("#templates_btn"+id).attr('style','background:#F50000;border-color:#F50000;');
		jQuery("#templates_btn"+id).text("Selected");
		 jQuery("#design"+id).prop( "checked", true );
		
	}

</script>
</div>
<div style=" overflow: hidden;padding: 10px;">
	<h3><?php _e('Add Team Member Here',wpshopmart_team_b_text_domain); ?></h3>
	<input type="hidden" name="team_b_save_data_action" value="team_b_save_data_action" />
	<ul class="clearfix" id="wpsm_team_panel">
		<?php
		$i=1;
		$All_data = unserialize(get_post_meta( $post->ID, 'wpsm_team_b_data', true));
		$TotalCount =  get_post_meta( $post->ID, 'wpsm_team_b_count', true );
		if($TotalCount) 
		{
			if($TotalCount>0)
			{
				
				foreach($All_data as $single_data)
				{
					 $mb_photo = $single_data['mb_photo'];
					 $mb_name = $single_data['mb_name'];
					 $mb_pos = $single_data['mb_pos'];
					 $mb_desc = $single_data['mb_desc'];
					 $mb_fb_url = $single_data['mb_fb_url'];
					 $mb_twit_url = $single_data['mb_twit_url'];
					 $mb_lnkd_url = $single_data['mb_lnkd_url'];
					 $mb_gp_url = $single_data['mb_gp_url'];
					 
					 
					
					?>
	
					<li class="wpsm_ac-panel single_color_box" >
						<div class="col-md-8">
							<div class="col-md-4">
								<img style="margin-bottom:15px" class="team-img-responsive" src="<?php echo $mb_photo; ?>" />
								<input style="margin-bottom:15px" type="button" id="upload-background" name="upload-background" value="Upload Member Photo" class="button-primary rcsp_media_upload btn-block"  onclick="wpsm_media_upload(this)"/>
								<input style="display:block;width:100%" type="hidden"  name="mb_photo[]" class="wpsm_ac_label_text"  value="<?php echo $mb_photo; ?>"  readonly="readonly" placeholder="No Media Selected" />
								<span>Please upload square-cropped photos with a minimum dimension of 500px</span>
							</div>
							<div class="col-md-8">
								<span class="ac_label"><?php _e('Member Name',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_name[]" value="<?php echo esc_attr($mb_name); ?>" placeholder="Enter Member Name Here" class="wpsm_ac_label_text">
								
								<span class="ac_label"><?php _e('Member Designation',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_pos[]" value="<?php echo esc_attr($mb_pos); ?>" placeholder="Enter Member Designation Title Here" class="wpsm_ac_label_text">
								<span class="ac_label"><?php _e('Member Small Description',wpshopmart_team_b_text_domain); ?></span>
								<textarea  name="mb_desc[]"  placeholder="Enter Member Small Description Here" class="wpsm_ac_label_text"><?php echo esc_html($mb_desc); ?></textarea>
							</div>
						</div>
						<div class="col-md-4">
								<span class="ac_label"><?php _e('Facebook Profile Url',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_fb_url[]" value="<?php echo esc_attr($mb_fb_url); ?>" placeholder="Enter Member Facebook profile url with http://" class="wpsm_ac_label_text">
								
								<span class="ac_label"><?php _e('Twitter Profile Url',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_twit_url[]" value="<?php echo esc_attr($mb_twit_url); ?>" placeholder="Enter Member twiiter profile url with http://" class="wpsm_ac_label_text">
								
								<span class="ac_label"><?php _e('Linkedin Profile Url',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_lnkd_url[]" value="<?php echo esc_attr($mb_lnkd_url); ?>" placeholder="Enter Member linkedin profile url with http://" class="wpsm_ac_label_text">
								
								<span class="ac_label"><?php _e('GooglePlus Profile Url',wpshopmart_team_b_text_domain); ?></span>
								<input type="text"  name="mb_gp_url[]" value="<?php echo esc_attr($mb_gp_url); ?>" placeholder="Enter Member Google+ profile url with http://" class="wpsm_ac_label_text">
								
							<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
						
						</div>
					</li>
					<?php 
					$i++;
				} // end of foreach
			}
			else{
			echo "<h2>No Tabs Found</h2>";
			}
		}
		else
		{

			for($i=1; $i<=3; $i++)
			{
				?>
				<li class="wpsm_ac-panel single_color_box" >
					<div class="col-md-8">
						<div class="col-md-4">
							<img style="margin-bottom:15px" class="team-img-responsive" src="<?php echo wpshopmart_team_b_directory_url.'assets/images/team.jpg'; ?>" />
							<input style="margin-bottom:15px" type="button" id="upload-background" name="upload-background" value="Upload Member Photo" class="button-primary rcsp_media_upload btn-block"  onclick="wpsm_media_upload(this)"/>
							<input style="display:block;width:100%" type="hidden"  name="mb_photo[]" class="wpsm_ac_label_text"  value="<?php echo wpshopmart_team_b_directory_url.'assets/images/team.jpg'; ?>"  readonly="readonly" placeholder="No Media Selected" />
							<span>Please upload square-cropped photos with a minimum dimension of 500px</span>
						</div>
						<div class="col-md-8">
							<span class="ac_label"><?php _e('Member Name',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_name[]" value="Sample Title" placeholder="Enter Member Name Here" class="wpsm_ac_label_text">
							
							<span class="ac_label"><?php _e('Member Designation',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_pos[]" value="Sample Title" placeholder="Enter Member Designation Title Here" class="wpsm_ac_label_text">
							<span class="ac_label"><?php _e('Member Small Description',wpshopmart_team_b_text_domain); ?></span>
							<textarea  name="mb_desc[]"  placeholder="Enter Member Small Description Here" class="wpsm_ac_label_text">Sample Description</textarea>
						</div>
					</div>
					<div class="col-md-4">
							<span class="ac_label"><?php _e('Facebook Profile Url',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_fb_url[]" value="#" placeholder="Enter Member Facebook profile url with http://" class="wpsm_ac_label_text">
							
							<span class="ac_label"><?php _e('Twitter Profile Url',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_twit_url[]" value="#" placeholder="Enter Member twiiter profile url with http://" class="wpsm_ac_label_text">
							
							<span class="ac_label"><?php _e('Linkedin Profile Url',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_lnkd_url[]" value="#" placeholder="Enter Member linkedin profile url with http://" class="wpsm_ac_label_text">
							
							<span class="ac_label"><?php _e('GooglePlus Profile Url',wpshopmart_team_b_text_domain); ?></span>
							<input type="text"  name="mb_gp_url[]" value="#" placeholder="Enter Member Google+ profile url with http://" class="wpsm_ac_label_text">
							
						<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
					
					</div>
				</li>
				<?php
			}
		}
		?>
	</ul>
	<div style="display:block;margin-top:20px;overflow:hidden;width: 100%;float:left;">
		<div class="col-md-10">
			<a class="btn " id="add_new_ac" onclick="add_new_content()"  style="overflow:hidden;font-size: 52px;font-weight: 600; padding:10px 30px 10px 30px; background:#1e73be;width:100%;color:#fff;text-align:center"  >
				<?php _e('Add New Team Member', wpshopmart_team_b_text_domain); ?>
			</a>
		</div>
		<div class="col-md-2">
		<a  style="float: left;width:100%;padding:10px !important;background:#31a3dd;" class=" add_wpsm_ac_new delete_all_acc" id="delete_all_colorbox"    >
			<i style="font-size:57px;"class="fa fa-trash-o"></i>
			<span style="display:block"><?php _e('Delete All',wpshopmart_team_b_text_domain); ?></span>
		</a>
		</div>
	</div>
	
</div>
<?php require('add-team-js-footer.php'); ?>