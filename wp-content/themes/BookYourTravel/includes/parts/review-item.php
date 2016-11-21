<?php
	global $entity_obj, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;
	
	$base_id = $entity_obj->get_base_id();
	$post_type = $entity_obj->get_entity_type();
	
	$reviews_total = $bookyourtravel_review_helper->get_reviews_count($base_id);
	
	$guest_reviews_info = '';
	if ($post_type == 'accommodation')
		$guest_reviews_info = esc_html__('Guest reviews are written by our customers after their stay at %s.', 'bookyourtravel');
	elseif ($post_type == 'tour')
		$guest_reviews_info = esc_html__('Guest reviews are written by our customers after their tour of %s.', 'bookyourtravel');
	elseif ($post_type == 'cruise')
		$guest_reviews_info = esc_html__('Guest reviews are written by our customers after their voyage on %s.', 'bookyourtravel');
	
	if ($reviews_total > 0) {
		$entity_type = $entity_obj->get_entity_type();
		$review_item_title = '';
		$context_option_id = '';
		
		if ($entity_type == 'accommodation') {
			$review_item_title = esc_html__('Accommodation review scores and score breakdown', 'bookyourtravel');
			$context_option_id = 'accommodation_review_fields';
		} else if ($entity_type == 'tour') {
			$review_item_title = esc_html__('Tour review scores and score breakdown', 'bookyourtravel');
			$context_option_id = 'tour_review_fields';
		} else if ($entity_type == 'cruise') {
			$review_item_title = esc_html__('Cruise review scores and score breakdown', 'bookyourtravel');	
			$context_option_id = 'cruise_review_fields';
		} else if ($entity_type == 'car_rental') {
			$review_item_title = esc_html__('Car rental review scores and score breakdown', 'bookyourtravel');
			$context_option_id = 'car_rental_review_fields';
		}
	?>
	<article>
		<h2><?php echo ucfirst($review_item_title); ?></h2>
		<div class="score">
		<?php 
			$review_score = $entity_obj->get_custom_field('review_score', false, true);
			$score_out_of_10 = round($review_score * 10);
		?>
			<span class="achieved"><?php echo esc_html($score_out_of_10); ?></span><span> / 10</span>
			<p class="info"><?php echo sprintf(esc_html__('Based on %d reviews', 'bookyourtravel'), $reviews_total); ?></p>
			<p class="disclaimer"><?php echo sprintf($guest_reviews_info, $entity_obj->get_title()); ?></p>
		</div>		
		<dl class="chart">
			<?php 
			$total_possible = $reviews_total * 10;	
			
			$review_fields = $bookyourtravel_review_helper->list_review_fields($post_type, true);
			foreach ($review_fields as $review_field) {
				$field_id = $review_field['id'];
				$field_value = intval($total_possible > 0 ? ($bookyourtravel_review_helper->sum_review_meta_values($base_id, $field_id) / $total_possible) * 10 : 0);
				
				$field_label = isset($review_field['label']) ? $review_field['label'] : '';
				$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context($context_option_id) . ' ' . $field_label, $field_label);			
			?>
			<dt><?php echo esc_html($field_label); ?></dt>
			<dd><span style="width:<?php echo $field_value * 10; ?>%;"><?php echo $field_value; ?>&nbsp;&nbsp;&nbsp;</span></dd>
			<?php
			}
			?>
		</dl>
	</article>
	<article>
		<h2><?php esc_html_e('Guest reviews', 'bookyourtravel');?></h2>
		<ul class="reviews">
			<!--review-->
			<?php
			$reviews_query = $bookyourtravel_review_helper->list_reviews($base_id);
			while ($reviews_query->have_posts()) : 
				global $post;
				$reviews_query->the_post();
			?>
			<li>
				<figure class="left"><?php echo get_avatar( get_the_author_meta( 'ID' ), 70 ); ?><address><span><?php the_author(); ?></span><br /><?php echo get_the_date('Y-m-d'); ?></address></figure>
				<div class="rev pro"><p><?php echo get_post_meta($post->ID, 'review_likes', true); ?></p></div>
				<div class="rev con"><p><?php echo get_post_meta($post->ID, 'review_dislikes', true); ?></p></div>
			</li>
			<!--//review-->
			<?php endwhile; 
				// Reset Second Loop Post Data
				wp_reset_postdata(); 
			?>
		</ul>
	</article>
<?php } else { ?>
	<article>
	<h3><?php 
	$post_type_label = '';
	switch($post_type) {
		case 'accommodation' :
			$post_type_label = esc_html__('accommodation', 'bookyourtravel');
			break;
		case 'tour' :
			$post_type_label = esc_html__('tour', 'bookyourtravel');
			break;
		case 'car_rental' :
			$post_type_label = esc_html__('car rental', 'bookyourtravel');
			break;
		case 'cruise' :
			$post_type_label = esc_html__('cruise', 'bookyourtravel');
			break;
		default :
			$post_type_label = esc_html__('accommodation', 'bookyourtravel');
			break;
	}
	echo sprintf(esc_html__('We are sorry, there are no reviews yet for this %s.', 'bookyourtravel'), $post_type_label); 
	?></h3>
	</article>
<?php }