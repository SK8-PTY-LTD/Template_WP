<?php 
/* Template Name: User Content List
 * The template for displaying the user submitted content list.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_accommodation_helper, 
$bookyourtravel_tour_helper, $bookyourtravel_room_type_helper, $bookyourtravel_review_helper, $current_user, $frontend_submit, $item_class;
 
if ( !is_user_logged_in() || !$frontend_submit->user_has_correct_role()) {
	wp_redirect( home_url('/') );
	exit;
}

$list_user_accommodation_vacancies_url = $bookyourtravel_theme_globals->get_list_user_accommodation_vacancies_url();
$list_user_accommodation_vacancies_url_with_arg = $list_user_accommodation_vacancies_url;
$list_user_accommodation_vacancies_url_with_arg = add_query_arg( 'accid', '', $list_user_accommodation_vacancies_url_with_arg);

$submit_accommodation_vacancies_url = $bookyourtravel_theme_globals->get_submit_accommodation_vacancies_url();
$submit_accommodation_vacancies_url_with_arg = $submit_accommodation_vacancies_url;
$submit_accommodation_vacancies_url_with_arg = add_query_arg( 'fesid', '', $submit_accommodation_vacancies_url_with_arg);

$list_user_accommodation_bookings_url = $bookyourtravel_theme_globals->get_list_user_accommodation_bookings_url();
$list_user_accommodation_bookings_url_with_arg = $list_user_accommodation_bookings_url;
$list_user_accommodation_bookings_url_with_arg = add_query_arg( 'accid', '', $list_user_accommodation_bookings_url_with_arg);

$list_user_tour_schedules_url = $bookyourtravel_theme_globals->get_list_user_tour_schedules_url();
$list_user_tour_schedules_url_with_arg = $list_user_tour_schedules_url;
$list_user_tour_schedules_url_with_arg = add_query_arg( 'tourid', '', $list_user_tour_schedules_url_with_arg);

$list_user_tour_bookings_url = $bookyourtravel_theme_globals->get_list_user_tour_bookings_url();
$list_user_tour_bookings_url_with_arg = $list_user_tour_bookings_url;
$list_user_tour_bookings_url_with_arg = add_query_arg( 'tourid', '', $list_user_tour_bookings_url_with_arg);

$submit_tour_schedules_url = $bookyourtravel_theme_globals->get_submit_tour_schedules_url();
$submit_tour_schedules_url_with_arg = $submit_tour_schedules_url;
$submit_tour_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_tour_schedules_url_with_arg);

$list_user_cruise_schedules_url = $bookyourtravel_theme_globals->get_list_user_cruise_schedules_url();
$list_user_cruise_schedules_url_with_arg = $list_user_cruise_schedules_url;
$list_user_cruise_schedules_url_with_arg = add_query_arg( 'cruiseid', '', $list_user_cruise_schedules_url_with_arg);

$list_user_cruise_bookings_url = $bookyourtravel_theme_globals->get_list_user_cruise_bookings_url();
$list_user_cruise_bookings_url_with_arg = $list_user_cruise_bookings_url;
$list_user_cruise_bookings_url_with_arg = add_query_arg( 'cruiseid', '', $list_user_cruise_bookings_url_with_arg);

$submit_cruise_schedules_url = $bookyourtravel_theme_globals->get_submit_cruise_schedules_url();
$submit_cruise_schedules_url_with_arg = $submit_cruise_schedules_url;
$submit_cruise_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_cruise_schedules_url_with_arg);

$submit_room_types_url = $bookyourtravel_theme_globals->get_submit_room_types_url();
$submit_cabin_types_url = $bookyourtravel_theme_globals->get_submit_cabin_types_url();	

$list_user_car_rental_bookings_url = $bookyourtravel_theme_globals->get_list_user_car_rental_bookings_url();
$list_user_car_rental_bookings_url_with_arg = $list_user_car_rental_bookings_url;
$list_user_car_rental_bookings_url_with_arg = add_query_arg( 'carrentalid', '', $list_user_car_rental_bookings_url_with_arg);


get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);
$current_author_id = $current_user->ID;
if (is_super_admin()) {
	$current_author_id = null; // we will list all items because this is a super admin. when passed to list method if author id is null, parameter is ignored so all items are returned.
}

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);
$current_url = get_permalink( $page_id );

$content_type = 'accommodation';
if (isset($page_custom_fields['user_content_type'])) {
	$content_type = $page_custom_fields['user_content_type'][0];
}

if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} else if ( get_query_var('page') ) {
    $paged = get_query_var('page');
} else {
    $paged = 1;
}
$posts_per_page = get_option('posts_per_page');

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
$item_class = 'full-width';
if ($page_sidebar_positioning == 'both') {
	$section_class = 'one-half';
} else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') {
	$section_class = 'three-fourth';
}
?>
<div class="row">
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>
	<!--three-fourth content-->
	<section class="<?php echo esc_attr($section_class); ?>">
	
		<?php get_template_part('includes/parts/user-account', 'menu'); ?>	
		
		<?php if ($content_type == 'accommodation') { ?>
		<!--Accommodation list-->
		<section id="accommodationlist" class="tab-content initial">
			<?php
				$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations ( $paged, $posts_per_page, '', '', 0, array(), array(), array(), false, null, $current_author_id, true );
			?>
			<div class="deals">
				<?php if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) { ?>
				
				<?php
				foreach ($accommodation_results['results'] as $accommodation_result) {
					global $post;
					$post = $accommodation_result;
					setup_postdata( $post ); 
					get_template_part('includes/parts/accommodation', 'item');
				}
				?>
				
				<nav class="page-navigation bottom-nav">
					<!--back up button-->
					<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
					<!--//back up button-->
					<!--pager-->
					<div class="pager">
						<?php 
						$total_results = $accommodation_results['total'];
						BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
						?>
					</div>
				</nav>
			<?php } else {
					   echo '<p>' . esc_html__('You have not submitted any accommodations yet.', 'bookyourtravel') . '</p>';
				  }  // end if ( $query->have_posts() ) ?>
			</div><!--//deals clearfix-->
		</section>
		<?php } else if ($content_type == 'tour') { ?>
		<!--Tour list-->
		<section id="tourlist" class="tab-content initial">
			<?php
				$tour_results = $bookyourtravel_tour_helper->list_tours ( $paged, $posts_per_page, '', '', 0, array(), array(), array(), false, $current_author_id, true );
			?>
			<div class="deals">
				<?php if ( count($tour_results) > 0 && $tour_results['total'] > 0 ) { ?>
				
				<?php
				foreach ($tour_results['results'] as $tour_result) {
					global $post;
					$post = $tour_result;
					setup_postdata( $post ); 
					get_template_part('includes/parts/tour', 'item');
				}
				?>
				
				<nav class="page-navigation bottom-nav">
					<!--back up button-->
					<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
					<!--//back up button-->
					<!--pager-->
					<div class="pager">
						<?php 
						$total_results = $tour_results['total'];
						BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
						?>
					</div>
				</nav>
			<?php } else {
					   echo '<p>' . esc_html__('You have not submitted any tours yet.', 'bookyourtravel') . '</p>';
				  }  // end if ( $query->have_posts() ) ?>
			</div><!--//deals clearfix-->
		</section>

		<?php } else if ($content_type == 'cruise') { ?>
		<!--Tour list-->
		<section id="tourlist" class="tab-content initial">
			<?php
				$cruise_results = $bookyourtravel_cruise_helper->list_cruises( $paged, $posts_per_page, '', '', 0, array(), array(), array(), false, $current_author_id, true );
			?>
			<div class="deals">
				<?php if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) { ?>
				
				<?php
				foreach ($cruise_results['results'] as $cruise_result) {
					global $post;
					$post = $cruise_result;
					setup_postdata( $post ); 
					get_template_part('includes/parts/cruise', 'item');
				}
				?>
				
				<nav class="page-navigation bottom-nav">
					<!--back up button-->
					<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
					<!--//back up button-->
					<!--pager-->
					<div class="pager">
						<?php 
						$total_results = $cruise_results['total'];
						BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
						?>
					</div>
				</nav>
			<?php } else {
					   echo '<p>' . esc_html__('You have not submitted any cruises yet.', 'bookyourtravel') . '</p>';
				  }  // end if ( $query->have_posts() ) ?>
			</div><!--//deals clearfix-->
		</section>		
		
		<?php } else if ($content_type == 'car_rental') { ?>
		<!--Tour list-->
		<section id="tourlist" class="tab-content initial">
			<?php
				$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals( $paged, $posts_per_page, '', '', 0, array(), array(), array(), false, $current_author_id, true );
			?>
			<div class="deals">
				<?php if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) { ?>
				
				<?php
				foreach ($car_rental_results['results'] as $car_rental_result) {
					global $post;
					$post = $car_rental_result;
					setup_postdata( $post ); 
					get_template_part('includes/parts/car_rental', 'item');
				}
				?>				
				<nav class="page-navigation bottom-nav">
					<!--back up button-->
					<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
					<!--//back up button-->
					<!--pager-->
					<div class="pager">
						<?php 
						$total_results = $car_rental_results['total'];
						BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
						?>
					</div>
				</nav>
			<?php } else {
					   echo '<p>' . esc_html__('You have not submitted any car rentals yet.', 'bookyourtravel') . '</p>';
				  }  // end if ( $query->have_posts() ) ?>
			</div><!--//deals clearfix-->
		</section>	
		
		<?php } elseif ($content_type == 'vacancy') {?>
		<script>
			function accommodationSelectRedirect(accommodationId) {
				document.location = <?php echo json_encode($list_user_accommodation_vacancies_url_with_arg); ?> + '=' + accommodationId;
			};
		</script>
		<?php 
			$accommodation_id = 0;
			if ( isset($_GET['accid']) ) {
				$accommodation_id = intval($_GET['accid']);
			}
			$date_format = get_option('date_format');
		?>
		<section id="accommodation-vacancy-list" class="tab-content initial">
			<div class="filter">
				<label for="filter_user_accommodations"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
			<?php
			$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations ( 0, -1, '', '', 0, array(), array(), array(), false, null, $current_author_id, true );
			$select_accommodations = "<select onchange='accommodationSelectRedirect(this.value)' name='filter_user_accommodations' id='filter_user_accommodations'>";
			$select_accommodations .= "<option value=''>" . esc_html__('Select accommodation', 'bookyourtravel') . "</option>";
			if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
				foreach ($accommodation_results['results'] as $accommodation_result) {
					global $post;
					$post = $accommodation_result;
					setup_postdata( $post ); 
					$select_accommodations .= "<option " . ($post->ID == $accommodation_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
				}
			}
			$select_accommodations .= "</select>";
			echo $select_accommodations;
			?>
			</div>
			<?php
			if ($accommodation_id > 0) {
				$vacancy_results = $bookyourtravel_accommodation_helper->list_accommodation_vacancies($accommodation_id, 0, '', '', $paged, $posts_per_page);
				
				if ( count($vacancy_results) > 0 && $vacancy_results['total'] > 0 ) {
					foreach ($vacancy_results['results'] as $vacancy_result) {
						$accommodation_obj = new BookYourTravel_Accommodation($vacancy_result->accommodation_id);
						$disabled_room_types = $accommodation_obj->get_disabled_room_types();
						$is_price_per_person = $accommodation_obj->get_is_price_per_person();
						
						$room_type_obj = null;
						if (!$disabled_room_types)
							$room_type_obj = new BookYourTravel_Room_Type($vacancy_result->room_type_id);
				?>				
				<article class="bookings vacancies article_vacancy_<?php echo $vacancy_result->Id; ?>">
					<h2>
						<a href="<?php echo esc_url($accommodation_obj->get_permalink()); ?>"><?php echo $accommodation_obj->get_title(); ?></a>
						<span></span>
					</h2>
					<div class="b-info">
						<table>
							<tr>
								<th><?php esc_html_e('Vacancy Id', 'bookyourtravel'); ?>:</th>
								<td>
									<?php echo $vacancy_result->Id; ?>
									<?php BookYourTravel_Theme_Utils::render_link_button($submit_accommodation_vacancies_url_with_arg . "=" . $vacancy_result->Id, "gradient-button", "", esc_html__('Edit', 'bookyourtravel')); ?>
									<form method='post' name='delete_vacancy_<?php echo $vacancy_result->Id; ?>' id='delete_vacancy_<?php echo $vacancy_result->Id; ?>'>
										<input type='hidden' class='delete_vacancy_id' value='<?php echo $vacancy_result->Id; ?>' />
										<?php wp_nonce_field('bookyourtravel_nonce'); ?>
										<?php BookYourTravel_Theme_Utils::render_link_button('#', "gradient-button button-delete button-delete-vacancy", "", esc_html__('Delete', 'bookyourtravel')); ?>
									</form>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e('Room type', 'bookyourtravel'); ?>:</th>
								<td><?php echo $room_type_obj == null ? esc_html__('N/A', 'bookyourtravel') : $room_type_obj->get_title(); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Available rooms', 'bookyourtravel'); ?>:</th>
								<td><?php echo $room_type_obj == null ? esc_html__('N/A', 'bookyourtravel') : $vacancy_result->room_count; ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Start date', 'bookyourtravel'); ?>:</th>
								<td><?php echo date_i18n($date_format, strtotime($vacancy_result->start_date)); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('End date', 'bookyourtravel'); ?>:</th>
								<td><?php echo date_i18n($date_format, strtotime($vacancy_result->end_date)); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Price', 'bookyourtravel'); ?>:</th>
								<td><?php echo $default_currency_symbol . $vacancy_result->price_per_day; ?><?php echo $is_price_per_person ? ' / ' . $default_currency_symbol . $vacancy_result->price_per_day_child : ''; ?></td>
							</tr>
						</table>
					</div>
				</article>
				<?php } ?>
				<nav class="page-navigation bottom-nav">
					<!--back up button-->
					<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
					<!--//back up button-->
					<!--pager-->
					<div class="pager">
						<?php 
						$total_results = $vacancy_results['total'];
						BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
						?>
					</div>
				</nav>
				
				<?php
				} else {
				   echo '<p>' . esc_html__('You have not created any vacancies for this accommodation yet.', 'bookyourtravel') . '</p>';
				}
			}
			?>
		</section>
		
<?php } elseif ($content_type == 'accommodation_booking') { ?>
	<script>
		function accommodationSelectRedirect(accommodationId) {
			document.location = <?php echo json_encode($list_user_accommodation_bookings_url_with_arg); ?> + '=' + accommodationId;
		};
	</script>
	<?php 
		$accommodation_id = 0;
		if ( isset($_GET['accid']) ) {
			$accommodation_id = intval($_GET['accid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="accommodation-booking-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_accommodations"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php
		$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations ( 0, -1, '', '', 0, array(), array(), array(), false, null, $current_author_id, true );
		$select_accommodations = "<select onchange='accommodationSelectRedirect(this.value)' name='filter_user_accommodations' id='filter_user_accommodations'>";
		$select_accommodations .= "<option value=''>" . esc_html__('Select accommodation', 'bookyourtravel') . "</option>";
		if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
			foreach ($accommodation_results['results'] as $accommodation_result) {
				global $post;
				$post = $accommodation_result;
				setup_postdata( $post ); 
				$select_accommodations .= "<option " . ($post->ID == $accommodation_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_accommodations .= "</select>";
		echo $select_accommodations;
		?>
		</div>
		<?php
		if ($accommodation_id > 0) {
			$bookings_results = $bookyourtravel_accommodation_helper->list_accommodation_bookings($paged, $posts_per_page, 'Id', 'ASC', null, 0, $current_author_id, $accommodation_id);
			
			if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
				foreach ($bookings_results['results'] as $bookings_result) {
					$booking_id = $bookings_result->Id;
					$booking_date_from = date_i18n($date_format, strtotime($bookings_result->date_from));
					$booking_date_to = date_i18n($date_format, strtotime($bookings_result->date_to)); 
					$booking_price = $bookings_result->total_price;
					$booking_full_name = (isset($bookings_result->first_name) ? $bookings_result->first_name : '') . ' ' . (isset($bookings_result->last_name) ? $bookings_result->last_name : '');					
					$booking_email = $bookings_result->email;
					$booking_created_date​ = date_i18n($date_format, strtotime($bookings_result->created));
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
						<tr>
							<th><?php esc_html_e('Customer name', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_first_name​ . ' ' . $booking_last_name​; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Customer email', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_email; ?></td>
						</tr>						
						<tr>
							<th><?php esc_html_e('Date created', 'bookyourtravel'); ?>:</th>
							<td><?php echo $booking_created_date​; ?></td>
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
			?>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php 
					$total_results = $bookings_results['total'];
					BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
					?>
				</div>
			</nav>				
			<?php				
			} else {
			   echo '<p>' . esc_html__('You have not had any bookings for this accommodation yet.', 'bookyourtravel') . '</p>';
			}
		}
		?>
	</section>		
		
<?php } elseif ($content_type == 'tour_schedule') {?>
	
	<script>
		function tourSelectRedirect(tourId) {
			document.location = <?php echo json_encode($list_user_tour_schedules_url_with_arg); ?> + '=' + tourId;
		};
	</script>
	<?php 
		$tour_id = 0;
		if ( isset($_GET['tourid']) ) {
			$tour_id = intval($_GET['tourid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="tour-schedule-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_tours"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php

		$tour_results = $bookyourtravel_tour_helper->list_tours ( 0, -1, '', '', 0, array(), array(), array(), false, $current_author_id, true );
		$select_tours = "<select onchange='tourSelectRedirect(this.value)' name='filter_user_tours' id='filter_user_tours'>";
		$select_tours .= "<option value=''>" . esc_html__('Select tour', 'bookyourtravel') . "</option>";
		if ( count($tour_results) > 0 && $tour_results['total'] > 0 ) {
			foreach ($tour_results['results'] as $tour_result) {
				global $post;
				$post = $tour_result;
				setup_postdata( $post ); 
				$select_tours .= "<option " . ($post->ID == $tour_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_tours .= "</select>";
		echo $select_tours;
		?>
		</div>
		<?php
		
		if ($tour_id > 0) {
			$schedule_results = $bookyourtravel_tour_helper->list_tour_schedules($paged, $posts_per_page, '', '', 0, 0, 0, $tour_id, '', $current_author_id);

			if ( count($schedule_results) > 0 && $schedule_results['total'] > 0 ) {
				foreach ($schedule_results['results'] as $schedule_result) {
					$tour_obj = new BookYourTravel_Tour($schedule_result->tour_id);
					$is_price_per_group = $tour_obj->get_is_price_per_group();
					$tour_type_is_repeated = $tour_obj->get_type_is_repeated();
					
			?>				
			<article class="bookings schedules article_tour_schedule_<?php echo $schedule_result->Id; ?>">
				<h2>
					<a href="<?php echo esc_url($tour_obj->get_permalink()); ?>"><?php echo $tour_obj->get_title(); ?></a>
					<span></span>
				</h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Schedule Id', 'bookyourtravel'); ?>:</th>
							<td>
								<?php echo $schedule_result->Id; ?>
								<?php BookYourTravel_Theme_Utils::render_link_button($submit_tour_schedules_url_with_arg . "=" . $schedule_result->Id, "gradient-button", "", esc_html__('Edit', 'bookyourtravel')); ?>
								<form method='post' name='delete_tour_schedule_<?php echo $schedule_result->Id; ?>' id='delete_tour_schedule_<?php echo $schedule_result->Id; ?>'>
									<input type='hidden' class='delete_tour_schedule_id' value='<?php echo $schedule_result->Id; ?>' />
									<?php wp_nonce_field('bookyourtravel_nonce'); ?>
									<?php BookYourTravel_Theme_Utils::render_link_button('#', "gradient-button button-delete button-delete-tour-schedule", "", esc_html__('Delete', 'bookyourtravel')); ?>
								</form>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e('Start date', 'bookyourtravel'); ?>:</th>
							<td><?php echo date_i18n($date_format, strtotime($schedule_result->start_date)); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('End date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $tour_type_is_repeated > 1 ? date_i18n($date_format, strtotime($schedule_result->end_date)) : esc_html__('N/A', 'bookyourtravel'); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Max people', 'bookyourtravel'); ?>:</th>
							<td><?php echo $schedule_result->max_people; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Duration days', 'bookyourtravel'); ?>:</th>
							<td><?php echo $schedule_result->duration_days; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Price', 'bookyourtravel'); ?>:</th>
							<td><?php echo $default_currency_symbol . $schedule_result->price; ?><?php echo !$is_price_per_group ? ' / ' . $default_currency_symbol . $schedule_result->price_child : ''; ?></td>
						</tr>
					</table>
				</div>
			</article>
			<?php } ?>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php 
					$total_results = $schedule_results['total'];
					BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
					?>
				</div>
			</nav>
			
			<?php
			} else {
			   echo '<p>' . esc_html__('You have not created any schedules for this tour yet.', 'bookyourtravel') . '</p>';
			}
		}
		?>
	</section>
<?php } elseif ($content_type == 'tour_booking') { ?>
	<script>
		function tourSelectRedirect(tourId) {
			document.location = <?php echo json_encode($list_user_tour_bookings_url_with_arg); ?> + '=' + tourId;
		};
	</script>
	<?php 
		$tour_id = 0;
		if ( isset($_GET['tourid']) ) {
			$tour_id = intval($_GET['tourid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="tour-bookings-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_tours"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php

		$tour_results = $bookyourtravel_tour_helper->list_tours ( 0, -1, '', '', 0, array(), array(), array(), false, $current_author_id, true );
		$select_tours = "<select onchange='tourSelectRedirect(this.value)' name='filter_user_tours' id='filter_user_tours'>";
		$select_tours .= "<option value=''>" . esc_html__('Select tour', 'bookyourtravel') . "</option>";
		if ( count($tour_results) > 0 && $tour_results['total'] > 0 ) {
			foreach ($tour_results['results'] as $tour_result) {
				global $post;
				$post = $tour_result;
				setup_postdata( $post ); 
				$select_tours .= "<option " . ($post->ID == $tour_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_tours .= "</select>";
		echo $select_tours;
		?>
		</div>
		<?php
		
		if ($tour_id > 0) {
			?>				
			<!--My Bookings-->
			<?php
			$date_format = get_option('date_format');
			
			$bookings_results = $bookyourtravel_tour_helper->list_tour_bookings($paged, $posts_per_page, 'Id', 'ASC', null, null, $current_author_id, $tour_id);
			
			if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
			
				foreach ($bookings_results['results'] as $bookings_result) {
				
					$booking_id = $bookings_result->Id;
					$booking_date_from = date_i18n($date_format, strtotime($bookings_result->tour_date));
					$booking_full_name = (isset($bookings_result->first_name) ? $bookings_result->first_name : '') . ' ' . (isset($bookings_result->last_name) ? $bookings_result->last_name : '');
					$booking_duration_days = $bookings_result->duration_days;
					$booking_price =  $bookings_result->total_price;
					$tour_name = $bookings_result->tour_name;

					$booking_email = $bookings_result->email;
					$booking_created_date​ = date_i18n($date_format, strtotime($bookings_result->created));								
					
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
								<th><?php esc_html_e('Customer name', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_full_name; ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Customer email', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_email; ?></td>
							</tr>						
							<tr>
								<th><?php esc_html_e('Date created', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_created_date​; ?></td>
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
				<article class="bookings"><p><?php echo esc_html__('You have not had any bookings for this tour yet!', 'bookyourtravel'); ?></p></article>
				<?php } ?>
			<!--//My Bookings-->
		<?php
			}
?>		
	</section>
<?php } elseif ($content_type == 'cruise_schedule') {?>
		
	<script>
		function cruiseSelectRedirect(cruiseId) {
			document.location = <?php echo json_encode($list_user_cruise_schedules_url_with_arg); ?> + '=' + cruiseId;
		};
	</script>
	<?php 
		$cruise_id = 0;
		if ( isset($_GET['cruiseid']) ) {
			$cruise_id = intval($_GET['cruiseid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="cruise-schedule-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_cruises"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php
		$cruise_results = $bookyourtravel_cruise_helper->list_cruises ( 0, -1, '', '', 0, array(), array(), array(), false, $current_author_id, true );
		$select_cruises = "<select onchange='cruiseSelectRedirect(this.value)' name='filter_user_cruises' id='filter_user_cruises'>";
		$select_cruises .= "<option value=''>" . esc_html__('Select cruise', 'bookyourtravel') . "</option>";
		if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			foreach ($cruise_results['results'] as $cruise_result) {
				global $post;
				$post = $cruise_result;
				setup_postdata( $post ); 
				$select_cruises .= "<option " . ($post->ID == $cruise_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_cruises .= "</select>";
		echo $select_cruises;
		?>
		</div>
		<?php
		
		if ($cruise_id > 0) {
			$schedule_results = $bookyourtravel_cruise_helper->list_cruise_schedules($paged, $posts_per_page, '', '', 0, 0, 0, $cruise_id, '', $current_author_id);

			if ( count($schedule_results) > 0 && $schedule_results['total'] > 0 ) {
				foreach ($schedule_results['results'] as $schedule_result) {
					$cruise_obj = new BookYourTravel_Cruise($schedule_result->cruise_id);
					$is_price_per_person = $cruise_obj->get_is_price_per_person();
					$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
					
					$cabin_type_obj = new BookYourTravel_Cabin_Type($schedule_result->cabin_type_id);
			?>				
			<article class="bookings schedules article_cruise_schedule_<?php echo $schedule_result->Id; ?>">
				<h2>
					<a href="<?php echo esc_url($cruise_obj->get_permalink()); ?>"><?php echo $cruise_obj->get_title(); ?></a>
					<span></span>
				</h2>
				<div class="b-info">
					<table>
						<tr>
							<th><?php esc_html_e('Schedule Id', 'bookyourtravel'); ?>:</th>
							<td>
								<?php echo $schedule_result->Id; ?>
								<?php BookYourTravel_Theme_Utils::render_link_button($submit_cruise_schedules_url_with_arg . "=" . $schedule_result->Id, "gradient-button", "", esc_html__('Edit', 'bookyourtravel')); ?>
								<form method='post' name='delete_cruise_schedule_<?php echo $schedule_result->Id; ?>' id='delete_cruise_schedule_<?php echo $schedule_result->Id; ?>'>
									<input type='hidden' class='delete_cruise_schedule_id' value='<?php echo $schedule_result->Id; ?>' />
									<?php wp_nonce_field('bookyourtravel_nonce'); ?>
									<?php BookYourTravel_Theme_Utils::render_link_button('#', "gradient-button button-delete button-delete-cruise-schedule", "", esc_html__('Delete', 'bookyourtravel')); ?>
								</form>								
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e('Cabin type', 'bookyourtravel'); ?>:</th>
							<td><?php echo $cabin_type_obj->get_title(); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Available cabins', 'bookyourtravel'); ?>:</th>
							<td><?php echo $schedule_result->cabin_count; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Start date', 'bookyourtravel'); ?>:</th>
							<td><?php echo date_i18n($date_format, strtotime($schedule_result->start_date)); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('End date', 'bookyourtravel'); ?>:</th>
							<td><?php echo $cruise_type_is_repeated > 1 ? date_i18n($date_format, strtotime($schedule_result->end_date)) : esc_html__('N/A', 'bookyourtravel'); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Duration days', 'bookyourtravel'); ?>:</th>
							<td><?php echo $schedule_result->duration_days; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Price', 'bookyourtravel'); ?>:</th>
							<td><?php echo $default_currency_symbol . $schedule_result->price; ?><?php echo $is_price_per_person ? ' / ' . $default_currency_symbol . $schedule_result->price_child : ''; ?></td>
						</tr>
					</table>
				</div>
			</article>
			<?php } ?>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php 
					$total_results = $schedule_results['total'];
					BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
					?>
				</div>
			</nav>
			
			<?php
			} else {
			   echo '<p>' . esc_html__('You have not created any schedules for this cruise yet.', 'bookyourtravel') . '</p>';
			}
		}
		?>
	</section>
		
<?php } elseif ($content_type == 'cruise_booking') { ?>
	<script>
		function cruiseSelectRedirect(cruiseId) {
			document.location = <?php echo json_encode($list_user_cruise_bookings_url_with_arg); ?> + '=' + cruiseId;
		};
	</script>
	<?php 
		$cruise_id = 0;
		if ( isset($_GET['cruiseid']) ) {
			$cruise_id = intval($_GET['cruiseid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="cruise-bookings-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_cruises"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php

		$cruise_results = $bookyourtravel_cruise_helper->list_cruises ( 0, -1, '', '', 0, array(), array(), array(), false, $current_author_id, true );
		$select_cruises = "<select onchange='cruiseSelectRedirect(this.value)' name='filter_user_cruises' id='filter_user_cruises'>";
		$select_cruises .= "<option value=''>" . esc_html__('Select cruise', 'bookyourtravel') . "</option>";
		if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			foreach ($cruise_results['results'] as $cruise_result) {
				global $post;
				$post = $cruise_result;
				setup_postdata( $post ); 
				$select_cruises .= "<option " . ($post->ID == $cruise_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_cruises .= "</select>";
		echo $select_cruises;
		?>
		</div>
		<?php
		
		if ($cruise_id > 0) {
			?>				
			<!--My Bookings-->
			<?php
			$date_format = get_option('date_format');
			
			$bookings_results = $bookyourtravel_cruise_helper->list_cruise_bookings($paged, $posts_per_page, 'Id', 'ASC', null, null, $current_author_id, $cruise_id);
			
			if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
			
				foreach ($bookings_results['results'] as $bookings_result) {
				
					$booking_id = $bookings_result->Id;
					$booking_date_from = date_i18n($date_format, strtotime($bookings_result->cruise_date));
					$booking_price =  $bookings_result->total_price;
					$cruise_name = $bookings_result->cruise_name;
					$cabin_type = $bookings_result->cabin_type; 
					
					$booking_full_name = (isset($bookings_result->first_name) ? $bookings_result->first_name : '') . ' ' . (isset($bookings_result->last_name) ? $bookings_result->last_name : '');					
					$booking_email = $bookings_result->email;
					$booking_created_date​ = date_i18n($date_format, strtotime($bookings_result->created));						
					
					$cruise_schedule_id = $bookings_result->cruise_schedule_id;
					$schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($cruise_schedule_id);
					$booking_duration_days =  $schedule->duration_days;
				?>
				<!--booking-->
				<article class="bookings">
					<h2><a href="<?php echo get_permalink($schedule->cruise_id); ?>"><?php echo $cruise_name; ?> </a></h2>
					<div class="b-info">
						<table>
							<tr>
								<th><?php esc_html_e('Booking number', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_id; ?></td>
							</tr>						
							<tr>
								<th><?php esc_html_e('Customer name', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_full_name; ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Customer email', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_email; ?></td>
							</tr>						
							<tr>
								<th><?php esc_html_e('Date created', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_created_date​; ?></td>
							</tr>								
							<tr>
								<th><?php esc_html_e('Cabin type', 'bookyourtravel'); ?>:</th>
								<td><?php echo $cabin_type; ?></td>
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
				<article class="bookings"><p><?php echo esc_html__('You have not had any bookings for this cruise yet!', 'bookyourtravel'); ?></p></article>
				<?php } ?>
			<!--//My Bookings-->
<?php	} ?>
	</section>
<?php } elseif ($content_type == 'car_rental_booking') { ?>
	<script>
		function carRentalSelectRedirect(carRentalId) {
			document.location = <?php echo json_encode($list_user_car_rental_bookings_url_with_arg); ?> + '=' + carRentalId;
		};
	</script>
	<?php 
		$car_rental_id = 0;
		if ( isset($_GET['carrentalid']) ) {
			$car_rental_id = intval($_GET['carrentalid']);
		}
		$date_format = get_option('date_format');
	?>
	<section id="car-rental-booking-list" class="tab-content initial">
		<div class="filter">
			<label for="filter_user_car_rentals"><?php esc_html_e('Filter by', 'bookyourtravel'); ?></label>
		<?php
		$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals( $paged, $posts_per_page, '', '', 0, array(), array(), array(), false, $current_author_id, true );
		$select_car_rentals = "<select onchange='carRentalSelectRedirect(this.value)' name='filter_user_car_rentals' id='filter_user_car_rentals'>";
		$select_car_rentals .= "<option value=''>" . esc_html__('Select car rental', 'bookyourtravel') . "</option>";
		if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) {
			foreach ($car_rental_results['results'] as $car_rental_result) {
				global $post;
				$post = $car_rental_result;
				setup_postdata( $post ); 
				$select_car_rentals .= "<option " . ($post->ID == $car_rental_id ? "selected" : "") . " value='$post->ID'>$post->post_title</option>";
			}
		}
		$select_car_rentals .= "</select>";
		echo $select_car_rentals;
		?>
		</div>
		<?php
		if ($car_rental_id > 0) {
			$bookings_results = $bookyourtravel_car_rental_helper->list_car_rental_bookings(null, 'Id', 'ASC', $paged, $posts_per_page, 0, $current_author_id, $car_rental_id);

			if ( count($bookings_results) > 0 && $bookings_results['total'] > 0 ) {
			
				foreach ($bookings_results['results'] as $bookings_result) {

					$booking_id = $bookings_result->Id;
					$booking_date_from = date_i18n($date_format, strtotime($bookings_result->from_day));
					$booking_date_to = date_i18n($date_format, strtotime($bookings_result->to_day)); 
					$booking_price =  $bookings_result->total_price;
					$car_rental_name = $bookings_result->car_rental_name;
					$pick_up_title = isset($bookings_result->pick_up_title) ? $bookings_result->pick_up_title : '';
					$drop_off_title = isset($bookings_result->drop_off_title) ? $bookings_result->drop_off_title : '';
					
					$booking_full_name = (isset($bookings_result->first_name) ? $bookings_result->first_name : '') . ' ' . (isset($bookings_result->last_name) ? $bookings_result->last_name : '');					
					$booking_email = $bookings_result->email;
					$booking_created_date​ = date_i18n($date_format, strtotime($bookings_result->created));						
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
								<th><?php esc_html_e('Customer name', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_full_name; ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Customer email', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_email; ?></td>
							</tr>						
							<tr>
								<th><?php esc_html_e('Date created', 'bookyourtravel'); ?>:</th>
								<td><?php echo $booking_created_date​; ?></td>
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
			?>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php 
					$total_results = $bookings_results['total'];
					BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
					?>
				</div>
			</nav>				
			<?php				
			} else {
			   echo '<p>' . esc_html__('You have not had any bookings for this car rental yet.', 'bookyourtravel') . '</p>';
			}
		}
		?>
	</section>	

<?php } elseif ($content_type == 'room_type' ) { ?>
		<script>
			window.moreInfoText = '<?php echo esc_html__('+ more info', 'bookyourtravel'); ?>';
			window.lessInfoText = '<?php echo esc_html__('+ less info', 'bookyourtravel'); ?>';
		</script>
		<!--Room list-->
		<section id="room-list" class="tab-content initial">
			<article>
				<?php
					$room_type_query = $bookyourtravel_room_type_helper->list_room_types($current_author_id, array('publish', 'private'));
					if ($room_type_query->have_posts()) {
					?>
					<ul class="room-types">
					<?php
						while ($room_type_query->have_posts()) {
							$room_type_query->the_post();
							global $post;				
							$room_type_id = intval($post->ID);
							$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
					?>
						<li id="room_type_<?php echo $room_type_id; ?>">
							<?php if ($room_type_obj->get_main_image('medium')) { ?>
								<figure class="left"><img src="<?php echo esc_url($room_type_obj->get_main_image('medium')) ?>" alt="<?php echo esc_attr($room_type_obj->get_title()); ?>" /><a href="<?php echo esc_url($room_type_obj->get_main_image()); ?>" class="image-overlay" rel="prettyPhoto[gallery1]"></a></figure>
							<?php } ?>
							<div class="meta room_type">
								<h3><?php echo $room_type_obj->get_title(); ?> <?php if ($room_type_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?></h3>
								<?php BookYourTravel_Theme_Utils::render_field('', '', '', $room_type_obj->get_custom_field('meta'), '', true, true); ?>
								<?php BookYourTravel_Theme_Utils::render_link_button("#", "more-info", "", esc_html__('+ more info', 'bookyourtravel')); ?>
							</div>
							<div class="room-information">
								<div>
									<span class="first"><?php esc_html_e('Max:', 'bookyourtravel'); ?></span>
									<span class="second">
										<?php for ( $j = 0; $j < $room_type_obj->get_max_adult_count(); $j++ ) { ?>
										<i class="material-icons">&#xE7FD;</i>
										<?php } ?>
									</span>
									<?php BookYourTravel_Theme_Utils::render_link_button($submit_room_types_url . "?fesid=" . $post->ID, "gradient-button", "", esc_html__('Edit', 'bookyourtravel')); ?>
								</div>
							</div>
							<div class="more-information">
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Room facilities:', 'bookyourtravel'), $room_type_obj->get_facilities_string(), '', true, true); ?>
								<?php echo $room_type_obj->get_description(); ?>
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Bed size:', 'bookyourtravel'), $room_type_obj->get_custom_field('bed_size'), '', true, true); ?>
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Room size:', 'bookyourtravel'), $room_type_obj->get_custom_field('room_size'), '', true, true); ?>
							</div>
						</li>
					<?php } ?>
					</ul>
				<?php }  else {
						   echo '<p>' . esc_html__('You have not submitted any room types yet.', 'bookyourtravel') . '</p>';
					  }?>
			</article>
		</section>
		
		<?php } elseif ($content_type == 'cabin_type' ) { ?>
		<script>
			window.moreInfoText = '<?php echo esc_html__('+ more info', 'bookyourtravel'); ?>';
			window.lessInfoText = '<?php echo esc_html__('+ less info', 'bookyourtravel'); ?>';
		</script>
		<!--Room list-->
		<section id="room-list" class="tab-content initial">
			<article>
				<?php
					$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types($current_author_id, array('publish', 'private'));
					if ($cabin_type_query->have_posts()) {
					?>
					<ul class="room-types cabin-types">
					<?php
						while ($cabin_type_query->have_posts()) {
							$cabin_type_query->the_post();
							global $post;				
							$cabin_type_id = intval($post->ID);
							$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
					?>
						<li id="room_type_<?php echo $cabin_type_id; ?>">
							<?php if ($cabin_type_obj->get_main_image('medium')) { ?>
								<figure class="left"><img src="<?php echo esc_url($cabin_type_obj->get_main_image('medium')) ?>" alt="<?php echo esc_attr($cabin_type_obj->get_title()); ?>" /><a href="<?php echo esc_url($cabin_type_obj->get_main_image()); ?>" class="image-overlay" rel="prettyPhoto[gallery1]"></a></figure>
							<?php } ?>
							<div class="meta room_type">
								<h3><?php echo $cabin_type_obj->get_title(); ?> <?php if ($cabin_type_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?></h3>
								<?php BookYourTravel_Theme_Utils::render_field('', '', '', $cabin_type_obj->get_custom_field('meta'), '', true, true); ?>
								<?php BookYourTravel_Theme_Utils::render_link_button("#", "more-info", "", esc_html__('+ more info', 'bookyourtravel')); ?>
							</div>
							<div class="room-information cabin-information">
								<div>
									<span class="first"><?php esc_html_e('Max:', 'bookyourtravel'); ?></span>
									<span class="second">
										<?php for ( $j = 0; $j < $cabin_type_obj->get_max_adult_count(); $j++ ) { ?>
										<i class="material-icons">&#xE7FD;</i>
										<?php } ?>
									</span>
									<?php BookYourTravel_Theme_Utils::render_link_button($submit_cabin_types_url . "?fesid=" . $post->ID, "gradient-button", "", esc_html__('Edit', 'bookyourtravel')); ?>
								</div>
							</div>
							<div class="more-information">
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Cabin facilities:', 'bookyourtravel'), $cabin_type_obj->get_facilities_string(), '', true, true); ?>
								<?php echo $cabin_type_obj->get_description(); ?>
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Bed size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('bed_size'), '', true, true); ?>
								<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Cabin size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('room_size'), '', true, true); ?>
							</div>
						</li>
					<?php } ?>
					</ul>
				<?php }  else {
						   echo '<p>' . esc_html__('You have not submitted any cabin types yet.', 'bookyourtravel') . '</p>';
					  }?>
			</article>
		</section>
		<?php } // if content_type == ?>
	</section>
<?php
	wp_reset_postdata();
	wp_reset_query();
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
?>
</div>
<?php
get_footer(); 