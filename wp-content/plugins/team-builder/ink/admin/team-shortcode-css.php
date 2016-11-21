<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
			#team_b_shortcode{
			background:#fff!important;
			box-shadow: 0 0 20px rgba(0,0,0,.2);
			}
			#team_b_shortcode .hndle , #team_b_shortcode .handlediv{
			display:none;
			}
			#team_b_shortcode p{
			color:#000;
			font-size:15px;
			}
			#team_b_shortcode input {
			font-size: 16px;
			padding: 8px 10px;
			width:100%;
			}
			
		</style>
		<h3>Team Shortcode</h3>
		<p><?php _e("Use below shortcode in any Page/Post to publish your Teams", wpshopmart_team_b_text_domain);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[TEAM_B id=".get_the_ID()."]"; ?>">
		<?php
		 $PostId = get_the_ID();
		$Settings = unserialize(get_post_meta( $PostId, 'Team_B_Settings', true));
		if(isset($Settings['custom_css'])){  
		     $custom_css   = $Settings['custom_css'];
		}
		else{
		$custom_css="";
		}		
		?>
		
		<br /><br />
			
		<h3>Custom Css</h3>
		<textarea name="custom_css" id="custom_css" style="width:100% !important ;height:300px;background:#ECECEC;"><?php echo $custom_css ; ?></textarea>
		<p>Enter Css without <strong>&lt;style&gt; &lt;/style&gt; </strong> tag</p>
		<br>
		
		<?php if(isset($Settings['custom_css'])){ ?> 
		<h3>Add This Team Group settings as default setting for new team Group</h3>
		<div class="">
			<a  class="button button-primary button-hero" name="updte_wpsm_team_b_default_settings" id="updte_wpsm_team_b_default_settings" onclick="wpsm_update_default()">Update Default Settings</a>
		</div>	
		<?php } ?>
		<script>
		jQuery(function() {
		// Target a single one
		  jQuery("#custom_css").linedtextarea();

		});
		</script>