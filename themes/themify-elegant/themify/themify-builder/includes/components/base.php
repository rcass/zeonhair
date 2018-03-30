<?php

class Themify_Builder_Component_Base {
	public function __construct() {}

	public function get_type() {
		return 'component';
	}
	
	public function get_name() {}

	public final function get_class_name() {
		return get_class( $this );
	}

	public function get_settings() {}

	public function get_style_settings() {
		return array();
	}

	public function get_form_settings() {}

	protected function _form_template() {}

	public function print_template_form() {
		ob_start();

		$this->_form_template();

		$output = ob_get_clean();

		if ( empty( $output ) ) {
			return;
		}
		?>
		<script type="text/html" id="tmpl-builder_form_<?php echo esc_attr( $this->get_name() ); ?>">
			<?php echo $output; ?>
		</script>
		<?php
	}
}