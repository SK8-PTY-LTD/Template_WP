<?php 
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ('Please do not load this page directly. Thanks!');

$allowed_tags = array();
$allowed_tags['a'] = array('href' => array());

if ( post_password_required() ) { ?>
<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
<?php
return;
}
?><!--comments-->
<div class="comments" id="comments">
	<?php if ( have_comments() ) : ?>
	<h2><?php comments_number(esc_html__('No comments', 'bookyourtravel'), esc_html__('One comment', 'bookyourtravel'), esc_html__('% comments', 'bookyourtravel') );?></h2>
	<?php wp_list_comments('type=comment&callback=bookyourtravel_comment'); ?>
 	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'bookyourtravel' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'bookyourtravel' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'bookyourtravel' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php else : // this is displayed if there are no comments so far ?>
	 
	<?php if ('open' == $post->comment_status) : ?>
	<!-- If comments are open, but there are no comments. -->
	 
	<?php else : // comments are closed ?>
	<!-- If comments are closed. -->
	<p class="nocomments"></p>
	 
	<?php endif; ?>
	<?php endif; ?>
	
	<?php if ('open' == $post->comment_status) : ?>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p><?php echo sprintf(wp_kses(__('You must be <a href="%s/wp-login.php?redirect_to=%s">logged in</a> to post a comment.', 'bookyourtravel'), $allowed_tags), esc_url( home_url('/')), esc_url(get_permalink())); ?></p>
	<?php else : ?>

	<?php 
	
	$args = array();
	$args['logged_in_as'] = "<p>" . sprintf(wp_kses(__('Logged in as <a href="%s/wp-admin/profile.php">%s</a>.', 'bookyourtravel'), $allowed_tags), esc_url( home_url('/')), $user_identity) . ' ' . sprintf(__('<a href="%s" title="Log out of this account">Log out &raquo;</a>', 'bookyourtravel'), wp_logout_url(get_permalink())) . '</p>';

	ob_start();
	
	$allowed_tags = array();
	$allowed_tags['strong'] = array('class' => array());
	$allowed_tags['span'] = array('class' => array());
	?>
		<p><?php echo wp_kses(__('<strong>Note:</strong> Comments on the web site reflect the views of their authors, and not necessarily the views of the bookyourtravel internet portal. Requested to refrain from insults, swearing and vulgar expression. We reserve the right to delete any comment without notice explanations.', 'bookyourtravel'), $allowed_tags); ?></p>
		<p><?php echo wp_kses(__('Your email address will not be published. Required fields are signed with <span class="req">*</span>', 'bookyourtravel'), $allowed_tags); ?></p>
	<div class="row">
	<?php
	$args['comment_notes_before'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
		<div class="f-item full-width">
			<textarea id="comment" name="comment" rows="10" cols="10"></textarea>
		</div>
	<?php
	$args['comment_field'] = ob_get_contents();
	ob_end_clean();
	
	$fields =  array();
	
	ob_start();
	?>
		<div class="f-item one-half">
			<label for="author"><?php esc_html_e('Name', 'bookyourtravel'); ?> <?php if ($req) echo "*"; ?></label>			
			<input type="text" id="author" name="author" value="<?php echo esc_attr($comment_author); ?>" />
			<?php if ($req) echo '<span class="req">*</span>'; ?>
		</div>
	<?php
	$fields['author'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
		<div class="f-item one-half">
			<label for="email"><?php esc_html_e('Email', 'bookyourtravel'); ?> <?php if ($req) echo "*"; ?></label>
			<input type="email" id="email" name="email" value="<?php echo esc_attr($comment_author_email); ?>" />
			<?php if ($req) echo '<span class="req">*</span>'; ?>
		</div>
	<?php
	$fields['email'] = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	?>
		<div class="f-item full-width">
			<label for="url"><?php esc_html_e('Website', 'bookyourtravel'); ?></label>
			<input type="text" id="url" name="url" value="<?php echo esc_attr($comment_author_url); ?>" />
		</div>
	</div>
	<?php
	
	$fields['url'] = ob_get_contents();
	ob_end_clean();
	
	$args['fields'] = $fields;
	
	?>
	<div class="post-comment static-content">
	<?php
	comment_form($args); 
	?>	
	</div>
	<?php endif; /* if (get_option('comment_registration')... */ ?>	
	<?php endif; /* if ('open'... */ ?>
	
</div><!--comments-->
<!--bottom navigation-->
<div class="bottom-nav">
	<!--back up button-->
	<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
	<!--//back up button-->
</div>
<!--//bottom navigation-->