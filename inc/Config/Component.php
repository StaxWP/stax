<?php
/**
 * Stax\Base_Support\Component class
 *
 * @package stax
 */

namespace Stax\Config;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use Stax\Customizer\Config;

use function Stax\stax;

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `stax()->get_option( 'option_name' )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Saved config options
	 *
	 * @var array
	 */
	private $config = [];

	/**
	 * Current customizer saved options
	 *
	 * @var array
	 */
	private $saved_options = null;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'config';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		$this->config = [
			'item_id'         => '24818607',
			'slug'            => 'stax',
			'purchase_link'   => '',
			'priority_addons' => [],
		];
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
			'get_option'               => [ $this, 'get_option' ],
			'set_config'               => [ $this, 'set_config' ],
			'get_config'               => [ $this, 'get_config' ],
			'get_video_from_content'   => [ $this, 'get_video_from_content' ],
			'get_gallery_from_content' => [ $this, 'get_gallery_from_content' ],
			'has_media'                => [ $this, 'has_media' ],
			'get_post_data'            => [ $this, 'get_post_data' ],
			'set_post_data'            => [ $this, 'set_post_data' ],
			'get_article_components'   => [ $this, 'get_article_components' ],
		];
	}

	/**
	 * Function to get options in front-end
	 *
	 * @param string|bool $option The option we need from the DB
	 * @param string|bool $default If $option doesn't exist in DB return $default value
	 * @param bool        $filters
	 *
	 * @return string|array
	 */
	public function get_option( $option = false, $default = false, $filters = true ) {
		if ( $option === false ) {
			return false;
		}

		if ( is_customize_preview() ) {
			if ( $filters === true ) {
				$value = get_theme_mod( $option, isset( Config::OPTIONS[ $option ] ) ? Config::OPTIONS[ $option ]['default'] : false );
				$value = $this->fetch_metabox_value( $value, $option );

				return apply_filters( 'stax_option', $value, $option );
			}

			$value = get_theme_mod( $option, $default );
			$value = $this->fetch_metabox_value( $value, $option );

			return $value;
		}

		if ( $this->saved_options === null ) {
			$this->saved_options = get_theme_mods();
		}

		if ( isset( $this->saved_options[ $option ] ) ) {
			$output_data = $this->saved_options[ $option ];
		} elseif ( $default === false ) {
			$output_data = isset( Config::OPTIONS[ $option ] ) ? Config::OPTIONS[ $option ]['default'] : false;
		} else {
			$output_data = $default;
		}

		$output_data = $this->fetch_metabox_value( $output_data, $option );

		if ( $filters === true ) {
			$output_data = apply_filters( 'stax_option', $output_data, $option );
		}

		return $output_data;
	}

	/**
	 * Check for metabox value for post and fetch it
	 *
	 * @param mixed  $value
	 * @param string $option
	 * @return mixed
	 */
	public function fetch_metabox_value( $value, $option ) {
		if ( get_post_type( get_the_ID() ) === 'page' ) {
			return $value;
		}

		if ( isset( Config::OPTIONS[ $option ]['metabox'] ) && Config::OPTIONS[ $option ]['metabox']['exists'] ) {
			$metabox_value = get_post_meta( get_the_ID(), $option, true );

			if ( $metabox_value ) {
				if ( 'inherit' === $metabox_value ) {
					return $value;
				}

				$passed_dependency = true;

				if ( isset( Config::OPTIONS[ $option ]['metabox']['dependency'] ) ) {
					$dependency_value = get_post_meta( get_the_ID(), Config::OPTIONS[ $option ]['metabox']['dependency']['option'], true );

					if ( ! in_array( $dependency_value, Config::OPTIONS[ $option ]['metabox']['dependency']['values'] ) ) {
						$passed_dependency = false;
					}
				}

				if ( $passed_dependency ) {
					if ( 'off' === $metabox_value ) {
						$metabox_value = false;
					}

					if ( 'on' === $metabox_value ) {
						$metabox_value = true;
					}

					$value = $metabox_value;
				}
			}
		}

		return $value;
	}

	/**
	 * @param $name
	 *
	 * @return bool|mixed
	 */
	public function get_config( $name ) {
		if ( isset( $this->config[ $name ] ) ) {
			return $this->config[ $name ];
		}

		return false;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function set_config( $name, $value ) {
		$this->config[ $name ] = $value;
	}

	/**
	 * Get first video from content
	 *
	 * @param int|null $post_id
	 * @return array
	 */
	public function get_video_from_content( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$data = [
			'source' => '',
			'video'  => '',
		];

		$content = get_the_content( null, false, $post_id );

		if ( has_block( 'video', $content ) || has_block( 'embed', $content ) ) {
			$post_blocks = parse_blocks( $content );

			foreach ( $post_blocks as $block ) {
				if ( $block['blockName'] === 'core/embed' ) {
					$data = [
						'source' => 'other',
						'video'  => $block['attrs']['url'],
					];
				} elseif ( $block['blockName'] === 'core/video' ) {
					$data = [
						'source' => 'hosted',
						'video'  => $block['attrs']['id'],
					];
				}

				if ( $data['video'] ) {
					break;
				}
			}
		}

		return $data;
	}

	/**
	 * Get frist gallery from content
	 *
	 * @param int|null $post_id
	 * @return array
	 */
	public function get_gallery_from_content( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$data = [];

		$content = get_the_content( null, false, $post_id );

		if ( has_block( 'gallery', $content ) ) {
			$post_blocks = parse_blocks( $content );

			foreach ( $post_blocks as $block ) {
				if ( $block ['blockName'] === 'core/gallery' ) {
					if ( empty( $block['innerBlocks'] ) && isset( $block['attrs']['ids'] ) ) {
						foreach ( $block['attrs']['ids'] as $id ) {
							$data[] = $id;
						}
					} else {
						foreach ( $block['innerBlocks'] as $inner_block ) {
							$data[] = $inner_block['attrs']['id'];
						}
					}
				}

				if ( ! empty( $data ) ) {
					break;
				}
			}
		}

		return $data;
	}

	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function has_media( $post_id = null ) {
		$format = get_post_format() ?: 'standard';

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		switch ( $format ) {
			case 'video':
				$data = $this->get_video_from_content( $post_id );

				return (bool) $data['video'];
				break;
			case 'gallery':
				$data = $this->get_gallery_from_content( $post_id );

				return ! empty( $data );
				break;
			default:
				return stax()->has_post_thumbnail();
		}

		return false;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function set_post_data( $name, $value ) {
		global $post;
		if ( ! isset( $post->stax ) ) {
			$post->stax = [];
		}

		$post->stax[ $name ] = $value;
	}

	/**
	 * @param $name
	 * @param bool $default
	 *
	 * @return bool
	 */
	public function get_post_data( $name, $default = false ) {
		global $post;

		if ( isset( $post->stax, $post->stax[ $name ] ) ) {
			return $post->stax[ $name ];
		}

		return $default;
	}

	/**
	 * @return array
	 */
	public function get_article_components() {
		return [
			'media',
			'categories',
			'title',
			'excerpt',
			'meta',
		];
	}

	/**
	 * @param $field
	 *
	 * @return bool|mixed
	 */
	public function post_overwrite_dependency( $field ) {
		$fields = [
			'media_fullscreen_individual'                  => 'media_panel_overwrite_individual',
			'media_content_color_individual'               => 'media_panel_overwrite_individual',
			'media_panel_fade_scroll_individual'           => 'media_panel_overwrite_individual',
			'media_image_width_individual'                 => 'media_image_overwrite_individual',
			'media_image_overlay_individual'               => 'media_image_overwrite_individual',
			'media_image_background_type_individual'       => 'media_image_overwrite_individual',
			'media_image_overlay_show_individual'          => 'media_image_overwrite_individual',
			'media_background_type_individual'             => 'media_image_overwrite_individual',
			'media_video_width_individual'                 => 'media_video_overwrite_individual',
			'media_video_overlay_individual'               => 'media_video_overwrite_individual',
			'media_video_overlay_show_individual'          => 'media_video_overwrite_individual',
			'media_gallery_width_individual'               => 'media_gallery_overwrite_individual',
			'media_gallery_item_width_individual'          => 'media_gallery_overwrite_individual',
			'media_gallery_overlay_individual'             => 'media_gallery_overwrite_individual',
			'media_gallery_overlay_show_individual'        => 'media_gallery_overwrite_individual',
			'media_catbread_type_individual'               => 'media_catbread_overwrite_individual',
			'media_catbread_animation_type_individual'     => 'media_catbread_overwrite_individual',
			'media_title_position_individual'              => 'media_title_overwrite_individual',
			'media_title_over_alignment_individual'        => 'media_title_overwrite_individual',
			'media_title_left_alignment_individual'        => 'media_title_overwrite_individual',
			'media_title_above_below_alignment_individual' => 'media_title_overwrite_individual',
			'media_title_size_individual'                  => 'media_title_overwrite_individual',
			'media_title_animation_type_individual'        => 'media_title_overwrite_individual',
			'media_meta_show_individual'                   => 'media_meta_overwrite_individual',
			'media_meta_style_individual'                  => 'media_meta_overwrite_individual',
			'media_meta_items_individual'                  => 'media_meta_overwrite_individual',
			'media_meta_animation_individual'              => 'media_meta_overwrite_individual',
			'fup_media_meta_action_individual'             => 'media_meta_overwrite_individual',
			'media_shapes_show_individual'                 => 'media_shapes_overwrite_individual',
			'blog_post_listing_thumb_position_individual'  => 'blog_post_default_listing_overwrite_individual',
			'blog_post_thumbnail_individual'               => 'blog_post_masonry_listing_overwrite_individual',
			'blog_post_thumbnail_list_big_individual'      => 'blog_post_big_listing_overwrite_individual',
			'blog_post_thumbnail_grid_individual'          => 'blog_post_grid_listing_overwrite_individual',
		];

		$fields = apply_filters( 'stax_post_options_overwrite', $fields );

		if ( isset( $fields[ $field ] ) ) {
			return $fields[ $field ];
		}

		return false;
	}

}
