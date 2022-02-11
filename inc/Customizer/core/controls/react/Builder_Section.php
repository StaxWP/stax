<?php

namespace Stax\Customizer\Core\Controls\React;

class Builder_Section extends \WP_Customize_Section {

	/**
	 * Type of this section.
	 *
	 * @var string
	 */
	public $type = 'stax_header_footer_builder_section';

	/**
	 * Default options schema.
	 *
	 * @var array
	 */
	public $default_options = [
		'setting'      => '',
		'builder_type' => '',
	];

	/**
	 * Options passed to section.
	 *
	 * @var array
	 */
	public $options = [];


	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$json            = parent::json();
		$json['options'] = wp_parse_args( $this->options, $this->default_options );

		return $json;
	}

}
