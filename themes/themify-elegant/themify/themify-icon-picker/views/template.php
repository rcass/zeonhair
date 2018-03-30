<?php
/**
 * @var $icon_fonts
 */
?>
<div id="themify_lightbox_fa" class="themify-admin-lightbox clearfix">
	<input type="text" id="themify-search-icon-input" placeholder="<?php _e( 'Search', 'themify' ); ?>" />
	<h3 class="themify_lightbox_title"><?php _e( 'Choose icon', 'themify' ); ?></h3>
	<a href="#" class="close_lightbox"><i class="ti-close"></i></a>

	<div class="tf-icon-group-select">
		<?php foreach( $icon_fonts as $class => $font ) : ?>
			<label><input name="icon-font-group" type="radio" value="<?php echo $font->get_id(); ?>"><?php echo $font->get_label(); ?></input></label> 
		<?php endforeach; ?>
	</div>

	<div class="lightbox_container">

		<?php foreach( $icon_fonts as $class => $font ) : ?>
			<div class="tf-font-group" data-group="<?php echo $font->get_id(); ?>">

				<ul class="themify-lightbox-icon">
					<?php foreach( $font->get_icons() as $category ) : ?>
						<li data-id="<?php echo $font->get_id() . '-' . $category['key']; ?>">
							<span><?php echo $category['label']; ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php foreach( $font->get_icons() as $category ) : ?>
					<section id="<?php echo $font->get_id() . '-' . $category['key']; ?>">
						<h2 class="page-header"><?php echo $category['label']; ?></h2>
						<div class="row">
							<?php foreach( $category['icons'] as $icon_key => $icon_label ) : ?>
								<a href="#" data-icon="<?php echo $icon_key; ?>">
									<i class="<?php echo $font->get_classname( $icon_key ); ?>" aria-hidden="true"></i>
									<?php echo $icon_label; ?>
								</a>
							<?php endforeach; ?>
						</div>
					</section><!-- #<?php echo $font->get_id() . '-' . $category['key']; ?> -->
				<?php endforeach; ?>

			</div><!-- .tf-font-group -->
		<?php endforeach; ?>

	</div><!-- .lightbox_container -->
</div>
<div id="themify_lightbox_overlay"></div>