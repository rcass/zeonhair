<?php
/**
 * Portfolio Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.7
 */
function themify_theme_portfolio_meta_box( $args = array() ) {
	extract( $args );
	return array(
		// Content Width
		array(
			'name'=> 'content_width',
			'title' => __('Content Width', 'themify'),
			'description' => '',
			'type' => 'layout',
			'show_title' => true,
			'meta' => array(
				array(
					'value' => 'default_width',
					'img' => 'themify/img/default.png',
					'selected' => true,
					'title' => __( 'Default', 'themify' )
				),
				array(
					'value' => 'full_width',
					'img' => 'themify/img/fullwidth.png',
					'title' => __( 'Fullwidth', 'themify' )
				)
			),
			'default' => 'default_width'
		),
		// Content Alignment
		array(
			'name' 		=> 'content_text_align',
			'title' 		=> __( 'Content Alignment', 'themify' ),
			'description' => '',
			'type' 		=> 'radio',
			'meta'		=> array(
				array( 'value' => 'initial', 'name' => __( 'Theme Default', 'themify' ), 'selected' => true ),
				array( 'value' => 'default', 'name' => __( 'Left', 'themify' ), ),
				array( 'value' => 'center',  'name' => __( 'Center', 'themify' ) )
			),
			'default' => 'initial'
		),
		// Post Image
		array(
			'name' 		=> 'post_image',
			'title' 		=> __('Featured Image', 'themify'),
			'description' => '',
			'type' 		=> 'image',
			'meta'		=> array()
		),
		// Featured Image Size
		array(
			'name'	=>	'feature_size',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s" target="_blank">Regenerated</a>', 'themify'), 'options-media.php', 'https://wordpress.org/plugins/regenerate-thumbnails/'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
		),
	    // Gallery Shortcode
        array(
            'name' 		=> 'gallery_shortcode',
            'title' 	=> __('Gallery', 'themify'),
            'description' => '',
            'type' 		=> 'gallery_shortcode'
        ),
		// Multi field: Image Dimension
		themify_image_dimensions_field(),
		// Image Filter
		array(
			'name'        => 'imagefilter_options',
			'title'       => __( 'Image Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial'
		),
		// Image Hover Filter
		array(
			'name'        => 'imagefilter_options_hover',
			'title'       => __( 'Image Hover Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial'
		),
		// Image Filter Apply To
		array(
			'name'        => 'imagefilter_applyto',
			'title'       => __( 'Apply Filter To', 'themify' ),
			'description' => sprintf( __( 'Theme Default = can be set in <a href="%s" target="_blank">Themify > Settings > Theme Settings</a>', 'themify' ), admin_url( 'admin.php?page=themify#setting-theme_settings' ) ),
			'type'        => 'radio',
			'meta'        => array(
				array( 'value' => 'initial', 'name' => __( 'Theme Default', 'themify' ), 'selected' => true ),
				array( 'value' => 'featured-only', 'name' => __( 'Featured Images Only', 'themify' ), ),
				array( 'value' => 'all', 'name' => __( 'All Images', 'themify' ) )
			),
			'default' => 'initial'
		),
		// Hide Title
		array(
			"name" 		=> "hide_post_title",
			"title"		=> __('Hide Post Title', 'themify'),
			"description"	=> '',
			"type" 		=> 'dropdown',
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Unlink Post Title
		array(
			"name" 		=> "unlink_post_title",
			"title" 		=> __('Unlink Post Title', 'themify'),
			"description" => __('Unlink post title (it will display the post title without link)', 'themify'),
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Hide Post Date
		array(
			"name" 		=> "hide_post_date",
			"title"		=> __('Hide Post Date', 'themify'),
			"description"	=> "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Hide Post Meta
		array(
			"name" 		=> "hide_post_meta",
			"title"		=> __('Hide Post Meta', 'themify'),
			"description"	=> "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Hide Post Image
		array(
			"name" 		=> "hide_post_image",
			"title" 		=> __('Hide Featured Image', 'themify'),
			"description" => "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Unlink Post Image
		array(
			"name" 		=> "unlink_post_image",
			"title" 		=> __('Unlink Featured Image', 'themify'),
			"description" => __('Display the Featured Image without link', 'themify'),
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// External Link
		array(
			'name' 		=> 'external_link',
			'title' 		=> __('External Link', 'themify'),
			'description' => __('Link Featured Image and Post Title to external URL', 'themify'),
			'type' 		=> 'textbox',
			'meta'		=> array()
		),
		// Lightbox Link
		themify_lightbox_link_field(),
		// Separator
		array(
			'name'        => '_separator_background',
			'title'       => '',
			'description' => '',
			'type'        => 'separator',
			'meta'        => array(
				'html' => '<h4>' . __( 'Header Wrap Background', 'themify' ) . '</h4><hr class="meta_fields_separator"/>'
			),
		),
		// Header Wrap
		array(
			'name'          => 'header_wrap',
			'title'         => '',
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => 'solid',
					'name'     => __( 'Solid Background', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'transparent',
					'name'  => __( 'Transparent Background', 'themify' )
				),
			),
			'enable_toggle' => true,
			'default' => 'solid'
		),
		// Background Color
		array(
			'name'        => 'background_color',
			'title'       => __( 'Background', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'toggle'      => 'solid-toggle',
		),
		// Background image
		array(
			'name'        => 'background_image',
			'title'       => '',
			'type'        => 'image',
			'description' => '',
			'meta'        => array(),
			'before'      => '',
			'after'       => '',
			'toggle'      => 'solid-toggle',
		),
		// Background repeat
		array(
			'name'        => 'background_repeat',
			'title'       => __( 'Background Repeat', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => 'fullcover',
					'name'  => __( 'Fullcover', 'themify' )
				),
				array(
					'value' => 'repeat',
					'name'  => __( 'Repeat', 'themify' )
				),
				array(
					'value' => 'repeat-x',
					'name'  => __( 'Repeat horizontally', 'themify' )
				),
				array(
					'value' => 'repeat-y',
					'name'  => __( 'Repeat vertically', 'themify' )
				),
			),
			'toggle'      => 'solid-toggle',
			'default' => 'fullcover'
		),
		// Header wrap text color
		array(
			'name'        => 'headerwrap_text_color',
			'title'       => __( 'Header Wrap Text Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
		),
		// Header wrap link color
		array(
			'name'        => 'headerwrap_link_color',
			'title'       => __( 'Header Wrap Link Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
		),
		// Separator
		array(
			'name' => 'separator_background',
			'title' => '',
			'description' => '',
			'type' => 'separator',
			'meta' => array(
				'html' => '<h4>' . __( 'Top Featured Area in Single View', 'themify' ) . '</h4><hr class="meta_fields_separator"/>'
			),
		),
		// Background Color
		array(
			'name'        => 'featured_area_background_color',
			'title'       => __( 'Background', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
		),
		// Backgroud image
		array(
			'name'        => 'featured_area_background_image',
			'title'       => '',
			'type'        => 'image',
			'description' => '',
			'meta'        => array(),
			'before'      => '',
			'after'       => ''
		),
		// Background repeat
		array(
			'name'        => 'featured_area_background_repeat',
			'title'       => __( 'Background Repeat', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => '',
					'name'  => ''
				),
				array(
					'value' => 'fullcover',
					'name'  => __( 'Fullcover', 'themify' )
				),
				array(
					'value' => 'repeat',
					'name'  => __( 'Repeat', 'themify' )
				),
				array(
					'value' => 'repeat-x',
					'name'  => __( 'Repeat horizontally', 'themify' )
				),
				array(
					'value' => 'repeat-y',
					'name'  => __( 'Repeat vertically', 'themify' )
				),
			),
			'default' => ''
		),
		// Featured area text color
		array(
			'name'        => 'featured_area_text_color',
			'title'       => __( 'Featured Area Text Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
		),
		// Featured area link color
		array(
			'name'        => 'featured_area_link_color',
			'title'       => __( 'Featured Area Link Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
		),
	);
}

/**************************************************************************************************
 * Portfolio Class - Shortcode
 **************************************************************************************************/

if ( ! class_exists( 'Themify_Portfolio' ) ) {

	class Themify_Portfolio {

		var $instance = 0;
		var $atts = array();
		var $post_type = 'portfolio';
		var $tax = 'portfolio-category';
		var $taxonomies;

		function __construct( $args = array() ) {
			add_action( 'save_post', array($this, 'set_default_term'), 100, 2 );

			add_shortcode( $this->post_type, array( $this, 'init_shortcode' ) );
			add_shortcode( 'themify_' . $this->post_type . '_posts', array( $this, 'init_shortcode' ) );

			add_filter('themify_default_post_layout_condition', array( $this, 'post_layout_condition' ), 12);
			add_filter('themify_default_post_layout', array( $this, 'post_layout' ), 12);
			add_filter('themify_default_layout_condition', array( $this, 'layout_condition' ), 12);
			add_filter('themify_default_layout', array( $this, 'layout' ), 12);

			add_filter( "builder_is_{$this->post_type}_active", '__return_true' );
		}

		/**
		 * Set default term for custom taxonomy and assign to post
		 * @param number
		 * @param object
		 */
		function set_default_term( $post_id, $post ) {
			if ( 'publish' === $post->post_status ) {
				$terms = wp_get_post_terms( $post_id, $this->tax );
				if ( empty( $terms ) ) {
					wp_set_object_terms( $post_id, __( 'Uncategorized', 'themify' ), $this->tax );
				}
			}
		}

		/**
		 * Includes new post types registered in theme to array of post types managed by Themify
		 * @param array
		 * @return array
		 */
		function extend_post_types( $types ) {
			return array_merge( $types, array( $this->post_type ) );
		}

		/**
		 * Checks if the string begins with - like "-excluded"
		 *
		 * @param string $string String to check
		 *
		 * @return bool
		 */
		function is_negative_string( $string ) {
			return '-' === $string[0];
		}

		/**
		 * Checks if the string does not being with - like "included"
		 *
		 * @param string $string String to check
		 *
		 * @return bool
		 */
		function is_positive_string( $string ) {
			return '-' !== $string[0];
		}

		/**
		 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
		 * @param $category
		 * @param $post_type
		 * @return array
		 */
		function parse_category_args( $category, $post_type ) {
			$tax_query = array();
			if ( 'all' != $category ) {
				$terms = explode(',', $category);
				if( preg_match( '#[a-z]#', $category ) ) {
					$include = array_filter( $terms, array( $this, 'is_positive_string' ) );
					$exclude = array_filter( $terms, array( $this, 'is_negative_string' ) );
					$field = 'slug';
				} else {
					$include = array_filter( $terms, 'themify_is_positive_number' );
					$exclude = array_map( 'themify_make_absolute_number', array_filter( $terms, 'themify_is_negative_number' ) );
					$field = 'id';
				}

				if ( !empty( $include ) && !empty( $exclude ) ) {
					$tax_query = array(
						'relation' => 'AND'
					);
				}
				if ( !empty( $include ) ) {
					$tax_query[] = array(
						'taxonomy' => $post_type . '-category',
						'field'    => $field,
						'terms'    => $include,
					);
				}
				if ( !empty( $exclude ) ) {
					$tax_query[] = array(
						'taxonomy' => $post_type . '-category',
						'field'    => $field,
						'terms'    => $exclude,
						'operator' => 'NOT IN',
					);
				}
			}
			return $tax_query;
		}

		/**
		 * Setup vars when filtering posts in edit.php
		 */
		function setup_vars() {
			$this->post_type =  get_current_screen()->post_type;
			$this->taxonomies = array_diff(get_object_taxonomies($this->post_type), get_taxonomies(array('show_admin_column' => 'false')));
		}

		/**
		 * Returns link wrapped in paragraph either to the post type archive page or a custom location
		 * @param bool|string $more_link False does nothing, true goes to archive page, custom string sets custom location
		 * @param string $more_text
		 * @param string $post_type
		 * @return string
		 */
		function section_link( $more_link = false, $more_text, $post_type ) {
			if ( $more_link ) {
				if ( 'true' == $more_link ) {
					$more_link = get_post_type_archive_link( $post_type );
				}
				return '<p class="more-link-wrap"><a href="' . esc_url( $more_link ) . '" class="more-link">' . $more_text . '</a></p>';
			}
			return '';
		}

		/**
		 * Returns class to add in columns when querying multiple entries
		 * @param string $style Entries layout
		 * @return string $col_class CSS class for column
		 */
		function column_class( $style ) {
			switch ( $style ) {
				case 'grid4':
					$col_class = 'col4-1';
					break;
				case 'grid3':
					$col_class = 'col3-1';
					break;
				case 'grid2':
					$col_class = 'col2-1';
					break;
				default:
					$col_class = '';
					break;
			}
			return $col_class;
		}

		/**
		 * Add shortcode to WP
		 * @param $atts Array shortcode attributes
		 * @return String
		 * @since 1.0.0
		 */
		function init_shortcode( $atts ) {
			$this->instance++;
			$this->atts = array(
				'id' => '',
				'title' => '',
				'unlink_title' => '',
				'image' => 'yes', // no
				'image_w' => 290,
				'image_h' => 290,
				'display' => 'none', // excerpt, content
				'post_meta' => '', // yes
				'post_date' => '', // yes
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 4,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => 'grid4', // grid4, grid3, grid2
				'sorting' => 'no', // yes
				'paged' => '0', // internal use for pagination, dev: previously was 1
				'use_original_dimensions' => 'no', // yes
				'filter' => 'yes', // entry filter
			);
			if ( ! isset( $atts['image_w'] ) || '' == $atts['image_w'] ) {
				if ( ! isset( $atts['style'] ) ) {
					$atts['style'] = 'grid3';
				}
				switch ( $atts['style'] ) {
					case 'list-post':
						$this->atts['image_w'] = 1160;
						$this->atts['image_h'] = 665;
						break;
					case 'grid4':
					case '':
						$this->atts['image_w'] = 290;
						$this->atts['image_h'] = 290;
						break;
					case 'grid3':
						$this->atts['image_w'] = 387;
						$this->atts['image_h'] = 387;
						break;
					case 'grid2':
						$this->atts['image_w'] = 580;
						$this->atts['image_h'] = 580;
						break;
					case 'list-large-image':
						$this->atts['image_w'] = 800;
						$this->atts['image_h'] = 460;
						break;
					case 'list-thumb-image':
						$this->atts['image_w'] = 260;
						$this->atts['image_h'] = 150;
						break;
					case 'grid2-thumb':
						$this->atts['image_w'] = 160;
						$this->atts['image_h'] = 95;
						break;
					case 'slider':
						$this->atts['image_w'] = 1280;
						$this->atts['image_h'] = 500;
						break;
				}
			}
			$shortcode_atts = shortcode_atts( $this->atts, $atts );
			return do_shortcode( $this->shortcode( $shortcode_atts, $this->post_type ) );
		}

		/**
		 * Main shortcode rendering
		 * @param array $atts
		 * @param $post_type
		 * @return string|void
		 */
		function shortcode($atts = array(), $post_type){
			extract($atts);
			// Pagination
			global $paged;
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			// Parameters to get posts
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $limit,
				'order' => $order,
				'orderby' => $orderby,
				'suppress_filters' => false,
				'paged' => $paged
			);
			// Category parameters
			$args['tax_query'] = $this->parse_category_args($category, $post_type);

			// Defines layout type
			$cpt_layout_class = $this->post_type.'-multiple clearfix type-multiple';
			$multiple = true;

			// Single post type or many single post types
			if( '' != $id ){
				if(strpos($id, ',')){
					$ids = explode(',', str_replace(' ', '', $id));
					foreach ($ids as $string_id) {
						$int_ids[] = intval($string_id);
					}
					$args['post__in'] = $int_ids;
					$args['orderby'] = 'post__in';
				} else {
					$args['p'] = intval($id);
					$cpt_layout_class = $this->post_type.'-single';
					$multiple = false;
				}
			}

			// Get posts according to parameters
			$portfolio_query = new WP_Query();
			$posts = $portfolio_query->query(apply_filters('themify_'.$post_type.'_shortcode_args', $args));

			// Grid Style
			if( '' == $style ){
				$style = themify_check('setting-default_portfolio_index_post_layout')?
							 themify_get('setting-default_portfolio_index_post_layout'):
							 'grid4';
			}

			if( is_singular('portfolio') ){
				if( '' == $image_w ){
					$image_w = themify_check('setting-default_portfolio_single_image_post_width')?
							themify_get('setting-default_portfolio_single_image_post_width'):
							'670';
				}
				if( '' == $image_h ){
					$image_h = themify_check('setting-default_portfolio_single_image_post_height')?
							themify_get('setting-default_portfolio_single_image_post_height'):
							'0';
				}
				if( '' == $post_date ){
					$post_date = themify_check('setting-default_portfolio_single_post_date')?
							themify_get('setting-default_portfolio_index_post_date'):
							'yes';
				}
				if( '' == $title ){
					$title = themify_check('setting-default_portfolio_single_title')?
							themify_get('setting-default_portfolio_single_title'):
							'yes';
				}
				if( '' == $unlink_title ){
					$unlink_title = themify_check('setting-default_portfolio_single_unlink_post_title')?
							themify_get('setting-default_portfolio_single_unlink_post_title'):
							'no';
				}
				if( '' == $post_meta ){
					$post_meta = themify_check('setting-default_portfolio_single_meta')?
							themify_get('setting-default_portfolio_single_meta'):
							'yes';
				}
			} else {
				if( '' == $image_w ){
					$image_w = themify_check('setting-default_portfolio_index_image_post_width')?
							themify_get('setting-default_portfolio_index_image_post_width'):
							'221';
				}
				if( '' == $image_h ){
					$image_h = themify_check('setting-default_portfolio_index_image_post_height')?
							themify_get('setting-default_portfolio_index_image_post_height'):
							'221';
				}
				if( '' == $title ){
					$title = themify_check('setting-default_portfolio_index_title')?
							themify_get('setting-default_portfolio_index_title'):
							'yes';
				}
				if( '' == $unlink_title ){
					$unlink_title = themify_check('setting-default_portfolio_index_unlink_post_title')?
							themify_get('setting-default_portfolio_index_unlink_post_title'):
							'no';
				}
				// Reverse logic
				if( '' == $post_date ){
					$post_date = themify_check('setting-default_portfolio_index_post_date')?
							'no' == themify_get('setting-default_portfolio_index_post_date')?
								'yes' : 'no':
							'no';
				}
				if( '' == $post_meta ){
					$post_meta = themify_check('setting-default_portfolio_index_post_meta_category')?
							'no' == themify_get('setting-default_portfolio_index_post_meta_category')? 'yes' : 'no' :
							'no';
				}
			}

			// Collect markup to be returned
			$out = '';

			if( $posts ) {
				global $themify;
				$themify_save = clone $themify; // save a copy

				// override $themify object
				$themify->hide_title = 'yes' == $title? 'no': 'yes';
				$themify->unlink_title =  ( '' == $unlink_title || 'no' == $unlink_title )? 'no' : 'yes';
				$themify->hide_image = 'yes' == $image? 'no': 'yes';
				$themify->hide_meta = 'yes' == $post_meta? 'no': 'yes';
				$themify->hide_date = 'yes' == $post_date? 'no': 'yes';
				if(!$multiple) {
					if( '' == $image_w || get_post_meta($args['p'], 'image_width', true ) ){
						$themify->width = get_post_meta($args['p'], 'image_width', true );
					}
					if( '' == $image_h || get_post_meta($args['p'], 'image_height', true ) ){
						$themify->height = get_post_meta($args['p'], 'image_height', true );
					}
				} else {
					$themify->width = $image_w;
					$themify->height = $image_h;
				}
				$themify->use_original_dimensions = 'yes' == $use_original_dimensions? 'yes': 'no';
				$themify->display_content = $display;
				$themify->more_link = $more_link;
				$themify->more_text = $more_text;
				$themify->post_layout = $style;
				$themify->portfolio_instance = $this->instance;
				$themify->is_shortcode = true;

				// Output entry filter
				if ( 'yes' == $filter ) {
					$themify->shortcode_query_category = $category;
					$themify->shortcode_query_taxonomy = $this->tax;
					ob_start();
					get_template_part( 'includes/filter', $this->post_type );
					$out .= ob_get_contents();
					ob_end_clean();
				}

				$out .= '<div class="loops-wrapper shortcode ' . $post_type  . ' ' . $style . ' '. $cpt_layout_class .'">';
					$out .= themify_get_shortcode_template($posts, 'includes/loop-portfolio', 'index');
					$out .= $this->section_link($more_link, $more_text, $post_type);

				$out .= '</div>';

				$themify = clone $themify_save; // revert to original $themify state
				$themify->is_shortcode = false;
			}
			return $out;
		}

		/**
		 * Extract image IDs from gallery shortcode and try to return them as entries list
		 * @param string $field
		 * @return array|bool
		 * @since 1.0.0
		 */
		function get_gallery_images( $field = 'gallery_shortcode' ) {
			$gallery_shortcode = themify_get( $field );
			$image_ids = preg_replace( '#\[gallery(.*)ids="([0-9|,]*)"(.*)\]#i', '$2', $gallery_shortcode );

			$query_args = array(
				'post__in' => explode( ',', str_replace( ' ', '', $image_ids ) ),
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => -1,
				'orderby' => stripos( $gallery_shortcode, 'rand' ) ? 'rand': 'post__in',
				'order' => 'ASC'
			);
			$entries = get_posts( apply_filters( 'themify_theme_get_gallery_images', $query_args ) );

			if ( $entries ) {
				return $entries;
			}

			return false;
		}

		/**
		 * Get columns specified in gallery shortcode. Defaults to 3.
		 *
		 * @since 1.0.0
		 *
		 * @param string $field Custom field where the gallery shortcode is saved.
		 *
		 * @return string Number of columns.
		 */
		function get_gallery_columns( $field = 'gallery_shortcode' ) {
			$gallery_shortcode = themify_get( $field );
			preg_match( '#\[gallery(.*?)columns="([0-9])"(.*?)\]#i', $gallery_shortcode, $matches );

			if ( isset( $matches[2] ) && ctype_digit( $matches[2] ) ) {
				return (int) $matches[2];
			}
			return 3;
		}

		/**
		 * Get size specified in gallery shortcode. Defaults to large.
		 *
		 * @since 1.1.9
		 *
		 * @param string $field Custom field where the gallery shortcode is saved.
		 *
		 * @return string Number of columns.
		 */
		function get_gallery_size( $field = 'gallery_shortcode' ) {
			$gallery_shortcode = themify_get( $field );
			preg_match( '#\[gallery(.*?)size="([a-z]+)"(.*?)\]#i', $gallery_shortcode, $matches );
                        return isset( $matches[2] )? $matches[2]:'thumbnail';
		}

		/**
		 * Return gallery post type entries
		 * @param string $field
		 * @return array|bool
		 * @since 1.0.0
		 */
		function get_gallery_posts( $field = 'gallery_posts' ) {
			$query_term = themify_check( $field ) ? themify_get( $field ) : '0';
			$query_args = array(
				'post_type' => 'gallery',
				'posts_per_page' => 15,
			);
			if ( '0' != $query_term ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'gallery-category',
						'field' => 'slug',
						'terms' => $query_term
					)
				);
			}
			$entries = get_posts( apply_filters( 'themify_theme_get_gallery_posts', $query_args ) );

			if ( $entries ) {
				return $entries;
			}

			return false;
		}

		/**
		 * Checks if there's a description and returns it, otherwise returns caption
		 * @param $image
		 * @return mixed
		 */
		function get_description( $image ) {
			if ( '' != $image->post_content ) {
				return $image->post_content;
			}
			return $image->post_excerpt;
		}

		/**
		 * Checks if there's a caption and returns it, otherwise returns description
		 * @param $image
		 * @return mixed
		 */
		function get_caption( $image ) {
			if ( '' != $image->post_excerpt ) {
				return $image->post_excerpt;
			}
			return $image->post_content;
		}

		/**************************************************************************************************
		 * Body Classes for Portfolio index and single
		 **************************************************************************************************/

		/**
		 * Changes condition to filter post layout class
		 * @param $condition
		 * @return bool
		 */
		function post_layout_condition($condition) {
			return $condition || is_tax('portfolio-category');
		}
		/**
		 * Returns modified post layout class
		 * @return string
		 */
		function post_layout() {
			global $themify;
			// get default layout
			$class = $themify->post_layout;
			if('portfolio' == $themify->query_post_type) {
				$class = themify_check('portfolio_layout') ? themify_get('portfolio_layout') : themify_get('setting-default_post_layout');
			} elseif (is_tax('portfolio-category')) {
				$class = themify_check('setting-default_portfolio_index_post_layout')? themify_get('setting-default_portfolio_index_post_layout') : 'list-post';
			}
			return $class;
		}

		/**
		 * Changes condition to filter sidebar layout class
		 *
		 * @param bool $condition
		 * @return bool
		 */
		function layout_condition( $condition ) {
			global $themify;
			// if layout is not set or is the home page and front page displays is set to latest posts
			return $condition || ( is_home() && 'posts' == get_option( 'show_on_front' ) ) || '' != $themify->query_category || is_tax( 'portfolio-category' ) || is_singular( 'portfolio' );
		}

		/**
		 * Returns modified sidebar layout class
		 *
		 * @param string $class Original body class
		 * @return string
		 */
		function layout( $class ) {
			global $themify;
			// get default layout
			$class = $themify->layout;
			if ( is_tax( 'portfolio-category' ) ) {
				$class = themify_check( 'setting-default_portfolio_index_layout' ) ? themify_get( 'setting-default_portfolio_index_layout' ) : 'sidebar-none';
			}
			return $class;
		}
	}
}

/**************************************************************************************************
 * Initialize Type Class
 **************************************************************************************************/
$GLOBALS['themify_portfolio'] = new Themify_Portfolio();

/**************************************************************************************************
 * Themify Theme Settings Module
 **************************************************************************************************/

if ( ! function_exists( 'themify_default_portfolio_single_layout' ) ) {
	/**
	 * Default Single Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_single_layout( $data=array() ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();

		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_single_';

		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);

		/**
		 * HTML for settings panel
		 * @var string
		 */
		/**
		 * Content Alignment
		 */
		$key1 = $prefix . 'portfolio_content_text_align';
		$preset = themify_check( $key1 ) ? themify_get( $key1 ) : 'default';
		$output = '<p>
					<span class="label">' . __( 'Content Alignment', 'themify' ) . '</span>' .

			        sprintf('<label for="%1$s"><input type="radio" id="%1$s" name="%1$s" %2$s value="default" /> %3$s</label>',
					$key1, checked( $preset, 'default', false ), __( 'Left', 'themify' ) ) .
					'<br/>
					<span class="pushlabel">' .
			        sprintf('<label for="%1$s"><input type="radio" id="%1$s" name="%1$s" %2$s value="center" /> %3$s</label>',
					$key1, checked( themify_get( $key1 ), 'center', false ), __( 'Center', 'themify' ) ) . '
					</span>

				</p>';

		$output .= '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date') . '
						</select>
					</p>';
		/**
		 * Image Dimensions
		 */
		$output .= '
			<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
				<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.themify_get( $prefix.'image_post_width' ).'" /> ' . __('width', 'themify') . ' <small>(px)</small>
				<input type="text" class="width2 show_if_enabled_img_php" name="'.$prefix.'image_post_height" value="'.themify_get( $prefix.'image_post_height' ).'" /> <span class="show_if_enabled_img_php">' . __('height', 'themify') . ' <small>(px)</small></span>
			</p>';

		// Portfolio Navigation
		$prefix = 'setting-portfolio_nav_';
		$output .= '
			<p>
				<span class="label">' . __('Portfolio Navigation', 'themify') . '</span>
				<label for="'. $prefix .'disable">
					<input type="checkbox" id="'. $prefix .'disable" name="'. $prefix .'disable" '. checked( themify_get( $prefix.'disable' ), 'on', false ) .'/> ' . __('Remove Portfolio Navigation', 'themify') . '
				</label>
				<span class="pushlabel vertical-grouped">
				<label for="'. $prefix .'same_cat">
					<input type="checkbox" id="'. $prefix .'same_cat" name="'. $prefix .'same_cat" '. checked( themify_get( $prefix. 'same_cat' ), 'on', false ) .'/> ' . __('Show only portfolios in the same category', 'themify') . '
				</label>
				</span>
			</p>';

		return $output;
	}
}

if ( ! function_exists( 'themify_default_portfolio_index_layout' ) ) {
	/**
	 * Default Archive Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_index_layout( $data=array() ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();
		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_index_';
		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		/**
		 * Sidebar Layout
		 * @var string
		 */
		$layout = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
		/**
		 * Sidebar Layout Options
		 * @var array
		 */
		$sidebar_options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'selected' => true, 'title' => __('No Sidebar', 'themify')),
		);
		/**
		 * Post Layout Options
		 * @var array
		 */
		$post_layout_options = array(
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify'), 'selected' => true),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify'))
		);
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Portfolio Sidebar Option', 'themify') . '</span>';
						foreach($sidebar_options as $option){
							if ( ( '' == $layout || !$layout || ! isset( $layout ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$layout = $option['value'];
							}
							if($layout == $option['value']){
								$class = 'selected';
							} else {
								$class = '';
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}
						$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$layout.'" />';
		$output .= '</p>';

		/**
		 * Content Alignment
		 */
		$key1 = $prefix . 'portfolio_content_text_align';
		$preset = themify_check( $key1 ) ? themify_get( $key1 ) : 'default';
		$output .= '<p>
					<span class="label">' . __( 'Content Alignment', 'themify' ) . '</span>' .

			        sprintf('<label for="%1$s"><input type="radio" id="%1$s" name="%1$s" %2$s value="default" /> %3$s</label>',
					$key1, checked( $preset, 'default', false ), __( 'Left', 'themify' ) ) .
					'<br/>
					<span class="pushlabel">' .
			        sprintf('<label for="%1$s"><input type="radio" id="%1$s" name="%1$s" %2$s value="center" /> %3$s</label>',
					$key1, checked( themify_get( $key1 ), 'center', false ), __( 'Center', 'themify' ) ) . '
					</span>

				</p>';

		/**
		 * Post Layout
		 */
		$output .= '<p>
						<span class="label">' . __('Portfolio Layout', 'themify') . '</span>';

						$val = isset( $data[$prefix.'post_layout'] ) ? $data[$prefix.'post_layout'] : '';

						foreach($post_layout_options as $option){
							if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$val = $option['value'];
							}
							if ( $val == $option['value'] ) {
								$class = "selected";
							} else {
								$class = "";
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}

		$output .= '	<input type="hidden" name="'.$prefix.'post_layout" class="val" value="'.$val.'" />
					</p>';
		/**
		 * Display Content
		 */
		$output .= '<p>
						<span class="label">' . __('Display Content', 'themify') . '</span>
						<select name="'.$prefix.'display">'.
							themify_options_module(array(
								array('name' => __('None', 'themify'),'value'=>'none'),
								array('name' => __('Full Content', 'themify'),'value'=>'content'),
								array('name' => __('Excerpt', 'themify'),'value'=>'excerpt')
							), $prefix.'display').'
						</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category', true, '') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date', true, 'yes') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Image', 'themify') . '</span>
						<select name="'.$prefix.'post_image">' .
							themify_options_module($default_options, $prefix.'post_image', true, 'yes') . '
						</select>
					</p>';

		/**
		 * Image Dimensions
		 */
		$output .= '<p>
						<span class="label">' . __('Image Size', 'themify') . '</span>
						<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.themify_get( $prefix.'image_post_width' ).'" /> ' . __('width', 'themify') . ' <small>(px)</small>
						<input type="text" class="width2 show_if_enabled_img_php" name="'.$prefix.'image_post_height" value="'.themify_get( $prefix.'image_post_height' ).'" /> <span class="show_if_enabled_img_php">' . __('height', 'themify') . ' <small>(px)</small></span>
					</p>';
		return $output;
	}
}

if ( ! function_exists( 'themify_portfolio_slug' ) ) {
	/**
	 * Portfolio Slug
	 * @param array $data
	 * @return string
	 */
	function themify_portfolio_slug($data=array()){
		$data = themify_get_data();
		$portfolio_slug = isset($data['themify_portfolio_slug'])? $data['themify_portfolio_slug']: apply_filters('themify_portfolio_rewrite', 'project');
		return '
			<p>
				<span class="label">' . __('Portfolio Base Slug', 'themify') . '</span>
				<input type="text" name="themify_portfolio_slug" value="'.$portfolio_slug.'" class="slug-rewrite">
				<br />
				<span class="pushlabel"><small>' . __('Use only lowercase letters, numbers, underscores and dashes.', 'themify') . '</small></span>
				<br />
				<span class="pushlabel"><small>' . sprintf(__('After changing this, go to <a href="%s">permalinks</a> and click "Save changes" to refresh them.', 'themify'), admin_url('options-permalink.php')) . '</small></span><br />
			</p>';
	}
}

/**
 * Creates portfolio slider module
 * @return string
 */
function themify_portfolio_slider() {
	return themify_generic_slider_controls('setting-portfolio_slider_');
}