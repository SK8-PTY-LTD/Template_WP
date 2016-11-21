<?php 
/* Template Name: Login Page
 * The template for displaying the Login page.
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

global $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$redirect_to_after_login_url = '';
if (isset($page_custom_fields['user_login_redirect_to_after_login'])) {
	$user_login_redirect_to_after_login_id = $page_custom_fields['user_login_redirect_to_after_login'][0];
	$user_login_redirect_to_after_login_id = empty($user_login_redirect_to_after_login_id) ? 0 : (int)$user_login_redirect_to_after_login_id;
	
	if ($user_login_redirect_to_after_login_id > 0) {
		$user_login_redirect_to_after_login_id = BookYourTravel_Theme_Utils::get_current_language_page_id( $user_login_redirect_to_after_login_id );
		if ($user_login_redirect_to_after_login_id > 0) {
			$redirect_to_after_login_url = get_permalink($user_login_redirect_to_after_login_id);
		}
	}
}

$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();

if (empty($redirect_to_after_login_url)) {
	$redirect_to_after_login_url = $bookyourtravel_theme_globals->get_redirect_to_after_login_page_url();
	if (!$redirect_to_after_login_url)
		$redirect_to_after_login_url = home_url('/');
}
	
$login = null;

if( isset( $_POST['log'] ) && isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {

	$is_ssl = is_ssl();

	$login = wp_signon(

		array(
			'user_login' => $_POST['log'],
			'user_password' => $_POST['pwd'],
			'remember' =>( ( isset( $_POST['rememberme'] ) && $_POST['rememberme'] ) ? true : false )
		),
		$is_ssl
	);
	
	if ( !is_wp_error( $login ) ) {
		wp_redirect( $redirect_to_after_login_url );
		exit;
	}
}

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

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
		<form id="login_form" method="post" action="<?php echo esc_url(BookYourTravel_Theme_Utils::get_current_page_url_no_query()); ?>" class="booking static-content">
			<fieldset>
				<h3><?php esc_html_e('Login', 'bookyourtravel'); ?></h3>
				<p class="">
					<?php esc_html_e('Don\'t have an account yet?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($register_page_url); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?></a>. <?php esc_html_e('Forgotten your password?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($reset_password_page_url); ?>"><?php esc_html_e('Reset it here', 'bookyourtravel'); ?></a>.
				</p>
				<?php if( is_wp_error( $login ) ) { 
					echo '<p class="error">' . esc_html__('Incorrect username or password. Please try again.', 'bookyourtravel') . '</p>';
				} 
				?>
				<div class="row">
					<div class="f-item one-half">
						<label for="log"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
						<input type="text" name="log" id="log" value="" />
					</div>
					<div class="f-item one-half">
						<label for="pwd"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
						<input type="password" name="pwd" id="pwd" value="" />
					</div>
					<div class="f-item full-width checkbox">
						<input type="checkbox" name="rememberme" name="rememberme">
						<label for="rememberme"><?php esc_html_e( 'Remember Me', 'bookyourtravel' ); ?> </label>
					</div>
					<div class="f-item full-width">
						<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />
						<input type="submit" id="login" name="login" value="<?php esc_attr_e('Login', 'bookyourtravel'); ?>" class="gradient-button"/>
					</div>
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