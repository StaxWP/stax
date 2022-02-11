<?php
/**
 * Stax\Custom_Logo\Component class
 *
 * @package stax
 */

namespace Stax\Custom_Logo;

use WP_Customize_Cropped_Image_Control;
use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function add_action;
use function add_theme_support;
use function apply_filters;

/**
 * Class for adding custom logo support.
 *
 * @link https://codex.wordpress.org/Theme_Logo
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'custom_logo';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_add_custom_logo_support' ] );
		add_filter( 'get_custom_logo', [ $this, 'get_header_logo' ] );
	}

	/**
	 * Adds support for the Custom Logo feature.
	 */
	public function action_add_custom_logo_support() {
		$args = apply_filters(
			'stax_custom_logo_args',
			array(
				'height'      => 80,
				'width'       => 200,
				'flex-height' => false,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);
		
		add_theme_support( 'custom-logo', $args );
	}

	/**
	 * @param $html
	 *
	 * @return string
	 */
	public function get_header_logo( $html ) {
		$large_logo_id = get_theme_mod( 'custom_logo' );

		if ( ! $large_logo_id ) {
			return '';
		}

		$logo_dark    = '';
		$anchor_class = 'custom-logo-link';
		$logo_class   = 'custom-logo svq-logo-default';

		$logo = wp_get_attachment_image(
			$large_logo_id,
			'full',
			false,
			[
				'class' => $logo_class,
				'alt'   => get_bloginfo( 'name', 'display' ),
			]
		);

		if ( $logo_dark !== '' ) {
			$anchor_class .= ' has-logo-dark';
		}

		return sprintf(
			'<a href="%1$s" class="' . esc_attr( $anchor_class ) . '" rel="home" itemprop="url">%2$s</a>',
			esc_url( home_url( '/' ) ),
			$logo
		);
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
			'get_logo' => [ $this, 'get_logo' ],
			'the_logo' => [ $this, 'the_logo' ],
		];
	}

	/**
	 * Outputs the logo
	 */
	public function the_logo() {
		echo wp_kses_post( $this->get_logo() );
	}

	/**
	 * Returns the logo
	 *
	 * @return string
	 */
	public function get_logo() {
		$output = '';
		$logo   = get_custom_logo( get_current_blog_id() );

		if ( function_exists( 'the_custom_logo' ) && $logo !== '' ) {
			$output .= $logo;
		} else {
			$output .= '<a href="' . home_url() . '"><span class="site-title">' . get_bloginfo() . '</span></a>';
		}

		return $output;
	}
}
