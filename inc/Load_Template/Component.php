<?php
/**
 * Stax\Load_Template\Component class
 *
 * @package stax
 */

namespace Stax\Load_Template;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;

/**
 * Class Component
 *
 * @package Stax\Load_Template
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'load_template';
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
			'get_template_part' => [ $this, 'get_template_part' ],
		];
	}

	/**
	 * Get template part and pass variables
	 *
	 * @param $slug
	 * @param null $name
	 * @param null $vars
	 *
	 * @return bool
	 */
	public function get_template_part( $slug, $name = null, $vars = null ) {
		/**
		 * Fires before the specified template part file is loaded.
		 *
		 * The dynamic portion of the hook name, `$slug`, refers to the slug name
		 * for the generic template part.
		 *
		 * @param string $slug The slug name for the generic template.
		 * @param string|null $name The name of the specialized template.
		 *
		 * @since 1.0.0
		 */
		do_action( "get_template_part_{$slug}", $slug, $name );

		$continue = apply_filters( "get_template_part_{$slug}", true );
		if ( ! $continue ) {
			return false;
		}

		if ( is_array( $vars ) ) {
			extract( $vars, EXTR_SKIP );
		}
		$templates = [];
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		/**
		 * Fires before a template part is loaded.
		 *
		 * @param string $slug The slug name for the generic template.
		 * @param string $name The name of the specialized template.
		 * @param string[] $templates Array of template files to search for, in order.
		 *
		 * @since 5.2.0
		 */
		do_action( 'get_template_part', $slug, $name, $templates );

		$template_path = locate_template( $templates );

		if ( $template_path != '' ) {
			include $template_path;
		}
	}
}
