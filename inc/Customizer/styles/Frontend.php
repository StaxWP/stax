<?php

namespace Stax\Customizer;

class Frontend extends Generator {
	use Css_Vars;

	/**
	 * Frontend constructor.
	 */
	public function __construct() {
		$this->_subscribers = [];

		$this->setup_typography();
	}

	/**
	 * Setup typography subscribers.
	 */
	public function setup_typography() {
		$rules                = $this->get_typography_rules();
		$this->_subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => ':root',
			Dynamic_Selector::KEY_RULES    => $rules,
		];
	}
}
