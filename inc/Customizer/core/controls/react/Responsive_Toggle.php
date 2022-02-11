<?php

namespace Stax\Customizer\Core\Controls\React;

class Responsive_Toggle extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_responsive_toggle_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $excluded_devices = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['excluded'] = $this->excluded_devices;
	}

}
