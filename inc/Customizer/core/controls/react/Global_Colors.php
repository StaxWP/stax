<?php

namespace Stax\Customizer\Core\Controls\React;

class Global_Colors extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_global_colors';
	/**
	 * Default values.
	 *
	 * @var string
	 */
	public $default_values = '';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var string
	 */
	public $input_attrs = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['defaultValues'] = $this->default_values;
		$this->json['input_attrs']   = $this->input_attrs;
	}

}
