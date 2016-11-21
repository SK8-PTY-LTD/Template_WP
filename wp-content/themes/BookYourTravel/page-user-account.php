<?php 
/* Template Name: User Account Page
 * The template for displaying the user account page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
 
if ( !is_user_logged_in() ) {
	wp_redirect( home_url('/') );
	exit;
}

global $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_review_helper, $current_user, $frontend_submit, $item_class, $post;

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();

$page_id = $post->ID;
$current_url = get_permalink( $page_id );

global $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$is_partner_page = false;
if (isset($page_custom_fields['user_account_is_partner_page'])) {
	$is_partner_page = $page_custom_fields['user_account_is_partner_page'][0] == '1' ? true : false;
}

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
$item_class = 'one-fourth';
if ($page_sidebar_positioning == 'both') {
	$section_class = 'one-half';
	$item_class = 'one-half';
} else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') {
	$section_class = 'three-fourth';
	$item_class = 'one-third';
}
?>
<div class="row">
<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>
	<!--three-fourth content-->
	<section class="<?php echo esc_attr($section_class); ?>">
	<?php
	$allowed_tags = array();
	$allowed_tags['span'] = array('class' => array());
	?>
		<?php  while ( have_posts() ) : the_post(); ?>
		<article id="page-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), $allowed_tags) ); ?>
			<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
		</article>
		<?php endwhile; ?>
		<?php get_template_part('includes/parts/user-account', 'menu'); ?>
		<!--MySettings-->
		<section id="settings" class="tab-content initial">
			<script type="text/javascript">
			
				window.settingsFirstNameError = '<?php esc_html_e('First name is a required field!', 'bookyourtravel'); ?>';
				window.settingsLastNameError = '<?php esc_html_e('Last name is a required field!', 'bookyourtravel'); ?>';
				window.settingsEmailError = '<?php esc_html_e('Please enter valid email address!', 'bookyourtravel'); ?>';
				window.settingsPasswordError = '<?php esc_html_e('Password is a required field!', 'bookyourtravel'); ?>';
				window.settingsOldPasswordError = '<?php esc_html_e('Old password is a required field!', 'bookyourtravel'); ?>';
			
			</script>		
			<article class="mysettings">
				<h2><?php esc_html_e('Personal details', 'bookyourtravel'); ?></h2>
				<table>
					<tr>
						<th><?php esc_html_e('First name', 'bookyourtravel'); ?></th>
						<td><span id="span_first_name"><?php echo $user_info->user_firstname;?></span>						
							<div style="display:none;" class="edit_field field_first_name">
								<form id="settings-first-name-form" method="post" action="" class="settings">
									<label for="first_name"><?php esc_html_e('First name', 'bookyourtravel'); ?>:</label>
									<input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($user_info->user_firstname);?>"/>
									<input type="submit" value="save" class="gradient-button save_first_name"/>
									<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
								</form>
							</div>
						</td>
						<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Last name', 'bookyourtravel'); ?>:</th>
						<td><span id="span_last_name"><?php echo $user_info->user_lastname;?></span>	
							<div style="display:none;" class="edit_field field_last_name">
								<form id="settings-last-name-form" method="post" action="" class="settings">
									<label for="last_name"><?php esc_html_e('Last name', 'bookyourtravel'); ?>:</label>
									<input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($user_info->user_lastname);?>" />
									<input type="submit" value="save" class="gradient-button save_last_name"/>
									<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
								</form>
							</div>						
						</td>
						<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Email address', 'bookyourtravel'); ?>:</th>
						<td><span id="span_email"><?php echo $user_info->user_email;?></span>	
							<div style="display:none;" class="edit_field field_email">
								<form id="settings-email-form" method="post" action="" class="settings">
									<label for="email"><?php esc_html_e('Email', 'bookyourtravel'); ?>:</label>
									<input type="text" id="email" name="email" value="<?php echo esc_attr($user_info->user_email);?>" />
									<input type="submit" value="save" class="gradient-button save_email"/>
									<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
								</form>
							</div>
						</td>
						<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Password', 'bookyourtravel'); ?>:</th>
						<td><span id="span_email">**************</span>
							<div style="display:none;" class="edit_field field_password">		
								<form id="settings-password-form" method="post" action="" class="settings">
									<label for="old_password"><?php esc_html_e('Current password', 'bookyourtravel'); ?>:</label>
									<input type="password" id="old_password" name="old_password" />
									<label for="password"><?php esc_html_e('New password', 'bookyourtravel'); ?>:</label>
									<input type="password" id="password" name="password" />
									<input type="submit" value="save" class="gradient-button save_password"/>
									<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
								</form>
							</div></td>
						<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
					</tr>
				</table>
			</article>
		</section>
		<!--//MySettings-->
		<?php if (!$is_partner_page && $enable_accommodations) { ?>
		<!--My Bookings-->
		<section id="accommodation-bookings" class="tab-content">
			<?php
				$date_format = get_option('date_format');
				$bookings_results = $bookyourtravel_accommodation_helper->list_accommodation_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
				if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
					foreach ($bookings_results['results'] as $bookings_result) {
						$booking_id = $bookings_result->Id;
						$booking_date_from = date_i18n($date_format, strtotime($bookings_result->date_from));
						$booking_date_to = date_i18n($date_format, strtotime($bookings_result->date_to)); 
						$booking_price =  $bookings_result->total_price;
						$accommodation = $bookings_result->accommodation_name;
						$room_type = $bookings_result->room_type;
						$adults = $bookings_result->adults;
						$children = $bookings_result->children;
			?>
			<!--booking-->
			<article class="bookings">
				<h2><a href="<?php echo get_permalink($bookings_result->accommodation_id); ?>"><?php echo $accommodation; ?></a></h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Booking number', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_id; ?></td>
						</tr>
						<?php if (isset($room_type) && !empty($room_type)) { ?>
						<tr>
							<th><?php esc_html_e('Room type', 'bookyourtravel'); ?>:</th>
							<td><?php echo $room_type; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<th><?php esc_html_e('Check-in date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_from; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Check-out date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_to; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Adults', 'bookyourtravel'); ?>:</th>
							<td><?php echo $adults; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Children', 'bookyourtravel'); ?>:</th>
							<td><?php echo $children; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Total price', 'bookyourtravel'); ?>:</th>
							<td>
								<div class="second price">
									<em>
										<?php if (!$show_currency_symbol_after) { ?>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<?php } else { ?>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<?php } ?>
									</em>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</article>
			<!--//booking-->
			<?php }
			} else { ?>
			<article class="bookings"><p><?php echo esc_html__('You have not made any bookings yet!', 'bookyourtravel'); ?></p></article>
			<?php } ?>
		</section>
		<!--//My Bookings-->
		<?php } ?>
		
		<?php if (!$is_partner_page && $enable_car_rentals) { ?>
		<!--My Bookings-->
		<section id="car_rental-bookings" class="tab-content">
			<?php
				$date_format = get_option('date_format');
				
				$bookings_results = $bookyourtravel_car_rental_helper->list_car_rental_bookings(null, 'Id', 'ASC', null, 0, $current_user->ID, null);
				
				if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
				
					foreach ($bookings_results['results'] as $bookings_result) {
						$booking_id = $bookings_result->Id;
						$booking_date_from = date_i18n($date_format, strtotime($bookings_result->from_day));
						$booking_date_to = date_i18n($date_format, strtotime($bookings_result->to_day)); 
						$booking_price =  $bookings_result->total_price;
						$car_rental_name = $bookings_result->car_rental_name;
						$pick_up_title = isset($bookings_result->pick_up_title) ? $bookings_result->pick_up_title : '';
						$drop_off_title = isset($bookings_result->drop_off_title) ? $bookings_result->drop_off_title : '';
			?>
			<!--booking-->
			<article class="bookings">
				<h2><a href="<?php echo get_permalink($bookings_result->car_rental_id); ?>"><?php echo $car_rental_name; ?></a></h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Booking number', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_id; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('From date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_from; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('To date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_to; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Pick up', 'bookyourtravel'); ?>:</th>
							<td><?php echo $pick_up_title; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Drop off', 'bookyourtravel'); ?>:</th>
							<td><?php echo $drop_off_title; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Total price', 'bookyourtravel'); ?>:</th>
							<td>
								<div class="second price">
									<em>
										<?php if (!$show_currency_symbol_after) { ?>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<?php } else { ?>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<?php } ?>
									</em>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</article>
			<!--//booking-->
			<?php }
			} else { ?>
			<article class="bookings"><p><?php echo esc_html__('You have not made any bookings yet!', 'bookyourtravel'); ?></p></article>
			<?php } ?>
		</section>
		<!--//My Bookings-->
		<?php } ?>	


		<?php if (!$is_partner_page && $enable_tours) { ?>
		<!--My Bookings-->
		<section id="tour-bookings" class="tab-content">
			<?php
				$date_format = get_option('date_format');
				
				$bookings_results = $bookyourtravel_tour_helper->list_tour_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID, null);
				
				if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
				
					foreach ($bookings_results['results'] as $bookings_result) {
					
						$booking_id = $bookings_result->Id;
						$booking_date_from = date_i18n($date_format, strtotime($bookings_result->tour_date));
						$booking_duration_days =  $bookings_result->duration_days;
						$booking_price =  $bookings_result->total_price;
						$tour_name = $bookings_result->tour_name;
						
						$tour_schedule_id = $bookings_result->tour_schedule_id;
						$schedule = $bookyourtravel_tour_helper->get_tour_schedule($tour_schedule_id);
			?>
			<!--booking-->
			<article class="bookings">
				<h2><a href="<?php echo get_permalink($schedule->tour_id); ?>"><?php echo $tour_name; ?></a></h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Booking number', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_id; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Start date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_from; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Duration days', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_duration_days; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Total price', 'bookyourtravel'); ?>:</th>
							<td>
								<div class="second price">
									<em>
										<?php if (!$show_currency_symbol_after) { ?>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<?php } else { ?>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<?php } ?>
									</em>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</article>
			<!--//booking-->
			<?php }
			} else { ?>
			<article class="bookings"><p><?php echo esc_html__('You have not made any bookings yet!', 'bookyourtravel'); ?></p></article>
			<?php } ?>
		</section>
		<!--//My Bookings-->
		<?php } ?>	
		


		<?php if (!$is_partner_page && $enable_cruises) { ?>
		<!--My Bookings-->
		<section id="cruise-bookings" class="tab-content">
			<?php
				$date_format = get_option('date_format');
				
				$bookings_results = $bookyourtravel_cruise_helper->list_cruise_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID, null);
				
				if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
				
					foreach ($bookings_results['results'] as $bookings_result) {
					
						$booking_id = $bookings_result->Id;
						$booking_date_from = date_i18n($date_format, strtotime($bookings_result->cruise_date));
						$booking_price =  $bookings_result->total_price;
						$cruise_name = $bookings_result->cruise_name;
						$cabin_type = $bookings_result->cabin_type; 
						
						$cruise_schedule_id = $bookings_result->cruise_schedule_id;
						$schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($cruise_schedule_id);
						$booking_duration_days =  $schedule->duration_days;
			?>
			<!--booking-->
			<article class="bookings">
				<h2><a href="<?php echo get_permalink($schedule->cruise_id); ?>"><?php echo $cruise_name; ?></a></h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Cabin type', 'bookyourtravel'); ?>:</th>
							<td><?php echo $cabin_type; ?></td>
						</tr>					
						<tr>
							<th><?php esc_html_e('Booking number', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_id; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Start date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_date_from; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Duration days', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_duration_days; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Total price', 'bookyourtravel'); ?>:</th>
							<td>
								<div class="second price">
									<em>
										<?php if (!$show_currency_symbol_after) { ?>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<?php } else { ?>
										<span class="amount"><?php echo number_format_i18n( $booking_price, $price_decimal_places ); ?></span>
										<span class="curr"><?php echo $default_currency_symbol; ?></span>
										<?php } ?>
									</em>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</article>
			<!--//booking-->
			<?php }
			} else { ?>
			<article class="bookings"><p><?php echo esc_html__('You have not made any bookings yet!', 'bookyourtravel'); ?></p></article>
			<?php } ?>
		</section>
		<!--//My Bookings-->
		<?php } ?>	
		
		<?php if (!$is_partner_page && $enable_reviews) { ?>		
		<!--MyReviews-->
		<section id="reviews" class="tab-content">
			<?php 
			$reviews_query = $bookyourtravel_review_helper->list_user_reviews($current_user->ID);

			if ($reviews_query->have_posts()) { 
				while ($reviews_query->have_posts()) { 
				global $post;
				$reviews_query->the_post();
				$review = $post;
				$review_id = $review->ID;
				$review_custom_fields = get_post_custom($review_id);
				$reviewed_post_id = 0;
				if (isset($review_custom_fields['review_post_id'])) 
					$reviewed_post_id = $review_custom_fields['review_post_id'][0];
				if ($reviewed_post_id > 0) {
					$reviewed_item = get_post($reviewed_post_id);

					$reviews_score = 0;
					$reviews_possible_score = 10 * 7;
					$reviews_score = $bookyourtravel_review_helper->sum_user_review_meta_values($review_id, $current_user->ID, $reviewed_item->post_type);
					$score_out_of_10 = 0;
					if ($reviews_possible_score > 0) {
						$score_out_of_10 = intval(($reviews_score / $reviews_possible_score) * 10);
					}

					$likes = $review_custom_fields['review_likes'][0];
					$dislikes = $review_custom_fields['review_dislikes'][0]; ?>
				<article class="myreviews">	
					<?php if ($reviewed_item->post_type == 'accommodation') { ?>
						<h2><?php echo sprintf(esc_html__('Your review of accommodation %s', 'bookyourtravel'), $reviewed_item ? $reviewed_item->post_title : ''); ?></h2>
					<?php } else if ($reviewed_item->post_type == 'tour') { ?>
						<h2><?php echo sprintf(esc_html__('Your review of tour %s', 'bookyourtravel'), $reviewed_item ? $reviewed_item->post_title : ''); ?></h2>
					<?php } else if ($reviewed_item->post_type == 'cruise') { ?>
						<h2><?php echo sprintf(esc_html__('Your review of cruise %s', 'bookyourtravel'), $reviewed_item ? $reviewed_item->post_title : ''); ?></h2>
					<?php } else if ($reviewed_item->post_type == 'car_rental') { ?>
						<h2><?php echo sprintf(esc_html__('Your review of car rental %s', 'bookyourtravel'), $reviewed_item ? $reviewed_item->post_title : ''); ?></h2>
					<?php } ?>
					<div class="score">
						<span class="achieved"><?php echo $score_out_of_10; ?></span>
						<span> / 10</span>
					</div>
					<div class="reviews">
						<div class="rev pro"><p><?php echo $likes; ?></p></div>
						<div class="rev con"><p><?php echo $dislikes; ?></p></div>
					</div>
				</article>			
			<?php 
					}
				} 
			} else { ?>
			<article class="myreviews"><p><?php echo esc_html__('You have not left any reviews yet!', 'bookyourtravel'); ?></p></article>
			<?php }			
			// Reset Loop Post Data
			?>
		</section>
		<!--//MyReviews-->
<?php } // if ($enable_reviews) ?>		
	</section>
	<!--//three-fourth content-->
<?php 
	wp_reset_postdata();
	wp_reset_query();
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
?>
</div>
<?php
get_footer();