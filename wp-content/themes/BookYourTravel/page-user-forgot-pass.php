<?php
/* Template Name: Reset Password Page
 * The template for displaying the reset password page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
 
if (is_user_logged_in()) {
	if ( current_user_can( 'manage_options' ) ) {
		get_header();  
		BookYourTravel_Theme_Utils::breadcrumbs();
		echo "<p class='error'>" . esc_html__('This page is not accessible when a user is logged in. You are seeing this warning because you have administrative privileges, otherwise you would be redirected to home. To access this page properly, please log out.', 'bookyourtravel') . "</p>";
		echo '<a href="' . wp_logout_url() . '">' . esc_html__('Logout', 'bookyourtravel') . '</a>';
		get_footer();
		exit;		
	} else {
		wp_redirect( home_url('/') );
		exit;
	}
}

global $bookyourtravel_theme_globals, $item_class;

$login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
$override_wp_login = $bookyourtravel_theme_globals->override_wp_login();
$register_page_url = $bookyourtravel_theme_globals->get_register_page_url();
$reset_password_page_url = $bookyourtravel_theme_globals->get_reset_password_page_url();
$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();

// Process reset password attempt on submit
if (isset($_POST['_wpnonce'])) {
	if( wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {

		// user data array
		$resetpassword_userdata = array(
			'user_email' => wp_kses( $_POST['user_email'], '', '' )
		);

		// custom user meta array
		$resetpassword_usermeta = array(
			'user_resetpassword_key' => wp_generate_password( 20, false ),
			'user_resetpassword_datetime' => date('Y-m-d H:i:s', time() )
		);	

		// validation
		$errors = array();

		// validate email
		if ( !is_email( $resetpassword_userdata['user_email'] ) ) {
			$user = get_user_by('login', $resetpassword_userdata['user_email']);
			if (!$user)
				$errors['user_email'] = esc_html__( 'You must enter a valid and existing email address or username.', 'bookyourtravel' );
		} else if ( !email_exists( $resetpassword_userdata['user_email'] ) ) {
			$errors['user_email'] = esc_html__( 'You must enter a valid and existing email address or username.', 'bookyourtravel' );
		}
		
		if( empty( $errors ) ) {

			$user = get_user_by( 'email', $resetpassword_userdata['user_email'] );

			// update custom user meta
			foreach ( $resetpassword_usermeta as $key => $value ) {
				update_user_meta( $user->ID, $key, $value );
			}

			BookYourTravel_Theme_Utils::resetpassword_notification( $user->ID );

			// refresh
			wp_redirect( esc_url_raw( add_query_arg( array( 'action' => 'resetpasswordnotification' ), get_permalink() ) ) );
			exit;
		}
	} 
}

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

global $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
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
	<section class="<?php echo esc_attr($section_class); ?>">
		<form id="reset_password_form" method="post" action="<?php echo esc_url( $reset_password_page_url ); ?>" class="booking static-content">
			<fieldset>
				<h3><?php esc_html_e('Reset password', 'bookyourtravel'); ?></h3>
				<?php 				
				if( isset( $_GET['action'] ) && $_GET['action'] == 'resetpasswordnotification' ) { 
				?>
				<p class="success">
					<?php esc_html_e( 'Please confirm the request to reset your password by clicking the link sent to your email address.', 'bookyourtravel' ) ?>
				</p>
				<?php
				} else if( isset( $_GET['action'] ) && $_GET['action'] == 'resetpassword' && isset( $_GET['user_id'] ) && isset( $_GET['resetpassword_key'] ) ) { 

					$user_id = wp_kses( $_GET['user_id'], '', '' );
					$resetpassword_key = wp_kses( $_GET['resetpassword_key'], '', '' );
					$new_password = BookYourTravel_Theme_Utils::resetpassword( $user_id, $resetpassword_key );

					if( $new_password && BookYourTravel_Theme_Utils::newpassword_notification( $user_id, $new_password ) ) { ?>
						<p class="success">
							<?php esc_html_e( 'Your password was successfully reset. We have sent the new password to your email address.', 'bookyourtravel' ) ?>
						</p>
					<?php } else { ?>
						<p class="error">
							<?php esc_html_e( 'We encountered an error when attempting to reset your password. Please try again later.', 'bookyourtravel' ) ?>
						</p>
					<?php }
				} else { ?>			
					<p>
					<?php esc_html_e('Don\'t have an account yet?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($register_page_url); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?></a>.
					</p>
					<?php if( isset( $errors['user_email'] ) ) { ?>
					<p class="error"><?php echo $errors['user_email']; ?></p>
					<?php } ?> 
					<div class="row">
						<div class="f-item full-width">
							<label for="user_email"><?php esc_html_e('Username or email address', 'bookyourtravel'); ?></label>
							<input type="text" name="user_email" id="user_email" value="" />
						</div>
						<div class="f-item full-width">
							<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
							<input type="submit" id="reset" name="reset" value="<?php esc_attr_e('Reset password', 'bookyourtravel'); ?>" class="gradient-button"/>
						</div>
					</div>
			<?php } ?>
			</fieldset>
		</form>
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