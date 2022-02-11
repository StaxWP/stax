<?php

namespace Stax\Customizer\Core\Controls\React;

class Ordering extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_ordering_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $components = [];

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $default_order = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['components']   = $this->components;
		$this->json['defaultOrder'] = $this->default_order;
	}

}
