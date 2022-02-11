<?php

namespace Stax\Customizer\Core\Controls\React;

class Builder extends \WP_Customize_Control {

	/**
	 * Type of this section.
	 *
	 * @var string
	 */
	public $type = 'stax_builder_control';

	/**
	 * Builder Type
	 *
	 * @var string
	 */
	public $builder_type = null;
	/**
	 * Columns Layout
	 *
	 * @var boolean
	 */
	public $columns_layout = false;

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$json                  = parent::json();
		$json['builderType']   = $this->builder_type;
		$json['columnsLayout'] = $this->columns_layout;

		return $json;
	}

}
