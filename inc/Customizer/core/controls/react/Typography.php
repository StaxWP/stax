<?php

namespace Stax\Customizer\Core\Controls\React;

class Typography extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_typeface_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $input_attrs = [];

	/**
	 * Refresh on reset flag.
	 *
	 * @var bool
	 */
	public $refresh_on_reset = false;

	/**
	 * The control that holds the font family used by this control
	 *
	 * @var string
	 */
	public $font_family_control = '';

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['input_attrs']         = is_array( $this->input_attrs ) ? wp_json_encode( $this->input_attrs ) : $this->input_attrs;
		$this->json['refresh_on_reset']    = $this->refresh_on_reset;
		$this->json['font_family_control'] = $this->font_family_control;
	}

}
