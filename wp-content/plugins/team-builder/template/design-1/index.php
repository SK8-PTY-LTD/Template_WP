<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
	<!-- wpshopmart team builder wrapper -->
	<div class="wpsm_team_1_b_row" id="wpsm_team_1_b_row_<?php echo $PostId; ?>">
		<div class="wpsm_row">  
			<style>
			#wpsm_team_1_b_row_<?php echo $PostId; ?> .wpsm_team_1_member_wrapper_inner h3{
				color:<?php echo $team_mb_name_clr; ?> !important;
				font-size:<?php echo $team_mb_name_ft_size; ?>px !important;
				font-family:<?php echo $font_family; ?> !important;
			}
			#wpsm_team_1_b_row_<?php echo $PostId; ?> .wpsm_team_1_name_divider{
				background-color:<?php echo $team_mb_name_clr; ?> !important;
			}
			#wpsm_team_1_b_row_<?php echo $PostId; ?> .wpsm_team_1_b_desig{
				color:<?php echo $team_mb_pos_clr; ?> !important;
				font-size:<?php echo $team_mb_pos_ft_size; ?>px !important;
				font-family:<?php echo $font_family; ?> !important;
			}
			#wpsm_team_1_b_row_<?php echo $PostId; ?> .wpsm_team_1_b_desc{
				color:<?php echo $team_mb_desc_clr; ?> !important;
				font-size:<?php echo $team_mb_desc_ft_size; ?>px !important;
				font-family:<?php echo $font_family; ?> !important;
			}
			#wpsm_team_1_b_row_<?php echo $PostId; ?> .wpsm_team_1_social_div a i{
				color:<?php echo $team_mb_social_icon_clr; ?> !important;
				background:<?php echo $team_mb_social_icon_clr_bg; ?> !important;
			}
			<?php echo $custom_css; ?>			
			</style>
			<?php 
			if($TotalCount!=-1)
			{	
				$i=1;
				switch($team_layout){
					case(6):
						$row=2;
					break;
					case(4):
						$row=3;
					break;
					case(3):
						$row=4;
					break;
				}
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
					<div class="col-md-<?php echo $team_layout; ?> wpsm-col-div wpsm_single_team">
						<div class="wpsm_team_1_member_wrapper">
							<img class="img-responsive wpsm_team_1_mem_img" src="<?php echo $mb_photo; ?>" alt="<?php echo $mb_name; ?>">
							<div class="wpsm_team_1_member_wrapper_inner">
								<h3>
									<?php echo $mb_name; ?>
									<div class="wpsm_team_1_name_divider"></div>
								</h3>
								<?php if($mb_pos!="") { ?><span class="wpsm_team_1_b_desig"> <?php echo $mb_pos; ?> </span> <?php } ?>
								<?php if($mb_desc!="") { ?><p class="wpsm_team_1_b_desc"> <?php echo $mb_desc; ?> </p> <?php } ?>
								<div class="wpsm_team_1_social_div">
									<?php if($mb_fb_url!="") { ?><a href="<?php echo $mb_fb_url; ?>" target="_blnk" title="facebook profile"><i class="fa fa-facebook"></i></a> <?php } ?>
									<?php if($mb_twit_url!="") { ?><a href="<?php echo $mb_twit_url; ?>" target="_blnk" title="twitter profile"><i class="fa fa-twitter"></i></a><?php } ?>
									<?php if($mb_lnkd_url!="") { ?><a href="<?php echo $mb_lnkd_url; ?>" target="_blnk" title="linkedin profile"><i class="fa fa-linkedin"></i></a><?php } ?>
									<?php if($mb_gp_url!="") { ?><a href="<?php echo $mb_gp_url; ?>" target="_blnk" title="google plus profile"><i class="fa fa-google-plus"></i></a><?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php
					if($i%$row==0){
						?>
						</div>
						<div class="wpsm_row">
						<?php 
					}	
					?>
					<?php 
					 $i++;
				}
				
			}
			else
			{
				echo "No Team Group Found";
			}
		
			?>		
		</div>
	</div>