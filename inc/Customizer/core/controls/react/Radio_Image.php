<?php

namespace Stax\Customizer\Core\Controls\React;

class Radio_Image extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_radio_image_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $choices = [];

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $documentation = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['choices']       = $this->choices;
		$this->json['documentation'] = $this->documentation;
	}

}
