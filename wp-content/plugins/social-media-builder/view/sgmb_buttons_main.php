<?php if (!defined( 'ABSPATH' )) exit; ?>
<?php $sgmbReview = get_option('SGMB_MEDIA_REVIEW_PANEL');?>
<div class="wrap">
<div class="headers-wrapper">
	<?php if(empty($sgmbReview)): ?>
		<div class="updated updated notice is-dismissible reviewPanel">
			<div class="reviewPanelContent">
				<span class="reviewPanelSpan">
					Dear user, we always do our best to help you and your opinion is very important for us!
				</span></br>
				<span class="reviewPanelSpan">
					So if you liked our <b>Social Media Plugin</b> and if our support was helpful for you, we will be thankful if you go ahead and leave a review.
				</span>
				<span class="reviewPanelSpan">
					Please <a class="reviewPanelHref" href="https://wordpress.org/support/view/plugin-reviews/social-media-builder?filter=5" target="_blank">rate it 5 stars.</a>
				</span>
			</div>
				<span class="reviewPanelClose">Dont show again</span>
				<button type="button" class="notice-dismiss closeButton"></button>
		</div>
	<?php endif; ?>
	<h1 class="h1-for-headers-wrapper">Social Buttons <a href="<?php echo admin_url();?>admin.php?page=create-button" class="add-new-h2">Add New</a>
		<?php if(SGMB_PRO != 1): ?>
			<input type="button" class="sgmbUpgrateProButton" value="Upgrade to PRO version" onclick="window.open('http://plugins.sygnoos.com/wordpress-social-buttons/')">
		<?php endif; ?>
	</h1>
</div>
<?php
	echo $this->table;
?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".reviewPanelClose").on({
			click: function() {
				var data = {
					action: 'close_review_panel',
				}
				$.post(ajaxurl, data, function(response,d) {

				});
				$( ".reviewPanel" ).hide(300);
			}
		});
	});
</script>
