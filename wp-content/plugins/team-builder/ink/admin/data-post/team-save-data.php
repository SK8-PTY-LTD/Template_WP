<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($PostID) && isset($_POST['team_b_save_data_action']) ) {
			$TotalCount = count($_POST['mb_name']);
			$All_data = array();
			if($TotalCount) {
				for($i=0; $i < $TotalCount; $i++) {
					$mb_photo = stripslashes(sanitize_text_field($_POST['mb_photo'][$i]));
					$mb_name = stripslashes(sanitize_text_field($_POST['mb_name'][$i]));
					$mb_pos = stripslashes(sanitize_text_field($_POST['mb_pos'][$i]));
					$mb_desc = stripslashes($_POST['mb_desc'][$i]);
					$mb_fb_url = sanitize_text_field($_POST['mb_fb_url'][$i]);
					$mb_twit_url = sanitize_text_field($_POST['mb_twit_url'][$i]);
					$mb_lnkd_url = sanitize_text_field($_POST['mb_lnkd_url'][$i]);
					$mb_gp_url = sanitize_text_field($_POST['mb_gp_url'][$i]);
					$All_data[] = array(
						'mb_photo' => $mb_photo,
						'mb_name' => $mb_name,
						'mb_pos' => $mb_pos,
						'mb_desc' => $mb_desc,
						'mb_fb_url' => $mb_fb_url,
						'mb_twit_url' => $mb_twit_url,
						'mb_lnkd_url' => $mb_lnkd_url,
						'mb_gp_url' => $mb_gp_url,
					);
				}
				update_post_meta($PostID, 'wpsm_team_b_data', serialize($All_data));
				update_post_meta($PostID, 'wpsm_team_b_count', $TotalCount);
			} else {
				$TotalCount = -1;
				update_post_meta($PostID, 'wpsm_team_b_count', $TotalCount);
				$All_data = array();
				update_post_meta($PostID, 'wpsm_team_b_data', serialize($All_data));
			}
		}
 ?>