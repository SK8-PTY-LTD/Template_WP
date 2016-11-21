<div>
	Label: <input id="" class="input-width-static js-social-btn-text btnLabel" data-social-button="facebook" type='text' name="labelfacebook" 
	<?php if( @$data['buttonOptions']['facebook']['label'] == ''): ?>
		value="Share" >
	<?php else: ?>
		value="<?php echo esc_attr(@$data['buttonOptions']['facebook']['label']); ?>" >
	<?php endif;?>
</div>


