<?php

namespace Stax\Customizer\Core\Controls\React;

class Multiselect extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_multiselect';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $choices = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['choices'] = $this->choices;
	}

}
