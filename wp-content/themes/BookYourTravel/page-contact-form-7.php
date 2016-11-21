<?php
/*	Template Name: Contact Form 7 
 * Template for displaying a contact page using a contact form 7 form.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
 
get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');	

global $bookyourtravel_theme_globals;
 
$contact_phone_number = $bookyourtravel_theme_globals->get_contact_phone_number();
$contact_email = $bookyourtravel_theme_globals->get_contact_email();
$business_address_longitude = $bookyourtravel_theme_globals->get_business_address_longitude();
$business_address_latitude = $bookyourtravel_theme_globals->get_business_address_latitude();
?>
<div class="row">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>	
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
	<aside class="right-sidebar lower one-fourth">
		<!--contact form-->
		<div class="widget">
			<?php the_content(); ?>
		</div>
		<!--//contact form-->	
	<?php if (!empty($contact_phone_number)	|| !empty($contact_email)) { ?>	
		<!--contact info-->
		<div class="widget">
			<h4><?php esc_html_e('Or contact us directly', 'bookyourtravel'); ?></h2>
			<?php if (!empty($contact_phone_number)) {?><p class="ico ico-phone"><?php echo esc_html($contact_phone_number); ?></p><?php } ?>
			<?php if (!empty($contact_email)) {?><p class="ico ico-emai"><a href="#"><?php echo esc_html($contact_email); ?></a></p><?php } ?>
		</div>
		<!--//contact info-->
	<?php } ?>		
	</aside>
	<!--//sidebar-->	
	<?php endwhile; ?>
</div>
<?php
get_footer();