<div>
	Label: <input id=""  class="input-width-static js-social-btn-text btnLabel" data-social-button="email" type='text' name="labelemail" 
	<?php if( @$data['buttonOptions']['email']['label'] == ''): ?>
		value="E-mail" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['email']['label']); ?>" >
	<?php endif;?>
</div>