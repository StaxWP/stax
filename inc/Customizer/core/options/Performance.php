<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Performance extends Base_Customizer {

	private $section_id = 'stax_performance';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_general();
		$this->controls_performance();
	}

	/**
	 * Add customize section
	 */
	private function section_general() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_PERFORMANCE,
					'title'    => esc_html__( 'Performance', 'stax' ),
				]
			)
		);
	}

	/**
	 * Add performance controls.
	 */
	private function controls_performance() {
		$this->add_control(
			new Control(
				Config::OPTION_PERF_CSS_PRELOAD,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_PERF_CSS_PRELOAD ]['default'],
				],
				[
					'label'       => __( 'CSS Preload', 'stax' ),
					'description' => __( 'Allow the browser to prioritize theme\'s CSS files.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 10,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_PERF_LEGACY_CSS,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_PERF_LEGACY_CSS ]['default'],
				],
				[
					'label'           => __( 'Load the full 1.x stylesheet', 'stax' ),
					'description'     => __( 'Stax 2.0 loads a much smaller stylesheet. Turn this on only if custom content relied on utility classes from Stax 1.x (for example Bootstrap classes such as "row" or "col-md-6").', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 5,
					// Sites updated from 1.x switch in the "Stax style" section, which keeps a way back.
					'active_callback' => function () {
						return ! \Stax\Style_Switch\Migration::is_offered();
					},
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_LAZY_LOAD_IMAGES,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_LAZY_LOAD_IMAGES ]['default'],
				],
				[
					'label'       => __( 'Lazy Load Images', 'stax' ),
					'description' => __( 'Images and embeds load only when they scroll into view, using the browser\'s native lazy loading.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 10,
				]
			)
		);
	}

}
