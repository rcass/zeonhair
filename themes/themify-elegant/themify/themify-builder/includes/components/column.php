<?php

class Themify_Builder_Component_Column extends Themify_Builder_Component_Base {
	public function __construct() {}

	public function get_name() {
		return 'column';
	}

	public function get_style_settings() {
		$options = apply_filters( 'themify_builder_column_fields', array(
	
			// Font
			array(
				'id' => 'separator_font',
				'type' => 'separator',
				'meta' => array('html' => '<h4>' . __( 'Font', 'themify' ) . '</h4>'),
			),
			array(
				'id' => 'font_family',
				'type' => 'font_select',
				'label' => __( 'Font Family', 'themify' ),
				'class' => 'font-family-select',
				'prop' => 'font-family',
				'selector' => array( '.module_column', '.module_column h1', '.module_column h2', '.module_column h3:not(.module-title)', '.module_column h4', '.module_column h5', '.module_column h6' )
			),
			array(
				'id' => 'font_color',
				'type' => 'color',
				'label' => __( 'Font Color', 'themify' ),
				'class' => 'small',
				'prop' => 'color',
				'selector' => array( '.module_column', '.module_column h1', '.module_column h2', '.module_column h3:not(.module-title)', '.module_column h4', '.module_column h5', '.module_column h6' )
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
						'selector' => '.module_column'
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
						'selector' => '.module_column'
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
				'selector' => '.module_column'
			),
			array(
				'id' => 'text_transform',
				'label' => __( 'Text Transform', 'themify' ),
				'type' => 'icon_radio',
				'meta' => Themify_Builder_Model::get_text_transform(),
				'prop' => 'text-transform',
				'selector' => '.module_column'
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
						'selector' => '.module_column'
					),
					array(
						'id' => 'text_decoration_regular',
						'type' => 'icon_radio',
						'meta' => Themify_Builder_Model::get_text_decoration(),
						'prop' => 'text-decoration',
						'class' => 'inline',
						'selector' => '.module_column'
					),
				)
			),
			// Link
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr />' )
			),
			array(
				'id' => 'separator_link',
				'type' => 'separator',
				'meta' => array( 'html' => '<h4>' . __( 'Link', 'themify' ) . '</h4>' ),
			),
			array(
				'id' => 'link_color',
				'type' => 'color',
				'label' => __( 'Color', 'themify' ),
				'class' => 'small',
				'prop' => 'color',
				'selector' => '.module_column a'
			),
			array(
				'id' => 'text_decoration',
				'type' => 'select',
				'label' => __( 'Text Decoration', 'themify' ),
				'meta' => Themify_Builder_Model::get_text_decoration( true ),
				'prop' => 'text-decoration',
				'selector' => '.module_column a'
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
			Themify_Builder_Model::get_field_group( 'padding', '.module_column', 'top' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_column', 'right' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_column', 'bottom' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_column', 'left' ),
			Themify_Builder_Model::get_field_group( 'padding', '.module_column', 'all' ),
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
			Themify_Builder_Model::get_field_group( 'border', '.module_column', 'top' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_column', 'right' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_column', 'bottom' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_column', 'left' ),
			Themify_Builder_Model::get_field_group( 'border', '.module_column', 'all' ),
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>' )
			),
			array(
				'id' => 'custom_css_column',
				'type' => 'text',
				'label' => __( 'Additional CSS Class', 'themify' ),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf('<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'themify' ))
			),
		));

		return $options;
	}

	protected function _form_template() { 
		$column_settings = $this->get_style_settings();
	?>
	
		<form id="tfb_column_settings">

			<div id="themify_builder_lightbox_options_tab_items">
				<li class="title"><?php _e('Column Styling', 'themify' ); ?></li>
			</div>

			<div id="themify_builder_lightbox_actions_items">
				<button id="builder_submit_column_settings" class="builder_button"><?php _e('Save', 'themify' ) ?></button>
			</div>

			<div class="themify_builder_options_tab_wrapper themify_builder_style_tab">
				<div class="themify_builder_options_tab_content">
					<?php
					foreach ($column_settings as $styling):

						$wrap_with_class = isset($styling['wrap_with_class']) ? $styling['wrap_with_class'] : '';
						echo ( $styling['type'] != 'separator' ) ? '<div class="themify_builder_field ' . esc_attr($wrap_with_class) . '">' : '';
						if (isset($styling['label'])) {
							echo '<div class="themify_builder_label">' . esc_html($styling['label']) . '</div>';
						}
						echo ( $styling['type'] != 'separator' ) ? '<div class="themify_builder_input">' : '';
						if ($styling['type'] != 'multi') {
							themify_builder_styling_field($styling);
						} else {
							foreach ($styling['fields'] as $field) {
								themify_builder_styling_field($field);
							}
						}
						echo ( $styling['type'] != 'separator' ) ? '</div>' : ''; // themify_builder_input
						echo ( $styling['type'] != 'separator' ) ? '</div>' : ''; // themify_builder_field

					endforeach;
					?>

					<p>
						<a href="#" class="reset-styling" data-reset="column">
							<i class="ti ti-close"></i>
							<?php _e('Reset Styling', 'themify' ) ?>
						</a>
					</p>

				</div>
			</div>
			<!-- /.themify_builder_options_tab_wrapper -->

		</form>

	<?php
	}
}