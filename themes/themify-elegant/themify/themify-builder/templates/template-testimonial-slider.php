<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Testimonial
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
$fields_default = array(
    'mod_title_testimonial' => '',
	'layout_testimonial'=>'',
    'tab_content_testimonial'=> '',
	'img_w_slider' => '',
	'img_h_slider' => '',
	'visible_opt_slider' => '',
	'auto_scroll_opt_slider' => 0,
	'scroll_opt_slider' => '',
	'speed_opt_slider' => '',
	'effect_slider' => 'scroll',
	'pause_on_hover_slider' => 'resume',
	'wrap_slider' => 'yes',
	'show_nav_slider' => 'yes',
	'show_arrow_slider' => 'yes',
    'show_arrow_buttons_vertical'=>'',
	'left_margin_slider' => '',
	'right_margin_slider' => '',
	'height_slider' => 'variable',
    'animation_effect' => '',
    'css_testimonial' => ''
);

$fields_args = wp_parse_args($mod_settings, $fields_default);
extract($fields_args, EXTR_SKIP);
$animation_effect = $this->parse_animation_effect($animation_effect, $fields_args);
$arrow_vertical=$show_arrow_slider==='yes' && $show_arrow_buttons_vertical==='vertical'?'themify_builder_slider_vertical':'';
$container_class = implode(' ', 
	apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID,'module-slider','themify_builder_slider_wrap', 'clearfix', $css_testimonial,$layout_testimonial, $animation_effect,$arrow_vertical
	), $mod_name, $module_ID, $fields_args )
);

$container_props = apply_filters( 'themify_builder_module_container_props', array(
    'id' => $module_ID,
    'class' => $container_class
), $fields_args, $mod_name, $module_ID );

$this->in_the_loop = true;
$visible = $visible_opt_slider;
$scroll = $scroll_opt_slider;
$auto_scroll = $auto_scroll_opt_slider;
$arrow = $show_arrow_slider;
$pagination = $show_nav_slider;
$left_margin = ! empty( $left_margin_slider ) ? $left_margin_slider .'px' : '';
$right_margin = ! empty( $right_margin_slider ) ? $right_margin_slider .'px' : '';
$effect = $effect_slider;

switch ( $speed_opt_slider ) {
	case 'slow':
		$speed = 4;
	break;
	
	case 'fast':
		$speed = '.5';
	break;

	default:
	 $speed = 1;
	break;
}
global $paged;
$paged = $this->get_paged_query();
?>
<?php if (TFCache::start_cache('testimonial', self::$post_id, array('page' => $paged, 'ID' => $module_ID))): ?>
   <!-- module slider testimonial -->
<div id="<?php echo esc_attr( $module_ID ); ?>-loader" class="themify_builder_slider_loader" style="<?php echo !empty($img_h_slider) ? 'height:'.$img_h_slider.'px;' : 'height:50px;'; ?>"></div>
<div<?php echo $this->get_element_attributes( $container_props ); ?>>

	<?php if ( $mod_title_testimonial != '' ): ?>
		<?php echo $settings['before_title'] . wp_kses_post( apply_filters( 'themify_builder_module_title', $mod_title_testimonial, $fields_args ) ) . $settings['after_title']; ?>
	<?php endif; ?>
	
	<ul class="themify_builder_slider" 
		data-id="<?php echo esc_attr( $module_ID ); ?>" 
		data-visible="<?php echo esc_attr( $visible ); ?>" 
		data-scroll="<?php echo esc_attr( $scroll ); ?>" 
		data-auto-scroll="<?php echo esc_attr( $auto_scroll ); ?>"
		data-speed="<?php echo esc_attr( $speed ); ?>"
		data-wrap="<?php echo esc_attr( $wrap_slider ); ?>"
		data-arrow="<?php echo esc_attr( $arrow ); ?>"
		data-pagination="<?php echo esc_attr( $pagination ); ?>"
		data-effect="<?php echo esc_attr( $effect ); ?>" 
		data-height="<?php echo esc_attr( $height_slider ); ?>" 
		data-pause-on-hover="<?php echo esc_attr( $pause_on_hover_slider ); ?>" >
		
		<?php
		do_action( 'themify_builder_before_template_content_render' );

		if ( count( $tab_content_testimonial ) > 0 ):
			foreach( $tab_content_testimonial as $content ):
		?>

		<li style="<?php echo !empty($left_margin) ? 'margin-left:'.$left_margin.';' : ''; ?> <?php echo !empty($right_margin) ? 'margin-right:'.$right_margin.';' : ''; ?>">
			<?php
				
				$image='';
				if ( !empty($content['person_picture_testimonial']) ){ 
					$image_url = isset( $content['person_picture_testimonial'] )? esc_url( $content['person_picture_testimonial'] ) : '';
					$image_w = $img_w_slider;
					$image_h = $img_h_slider;
					$image_title = isset( $content['title_testimonial'] )? $content['title_testimonial'] : '';
					if ( $alt_by_url = Themify_Builder_Model::get_alt_by_url( $image_url ) ) {
						$image_alt = $alt_by_url;
					} else {
						$image_alt = $image_title;
					}
					$param_image_src = 'src='.$image_url.'&w='.$image_w .'&h='.$image_h.'&alt='.$image_alt.'&ignore=true&class=person-picture';
					if ( $this->is_img_php_disabled() ) {
						// get image preset
						$preset = $image_size_slider != '' ? $image_size_slider : themify_get('setting-global_feature_size');
						if ( isset( $_wp_additional_image_sizes[ $preset ]) && $image_size_slider != '') {
							$image_w = intval( $_wp_additional_image_sizes[ $preset ]['width'] );
							$image_h = intval( $_wp_additional_image_sizes[ $preset ]['height'] );
						} else {
							$image_w = $image_w != '' ? $image_w : get_option($preset.'_size_w');
							$image_h = $image_h != '' ? $image_h : get_option($preset.'_size_h');
						}
						$image = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" class="person-picture" width="' . esc_attr( $image_w ) . '" height="' . esc_attr( $image_h ) . '">';
					} else {
						$image = themify_get_image( $param_image_src );
					}
					?>				
				
				<?php } ?>	
			
			<?php if(!empty($image) && $layout_testimonial=='image-top'){ ?>			
					<figure class="testimonial-image">
						<?php echo $image; ?>
					</figure>
			<?php } ?>
				
			<div class="testimonial-content">
				<?php if(!empty($content['title_testimonial'])){ ?>
				<h3 class="testimonial-title"><?php echo $content['title_testimonial']; ?></h3>
				<?php } ?>
				<?php if(!empty($content['content_testimonial'])){ ?>
				<div class="testimonial-entry-content">					
					<?php echo $content['content_testimonial']; ?>
				</div>
				<?php } ?>
				<!-- /testimonial-enter-content -->
				<?php if(!empty($image) && $layout_testimonial=='image-bottom'){ ?>				
						<figure class="testimonial-image">
							<?php echo $image; ?>
						</figure>
				<?php } ?>
				<?php if(!empty($content['person_name_testimonial'])){ ?>
				<div class="testimonial-author">
					<div class="person-name"><?php echo $content['person_name_testimonial']; ?></div>
					<div class="person-company">
				<span class="person-position"><?php echo $content['person_position_testimonial']; ?></span>, <span class="person-company"><?php if(trim($content['company_website_testimonial'])!=''){ ?><a href="<?php echo $content['company_website_testimonial']; ?>"><?php } ?><?php echo $content['company_testimonial']; ?><?php if(trim($content['company_website_testimonial'])!=''){ ?></a><?php } ?></span>
					</div>
				</div>
				<?php } ?>
				<!-- /testimonial-author -->
				
				

			</div>
			<!-- /testimonial-content -->
			
			
		</li>
		<?php endforeach;  ?>
	<?php endif; ?>

		<?php do_action( 'themify_builder_after_template_content_render' );  ?>

	</ul>
	<!-- /themify_builder_slider -->

</div>
<!-- /module slider testimonial -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
<?php $this->in_the_loop = false; ?>