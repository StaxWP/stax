<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Panel;

use Stax\Customizer\Config;

class Main extends Base_Customizer {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->register_types();
		$this->add_main_panels();
		$this->add_ui();
		$this->change_controls();
	}

	/**
	 * Register customizer controls type.
	 */
	private function register_types() {
		$this->register_type( 'Stax\Customizer\Core\Controls\Radio_Image', 'control' );
		$this->register_type( 'Stax\Customizer\Core\Controls\Range', 'control' );
		$this->register_type( 'Stax\Customizer\Core\Controls\Responsive_Number', 'control' );
		$this->register_type( 'Stax\Customizer\Core\Controls\Tabs', 'control' );
		$this->register_type( 'Stax\Customizer\Core\Controls\Heading', 'control' );
		$this->register_type( 'Stax\Customizer\Core\Controls\Checkbox', 'control' );
	}

	/**
	 * Add main panels.
	 */
	private function add_main_panels() {
		$panels = [
			'stax_layout'      => [
				'priority' => Config::CUSTOMIZER_LAYOUT_PRIORITY,
				'title'    => __( 'Layout', 'stax' ),
			],
			'stax_single_post' => [
				'priority' => Config::CUSTOMIZER_SINGLE_POST_PRIORITY,
				'title'    => __( 'Single Post', 'stax' ),
			],
			'stax_typography'  => [
				'priority' => Config::CUSTOMIZER_TYPOGRAPHY_PRIORITY,
				'title'    => __( 'Typography', 'stax' ),
			],
		];

		foreach ( $panels as $panel_id => $panel ) {
			$this->add_panel(
				new Panel(
					$panel_id,
					[
						'priority' => $panel['priority'],
						'title'    => $panel['title'],
					]
				)
			);
		}
	}

	/**
	 * Adds UI control.
	 */
	private function add_ui() {
		$this->add_control(
			new Control(
				'stax_ui_control',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'section' => 'static_front_page',
					'type'    => 'stax_ui_control',
				]
			)
		);
	}

	/**
	 * Change controls
	 */
	protected function change_controls() {
		$this->change_customizer_object( 'section', 'static_front_page', 'panel', 'stax_layout' );
	}

}
