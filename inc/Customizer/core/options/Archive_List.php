<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Archive_List extends Base_Customizer {

	private $section_id = 'stax_archive_list';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_general();
		$this->controls_archive_list();
	}

	/**
	 * Add customize section
	 */
	private function section_general() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => Config::CUSTOMIZER_ARCHIVE_LIST,
					'title'    => esc_html__( 'Archive Listing', 'stax' ),
				]
			)
		);
	}

	/**
	 * Add archive list controls.
	 */
	private function controls_archive_list() {
		$this->add_control(
			new Control(
				'stax_archive_list_type_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'List Style', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 10,
					'class'            => esc_attr( 'advanced-list-accordion-1' ),
					'accordion'        => true,
					'controls_to_wrap' => 9,
					'expanded'         => true,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_TYPE,
				[
					'sanitize_callback' => [ $this, 'sanitize_list_type' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE ]['default'],
				],
				[
					'label'    => __( 'Style', 'stax' ),
					'priority' => 20,
					'section'  => $this->section_id,
					'type'     => 'stax_inline_select',
					'options'  => [
						'list'     => __( 'Default List', 'stax' ),
						'list-big' => __( 'Big List', 'stax' ),
						'masonry'  => __( 'Masonry', 'stax' ),
						'grid'     => __( 'Grid', 'stax' ),
					],
					'default'  => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE ]['default'],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS,
				[
					'sanitize_callback' => [ $this, 'sanitize_default_image_position' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS ]['default'],
				],
				[
					'label'           => esc_html__( 'Image position', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_radio_buttons_control',
					'choices'         => [
						'left'  => [
							'icon'    => 'text',
							'tooltip' => __( 'Left', 'stax' ),
						],
						'right' => [
							'icon'    => 'text',
							'tooltip' => __( 'Right', 'stax' ),
						],
					],
					'showLabels'      => true,
					'priority'        => 30,
					'active_callback' => [ $this, 'if_style_default' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_masonry_media_size' ],
				],
				[
					'label'           => __( 'Image/Gallery/Video Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 40,
					'choices'         => [
						'normal' => [
							'name'  => __( 'Normal', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-normal.svg',
						],
						'wide'   => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-wide.svg',
						],
					],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_default_big_media_size' ],
				],
				[
					'label'           => __( 'Image/Gallery/Video Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 50,
					'choices'         => [
						'normal' => [
							'name'  => __( 'Normal', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-normal-big.svg',
						],
						'wide'   => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/post-image-wide-big.svg',
						],
					],
					'active_callback' => [ $this, 'if_style_default_big' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_STANDARD_IMAGE,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_STANDARD_IMAGE ]['default'],
				],
				[
					'label'    => __( 'Show Featured Image For Standard Format', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 60,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_SHOW_CATEGORY,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_SHOW_CATEGORY ]['default'],
				],
				[
					'label'    => __( 'Show Category', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 70,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR ]['default'],
				],
				[
					'label'    => __( 'Show Author Avatar', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 80,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME ]['default'],
				],
				[
					'label'    => __( 'Show Author Name', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 90,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_SHOW_POST_DATE,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_SHOW_POST_DATE ]['default'],
				],
				[
					'label'    => __( 'Show Post Date', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 100,
				]
			)
		);

		$this->add_control(
			new Control(
				'stax_archive_masonry_options_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Masonry Options', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 110,
					'class'            => esc_attr( 'advanced-list-accordion-2' ),
					'accordion'        => true,
					'controls_to_wrap' => 7,
					'expanded'         => false,
					'active_callback'  => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_FIRST_BIG,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_FIRST_BIG ]['default'],
				],
				[
					'label'           => __( 'Make 1st Post Big', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 120,
					'active_callback' => [ $this, 'if_style_masonry' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_sidebar_mobile' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (With Sidebar) - Mobile', 'stax' ),
					'priority'        => 130,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_sidebar_tablet' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (With Sidebar) - Tablet', 'stax' ),
					'priority'        => 140,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_sidebar_desktop' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (With Sidebar) - Desktop', 'stax' ),
					'priority'        => 150,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_no_sidebar_mobile' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Mobile', 'stax' ),
					'priority'        => 160,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
						'4'  => __( '3', 'stax' ),
						'3'  => __( '4', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_no_sidebar_tablet' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Tablet', 'stax' ),
					'priority'        => 170,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
						'4'  => __( '3', 'stax' ),
						'3'  => __( '4', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP,
				[
					'sanitize_callback' => [ $this, 'sanitize_posts_per_row_no_sidebar_desktop' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Desktop', 'stax' ),
					'priority'        => 180,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'6'  => __( '2', 'stax' ),
						'4'  => __( '3', 'stax' ),
						'3'  => __( '4', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP ]['default'],
					'active_callback' => [ $this, 'if_style_masonry' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				'stax_archive_grid_options_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Grid Options', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 190,
					'class'            => esc_attr( 'advanced-list-accordion-3' ),
					'accordion'        => true,
					'controls_to_wrap' => 3,
					'expanded'         => false,
					'active_callback'  => [ $this, 'if_style_grid' ],
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE,
				[
					'sanitize_callback' => [ $this, 'sanitize_grid_posts_per_row_no_sidebar_mobile' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Mobile', 'stax' ),
					'priority'        => 200,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'3'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE ]['default'],
					'active_callback' => [ $this, 'if_style_grid' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET,
				[
					'sanitize_callback' => [ $this, 'sanitize_grid_posts_per_row_no_sidebar_tablet' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Tablet', 'stax' ),
					'priority'        => 210,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'3'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET ]['default'],
					'active_callback' => [ $this, 'if_style_grid' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP,
				[
					'sanitize_callback' => [ $this, 'sanitize_grid_posts_per_row_no_sidebar_desktop' ],
					'default'           => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP ]['default'],
				],
				[
					'label'           => __( 'Posts Per Row (Without Sidebar) - Desktop', 'stax' ),
					'priority'        => 220,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'12' => __( '1', 'stax' ),
						'3'  => __( '2', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP ]['default'],
					'active_callback' => [ $this, 'if_style_grid' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);
	}

	/**
	 * Sanitize list type value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_list_type( $value ) {
		if ( ! in_array( $value, [ 'list', 'list-big', 'masonry', 'grid' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize list default image position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_default_image_position( $value ) {
		if ( ! in_array( $value, [ 'left', 'right' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize list masonry media size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_masonry_media_size( $value ) {
		if ( ! in_array( $value, [ 'normal', 'wide' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize list default big media size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_default_big_media_size( $value ) {
		if ( ! in_array( $value, [ 'normal', 'wide' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with sidebar mobile value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_sidebar_mobile( $value ) {
		if ( ! in_array( $value, [ '12', '6' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with sidebar tablet value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_sidebar_tablet( $value ) {
		if ( ! in_array( $value, [ '12', '6' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with sidebar desktop value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_sidebar_desktop( $value ) {
		if ( ! in_array( $value, [ '12', '6' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with no sidebar mobile value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_no_sidebar_mobile( $value ) {
		if ( ! in_array( $value, [ '12', '6', '4', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with no sidebar tablet value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_no_sidebar_tablet( $value ) {
		if ( ! in_array( $value, [ '12', '6', '4', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize posts per row with no sidebar desktop value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_per_row_no_sidebar_desktop( $value ) {
		if ( ! in_array( $value, [ '12', '6', '4', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize grid posts per row with no sidebar mobile value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_grid_posts_per_row_no_sidebar_mobile( $value ) {
		if ( ! in_array( $value, [ '12', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize grid posts per row with no sidebar tablet value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_grid_posts_per_row_no_sidebar_tablet( $value ) {
		if ( ! in_array( $value, [ '12', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize grid posts per row with no sidebar desktop value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_grid_posts_per_row_no_sidebar_desktop( $value ) {
		if ( ! in_array( $value, [ '12', '3' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Check if list style is default
	 *
	 * @return bool
	 */
	public function if_style_default() {
		return 'list' === get_theme_mod( Config::OPTION_ARCHIVE_LIST_TYPE, false );
	}

	/**
	 * Check if list style is masonry
	 *
	 * @return bool
	 */
	public function if_style_masonry() {
		return 'masonry' === get_theme_mod( Config::OPTION_ARCHIVE_LIST_TYPE, false );
	}

	/**
	 * Check if list style is default big
	 *
	 * @return bool
	 */
	public function if_style_default_big() {
		return 'list-big' === get_theme_mod( Config::OPTION_ARCHIVE_LIST_TYPE, false );
	}

	/**
	 * Check if list style is grid
	 *
	 * @return bool
	 */
	public function if_style_grid() {
		return 'grid' === get_theme_mod( Config::OPTION_ARCHIVE_LIST_TYPE, false );
	}
}
