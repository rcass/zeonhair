<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">

<!-- wp_header -->
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php themify_body_start(); // hook ?>
<div id="pagewrap" class="hfeed site">

	<div id="headerwrap">

		<?php themify_header_before(); // hook ?>

		<header id="header" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPHeader">

        	<?php themify_header_start(); // hook ?>

      <!-- Header Widget Area -->
      <div class="header-widget-container">
        <?php dynamic_sidebar( 'header-widget' ); ?>
      </div>

			<?php // echo themify_logo_image(); ?>
			<?php if ( $site_desc = get_bloginfo( 'description' ) ) : ?>
				<?php global $themify_customizer; ?>
				<div id="site-description" class="site-description"><?php echo class_exists( 'Themify_Customizer' ) ? $themify_customizer->site_description( $site_desc ) : $site_desc; ?></div>
			<?php endif; ?>

			<a id="menu-icon" href="#sidr" data-uk-offcanvas="{target:'#sidr'}"></a>
			<nav id="sidr" class="uk-offcanvas">
				<div class="uk-offcanvas-bar uk-offcanvas-bar-flip">

					<div class="social-widget">
						<?php dynamic_sidebar('social-widget'); ?>

						<?php if ( ! themify_check( 'setting-exclude_rss' ) ) : ?>
							<div class="rss"><a href="<?php echo themify_get( 'setting-custom_feed_url' ) != '' ? themify_get( 'setting-custom_feed_url' ) : get_bloginfo( 'rss2_url' ); ?>" class="hs-rss-link"><i class="fa fa-rss"></i></a></div>
						<?php endif ?>
					</div>
					<!-- /.social-widget -->

					<div id="searchform-wrap">
						<?php if(!themify_check('setting-exclude_search_form')): ?>
							<?php get_search_form(); ?>
						<?php endif ?>
					</div>
					<!-- /searchform-wrap -->

					<nav itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
						<?php themify_theme_menu_nav(); ?>
						<!-- /#main-nav -->
					</nav>

					<a id="menu-icon-close" href="#sidr"></a>

				</div>
			</nav>

			<?php themify_header_end(); // hook ?>

		</header>
		<!-- /#header -->

        <?php themify_header_after(); // hook ?>

	</div>
	<!-- /#headerwrap -->

	<div id="body" class="clearfix">

		<?php themify_layout_before(); //hook ?>
