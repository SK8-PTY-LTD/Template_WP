<div>
	<div class="sgmb-options-for-twitter">
		<span class="sgmb-label-forTweetOptions">Label:</span>
		<input id="" class="input-width-static js-social-btn-text sgmb-input-forTweetOptions" data-social-button="twitter" type='text' name="labeltwitter"
		<?php if( @$data['buttonOptions']['twitter']['label'] == ''): ?>
			value="Tweet" >
		<?php else: ?>
			value="<?php echo @$data['buttonOptions']['twitter']['label'] ?>" >
		<?php endif;?>
	</div>
	<div class="sgmb-options-for-twitter">
		<span class="sgmb-label-forTweetOptions">Hashtags:</span>
		<input id="" class="input-width-static sgmb-input-forTweetOptions" data-social-button="twitter" type='text' name="hashtags" value="<?php echo @$data['buttonOptions']['twitter']['hashtags'] ?>">
	</div>
	<div class="sgmb-options-for-twitter">
		<span class="sgmb-label-forTweetOptions">Via:</span> 
		<input id="" class="input-width-static sgmb-input-forTweetOptions" data-social-button="twitter" type='text' name="via" value="<?php echo @$data['buttonOptions']['twitter']['via'] ?>">
	</div>
</div>