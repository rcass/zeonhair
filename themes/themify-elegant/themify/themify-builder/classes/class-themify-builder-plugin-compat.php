<?php
/**
 * Builder Plugin Compatibility Code
 *
 * Themify_Builder_Plugin_Compat class provide code hack for some
 * plugins that need to be compatible.
 * 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

/**
 * The Builder Plugin Compatibility class.
 *
 * This class contain hook, filters, and method to hack plugins.
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class Themify_Builder_Plugin_Compat {
	
	/**
	 * Constructor.
	 * 
	 * @access public
	 */
	public function __construct() {
		global $ThemifyBuilder;

		// Hooks
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 10 );

		// WooCommerce
		if ( $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'show_builder_below_tabs'), 12 );
			add_action( 'woocommerce_archive_description', array( $this, 'wc_builder_shop_page' ), 11 );
		}

		// WPSEO live preview
		if ( $this->is_yoast_seo_active() ) {
			add_action( 'wp_ajax_wpseo_get_html_builder', array( &$this, 'wpseo_get_html_builder_ajaxify' ), 10 );
			add_filter( 'wpseo_sitemap_urlimages', array( $this, 'wpseo_sitemap_urlimages' ), 10, 2 );
		}

		// WPML compatibility
		if ( $this->is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			add_action( 'wp_ajax_themify_builder_icl_copy_from_original', array( $this, 'icl_copy_from_original' ) );
		}

		// Paid Membership Pro
		if( defined( 'PMPRO_VERSION' ) ) {
			add_filter( 'themify_builder_display', array( $this, 'pmpro_themify_builder_display' ), 10, 2 );
		}

		// Members
		if( class_exists( 'Members_Load' ) ) {
			add_filter( 'themify_builder_display', array( $this, 'members_themify_builder_display' ), 10, 2 );
		}

		// WooCommerce Membership
		if( function_exists( 'wc_memberships' ) ) {
			add_filter( 'themify_builder_display', array( $this, 'wc_memberships_themify_builder_display' ), 10, 2 );
		}

		// Duplicate Post plugin
		if ( $this->is_plugin_active( 'duplicate-post/duplicate-post.php' ) ) {
			add_filter( 'option_duplicate_post_blacklist', array( $this, 'dp_meta_backlist'), 10, 2 );
			add_action('dp_duplicate_post', array( $this, 'dp_duplicate_builder_data'), 10, 2);
			add_action('dp_duplicate_page', array( $this, 'dp_duplicate_builder_data'), 10, 2);
		}

		// BWP Minify Plugin
		// Only apply the filter when WP Multisite with subdirectory install.
		if ( $this->is_plugin_active('bwp-minify/bwp-minify.php') && defined( 'SUBDOMAIN_INSTALL' ) ) {
			if ( ! SUBDOMAIN_INSTALL ) {
				add_filter( 'bwp_minify_get_src', array( $this, 'bwp_minify_get_src' ) );
			}
		}

		// Envira Gallery
		add_filter('themify_builder_post_types_support',array($this,'themify_builder_post_types_support'),12,1);
		add_filter('themify_post_types',array($this,'themify_builder_post_types_support'),12,1);

		// WP Super Cache
		add_action( 'themify_builder_save_data', array( $this, 'wp_super_cache_purge' ), 10, 2 );

		// Thrive Builder and Thrive Leads
		add_filter( 'themify_builder_is_frontend_editor', array( $this, 'thrive_compat' ) );

		// WP Gallery Custom Links
		if( $this->is_plugin_active( 'wp-gallery-custom-links/wp-gallery-custom-links.php' ) ) {
			add_filter( 'themify_builder_image_link_before', 'wp_gallery_custom_links', 10, 3 );
		}

		// WordPress Related Posts
		if( $this->is_plugin_active( 'wordpress-23-related-posts-plugin/wp_related_posts.php' ) ) {
			add_action( 'init', array( $this, 'wp_related_posts' ) );
		}
	}

	/**
	 * WordPress Related Posts plugin compatibility
	 * @link https://wordpress.org/plugins/wordpress-23-related-posts-plugin/
	 * Display related posts after the Builder content
	 */
	function wp_related_posts() {
		remove_filter( 'the_content', 'wp_rp_add_related_posts_hook', 10 );
		add_filter( 'the_content', 'wp_rp_add_related_posts_hook', 12 );
	}

	/**
	 * Compatibility with WP Gallery Custom Links plugin
	 * @link https://wordpress.org/plugins/wp-gallery-custom-links
	 * Apply Link and Target fields to gallery images in Grid layout
	 *
	 * @return string
	 */
	function wp_gallery_custom_links( $link_before, $image, $settings ) {
		$attachment_meta = get_post_meta( $image->ID, '_gallery_link_url', true );
		if( $attachment_meta ) {
			$link_before = preg_replace( '/href="(.*)"/', 'href="' . $attachment_meta . '"', $link_before );
		}
		$attachment_meta = get_post_meta( $image->ID, '_gallery_link_target', true );
		if( $attachment_meta ) {
			$link_before = str_replace( '>', ' target="' . $attachment_meta . '">', $link_before );
		}

		return $link_before;
	}

	/**
	 * Paid Membership Pro
	 * Show Builder contents only if user has access
	 *
	 * @access public
	 * @return bool
	 */
	public function pmpro_themify_builder_display( $display, $post_id ) {
		$hasaccess = pmpro_has_membership_access( NULL, NULL, true );
		if( is_array( $hasaccess ) ) {
			//returned an array to give us the membership level values
			$post_membership_levels_ids = $hasaccess[1];
			$post_membership_levels_names = $hasaccess[2];
			$hasaccess = $hasaccess[0];
		}

		if( ! $hasaccess ) {
			return false;
		}

		return $display;
	}

	/**
	 * Members compatibility
	 * Show Builder contents only if user has access
	 *
	 * @access public
	 * @return bool
	 */
	public function members_themify_builder_display( $display, $post_id ) {
		if( ! members_can_current_user_view_post( $post_id ) ) {
			return false;
		}

		return $display;
	}

	/**
	 * WooCommerce Membership compatibility
	 * Show Builder contents only if user has access
	 *
	 * @access public
	 * @return bool
	 */
	public function wc_memberships_themify_builder_display( $display, $post_id ) {
		if( wc_memberships_is_post_content_restricted() ) {
			if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) || ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Load Builder content from original page when "Copy content" feature in WPML is used
	 *
	 * @access public
	 * @since 1.4.3
	 */
	public function icl_copy_from_original() {
		global $ThemifyBuilder, $wpdb;

		if( isset( $_POST['source_page_id'] ) && isset( $_POST['source_page_lang'] ) ) {
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s", $_POST[ 'source_page_id' ], $_POST[ 'source_page_lang' ] ) );
			$post    = get_post( $post_id );
			if ( ! empty( $post ) ) {
				$builder_data = $ThemifyBuilder->get_builder_data( $post->ID );
				include THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-meta.php';
			} else {
				echo '-1';
			}
		}
		die;
	}

	/**
	 * Make the sitemap in WP-SEO plugin count images in Builder
	 *
	 * @access public
	 * @since 1.4.2
	 * @return array
	 */
	public function wpseo_sitemap_urlimages( $images, $id ) {
		global $ThemifyBuilder;

		$modules = $ThemifyBuilder->get_flat_modules_list( $id );
		foreach( $modules as $module ) {
			$img = $this->_find_image_modules( $module );
			if( ! empty( $img ) ) {
				$images[] = $img;
			}
		}

		return $images;
	}

	/**
	 * get module settings and if it's an image module, returns an array of image url and alt text
	 * Used in wpseo_sitemap_urlimages
	 *
	 * @access public
	 * @since 1.4.2
	 * @return array
	 */
	public function _find_image_modules( $mod ) {
		if( $mod['mod_name'] == 'image' && isset( $mod['mod_settings'] ) ) {
			return array(
				'src' => isset( $mod['mod_settings']['url_image'] ) ? $mod['mod_settings']['url_image'] : '',
				'alt' => isset( $mod['mod_settings']['alt_image'] ) ? $mod['mod_settings']['alt_image'] : '',
				'title' => isset( $mod['mod_settings']['title_image'] ) ? $mod['mod_settings']['title_image'] : '',
			);
		}

		return array();
	}

	/**
	 * Render builder below WooCommerce Tabs.
	 * 
	 * @access public
	 * @return type
	 */
	public function show_builder_below_tabs() {
		global $post, $ThemifyBuilder;
		if ( ! is_singular( 'product' ) && 'product' != get_post_type() ) return;
		
		$builder_data = $ThemifyBuilder->get_builder_data( $post->ID );

		$output = $ThemifyBuilder->retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $post->ID ), '', '', false );
		echo $ThemifyBuilder->get_builder_stylesheet( $output ) . $output;
	}

	/**
	 * Show builder on Shop page.
	 * 
	 * @access public
	 */
	public function wc_builder_shop_page() {
		global $ThemifyBuilder;

		if ( is_post_type_archive( 'product' ) ) {
			$shop_page   = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {
				
				$builder_data = $ThemifyBuilder->get_builder_data( $shop_page->ID );
				$output = $ThemifyBuilder->retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $shop_page->ID ), '', '', false );
				echo $ThemifyBuilder->get_builder_stylesheet( $output ) . $output;
			}
		}
	}

	/**
	 * Load Admin Scripts.
	 * 
	 * @access public
	 * @param string $hook 
	 */
	public function load_admin_scripts( $hook ) {
		global $version, $pagenow, $current_screen;

		if (in_array($hook, array('post-new.php', 'post.php')) && in_array(get_post_type(), themify_post_types()) && Themify_Builder_Model::hasAccess()) {
			wp_enqueue_script( 'themify-builder-plugin-compat', THEMIFY_BUILDER_URI .'/js/themify.builder.plugin.compat.js', array('jquery'), $version, true );
			wp_localize_script( 'themify-builder-plugin-compat', 'TBuilderPluginCompat', apply_filters( 'themify_builder_plugin_compat_vars', array(
				'wpseo_active' => $this->is_yoast_seo_active(),
				'wpseo_builder_content_text' => __( 'Themify Builder: ', 'themify')
			)) );
		}
	}

	/**
	 * Echo builder on description tab
	 * 
	 * @access public
	 * @return void
	 */
	public function echo_builder_on_description_tabs() {
		global $post;
		echo apply_filters( 'the_content', $post->post_content );
	}

	/**
	 * Plugin Active checking
	 * 
	 * @access public
	 * @param string $plugin 
	 * @return bool
	 */
	public function is_plugin_active( $plugin ) {
		$network_active = false;
		if ( is_multisite() ) {
			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( isset( $plugins[ $plugin ] ) ) {
				$network_active = true;
			}
		}
		return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || $network_active;
	}

	/**
	 * Get html data builder.
	 * 
	 * @access public
	 */
	public function wpseo_get_html_builder_ajaxify(){
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		global $ThemifyBuilder;
		$post_id = (int) $_POST['post_id'];

		// get list of all modules in $post_id
		$builder_data = $ThemifyBuilder->get_builder_data( $post_id );
		$modules = $ThemifyBuilder->get_flat_modules_list( null, $builder_data );

		// disable builder cache
		add_filter( 'themify_builder_is_cache_active', '__return_false' );

		$string = '';
		// render each module individually
		foreach( $modules as $module ) {
			if( isset( $module['mod_name'] ) ) {
				$string .= $ThemifyBuilder->get_template_module( $module, $post_id, false, false );
			}
		}

		echo ' ' . $string;

		die();
	}

	/**
	 * Get all builder text content from module which contain text
	 * 
	 * @access public
	 * @param array $data 
	 * @return string
	 */
	public function _get_all_builder_text_content( $data ) {
		global $ThemifyBuilder;

		$data = $ThemifyBuilder->get_flat_modules_list( null, $data );
		$text = '';
		if( is_array( $data ) ) {
			foreach( $data as $module ) {
				if( isset( Themify_Builder_Model::$modules[$module['mod_name']] ) ) {
					$text .= ' ' . Themify_Builder_Model::$modules[$module['mod_name']]->get_plain_text( $module['mod_settings'] );
				}
			}
		}
		return strip_tags( strip_shortcodes( $text ) );
	}

	/**
	 * Backlist builder meta_key from duplicate post settings custom fields
	 * 
	 * @access public
	 * @param string $value 
	 * @param string $option 
	 * @return string
	 */
	public function dp_meta_backlist( $value, $option ) {
		$list_arr = explode(',', $value );
		array_push( $list_arr, '_themify_builder_settings_json');
		$value = implode( ',', $list_arr );
		return $value;
	}

	/**
	 * Action to duplicate builder data.
	 * 
	 * @access public
	 * @param int $new_id 
	 * @param object $post 
	 */
	public function dp_duplicate_builder_data( $new_id, $post ) {
		global $ThemifyBuilder, $ThemifyBuilder_Data_Manager;
		$builder_data = $ThemifyBuilder->get_builder_data( $post->ID ); // get builder data from original post
		$ThemifyBuilder_Data_Manager->save_data( $builder_data, $new_id ); // save the data for the new post
	}
        
    /**
	 * Filter builder post types compatibility
	 * 
	 * @access public
	 * @param int $new_id 
	 * @param object $post 
	 */
	public function themify_builder_post_types_support($post_types){
		$post_types = array_unique($post_types);
		$exclude = array_search('envira', $post_types);
		if($exclude!==false){
			unset($post_types[$exclude]);
		}
		$exclude = array_search('envira_album', $post_types);
		if($exclude!==false){
			unset($post_types[$exclude]);
		}
		return $post_types;
	}

	/**
	 * Modify the src for builder stylesheet.
	 * 
	 * @access public
	 * @param string $string
	 * @return string
	 */
	public function bwp_minify_get_src( $string ) {
		$split_string = explode( ',', $string );
		$found_src = array();
		foreach( $split_string as $src ) {
			if ( preg_match( '/^files\/themify-css/', $src ) ) {
				array_push( $found_src, $src );
			}
		}
		if ( count( $found_src ) > 0 ) {
			$upload_dir = wp_upload_dir();
			$base_path = substr( $upload_dir['basedir'], strpos( $upload_dir['basedir'], 'wp-content' ) );
			foreach ( $found_src as $replace_src ) {
				$key = array_search( $replace_src, $split_string );
				if ( $key !== false ) {
					$split_string[ $key ] = trailingslashit( $base_path ) . str_replace( 'files/themify-css', 'themify-css', $split_string[ $key ] );
				}
			}
			$string = implode( ',', $split_string );
		}
		return $string;
	}

	/**
	 * Clear WP Super Cache plugin cache for a post when Builder data is saved
	 *
	 * @access public
	 * @since 2.5.8
	 */
	public function wp_super_cache_purge( $builder_data, $post_id ) {
		if( function_exists( 'wp_cache_post_change' ) ) {
			wp_cache_post_change( $post_id );
		}
	}

	/**
	 * Check whether Yoast SEO (free/premium) plugin activated
	 * 
	 * @access public
	 * @return boolean
	 */
	public function is_yoast_seo_active() {
		return $this->is_plugin_active( 'wordpress-seo/wp-seo.php' ) || $this->is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' );	
	}

	/**
	 * Compatibility with Thrive Builder and Thrive Leads plugins
	 * Disables Builder's frontend editor when Thrive editor is active
	 *
	 * @return bool
	 */
	function thrive_compat( $enabled ) {
		if( function_exists( 'tve_editor_content' ) && isset( $_GET['tve'] ) && $_GET['tve'] === 'true' ) {
			$enabled = false;
		}

		return $enabled;
	}
}