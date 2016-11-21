<div>
	Label: <input id="" class="input-width-static js-social-btn-text btnLabel" data-social-button="linkedin" type='text' name="labellinkedin" 
	<?php if( @$data['buttonOptions']['linkedin']['label'] == ''): ?>
		value="Share" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['linkedin']['label']); ?>" >
	<?php endif;?>
</div>