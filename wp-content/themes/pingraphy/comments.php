<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Pingraphy
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<div class="comment-desc">
	<?php endif; ?>
	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( // WPCS: XSS OK.
					esc_html( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'pingraphy' ) ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'pingraphy' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( '<i class="fa fa-angle-double-left"></i> Older Comments', 'pingraphy' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments <i class="fa fa-angle-double-right"></i>', 'pingraphy' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation. ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'max_depth'  => '4',
					'avatar_size'=> 106,
					'reply_text' => 'Reply <i class="fa fa-angle-double-down"></i>',
					'short_ping' => true
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'pingraphy' ); ?></h2>
			<div class="nav-links">
				<div class="nav-previous"><?php previous_comments_link( esc_html__( '<i class="fa fa-angle-double-left"></i> Older Comments', 'pingraphy' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments <i class="fa fa-angle-double-right"></i>', 'pingraphy' ) ); ?></div>
			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; // Check for comment navigation. ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'pingraphy' ); ?></p>
	<?php endif; ?>

	<?php if ( have_comments() ) : ?>
	</div>
	<?php endif; ?>

	<div class="comment-form-wrap">

	<?php 
		
		$comment_args = array( 'title_reply'=>'Leave A Comment',

		'fields' => apply_filters( 'comment_form_default_fields', array(
			'author' => '<p class="comment-form-author"><input placeholder="Name'.( $req ? '*' : '' ).'" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" /></p>',   

			'email'  => '<p class="comment-form-email"><input placeholder="Email'.( $req ? '*' : '' ).'" id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" />'.'</p>',

			'url' => '<p class="comment-form-url"><input placeholder="Website" id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>' )),

			'comment_field' => '<p><textarea placeholder="Your Comment Here ..." id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>'.'</p>',

			'comment_notes_after' => '',
		);

		comment_form($comment_args);
	?>
	</div>

</div><!-- #comments -->