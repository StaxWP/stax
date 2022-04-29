<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Layout_Container extends Base_Customizer {

	private $section_id = 'stax_general_container';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_container();
		$this->control_general_container();
	}

	/**
	 * Add customize section
	 */
	private function section_container() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => 25,
					'title'    => esc_html__( 'Site Container', 'stax' ),
					'panel'    => 'stax_layout',
				]
			)
		);
	}

	/**
	 * Add container width control
	 */
	private function control_general_container() {
		$this->add_control(
			new Control(
				Config::OPTION_GENERAL_CONTAINER_BOXED,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_BOXED ]['default'],
				],
				[
					'label'    => esc_html__( 'Boxed', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 10,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_GENERAL_CONTAINER_WIDTH,
				[
					'default'   => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_WIDTH ]['default'],
					'transport' => 'postMessage',
				],
				[
					'label'           => esc_html__( 'Width', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_range_control',
					'input_attrs'     => [
						'min'        => 1000,
						'max'        => 1800,
						'step'       => 10,
						'units'      => [ 'px' ],
						'defaultVal' => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_WIDTH ]['default'],
						'output'     => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_WIDTH ]['input_attrs'],
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'  => [
							'vars'     => '--layout-max-width',
							'selector' => 'body',
							'suffix'   => 'px',
						],
						'type' => 'default',
					],
					'priority'        => 20,
					'active_callback' => [ $this, 'if_container_boxed' ],
				],
				'\Stax\Customizer\Core\Controls\React\Range'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_GENERAL_CONTAINER_BACKGROUND,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_colors' ],
					'default'           => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_BACKGROUND ]['default'],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Background Color', 'stax' ),
					'section'               => $this->section_id,
					'default'               => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_BACKGROUND ]['default'],
					'input_attrs'           => Config::OPTIONS[ Config::OPTION_GENERAL_CONTAINER_BACKGROUND ]['input_attrs'],
					'priority'              => 30,
					'active_callback'       => [ $this, 'if_container_boxed' ],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'template' => '
							.layout-boxed {
							    background-color: {{value}};
						    }',
					],
				],
				'\Stax\Customizer\Core\Controls\React\Color'
			)
		);
	}

	/**
	 * Active callback container boxed
	 */
	public function if_container_boxed() {
		return get_theme_mod( Config::OPTION_GENERAL_CONTAINER_BOXED, false );
	}

}
