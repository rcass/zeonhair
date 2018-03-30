<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'Themify_Pageoptions' ) ) :
class Themify_Pageoptions {

	private static $instance = null;

	public static function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	private function __construct() {
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'wp_ajax_tf_update_page_options', array( $this, 'save_pageoptions' ) );
	}

	function template_redirect() {
		$a = is_page();
		if( ! ( is_page() && ( $id = get_queried_object_id() ) && current_user_can( 'edit_page', $id ) ) ) {
			return;
		}
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10 );
		add_action( 'wp_footer', array( $this, 'themify_pageoptions_model' ), 10 );
	}

	function admin_bar_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_node( array(
			'id' => 'themify-pageoptions',
			'parent' => 'edit',
			'title' => sprintf( '<span class="themify_pageoptions_front_icon"></span> %s', __( 'Page Options', 'themify' ) ),
			'href' => '#',
			'meta' => array( 'class' => 'themify_module_options themify_pageoptions_popup' ),
		) );
	}

	function wp_enqueue_scripts() { 
		Themify_Metabox::get_instance()->admin_enqueue_scripts();
		Themify_Metabox::get_instance()->enqueue();
		wp_enqueue_style ( 'lightbox.css', THEMIFY_URI . '/css/lightbox.css' );
		wp_enqueue_script( 'lightbox.js', THEMIFY_URI . '/js/lightbox.js', array( 'jquery' ) );
		wp_enqueue_style( 'themify-pageoptions', THEMIFY_URI . "/page-options/themify-pageoptions.css" );
		wp_enqueue_script( 'themify-pageoptions', THEMIFY_URI . "/page-options/themify-pageoptions.js", array( 'jquery' ) );
		wp_localize_script( 'themify-pageoptions', 'tfPageOptionsVars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}

	function themify_pageoptions_model() {
		include_once( sprintf("%s/themify-pageoptions-popup.php", dirname( __FILE__ ) . '/includes' ) );
	}

	function get_meta_boxes() {
		global $post;

		// get Page Options from Themify Custom Panel
		$options = Themify_Metabox::get_instance()->get_meta_box_options( 'themify-meta-boxes', $post->post_type );
		if( ! empty( $options ) ) {
			foreach( $options as $id => $tab ) {
				if( ! ( isset( $tab['id'] ) && $tab['id'] == 'page-options' ) ) {
					unset( $options[$id] );
				}
			}
		}
		echo Themify_Metabox::get_instance()->render_tabs( $options, $post, 'themify-meta-boxes' );
	}

	function save_pageoptions() {
		$post_id = $_POST['post_id'];
		if( ! Themify_Metabox::get_instance()->save_postdata( $post_id ) ) {
			$response['status'] = 'success';
			$response['msg'] = get_permalink( $post_id );
		}
		else {
			$response['status'] = 'failed';
			if( ! isset( $response['msg'] ) ) {
				$response['msg'] = __('Something went wrong', 'themify');
			}
		}
		wp_send_json( $response );
	}
}
endif;
Themify_Pageoptions::get_instance();