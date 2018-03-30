<?php

class Themify_Builder_Stylesheet {
	private $builder;

	/**
	 * Flag to know if we're in the middle of saving the stylesheet.
	 * @var string
	 */
	private $saving_stylesheet = false;

	/**
	 * Flag to know if we're rendering the style inline.
	 * @var string
	 */
	public $is_front_end_style_inline = false;

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param object Themify_Builder $builder 
	 */
	public function __construct( Themify_Builder $builder ) {
		$this->builder = $builder;

		if (Themify_Builder_Model::is_frontend_editor_page()) {

			// Row Styling
			add_action('themify_builder_row_start', array($this, 'render_row_styling'), 10, 2);
			
			// Sub-Row Styling
			add_action('themify_builder_sub_row_start', array($this, 'render_sub_row_styling'), 10, 4);

			// Column Styling
			add_action('themify_builder_column_start', array($this, 'render_column_styling'), 10, 3);

			// Sub-Column Styling
			add_action('themify_builder_sub_column_start', array($this, 'render_sub_column_styling'), 10, 5);

			// Google Fonts
			add_filter( 'themify_google_fonts', array( $this, 'enqueue_fonts' ) );

		} else {
			
			// If user not logged in and is not a Builder editor view, enqueue static stylesheet
			if (isset($_GET['themify_builder_infinite_scroll']) && 'yes' == $_GET['themify_builder_infinite_scroll']) {
				add_action('themify_builder_row_start', array($this, 'render_row_styling'), 10, 2);
			} else {
				add_action('wp_enqueue_scripts', array($this, 'enqueue_stylesheet'), 14);
				add_action('themify_builder_before_template_content_render', array($this, 'enqueue_stylesheet'), 10, 2);
			}
		}

		// Checks if a stylesheet with the proper slug exists, otherwise generates it.
		add_action('save_post', array($this, 'build_stylesheet_if_needed'), 77, 1);
	}

	/**
	 * Get css rules from a component.
	 * 
	 * @access public
	 * @param string $component_name 
	 * @param array $settings 
	 * @return array
	 */
	public function get_rules( $component_name, $settings ) {
		global $themify;

		if (!isset($themify->builder_google_fonts)) {
			$themify->builder_google_fonts = '';
		}
		$css = array();
		$styling = array();

		if ( isset( Themify_Builder_Model::$modules[ $component_name ] ) ) {
			$styling = Themify_Builder_Model::$modules[$component_name]->get_styling_settings();
		} elseif ( $component_instance = $this->builder->components_manager->get_component_types( $component_name ) ) {
			$styling = $component_instance->get_style_settings();
		}

		$rules = $this->make_rules($styling, $settings, false, $component_name);

		if (!empty($rules) && is_array($rules)) {
			$css_rules = array();
			foreach ($rules as $v){
				$css_rules[$v['id']] = $v;
			}

			foreach ($rules as $value) {
				$css[$value['selector']] = isset($css[$value['selector']]) ? $css[$value['selector']] : '';

				if ( in_array( $value['prop'], array( 'background-color', 'color', 'border-top-color', 'border-bottom-color', 'border-left-color', 'border-right-color', 'column-rule-color' ) ) ) {
					if( in_array($value['prop'], array( 'border-top-color', 'border-bottom-color', 'border-left-color', 'border-right-color', 'column-rule-color' ) ) ){
						$temp_id = str_replace( '_color','', $value['id'] );
						if(empty( $css_rules[$temp_id.'_style']['value'] )){
							$css_rules[$temp_id.'_style']['value'] = 'solid';
						}
						if ( empty( $css_rules[$temp_id.'_width']['value'] ) || $css_rules[$temp_id.'_style']['value']==='none' ) {
							continue;
						}
					}
					elseif($value['prop']==='background-color' && ($value['id']==='cover_color' || $value['id']==='cover_color_hover') && isset($settings[$value['id'].'-type']) && $settings[$value['id'].'-type']!=='color'){
						continue;
					}
					// Split color and opacity
					$temp_color = explode( '_', $value['value'] );
					$temp_opacity = isset($temp_color[1]) ? $temp_color[1] : '1';
					// Write hexadecimal color.
					$css[$value['selector']][ $value['prop'] ] = sprintf( '#%s', $temp_color[0] );
					// If there's opacity, that is, if it's not 1 or 1.00 write RGBA color.
					if ( '1' != $temp_opacity && '1.00' != $temp_opacity ) {
						$css[$value['selector']][ $value['prop'] ] = self::get_rgba_color($value['value']);
					}
				} elseif ($value['prop'] === 'font-family' && $value['value'] !== 'default') {
					if (!in_array($value['value'], themify_get_web_safe_font_list(true))) {
						$themify->builder_google_fonts .= str_replace(' ', '+', $value['value'] . '|');
					}
					$css[$value['selector']][ $value['prop'] ] = $value['value'];
				} elseif (in_array($value['prop'], array('font-size', 'line-height', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width', 'column-rule-width', 'column-gap'))) {
					if(in_array($value['prop'], array('border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width', 'column-rule-width'))){
						$temp_id = str_replace( '_width', '', $value['id'] );
						if(empty( $css_rules[$temp_id.'_style']['value'] )){
							$css_rules[$temp_id.'_style']['value'] = 'solid';
						}
						if( empty($value['value']) ||  $css_rules[$temp_id.'_style']['value']==='none'){
							continue;
						}
					}
					$unit = isset($settings[$value['id'] . '_unit']) ? $settings[$value['id'] . '_unit'] : 'px';
					$css[$value['selector']][ $value['prop'] ] = sprintf('%s%s', $value['value'], $unit);
				} elseif (in_array($value['prop'], array('text-decoration', 'text-align', 'background-repeat', 'background-position', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style', 'column-rule-style'))) {
					if(in_array($value['prop'], array('border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style', 'column-rule-style'))){
						$temp_id = str_replace( '_style', '', $value['id'] );
						if(empty($css_rules[$temp_id.'_width']['value']) && $value['value']!=='none'){
							continue;
						}
						elseif(empty($value['value'])){
							$value['value'] = 'solid';
						}
					}
					$css[$value['selector']][ $value['prop'] ] = $value['value'];
				} elseif ( $value['prop'] === 'background-image' && $value['type'] === 'image' ) {
					$css[$value['selector']][ $value['prop'] ] = sprintf('url("%s")', themify_https_esc($value['value']));
				} elseif( $value['prop'] === 'background-image' && $value['type'] === 'gradient' && !empty( $settings[$value['id'] . '-gradient'] ) ) {
					$css[$value['selector']][ $value['prop'] ] = $this->get_gradient( $settings, $value['id'] );
				} elseif( $value['prop'] === 'column-count' ) {
					$css[$value['selector']][ $value['prop'] ] = $value['value'];
				}
			}

			if (!empty($css)) {
				foreach ($css as $selector => $defs) {
					if (empty($defs)) {
						unset( $css[ $selector] );
					}
				}
			}
		}
		
		return $css;
	}

	public function get_gradient( $settings, $key ) {
		$args = array();
		if( isset( $settings[ "{$key}-gradient" ] ) ) {
			$args['gradient'] = $settings[ "{$key}-gradient" ];
		}
		if( isset( $settings[ "{$key}-gradient-type" ] ) ) {
			$args['type'] = $settings[ "{$key}-gradient-type" ];
		}
		if( isset( $settings[ "{$key}-gradient-angle" ] ) ) {
			$args['angle'] = $settings[ "{$key}-gradient-angle" ];
		}
		if( isset( $settings[ "{$key}-circle-radial" ] ) && $settings[ "{$key}-circle-radial" ] ) {
			$args['ending_shape'] = 'circle';
		}

		return $this->get_gradient_css( $args );
	}

	function default_gradient() {
		return '0% rgba(0,0,0, 1)|100% rgba(255,255,255,1)';
	}

	/**
	 * Get gradient value.
	 * 
	 * @access private
	 * @param string $string 
	 * @return string
	 */
	private function get_gradient_css( $args = array() ) {
		extract( wp_parse_args( $args, array(
			'gradient' => $this->default_gradient(),
			'angle' => 0,
			'type' => 'linear',
			'ending_shape' => 'ellipse',
		) ) );

		if( $type == 'linear' ) {
			$angle .= 'deg';
		} else {
			$angle = $ending_shape;
		}

		$stops = array();
		foreach( explode( '|', $gradient ) as $stop ) {
			preg_match( '/(\d+%)\s(.*)/', $stop, $matches );
			$stops[] = $matches[2] . ' ' . $matches[1];
		}
		$stops = join( ', ', $stops );
		return "{$type}-gradient( {$angle}, {$stops} );";
	}

	/**
	 * Make css rules.
	 * 
	 * @access public
	 * @param array $def 
	 * @param array $settings 
	 * @param boolean $empty 
	 * @return array
	 */
	public function make_rules($def, $settings, $empty = false, $component_name = '') {
		$result = array();
		if (empty($def)) {
			return $result;
		}

		foreach ($def as $option) {
			if ($option['type'] === 'multi') {
				$result = array_merge($result, $this->make_rules($option['fields'], $settings, $empty, $component_name));
			} elseif ($option['type'] === 'tabs') {
				foreach ($option['tabs'] as $tab) {
					$result = array_merge($result, $this->make_rules($tab['fields'], $settings, $empty, $component_name));
				}
			} elseif ( in_array( $component_name, array('row', 'subrow', 'column') ) && 'gradient' == $option['type'] && !$empty ) {
				$new = false;
				switch ( $option['id'] ) {
					case 'background_gradient':
						if ( 'gradient' === $settings['background_type'] ) {
							$new = array(
								'id' => $option['id'],
								'value' => $this->get_gradient( $settings, $option['id'] ),
								'prop' => 'background-image',
								'type' => 'gradient'
							);
						}
					break;

					case 'cover_gradient':
						if ( 'cover_gradient' === $settings['cover_color-type'] && !empty($settings['cover_gradient-gradient'])) {

							$new = array(
								'id' => $option['id'],
								'value' =>  $this->get_gradient( $settings, $option['id'] ),
								'prop' => 'background-image',
								'type' => 'gradient'
							);
						}
					break;

					case 'cover_gradient_hover':
						if ( 'hover_gradient' === $settings['cover_color_hover-type'] && !empty($settings['cover_gradient_hover-gradient'])) {
							$new = array(
								'id' => $option['id'],
								'value' =>  $this->get_gradient( $settings, $option['id'] ),
								'prop' => 'background-image',
								'type' => 'gradient'
							);
						}
					break;
				}

				if ($new) {
					foreach ((array) $option['selector'] as $selector) {
						$result[] = array_merge( $new, array( 'selector' => $selector ) );
					}
				}
			} elseif( $option['type'] === 'image_and_gradient' && !$empty ) {
				if( isset($settings[$option['id'] . '-type']) && $settings[$option['id'] . '-type'] === 'gradient'  ) {
					$new = array(
						'id' => $option['id'],
						'value' =>  $this->get_gradient( $settings, $option['id'] ),
						'prop' => 'background-image',
						'type' => 'gradient'
					);
				} elseif( ( ! isset( $settings[$option['id'] . '-type'] ) || ( isset($settings[$option['id'] . '-type'], $settings[$option['id']]) && $settings[$option['id'] . '-type'] === 'image' ) )  ) {
					$new = array(
						'id' => $option['id'],
						'value' => $settings[$option['id']],
						'prop' => 'background-image',
						'type' => 'image'
					);
				}
				if( isset( $new ) ) {
					foreach ((array) $option['selector'] as $selector) {
						$result[] = array_merge( $new, array( 'selector' => $selector ) );
				   }
				}
			}
			elseif (isset($option['prop']) && (isset($settings[$option['id']]) || $empty)) {
				if ($empty) {
					if($option['type']!=='seperator'){
						$result[$option['id']] = $option;
					}
				} else {
					foreach ((array) $option['selector'] as $selector) {
						$result[] = array(
							'id' => $option['id'],
							'prop' => $option['prop'],
							'type' => $option['type'],
							'selector' => $selector,
							'value' => $settings[$option['id']]
						);
					}
				}
			}
		}

		return $result;
	}

	public function get_style_rules( $component_name, $settings ) {
		$breakpoints = themify_get_breakpoints();
		$rules = array();

		$desktop = $this->get_rules( $component_name, $settings );
		if ( !empty( $desktop ) ){
					$rules['desktop'] = $desktop;
				}

		foreach( $breakpoints as $bp => $val ) {
			if ( isset( $settings[ 'breakpoint_' . $bp ] ) && is_array( $settings[ 'breakpoint_' . $bp ] ) ) {
				$rules[ $bp ] = $this->get_rules( $component_name, $settings[ 'breakpoint_' . $bp ] );
			}
		}

		return $rules;
	}

	/**
	 * Output row styling style
	 * @param int $builder_id
	 * @param array $row
	 * @return string
	 */
	public function render_row_styling($builder_id, $row) {
		$row['styling'] = isset($row['styling']) ? $row['styling'] : '';
		$row['row_order'] = isset($row['row_order']) ? $row['row_order'] : '';
		$settings = $row['styling'];
		$style_id = '.themify_builder .module_row_' . $builder_id . '-' . $row['row_order'];
		echo $this->get_custom_styling($style_id, 'row', $settings);

		// responsive styling
		echo $this->render_responsive_style($style_id, 'row', $settings);

	}

	/**
	 * Output column styling style
	 * @param int $builder_id
	 * @param array $row
	 * @param array $column
	 * @return string
	 */
	function render_column_styling($builder_id, $row, $column) {
		$column['styling'] = isset($column['styling']) ? $column['styling'] : '';
		$column['column_order'] = isset($column['column_order']) ? $column['column_order'] : '';
		$settings = $column['styling'];
		/*$style_id = '.themify_builder_' . $builder_id . '_row.module_row_' . $row['row_order'] .
				' .tb_' . $builder_id . '_column.module_column_' . $column['column_order'];*/
		$style_id = '.themify_builder .module_column_' . $builder_id . '-' . $row['row_order'] . '-' . $column['column_order'];
		
		if( !empty( $column[ 'grid_width' ] ) ) {
			$settings[ 'width' ] = $column[ 'grid_width' ] . '%';
		}

		echo $this->get_custom_styling($style_id, 'column', $settings);
		// responsive styling
		echo $this->render_responsive_style($style_id, 'column', $settings);
	}

	/**
	 * Output sub-row styling style
	 * @param int $builder_id
	 * @param int $row
	 * @param int $column
	 * @param array $subrow
	 * @return string
	 */
	function render_sub_row_styling($builder_id, $row, $column, $subrow) {
		$subrow['styling'] = isset($subrow['styling']) ? $subrow['styling'] : '';
		$subrow['row_order'] = isset($subrow['row_order']) ? $subrow['row_order'] : '';
		$settings = $subrow['styling'];
		$style_id = '.themify_builder_sub_row.sub_row_'. $row . "-" . $column . "-" .$subrow['row_order'];

		echo $this->get_custom_styling($style_id, 'subrow', $settings);
		// responsive styling
		echo $this->render_responsive_style($style_id, 'subrow', $settings);

	}

	/**
	 * Output sub-column styling style
	 * @param int $builder_id
	 * @param int $rows
	 * @param int $cols
	 * @param int $modules
	 * @param array $sub_column
	 * @return string
	 */
	function render_sub_column_styling($builder_id, $rows, $cols, $modules, $sub_column) {
		$sub_column['styling'] = isset($sub_column['styling']) ? $sub_column['styling'] : '';
		$sub_column['column_order'] = isset($sub_column['column_order']) ? $sub_column['column_order'] : '';
		$settings = $sub_column['styling'];
		$style_id = '.sub_column_post_' . $builder_id . '.sub_column_' . $rows . '-' .$cols . '-' . $modules . '-' . $sub_column['column_order'];
		
		if( !empty( $sub_column[ 'grid_width' ] ) ) {
			$settings[ 'width' ] = $sub_column[ 'grid_width' ] . '%';
		}

		echo $this->get_custom_styling($style_id, 'sub_column', $settings);
				// responsive styling
		echo $this->render_responsive_style($style_id, 'sub_column', $settings);
	}

	/**
	 * Generate CSS styling.
	 * 
	 * @since 1.0.0
	 * @since 2.2.5 Added the ability to return pure CSS without <style> tags for stylesheet generation.
	 *
	 * @param int $style_id
	 * @param string $mod_name Name of the module to build styles for. Example 'row' for row styling.
	 * @param array $settings List of settings to generate style.
	 * @param bool $array Used for legacy styling generation.
	 * @param string $format Use 'tag' to return the CSS enclosed in <style> tags. This mode is used while user is logged in and Builder is active. Use 'css' to return only the CSS. This mode is used on stylesheet generation.
	 *
	 * @return string
	 */
	public function get_custom_styling($style_id, $mod_name, $settings, $array = false, $format = 'tag', $rules=false) {
		global $themify;

		/**
		 * Filter style id selector. This can be used to modify the selector on a theme by theme basis.
		 * 
		 * @since 2.3.1
		 *
		 * @param string $style_id Full selector string to be filtered.
		 * @param string $builder_id ID of Builder instance.
		 * @param array $row Current row.
		 */
		$style_id = apply_filters('themify_builder_row_styling_style_id', $style_id);

		if (!isset($themify->builder_google_fonts)) {
			$themify->builder_google_fonts = '';
		}
		$output = '';
		// legacy module def support
		if (
				'row' === $mod_name ||
				'subrow' === $mod_name ||
				'column' === $mod_name ||
				'sub_column' === $mod_name ||
				( isset(Themify_Builder_Model::$modules[$mod_name]) && is_array(Themify_Builder_Model::$modules[$mod_name]->get_css_selectors()) )
		) {
			return $this->get_custom_styling_legacy($style_id, $mod_name, $settings, $array, $format);
		}

		if($rules===false){
			$styling = isset(Themify_Builder_Model::$modules[$mod_name]) ? Themify_Builder_Model::$modules[$mod_name]->get_styling_settings() : array();
			$rules = $this->make_styling_rules($styling, $settings);
		}

		if (!empty($rules) && is_array($rules)) {
			$css = array();
						$css_rules = array();
						foreach ($rules as $v){
							$css_rules[$v['id']] = $v;
						}
			foreach ($rules as $value) {
				$css[$value['selector']] = isset($css[$value['selector']]) ? $css[$value['selector']] : '';

				if ( in_array( $value['prop'], array( 'background-color', 'color', 'border-top-color', 'border-bottom-color', 'border-left-color', 'border-right-color', 'column-rule-color' ) ) ) {
					if( in_array($value['prop'], array( 'border-top-color', 'border-bottom-color', 'border-left-color', 'border-right-color', 'column-rule-color' ) ) ){
						$temp_id = str_replace( '_color','', $value['id'] );
						if ( empty( $css_rules[$temp_id.'_width']['value'] ) || empty( $css_rules[$temp_id.'_style']['value'] ) || $css_rules[$temp_id.'_style']['value']==='none' ) {
							continue;
						}
					}
					// Split color and opacity
					$temp_color = explode( '_', $value['value'] );
					$temp_opacity = isset($temp_color[1]) ? $temp_color[1] : '1';
					// Write hexadecimal color.
					$css[$value['selector']] .= sprintf( '%s: #%s; ', $value['prop'], $temp_color[0] );
					// If there's opacity, that is, if it's not 1 or 1.00 write RGBA color.
					if ( '1' != $temp_opacity && '1.00' != $temp_opacity ) {
						$css[$value['selector']] .= sprintf('%s: %s; ', $value['prop'], self::get_rgba_color($value['value']));
					}
				} elseif ($value['prop'] == 'font-family' && $value['value'] != 'default') {
					if (!in_array($value['value'], themify_get_web_safe_font_list(true))) {
						$themify->builder_google_fonts .= str_replace(' ', '+', $value['value'] . '|');
					}
					$css[$value['selector']] .= sprintf('font-family: %s; ', $value['value']);
				} elseif (in_array($value['prop'], array('font-size', 'line-height', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width', 'column-rule-width', 'column-gap', 'letter-spacing'))) {
					if(in_array($value['prop'], array('border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width', 'column-rule-width'))){
						$temp_id = str_replace( '_width', '', $value['id'] );
						if(empty($css_rules[$temp_id.'_style']['value']) || empty($value['value']) ||  $css_rules[$temp_id.'_style']['value']==='none'){
							continue;
						}
					}
					$unit = isset($settings[$value['id'] . '_unit']) ? $settings[$value['id'] . '_unit'] : 'px';
					$css[$value['selector']] .= sprintf('%s: %s%s; ', $value['prop'], $value['value'], $unit);
				} elseif (in_array($value['prop'], array('text-decoration', 'text-align', 'background-repeat', 'background-position', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style', 'column-rule-style', 'text-transform', 'text-decoration', 'font-style'))) {
					if (in_array($value['prop'], array('border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style', 'column-rule-style'))){
						$temp_id = str_replace( '_style', '', $value['id'] );
						if(empty($css_rules[$temp_id.'_width']['value']) && $value['value']!=='none'){
							continue;
						}
					}

					if ( ! empty( $value['value'] ) ) {
						$css[$value['selector']] .= sprintf('%s: %s; ', $value['prop'], $value['value']);
					}
				} elseif ( $value['prop'] === 'background-image' && $value['type'] === 'image' ) {
					$css[$value['selector']] .= sprintf('%s: url("%s"); ', $value['prop'], themify_https_esc($value['value']));
				} elseif( $value['prop'] === 'background-image' && $value['type'] === 'gradient' && !empty( $settings[$value['id'] . '-gradient'] ) ) {
					$css[$value['selector']] .= sprintf( '%s: %s; ', $value['prop'], $this->get_gradient( $settings, $value['id'] ) );
				} elseif( $value['prop'] === 'column-count' ) {
					$css[$value['selector']] .= sprintf('%s: %s; ', $value['prop'], $value['value']);
				}
			}

			if (!empty($css)) {
				foreach ($css as $selector => $defs) {
					if (empty($defs)) {
						continue;
					}
					$output .= "{$style_id}{$selector} { {$defs} } \n";
				}
				if ('tag' === $format && !empty($output)) {
					$output = '<style type="text/css">' . $output . '</style>';
				}
			}
		}
		return $output;
	}

	/**
	 * Render responsive style media queries.
	 * 
	 * @since 2.6.6
	 * @access public
	 * @param string $style_id 
	 * @param string $element 
	 * @param array $settings 
	 * @return string
	 */
	public function render_responsive_style($style_id, $element, $settings) {
		static $breakpoints = false;
				static $column_breakpoints = array();
		$output = '';
		$before = '';
		$after = '';
		if(!$breakpoints){
			$breakpoints = themify_get_breakpoints();
			foreach( $breakpoints as $bp => $value ) {
				$breakpoints[ $bp ] = array(is_array($value)?$value[1]:$value);
			}
		}  
		foreach ($breakpoints as $bp => $val) {
			if($this->saving_stylesheet && !isset($column_breakpoints[$bp]) && !empty($settings['col_'.$bp]) && $settings['col_'.$bp]!==($bp.'-auto')){
				$column_breakpoints[$bp] = 1;
				$output.= $this->get_column_breakpoints_styles($bp,$val);
			}
			if ( ! empty( $settings['col_'.$bp] ) ) 
				unset($settings['col_'.$bp]);
			// responsive styling
			if (!empty($settings['breakpoint_' . $bp]) && is_array($settings['breakpoint_' . $bp])) {
				$style = $this->get_custom_styling($style_id, $element, $settings['breakpoint_' . $bp], false, 'css');
				$media_queries =count($val) === 2?
							 sprintf('@media only screen and (min-width : %spx) and (max-width : %spx) {', $val[0], $val[1])
							 :sprintf('@media screen and (max-width: %spx) {', $val[0]);

				$output .= $media_queries.$style.'}'.PHP_EOL;
							  
			}
		}

		if ( '' != $output ) {
			if(!$this->saving_stylesheet){
					$before = '<style type="text/css">';
					$after = '</style>';
			}
			$output = $before . $output . $after;

		}
		return $output;
	}
		
	private function get_column_breakpoints_styles($bp,$val){
		if($bp==='tablet'){
			$val = themify_get_breakpoints('tablet_landscape');
			$val = array(is_array($val)?$val[1]:$val);
		}
		$row_style='';
		$media_queries =  count($val) === 2?
									 sprintf('@media only screen and (min-width : %spx) and (max-width : %spx) {', $val[0], $val[1])
									 :sprintf('@media screen and (max-width: %spx) {', $val[0]);
		return $media_queries.$row_style.PHP_EOL.'}'.PHP_EOL;
	}

		/**
	 * Get custom style
	 *
	 * @since 2.2.5 New parameter $format to return output enclosed in style tags or not.
	 *
	 * @param string $style_id 
	 * @param string $mod_name 
	 * @param array $settings 
	 * @param boolean $array 
	 * @param string $format Use 'tag' to return the CSS enclosed in <style> tags. This mode is used while user is logged in and Builder is active. Use 'css' to return only the CSS. This mode is used on stylesheet generation.
	 *
	 * @return string|array
	 */
	public function get_custom_styling_legacy($style_id, $mod_name, $settings, $array = false, $format = 'tag') {
		global $themify;

		if (!isset($themify->builder_google_fonts)) {
			$themify->builder_google_fonts = '';
		}

		$rules_arr = array(
			'font_size' => array(
				'prop' => 'font-size',
				'key' => array('font_size', 'font_size_unit')
			),
			'font_family' => array(
				'prop' => 'font-family',
				'key' => 'font_family'
			),
			'font_style_regular' => array(
				'prop' => 'font-style',
				'key' => 'font_style_regular'
			),
			'line_height' => array(
				'prop' => 'line-height',
				'key' => array('line_height', 'line_height_unit')
			),
			'letter_spacing' => array(
				'prop' => 'letter-spacing',
				'key' => array('letter_spacing', 'letter_spacing_unit')
			),
			'text_transform' => array(
				'prop' => 'text-transform',
				'key' => 'text_transform'
			),
			'text_decoration_regular' => array(
				'prop' => 'text-decoration',
				'key' => 'text_decoration_regular'
			),
			'text_align' => array(
				'prop' => 'text-align',
				'key' => 'text_align'
			),
			'color' => array(
				'prop' => 'color',
				'key' => 'font_color'
			),
			'link_color' => array(
				'prop' => 'color',
				'key' => 'link_color'
			),
			'text_decoration' => array(
				'prop' => 'text-decoration',
				'key' => 'text_decoration'
			),
			'background_color' => array(
				'prop' => 'background-color',
				'key' => 'background_color'
			),
			'background_image' => array(
				'prop' => 'background-image',
				'key' => 'background_image'
			),
			'background_gradient' => array(
				'prop' => 'background-image',
				'key' => 'background_gradient-gradient'
			),
			'background_overlay' => array(
				'prop' => 'background',
				'key' => array('cover_color','cover_gradient-gradient')
			),
			'background_overlay_hover' => array(
				'prop' => 'background',
				'key' => array('cover_color_hover','cover_gradient_hover-gradient')
			),
			'background_repeat' => array(
				'prop' => 'background-repeat',
				'key' => 'background_repeat'
			),
			'background_position' => array(
				'prop' => 'background-position',
				'key' => array('background_position_x', 'background_position_y')
			),
			'padding' => array(
				'prop' => 'padding',
				'key' => array('padding_top', 'padding_right', 'padding_bottom', 'padding_left')
			),
			'margin' => array(
				'prop' => 'margin',
				'key' => array('margin_top', 'margin_right', 'margin_bottom', 'margin_left')
			),
			'border_top' => array(
				'prop' => 'border-top',
				'key' => array('border_top_color', 'border_top_width', 'border_top_style')
			),
			'border_right' => array(
				'prop' => 'border-right',
				'key' => array('border_right_color', 'border_right_width', 'border_right_style')
			),
			'border_bottom' => array(
				'prop' => 'border-bottom',
				'key' => array('border_bottom_color', 'border_bottom_width', 'border_bottom_style')
			),
			'border_left' => array(
				'prop' => 'border-left',
				'key' => array('border_left_color', 'border_left_width', 'border_left_style')
			)
		);
		if(in_array($mod_name,array('row','column','subrow','sub_column'))){
			$selector = $mod_name==='sub_column'?'.'.$mod_name:'.module_'.$mod_name;
			$styles_selector = array(
				$selector=> array(
						'background_image','background_gradient', 'background_color', 'font_family', 'font_size', 'line_height', 'letter_spacing', 'text_align', 'color', 'padding', 'margin', 'border_top', 'border_right', 'border_bottom', 'border_left', 'text_transform', 'text_decoration_regular', 'font_style_regular'
				),
				' a' => array(
						'link_color', 'text_decoration'
				),
				$selector.' h1' => array('color', 'font_family'),
				$selector.' h2' => array('color', 'font_family'),
				$selector.' h3:not(.module-title)' => array('color', 'font_family'),
				$selector.' h4' => array('color', 'font_family'),
				$selector.' h5' => array('color', 'font_family'),
				$selector.' h6' => array('color', 'font_family'),
				$selector.'>.builder_row_cover' => array('background_overlay'),
				$selector.'> div > .builder_row_cover' => array('background_overlay'), // Fix overlay on split theme
				$selector.'>.builder_row_cover:before' => array('background_overlay_hover'),
			);
		}
		else{
			$styles_selector = Themify_Builder_Model::$modules[$mod_name]->get_css_selectors();
		}
		$rules = array();
		$css = array();
		$style = '';

		foreach ($styles_selector as $selector => $properties) {
			$property_arr = array();
			foreach ($properties as $property) {
				$property_arr[] = $rules_arr[$property];
			}
			$rules[$style_id . $selector] = $property_arr;
		}
		$web_fonts =  themify_get_web_safe_font_list(true);

		// set default settings for row/column, and subrow/sub_column
		// these are not saved by default, so we need to manually inject them in
		$settings = wp_parse_args( $settings, array(
			'background_type' => 'image',
			'cover_color-type' => 'color',
			'cover_color_hover-type' => 'hover_color',
			'background_gradient-gradient' => $this->default_gradient(),
			'cover_gradient-gradient' => $this->default_gradient(),
			'cover_gradient_hover-gradient' => $this->default_gradient(),
		) );

		foreach ($rules as $selector => $property) {
			if(empty($css[$selector])){
				$css[$selector] = array();
			}
			foreach ($property as $val) {
				$prop = $val['prop'];
				$key = $val['key'];
				if (is_array($key)) {
					if ($prop === 'font-size' && !empty($settings[$key[0]]) ) {
						$css[$selector][$prop] = $prop . ': ' . $settings[$key[0]] . $settings[$key[1]];
					} elseif ($prop === 'line-height' && !empty($settings[$key[0]])) {
						$css[$selector][$prop] = $prop . ': ' . $settings[$key[0]] . $settings[$key[1]];
					} elseif ($prop === 'letter-spacing' && !empty($settings[$key[0]])) {
						$css[$selector][$prop] = $prop . ': ' . $settings[$key[0]] . $settings[$key[1]];
					} elseif ($prop === 'background-position' && !empty($settings[$key[0]])) {
						$css[$selector][$prop] = $prop . ': ' . $settings[$key[0]] . ' ' . $settings[$key[1]];
					} elseif ($prop === 'padding') {
						$padding['top'] = !empty($settings[$key[0]])? $settings[$key[0]] : '';
						$padding['right'] = !empty($settings[$key[1]]) ? $settings[$key[1]] : '';
						$padding['bottom'] = !empty($settings[$key[2]]) ? $settings[$key[2]] : '';
						$padding['left'] = !empty($settings[$key[3]]) ? $settings[$key[3]] : '';

						foreach ($padding as $k => $v) {
							if ('' == $v){
								continue;
							}
							$unit = isset($settings["padding_{$k}_unit"]) ? $settings["padding_{$k}_unit"] : 'px';
							$css[$selector]['padding-' . $k] = 'padding-' . $k . ' : ' . $v . $unit;
						}
					} elseif ($prop === 'margin') {
						$margin['top'] = !empty($settings[$key[0]])  ? $settings[$key[0]] : '';
						$margin['right'] = !empty($settings[$key[1]])  ? $settings[$key[1]] : '';
						$margin['bottom'] = !empty($settings[$key[2]])  ? $settings[$key[2]] : '';
						$margin['left'] = !empty($settings[$key[3]])  ? $settings[$key[3]] : '';

						foreach ($margin as $k => $v) {
							if ('' == $v){
								continue;
							}
							$unit = isset($settings["margin_{$k}_unit"]) ? $settings["margin_{$k}_unit"] : 'px';
							$css[$selector]['margin-' . $k] = 'margin-' . $k . ' : ' . $v . $unit;
						}
					} elseif (in_array($prop, array('border-top', 'border-right', 'border-bottom', 'border-left'))) {

						if(!empty($settings[$key[1]])){
							$border['color'] = !empty($settings[$key[0]])  ? $settings[$key[0]] : '';
							$border['width'] = !empty($settings[$key[1]]) ? $settings[$key[1]] . 'px' : '';
							$border['style'] = isset( $settings[$key[2]] ) ? $settings[$key[2]] : 'solid';
							$border_result = $this->build_color_props(array(
								'color_opacity' => $border['color'],
								'property' => $prop,
								'border_width' => $border['width'],
								'border_style' => $border['style'],
							) );
							
							if($border_result){
								$css[$selector][$prop] = $border_result;
							}
						}
					}
					elseif($prop==='background' && !isset($css[$selector][$prop]) && (!empty($settings[$key[0]]) || !empty($settings[$key[1]]))){
						if($key[0]==='cover_color'){
							if( !empty($settings[$key[0]]) && ( empty( $settings['cover_color-type'] ) || $settings['cover_color-type'] === 'color' ) ) {
								 $css[$selector][$prop] = $prop . ':'.self::get_rgba_color($settings[$key[0]]);
							}
							elseif(!empty($settings['cover_color-type']) && !empty($settings['cover_gradient-gradient']) && $settings['cover_color-type']==='cover_gradient' && !empty($settings[$key[1]])){
								$css[$selector][$prop] = $prop . ':' . $this->get_gradient( $settings, 'cover_gradient' );
							}
						}
						elseif($key[0]==='cover_color_hover'){
						   if(!empty($settings[$key[0]])  && (empty($settings['cover_color_hover-type']) || $settings['cover_color_hover-type']==='hover_color')){
							   $css[$selector][$prop] =  $prop . ':'.self::get_rgba_color($settings[$key[0]]);
						   }
						   elseif(!empty($settings['cover_color_hover-type']) && !empty($settings['cover_gradient_hover-gradient']) && $settings['cover_color_hover-type']==='hover_gradient' && !empty($settings[$key[1]])){
							   $css[$selector][$prop] = $prop . ':' . $this->get_gradient( $settings, 'cover_gradient_hover' );
						   }
						}
					}
				}
				elseif (!empty($settings[$key]) && 'default' !== $settings[$key]) {
					if ($prop === 'color' || stripos($prop, 'color')) {
						$css[$selector][$prop] = $this->build_color_props( array(
							'color_opacity' => $settings[$key],
							'property' => $prop,
						) );
					} 
					elseif ($prop === 'background-image' && 'default' !== $settings[$key] &&  !empty( $settings[$key] )) {
						if(isset($settings['background_type']) && $key==='background_gradient-gradient' && $settings['background_type']==='gradient'){
							$css[$selector][$prop] = $prop . ':' . $this->get_gradient( $settings, 'background_gradient' );
						}
						elseif(isset($settings['background_type']) && $settings['background_type']!=='gradient' && $key!=='background_gradient-gradient') {
							$css[$selector][$prop] = $prop . ': url(' . themify_https_esc($settings[$key]) . ')';
							if ('video' === $settings['background_type']) {
								$css[$selector][$prop] .= ";\n\tbackground-size: cover";
							}
						}
					} elseif ($prop === 'font-family') {
						$font = $settings[$key];
						$css[$selector][$prop] = $prop . ': ' . $font;
						if (!in_array($font,$web_fonts)) {
							$themify->builder_google_fonts .= str_replace(' ', '+', $font . '|');
						}
					} else {
						$css[$selector][$prop] = $prop . ': ' . $settings[$key];
					}
				}
			}

			if (!empty($css[$selector])) {
				$style .= "$selector {\n\t" . implode(";\n\t", array_map(array($this, 'trim_last_semicolon'), $css[$selector])) . "\n}\n";
			}	
		}

		if( !empty( $settings[ 'width' ] ) ) {
			reset( $rules );
			$style .= sprintf( '@media ( min-width: 1025px ) { %s{ width: %s !important; } }'
				, key( $rules ), $settings[ 'width' ] );
		}

		if (!$array) {
			if ('' != $style) {
				if ('tag' === $format) {
					return "\n<!-- $style_id Style -->\n<style type=\"text/css\" >\n$style</style>\n<!-- End $style_id Style -->\n";
				} else {
					return "/* $style_id Style */\n$style\n";
				}
			}
		} else if ($array) {
			return $css;
		}
	}

	/**
	 * Tries to enqueue stylesheet. If it's not possible, it hooks an action to wp_head to build the CSS and output it.
	 * 
	 * @since 2.2.5
	 */
	public function enqueue_stylesheet() {
		if ($this->is_enqueue_stylesheet()) {
			// If enqueue fails, maybe the file doesn't exist...
			if (!$this->test_and_enqueue()) {
				// Try to generate it right now.
				if ($post_data = get_post_meta(get_the_ID(), $this->builder->meta_key, true)) {
					// Write Stylesheet
					$this->write_stylesheet(array('id' => get_the_ID(), 'data' => $post_data));
				}
				if (!$this->test_and_enqueue()) {
					// No luck. Let's do it inline.
					$this->is_front_end_style_inline = true;
					add_action('themify_builder_row_start', array($this, 'render_row_styling'), 10, 2);
					add_action('themify_builder_sub_row_start', array($this, 'render_sub_row_styling'), 10, 4);
					add_action('themify_builder_column_start', array($this, 'render_column_styling'), 10, 3);
					add_action('themify_builder_sub_column_start', array($this, 'render_sub_column_styling'), 10, 5);
				}
			}
		} else {
			// No luck. Let's do it inline.
			$this->is_front_end_style_inline = true;
			add_action('themify_builder_row_start', array($this, 'render_row_styling'), 10, 2);
			add_action('themify_builder_sub_row_start', array($this, 'render_sub_row_styling'), 10, 4);
			add_action('themify_builder_column_start', array($this, 'render_column_styling'), 10, 3);
			add_action('themify_builder_sub_column_start', array($this, 'render_sub_column_styling'), 10, 5);
		}
	}

	/**
	 * Write stylesheet file.
	 * 
	 * @since 2.2.5
	 * 
	 * @return array
	 */
	public function write_stylesheet($data_set) {
		// Information about how writing went.
		$results = array();

		$this->saving_stylesheet = true;
		$style_id = $data_set['id'];

		$css_to_save = $this->recursive_style_generator($data_set['data'], $style_id);

		$css_file = $this->get_stylesheet('bydir', (int) $style_id);

		$filesystem = Themify_Filesystem::get_instance();

		if ($filesystem->execute->is_file($css_file)) {
			$filesystem->execute->delete($css_file);
		}

		// Write file information to be returned.
		$results['css_file'] = $css_file;

		if (!empty($css_to_save)) {
			/**
			 * Filters the CSS that will be saved for modules that output inline <style> tags for styling changes not managed by get_styling_settings().
			 * 
			 * @since 2.2.5
			 *
			 * @param string $css_to_save CSS text right before it's saved.
			 */
			$css_to_save = apply_filters('themify_builder_css_to_stylesheet', $css_to_save);
			if ($write = $filesystem->execute->put_contents($css_file, $css_to_save, FS_CHMOD_FILE)) {
				update_option('themify_builder_stylesheet_timestamp', current_time('y.m.d.H.i.s'));
			}

			// Add information about writing.
			$results['write'] = $write;

			// Save Google Fonts
			global $themify;
			if (isset($themify->builder_google_fonts) && !empty($themify->builder_google_fonts)) {
				$builder_fonts = get_option('themify_builder_google_fonts');
				if (empty($builder_fonts) || !is_array($builder_fonts)) {
					$builder_fonts = array();
				}
				if (isset($builder_fonts[$style_id])) {
					$builder_fonts[$style_id] = $themify->builder_google_fonts;
					$entry_fonts = $builder_fonts;
				} else {
					$entry_fonts = array($style_id => $themify->builder_google_fonts) + $builder_fonts;
				}
				update_option('themify_builder_google_fonts', $entry_fonts);
			}
		} else {
			// Add information about writing.
			$results['write'] = esc_html__('Nothing written. Empty CSS.', 'themify');
		}

		$this->saving_stylesheet = false;

		return $results;
	}

	/**
	 * Build style recursively. Written for sub_row styling generation.
	 * 
	 * @since 2.2.6
	 * 
	 * @param array $data Collection of styling data.
	 * @param int $style_id ID of the current entry.
	 * @param string $sub_row Row ID when it's a sub row. This is used starting from second level depth.
	 *
	 * @return string
	 */
	function recursive_style_generator($data, $style_id, $sub_row = '') {
		$css_to_save = '';
		$css_col_custom = '';
		if (!is_array($data)) {
			return $css_to_save;
		}
		$is_subrow = !empty($sub_row);
		foreach ($data as $row_index => $row) {
			$row_order = $row_index;
			
			if (isset($row['row_order'])) {
				$row_order = $row['row_order'];
			}
			if (!empty($row['col_tablet'])) {
				$row['styling']['col_tablet'] = $row['col_tablet'];
			}
			if (!empty($row['col_mobile'])) {
				$row['styling']['col_mobile'] = $row['col_mobile'];
			}
			if (!$is_subrow && !empty($row['styling']) && is_array($row['styling'])) {
				$selector = ".themify_builder_{$style_id}_row.module_row_{$row_order}";

				$css_to_save .= $this->get_custom_styling($selector, 'row', $row['styling'], false, 'css');

				// responsive styling
				$css_to_save .= $this->render_responsive_style($selector, 'row', $row['styling']);
			}
			if($is_subrow){
                            $sub_row_parts = explode('-',str_replace('sub_row_','',$sub_row));
                            // Sub Row Style
                            if(!empty($row['styling']) && is_array($row['styling'])){

                                    $selector2 = '.module_row_' . $sub_row_parts[0] . ' .module_column_' . $sub_row_parts[1] . ' .sub_row_' . $sub_row_parts[0] . '-' . $sub_row_parts[1] . '-' . $row['row_order'];
                                    $css_to_save .= $this->get_custom_styling($selector2, "subrow", $row['styling'], false, 'css');
                                    // responsive styling
                                    $css_to_save .= $this->render_responsive_style($selector2, "subrow", $row['styling']);
                            }
                        }
			
			if (!isset($row['cols']) || !is_array($row['cols'])) {
				continue;
			}
			foreach ($row['cols'] as $col_index => $col) {
				$column_order = $col_index;

				if (isset($col['column_order'])) {
					$column_order = $col['column_order'];
				}

				// column styling
				if (!empty($col['grid_width']) || (!empty($col['styling']) && is_array($col['styling']))) {

					$col_or_sub_col = 'column';

					// dealing with 1st level columns
					if (!$is_subrow) {
						$selector = ".module_row_{$row_order}" ." .module_column_{$column_order}.tb_{$style_id}_column";
					} else { // dealing with 2nd level columns (sub-columns)
						$row_col = $sub_row_parts[0] . '-' . $sub_row_parts[1];
						$selector = ".sub_column_post_{$style_id}.sub_column_{$row_col}-{$row_order}-{$column_order}";

						$col_or_sub_col = 'sub_column';
					}
					if((!empty($col['styling']) && is_array($col['styling']))){
						$css_to_save .= $this->get_custom_styling($selector, $col_or_sub_col, $col['styling'], false, 'css');

						// responsive styling
						$css_to_save .= $this->render_responsive_style($selector, $col_or_sub_col, $col['styling']);
					}
					if(!empty($col['grid_width'])){
						if($col_or_sub_col==='sub_column'){
							$selector = '.themify_builder_sub_row .'.$col_or_sub_col.$selector;
						} else {
							$selector = '.themify_builder_row' . $selector;
						}

						$css_col_custom .= sprintf( '%s{ width: %s !important; }' . "\n\t"
							, $selector, $col[ 'grid_width' ] . '%' );
						$css_to_save .= end( array_keys( $row['cols'] ) ) === $col_index
							? sprintf( "@media (min-width: 1025px) {\n\t%s\n}\n", $css_col_custom ) : '';
					}
				}

				if (!isset($col['modules']) || !is_array($col['modules'])) {
					continue;
				}
				foreach ($col['modules'] as $mod_index => $mod) {
					if (isset($mod['mod_name'])) {
						if ('layout-part' === $mod['mod_name']) {
							$lp = get_page_by_path($mod['mod_settings']['selected_layout_part'], OBJECT, 'tbuilder_layout_part');
							$lp_meta = get_post_meta($lp->ID, $this->builder->meta_key, true);
							Themify_Builder::remove_cache($lp->ID);
							if (!empty($lp_meta)) {
								foreach ($lp_meta as $lp_row_index => $lp_row) {
									if (!empty($lp_row['styling']) && is_array($lp_row['styling'])) {
										$css_to_save .= $this->get_custom_styling(".themify_builder_content-$lp->ID .module_row_{$lp_row['row_order']}", 'row', $lp_row['styling'], false, 'css');
									}
									if (isset($lp_row['cols']) && is_array($lp_row['cols'])) {
										foreach ($lp_row['cols'] as $lp_col_index => $lp_col) {
											if (isset($lp_col['modules']) && is_array($lp_col['modules'])) {
												foreach ($lp_col['modules'] as $lp_mod_index => $lp_mod) {
													if (is_null($lp_mod)) {
														continue;
													}
													if (empty($sub_row)) {
														$this_index = "$lp_row_index-$lp_col_index-$lp_mod_index";
													} else {
														if (isset($row['row_order'])) {
															$this_index = $sub_row . "{$row['row_order']}-$lp_col_index-$lp_mod_index";
														} else {
															$sr_index = $row_index + 1;
															$this_index = $sub_row . "$sr_index-$lp_col_index-$lp_mod_index";
														}
													}
													$css_to_save .= $this->get_custom_styling(".themify_builder .{$lp_mod['mod_name']}-$lp->ID-$this_index", $lp_mod['mod_name'], $lp_mod['mod_settings'], false, 'css');
												}
											}
										}
									}
								}
							}
						} else {
							if (empty($sub_row)) {
								$this_index = "$row_index-$col_index-$mod_index";
							} else {
								if (isset($row['row_order'])) {
									$this_index = $sub_row . "{$row['row_order']}-$col_index-$mod_index";
								} else {
									$sr_index = $row_index + 1;
									$this_index = $sub_row . "$sr_index-$col_index-$mod_index";
								}
							}
							$css_to_save .= $this->get_custom_styling(".themify_builder .{$mod['mod_name']}-$style_id-$this_index", $mod['mod_name'], $mod['mod_settings'], false, 'css');

							// responsive styling modules
							$css_to_save .= $this->render_responsive_style(".themify_builder .{$mod['mod_name']}-$style_id-$this_index", $mod['mod_name'], $mod['mod_settings']);
						}
					}
					if (isset($mod['row_order'])) {
						$css_to_save .= $this->recursive_style_generator(array($mod), $style_id, "sub_row_$row_index-$col_index-");
					}
				}
			}
		}
		return $css_to_save;
	}

	/**
	 * Return the URL or the directory path for a template, template part or content builder styling stylesheet.
	 * 
	 * @since 2.2.5
	 *
	 * @param string $mode Whether to return the directory or the URL. Can be 'bydir' or 'byurl' correspondingly. 
	 * @param int $single ID of layout, layour part or entry that we're working with.
	 *
	 * @return string
	 */
	public static function get_stylesheet($mode = 'bydir', $single = null) {
		static $before;
		if (!isset($before)) {
			$upload_dir = wp_upload_dir();
			$before = array(
				'bydir' => $upload_dir['basedir'],
				'byurl' => $upload_dir['baseurl'],
			);
		}
		if (is_null($single)) {
			$single = get_the_ID();
		}

		$single = is_int($single) ? get_post($single) : get_page_by_path($single, OBJECT, 'tbuilder_layout_part');

		if (!is_object($single)) {
			return '';
		}

		$single = $single->ID;

		$path = "$before[$mode]/themify-css";

		if ('bydir' == $mode) {
			if (!function_exists('WP_Filesystem')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			WP_Filesystem();
			global $wp_filesystem;
			$dir_exists = $wp_filesystem->is_dir($path);
			if (!$dir_exists) {
				$dir_exists = $wp_filesystem->mkdir($path, FS_CHMOD_DIR);
			}
		}

		$stylesheet = "$path/themify-builder-$single-generated.css";

		/**
		 * Filters the return URL or directory path including the file name.
		 *
		 * @param string $stylesheet Path or URL for the global styling stylesheet.
		 * @param string $mode What was being retrieved, 'bydir' or 'byurl'.
		 * @param int $single ID of the template, template part or content builder that we're fetching.
		 *
		 */
		return apply_filters('themify_builder_get_stylesheet', $stylesheet, $mode, $single);
	}

	/**
	 * Checks if the builder stylesheet exists and enqueues it. Otherwise hooks an action to wp_head to build the CSS and output it.
	 * 
	 * @since 2.2.5
	 */
	function delete_stylesheet() {
		$css_file = $this->get_stylesheet();
		$filesystem = Themify_Filesystem::get_instance();

		if ($filesystem->execute->is_file($css_file)) {
			$filesystem->execute->delete($css_file);
		}
	}

	/**
	 * If there wasn't a proper stylesheet, that is, one that matches this slug, generate it.
	 *
	 * @since 2.2.5
	 *
	 * @param int $post_id
	 */
	function build_stylesheet_if_needed($post_id) {
		//verify post is not a revision
		if (!wp_is_post_revision($post_id)) {
			if (!$this->is_readable_and_not_empty($this->get_stylesheet('bydir', $post_id))) {
				if ($post_data = get_post_meta($post_id, $this->builder->meta_key, true)) {
					// Write Stylesheet
					$this->write_stylesheet(array('id' => $post_id, 'data' => $post_data));
				}
			}
		}
	}

	/**
	 * Checks if the builder stylesheet exists and enqueues it.
	 * 
	 * @since 2.2.5
	 * 
	 * @return bool True if enqueue was successful, false otherwise.
	 */
	public function test_and_enqueue() {
		$stylesheet_path = $this->get_stylesheet();
		if ($this->is_readable_and_not_empty($stylesheet_path)) {
			setlocale(LC_CTYPE, get_locale() . '.UTF-8');
			$handler = pathinfo($stylesheet_path);
			wp_enqueue_style($handler['filename'], themify_https_esc($this->get_stylesheet('byurl')), array(), $this->get_stylesheet_version());
			// Load Google Fonts. Despite this function is hit twice while on-the-fly stylesheet generation, they're loaded only once.
			add_filter( 'themify_google_fonts', array( $this, 'enqueue_fonts' ) );
			return true;
		}
		return false;
	}

	/**
	 * Return timestamp to use as stylesheet version.
	 * 
	 * @since 2.2.5
	 */
	function get_stylesheet_version() {
		return get_option('themify_builder_stylesheet_timestamp');
	}

	/**
	 * Enqueues Google Fonts
	 * 
	 * @since 2.2.6
	 */
	public function enqueue_fonts( $google_fonts ) {
		$entry_google_fonts = get_option('themify_builder_google_fonts');
		if ( ! empty( $entry_google_fonts ) && is_array( $entry_google_fonts ) ) {
			$get_layout_part_ids = $this->builder->get_layout_part_ids( get_the_id() );
			$entry_ids = array( get_the_id() );

			if( ! empty( $get_layout_part_ids ) ) {
				$entry_ids = array_merge( $entry_ids, $get_layout_part_ids );
			}

			foreach( $entry_ids as $entry_id ) {
				if ( isset( $entry_google_fonts[$entry_id] ) ) {
					$fonts = explode( '|', $entry_google_fonts[$entry_id] );
					$fonts = array_unique( array_filter( $fonts ) );
					foreach( $fonts as $font ) {
						$google_fonts[] = $font;
					}
				}
			}

		}

		return $google_fonts;
	}

	/**
	 * Make styling rules.
	 * 
	 * @access public
	 * @param array $def 
	 * @param array $settings 
	 * @param boolean $empty 
	 * @return array
	 */
	public function make_styling_rules($def, $settings, $empty = false) {
		$result = array();
		if (empty($def)) {
			return $result;
		}

		foreach ($def as $option) {

			/* add default value to the $setting array */
			if( isset( $option['default'] ) && ! isset( $settings[ $option['id'] ] ) ) {
				$settings[ $option['id'] ] = $option['default'];
			}

			if ($option['type'] === 'multi') {
				$result = array_merge($result, $this->make_styling_rules($option['fields'], $settings, $empty));
			} elseif ($option['type'] === 'tabs') {
				foreach ($option['tabs'] as $tab) {
					$result = array_merge($result, $this->make_styling_rules($tab['fields'], $settings, $empty));
				}
			} elseif( $option['type'] === 'image_and_gradient' && !$empty ) {
				$new = false;
				if( isset($settings[$option['id'] . '-type'], $settings[$option['id'] . '-gradient']) && $settings[$option['id'] . '-type'] === 'gradient' ) {
					$new = array(
						'id' => $option['id'],
						// 'value' => $this->get_gradient( $settings, $option['id'] ),
						'prop' => 'background-image',
						'type' => 'gradient'
					);
				} elseif( ( ! isset( $settings[$option['id'] . '-type'] ) || ( isset($settings[$option['id'] . '-type']) && $settings[$option['id'] . '-type'] === 'image' ) ) && isset( $settings[$option['id']] ) ) {
					$new = array(
						'id' => $option['id'],
						'value' => $settings[$option['id']],
						'prop' => 'background-image',
						'type' => 'image'
					);
				}
				if( $new ) {
					foreach ((array) $option['selector'] as $selector) {
						$result[] = array_merge( $new, array( 'selector' => $selector ) );
				   }
				}
			}
			elseif (isset($option['prop']) && (isset($settings[$option['id']]) || $empty)) {
				if ($empty) {
					if($option['type']!=='seperator'){
						$result[$option['id']] = $option;
					}
				} else {
					foreach ((array) $option['selector'] as $selector) {
						$result[] = array(
							'id' => $option['id'],
							'prop' => $option['prop'],
							'type' => $option['type'],
							'selector' => $selector,
							'value' => $settings[$option['id']]
						);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Checks whether a file exists, can be loaded and is not empty.
	 * 
	 * @since 2.2.5
	 * 
	 * @param string $file_path Path in server to the file to check.
	 * 
	 * @return bool
	 */
	public function is_readable_and_not_empty($file_path = '') {
		if (empty($file_path)) {
			return false;
		}
		return is_readable($file_path) && 0 !== filesize($file_path);
	}

	/**
	 * If string has an semicolon at the end, it will be stripped.
	 *
	 * @since 2.3.3
	 *
	 * @param string $string
	 * @return string
	 */
	public function trim_last_semicolon($string) {
		return rtrim($string, ';');
	}

	/**
	 * Outputs color for the logo in text mode since it's needed for the <a>.
	 *
	 * @since 1.9.6
	 *
	 * @param array $args
	 * @return string
	 */
	protected function build_color_props($args = array()) {
		$args = wp_parse_args($args, array(
			'color_opacity' => '',
			'property' => 'color',
			'border_width' => '',
			'border_style' => 'solid',
		));
		// Strip any lingering hashes just in case
		$args['color_opacity'] = str_replace('#', '', $args['color_opacity']);
		// Separator between color and opacity
		$sep = '_';

		if (false !== stripos($args['color_opacity'], $sep)) {
			// If it's the new color+opacity, an underscore separates color from opacity
			$all = explode($sep, $args['color_opacity']);
			$color = isset($all[0]) ? $all[0] : '';
			$opacity = isset($all[1]) ? $all[1] : '';
		} else {
			// If it's the traditional, it's a simple color
			$color = $args['color_opacity'];
			$opacity = '';
		}
		$element_props = '';
		if ('' != $color || false !== stripos($args['property'], 'border')) {
			// Setup opacity value or solid
			$opacity = ( '' != $opacity ) ? $opacity : '1';
			if (false !== stripos($args['property'], 'border')) {
				// It's a border property, a composite of border size style
				
				if($args['border_style']!=='none'){
					if(!empty($args['border_width'])){
						$element_props .= "\n\t{$args['property']}: rgba(" . self::hex2rgb($color) . ",  $opacity) {$args['border_width']} {$args['border_style']}";
					}
					else{
						return false;
					}
				}
				else{
					 $element_props .= "{$args['property']}: {$args['border_style']};";
				}
			} else {
				// It's either background-color or color, a simple color
				$element_props .= "\n\t{$args['property']}: rgba(" . self::hex2rgb($color) . ", $opacity)";
			}
		}
		return $element_props;
	}

	/**
	 * Converts color in hexadecimal format to RGB format.
	 *
	 * @since 1.9.6
	 *
	 * @param string $hex Color in hexadecimal format.
	 * @return string Color in RGB components separated by comma.
	 */
	public static function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3) {
			$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
		} else {
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}
		return implode(',', array($r, $g, $b));
	}

	/**
	 * Get RGBA color format from hex color
	 *
	 * @return string
	 */
	public static function get_rgba_color($color) {
		if( $color === '_' || $color === '_1' )
			return;

		$color = explode('_', $color);
		$opacity = isset($color[1]) ? $color[1] : '1';
		return 'rgba(' . self::hex2rgb($color[0]) . ', ' . $opacity . ')';
	}

	/**
	 * Check whether css style should be rendered in a stylesheet file.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function is_enqueue_stylesheet() {
		return apply_filters('themify_builder_enqueue_stylesheet', true);
        }
}
