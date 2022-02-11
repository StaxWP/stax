<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax_Assets;

class Typography extends Base_Customizer {

	/**
	 * Add controls
	 */
	public function add_controls() {
		$this->sections_typography();
		$this->controls_typography_body();
		$this->controls_typography_headings();
		$this->controls_typography_quote();
		$this->controls_typography_grid_listing();
		$this->controls_typography_normal_listing();
		$this->controls_typography_big_listing();
	}

	/**
	 * Add the customizer section.
	 */
	private function sections_typography() {
		$typography_sections = [
			'stax_typography_body'           => [
				'title'    => __( 'Body', 'stax' ),
				'priority' => 20,
			],
			'stax_typography_headings'       => [
				'title'    => __( 'Headings', 'stax' ),
				'priority' => 30,
			],
			'stax_typography_quote'          => [
				'title'    => __( 'Quote', 'stax' ),
				'priority' => 40,
			],
			'stax_typography_grid_listing'   => [
				'title'    => __( 'Masonry/Grid List', 'stax' ),
				'priority' => 50,
			],
			'stax_typography_normal_listing' => [
				'title'    => __( 'Normal List', 'stax' ),
				'priority' => 60,
			],
			'stax_typography_big_listing'    => [
				'title'    => __( 'Big List', 'stax' ),
				'priority' => 70,
			],
		];

		foreach ( $typography_sections as $section_id => $section_data ) {
			$this->add_section(
				new Section(
					$section_id,
					[
						'title'    => $section_data['title'],
						'panel'    => 'stax_typography',
						'priority' => $section_data['priority'],
					]
				)
			);
		}
	}

	private function controls_typography_body() {
		$this->add_control(
			new Control(
				'stax_body_font_family',
				[
					'transport'         => 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'Mukta, sans-serif',
				],
				[
					'label'                 => esc_html__( 'Body', 'stax' ),
					'section'               => 'stax_typography_body',
					'priority'              => 10,
					'type'                  => 'stax_font_family_control',
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--font-family',
							'selector' => 'body',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Font_Family'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_general',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_body_typography_defaults(),
				],
				[
					'priority'              => 11,
					'section'               => 'stax_typography_body',
					'input_attrs'           => [
						'with_transform'         => false,
						'with_weight'            => false,
						'size_units'             => [ 'px', 'rem', 'em' ],
						'line_height_units'      => [ 'px', 'rem', 'em' ],
						'letter_spacing_units'   => [ 'px', 'rem', 'em' ],
						'size_default'           => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'font-size-sm',
								'font-size-md',
								'font-size-lg',
							],
							'mobile'  => 1,
							'tablet'  => 1.13,
							'desktop' => 1.19,
						],
						'line_height_default'    => [
							'suffix'  => [
								'mobile'  => 'em',
								'tablet'  => 'em',
								'desktop' => 'em',
							],
							'vars'    => [
								'line-height-sm',
								'line-height-md',
								'line-height-lg',
							],
							'mobile'  => 1.5,
							'tablet'  => 1.6,
							'desktop' => 1.7,
						],
						'letter_spacing_default' => [
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
							'vars'    => [
								'letter-spacing-sm',
								'letter-spacing-md',
								'letter-spacing-lg',
							],
							'mobile'  => 0.03,
							'tablet'  => 0.03,
							'desktop' => 0.03,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);
	}

	/**
	 * Add controls for typography headings.
	 */
	private function controls_typography_headings() {
		/**
		 * Headings font family
		 */
		$this->add_control(
			new Control(
				'stax_headings_font_family',
				[
					'transport'         => 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'Heebo, sans-serif',
				],
				[
					'section'               => 'stax_typography_headings',
					'priority'              => 10,
					'type'                  => 'stax_font_family_control',
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--heading-font-family',
							'selector' => 'body',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Font_Family'
			)
		);

		$priority = 20;
		foreach ( Stax_Assets::instance()->get_headings_defaults() as $heading_id => $default_values ) {
			$this->add_control(
				new Control(
					'stax_' . $heading_id . '_accordion_wrap',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => $heading_id,
						'section'          => 'stax_typography_headings',
						'priority'         => $priority += 1,
						'class'            => esc_attr( 'advanced-sidebar-accordion-' . $heading_id ),
						'accordion'        => true,
						'controls_to_wrap' => 1,
						'expanded'         => false,
					],
					'Stax\Customizer\Core\Controls\Heading'
				)
			);

			$this->add_control(
				new Control(
					'stax_' . $heading_id . '_typeface_general',
					[
						'transport' => 'postMessage',
						'default'   => Stax_Assets::instance()->get_headings_typography_defaults( $heading_id ),
					],
					[
						'priority'              => $priority += 1,
						'section'               => 'stax_typography_headings',
						'refresh_on_reset'      => true,
						'input_attrs'           => [
							'with_transform'         => false,
							'weight_default'         => '900',
							'size_units'             => [ 'px', 'rem', 'em' ],
							'line_height_units'      => [ 'px', 'rem', 'em' ],
							'letter_spacing_units'   => [ 'px', 'rem', 'em' ],
							'size_default'           => Stax_Assets::instance()->get_headings_defaults()[ $heading_id ],
							'line_height_default'    => [
								'suffix'  => [
									'mobile'  => 'rem',
									'tablet'  => 'rem',
									'desktop' => 'rem',
								],
								'vars'    => [
									$heading_id . '-line-height-sm',
									$heading_id . '-line-height-md',
									$heading_id . '-line-height-lg',
								],
								'mobile'  => 1.2,
								'tablet'  => 1.2,
								'desktop' => 1.2,
							],
							'letter_spacing_default' => [
								'suffix'  => [
									'mobile'  => 'px',
									'tablet'  => 'px',
									'desktop' => 'px',
								],
								'vars'    => [
									$heading_id . '-letter-spacing-sm',
									$heading_id . '-letter-spacing-md',
									$heading_id . '-letter-spacing-lg',
								],
								'mobile'  => 0,
								'tablet'  => 0,
								'desktop' => 0,
							],
						],
						'type'                  => 'stax_typeface_control',
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => [
							'cssVar' => [
								'vars'     => [
									'--' . $heading_id . '-font-weight' => 'fontWeight',

								],
								'selector' => 'body',
							],
						],
					],
					'\Stax\Customizer\Core\Controls\React\Typography'
				)
			);
		}
	}

	/**
	 * Add controls for quote typography.
	 */
	private function controls_typography_quote() {
		$this->add_control(
			new Control(
				'stax_quote_font_family',
				[
					'transport'         => 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'Noto Serif, sans-serif',
				],
				[
					'label'                 => esc_html__( 'Quote', 'stax' ),
					'section'               => 'stax_typography_quote',
					'priority'              => 10,
					'type'                  => 'stax_font_family_control',
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--quote-font-family',
							'selector' => 'body',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Font_Family'
			)
		);

	}

	/**
	 * Add controls for grid typography.
	 */
	private function controls_typography_grid_listing() {
		$this->add_control(
			new Control(
				'stax_typeface_grid_listing_title_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Title', 'stax' ),
					'section'   => 'stax_typography_grid_listing',
					'priority'  => 10,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_grid_listing_title',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_grid_typography_defaults( 'title' ),
				],
				[
					'priority'              => 20,
					'section'               => 'stax_typography_grid_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-card-title-sm',
								'fs-article-card-title-md',
								'fs-article-card-title-lg',
							],
							'mobile'  => 1.31,
							'tablet'  => 1.5,
							'desktop' => 1.75,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_grid_listing_text_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Text', 'stax' ),
					'section'   => 'stax_typography_grid_listing',
					'priority'  => 30,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_grid_listing_text',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_grid_typography_defaults( 'text' ),
				],
				[
					'priority'              => 40,
					'section'               => 'stax_typography_grid_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-list-card-sm',
								'fs-article-list-card-md',
								'fs-article-list-card-lg',
							],
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1.15,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);
	}

	/**
	 * Add controls for normal list typography.
	 */
	private function controls_typography_normal_listing() {
		$this->add_control(
			new Control(
				'stax_typeface_normal_listing_title_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Title', 'stax' ),
					'section'   => 'stax_typography_normal_listing',
					'priority'  => 10,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_normal_listing_title',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_normal_list_typography_defaults( 'title' ),
				],
				[
					'priority'              => 20,
					'section'               => 'stax_typography_normal_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-list-title-sm',
								'fs-article-list-title-md',
								'fs-article-list-title-lg',
							],
							'mobile'  => 1.31,
							'tablet'  => 1.5,
							'desktop' => 1.75,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_normal_listing_text_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Text', 'stax' ),
					'section'   => 'stax_typography_normal_listing',
					'priority'  => 30,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_normal_listing_text',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_normal_list_typography_defaults( 'text' ),
				],
				[
					'priority'              => 40,
					'section'               => 'stax_typography_normal_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-list-sm',
								'fs-article-list-md',
								'fs-article-list-lg',
							],
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1.15,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);
	}

	/**
	 * Add controls for big list typography.
	 */
	private function controls_typography_big_listing() {
		$this->add_control(
			new Control(
				'stax_typeface_big_listing_title_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Title', 'stax' ),
					'section'   => 'stax_typography_big_listing',
					'priority'  => 10,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_big_listing_title',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_big_list_typography_defaults( 'title' ),
				],
				[
					'priority'              => 20,
					'section'               => 'stax_typography_big_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-big-title-sm',
								'fs-article-big-title-md',
								'fs-article-big-title-lg',
							],
							'mobile'  => 1.5,
							'tablet'  => 1.75,
							'desktop' => 2.25,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_big_listing_text_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'     => __( 'Text', 'stax' ),
					'section'   => 'stax_typography_big_listing',
					'priority'  => 30,
					'accordion' => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'stax_typeface_big_listing_text',
				[
					'transport' => 'postMessage',
					'default'   => Stax_Assets::instance()->get_big_list_typography_defaults( 'text' ),
				],
				[
					'priority'              => 40,
					'section'               => 'stax_typography_big_listing',
					'input_attrs'           => [
						'with_transform'       => false,
						'with_weight'          => false,
						'with_height'          => false,
						'with_spacing'         => false,
						'size_units'           => [ 'px', 'rem', 'em' ],
						'line_height_units'    => [ 'px', 'rem', 'em' ],
						'letter_spacing_units' => [ 'px', 'rem', 'em' ],
						'size_default'         => [
							'suffix'  => [
								'mobile'  => 'rem',
								'tablet'  => 'rem',
								'desktop' => 'rem',
							],
							'vars'    => [
								'fs-article-big-sm',
								'fs-article-big-md',
								'fs-article-big-lg',
							],
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1.25,
						],
					],
					'type'                  => 'stax_typeface_control',
					'live_refresh_selector' => true,
				],
				'\Stax\Customizer\Core\Controls\React\Typography'
			)
		);
	}

}
