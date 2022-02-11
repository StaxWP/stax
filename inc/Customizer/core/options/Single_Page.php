<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Single_Page extends Base_Customizer {

	private $section_id = 'stax_single_page';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_general();
		$this->controls_page();
	}

	/**
	 * Add customize section
	 */
	private function section_general() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_SINGLE_PAGE_PRIORITY,
					'title'    => esc_html__( 'Single Page', 'stax' ),
				]
			)
		);
	}

	/**
	 * Add single page controls.
	 */
	private function controls_page() {
		$this->add_control(
			new Control(
				'stax_single_page_title_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Title', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 10,
					'class'            => esc_attr( 'advanced-page-accordion-2' ),
					'accordion'        => true,
					'controls_to_wrap' => 2,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_PAGE_TITLE_SIZE,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_TITLE_SIZE ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_page_title_size' ],
				],
				[
					'label'    => __( 'Size', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 20,
					'choices'  => [
						'default' => [
							'name'  => __( 'Normal', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-size-normal.svg',
						],
						'large'   => [
							'name'  => __( 'Large', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-size-large.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_PAGE_TITLE_ALIGN,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_TITLE_ALIGN ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_page_title_align' ],
				],
				[
					'label'    => __( 'Alignment', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 30,
					'choices'  => [
						'default' => [
							'name'  => __( 'Default', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-align-default.svg',
						],
						'center'  => [
							'name'  => __( 'Center Horizontally', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-center-horizontally.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				'stax_single_page_breadcrumbs_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Breadcrumbs', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 40,
					'class'            => esc_attr( 'advanced-page-accordion-1' ),
					'accordion'        => true,
					'controls_to_wrap' => 1,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_PAGE_BREADCRUMBS,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_BREADCRUMBS ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_page_breadcrumbs' ],
				],
				[
					'label'    => __( 'Visibility', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 50,
					'choices'  => [
						'0' => [
							'name'  => __( 'Hide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/no-category-breadcrumb.svg',
						],
						'1' => [
							'name'  => __( 'Show', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/has-breadcrumb.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				'stax_single_page_shapes_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Shapes', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 60,
					'class'            => esc_attr( 'advanced-page-accordion-3' ),
					'accordion'        => true,
					'controls_to_wrap' => 1,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_PAGE_SHAPES,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_SHAPES ]['default'],
				],
				[
					'label'       => __( 'Show', 'stax' ),
					'description' => __( 'Displays predefined & animated shapes over the title section.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 70,
				]
			)
		);
	}

	/**
	 * Sanitize breadcrumbs value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_page_breadcrumbs( $value ) {
		if ( ! in_array( $value, [ '0', '1' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_BREADCRUMBS ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize title size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_page_title_size( $value ) {
		if ( ! in_array( $value, [ 'default', 'large' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_TITLE_SIZE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize title align value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_page_title_align( $value ) {
		if ( ! in_array( $value, [ 'default', 'center' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_PAGE_TITLE_ALIGN ]['default'];
		}

		return sanitize_text_field( $value );
	}

}
