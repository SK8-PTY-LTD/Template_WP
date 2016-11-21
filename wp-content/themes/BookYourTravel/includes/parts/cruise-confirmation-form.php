<?php 
global $enable_extra_items, $booking_form_fields, $bookyourtravel_theme_of_custom;
do_action( 'bookyourtravel_show_cruise_confirm_form_before' ); ?>
<form id="cruise-confirmation-form" method="post" action="<?php echo BookYourTravel_Theme_Utils::get_current_page_url(); ?>" class="booking" style="display:none">
	<fieldset>
		<h3><span>02 </span><?php esc_html_e('Confirmation', 'bookyourtravel') ?></h3>
		<div class="text-wrap">
			<p><?php esc_html_e('Thank you. We will get back you with regards your cruise booking within 24 hours.', 'bookyourtravel') ?></p>
		</div>				
		<h3><?php esc_html_e('Traveller info', 'bookyourtravel') ?></h3>
		<?php 	
			foreach ($booking_form_fields as $booking_field) {
				$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
				$field_id = $booking_field['id'];
				$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
				$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);			

				if (!$field_hidden) {
				?>
			<div class="row">
				<div class="output one-half">
					<p><?php echo esc_html($field_label); ?>: 
						<strong class="confirm_<?php echo esc_attr($field_id); ?>_p"></strong>
					</p>
				</div>
			</div>
		<?php 	} 
			}
		?>
		<div class="row">	
			<div class="output one-half">
				<p><?php esc_html_e('Cruise', 'bookyourtravel') ?>: 
					<strong class="confirm_cruise_title_p"></strong>
				</p>
			</div>
			<div class="output one-half">
				<p><?php esc_html_e('Cruise date', 'bookyourtravel') ?>: 
					<strong class="confirm_cruise_date_p"></strong>
				</p>
			</div>
			<div class="output one-half">
				<p><?php esc_html_e('Adults', 'bookyourtravel') ?>: 
					<strong class="confirm_adults_p"></strong>
				</p>
			</div>
			<div class="output one-half">
				<p><?php esc_html_e('Children', 'bookyourtravel') ?>: 
					<strong class="confirm_children_p"></strong>
				</p>
			</div>
			<?php if ($enable_extra_items) { ?>
			<div class="output one-half">
				<p><?php esc_html_e('Reservation total', 'bookyourtravel') ?>: 
					<strong class="confirm_reservation_total_p"></strong>
				</p>
			</div>
			<div class="output one-half">
				<p><?php esc_html_e('Extra items total', 'bookyourtravel') ?>: 
					<strong class="confirm_extra_items_total_p"></strong>
				</p>
			</div>
			<?php } ?>
			<div class="output one-half">
				<p><?php esc_html_e('Total price', 'bookyourtravel') ?>: 
					<strong class="confirm_total_price_p"></strong>
				</p>
			</div>
		</div>
		<div class="text-wrap">
			<p><?php echo sprintf(__('<strong>We wish you a pleasant cruise</strong><br /><i>your %s team</i>', 'bookyourtravel'), of_get_option('contact_company_name', 'BookYourTravel')) ?></p>
		</div>
	</fieldset>
</form>
<?php do_action( 'bookyourtravel_show_cruise_confirm_form_after' );