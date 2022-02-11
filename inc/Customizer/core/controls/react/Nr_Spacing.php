<?php

namespace Stax\Customizer\Core\Controls\React;

class Nr_Spacing extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_non_responsive_spacing';

	/**
	 * Min.
	 *
	 * @var int
	 */
	public $min = 0;

	/**
	 * Max.
	 *
	 * @var int
	 */
	public $max = 300;

	/**
	 * Units.
	 *
	 * @var array
	 */
	public $units = [ 'px', 'em', '%' ];

	/**
	 * Default value.
	 *
	 * @var array
	 */
	public $default = [];

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['min']        = $this->min;
		$this->json['max']        = $this->max;
		$this->json['units']      = $this->units;
		$this->json['defaultVal'] = $this->default;
	}

}
