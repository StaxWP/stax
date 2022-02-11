<?php
/**
 * Stax\Load_Icon\Component class
 *
 * @package stax
 */

namespace Stax\Load_Icon;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;

/**
 * Class Component
 *
 * @package Stax\Load_Icon
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'load_icon';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() {
		return [
			'load_icon' => [ $this, 'load_icon' ],
		];
	}

	/**
	 * @param $icon
	 * @param int  $size
	 *
	 * @return string
	 */
	public function load_icon( $icon, $size = 24 ) {
		return '<span class="svq-icon icon-' . $icon . ' icon--x' . $size . '"></span>';
	}
}
