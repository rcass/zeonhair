<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Menu
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache('menu', self::$post_id, array('ID' => $module_ID))):
    
    $fields_default = array(
        'mod_title_menu' => '',
        'layout_menu' => '',
        'custom_menu' => '',
        'color_menu' => '',
        'according_style_menu' => '',
        'css_menu' => '',
        'animation_effect' => '',
        'menu_breakpoint' => '',
        'menu_slide_direction' => '',
        'allow_menu_breakpoint' => ''
    );

    if (isset($mod_settings['according_style_menu']))
        $mod_settings['according_style_menu'] = $this->get_checkbox_data($mod_settings['according_style_menu']);

    $fields_args = wp_parse_args($mod_settings, $fields_default);
    extract($fields_args, EXTR_SKIP);
    $animation_effect = $this->parse_animation_effect($animation_effect, $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $css_menu, $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );

    $mobile_menu_data = array();

    if( ! empty( $allow_menu_breakpoint ) ) {
        $mobile_menu_data = array(
            'data-menu-breakpoint' => $menu_breakpoint,
            'data-menu-direction' => $menu_slide_direction
        );
    }

    $container_props = apply_filters( 'themify_builder_module_container_props', array_merge( array(
        'id' => $module_ID,
        'class' => $container_class
    ), $mobile_menu_data ), $fields_args, $mod_name, $module_ID );
    ?>

    <!-- module menu -->
    <div<?php echo $this->get_element_attributes( $container_props ); ?>>
        <?php if ($mod_title_menu != ''): ?>
            <?php echo $mod_settings['before_title'] . wp_kses_post(apply_filters('themify_builder_module_title', $mod_title_menu, $fields_args)) . $mod_settings['after_title']; ?>
        <?php endif; ?>

        <?php
        $args = array(
            'menu' => $custom_menu,
            'menu_class' => 'ui nav ' . $layout_menu . ' ' . $color_menu . ' ' . $according_style_menu
        );
        wp_nav_menu($args);
        ?>
    </div>
    <!-- /module menu -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>