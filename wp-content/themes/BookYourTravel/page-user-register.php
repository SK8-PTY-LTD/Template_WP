<?php 
/* Template Name: Register Page
 * The template for displaying the Register page.
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

global $bookyourtravel_theme_globals, $login_page_url, $reset_password_page_url, $register_page_url, $my_account_page_url, $cart_page_url, $override_wp_login;

$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();
$enc_key = $bookyourtravel_theme_globals->get_enc_key();
$add_captcha_to_forms = $bookyourtravel_theme_globals->add_captcha_to_forms();
$let_users_set_pass = $bookyourtravel_theme_globals->let_users_set_pass();

global $post;
$page_id = $post->ID;
$can_frontend_submit = get_post_meta($page_id, 'user_register_can_frontend_submit', true);

$errors = array();

if( isset( $_POST['user_login'] ) &&  isset( $_POST['user_email'] ) && isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {

	// user data array
	$register_userdata = array(
		'user_login' => wp_kses( $_POST['user_login'], '' ),
		'user_email' => wp_kses( $_POST['user_email'], '' ),
		'first_name' => '',
		'last_name' => '',
		'user_url' => '',
		'description' => '',
		'email' => wp_kses( $_POST['user_email'], '' )
	);
	
	if ($let_users_set_pass) {
		$register_userdata['user_pass'] = wp_kses( $_POST['password'], '' );
		$register_userdata['confirm_pass'] = wp_kses( $_POST['repeat_password'], '' );
	} else {
		$register_userdata['user_pass'] = wp_generate_password( 10, false );
		$register_userdata['confirm_pass'] = $register_userdata['user_pass'];
	}
	
	// custom user meta array
	$register_usermeta = array(
		'agree' =>( ( isset( $_POST['checkboxagree'] ) && !empty( $_POST['checkboxagree'] ) ) ? '1' : '0' ),
		'user_activation_key' => wp_generate_password( 20, false )
	);
	
	// validation

	// validate username
	if ( trim( $register_userdata['user_login'] ) == '' ) {
		$errors['user_login'] = esc_html__( 'Username is required.', 'bookyourtravel' );
	}
	else if ( strlen( $register_userdata['user_login'] ) < 6 ) {
		$errors['user_login'] = esc_html__( 'Sorry, username must be 6 characters or more.', 'bookyourtravel' );
	}
	else if ( !validate_username( $register_userdata['user_login'] ) ) {
		$errors['user_login'] = esc_html__( 'Sorry, the username you provided is invalid.', 'bookyourtravel' );
	}
	else if ( username_exists( $register_userdata['user_login'] ) ) {
		$errors['user_login'] = esc_html__( 'Sorry, that username already exists.', 'bookyourtravel' );
	}

	if ($let_users_set_pass) {
		// validate password
		if ( trim( $register_userdata['user_pass'] ) == '' ) {
			$errors['user_pass'] = esc_html__( 'Password is required.', 'bookyourtravel' );
		}
		else if ( strlen( $register_userdata['user_pass'] ) < 6 ) {
			$errors['user_pass'] = esc_html__( 'Sorry, password must be 6 characters or more.', 'bookyourtravel' );
		}
		else if ( $register_userdata['user_pass'] !== $register_userdata['confirm_pass'] ) {
			$errors['confirm_pass'] = esc_html__( 'Password and confirm password fields must match.', 'bookyourtravel' );
		}
	}
	
	// validate user_email
	if ( !is_email( $register_userdata['user_email'] ) ) {
		$errors['user_email'] = esc_html__( 'You must enter a valid email address.', 'bookyourtravel' );
	}
	else if ( email_exists( $register_userdata['user_email'] ) ) {
		$errors['user_email'] = esc_html__( 'Sorry, that email address is already in use.', 'bookyourtravel' );
	}

	// validate agree
	if( $register_usermeta['agree'] == '0' ) {
		$errors['agree'] = esc_html__( 'You must agree to our terms &amp; conditions to sign up.', 'bookyourtravel' );
	}

	// validate captcha
	if ($add_captcha_to_forms) {
		if (isset($_POST['c_val_s_reg']) && isset($_POST['c_val_1_reg']) && isset($_POST['c_val_2_reg'])) {
			$c_val_s = intval(wp_kses($_POST['c_val_s_reg'], array()));
			$c_val_1 = intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_POST['c_val_1_reg'], array()), $enc_key));
			$c_val_2 = intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_POST['c_val_2_reg'], array()), $enc_key));
			
			if ($c_val_s != ($c_val_1 + $c_val_2)) {
				$errors['captcha'] = esc_html__( 'You must input the correct captcha answer.', 'bookyourtravel' );
			}
		} else {
			$errors['captcha'] = esc_html__( 'You must input a valid captcha answer.', 'bookyourtravel' );
		}
	}

	if( empty( $errors ) ) {
		
		// insert new user
		$new_user_id = wp_insert_user( $register_userdata );
		
		$new_user = get_userdata( $new_user_id );
		
		$user_obj = new WP_User($new_user_id);		
		
		// Temporarily save plaintext pass
		update_user_meta( $new_user_id, 'user_pass', $register_userdata['user_pass'] );

		$user_obj->set_role( 'pending' );
		
		if ($can_frontend_submit) {
			// Set user meta temporarily (to retrieve later when activating).
			update_user_meta( $new_user_id, 'user_can_frontend_submit', 1 );
		}		

		// update custom user meta
		foreach ( $register_usermeta as $key => $value ) {
			update_user_meta( $new_user_id, $key, $value );
		}

		// send notification
		BookYourTravel_Theme_Utils::send_activation_notification( $new_user_id );

		// refresh
		wp_redirect( esc_url_raw (add_query_arg( array( 'action' => 'registered' ), get_permalink() ) ) );
		exit;
	}
}

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

$c_val_1_reg = mt_rand(1, 20);
$c_val_2_reg = mt_rand(1, 20);

$c_val_1_reg_str = BookYourTravel_Theme_Utils::encrypt($c_val_1_reg, $enc_key);
$c_val_2_reg_str = BookYourTravel_Theme_Utils::encrypt($c_val_2_reg, $enc_key);

global $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'three-fourth';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
	$section_class = 'three-fourth';
?>
<div class="row">
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>
	<section class="<?php echo esc_attr($section_class); ?>">
		<form id="register_form" method="post" action="<?php echo esc_url(BookYourTravel_Theme_Utils::get_current_page_url_no_query()); ?>" class="booking static-content">
			<fieldset>
			<?php
			/**
			 * Registration completed
			 */
			if( isset( $_GET['action'] ) && $_GET['action'] == 'registered') { 
				?>
				<p class="success">
					<?php esc_html_e( 'Account was successfully created. Please click the activation link in the email we just sent you to complete the registration process.', 'bookyourtravel' ) ?>
				</p>
			<?php
			} else if( isset( $_GET['action'] ) && $_GET['action'] == 'activate' && isset( $_GET['user_id'] ) && isset( $_GET['activation_key'] ) ) {
			/**
			 * User activation
			 */
				if( BookYourTravel_Theme_Utils::activate_user( wp_kses( $_GET['user_id'], '' ), wp_kses( $_GET['activation_key'], '' ) ) ) {
					$allowed_tags = array();
					$allowed_tags['a'] = array('class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array());
					?>
					<p class="success">
						<?php echo sprintf(wp_kses(__( 'User account successfully activated. Please proceed to the <a href="%s">login</a> page to login in.', 'bookyourtravel' ), $allowed_tags), $login_page_url); ?>
					</p>
					<?php

				} else{
					?>
					<p class="error">
						<?php esc_html_e( 'An error was encountered when attempting to activate your account.', 'bookyourtravel' ) ?>
					</p>
					<?php
				}
			} else if( isset( $_GET['action'] ) && $_GET['action'] == 'sendactivation' && isset( $_GET['user_id'] ) ) {
				/**
				 * Resend activation notification
				 */
				if( BookYourTravel_Theme_Utils::send_activation_notification( wp_kses( $_GET['user_id'], '', '' ) ) ) {
					?>
					<p class="success">
						<?php esc_html_e( 'Activation link was successfully sent.', 'bookyourtravel' ) ?>
					</p>
					<?php
				} else { ?>
					<p class="error">
						<?php esc_html_e( 'An error was encountered when attempting to send the activation link. Please try again later.', 'bookyourtravel' ) ?>
					</p>
					<?php
				}
			} else {
			?>
				<h3><?php the_title(); ?></h3>
				<p>	
				<?php esc_html_e('Already a member?', 'bookyourtravel'); ?> <?php echo sprintf(__('Proceed to <a href="%s">login</a> page', 'bookyourtravel'), esc_url($login_page_url)); ?>. <?php esc_html_e('Forgotten your password?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($reset_password_page_url); ?>"><?php esc_html_e('Reset it here', 'bookyourtravel'); ?></a>.
				</p>
				<?php
				if (count($errors) > 0) {
					?>
					<div class="error">
						<p><?php esc_html_e( 'Errors were encountered when processing your registration request.', 'bookyourtravel' ) ?></p>
						<?php foreach ($errors as $error) {
							echo '<p>' . $error . '</p>';
						} ?>
					</div>
					<?php
				}
				?>
				<div class="row">
					<div class="f-item one-half">
						<label for="user_login"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
						<input tabindex="1" type="text" id="user_login" name="user_login" value="<?php echo isset($register_userdata) ? $register_userdata['user_login'] : ''; ?>" />
						<?php if( isset( $errors['user_login'] ) ) { ?>
							<span class="input_error"><?php echo $errors['user_login']; ?></span>
						<?php } ?>
					</div>
					<div class="f-item one-half">
						<label for="user_email"><?php esc_html_e('Email', 'bookyourtravel'); ?></label>
						<input tabindex="2" type="email" id="user_email" name="user_email" value="<?php echo isset($register_userdata) ? $register_userdata['user_email'] : ''; ?>" />
						<?php if( isset( $errors['user_email'] ) ) { ?>
							<span class="input_error"><?php echo $errors['user_email']; ?></span>
						<?php } ?>
						<input type="hidden" name="email" id="email" value="" />
						<input type="hidden" name="password" id="password" value="" />
					</div>

					<?php do_action( 'woocommerce_register_form' ); ?>
					<?php do_action( 'register_form' ); ?>	
					
					<?php if ($add_captcha_to_forms) { ?>
					<div class="f-item full-width captcha">
						<label><?php echo sprintf(esc_html__('How much is %d + %d', 'bookyourtravel'), $c_val_1_reg, $c_val_2_reg) ?>?</label>
						<input tabindex="3" type="text" required="required" id="c_val_s_reg" name="c_val_s_reg" />
						<input type="hidden" name="c_val_1_reg" id="c_val_1_reg" value="<?php echo esc_attr($c_val_1_reg_str); ?>" />
						<input type="hidden" name="c_val_2_reg" id="c_val_2_reg" value="<?php echo esc_attr($c_val_2_reg_str); ?>" />
					</div>
					
					<?php } ?>
									
					<div class="f-item full-width checkbox">
						<input type="checkbox" value="ch1" id="checkboxagree" name="checkboxagree" />
						<label><?php echo sprintf(__('I agree to the <a href="%s">terms &amp; conditions</a>.', 'bookyourtravel'), esc_url($terms_page_url)); ?></label>
						<?php if( isset( $errors['agree'] ) ) { ?>
							<div class="error"><p><?php echo $errors['agree']; ?></p></div>
						<?php } ?>
					</div>
					
					<div class="f-item full-width">
						<?php wp_nonce_field( 'woocommerce-register' ); ?>
						<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
						<input type="submit" id="register" name="register" value="<?php esc_attr_e('Register', 'bookyourtravel'); ?>" class="gradient-button"/>
					</div>
				<?php } ?>
				</div>
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