<?php
/**
 * Template for single post view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="featured-area <?php echo themify_theme_featured_area_style(); ?>">

	<?php if ( $themify->hide_image != 'yes' ) : ?>

		<?php themify_before_post_image(); // Hook ?>

		<?php if ( themify_get( 'video_url' ) != '' ) : ?>

			<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
			?>

		<?php elseif( $post_image = themify_get_image($themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ) : ?>

			<figure class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
				<?php endif; // unlink image ?>
			</figure>

		<?php endif; // video else image ?>

		<?php themify_after_post_image(); // Hook ?>

	<?php endif; // hide image ?>

</div>

<!-- layout-container -->
<div id="layout" class="pagewidth clearfix">

	<?php themify_content_before(); // hook ?>
	<!-- content -->
	<div id="content" class="list-post">
    	<?php themify_content_start(); // hook ?>

		<?php get_template_part( 'includes/loop' , 'single'); ?>

		<?php wp_link_pages(array('before' => '<p class="post-pagination"><strong>' . __('Pages:', 'themify') . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>

		<?php get_template_part( 'includes/author-box', 'single'); ?>

		<?php get_template_part( 'includes/post-nav'); ?>

		<?php if(!themify_check('setting-comments_posts')): ?>
			<?php comments_template(); ?>
		<?php endif; ?>

		<?php themify_content_end(); // hook ?>
	</div>
	<!-- /content -->
    <?php themify_content_after(); // hook ?>

<?php endwhile; ?>

<?php
/////////////////////////////////////////////
// Sidebar
/////////////////////////////////////////////
if ($themify->layout != 'sidebar-none'): get_sidebar(); endif; ?>

</div>
<!-- /layout-container -->

<?php get_footer(); ?>
