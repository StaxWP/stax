<?php
/**
 * Stax\Lazyload\Component class
 *
 * Images and iframes use the browser's native lazy loading, which WordPress core adds.
 * The JavaScript lazy loader shipped until 1.2.2 was removed in 2.0. The Customizer
 * option "Lazy Load Images" (stax_lazy_load_images) still works: turning it off
 * turns off core lazy loading.
 *
 * @package stax
 */

namespace Stax\Lazyload;

use Stax\Component_Interface;
use Stax\Customizer\Config;
use function Stax\stax;

/**
 * Class for native lazy loading.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'lazyload';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'wp_lazy_loading_enabled', [ $this, 'filter_lazy_loading_enabled' ] );
	}

	/**
	 * Turns off core lazy loading when the Customizer option is off.
	 *
	 * @param bool $enabled Whether lazy loading is enabled.
	 *
	 * @return bool
	 */
	public function filter_lazy_loading_enabled( $enabled ) {
		if ( false === (bool) stax()->get_option( Config::OPTION_LAZY_LOAD_IMAGES ) ) {
			return false;
		}

		return $enabled;
	}
}
