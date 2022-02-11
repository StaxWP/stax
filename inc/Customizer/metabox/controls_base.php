<?php

namespace Stax\Metabox;

abstract class Controls_Base {

	/**
	 * Controls.
	 *
	 * @var array
	 */
	private $controls = [];

	/**
	 * Init function
	 */
	public function init() {
		$this->add_controls();
	}

	/**
	 * Add controls.
	 */
	abstract protected function add_controls();

	/**
	 * Add the control.
	 *
	 * @param Controls\Control_Base|Object $control the control object.
	 */
	public function add_control( $control ) {
		array_push( $this->controls, $control );
	}

	/**
	 * Get the controls.
	 *
	 * @return array
	 */
	public function get_controls() {
		return $this->controls;
	}

}
