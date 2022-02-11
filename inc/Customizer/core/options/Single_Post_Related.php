<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Single_Post_Related extends Base_Customizer {

	private $section_id = 'stax_single_post_related';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_related_posts();
		$this->controls_related_posts();
	}

	/**
	 * Add customize section
	 */
	private function section_related_posts() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => 300,
					'title'    => esc_html__( 'Related Posts', 'stax' ),
					'panel'    => 'stax_single_post',
				]
			)
		);
	}

	/**
	 * Add single post related posts controls.
	 */
	private function controls_related_posts() {
		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_RELATED_POSTS,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS ]['default'],
				],
				[
					'label'    => esc_html__( 'Show Related Posts', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 301,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_RELATED_POSTS_TYPE,
				[
					'sanitize_callback' => [ $this, 'sanitize_related_posts_type' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS_TYPE ]['default'],
				],
				[
					'label'           => __( 'Post Query', 'stax' ),
					'priority'        => 302,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'category' => __( 'Related to category', 'stax' ),
						'author'   => __( 'Related to author', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS_TYPE ]['default'],
					'active_callback' => [ $this, 'if_related_posts_enabled' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_related_posts_thumbnail' ],
				],
				[
					'label'           => __( 'Thumbnail Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 303,
					'choices'         => [
						'normal' => [
							'name'  => __( 'Normal', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-normal.svg',
						],
						'wide'   => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-wide.svg',
						],
					],
					'active_callback' => [ $this, 'if_related_posts_enabled' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Sanitize post related type option.
	 *
	 * @param string $value the control value.
	 *
	 * @return string
	 */
	public function sanitize_related_posts_type( $value ) {
		if ( ! in_array( $value, [ 'category', 'author' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS_TYPE ]['default'];
		}

		return $value;
	}

	/**
	 * Sanitize post related thumbnail option.
	 *
	 * @param string $value the control value.
	 *
	 * @return string
	 */
	public function sanitize_related_posts_thumbnail( $value ) {
		if ( ! in_array( $value, [ 'normal', 'wide' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL ]['default'];
		}

		return $value;
	}

	/**
	 * Active callback related posts enabled
	 */
	public function if_related_posts_enabled() {
		return get_theme_mod( Config::OPTION_SINGLE_POST_RELATED_POSTS, false );
	}
}
