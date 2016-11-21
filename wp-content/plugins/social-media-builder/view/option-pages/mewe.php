<div>
	Label: <input id="" class="input-width-static js-social-btn-text btnLabel" data-social-button="mewe" type='text' name="labelmewe"
	<?php if( @$data['buttonOptions']['mewe']['label'] == ''): ?>
		value="Share" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['mewe']['label']); ?>" >
	<?php endif;?>
</div>
