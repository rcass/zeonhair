<?php
/**
 * Fontello icon font
 * @link http://fontello.com/
 */
class Themify_Icon_Picker_Fontello extends Themify_Icon_Picker_Font {

	function get_id() {
		return 'fontello';
	}

	function get_label() {
		return __( 'Fontello', 'themify' );
	}

	/**
	 * Check if the icon name belongs to the Fontello icon font
	 *
	 * @return bool
	 */
	function is_valid_icon( $name ) {
		if( substr( $name, 0, 5 ) === 'icon-' ) {
			return true;
		}

		return false;
	}

	function get_classname( $icon ) {
		return $icon;
	}

	/**
	 * Get a list of available icons from the config.json file provided by Fontello
	 *
	 * @return array
	 */
	function get_icons() {
		$icons = array();
		if( themify_check( 'setting-fontello' ) ) {
			if( $config = themify_get_file_contents( $this->get_path() . 'config.json' ) ) {
				$config = json_decode( $config, true );
				if( isset( $config['glyphs'] ) && ! empty( $config['glyphs'] ) ) {
					foreach( $config['glyphs'] as $glyph ) {
						$icons[ 'icon-' . $glyph['css'] ] = $glyph['css'];
					}
				}
			}
		}
		return array(
			array(
				'key' => 'custom',
				'label' => __( 'Icons', 'themify' ),
				'icons' => $icons
			),
		);
	}

	/**
	 * Get the system path to where Fontello assets are located
	 *
	 * @return string
	 */
	function get_path() {
		$path = themify_fontello_path();
		if( $path ) {
			return $path['dir'];
		}
	}
}