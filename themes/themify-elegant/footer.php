<?php
/**
 * Template for site footer
 * @package themify
 * @since 1.0.0
 */
?>
<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>

	<?php themify_layout_after(); //hook ?>
    </div>
	<!-- /body -->
		
	<div id="footerwrap">

		<?php themify_footer_before(); // hook ?>
		<footer id="footer" class="pagewidth">
			<?php themify_footer_start(); // hook ?>

			<div class="back-top clearfix">
				<div class="arrow-up">
					<a href="#header"><?php _e( 'Back to Top', 'themify' ); ?></a>
				</div>
			</div>

			<?php if ( is_active_sidebar( 'footer-social-widget' ) ) : ?>
				<div class="footer-social-widgets">
					<div class="social-widget">
						<?php dynamic_sidebar( 'footer-social-widget' ); ?>
					</div>
				</div>
				<!-- /.footer-social-widgets -->
			<?php endif; ?>

			<?php get_template_part( 'includes/footer-widgets' ); ?>

			<div class="footer-text clearfix">
				<?php themify_the_footer_text(); ?>
				<?php themify_the_footer_text( 'right' ); ?>
			</div>
				<!-- /footer-text -->

			<?php themify_footer_end(); // hook ?>
		</footer>
		<!-- /#footer -->
		<?php themify_footer_after(); // hook ?>

	</div>
	<!-- /#footerwrap -->
	
</div>
<!-- /#pagewrap -->

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<?php themify_body_end(); // hook ?>
<!-- wp_footer -->
<?php wp_footer(); ?>

</body>
</html>