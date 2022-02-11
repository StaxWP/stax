<?php
/**
 * Stax\Breadcrumb\Component class
 *
 * @package stax
 */

namespace Stax\Breadcrumb;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;

/**
 * Class Component
 *
 * @package Stax\Breadcrumb
 */
class Component implements Component_Interface, Templating_Component_Interface {


	private $cached_data = false;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'breadcrumb';
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
			'get_breadcrumb' => [ $this, 'get_breadcrumb' ],
		];
	}

	/**
	 * Get breadcrumbs of current location
	 *
	 * @param array $args
	 *
	 * @return bool|mixed|null|string|void
	 */
	public function get_breadcrumb( $args = [] ) {

		if ( $this->cached_data ) {
			return $this->cached_data;
		}

		$breadcrumb_filter = false;

		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$yoast_breadcrumb = yoast_breadcrumb( '<div class="svq-breadcrumb breadcrumb svq-yoast">', '</div>', 'false' );

			if ( $yoast_breadcrumb ) {
				$breadcrumb_filter = $yoast_breadcrumb;
			}
		}

		$breadcrumb_filter = apply_filters( 'stax_breadcrumb_data', $breadcrumb_filter );
		if ( $breadcrumb_filter ) {
			return $breadcrumb_filter;
		}

		if ( function_exists( 'bp_is_active' ) && ! bp_is_blog_page() ) {
			$breadcrumb = new Buddypress_Trail( $args );
		} elseif ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
			$breadcrumb = new Bbpress_Trail( $args );
		} else {
			$breadcrumb = new Trail( $args );
		}
		$this->cached_data = $breadcrumb->trail();

		return $breadcrumb->trail();
	}

}
