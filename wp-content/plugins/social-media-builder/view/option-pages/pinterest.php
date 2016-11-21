<div>
	Label: <input id="" class="input-width-static js-social-btn-text btnLabel" data-social-button="pinterest" type='text' name="labelpinterest"
	<?php if( @$data['buttonOptions']['pinterest']['label'] == ''): ?>
		value="Pin this" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['pinterest']['label']); ?>" >
	<?php endif;?>
</div>