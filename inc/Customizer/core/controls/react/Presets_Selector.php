<?php

namespace Stax\Customizer\Core\Controls\React;

class Presets_Selector extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_presets_selector';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $presets = [];

	/**
	 * Builder Setting Slug.
	 *
	 * @var string | null
	 */
	public $builder = null;

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['presets'] = $this->presets;
		$this->json['builder'] = $this->builder;
	}

}
