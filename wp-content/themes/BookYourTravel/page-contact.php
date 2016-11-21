<?php
/*	Template Name: Contact
 * The template for displaying the contact page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');	

global $bookyourtravel_theme_globals;
$enc_key = $bookyourtravel_theme_globals->get_enc_key();

$contact_phone_number = $bookyourtravel_theme_globals->get_contact_phone_number();
$business_contact_email = $bookyourtravel_theme_globals->get_contact_email();
$add_captcha_to_forms = $bookyourtravel_theme_globals->add_captcha_to_forms();
$business_address_longitude = $bookyourtravel_theme_globals->get_business_address_longitude();
$business_address_latitude = $bookyourtravel_theme_globals->get_business_address_latitude();

$form_submitted = false;
$contact_error = '';
$contact_message = '';
$contact_email = '';
$contact_name = '';

if(isset($_POST['contact_submit'])) {

	$form_submitted = true;	
	if ( empty($_POST) || !wp_verify_nonce($_POST['_wpnonce'], 'bookyourtravel_nonce') )
	{
	   // failed to verify nonce so exit.
	   exit;
	}
	else
	{
		// process form data since nonce was verified	   
		$contact_message = sanitize_text_field($_POST['contact_message']);
		$contact_email = sanitize_text_field($_POST['contact_email']);
		$contact_name = sanitize_text_field($_POST['contact_name']);
		
		$c_val_s = intval(wp_kses($_POST['c_val_s'], array()));
		$c_val_1 = intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_POST['c_val_1'], array()), $enc_key));
		$c_val_2 = intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_POST['c_val_2'], array()), $enc_key));
		
		if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {			
			
			$contact_error = esc_html__('Invalid captcha, please try again!', 'bookyourtravel');			
			
		} else if (!empty($contact_name) && !empty($contact_email) && !empty($contact_message)) {				
			
			$email_to = get_option('admin_email');
			
			// if (!empty($business_contact_email))
			//	$email_to = $business_contact_email;
			
			$subject = sprintf(esc_html__('Contact form submission from %s', 'bookyourtravel'), $contact_name);
			$body = sprintf(__('Name: %s', 'bookyourtravel'), $contact_name);
			$body .= "\n\n";
			$body .= sprintf(__('Email: %s', 'bookyourtravel'), $contact_email);
			$body .= "\n\n";
			$body .= sprintf(__('Message: %s', 'bookyourtravel'), $contact_message);
			$body .= "\n\n";
			
			// $headers_array   = array();
			// $headers_array[] = "MIME-Version: 1.0";
			// $headers_array[] = "Content-type: text/plain; charset=utf-8";
			// $headers_array[] = "From: " . $contact_name . " <" . $contact_email . ">";
			// $headers_array[] = "Reply-To: " . $contact_name . " <" . $contact_email . ">";
			// $headers_array[] = "X-Mailer: PHP/".phpversion();
			
			// $headers = implode( "\r\n", $headers_array );
			
			$headers = "Content-Type: text/html; charset=utf-8\r\n";
			$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";									
			
			if (!empty($contact_email)) {
				$headers .= "Reply-To: " . $contact_email . " <" . $contact_email . ">\r\n";
			} else {
				$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";						
			}				
			
			$ret = wp_mail($email_to, $subject, $body, $headers, "");
			if (!$ret) {
				global $phpmailer;
				if (isset($phpmailer) && WP_DEBUG) {
					var_dump($phpmailer->ErrorInfo);
				}
			}
		} else {
			$contact_error = esc_html__('To submit contact form, please enable JavaScript', 'bookyourtravel');
		}
	}
} 

$c_val_1 = mt_rand(1, 20);
$c_val_2 = mt_rand(1, 20);

$c_val_1_str = BookYourTravel_Theme_Utils::encrypt($c_val_1, $enc_key);
$c_val_2_str = BookYourTravel_Theme_Utils::encrypt($c_val_2, $enc_key);
?>
<div class="row">
<?php
	if ( have_posts() ) while ( have_posts() ) : the_post(); ?>	
	<!--three-fourth content-->
	<section class="three-fourth">
		<h1><?php the_title(); ?></h1>
		<?php 
		$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();
		$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
		if (!empty($google_maps_key)) {
			if (!empty($business_address_longitude) && !empty($business_address_latitude)) { ?>
		<!--map-->
		<div class="map-wrap">
			<div class="gmap" id="map_canvas"></div>
		</div>
		<!--//map-->
		<?php } 
		} else {?>
		<p><?php echo wp_kses(__('Before using google maps you must go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google maps api console</a> and get an api key. After you do, please proceed to Appearance -> Theme options -> Configuration settings and enter your key in the field labeled "Google maps api key"', 'bookyourtravel'), $allowed_tags); ?></p>
		<?php } ?>
	</section>	
	<!--three-fourth content-->	
	<!--sidebar-->
	<aside class="right-sidebar lower  one-fourth">
		<!--contact form-->
		<div class="widget">
			<h4><?php esc_html_e('Send us a message', 'bookyourtravel'); ?></h4>
			<?php 
			if ($form_submitted) { ?>
			<p>
			<?php
				if (!empty($contact_error)) {
					echo $contact_error;
				} else {
					esc_html_e('Thank you for contacting us. We will get back to you as soon as we can.', 'bookyourtravel');
				} ?>
			</p>
			<?php
			}
			if (!$form_submitted || !empty($contact_error)) { ?>
			<form action="<?php echo esc_url(BookYourTravel_Theme_Utils::get_current_page_url()); ?>" id="contact-form" method="post">
				<fieldset class="row">
					<div class="f-item full-width">
						<label for="contact_name"><?php esc_html_e('Your name', 'bookyourtravel'); ?></label>
						<input type="text" id="contact_name" name="contact_name" required="required" value="<?php echo esc_attr($contact_name); ?>" />
					</div>
					<div class="f-item full-width">
						<label for="contact_email"><?php esc_html_e('Your e-mail', 'bookyourtravel'); ?></label>
						<input type="email" id="contact_email" name="contact_email" required="required" value="<?php echo esc_attr($contact_email); ?>" />
					</div>
					<div class="f-item full-width">
						<label for="contact_message"><?php esc_html_e('Your message', 'bookyourtravel'); ?></label>
						<textarea name="contact_message" id="contact_message" rows="10" cols="10" required="required"><?php echo esc_attr($contact_message); ?></textarea>
					</div>
					<?php if ($add_captcha_to_forms) { ?>
					<div class="f-item captcha full-width">
						<label><?php echo sprintf(esc_html__('How much is %d + %d', 'bookyourtravel'), $c_val_1, $c_val_2) ?>?</label>
						<input type="text" required="required" id="c_val_s" name="c_val_s" />
						<input type="hidden" name="c_val_1" id="c_val_1" value="<?php echo esc_attr($c_val_1_str); ?>" />
						<input type="hidden" name="c_val_2" id="c_val_2" value="<?php echo esc_attr($c_val_2_str); ?>" />
					</div>
					<?php } ?>
					<?php wp_nonce_field('bookyourtravel_nonce'); ?>
					<div class="f-item full-width"><input type="submit" value="<?php esc_attr_e('Send', 'bookyourtravel'); ?>" id="contact_submit" name="contact_submit" class="gradient-button" /></div>
				</fieldset>
			</form>
			<?php } ?>
		</div>
		<!--//contact form-->		
		<?php if (!empty($contact_phone_number) || !empty($business_contact_email)) { ?>	
		<!--contact info-->
		<div class="widget">
			<h4><?php esc_html_e('Or contact us directly', 'bookyourtravel'); ?></h4>
			<?php if (!empty($contact_phone_number)) {?><p class="ico ico-phone"><?php echo esc_html($contact_phone_number); ?></p><?php } ?>
			<?php if (!empty($business_contact_email)) {?><p class="ico ico-email"><a href="mailto:<?php echo esc_attr($business_contact_email); ?>"><?php echo esc_html($business_contact_email); ?></a></p><?php } ?>
		</div>
		<!--//contact info-->
		<?php } ?>	
	</aside>
	<!--//sidebar-->	
	<?php 
	endwhile; ?>
</div>
<?php
get_footer();