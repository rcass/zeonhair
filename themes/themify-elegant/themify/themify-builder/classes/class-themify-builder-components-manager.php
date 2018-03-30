<?php

class Themify_Builder_Components_Manager {
	
	/**
	 * @var $_component_types;
	 */
	private $_component_types;

	public function __construct() {
		$this->includes();
	}

	private function includes() {
		include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/base.php' );
		include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/row.php' );
		include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/subrow.php' );
		include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/components/column.php' );
	}

	public function register_component_type( Themify_Builder_Component_Base $component ) {
		$this->_component_types[ $component->get_name() ] = $component;

		return true;
	}

	public function get_component_types( $component_name = null ) {
		if ( is_null( $this->_component_types ) ) {
			$this->_init_components();
		}

		if ( null !== $component_name ) {
			return isset( $this->_component_types[ $component_name ] ) ? $this->_component_types[ $component_name ] : null;
		}

		return $this->_component_types;
	}

	private function _init_components() {
		$this->_component_types = array();

		foreach ( array( 'row', 'subrow', 'column' ) as $component_name ) {
			$class_name = 'Themify_Builder_Component_' . ucfirst( $component_name );

			$this->register_component_type( new $class_name() );
		}
	}

	public function render_components_form_content() {
		foreach ( $this->get_component_types() as $component_type ) {
			$component_type->print_template_form();
		}

		$this->render_modules_form_content();
	}

	public function render_modules_form_content() {
		foreach( Themify_Builder_Model::$modules as $module ) {
			$module->print_template_form();
		}
	}
}