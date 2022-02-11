<?php
/**
 * Stax\Lazyload\Component class
 *
 * @package stax
 */

namespace Stax\Lazyload;

use Stax\Component_Interface;
use Stax\Customizer\Config;
use function Stax\stax;
use function add_action;
use function add_filter;
use function is_admin;
use function get_theme_mod;
use function apply_filters;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function is_feed;
use function is_preview;
use function wp_kses_hair;

/**
 * Class for managing lazy-loading images.
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
		add_action( 'init', [ $this, 'action_lazyload_images' ] );
	}

	/**
	 * Initializes lazy-loading images functionality.
	 */
	public function action_lazyload_images() {
		// If this is the admin page, return early.
		if ( is_admin() ) {
			return;
		}

		// If lazy-load is disabled in Customizer, return early.
		if ( false === stax()->get_option( Config::OPTION_LAZY_LOAD_IMAGES ) ) {
			return;
		}

		// If the Jetpack Lazy-Images module is active, return early.
		if ( ! apply_filters( 'lazyload_is_enabled', true ) ) {
			return;
		}

		// If the AMP plugin is active, return early.
		if ( stax()->is_amp() ) {
			return;
		}

		add_action( 'wp_loaded', [ $this, 'action_add_lazyload_filters' ], PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_lazyload_assets' ] );

		add_filter( 'wp_kses_allowed_html', [ $this, 'filter_allow_lazyload_attributes' ] );
	}

	/**
	 * Adds filters to enable lazy-loading of images.
	 */
	public function action_add_lazyload_filters() {
		add_filter( 'the_content', [ $this, 'filter_add_lazyload_placeholders_content' ], 20 );
		add_filter( 'post_thumbnail_html', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'get_avatar', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'widget_text', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'get_image_tag', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_lazyload_attributes' ], PHP_INT_MAX, 3 );
	}

	/**
	 * Enqueues and defer lazy-loading JavaScript.
	 */
	public function action_enqueue_lazyload_assets() {
		wp_enqueue_script(
			'lazy-load-images',
			get_theme_file_uri( '/assets/js/lazyload.min.js' ),
			[],
			stax()->get_asset_version( get_theme_file_path( '/assets/js/lazyload.min.js' ) ),
			true
		);

		// Fix .closest() for IE
		$data = 'if (window.Element && !Element.prototype.closest) {
				    Element.prototype.closest =
				    function(s) {
				        var matches = (this.document || this.ownerDocument).querySelectorAll(s),
				            i,
				            el = this;
				        do {
				            i = matches.length;
				            while (--i >= 0 && matches.item(i) !== el) {};
				        } while ((i < 0) && (el = el.parentElement));
				        return el;
				    };
				} ';

		$data .= 'function staxTriggerLazy() {' .
					'let loadImage = new LazyLoad({' .
						'elements_selector: ".lazy",' .
						'class_loading: "lazy-is-loading",' .
						'class_loaded: "lazy-is-loaded",' .
						'callback_enter: function(el) {' .
							'el.setAttribute("style", "");' .
							'var parentEl = el.closest(".placeholder-el");' .
							'if(parentEl !== null) { el.closest(".placeholder-el").classList.add("el-loaded"); }' .
						'}' .
					'});' .
				'}' .
				'window.addEventListener("DOMContentLoaded", staxTriggerLazy);' .
				'window.addEventListener("loadLazy", staxTriggerLazy);';

		wp_add_inline_script( 'lazy-load-images', $data );
		wp_script_add_data( 'lazy-load-images', 'defer', true );
	}

	/**
	 * Ensures that lazy-loading image attributes are not filtered out of image tags.
	 *
	 * @param array $allowed_tags The allowed tags and their attributes.
	 *
	 * @return array Filtered allowed tags.
	 */
	public function filter_allow_lazyload_attributes( array $allowed_tags ) {
		if ( ! isset( $allowed_tags['img'] ) ) {
			return $allowed_tags;
		}

		// But, if images are allowed, ensure that our attributes are allowed!
		$allowed_tags['img'] = array_merge(
			$allowed_tags['img'],
			[
				'data-src'    => 1,
				'data-srcset' => 1,
				'data-sizes'  => 1,
				'data-width'  => 1,
				'data-height' => 1,
				'data-id'     => 1,
				'class'       => 1,
				'style'       => 1,
			]
		);

		$allowed_tags['noscript'] = [];

		return $allowed_tags;
	}

	/**
	 * Finds image elements that should be lazy-loaded.
	 *
	 * @param string $content The content.
	 *
	 * @return string Filtered content.
	 */
	public function filter_add_lazyload_placeholders_content( $content ) {
		// Don't lazyload for feeds, previews.
		if ( is_feed() || is_preview() ) {
			return $content;
		}

		// Find all <img> elements via regex, add lazy-load attributes.
		$content = preg_replace_callback(
			'#<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si',
			function ( array $matches ) {

				$old_attributes_str       = $matches[2];
				$old_attributes_kses_hair = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );
				if ( empty( $old_attributes_kses_hair['src'] ) ) {
					return $matches[0];
				}

				$old_attributes = $this->flatten_kses_hair_data( $old_attributes_kses_hair );

				if ( isset( $old_attributes['class'] ) && strpos( $old_attributes['class'], 'lazy' ) !== false ) {
					return $matches[0];
				}

				$new_attributes = $this->filter_lazyload_attributes( $old_attributes );

				$before_content = '';
				$after_content  = '';
				$aspect_ratio   = '';

				if ( isset( $new_attributes['width'] ) && isset( $new_attributes['height'] ) ) {
					$width  = $new_attributes['width'];
					$height = $new_attributes['height'];

				} elseif ( isset( $new_attributes['data-width'] ) && isset( $new_attributes['data-height'] ) ) {
					$width  = $new_attributes['data-width'];
					$height = $new_attributes['data-height'];
				}

				if ( isset( $width, $height ) && is_numeric( $width ) && is_numeric( $height ) && $width > 0 && $height > 0 ) {
					$aspect_ratio            = $height / $width * 100;
					$aspect_ratio            = number_format( $aspect_ratio, 2 );
					$new_attributes['style'] = 'height: 0; width: ' . $width . 'px; padding-bottom: ' . $aspect_ratio . '%;';
				}

				if ( $aspect_ratio ) {
					$posible_aligns = [
						'alignright'  => 'right',
						'alignleft'   => 'left',
						'aligncenter' => 'center',
						'alignnone'   => 'none',
					];

					$placeholder_extra_classes = '';

					if ( isset( $old_attributes['class'] ) && $old_attributes['class'] ) {
						foreach ( $posible_aligns as $default => $align ) {
							if ( strpos( $old_attributes['class'], $default ) !== false ) {
								$placeholder_extra_classes = $align;
							}
						}
					}

					$before_content = '<span class="placeholder-el" data-svq-align="' . esc_attr( $placeholder_extra_classes ) . '">';
					$after_content  = '<span class="svq-img-loader"></span></span>';
				}

				// If we didn't add lazy attributes, just return the original image source.
				if ( empty( $new_attributes['data-src'] ) ) {
					return $matches[0];
				}

				$new_attributes_str = $this->build_attributes_string( $new_attributes );

				return sprintf( $before_content . '<img %1$s>' . $after_content . '<noscript>%2$s</noscript>', $new_attributes_str, $matches[0] );
			},
			$content
		);

		return $content;
	}

	/**
	 * Finds image elements that should be lazy-loaded.
	 *
	 * @param string $content The content.
	 *
	 * @return string Filtered content.
	 */
	public function filter_add_lazyload_placeholders( $content ) {
		// Don't lazyload for feeds, previews.
		if ( is_feed() || is_preview() ) {
			return $content;
		}

		// Don't lazy-load if the content has already been run through previously.
		if ( false !== strpos( $content, 'data-src' ) ) {
			return $content;
		}

		// Find all <img> elements via regex, add lazy-load attributes.
		$content = preg_replace_callback(
			'#<(img)([^>]+?)(>(.*?)<\/\\1>|[\/]?>)#si',
			function ( array $matches ) {
				$old_attributes_str       = $matches[2];
				$old_attributes_kses_hair = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );
				if ( empty( $old_attributes_kses_hair['src'] ) ) {
					return $matches[0];
				}

				$old_attributes = $this->flatten_kses_hair_data( $old_attributes_kses_hair );
				$new_attributes = $this->filter_lazyload_attributes( $old_attributes );

				// If we didn't add lazy attributes, just return the original image source.
				if ( empty( $new_attributes['data-src'] ) ) {
					return $matches[0];
				}

				$new_attributes_str = $this->build_attributes_string( $new_attributes );

				return sprintf( '<img %1$s><noscript>%2$s</noscript>', $new_attributes_str, $matches[0] );
			},
			$content
		);

		return $content;
	}

	/**
	 * @param array  $attributes
	 * @param null   $attachment
	 * @param string $size
	 *
	 * @return array
	 */
	public function filter_lazyload_attributes( array $attributes, $attachment = null, $size = null ) {
		if ( empty( $attributes['src'] ) ) {
			return $attributes;
		}

		if ( ! isset( $size ) ) {
			$size = 'full';
		}

		$temp          = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attributes['src'] );
		$attachment_id = attachment_url_to_postid( $temp );

		if ( ! $attachment_id ) {
			if ( array_key_exists( 'class', $attributes ) ) {
				$attributes['class'] .= ' skip-lazy';
			} else {
				$attributes['class'] = 'skip-lazy';
			}
		}

		if ( ! empty( $attributes['class'] ) && $this->should_skip_image_with_blacklisted_class( $attributes['class'] ) ) {
			return $attributes;
		}

		if ( isset( $attributes['data-width'], $attributes['data-height'] ) ) {
			return $attributes;
		}

		$old_attributes = $attributes;

		// Add the lazy class to the img element.
		$attributes['class'] = $this->lazyload_class( $attributes );

		// Process `srcset` attribute.
		if ( ! empty( $attributes['srcset'] ) ) {
			$attributes['data-srcset'] = $old_attributes['srcset'];
			unset( $attributes['srcset'] );
		}

		// Process `sizes` attribute.
		if ( ! empty( $attributes['sizes'] ) ) {
			$attributes['data-sizes'] = $old_attributes['sizes'];
			unset( $attributes['sizes'] );
		}

		if ( $attachment_id ) {
			if ( ! isset( $old_attributes['width'], $old_attributes['height'] ) ) {
				$existing_image = wp_get_attachment_image_src( $attachment_id, $size );
				if ( isset( $existing_image[1], $existing_image[2] ) ) {
					$attributes['data-width']  = $existing_image[1];
					$attributes['data-height'] = $existing_image[2];
				}
			} else {
				$attributes['data-width']  = $old_attributes['width'];
				$attributes['data-height'] = $old_attributes['height'];
			}
		}

		// Set placeholder and lazy-src.
		$attributes['src'] = $this->lazyload_get_placeholder_image( $attachment_id, $attributes );

		// Set data-src to the original source uri.
		$attributes['data-src'] = $old_attributes['src'];

		return $attributes;
	}

	/**
	 * Returns true when a given string of classes contains a class signifying image
	 * should not be lazy-loaded
	 *
	 * @param string $classes
	 *
	 * @return bool Whether the classes contain a class indicating that lazyloading should be skipped.
	 */
	protected function should_skip_image_with_blacklisted_class( $classes ) {
		$blacklisted_classes = [
			'skip-lazy',
			'custom-logo',
		];

		foreach ( $blacklisted_classes as $class ) {
			if ( false !== strpos( $classes, $class ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Appends a 'lazy' class to <img> elements for lazy-loading.
	 *
	 * @param array $attributes <img> element attributes.
	 *
	 * @return string Classes string including a 'lazy' class.
	 */
	protected function lazyload_class( array $attributes ) {
		if ( array_key_exists( 'class', $attributes ) ) {
			$classes  = $attributes['class'];
			$classes .= ' lazy';
		} else {
			$classes = 'lazy';
		}

		return $classes;
	}

	/**
	 * Gets the placeholder image URL.
	 *
	 * @param $attachment_id
	 * @param $attributes
	 *
	 * @return string The URL to the placeholder image.
	 */
	protected function lazyload_get_placeholder_image( $attachment_id = null, $attributes = [] ) {

		if ( isset( $attributes['data-width'], $attributes['data-height'] ) && $attributes['data-width'] !== $attributes['data-height'] ) {
			if ( $img = wp_get_attachment_image_src( $attachment_id, 'stax-lazy' ) ) {
				if ( $img[1] < $attributes['data-width'] ) {
					return $img[0];
				}
			}
		}

		return get_theme_file_uri( '/assets/img/placeholder.png' );
	}

	/**
	 * Flattens an attribute list into key value pairs.
	 *
	 * @param array $attributes Array of attributes.
	 *
	 * @return array Flattened attributes as $attr => $attr_value pairs.
	 */
	protected function flatten_kses_hair_data( array $attributes ) {
		$flattened_attributes = [];
		foreach ( $attributes as $name => $attribute ) {
			$flattened_attributes[ $name ] = $attribute['value'];
		}

		return $flattened_attributes;
	}

	/**
	 * Builds a string of attributes for an HTML element.
	 *
	 * @param array $attributes Array of attributes.
	 *
	 * @return string HTML attribute string.
	 */
	protected function build_attributes_string( array $attributes ) {
		$string = [];
		foreach ( $attributes as $name => $value ) {
			if ( '' === $value ) {
				$string[] = sprintf( '%s', $name );
			} else {
				$string[] = sprintf( '%s="%s"', $name, esc_attr( $value ) );
			}
		}

		return implode( ' ', $string );
	}

}
