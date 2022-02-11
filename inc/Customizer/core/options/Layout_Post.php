<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Layout_Post extends Base_Customizer {

	private $section_id = 'stax_layout_post';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_container();
		$this->control_layout_post();
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
					'title'    => esc_html__( 'Post Layout', 'stax' ),
					'panel'    => 'stax_layout',
				]
			)
		);
	}

	/**
	 * Add layout post control
	 */
	private function control_layout_post() {
		$this->add_control(
			new Control(
				Config::OPTION_LAYOUT_POST,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_LAYOUT_POST ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_post_layout' ],
				],
				[
					'label'    => __( 'Layout', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 10,
					'choices'  => [
						'no-side' => [
							'name'  => __( 'No Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/1c.svg',
						],
						'sl'      => [
							'name'  => __( 'Left Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/2cl.svg',
						],
						'sr'      => [
							'name'  => __( 'Right Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/2cr.svg',
						],
						'sll'     => [
							'name'  => __( '2 x Left Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/3cl.svg',
						],
						'srr'     => [
							'name'  => __( '2 x Right Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/3cr.svg',
						],
						'slr'     => [
							'name'  => __( 'Left & Right Sidebar', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/3cm.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_LAYOUT_POST_CONTAINER,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_LAYOUT_POST_CONTAINER ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_post_container_size' ],
				],
				[
					'label'           => __( 'Container Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 20,
					'choices'         => [
						'small' => [
							'name'  => __( 'Small', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/container-small.svg',
						],
						'large' => [
							'name'  => __( 'Large', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/container-large.svg',
						],
					],
					'active_callback' => [ $this, 'if_has_no_sidebar' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR ]['default'],
				],
				[
					'label'           => esc_html__( 'Sticky Primary Sidebar', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 30,
					'active_callback' => [ $this, 'if_has_one_sidebar' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR ]['default'],
				],
				[
					'label'           => esc_html__( 'Sticky Secondary Sidebar', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 40,
					'active_callback' => [ $this, 'if_has_two_sidebars' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_LAYOUT_POST_SIDEBAR_GAP,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_range_value' ],
					'default'           => Config::OPTIONS[ Config::OPTION_LAYOUT_POST_SIDEBAR_GAP ]['default'],
					'transport'         => 'postMessage',
				],
				[
					'label'           => esc_html__( 'Sidebar Top Gap When Sticky', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_range_control',
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 200,
						'units'      => [ 'px' ],
						'defaultVal' => Config::OPTIONS[ Config::OPTION_LAYOUT_POST_SIDEBAR_GAP ]['default'],
					],
					'priority'        => 50,
					'active_callback' => [ $this, 'if_has_one_sidebar_and_sticky' ],
				],
				'\Stax\Customizer\Core\Controls\React\Range'
			)
		);
	}

	/**
	 * Sanitize the container layout value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_post_layout( $value ) {
		$allowed_values = [ 'no-side', 'sl', 'sr', 'sll', 'srr', 'slr' ];

		if ( ! in_array( $value, $allowed_values, true ) ) {
			return Config::OPTIONS[ Config::OPTION_LAYOUT_POST ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize the container size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_post_container_size( $value ) {
		$allowed_values = [ 'small', 'large' ];

		if ( ! in_array( $value, $allowed_values, true ) ) {
			return Config::OPTIONS[ Config::OPTION_LAYOUT_POST_CONTAINER ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * CCheck if it has no sidebar
	 *
	 * @return void
	 */
	public function if_has_no_sidebar() {
		return in_array( get_theme_mod( Config::OPTION_LAYOUT_POST, Config::OPTIONS[ Config::OPTION_LAYOUT_POST ]['default'] ), [ 'no-side' ] );
	}

	/**
	 * Check if it has 1 sidebar
	 *
	 * @return bool
	 */
	public function if_has_one_sidebar() {
		return in_array( get_theme_mod( Config::OPTION_LAYOUT_POST, Config::OPTIONS[ Config::OPTION_LAYOUT_POST ]['default'] ), [ 'sl', 'sr', 'sll', 'srr', 'slr' ] );
	}

	/**
	 * Check if it has 2 sidebars
	 *
	 * @return bool
	 */
	public function if_has_two_sidebars() {
		return in_array( get_theme_mod( Config::OPTION_LAYOUT_POST, Config::OPTIONS[ Config::OPTION_LAYOUT_POST ]['default'] ), [ 'sll', 'srr', 'slr' ] );
	}

	/**
	 * Check if at least 1 sidebar exists and at least one is sticky
	 *
	 * @return bool
	 */
	public function if_has_one_sidebar_and_sticky() {
		return $this->if_has_one_sidebar() &&
			( get_theme_mod( Config::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR, Config::OPTIONS[ Config::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR ]['default'] ) ||
			get_theme_mod( Config::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR, Config::OPTIONS[ Config::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR ]['default'] ) );
	}

}
