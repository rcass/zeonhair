<?php
/**
 * Zeon Hair (child theme) functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */
 
 /**
 * Register header widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function zeon_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html( 'Header' ),
		'id'            => 'header-widget',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'zeon_widgets_init' );