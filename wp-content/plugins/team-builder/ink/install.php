<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', 'wpsm_team_b_tr');
function wpsm_team_b_tr() {
	load_plugin_textdomain( wpshopmart_team_b_text_domain, FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
}
function wpsm_team_b_front_script() {
	wp_enqueue_style('wpsm_team_b-font-awesome-front', wpshopmart_team_b_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('wpsm_team_b_bootstrap-front', wpshopmart_team_b_directory_url.'assets/css/bootstrap-front.css');
	wp_enqueue_style('wpsm_team_b_team1', wpshopmart_team_b_directory_url.'assets/css/team1.css');
	wp_enqueue_style('wpsm_team_b_team2', wpshopmart_team_b_directory_url.'assets/css/team2.css');
}

add_action( 'wp_enqueue_scripts', 'wpsm_team_b_front_script' );
add_filter( 'widget_text', 'do_shortcode');

add_action('media_buttons_context', 'wpsm_team_b_editor_popup_content_button');
add_action('admin_footer', 'wpsm_team_b_editor_popup_content');

function wpsm_team_b_editor_popup_content_button($context) {
 $img = wpshopmart_team_b_directory_url.'assets/images/icon.png';
  $container_id = 'TEAM_B';
  $title = 'Select Team Group to insert into post';
  $context .= '<style>.wp_team_b_shortcode_button {
				background: #11CAA5 !important;
				border-color: #11CAA5 #11CAA5 #11CAA5 !important;
				-webkit-box-shadow: 0 1px 0 #11CAA5 !important;
				box-shadow: 0 1px 0 #11CAA5 !important;
				color: #fff;
				text-decoration: none;
				text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
			    }</style>
			    <a class="button button-primary wp_team_b_shortcode_button thickbox" title="Select Teams to insert into post"    href="#TB_inline?width=400&inlineId='.$container_id.'">
					<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
				Team Builder Shortcode
				</a>';
  return $context;
}

function wpsm_team_b_editor_popup_content() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#wpsm_team_b_insert').on('click', function() {
			var id = jQuery('#wpsm_team_b_insertselect option:selected').val();
			window.send_to_editor('<p>[TEAM_B id=' + id + ']</p>');
			tb_remove();
		})
	});
	</script>
<style>
.wp_team_b_shortcode_button {
    background: #11CAA5; !important;
    border-color: #11CAA5; #11CAA5 #11CAA5 !important;
    -webkit-box-shadow: 0 1px 0 #11CAA5 !important;
    box-shadow: 0 1px 0 #11CAA5 !important;
    color: #fff !important;
    text-decoration: none;
    text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
}
</style>
	<div id="TEAM_B" style="display:none;">
	  <h3>Select Team To Insert Into Post</h3>
	  <?php 
		
		$all_posts = wp_count_posts( 'team_builder')->publish;
		$args = array('post_type' => 'team_builder', 'posts_per_page' =>$all_posts);
		global $All_rac;
		$All_rac = new WP_Query( $args );			
		if( $All_rac->have_posts() ) { ?>	
			<select id="wpsm_team_b_insertselect" style="width: 100%;margin-bottom: 20px;">
				<?php
				while ( $All_rac->have_posts() ) : $All_rac->the_post(); ?>
				<?php $title = get_the_title(); ?>
				<option value="<?php echo get_the_ID(); ?>"><?php if (strlen($title) == 0) echo 'No Title Found'; else echo $title;   ?></option>
				<?php
				endwhile; 
				?>
			</select>
			<button class='button primary wp_team_b_shortcode_button' id='wpsm_team_b_insert'><?php _e('Insert Teams Shortcode', wpshopmart_team_b_text_domain); ?></button>
			<?php
		} else {
			_e('No Teams Found', wpshopmart_team_b_text_domain);
		}
		?>
	</div>
	<?php
}
?>