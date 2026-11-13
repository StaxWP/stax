<?php
/**
 * Stax\Customizer\Core\Controls\React\Renders_In_Script trait
 *
 * @package stax
 */

namespace Stax\Customizer\Core\Controls\React;

/**
 * For controls drawn by their React component (renderContent in controls.js): no PHP markup.
 * The default markup was an input printing the value, which raised "Array to string conversion"
 * for controls with array values and was replaced by the script anyway.
 */
trait Renders_In_Script {

	/**
	 * The script renders the control.
	 */
	protected function render_content() {}
}
