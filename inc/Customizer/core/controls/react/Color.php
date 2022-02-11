<?php

namespace Stax\Customizer\Core\Controls\React;

class Color extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_color_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var string
	 */
	public $default = '';

	/**
	 * Disable Alpha in colorpicker.
	 *
	 * @var bool
	 */
	public $disable_alpha = false;

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['default']      = $this->default;
		$this->json['disableAlpha'] = $this->disable_alpha;
		$this->json['input_attrs']  = $this->input_attrs;
	}

}
