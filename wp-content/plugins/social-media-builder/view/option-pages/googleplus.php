<div>
	Label: <input id="" class="input-width-static js-social-btn-text btnLabel" data-social-button="googleplus" type='text' name="labelgoogleplus"
	<?php if( @$data['buttonOptions']['googleplus']['label'] == ''): ?>
		value="+1" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['googleplus']['label']); ?>" >
	<?php endif;?>
</div>