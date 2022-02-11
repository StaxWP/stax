<?php

namespace Stax\Customizer\Core\Controls\React;

class Repeater extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_repeater_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $fields = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['fields'] = $this->fields;
	}

}
