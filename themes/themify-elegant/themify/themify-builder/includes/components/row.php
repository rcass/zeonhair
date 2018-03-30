<?php

class Themify_Builder_Component_Row extends Themify_Builder_Component_Base {
	public function __construct() {}

	public function get_name() {
		return 'row';
	}

	public function get_settings() {
		$options = apply_filters( 'themify_builder_row_fields_options', array(
			// Row Width
			array(
				'id' => 'row_width',
				'label' => __( 'Row Width', 'themify' ),
				'type' => 'radio',
				'description' => '',
				'meta' => array(
					array( 'value' => '', 'name' => __( 'Default', 'themify' ), 'selected' => true ),
					array( 'value' => 'fullwidth', 'name' => __( 'Fullwidth row container', 'themify' ) ),
					array( 'value' => 'fullwidth-content', 'name' => __( 'Fullwidth content', 'themify' ) )
				),
				'wrap_with_class' => '',
			),
			// Row Height
			array(
				'id' => 'row_height',
				'label' => __( 'Row Height', 'themify' ),
				'type' => 'radio',
				'description' => '',
				'meta' => array(
					array( 'value' => '', 'name' => __( 'Default', 'themify' ), 'selected' => true ),
					array( 'value' => 'fullheight', 'name' => __( 'Fullheight (100% viewport height)', 'themify' ) )
				),
				'wrap_with_class' => '',
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>' )
			),
			array(
				'id' => 'custom_css_row',
				'type' => 'text',
				'label' => __( 'Additional CSS Class', 'themify' ),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'themify' ) )
			),
			array(
				'id' => 'row_anchor',
				'type' => 'text',
				'label' => __( 'Row Anchor', 'themify' ),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __( 'Example: enter ‘about’ as row anchor and add ‘#about’ link in navigation menu. When link is clicked, it will scroll to this row.', 'themify' ) )
			),
		));
		return $options;
	}

	public function get_style_settings() {
		$options = apply_filters( 'themify_builder_row_fields_styling', array(
	
			// Font
			array(
				'id' => 'separator_font',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Font', 'themify' ) . '</h4>' )
			),
			array(
				'id' => 'font_family',
				'type' => 'font_select',
				'label' => __( 'Font Family', 'themify' ),
				'class' => 'font-family-select',
				'prop' => 'font-family',
				'selector' => array( '.module_row', '.module_row h1', '.module_row h2', '.module_row h3:not(.module-title)', '.module_row h4', '.module_row h5', '.module_row h6' )
			),
			array(
				'id' => 'font_color',
				'type' => 'color',
				'label' => __( 'Font Color', 'themify' ),
				'class' => 'small',
				'prop' => 'color',
				'selector' => array( '.module_row', '.module_row h1', '.module_row h2', '.module_row h3:not(.module-title)', '.module_row h4', '.module_row h5', '.module_row h6' ),
			),
			array(
				'id' => 'multi_font_size',
				'type' => 'multi',
				'label' => __( 'Font Size', 'themify' ),
				'fields' => array(
					array(
						'id' => 'font_size',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'font-size',
						'selector' => '.module_row'
					),
					array(
						'id' => 'font_size_unit',
						'type' => 'select',
						'meta' => Themify_Builder_Model::get_css_units(),
						'default' => 'px',
					)
				)
			),
			array(
				'id' => 'multi_line_height',
				'type' => 'multi',
				'label' => __( 'Line Height', 'themify' ),
				'fields' => array(
					array(
						'id' => 'line_height',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'line-height',
						'selector' => '.module_row'
					),
					array(
						'id' => 'line_height_unit',
						'type' => 'select',
						'meta' => Themify_Builder_Model::get_css_units(),
						'default' => 'px',
					)
				)
			),
			array(
				'id' => 'multi_letter_spacing',
				'type' => 'multi',
				'label' => __( 'Letter Spacing', 'themify' ),
				'fields' => array(
					array(
						'id' => 'letter_spacing',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'letter-spacing',
						'selector' => '.module_row'
					),
					array(
						'id' => 'letter_spacing_unit',
						'type' => 'select',
						'meta' => Themify_Builder_Model::get_css_units(),
						'default' => 'px',
					)
				)
			),
			array(
				'id' => 'text_align',
				'label' => __( 'Text Align', 'themify' ),
				'type' => 'icon_radio',
				'meta' => Themify_Builder_Model::get_text_align(),
				'prop' => 'text-align',
				'selector' => '.module_row'
			),
			array(
				'id' => 'text_transform',
				'label' => __( 'Text Transform', 'themify' ),
				'type' => 'icon_radio',
				'meta' => Themify_Builder_Model::get_text_transform(),
				'prop' => 'text-transform',
				'selector' => '.module_row'
			),
			array(
				'id' => 'multi_font_style',
				'type' => 'multi',
				'label' => __( 'Font Style', 'themify' ),
				'fields' => array(
					array(
						'id' => 'font_style_regular',
						'type' => 'icon_radio',
						'meta' => Themify_Builder_Model::get_font_style(),
						'prop' => 'font-style',
						'class' => 'inline',
						'selector' => '.module_row'
					),
					array(
						'id' => 'text_decoration_regular',
						'type' => 'icon_radio',
						'meta' => Themify_Builder_Model::get_text_decoration(),
						'prop' => 'text-decoration',
						'class' => 'inline',
						'selector' => '.module_row'
					),
				)
			),
			// Link
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr />')
			),
			array(
				'id' => 'separator_link',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Link', 'themify' ) . '</h4>' )
			),
			array(
				'id' => 'link_color',
				'type' => 'color',
				'label' => __( 'Color', 'themify' ),
				'class' => 'small',
				'prop' => 'color',
				'selector' => '.module_row a'
			),
			array(
				'id' => 'text_decoration',
				'type' => 'select',
				'label' => __( 'Text Decoration', 'themify' ),
				'meta' => Themify_Builder_Model::get_text_decoration( true ),
				'prop' => 'text-decoration',
				'selector' => '.module_row a'
			),
			// Padding
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr />' )
			),
			array(
				'id' => 'separator_padding',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Padding', 'themify' ) . '</h4>' ),
			),
			Themify_Builder_Model::get_field_group( 'padding', '.module_row', 'top' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_row', 'right' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_row', 'bottom' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_row', 'left' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_row', 'all' ),
			// Margin
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr />' )
			),
			array(
				'id' => 'separator_margin',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Margin', 'themify') . '</h4>' ),
			),
			Themify_Builder_Model::get_field_group( 'margin', '.module_row', 'top' ),
			Themify_Builder_Model::get_field_group( 'margin', '.module_row', 'right' ),
			Themify_Builder_Model::get_field_group( 'margin', '.module_row', 'bottom' ),
			Themify_Builder_Model::get_field_group( 'margin', '.module_row', 'left' ),
			Themify_Builder_Model::get_field_group( 'margin', '.module_row', 'all' ),
			// Border
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr />' )
			),
			array(
				'id' => 'separator_border',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Border', 'themify' ) . '</h4>' )
			),
			Themify_Builder_Model::get_field_group( 'border', '.module_row', 'top' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_row', 'right' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_row', 'bottom' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_row', 'left' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_row', 'all' )
		));

		return $options;
	}

	public function get_form_settings() {
		$row_form_settings = array(
			'options' => array(
				'name' => esc_html__( 'Row Options', 'themify' ),
				'options' => $this->get_settings()
			),
			'styling' => array(
				'name' => esc_html__( 'Styling', 'themify' ),
				'options' => $this->get_style_settings()
			)
		);
		return apply_filters( 'themify_builder_row_lightbox_form_settings', $row_form_settings );
	}

	protected function _form_template() { 
		$row_form_settings = $this->get_form_settings();
	?>
	
		<form id="tfb_row_settings">
			<div id="themify_builder_lightbox_options_tab_items">
				<?php foreach( $row_form_settings as $setting_key => $setting ): ?>
				<li><a href="#themify_builder_row_fields_<?php echo esc_attr( $setting_key ); ?>">
					<?php echo esc_attr( $setting['name'] ); ?>
				</a></li>
				<?php endforeach; ?>
			</div>

			<div id="themify_builder_lightbox_actions_items">
				<button id="builder_submit_row_settings" class="builder_button"><?php _e('Save', 'themify' ) ?></button>
			</div>
			
			<?php foreach( $row_form_settings as $setting_key => $setting ): ?>
			<div id="themify_builder_row_fields_<?php echo esc_attr( $setting_key ); ?>" class="themify_builder_options_tab_wrapper<?php echo $setting_key==='styling'?' themify_builder_style_tab':''?>">
				<div class="themify_builder_options_tab_content">
					<?php 
					if ( isset( $setting['options'] ) && count( $setting['options'] ) > 0 ) 
						themify_render_row_fields( $setting['options'] );
					?>

					<?php if ( 'styling' == $setting_key ): ?>
					<p>
						<a href="#" class="reset-styling" data-reset="row">
							<i class="ti ti-close"></i>
							<?php _e('Reset Styling', 'themify' ) ?>
						</a>
					</p>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>

		</form>

	<?php
	}
}