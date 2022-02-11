<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Not_Found extends Base_Customizer {

	private $section_id = 'stax_404_page';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_404();
		$this->controls_404();
	}

	/**
	 * Add customize section
	 */
	private function section_404() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_404_PRIORITY,
					'title'    => esc_html__( '404 Page', 'stax' ),
				]
			)
		);
	}

	/**
	 * Add 404 page controls.
	 */
	private function controls_404() {
		$this->add_control(
			new Control(
				Config::OPTION_404_FOOTER,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_404_FOOTER ]['default'],
				],
				[
					'label'    => esc_html__( 'Show Footer', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 20,
				]
			)
		);
	}

}
