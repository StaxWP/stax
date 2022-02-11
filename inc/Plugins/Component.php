<?php
/**
 * Stax\Plugins\Component class
 *
 * @package stax
 */

namespace Stax\Plugins;

use Stax\Component_Interface;

/**
 * Class Component
 *
 * @package Stax\Plugins
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'plugins';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

		if ( class_exists( \WooCommerce::class ) ) {
			new Woo();
		}
	}

}
