<?php
/**
 * Stax\Customizer\Core\Controls\React\Script_Control class
 *
 * @package stax
 */

namespace Stax\Customizer\Core\Controls\React;

/**
 * Control registered by type name only (for example "stax_background_control") and drawn by its script.
 */
class Script_Control extends \WP_Customize_Control {

	use Renders_In_Script;

	/**
	 * A control for these arguments: drawn by its script for "stax_*" types, a core control otherwise.
	 *
	 * @param \WP_Customize_Manager $manager Customizer.
	 * @param string                $id      Control ID.
	 * @param array                 $args    Control arguments.
	 *
	 * @return \WP_Customize_Control
	 */
	public static function create( $manager, $id, $args ) {
		$type = isset( $args['type'] ) ? (string) $args['type'] : '';

		return 0 === strpos( $type, 'stax_' ) ? new self( $manager, $id, $args ) : new \WP_Customize_Control( $manager, $id, $args );
	}
}
