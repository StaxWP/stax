<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Single_Post_Navigation extends Base_Customizer {

	private $section_id = 'stax_single_post_navigation';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_navigation();
		$this->controls_navigation();
	}

	/**
	 * Add customize section
	 */
	private function section_navigation() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => 200,
					'title'    => esc_html__( 'Navigation', 'stax' ),
					'panel'    => 'stax_single_post',
				]
			)
		);
	}

	/**
	 * Add single post navigation controls.
	 */
	private function controls_navigation() {
		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_NAVIGATION,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_NAVIGATION ]['default'],
				],
				[
					'label'    => esc_html__( 'Show Navigation', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 201,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_NAVIGATION_POSITION,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_NAVIGATION_POSITION ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_navigation_position' ],
				],
				[
					'label'           => __( 'Navigation Position', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 202,
					'choices'         => [
						'normal' => [
							'name'  => __( 'Normal', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-nav-after-content.svg',
						],
						'sticky' => [
							'name'  => __( 'Sticky', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-nav-sticky.svg',
						],
					],
					'active_callback' => [ $this, 'if_navigation_enabled' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Sanitize navigation position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_navigation_position( $value ) {
		if ( ! in_array( $value, [ 'normal', 'sticky' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_NAVIGATION_POSITION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Active callback navigation enabled
	 */
	public function if_navigation_enabled() {
		return get_theme_mod( Config::OPTION_SINGLE_POST_NAVIGATION, false );
	}

}
