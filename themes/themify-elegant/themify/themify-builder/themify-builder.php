<?php
/**
 * Framework Name: Themify Builder
 * Framework URI: http://themify.me/
 * Description: Page Builder with interactive drag and drop features
 * Version: 1.0
 * Author: Themify
 * Author URI: http://themify.me
 *
 *
 * @package ThemifyBuilder
 * @category Core
 * @author Themify
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define builder constant
 */
define( 'THEMIFY_BUILDER_DIR', dirname(__FILE__) );
define( 'THEMIFY_BUILDER_MODULES_DIR', THEMIFY_BUILDER_DIR . '/modules' );
define( 'THEMIFY_BUILDER_TEMPLATES_DIR', THEMIFY_BUILDER_DIR . '/templates' );
define( 'THEMIFY_BUILDER_CLASSES_DIR', THEMIFY_BUILDER_DIR . '/classes' );
define( 'THEMIFY_BUILDER_INCLUDES_DIR', THEMIFY_BUILDER_DIR . '/includes' );
define( 'THEMIFY_BUILDER_LIBRARIES_DIR', THEMIFY_BUILDER_INCLUDES_DIR . '/libraries' );

require_once( THEMIFY_BUILDER_DIR . '/themify-builder-functions.php' );

// URI Constant
define( 'THEMIFY_BUILDER_URI', THEMIFY_URI . '/themify-builder' );

/**
 * Include builder class
 */
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-model.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-include.php');
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-layouts.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-module.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder.php' );

/**
 * Init themify builder class
 */
add_action( 'after_setup_theme', 'themify_builder_init', 15 );
function themify_builder_init() {
	global $ThemifyBuilder, $Themify_Builder_Layouts;
	if ( class_exists( 'Themify_Builder') ) {

		do_action( 'themify_builder_before_init' );

		if ( Themify_Builder_Model::builder_check() ) {
			$Themify_Builder_Layouts = new Themify_Builder_Layouts();

			$ThemifyBuilder = new Themify_Builder();
			$ThemifyBuilder->init();
		}
	} // class_exists check

	if( is_admin() ) {
		if( current_user_can( 'update_plugins' ) ) {
			include THEMIFY_BUILDER_DIR . '/themify-builder-updater.php';
		}
	}
}

if ( ! function_exists('themify_builder_edit_module_panel') ) {
	/**
	 * Hook edit module frontend panel
	 * @param $mod_name
	 * @param $mod_settings
	 */
	function themify_builder_edit_module_panel( $mod_name, $mod_settings ) {
		do_action( 'themify_builder_edit_module_panel', $mod_name, $mod_settings );
	}
}

if(!function_exists('themify_manage_builder')) {
	/**
	 * Builder Settings
	 * @param array $data
	 * @return string
	 * @since 1.2.7
	 */
	function themify_manage_builder($data=array()) {
		global $ThemifyBuilder;
		$data = themify_get_data();
		$pre = 'setting-page_builder_';
		$output = '';
		$modules = $ThemifyBuilder->get_modules( 'all' );

		foreach ($modules as $m) {
			$exclude = $pre.'exc_'.$m['id'];
			$checked = isset($data[$exclude]) && $data[$exclude] ? 'checked="checked"' : '';
			$output .= '<p>
						<span><input id="'.esc_attr( 'builder_module_'.$m['id'] ).'" type="checkbox" name="'.esc_attr( $exclude ).'" value="1" '.$checked.'/> <label for="'.esc_attr( 'builder_module_'.$m['id'] ).'">' . wp_kses_post( sprintf(__('Exclude %s module', 'themify'), $m['name'] ) ) . '</label></span>
					</p>';	
		}
		
		return $output;
	}
}

if(!function_exists('themify_manage_builder_active')) {
	/**
	 * Builder Settings
	 * @param array $data
	 * @return string
	 * @since 1.2.7
	 */
	function themify_manage_builder_active($data=array()) {
		$pre = 'setting-page_builder_';
		$output = '';
		$options = array(
			array('name' => __('Enable', 'themify'), 'value' => 'enable'),
			array('name' => __('Disable', 'themify'), 'value' =>'disable')
		);

		$output .= sprintf('<p><span class="label">%s</span><select id="%s" name="%s">%s</select>%s</p>',
			esc_html__( 'Themify Builder:', 'themify' ),
			esc_attr( $pre . 'is_active' ),
			esc_attr( $pre . 'is_active' ),
			themify_options_module( $options, $pre . 'is_active' ),
			sprintf( '<small class="pushlabel" data-show-if-element="[name=setting-page_builder_is_active]" data-show-if-value="disable">%s</small>'
			, esc_html__( 'WARNING: When Builder is disabled, all Builder content/layout will not appear. They will re-appear once Builder is enabled.', 'themify' ) )
		);

		if ( 'disable' != themify_get( $pre . 'is_active' ) ) {
			
			$output .= sprintf( '<p><label for="%s"><input type="checkbox" id="%s" name="%s"%s> %s</label></p>',
				esc_attr( $pre . 'disable_shortcuts' ),
				esc_attr( $pre . 'disable_shortcuts' ),
				esc_attr( $pre . 'disable_shortcuts' ),
				checked( 'on', themify_get( $pre . 'disable_shortcuts' ), false ),
				wp_kses_post( __( 'Disable Builder shortcuts (eg. disable shortcut like Cmd+S = save)', 'themify') )
			);
                       
		}

		return $output;
	}
}

if(!function_exists('themify_manage_builder_animation')) {
	/**
	 * Builder Setting Animations
	 * @param array $data
	 * @return string
	 * @since 2.0.0
	 */
	function themify_manage_builder_animation($data=array()) {
		$opt_data = themify_get_data();
		$pre = 'setting-page_builder_animation_';
		$options = array(
			array( 'name' => '', 'value' => '' ),
			array( 'name' => esc_html__( 'Disable on mobile & tablet', 'themify' ), 'value' =>'mobile' ),
			array( 'name' => esc_html__( 'Disable on all devices', 'themify' ), 'value' =>'all' )
		);

		$output = '';
		$output .= sprintf('<p><label for="%s" class="label">%s</label><select id="%s" name="%s">%s</select></p>',
			esc_attr( $pre . 'appearance' ),
			esc_html__( 'Appearance Animation', 'themify' ),
			esc_attr( $pre . 'appearance' ),
			esc_attr( $pre . 'appearance' ),
			themify_options_module( $options, $pre . 'appearance' )
		);
		$output .= sprintf('<p><label for="%s" class="label">%s</label><select id="%s" name="%s">%s</select></p>',
			esc_attr( $pre . 'parallax_bg' ),
			esc_html__( 'Parallax Background', 'themify' ),
			esc_attr( $pre . 'parallax_bg' ),
			esc_attr( $pre . 'parallax_bg' ),
			themify_options_module( $options, $pre . 'parallax_bg' )
		);
		$output .= sprintf('<p><label for="%s" class="label">%s</label><select id="%s" name="%s">%s</select></p>',
			esc_attr( $pre . 'parallax_scroll' ),
			esc_html__( 'Parallax Scrolling', 'themify' ),
			esc_attr( $pre . 'parallax_scroll' ),
			esc_attr( $pre . 'parallax_scroll' ),
			themify_options_module( $options, $pre . 'parallax_scroll', true, 'mobile' )
		);
		$output .= sprintf( '<span class="pushlabel"><small>%s</small></span>', 
			esc_html__( 'If animation is disabled, the element will appear static', 'themify' )
		);

		return $output;
	}
}

if(!function_exists('themify_manage_builder_responsive_design')) {
	/**
	 * Builder Setting Responsive Design
	 * @param array $data
	 * @return string
	 * @since 2.6.6
	 */
	function themify_manage_builder_responsive_design($data=array()) {
		$opt_data = themify_get_data();
		$pre = 'setting-page_builder_responsive_design_';
		$bp_tablet = ( isset( $opt_data[ $pre. 'tablet'] ) && ! empty( $opt_data[ $pre . 'tablet'] ) ) ? $opt_data[ $pre . 'tablet'] : 768;
		$bp_tablet_landscape = ( isset( $opt_data[ $pre. 'tablet_landscape'] ) && ! empty( $opt_data[ $pre . 'tablet_landscape'] ) ) ? $opt_data[ $pre . 'tablet_landscape'] : 1024;
		$bp_mobile = ( isset( $opt_data[ $pre. 'mobile'] ) && ! empty( $opt_data[ $pre . 'mobile'] ) ) ? $opt_data[ $pre . 'mobile'] : 680;

		$out = '';
		$out .= sprintf( '<p class="clearfix"><span class="label">%s</span></p>', esc_html__( 'Responsive Breakpoints:', 'themify' ) );
		$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
			esc_html__( 'Tablet Landscape', 'themify' ),
			$pre . 'tablet_landscape',
			$bp_tablet_landscape,
			769,
			1200,
			$bp_tablet_landscape
		);
		$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
			esc_html__( 'Tablet Portrait', 'themify' ),
			$pre . 'tablet',
			$bp_tablet,
			681,
			768,
			$bp_tablet
		);
		$out .= sprintf( '<div class="clearfix"><div class="label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
			esc_html__( 'Mobile', 'themify' ),
			$pre . 'mobile',
			$bp_mobile,
			320,
			680,
			$bp_mobile
		);

		return $out;
	}
}

/**
 * Add Builder to all themes using the themify_theme_config_setup filter.
 * @param $themify_theme_config
 * @return mixed
 * @since 1.4.2
 */
function themify_framework_theme_config_add_builder($themify_theme_config) {
	$themify_theme_config['panel']['settings']['tab']['page_builder'] = array(
		'title' => __('Themify Builder', 'themify'),
		'id' => 'themify-builder',
		'custom-module' => array(
			array(
				'title' => __('Themify Builder Options', 'themify'),
				'function' => 'themify_manage_builder_active'
			),
		)
	);
	if ( 'disable' != apply_filters( 'themify_enable_builder', themify_get('setting-page_builder_is_active') ) ) {
		$themify_theme_config['panel']['settings']['tab']['page_builder']['custom-module'][] = array(
			'title' => __('Animation Effects', 'themify'),
			'function' => 'themify_manage_builder_animation'
		);

		$themify_theme_config['panel']['settings']['tab']['page_builder']['custom-module'][] = array(
			'title' => __('Responsive Design', 'themify'),
			'function' => 'themify_manage_builder_responsive_design'
		);

		$themify_theme_config['panel']['settings']['tab']['page_builder']['custom-module'][] = array(
			'title' => __('Exclude Builder Modules', 'themify'),
			'function' => 'themify_manage_builder'
		);
	}
	return $themify_theme_config;
};
add_filter('themify_theme_config_setup', 'themify_framework_theme_config_add_builder');

if ( ! function_exists( 'themify_builder_grid_lists' ) ) {
	/**
	 * Get Grid menu list
	 */
	function themify_builder_grid_lists( $handle = 'row', $set_gutter = null, $column_alignment_value = '', $row_anchor = '' ) {
		$grid_lists = Themify_Builder_Model::get_grid_settings();
		$gutters = Themify_Builder_Model::get_grid_settings( 'gutter' );
		$column_alignment = Themify_Builder_Model::get_grid_settings( 'column_alignment' );
		$selected_gutter = is_null( $set_gutter ) ? '' : $set_gutter; ?>
		<div class="grid_menu" data-handle="<?php echo esc_attr( $handle ); ?>">
			<div class="grid_icon ti-layout-column3"><span class="row-anchor-name"><?php echo esc_attr( $row_anchor ); ?></span></div>
			<div class="themify_builder_grid_list_wrapper">
				<ul class="themify_builder_grid_list clearfix">
					<?php foreach( $grid_lists as $row ): ?>
					<li>
						<ul>
							<?php foreach( $row as $li ): ?>
								<li><a href="#" class="themify_builder_column_select <?php echo esc_attr( 'grid-layout-' . implode( '-', $li['data'] ) ); ?>" data-handle="<?php echo esc_attr( $handle ); ?>" data-grid="<?php echo esc_attr( json_encode( $li['data'] ) ); ?>"><img src="<?php echo esc_url( $li['img'] ); ?>"></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
					<?php endforeach; ?>
				</ul>

				<ul class="themify_builder_column_alignment clearfix">
					<?php foreach( $column_alignment as $li ): ?>
						<li <?php if ( $column_alignment_value == esc_attr( $li['alignment'] ) || ( $column_alignment_value == '' && esc_attr( $li['alignment'] ) == 'col_align_top' ) ) echo ' class="selected"' ?>><a href="#" class="themify_builder_column_select column-alignment-<?php echo esc_attr( $li['alignment'] ); ?>" data-handle="<?php echo esc_attr( $handle ); ?>" data-alignment="<?php echo esc_attr( $li['alignment'] ); ?>"><img src="<?php echo esc_url( $li['img'] ); ?>"></a></li>
					<?php endforeach; ?>

					<li><?php esc_html_e( 'Column Alignment', 'themify' ) ?></li>
				</ul>

				<select class="gutter_select" data-handle="<?php echo esc_attr( $handle ); ?>">
					<?php foreach( $gutters as $gutter ): ?>
					<option value="<?php echo esc_attr( $gutter['value'] ); ?>"<?php selected( $selected_gutter, $gutter['value'] ); ?>><?php echo esc_html( $gutter['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<span><?php esc_html_e('Gutter Spacing', 'themify') ?></span>

			</div>
			<!-- /themify_builder_grid_list_wrapper -->
		</div>
		<!-- /grid_menu -->
		<?php
	}
}
