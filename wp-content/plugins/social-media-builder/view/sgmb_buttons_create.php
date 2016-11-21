<?php if (!defined( 'ABSPATH' )) exit; ?>
<form method="POST" action="<?php echo admin_url();?>admin-post.php" id="add-form">
<input type="hidden" name="action" value="save_button">
<input type="hidden"  class="select-button" name="button" value="">
<input type="hidden" class="button-primary" value="<?php echo esc_attr(@$_GET['id']); ?>" name="hidden_button_id" />
<?php wp_nonce_field('save_button', 'wp-nonce-token'); ?>
<div class="sgmb-container">
	<?php if (isset($_GET['saved']) && $_GET['saved']==1): ?>
		<div id="default-message" class="updated notice notice-success is-dismissible " ><p>Changes were saved</p></div>
	<?php endif; ?>


	<div class="create-title">
		<div >
			<h1 class="title-crud"> Create new social buttons </h1>
		</div>
		<div class="save-button">
			<p>
			<input type="submit" id="sgmb-save-button" class="button-primary"  value="Save Changes">
			</p>
		</div>
	</div>
	<div id="titlediv" class="title-input">
		<div id="titlewrap">
			<label class="screen-reader-text" id="title-prompt-text" for="title">Enter title here</label>
			<input type="text" name="post_title" size="30" value="<?php echo esc_attr(@$data['title']) ?>" id="title" required="required" spellcheck="true" autocomplete="off" placeholder="Enter title here" />
		</div>
		<div class="inside">
			<div id="edit-slug-box" class="hide-if-no-js">
			</div>
		</div>
	</div>
	<div>
	<div class="wrapper">
		<div class="sgmbLivePreview" id="sgmbLivePreview">
			<h1>Live preview</h1>
			<div class="sgmbWidget<?php echo esc_attr(@$data['id']); ?>-1">
				<div class="sgmbShare" id="sgmbShare<?php echo esc_attr(@$data['id']); ?>-1"></div>
			</div>
			<?php if($data): ?>
			<div class="conteiner-shortcode-inside-livePreview">
				<span class="shortcode-title-inside-livePreview">Shortcode: </span>
				<span class="sgmb-shortcode">[sgmb id=<?php echo esc_attr(@$data['id']); ?>] </span>
			</div>
			<?php endif;?>
			<div class="dropdownWrapper dropdownWrapper<?php echo esc_attr(@$data['id']); ?> dropdownWrapper-for-livePreview ">
				<div class="dropdownLabel"><span class="sgmbButtonListLabel<?php echo esc_attr(@$data['id']); ?>">Share List</span> </div>
				<div class="dropdownPanel  dropdownPanel<?php echo esc_attr(@$data['id']); ?>-1">
				</div>
			</div>
		</div>

		<?php global $SGMB_BUTTON_FONT_SIZE, $SGMB_WIDGET_THEMES, $SGMB_SOCIAL_BUTTONS, $SGMB_WIDGET_EFFECTS, $SGMB_ADVANCED_NAME_SOCIAL_BUTTONS, $SGMB_FONT_SIZE_FOR_SHARE_LIST; ?>
		<div class="sgmb-tabs" id="sgmb-tabs">
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">General</a></li>
					<li><a href="#tabs-2">Visual</a></li>
					<li><a href="#tabs-3">Themes</a></li>
					<li><a href="#tabs-4">Effects</a></li>
					<li><a href="#tabs-5">Google Analitics <span class="pro-label-analytics-title">*Pro</span></a></li>
				</ul>
				<div id="tabs-1">
					<div class="ui-widget ui-helper-clearfix">
						<div  class="ui-widget-content buttons-content">
							<ul id="gallery" class="gallery ui-helper-reset ui-helper-clearfix">
								<?php foreach ($SGMB_SOCIAL_BUTTONS as $value): ?>
								<li id="<?php echo esc_attr($value); ?>" data-social-button="<?php echo esc_attr($value); ?>" class="ui-widget-content ui-corner-tr js-social-btn js-social-btn-status">
									<?php if(SGMB_PRO != 1): ?>
										<?php if($value == 'fbLike' || $value == 'twitterFollow' || $value == 'whatsapp' || $value == 'tumblr' || $value == 'reddit'  || $value == 'line' || $value == 'vk' || $value == 'stumbleupon'): ?>
											<img src="<?php echo SGMB_URL."/img/$value".'Pro'.".png"; ?>" class="img-for-drag">
										<?php else: ?>
											<img src="<?php echo SGMB_URL."/img/$value.png"; ?>" class="img-for-drag">
										<?php endif; ?>
									<?php else: ?>
										<img src="<?php echo SGMB_URL."/img/$value.png"; ?>" class="img-for-drag">
									<?php endif; ?>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div>
							<span class="label-dragOverToAdd">Drag over to add</span>
							<img src="<?php echo SGMB_URL."/img/arrow.png"; ?>"  class="img-for-arrow">
						</div>
						<div id="trash" class="ui-widget-content ui-state-default buttons-content ">
						</div>
					</div>
					<div class = "sgmb-share-url">
						<h4>Share the following url:</h4>
						<input type="radio" name="currentUrl" value="1" <?php if( @$data['options']['currentUrl'] != '0' ): ?> checked  <?php endif; ?> >Current url<br>
						<input type="radio"   value="0" name="currentUrl" <?php if(@$data['options']['currentUrl'] == '0'): ?> checked  <?php endif; ?> >
						<span class="sgmb-label-url">Url:</span>
						<input id="inputUrl" name="url" class="js-url-input" type="text" value="<?php echo @$data['options']['url'] ?>">
						<div class="div-share-text">
							<span class="sgmb-label-share-text">Your share text:</span>
							<input id="shareText" name="shareText" class="js-share-text-input" type="text" value="<?php echo @$data['options']['shareText'] ?>">
						</div>
					</div>
					<div id="accordion">
						<?php foreach ($SGMB_SOCIAL_BUTTONS as $value): ?>
							<h3 class="<?php echo esc_attr($value); ?>"><?php echo @$SGMB_ADVANCED_NAME_SOCIAL_BUTTONS[$value] ?></h3>
							<div class="<?php echo esc_attr($value); ?>">
								<p>
								<?php
									$sgmbAddNewSection = new SgmbAddNewSection();
									$sgmbAddNewSection->renderOptions($value);
								?>
								</p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div id="tabs-2">
					<div>
						<?php  $fieldName = "sgmbSocialButtonSize";?>
						<span class="sgmb-label-checkbox">Font size:</span>
						<?php echo SgmbAddNewSection::createSelect($fieldName,$SGMB_BUTTON_FONT_SIZE,@$data,'fontSize'); ?>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Use round buttons:</span>
						<input type="checkbox" name="roundButton" id="checkbox-round-buttons"
						<?php if(@$data['options']['roundButton'] == 'on'): ?>
						checked
					 	<?php endif; ?>>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Show labels:</span>
						<?php $checked = "checked";?>
						<?php
							if(isset($data['options']['showLabels']) && $data['options']['showLabels'] == '') {
					 			$checked = "";
							}
					 	?>
					 	<input type="checkbox" id="checkbox-show-labels"name="showLabels" <?php echo $checked ?> >
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Show counts:</span>
						<input type="checkbox" name="showCounts" id="checkbox-show-counts"
						<?php if(@$data['options']['showCounts'] == 'on'): ?>
							checked
					 	<?php endif; ?>>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Center Buttons:</span>
						<input type="checkbox" name="showCenter" id="checkbox-show-counts"
						<?php if(@$data['options']['showCenter'] == 'on'): ?>
							checked
						<?php endif; ?>>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Space between buttons:</span>
						<input class="sgmb-betweenButtons"  type='text' name="betweenButtons"
							<?php if( @$data['options']['betweenButtons'] == ''): ?>
								value="1px"
							<?php else: ?>
								value="<?php echo esc_attr(@$data['options']['betweenButtons']); ?>"
							<?php endif;?>
						>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Toggle dropdown to show buttons:</span>
						<input type="checkbox" name="showButtonsAsList" id="checkbox-show-widget-in-dropdown"
						<?php if(@$data['options']['showButtonsAsList'] == 'on'): ?>
						checked
					 	<?php endif; ?>>
					</div>
					<div class="sgmb-dropdown-color sgmb-dropdown-advance-options">
						<span class="sgmb-changeColor-label">Change button color:</span>
						<div id="color-picker" class="div-color-picker">
							<?php if(SGMB_PRO != 1): ?>
								<div class="div-for-free-color-picker"></div>
							<?php endif;?>
							<input  class="sgmbDropdownColor" id="sgmbDropdownColor" type="text" name="sgmbDropdownColor" value="<?php echo esc_attr(@$data['options']['sgmbDropdownColor']) ?>" />
						</div>
						<?php if(SGMB_PRO != 1): ?>
							<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" class="sgmb-pro-label-for-visual">PRO</a>
						<?php endif;?>
					</div>
					<div class="sgmb-dropdown-label-color sgmb-dropdown-advance-options" disabled='true'>
						<span class="sgmb-changeColor-label">Change label color:</span>
						<div id="color-picker" class="div-color-picker">
							<?php if(SGMB_PRO != 1): ?>
								<div class="div-for-free-color-picker"></div>
							<?php endif;?>
							<input  class="sgmbDropdownLabelColor" id="sgmbDropdownLabelColor" type="text"  name="sgmbDropdownLabelColor" value="<?php echo esc_attr(@$data['options']['sgmbDropdownLabelColor']) ?>" />
						</div>
						<?php if(SGMB_PRO != 1): ?>
							<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" class="sgmb-pro-label-for-visual">PRO</a>
						<?php endif;?>
					</div>
					<div class="sgmb-dropdown-advance-options">
						<?php  $fieldName = "sgmbDropdownLabelFontSize";?>
						<span class="sgmb-label-checkbox">Label font size:</span>
						<?php echo SgmbAddNewSection::createSelect($fieldName, $SGMB_FONT_SIZE_FOR_SHARE_LIST, @$data, 'sgmbDropdownLabelFontSize'); ?>
					</div>
					<div class="sgmb-checkbox">
						<span class="sgmb-label-checkbox">Show social media on every post:</span>
						<?php if(@$data['id'] != get_option( 'SGMB_SHARE_BUTTON_ID' )) { @$data['options']['showButtonsOnEveryPost'] = ''; } ?>
						<input type="checkbox" name="showButtonsOnEveryPost"
						<?php if(@$data['options']['showButtonsOnEveryPost'] == 'on'): ?>
							checked
					 	<?php endif; ?>>
					</div>
					<div class="showEveryPostOptions">
						<div class="sgmb-checkbox">
							<span class="sgmb-label-checkbox">Show buttons before post contents:</span>
							<input type="checkbox" name="showButtonsBeforePostContents"
							<?php if(SGMB_PRO != 1): ?>
								disabled>
								<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" class="sgmb-pro-label-for-visual" target="_blanck">PRO</a>
							<?php else: ?>
								>
							<?php endif; ?>
						</div>
						<div class="sgmb-checkbox">
							<span class="sgmb-label-checkbox">Text before the sharing buttons:</span>
							<input class="sgmb-textOnEveryPost"  type='text' name="textOnEveryPost" value="<?php echo esc_attr(@$data['options']['textOnEveryPost']); ?>" >
						</div>
						<div class="sgmb-selctor-position-every-post">
							<span class="sgmb-label-checkbox">Social media position:</span>
							<?php $sgmbPostionOnEveryPost = 'sgmbPostionOnEveryPost'; $sgmbPostion = array('Left', 'Center', 'Right');
								echo SgmbAddNewSection::createSelect($sgmbPostionOnEveryPost, $sgmbPostion, @$data, $sgmbPostionOnEveryPost); ?>
						</div>
						<div class="sgmb-checkbox">
							<span class="sgmb-label-checkbox">Show on all posts:</span>
							<?php if(@$data['id'] != get_option( 'SGMB_SHARE_BUTTON_ID' )) { @$data['options']['showOnAllPost'] = ''; } ?>
							<input type="checkbox" name="showOnAllPost"
							<?php if(@$data['options']['showOnAllPost'] == 'on'): ?>
								checked
							<?php endif; ?>>
						</div>
						<div class="sgmb-selctor-position-every-post sgmb-select-posts">
							<span class="sgmb-label-checkbox">Select Posts:</span>
							<input type="hidden" class="sgmb-all-selected-post" name="sgmb-all-selected-post" value="">
							<?php
								$classes = 'selectOption';
								$args = array('posts_per_page' => -1); // Set to -1 to remove the limit, default 5
								$posts = get_posts($args);
								foreach ($posts as $post) {
									$selectedKeys[$post->ID] = $post->post_title;
								}
								echo SgmbAddNewSection::createMultiSelect('sgmbSelectedPosts', $selectedKeys, @$data, $classes);
							?>
						</div>
					</div>
					<div class="sgmb-pro-wrapper">
						<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" class="sgmb-pro-label">Upgrade Pro Now</a>
						<small class="sgmb-pro-notice">* In the picture bellow is shown what you'll get in Pro</small>
					</div>
					<img src="<?php echo SGMB_URL."/img/proFeatures.png"?>">
				</div>
				<div id="tabs-3" >
					<div>
						<?php foreach ($SGMB_WIDGET_THEMES as $theme => $sgmbIsPro): ?>
							<div>
								<div class = "theme-selector">
									<input  id="<?php echo "theme-"."$theme"."-radio" ?>" type="radio" value="<?php echo esc_attr($theme); ?>" name="theme"
									<?php if(empty($data['options']['theme']) && $theme =='classic'): ?>
										checked
									<?php endif; ?>
									<?php if( @$data['options']['theme'] ==  $theme ): ?>
										checked
									<?php endif; ?>
									<?php if($sgmbIsPro == 1 && SGMB_PRO != 1): ?>
										disabled
									<?php endif;?>>
								</div>
								<div class= "theme-selector">
									<?php foreach ($SGMB_SOCIAL_BUTTONS as $value): ?>
										<?php if($value != 'fbLike' && $value != 'twitterFollow'): ?>
											<img src="<?php echo SGMB_URL."/img/"."$theme"."$value".".png"?>" class="img-for-theme">
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
								<div class= "theme-selector">
									<?php if($sgmbIsPro == 1 && SGMB_PRO != 1): ?>
										<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" class="sgmb-pro-label">PRO</a>
									<?php endif;?>
								</div>
							</div>
						<?php endforeach; ?>
						<input name = "socialTheme" type="hidden" value="<?php echo esc_attr(@$data['options']['socialTheme']); ?>">
						<input name = "logo" type="hidden" value="<?php echo esc_attr(@$data['options']['icon']); ?>">
					</div>
				</div>
				<div id="tabs-4" >
					<?php $buttonsPanelEffect = "buttonsPanelEffect";$buttonsEffect = "buttonsEffect";$iconsEffect = "iconsEffect";?>
					<div class="sgmbEffectsContent">
						<span class="sgmbEffectsLabel">Panel effect type:</span>
						<?php echo SgmbAddNewSection::createSelect($buttonsPanelEffect, $SGMB_WIDGET_EFFECTS, @$data, $buttonsPanelEffect, '', false); ?>
					</div>
					<div class="sgmbEffectsContent">
						<span class="sgmbEffectsLabel">Buttons hover effects:</span>
						<?php echo SgmbAddNewSection::createSelect($buttonsEffect, $SGMB_WIDGET_EFFECTS, @$data, 'buttonsEffect'); ?>
					</div>
					<div class="sgmbEffectsContent">
						<span class="sgmbEffectsLabel">Icons hover effects:</span>
						<?php echo SgmbAddNewSection::createSelect($iconsEffect, $SGMB_WIDGET_EFFECTS, @$data, 'iconsEffect'); ?>
					</div>
				</div>
				<div id="tabs-5">
					<div class="sgmb-title-analytics">
						<span>Why and How to use</span>
					</div>
					<div class="sgmb-text-about-analytics">
						<span>This tool will help you to track the number of clicks on your social buttons from every page they are on.So you will know how many times your buttons are clicked.</span>
					</div>
					<div class="sgmb-text-about-analytics">
						<span>
						The only thing you need to do is to create an account in  <a href="https://analytics.google.com" target="_blank">Google Analytics</a>. After, simply insert the account number into the <a href="https://support.google.com/analytics/answer/1032385?hl=en" target="_blank">"Set Account Number" </a> field.
						<span>
					</div>
					<div class="">
						<span class="sgmb-analytics-options-label">Set Account Number:</span>
						<input class="" disabled="disabled" type='text' name="googleAnaliticsAccount" value="<?php echo esc_attr(@$data['options']['googleAnaliticsAccount']); ?>" >
					</div>
					<div class="sgmb-text-about-analytics">
						<span>
						As a result, you will have the statistics of your social buttons clicks on Google Analytics, like in the picture below.
						<span>
					</div>
					<div class="sgmb-text-about-analytics">
						<img src="<?php echo SGMB_URL."/img/analytics.png"; ?>" class="sgmb-image-analytics">
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
</form>
<script>
jQuery(document).ready(function($){
	var data = <?php echo json_encode($data); ?>;
	var sgmbIsPro = <?php echo SGMB_PRO; ?>;
	sgmb = new SGMB();
	sgmb.init(data, sgmbIsPro);
	var widget = new SGMBWidget();
	widget.setShareUrl("<?php echo esc_url(SGMB_DEFAULT_SHARE_URL); ?>");
	var lp = sgmb.getLivePreview();
	lp.setWidget(widget);
	widget.show(data, 1, '1');
});
SGMB_URL = "<?php echo esc_url(SGMB_URL); ?>";
</script>
