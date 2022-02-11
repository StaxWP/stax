<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Config;
use Stax_Assets;

class Colors extends Base_Customizer {

	private $section_id = 'stax_colors';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->wpc->remove_control( 'background_color' );
		$this->section_colors_background();
		$this->controls_colors();
	}

	/**
	 * Add customize section
	 */
	private function section_colors_background() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_COLORS_PRIORITY,
					'title'    => esc_html__( 'Colors', 'stax' ),
				]
			)
		);
	}

	/**
	 * Change controls.
	 */
	public function change_controls() {
		$priority         = 30;
		$controls_to_move = [
			'background_image',
			'background_preset',
			'background_position',
			'background_size',
			'background_repeat',
			'background_attachment',
		];

		foreach ( $controls_to_move as $control_slug ) {
			$control           = $this->get_customizer_object( 'control', $control_slug );
			$control->priority = $priority;
			$control->section  = $this->section_id;
			$priority         += 5;
		}
	}

	/**
	 * Add global colors.
	 */
	private function controls_colors() {
		$this->add_control(
			new Control(
				'stax_global_colors',
				[
					'sanitize_callback' => [ $this, 'sanitize_global_colors' ],
					'default'           => Stax_Assets::instance()->get_global_colors_default(),
					'transport'         => 'postMessage',
				],
				[
					'label'                 => __( 'Global Colors', 'stax' ),
					'priority'              => 10,
					'section'               => $this->section_id,
					'type'                  => 'stax_global_colors',
					'default_values'        => Stax_Assets::instance()->get_global_colors_default(),
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Global_Colors'
			)
		);
	}

	/**
	 * Sanitize Global Colors Setting
	 *
	 * @param array $value recieved value.
	 * @return array
	 */
	public function sanitize_global_colors( $value ) {
		// `flag` key is used to trigger setting change on deep state changes inside the palettes.
		if ( isset( $value['flag'] ) ) {
			unset( $value['flag'] );
		}

		$default = Stax_Assets::instance()->get_global_colors_default();

		if ( ! isset( $value['activePalette'] ) || ! isset( $value['palettes'] ) ) {
			return $default;
		}

		foreach ( $value['palettes'] as $slug => $args ) {
			foreach ( $args['colors'] as $key => $color_val ) {
				if ( is_array( $color_val ) ) {
					$color = $value['palettes'][ $slug ]['colors'][ $key ]['color'];
					$value['palettes'][ $slug ]['colors'][ $key ]['color'] = Sanitizer::instance()->sanitize_colors( $color );
				} else {
					$value['palettes'][ $slug ]['colors'][ $key ] = Sanitizer::instance()->sanitize_colors( $color_val );
				}
			}
		}

		return $value;
	}
}
