<?php 
global $login_page_url, $reset_password_page_url;
?>	
<div class="lightbox" style="display:none;" id="login_lightbox">
	<div class="lb-wrap">
		<a href="javascript:void(0);" class="close toggle_lightbox login_lightbox">x</a>
		<div class="lb-content">
			<form action="<?php echo esc_url( $login_page_url ); ?>" method="post">
				<h2><?php esc_html_e('Log in', 'bookyourtravel'); ?></h2>
				<div class="row">
					<div class="f-item full-width">
						<label for="log"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
						<input type="text" name="log" id="log" value="" />
					</div>
					<div class="f-item full-width">
						<label for="pwd"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
						<input type="password" id="pwd" name="pwd" />
					</div>
					<div class="f-item checkbox full-width">
						<input type="checkbox" id="rememberme" name="rememberme" checked="checked" value="forever" />
						<label for="rememberme"><?php esc_html_e('Remember me next time', 'bookyourtravel'); ?></label>
					</div>
				</div>
				<p><a href="<?php echo esc_url($reset_password_page_url); ?>" title="<?php esc_attr_e('Forgot your password?', 'bookyourtravel'); ?>"><?php esc_html_e('Forgot your password?', 'bookyourtravel'); ?></a><br />
				<?php esc_html_e("Don't have an account yet?", 'bookyourtravel'); ?> <a class="toggle_lightbox register_lightbox" href="javascript:void(0);" title="<?php esc_attr_e('Sign up', 'bookyourtravel'); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?>.</a></p>
				<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" />
				<input type="submit" id="login" name="login" value="<?php esc_attr_e('Login', 'bookyourtravel'); ?>" class="gradient-button"/>
			</form>
		</div>
	</div>
</div>