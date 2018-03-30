<?php
/**
 * The preview builder class.
 *
 * Handle the builder preview
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class Themify_Builder_Preview {
	private $builder;

	/**
	 * Constructor.
	 * 
	 * @access public
	 * @param instance Themify_Builder $builder
	 */
	public function __construct( Themify_Builder $builder ){
		$this->builder = $builder;

		add_action( 'template_redirect', array( $this, 'init' ) );
	}

	/**
	 * Init
	 * 
	 * @access public
	 */
	public function init() {
		if ( is_admin() || ! $this->is_preview() ) return;

		add_filter( 'show_admin_bar', '__return_false' );

		remove_filter( 'the_content', array( $this->builder, 'builder_show_on_front' ), 11);
		add_filter( 'the_content', array( $this, 'add_preview_wrapper' ), 11 );

		add_filter( 'post_class', array( $this, 'add_product_class' ), 10, 1);
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Check whether page is preview builder page.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function is_preview() {
		if ( ! isset( $_REQUEST['tb-preview'] ) ) return false;
		return true;
	}

	/**
	 * Enqueue assets js & css.
	 * 
	 * @access public
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'builder-styles', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-style.css'),array(), THEMIFY_VERSION );
		wp_enqueue_style('themify-builder-admin-ui', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui.css'), array(), THEMIFY_VERSION);
		if (is_rtl()) {
			wp_enqueue_style('themify-builder-admin-ui-rtl', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui-rtl.css'), array('themify-builder-admin-ui'), THEMIFY_VERSION);
		}
		wp_enqueue_style('google-fonts-builder', themify_https_esc('http://fonts.googleapis.com/css') . '?family=Open+Sans:400,300,600|Montserrat');
	}

	/**
	 * Add preview wrapper for builder content.
	 * 
	 * @access public
	 * @param string $content 
	 * @return string
	 */
	public function add_preview_wrapper( $content ) {
		global $post;
		// Exclude builder output in admin post list mode excerpt, Dont show builder on product single description
		if (!is_object($post) 
					|| ( is_admin() && ! defined( 'DOING_AJAX' ) )
					|| post_password_required()
					|| ( is_singular('product') && 'product' == get_post_type() )
		) {
					return $content;
		}
			
		$display = apply_filters('themify_builder_display', true, $post->ID);
		if (false === $display) {
			return $content;
		}

		$wrapper = sprintf( '<div id="themify_builder_content-%1$d" data-postid="%1$d" class="themify_builder_content themify_builder_content-%1$d themify_builder themify_builder_front"><div></div></div>', $post->ID );
		return $content . $wrapper;
	}

	/**
	 * Add body class
	 * @param array $classes 
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'themify_builder_active themify_builder_front themify_builder_responsive_frame_body';
		return $classes;
	}

	/**
	* Add product class when builder is active
	*
	* @access public
	* @return array
	*/
	public function add_product_class($classes){
		if(get_post_type()==='product'){
			$classes[] = 'product';
		}
		return $classes;
	}
}