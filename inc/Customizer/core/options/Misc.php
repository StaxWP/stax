<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Misc extends Base_Customizer {

	private $section_id = 'stax_misc';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_general();
		$this->controls_misc();
	}

	/**
	 * Add customize section
	 */
	private function section_general() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_MISC_PRIORITY,
					'title'    => esc_html__( 'Miscellaneous', 'stax' ),
				]
			)
		);
	}

	/**
	 * Add misc controls.
	 */
	private function controls_misc() {
		$this->add_control(
			new Control(
				Config::OPTION_MISC_PRELOADER,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_MISC_PRELOADER ]['default'],
				],
				[
					'label'       => __( 'Page Preloader', 'stax' ),
					'description' => __( 'Displays a preloader while navigating.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 10,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_MISC_DISABLE_PAGE_COMMENTS,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_MISC_DISABLE_PAGE_COMMENTS ]['default'],
				],
				[
					'label'       => __( 'Disable Page Comments', 'stax' ),
					'description' => __( 'Force disable comments section on all pages.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 30,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_MISC_DISABLE_POST_COMMENTS,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_MISC_DISABLE_POST_COMMENTS ]['default'],
				],
				[
					'label'       => __( 'Disable Post Comments', 'stax' ),
					'description' => __( 'Force disable comments section on all posts.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 40,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_MISC_POST_UPDATED_DATE,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_MISC_POST_UPDATED_DATE ]['default'],
				],
				[
					'label'       => __( 'Display Post Updated Date', 'stax' ),
					'description' => __( 'Show post\'s updated date instead of publish date on post listings and single post.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 50,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_MISC_DEV_MODE,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_MISC_DEV_MODE ]['default'],
				],
				[
					'label'       => __( 'Dev Mode', 'stax' ),
					'description' => __( 'Load resources unminified.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 60,
				]
			)
		);
	}

}
