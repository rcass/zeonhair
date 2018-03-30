<?php
/**
 * Template for comments
 * @package themify
 * @since 1.0.0
 */
?>

<?php if ( post_password_required() ) : ?>
	<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'themify' ); ?></p>
<?php return;
	endif;
?>

<?php if ( comments_open() ) : ?>

	<?php themify_comment_before(); //hook ?>

	<div id="comments" class="commentwrap">

		<?php themify_comment_start(); //hook ?>

		<?php if ( have_comments() ) : ?>

			<h4 class="comment-title"><?php comments_number(__('No Comments','themify'), __('One Comment','themify'), __('% Comments','themify') );?></h4>

			<?php // Comment Pagination
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<div class="pagenav top clearfix">
					<?php paginate_comments_links( array('prev_text' => '&lt;', 'next_text' => '&gt;') );?>
				</div>
				<!-- /.pagenav -->
			<?php endif; ?>

			<ol class="commentlist">
				<?php wp_list_comments('callback=themify_theme_comment'); ?>
			</ol>

			<?php // Comment Pagination
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<div class="pagenav bottom clearfix">
					<?php paginate_comments_links( array('prev_text' => '&lt;', 'next_text' => '&gt;') );?>
				</div>
				<!-- /.pagenav -->
			<?php endif; ?>

		<?php endif; // end have_comments() ?>

		<?php comment_form(); ?>

		<?php themify_comment_end(); //hook ?>

	</div>
	<!-- /.commentwrap -->

<?php themify_comment_after(); //hook ?>

<?php endif; // end commentwrap ?>