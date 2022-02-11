<?php
/**
 * Stax\Image_Sizes\Component class
 *
 * @package stax
 */

namespace Stax\Image_Sizes;

use Stax\Customizer\Config;
use Stax\Component_Interface;
use function Stax\stax;
use WP_Post;
use function add_filter;

/**
 * Class for managing responsive image sizes.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'image_sizes';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'wp_calculate_image_sizes', [ $this, 'filter_content_image_sizes_attr' ], 10, 2 );
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_post_thumbnail_sizes_attr' ], 10, 3 );

		add_action( 'after_setup_theme', [ $this, 'register_image_sizes' ] );
		add_filter( 'image_size_names_choose', [ $this, 'custom_image_sizes' ] );
	}

	/**
	 * Adds custom image sizes attribute to enhance responsive image functionality for content images.
	 *
	 * @param string $sizes A source size value for use in a 'sizes' attribute.
	 * @param array  $size Image size. Accepts an array of width and height
	 *                       values in pixels (in that order).
	 *
	 * @return string A source size value for use in a content image 'sizes' attribute.
	 */
	public function filter_content_image_sizes_attr( $sizes, array $size ) {
		if ( stax()->is_primary_sidebar_active() ) {
			$sizes = '(min-width: 960px) 75vw, 100vw';
		}

		return $sizes;
	}

	/**
	 * Adds custom image sizes attribute to enhance responsive image functionality for post thumbnails.
	 *
	 * @param array        $attr Attributes for the image markup.
	 * @param WP_Post      $attachment Attachment post object.
	 * @param string|array $size Registered image size or flat array of height and width dimensions.
	 *
	 * @return array The filtered attributes for the image markup.
	 */
	public function filter_post_thumbnail_sizes_attr( array $attr, WP_Post $attachment, $size ) {
		$listing     = stax()->get_post_data( 'listing_type' );
		$media_width = stax()->get_post_data( 'media_width' );
		$media_size  = stax()->get_post_data( 'media_size' );

		$layout_width    = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_CONTAINER );
		$small_item_cols = [
			'mobile'  => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE ),
			'tablet'  => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET ),
			'desktop' => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP ),
		];
		$wide_item_cols  = [
			'mobile'  => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE ),
			'tablet'  => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET ),
			'desktop' => stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP ),
		];

		if ( stax()->is_primary_sidebar_active() || stax()->is_secondary_sidebar_active() ) {
			$layout_width = 'small';
		}

		$sizes = '';

		switch ( $listing ) {
			case 'list':
				if ( $media_size === 'auto' || $media_width === 'wide' ) {
					$sizes = $this->add_img_query( 350, 400, $sizes );
					$sizes = $this->add_img_query( 700, 768, $sizes );

					if ( $layout_width === 'small' ) {
						$sizes = $this->add_img_query( 240, null, $sizes );
					} else {
						$sizes = $this->add_img_query( 240, 992, $sizes );
						$sizes = $this->add_img_query( 350, null, $sizes );
					}
				} elseif ( $media_width === 'normal' ) {
					$sizes = $this->add_img_query( 100, 400, $sizes );
					$sizes = $this->add_img_query( 150, 768, $sizes );

					if ( $layout_width === 'small' ) {
						$sizes = $this->add_img_query( 240, null, $sizes );
					} else {
						$sizes = $this->add_img_query( 240, 992, $sizes );
						$sizes = $this->add_img_query( 350, null, $sizes );
					}
				}
				break;
			case 'list-big':
				if ( $media_size === 'auto' ) {
					$sizes = $this->add_img_query( 240, 400, $sizes );
					$sizes = $this->add_img_query( 350, 992, $sizes );
				} else {
					$sizes = $this->add_img_query( 350, 400, $sizes );
				}

				if ( ( $layout_width === 'small' ) && $media_size === 'normal' ) {
					$sizes = $this->add_img_query( 700, null, $sizes );
				} else {
					$sizes = $this->add_img_query( 1100, null, $sizes );
				}
				break;
			case 'list-widget':
				$sizes = $this->add_img_query( 100, 400, $sizes );
				$sizes = $this->add_img_query( 150, 768, $sizes );
				$sizes = $this->add_img_query( 240, 992, $sizes );
				$sizes = $this->add_img_query( 100, 1200, $sizes );
				$sizes = $this->add_img_query( 120, null, $sizes );
				break;
			case 'list-featured':
				if ( $media_size === 'auto' ) {
					$sizes = $this->add_img_query( 240, 450, $sizes );
					$sizes = $this->add_img_query( 350, 576, $sizes );
					$sizes = $this->add_img_query( 240, null, $sizes );
				} else {
					$sizes = $this->add_img_query( 350, null, $sizes );
				}
				break;
			case 'grid':
				if ( $layout_width === 'small' ) {
					$sizes = $this->add_img_query( 350, 768, $sizes );
					$sizes = $this->add_img_query( 700, null, $sizes );
				} else {
					$wide_cols = [
						'mobile'  => stax()->get_options( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE ),
						'tablet'  => stax()->get_options( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET ),
						'desktop' => stax()->get_options( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP ),
					];
					$sizes     = $this->add_img_query( 350, 450, $sizes );

					if ( $wide_cols['tablet'] === '3' ) {
						$sizes = $this->add_img_query( 350, 992, $sizes );
					} elseif ( $wide_cols['tablet'] === '12' ) {
						$sizes = $this->add_img_query( 700, 768, $sizes );
					}

					if ( $wide_cols['desktop'] === '3' ) {
						$sizes = $this->add_img_query( 700, null, $sizes );
					} elseif ( $wide_cols['desktop'] === '12' ) {
						$sizes = $this->add_img_query( 1100, null, $sizes );
					}
				}
				break;
			case 'masonry':
				if ( $layout_width === 'small' ) {
					if ( $media_width === 'normal' ) {
						$sizes = $this->add_img_query( 100, 450, $sizes );
						$sizes = $this->add_img_query( 150, 768, $sizes );

						if ( $small_item_cols['tablet'] === '6' ) {
							$sizes = $this->add_img_query( 100, 992, $sizes );
						} elseif ( $small_item_cols['tablet'] === '12' ) {
							$sizes = $this->add_img_query( 240, 992, $sizes );
						}

						if ( $small_item_cols['desktop'] === '6' ) {
							$sizes = $this->add_img_query( 100, null, $sizes );
						} elseif ( $small_item_cols['desktop'] === '12' ) {
							$sizes = $this->add_img_query( 240, null, $sizes );
						}
					} elseif ( $media_width === 'wide' ) {
						$sizes = $this->add_img_query( 240, 360, $sizes );
						$sizes = $this->add_img_query( 350, 480, $sizes );
						$sizes = $this->add_img_query( 700, 768, $sizes );

						if ( $small_item_cols['tablet'] === '6' ) {
							$sizes = $this->add_img_query( 350, 992, $sizes );
						} elseif ( $small_item_cols['tablet'] === '12' ) {
							$sizes = $this->add_img_query( 700, 992, $sizes );
						}

						if ( $small_item_cols['desktop'] === '6' ) {
							$sizes = $this->add_img_query( 350, null, $sizes );
						} elseif ( $small_item_cols['desktop'] === '12' ) {
							$sizes = $this->add_img_query( 700, null, $sizes );
						}
					}
				} elseif ( $media_width === 'normal' ) {
					$sizes = $this->add_img_query( 100, 450, $sizes );
					$sizes = $this->add_img_query( 150, 768, $sizes );

					if ( $wide_item_cols['tablet'] === '6' ) {
						$sizes = $this->add_img_query( 100, 992, $sizes );
					} elseif ( $wide_item_cols['tablet'] === '12' ) {
						$sizes = $this->add_img_query( 240, 992, $sizes );
					}

					if ( $wide_item_cols['desktop'] === '6' ) {
						$sizes = $this->add_img_query( 150, null, $sizes );
					} elseif ( $wide_item_cols['desktop'] === '12' ) {
						$sizes = $this->add_img_query( 240, 1200, $sizes );
						$sizes = $this->add_img_query( 350, null, $sizes );
					} else {
						$sizes = $this->add_img_query( 100, null, $sizes );
					}
				} elseif ( $media_width === 'wide' ) {
					$sizes = $this->add_img_query( 100, 450, $sizes );
					$sizes = $this->add_img_query( 150, 768, $sizes );

					if ( $wide_item_cols['tablet'] === '12' ) {
						$sizes = $this->add_img_query( 240, 992, $sizes );
					} else {
						$sizes = $this->add_img_query( 100, 992, $sizes );
					}

					if ( $wide_item_cols['desktop'] === '4' ) {
						$sizes = $this->add_img_query( 350, null, $sizes );
					} elseif ( $wide_item_cols['desktop'] === '3' ) {
						$sizes = $this->add_img_query( 240, null, $sizes );
					} else {
						$sizes = $this->add_img_query( 700, null, $sizes );
					}
				}
				break;
			default:
		}

		$attr['sizes'] = $sizes ?: '100vw';

		return $attr;
	}

	/**
	 * Image media queries helper
	 *
	 * @param $size
	 * @param null   $resolution
	 * @param string $query
	 * @param bool   $max
	 *
	 * @return string
	 */
	private function add_img_query( $size, $resolution = null, $query = '', $max = true ) {
		if ( $query ) {
			$query .= ', ';
		}

		if ( $resolution ) {
			$query .= '(' . ( $max ? 'max' : 'min' ) . '-width: ' . $resolution . 'px) ' . $size . 'px';
		} else {
			$query .= $size . 'px';
		}

		return $query;
	}

	/**
	 * Registers image sizes
	 */
	public function register_image_sizes() {
		add_image_size(
			'stax-img-wide-xxl',
			1100,
			620,
			true
		);

		add_image_size(
			'stax-img-wide-xl',
			700,
			400,
			true
		);

		add_image_size(
			'stax-img-xxl',
			700,
			700,
			true
		);

		add_image_size(
			'stax-img-xl',
			350,
			560,
			true
		);

		add_image_size(
			'stax-img-lg',
			350,
			350,
			true
		);

		add_image_size(
			'stax-img-md',
			240,
			240,
			true
		);

		add_image_size(
			'stax-img-sm',
			120,
			120,
			true
		);

		add_image_size(
			'stax-img-xs',
			100,
			100,
			true
		);

		add_image_size(
			'stax-lazy',
			45,
			9999
		);
	}

	/**
	 * Add registered image sizes to size choices
	 *
	 * @param $sizes
	 *
	 * @return array
	 */
	public function custom_image_sizes( $sizes ) {
		return array_merge(
			$sizes,
			[
				'stax-img-wide-xxl' => esc_html__( 'Stax wide mega large image', 'stax' ),
				'stax-img-wide-xl'  => esc_html__( 'Stax wide large image', 'stax' ),
				'stax-img-xxl'      => esc_html__( 'Stax mega large image', 'stax' ),
				'stax-img-xl'       => esc_html__( 'Stax extra large image', 'stax' ),
				'stax-img-lg'       => esc_html__( 'Stax large image', 'stax' ),
				'stax-img-md'       => esc_html__( 'Stax medium image', 'stax' ),
				'stax-img-sm'       => esc_html__( 'Stax small image', 'stax' ),
				'stax-img-xs'       => esc_html__( 'Stax extra small image', 'stax' ),
				'stax-lazy'         => esc_html__( 'Stax for lazy load', 'stax' ),
			]
		);
	}

	// Useful helper function from codex example: http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
	public function get_image_sizes( $size = '' ) {
		global $_wp_additional_image_sizes;
		$sizes                        = [];
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info
		foreach ( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ] ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = [
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				];
			}
		}

		// Get only 1 size if found
		if ( $size ) {
			return isset( $sizes[ $size ] ) ? $sizes[ $size ] : false;
		}

		return $sizes;
	}

}
