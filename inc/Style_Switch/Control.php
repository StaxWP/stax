<?php
/**
 * Stax\Style_Switch\Control class
 *
 * @package stax
 */

namespace Stax\Style_Switch;

/**
 * Customizer control that prints the "Stax style" panel (no setting of its own).
 */
class Control extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_style_switch';

	/**
	 * Component that renders the panel.
	 *
	 * @var Component
	 */
	public $renderer;

	/**
	 * Renders the panel.
	 */
	public function render_content() {
		$this->renderer->render_panel( 'customizer' );
	}
}
