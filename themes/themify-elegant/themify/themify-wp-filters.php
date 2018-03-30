<?php
/**
 * Changes to WordPress behavior and interface applied by Themify framework
 *
 * @package Themify
 */

/**
 * Add Themify Settings link to admin bar
 * @since 1.1.2
 */
function themify_admin_bar() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
		'id' => 'themify-settings',
		'parent' => 'appearance',
		'title' => __( 'Themify Settings', 'themify' ),
		'href' => admin_url( 'admin.php?page=themify' )
	));
}
add_action( 'wp_before_admin_bar_render', 'themify_admin_bar' );

/**
 * Generate CSS code from Styling panel
 */
function themify_get_css() {
	$data = themify_get_data();
	$output = '';
	/**
	 * Stores CSS rules
	 * @var string
	 */
	$module_styling = '';
	if( is_array( $data ) ) {
		$new_arr = array();
		foreach( $data as $name => $value ) {
			$array = explode( '-', $name );
			$path = '';
			foreach($array as $part){
				$path .= "[$part]";
			}
			$new_arr[ $path ] = $value;
		}
		$themify_config = themify_convert_brackets_string_to_arrays( $new_arr );

		if( isset( $themify_config['styling'] ) && is_array( $themify_config['styling'] ) ) {
			foreach( $themify_config['styling'] as $nav => $value ) {
				foreach( $value as $element => $val ) {
					$temp = '';
					foreach( $val as $attribute => $v ) {
						$attribute = str_replace("_", "-", $attribute);
						if( isset( $v['value'] ) && $v['value'] != '' ) {
							switch( $attribute ) {
								case "border":
									foreach( $v['value'] as $key => $val ) {
										if( '' == $val ) {
											if( strpos( $key, 'style' ) === false ) {
												if ( strpos( $key, 'color' ) === false ) {
													$v['value'][$key] = 0;
												} else {
													$v['value'][$key] = '000000';
												}
											} else {
												$v['value'][$key] = 'solid';
											}
										}
									}
									if( isset( $v['value']['checkbox'] ) && $v['value']['checkbox'] ) {
										$temp .= "border: ".$v['value']['same']."px ".$v['value']['same_style']." #".$v['value']['same_color'].";\n";
									} else {
										if( isset( $v['value']['top'] ) && isset( $v['value']['top_style'] ) && isset( $v['value']['top_color'] ) && $v['value']['top'] != '' && $v['value']['top_style'] != '' && $v['value']['top_color'] != '' ) {
											$temp .= "border-top: ".$v['value']['top']."px ".$v['value']['top_style']." #".$v['value']['top_color'].";\n";
										}
										if ( isset( $v['value']['right'] ) && isset( $v['value']['right_style'] ) && isset( $v['value']['right_color'] ) && $v['value']['right'] != '' && $v['value']['right_style'] != '' && $v['value']['right_color'] != '' ) {
											$temp .= "border-right: ".$v['value']['right']."px ".$v['value']['right_style']." #".$v['value']['right_color'].";\n";
										}
										if ( isset( $v['value']['bottom'] ) && isset( $v['value']['bottom_style'] ) && isset( $v['value']['bottom_color'] ) && $v['value']['bottom'] != '' && $v['value']['bottom_style'] != '' && $v['value']['bottom_color'] != '' ) {

											$temp .= "border-bottom: ".$v['value']['bottom']."px ".$v['value']['bottom_style']." #".$v['value']['bottom_color'].";\n";
										}
										if ( isset( $v['value']['left'] ) && isset( $v['value']['left_style'] ) && isset( $v['value']['left_color'] ) && $v['value']['left'] != '' && $v['value']['left_style'] != '' && $v['value']['left_color'] != '' ) {
											$temp .= "border-left: ".$v['value']['left']."px ".$v['value']['left_style']." #".$v['value']['left_color'].";\n";
										}
									}
								break;
								case "background-position":
									if ( isset( $v['value']['x'] ) && isset( $v['value']['y'] ) && $v['value']['x'] != '' && $v['value']['y'] ) {
										foreach ( $v['value'] as $key => $val ) {
											if ( $val == '' ) {
												$v['value'][$key] = 0;
											}
										}
										$temp .= $attribute.": ";
										$temp .= $v['value']['x']." ".$v['value']['y'].";\n";
									}
								break;
								case "padding":
									if ( isset( $v['value']['checkbox'] ) && $v['value']['checkbox'] ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['same']."px".";\n";
									} else {
										if ( isset( $v['value']['top'] ) &&  $v['value']['top'] != '' ) {
											$temp .= "padding-top: ".$v['value']['top']."px;\n";
										}
										if ( isset( $v['value']['right'] ) &&  $v['value']['right'] != '' ) {
											$temp .= "padding-right: ".$v['value']['right']."px;\n";
										}
										if ( isset( $v['value']['bottom'] ) &&  $v['value']['bottom'] != '' ) {
											$temp .= "padding-bottom: ".$v['value']['bottom']."px;\n";
										}
										if ( isset( $v['value']['left'] ) &&  $v['value']['left'] != '' ) {
											$temp .= "padding-left: ".$v['value']['left']."px;\n";
										}
									}
								break;
								case "margin":
									if ( isset( $v['value']['checkbox'] ) && $v['value']['checkbox'] ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['same']."px".";\n";
									} else {
										if ( isset( $v['value']['top'] ) && $v['value']['top'] != '' ) {
											$temp .= "margin-top: ".$v['value']['top']."px;\n";
										}
										if ( isset( $v['value']['right'] ) && $v['value']['right'] != '' ) {
											$temp .= "margin-right: ".$v['value']['right']."px;\n";
										}
										if ( isset( $v['value']['bottom'] ) && $v['value']['bottom'] != '' ) {
											$temp .= "margin-bottom: ".$v['value']['bottom']."px;\n";
										}
										if ( isset( $v['value']['left'] ) && $v['value']['left'] != '' ) {
											$temp .= "margin-left: ".$v['value']['left']."px;\n";
										}
									}
								break;
								case "color":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= "#".$v['value']['value'].";\n";
									}
								break;
								case "background-color":
									if ( isset( $v['value']['transparent'] ) && $v['value']['transparent'] ) {
										$temp .= $attribute.": transparent;\n";
									} elseif ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= "#".$v['value']['value'].";\n";
									}
								break;
								case "background-image":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= "url(".$v['value']['value'].")".";\n";
									} elseif ( isset( $v['value']['none'] ) && 'on' == $v['value']['none'] ) {
										$temp .= $attribute.": ";
										$temp .= "none;\n";
									}
								break;
								case "background-repeat":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['value'].";\n";
									}
								break;
								case "font-family":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";

										// check google fonts
										if ( themify_is_google_fonts( $v['value']['value'] ) ) {
											$temp .= '"' . $v['value']['value'] . '"' .";\n";
										} else {
											$temp .= $v['value']['value'] .";\n";
										}
									}
								break;
								case "line-height":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['value'].$v['value']['unit'].";\n";
									}
								break;
								case "position":
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['value'].";\n";
										if($v['value']['value'] == 'absolute' || $v['value']['value'] == 'fixed'){
											if($v['value']['x_value'] != '' && $v['value']['x_value'] != ' '){
												$temp .= $v['value']['x'].": ".$v['value']['x_value']."px;\n";
											}
											if($v['value']['y_value'] != '' && $v['value']['y_value'] != ' '){
												$temp .= $v['value']['y'].": ".$v['value']['y_value']."px;\n";
											}
										}
									}
								break;
								default:
									if ( isset( $v['value']['value'] ) && $v['value']['value'] != '' && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.": ";
										$temp .= $v['value']['value'];
										if(isset($v['value']['unit'])){
											$temp .= $v['value']['unit'];
										}
										$temp .= ";\n";
									}
								break;
							}
						}
					}
					if($temp != '' && $temp != ' '){

						$style_selector = themify_get_styling_selector('id', $element, $nav, true);
						if ( $style_selector != '' ) {
							$module_styling .= $style_selector." {\n";
							$module_styling .= $temp;
							$module_styling .= "}\n\n";
						}
					}
				}
			}
		}
	} else {
		$output = '<style type="text/css">/* ' . __('No Values in the Database', 'themify') . ' */</style>';
	}
	$module_styling_before = "<!-- modules styling -->\n<style type='text/css'>\n";
	$module_styling_after = "</style>";
	if( '' != $module_styling ){
		$output .= $module_styling_before . $module_styling . $module_styling_after;
	}
	echo "\n\n".$output;
}

/**
 * Outputs the module styling and then the Custom CSS module content.
 * @since 1.7.4
 */
function themify_output_framework_styling() {
	// Module Styling
	themify_get_css();

	// Custom CSS
	if ( $custom_css = themify_get( 'setting-custom_css' ) ) {
		echo "\n\n<!-- custom css -->\n\n<style type='text/css'>$custom_css</style>";
	}
	add_filter( 'style_loader_tag', 'themify_add_style_property', 15, 4 );
}
add_action( 'wp_head', 'themify_output_framework_styling' );

/**
 * Add property stylesheet attribute for footer styles
 */ 
function themify_add_style_property($html,$handle,$href,$media){
	global $wp_styles;
	$wp_styles->add_data( $handle, 'property', 'stylesheet' );
	return $html;
}

/**
 * Add different CSS classes to body tag.
 * Outputs:
 * 		skin name
 * 		layout
 * @param Array
 * @return Array
 * @since 1.2.2
 */
function themify_body_classes( $classes ) {
	global $themify;

	$template = get_template();
	$classes[] = 'themify-fw-' . str_replace( '.', '-', THEMIFY_VERSION );
	$classes[] = $template . '-' . str_replace( '.', '-', wp_get_theme( $template )->version );

	// Add skin name
	if( $skin = themify_is_theme_skin() ) {
		$skin_dir = explode( '/', $skin );
		$classes[] = 'skin-' . $skin_dir[sizeof( $skin_dir ) - 2];
	} else {
		$classes[] = 'skin-default';
	}

	// Browser classes
	global $is_gecko, $is_opera, $is_iphone, $is_IE, $is_winIE, $is_macIE;

	$is_android = $is_webkit = $is_ie10 = $is_ie9 = $is_ie8 = $is_ie7 = false;

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'android' ) ) {
			$is_android = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'webkit' ) ) {
			$is_webkit = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 10' ) ) {
			$is_ie10 = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) ) {
			$is_ie9 = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8' ) ) {
			$is_ie8 = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7' ) ) {
			$is_ie7 = true;
		}
	}

	$browsers = array(
		'gecko'   => $is_gecko,
		'opera'   => $is_opera,
		'iphone'  => $is_iphone,
		'android' => $is_android,
		'webkit'  => $is_webkit,
		'ie' 	  => $is_IE,
		'iewin'   => $is_winIE,
		'iemac'   => $is_macIE,
		'ie10' 	  => $is_ie10,
		'ie9' 	  => $is_ie9,
		'ie8' 	  => $is_ie8,
		'ie7' 	  => $is_ie7
	);

	$is_not_ie = true;

	foreach( $browsers as $browser => $state ) {
		if ( $state ) {
			$classes[] = $browser;
			if ( stripos( $browser, 'ie' ) !== false ) {
				$is_not_ie = false;
			}
		}
	}
	if ( $is_not_ie ) {
		$classes[] = 'not-ie';
	}

	// Add default layout and post layout
	$post_layout = themify_get('setting-default_post_layout');

	// Set content width
	if ( is_search() ) {
		$classes[] = 'default_width';
	} elseif ( is_singular() || themify_is_shop() ) {
		$classes[] = themify_check( 'content_width' ) ? themify_get( 'content_width' ) : 'default_width';
	}

	if( themify_is_query_page() ) {
		$classes[] = 'query-page';
		$classes[] = isset($themify->query_post_type) ? 'query-'.$themify->query_post_type: 'query-post';
	}

	// It's a singular view (post, page, portfolio, any custom post type)
	if ( is_singular() ) {
		// Post requires password
		if ( post_password_required( get_the_ID() ) ) {
			$classes[] = 'entry-password-required';
		}
	}

	// If still empty, set default
	if( apply_filters('themify_default_layout_condition', '' == $themify->layout) ){
		$themify->layout = apply_filters('themify_default_layout', 'sidebar1');
	}
	$classes[] = $themify->layout;

	// non-homepage pages
	if( ! ( is_home() || is_front_page() ) ) {
		$classes[] = 'no-home';
	}

	// if the page is being displayed in lightbox
	if( isset( $_GET['iframe'] ) && $_GET['iframe'] == 'true' ) {
		$classes[] = 'lightboxed';
	}

	// Set post layout for blog, archive or a query category page
	$post_query_category = isset($themify->query_category)? $themify->query_category : themify_get('query_category');
	if( is_home() || is_archive() || '' != $post_query_category || is_search() ){
		$post_layout = $themify->post_layout;
		if(apply_filters('themify_default_post_layout_condition', '' == $post_layout)){
			$post_layout = apply_filters('themify_default_post_layout', 'list-post');
		}
		$classes[] = $post_layout;
	}

	$classes[] = themify_is_touch() ? 'touch' : 'no-touch';
		
	if(themify_get('setting-lightbox_content_images')){
		$classes[] = 'themify_lightboxed_images';
	}
		
	return apply_filters('themify_body_classes', $classes);
}
add_filter( 'body_class', 'themify_body_classes' );

/**
 * Adds classes to .post based on elements enabled for the currenty entry.
 *
 * @since 2.0.4
 *
 * @param $classes
 *
 * @return array
 */
function themify_post_class( $classes ) {
	global $themify;

	$classes[] = ( ! isset($themify->hide_title) || ( isset( $themify->hide_title ) && $themify->hide_title != 'yes' ) ) ? 'has-post-title' : 'no-post-title';
	$classes[] = ( ! isset( $themify->hide_date ) || ( isset( $themify->hide_date ) && $themify->hide_date != 'yes' ) ) ? 'has-post-date' : 'no-post-date';
	$classes[] = ( ! isset( $themify->hide_meta_category ) || ( isset( $themify->hide_meta_category ) && $themify->hide_meta_category != 'yes' ) ) ? 'has-post-category' : 'no-post-category';
	$classes[] = ( ! isset( $themify->hide_meta_tag ) || ( isset( $themify->hide_meta_tag ) && $themify->hide_meta_tag != 'yes' ) ) ? 'has-post-tag' : 'no-post-tag';
	$classes[] = ( ! isset( $themify->hide_meta_comment ) || ( isset( $themify->hide_meta_comment ) && $themify->hide_meta_comment != 'yes' ) ) ? 'has-post-comment' : 'no-post-comment';
	$classes[] = ( ! isset( $themify->hide_meta_author ) || ( isset( $themify->hide_meta_author ) && $themify->hide_meta_author != 'yes' ) ) ? 'has-post-author' : 'no-post-author';

	return apply_filters( 'themify_post_classes', $classes );
}
add_filter( 'post_class', 'themify_post_class' );

if ( ! function_exists( 'themify_disable_responsive_design' ) ) :
/**
 * Disables the responsive design by removing media-queries.css file and changing viewport tag
 *
 * @since 2.1.5
 */
function themify_disable_responsive_design() {
	// Remove media-queries.css
	add_action( 'wp_enqueue_scripts', create_function( '', "wp_deregister_style( 'themify-media-queries' );" ), 20 );

	// Remove JS for IE
	remove_action( 'wp_head', 'themify_ie_enhancements' );

	// Remove meta viewport tag
	remove_action( 'wp_head', 'themify_viewport_tag' );
}
endif;
if ( 'on' == themify_get( 'setting-disable_responsive_design' ) ) {
	add_action( 'init', 'themify_disable_responsive_design' );
}

if ( ! function_exists( 'themify_wp_video_shortcode' ) ) :
/**
 * Removes height in video to replicate this fix https://github.com/markjaquith/WordPress/commit/3d8e31fb82cc1485176c89d27b736bcd9d2444ba#diff-297bf46a572d5f80513d3fed476cd2a2R1862
 *
 * @param $out
 * @param $atts
 *
 * @return mixed
 */
function themify_wp_video_shortcode( $out, $atts ) {
	$width_rule = '';
	if ( ! empty( $atts['width'] ) ) {
		$width_rule = sprintf( 'width: %dpx; ', $atts['width'] );
	}
	return preg_replace( '/<div style="(.*?)" class="wp-video">/i', '<div style="' . esc_attr( $width_rule ) . '" class="wp-video">', $out );
}
endif;
add_filter( 'wp_video_shortcode', 'themify_wp_video_shortcode', 10, 2 );

add_filter( 'embed_oembed_html', 'themify_parse_video_embed_vars', 10, 2 );

/**
 * Add extra protocols like skype: to list of allowed protocols.
 *
 * @since 2.1.8
 *
 * @param array $protocols List of protocols allowed by default by WordPress.
 *
 * @return array $protocols Updated list including extra protocols added.
 */
function themify_allow_extra_protocols( $protocols ){
	$protocols[] = 'skype';
	$protocols[] = 'sms';
	$protocols[] = 'comgooglemaps';
	$protocols[] = 'comgooglemapsurl';
	$protocols[] = 'comgooglemaps-x-callback';
	return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'themify_allow_extra_protocols' );

if( ! function_exists( 'themify_upload_mime_types' ) ) :
/**
 * Adds .svg and .svgz to list of mime file types supported by WordPress
 * @param array $existing_mime_types WordPress supported mime types
 * @return array Array extended with svg/svgz support
 * @since 1.3.9
 */
function themify_upload_mime_types( $existing_mime_types = array() ) {
	$existing_mime_types['svg'] = 'image/svg+xml';
	$existing_mime_types['svgz'] = 'image/svg+xml';
	$existing_mime_types['zip'] = 'application/zip';
	$existing_mime_types['json'] = 'application/json';
	return $existing_mime_types;
}
endif;
add_filter( 'upload_mimes', 'themify_upload_mime_types' );

/**
 * Display an additional column in categories list
 * @since 1.1.8
 */
function themify_custom_category_header( $cat_columns ) {
    $cat_columns['cat_id'] = __( 'ID', 'themify' );
    return $cat_columns;
}
add_filter( 'manage_edit-category_columns', 'themify_custom_category_header', 10, 2 );

/**
 * Display ID in additional column in categories list
 * @since 1.1.8
 */
function themify_custom_category( $null, $column, $termid ) {
	return $termid;
}
add_filter( 'manage_category_custom_column', 'themify_custom_category', 10, 3 );

/**
 * Set a default title for the front page
 *
 * @return string
 * @since 1.7.6
 */
function themify_filter_wp_title( $title, $sep ) {
	global $aioseop_options;

	if( empty( $title ) && ( is_home() || is_front_page() ) ) {
		if( class_exists( 'All_in_One_SEO_Pack' ) && '' != $aioseop_options['aiosp_home_title'] ) {
			return $aioseop_options['aiosp_home_title'];
		}
		return get_bloginfo( 'name' );
	}

	return str_replace( $sep , '', $title );
}
add_filter( 'wp_title', 'themify_filter_wp_title', 10, 2 );

/**
 * Filters the title. Removes the default separator.
 *
 * @since 2.0.2
 *
 * @param string $title Page title to be output.
 * @param string $sep Separator to search and replace.
 *
 * @return mixed
 */
function themify_wp_title( $title, $sep ) {
	return str_replace( $sep, '', $title );
}
add_filter( 'wp_title', 'themify_wp_title', 10, 2 );

/**
 * Hijacks themes passed for upgrade checking and remove those from Themify
 * @param Bool
 * @param Array $r List of themes
 * @param String $url URL of upgrade check
 * @return Array
 * @since 1.1.8
 */
function themify_hide_themes( $r, $url ){
	if ( false !== stripos( $url, 'api.wordpress.org/themes/update-check' ) ) {
		$themes = json_decode( $r['body']['themes'] );
		$themes_list = themify_get_theme_names();
		if ( is_array( $themes_list ) ) {
			foreach( $themes_list as $theme_name ){
				unset( $themes->themes->{$theme_name} );
			}
			$r['body']['themes'] = json_encode( $themes );
		}
	}
	return $r;
}
if( is_admin() )
	add_filter( 'http_request_args', 'themify_hide_themes', 5, 2);

/**
 * Add property attribute for HTML validation purpose
 * @since 2.7.3
 */
function themify_style_loader_tag( $link, $handle ) {
	if ( 'mediaelement' === $handle || 'wp-mediaelement' === $handle ) {
		$link = str_replace( "type='text/css'", "type='text/css' property='stylesheet'", $link );
	}
	return $link;
}
add_action( 'style_loader_tag', 'themify_style_loader_tag', 10, 2 );

/**
 * Add menu name as a classname to menus when "container" is missing
 *
 * @since 2.8.9
 * @return array
 */
function themify_wp_nav_menu_args_filter( $args ) {
	if ( ! $args['container'] ) {
		if( ! empty( $args['menu'] ) ) {
			$menu = wp_get_nav_menu_object( $args['menu'] );
		} elseif ( $args['theme_location'] && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args['theme_location'] ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $args['theme_location'] ] );
		}

		if ( isset( $menu ) && ! is_wp_error( $menu ) && $menu !== false ) {
			$args['menu_class'] .= ' menu-name-' . $menu->slug;
		}
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'themify_wp_nav_menu_args_filter' );

function themify_favicon_action( $data = array() ) {
	$data = themify_get_data();
	if ( isset( $data['setting-favicon'] ) && $data['setting-favicon'] != '' ) {
		$favurl = themify_https_esc($data['setting-favicon']);
		echo "\n\n".'<link href="' . esc_attr( $favurl ) . '" rel="shortcut icon" /> ';
	}
}
add_action( 'wp_head', 'themify_favicon_action' );

/**
 * Header HTML Module - Action
 * @param array $data
 */
function themify_header_html_action( $data = array() ) {
	echo "\n\n" . themify_get( 'setting-header_html' );
}
add_action( 'wp_head','themify_header_html_action' );

/**
 * Footer HTML Module - Action
 * @param array $data
 */
function themify_footer_html_action( $data = array() ) {
	echo "\n\n" . themify_get( 'setting-footer_html' );
}
add_action( 'wp_footer','themify_footer_html_action' );

if ( ! function_exists( 'themify_search_excludes_cpt' ) ) :
/**
 * Exclude Custom Post Types from Search - Filter
 *
 * @param $query
 * @return mixed
 */
function themify_search_excludes_cpt( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search ) {

		/////////////////////////////////
		// Set category search settings
		/////////////////////////////////
		$cat_search = themify_get( 'setting-search_settings' );
		if ( isset( $cat_search ) && $cat_search != '' ) {
			$query->set( 'cat', $cat_search );
		}

		//////////////////////////////////
		// Exclude pages & post types ////
		//////////////////////////////////

		// If it's not a product search, proceed: retrieve the post types.
		$types = get_post_types( array( 'exclude_from_search' => false ) );

		// Exclude posts /////////////////
		$exclude_posts = themify_get( 'setting-search_exclude_post' );
		if ( isset( $exclude_posts ) && $exclude_posts ) {
			unset( $types['post'] );
		}

		// Exclude pages /////////////////
		$exclude_pages = themify_get( 'setting-search_settings_exclude' );
		if ( isset( $exclude_pages ) && $exclude_pages ) {
			unset( $types['page'] );
		}

		// Exclude custom post types /////
		$exclude_types = apply_filters( 'themify_types_excluded_in_search', get_post_types( array(
			'_builtin' => false,
			'public' => true,
			'exclude_from_search' => false
		)));

		foreach( array_keys( $exclude_types ) as $type ) {
			$exclude_type = null;
			$exclude_type = themify_get( 'setting-search_exclude_' . $type );
			if ( isset( $exclude_type ) && $exclude_type ) {
				unset( $types[$type] );
			}
		}

		// Section post type is always excluded
		if ( isset( $types['section'] ) ) {
			unset( $types['section'] );
		}

		// Exclude Layout and Layout Part custom post types /////
		unset( $types['tbuilder_layout'] );
		unset( $types['tbuilder_layout_part'] );

		// Search for products
		if ( isset( $query->query_vars['post_type'] ) ) {
			if ( 'post' == $query->query_vars['post_type'] ) {
				unset( $query->query_vars['post_type'] );
				unset( $types['page'] );
				$types[] = 'post';
				if ( ! isset( $exclude_pages ) || ! $exclude_pages ) {
					$types[] = 'page';
				}
			} else {
				$types = array( $query->query_vars['post_type'] );
			}
		}

		// product post_type is excluded when the search is for post post_type
		if ( isset( $_GET['search-option'] ) && $_GET['search-option'] == 'post' ) {
			if ( isset( $types['product'] ) ) {
				unset( $types['product'] );
			}
		}

		// Set final query parameters ////
		$query->set('post_type', $types);
	}
	return $query;
}
endif;
add_filter( 'pre_get_posts', 'themify_search_excludes_cpt', 9 );

function themify_feed_settings_action($query){
	$data = themify_get_data();
	if( $query->is_feed ) {
		if( isset( $data['setting-feed_settings'] ) ) {
			$query->set( 'cat', $data['setting-feed_settings'] );	
		}
	}

	return $query;
}
add_filter('pre_get_posts','themify_feed_settings_action');

$themify_data = themify_get_data();
if( !isset($themify_data['setting-exclude_img_rss']) || '' == $themify_data['setting-exclude_img_rss'] ) {
	add_filter('the_content', 'themify_custom_fields_for_feeds');

/* Firefox doesn't render images to feed when select full text from admin > Settings > Reading But IE does automatically for full text.
 * So this code below will be used by firefox only to render/fetch images in feed. If we use for all then it will show images 2 times. */

	$useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if(preg_match('|Firefox/([0-9\.]+)|',$useragent)) {
		add_filter('the_excerpt_rss', 'themify_custom_fields_for_feeds');
		add_filter('the_content_feed', 'themify_custom_fields_for_feeds');
	}

	function themify_custom_fields_for_feeds( $content ) {

		global $post, $id, $themify_check;
		if(!is_feed() || $themify_check == true){
			return $content;
		}

		if(themify_check('post_image')) {
			$content = "<p><img src='" . esc_url( themify_get( 'post_image' ) ) . "'></p>" . $content;
		}
		$themify_check = false;
		return $content;
	}
}

/**
 * Show custom 404 page (function)
 */
function themify_404_display_static_page_result( $posts ) {
	remove_filter( 'posts_results', 'themify_404_display_static_page_result', 999,1 ); 
	
        $pageid = themify_get( 'setting-page_404' );

        if ( 0 != $pageid ) {
                $condition = is_main_query() && ! is_robots() && ! is_home() && ! is_feed() && ! is_search() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX );

                if ( empty( $posts ) && $condition ) {

                        $posts = array( get_post( $pageid ) );
                        add_action( 'wp', 'themify_404_header' );
                        add_filter( 'body_class', 'themify_404_body_class' );
                        add_filter( 'template_include', 'themify_404_template', 99 );

                }
                else {
                        $count = count( $posts );

                        if ( 1 === $count ) {
                                $condition = $condition && ! is_user_logged_in();
                                // Show 404 if is draft or private AND user is not logged
                                if ($condition && ('draft' == $posts[0]->post_status || 'private' == $posts[0]->post_status)) {

                                        $posts = array( get_post( $pageid ) );
                                        add_action( 'wp', 'themify_404_header' );
                                        add_filter( 'body_class', 'themify_404_body_class' );
                                        add_filter( 'template_include', 'themify_404_template', 99 );

                                }
                                // Do a 404 if the 404 page is opened directly
                                elseif ( 'page' === $posts[0]->post_type && $posts[0]->ID == $pageid ) {

                                        add_action( 'wp', 'themify_404_header' );
                                        add_filter( 'body_class', 'themify_404_body_class' );

                                }
                        }
                }
        }
	return $posts;
}

function themify_404_display_static_page_pre_get($query){
    if ($query->is_404()) {
        remove_filter( 'pre_get_posts', 'themify_404_display_static_page_pre_get', 999,1 ); 
        global $wpdb;
        if(0 != themify_get( 'setting-page_404' ) && empty($wpdb->last_result) && $query->is_main_query() && !$query->is_robots() && !$query->is_home() && ! $query->is_feed() && ! $query->is_search() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )){
            add_action( 'wp', 'themify_404_header' );
            add_filter( 'body_class', 'themify_404_body_class' );
            add_filter( 'template_include', 'themify_404_template', 99 );
            remove_filter( 'posts_results', 'themify_404_display_static_page_result', 999,1 ); 
        }
    }
    return $query;
}

if ( ! is_admin() && ! is_customize_preview() ) {
    add_filter( 'posts_results', 'themify_404_display_static_page_result', 999,1 );
    add_filter( 'pre_get_posts', 'themify_404_display_static_page_pre_get', 999,1 );
}

/**
 * Send a 404 HTTP header
 */
function themify_404_header() {
	remove_action( 'wp', 'themify_404_header' );
	status_header( 404 );
	nocache_headers();
}

/**
 * Adds the error404 class to the body classes
 */
function themify_404_body_class( $classes ) {
	remove_action( 'body_class', 'themify_404_body_class' );
	$classes[] = 'error404';
	return $classes;
}

/**
 * Set 404 page template
 */
function themify_404_template( $template ) {
	remove_action( 'template_include', 'themify_404_template', 99 );

	global $wp_query;
	global $themify;

	$pageid = themify_get( 'setting-page_404' );

	if ( $pageid > 0 ) {
		$wp_query = null;
		$wp_query = new WP_Query();
		$wp_query->query( 'page_id=' . $pageid );
		$wp_query->the_post();

		// START custom variables ($template, global $themify)

			// PAGE TEMPLATE ($template)

			$template = get_page_template();

			// PAGE LAYOUT (global $themify)

			$layout = ( themify_get( 'page_layout' ) != 'default' && themify_check( 'page_layout' ) ) ? themify_get( 'page_layout' ) : themify_get( 'setting-default_page_layout' );
			
			if ( $layout != '' ) {
				$themify->layout = $layout;
			}

			// PAGE TITLE VISIBILITY (global $themify)

			$hide_page_title = get_post_meta( $pageid, 'hide_page_title', true );

			if ( ! empty( $hide_page_title ) && 'default' != $hide_page_title ) {
				$themify->page_title = $hide_page_title;
			} else {
				$themify->page_title = themify_check( 'setting-hide_page_title' ) ? themify_get( 'setting-hide_page_title' ) : 'no';
			}
			
			if ( 'yes' == $themify->page_title ) {
				add_filter( 'woocommerce_show_page_title', '__return_false' );
			}

		// END custom variables ($template, global $themify)

		rewind_posts();
	}

	return $template;
}

/**
 * Handle Builder's JavaScript fullwidth rows, forces fullwidth rows if sidebar is disabled
 *
 * @return bool
 */
function themify_theme_fullwidth_layout_support( $support ) {
	global $themify;

	/* if Content Width option is set to Fullwidth, do not use JavaScript */
	if( themify_get( 'content_width' ) == 'full_width' ) {
		return true;
	}

	/* using sidebar-none layout, force fullwidth rows using JavaScript */
	if( $themify->layout == 'sidebar-none' ) {
		return false;
	}

	return true;
}
add_filter( 'themify_builder_fullwidth_layout_support', 'themify_theme_fullwidth_layout_support' );

/**
 * Load current skin's functions file if it exists
 *
 * @since 1.4.9
 */
function themify_theme_load_skin_functions() {
	$current_skin = themify_get( 'skin' );
	if( $current_skin ) {
		$parsed_skin = parse_url( $current_skin, PHP_URL_PATH );
		$basedir_skin = basename( dirname( $parsed_skin ) );
		if( is_file( THEME_DIR . '/skins/' . $basedir_skin . '/functions.php' ) ) {
			include THEME_DIR . '/skins/' . $basedir_skin . '/functions.php';
		}
	}
}
add_action( 'after_setup_theme', 'themify_theme_load_skin_functions', 1 );

/**
 * Adds the global variable in JS that controls the mobile menu breakpoint
 *
 * This is added separately from script in themify_mobile_menu_script() to ensure the
 * variable is always present in the page.
 */
function themify_set_global_menu_trigger_point() { ?>
	<script>
		var tf_mobile_menu_trigger_point = <?php echo themify_get( 'setting-mobile_menu_trigger_point', 1200 ); ?>;
	</script><?php
}
add_action( 'wp_head', 'themify_set_global_menu_trigger_point' );

/**
 * JavaScript code that toggles "mobile_menu_active" body class, based on browser window size
 *
 * @since 2.9.2
 */
function themify_mobile_menu_script() {
?>
<script type="text/javascript">
	function themifyMobileMenuTrigger() {
		if( document.body.clientWidth <= tf_mobile_menu_trigger_point ) {
			jQuery( 'body' ).addClass( 'mobile_menu_active' );
		} else {
			jQuery( 'body' ).removeClass( 'mobile_menu_active' );
		}
	}
	themifyMobileMenuTrigger();
	jQuery( window ).resize( themifyMobileMenuTrigger );
</script>
<?php
}
add_action( 'themify_body_start', 'themify_mobile_menu_script' );

/**
 * Prevent framework.css stylesheet from loading in the page, the stylesheet is loaded in main.js
 *
 * @return html
 */
function themify_framework_stylesheet_style_tag( $tag, $handle, $href, $media ) {
	if( 'themify-framework' == $handle ) {
		$tag = '<meta name="themify-framework-css" content="" id="themify-framework-css">' . "\n";
	}

	return $tag;
}
add_filter( 'style_loader_tag', 'themify_framework_stylesheet_style_tag', 10, 4 );