<?php
/**
 * Stax\Date\Component class
 *
 * @package stax
 */

namespace Stax\Date;

use Stax\Customizer\Config;
use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function get_permalink;
use function get_the_date;
use function get_the_modified_date;
use function Stax\stax;

/**
 * Class for date handling.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'the_date';
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
			'get_the_date' => [ $this, 'get_the_date' ],
		];
	}

	/**
	 * @return string
	 */
	public function get_the_date() {
		$hidden_classes       = ' hide hidden d-none';
		$published_date_class = $modified_date_class = '';

		if ( stax()->get_option( Config::OPTION_MISC_POST_UPDATED_DATE ) ) {
			$published_date_class = $hidden_classes;
		} else {
			$modified_date_class = $hidden_classes;
		}

		$date = sprintf(
			'<a href="%1$s" rel="bookmark" class="posted-on__updated">' .
						 '<time class="entry-date published%6$s" datetime="%2$s">%3$s</time>' .
						 '<time class="modify-date updated%7$s" datetime="%4$s">%5$s</time>' .
						 '</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_html( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() ),
			$published_date_class,
			$modified_date_class
		);

		return $date;
	}
}
