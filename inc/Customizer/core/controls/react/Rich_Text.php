<?php

namespace Stax\Customizer\Core\Controls\React;

class Rich_Text extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_rich_text';

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['toolbars'] = [];

		if ( isset( $this->input_attrs['toolbars'] ) && ! empty( $this->input_attrs['toolbars'] ) ) {
			$this->json['toolbars'] = $this->input_attrs['toolbars'];
		}
	}

}
