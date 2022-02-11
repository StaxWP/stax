<?php
/**
 * Stax\Post_Thumbnails\Component class
 *
 * @package stax
 */

namespace Stax\Post_Thumbnails;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function add_action;
use function add_theme_support;

/**
 * Class for managing post thumbnail support.
 *
 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'post_thumbnails';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_add_post_thumbnail_support' ] );
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
			'has_post_thumbnail' => [ $this, 'has_post_thumbnail' ],
		];
	}

	/**
	 * Adds support for post thumbnails.
	 */
	public function action_add_post_thumbnail_support() {
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Proper check for post thumbnail
	 *
	 * @param null $post
	 *
	 * @return bool
	 */
	public function has_post_thumbnail( $post = null ) {
		$thumbnail_id = get_post_thumbnail_id( $post );

		if ( $thumbnail_id ) {
			$thumbnail_id = (int) $thumbnail_id;
			if ( $post = get_post( $thumbnail_id ) ) {
				return true;
			}
		}

		return false;
	}
}
