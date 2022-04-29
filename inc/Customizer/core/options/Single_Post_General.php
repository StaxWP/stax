<?php

namespace Stax\Customizer\Core\Options;

use Stax\Customizer\Core\Base_Customizer;
use Stax\Customizer\Core\Types\Control;
use Stax\Customizer\Core\Types\Section;

use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Config;

class Single_Post_General extends Base_Customizer {

	private $section_id = 'stax_single_post_general';

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->section_general();
		$this->controls_post_media();
		$this->controls_post_media_breadcrumbs();
		$this->controls_post_media_title();
		$this->controls_post_image();
		$this->controls_post_video();
		$this->controls_post_gallery();
		$this->controls_post_audio();
		$this->controls_post_meta();
		$this->controls_post_shapes();
	}

	/**
	 * Add customize section
	 */
	private function section_general() {
		$this->add_section(
			new Section(
				$this->section_id,
				[
					'priority' => 20,
					'title'    => esc_html__( 'General', 'stax' ),
					'panel'    => 'stax_single_post',
				]
			)
		);
	}

	/**
	 * Add single post media controls.
	 */
	private function controls_post_media() {
		$this->add_control(
			new Control(
				'stax_single_post_media_panel_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Media Panel', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 30,
					'class'            => esc_attr( 'advanced-post-accordion-1' ),
					'accordion'        => true,
					'controls_to_wrap' => 4,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT,
				[
					'sanitize_callback' => [ $this, 'sanitize_panel_height' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT ]['default'],
				],
				[
					'label'       => __( 'Full Height', 'stax' ),
					'description' => __( 'Choose up to which resolution the panel should be full height.', 'stax' ),
					'priority'    => 31,
					'section'     => $this->section_id,
					'type'        => 'stax_inline_select',
					'options'     => [
						'1' => __( 'Off', 'stax' ),
						'2' => __( 'Up to Mobile', 'stax' ),
						'3' => __( 'Up to Tablet', 'stax' ),
						'4' => __( 'Up to Desktop', 'stax' ),
					],
					'default'     => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT ]['default'],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_range_value' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT ]['default'],
					'transport'         => 'postMessage',
				],
				[
					'label'                 => esc_html__( 'Max Height', 'stax' ),
					'section'               => $this->section_id,
					'type'                  => 'stax_range_control',
					'input_attrs'           => [
						'min'        => 500,
						'max'        => 900,
						'step'       => 10,
						'units'      => [ 'px' ],
						'defaultVal' => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT ]['default'],
						'output'     => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT ]['input_attrs'],
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--media-max-height',
							'selector' => 'body',
							'suffix'   => 'px',
						],
						'type' => 'default',
					],
					'priority'              => 32,
					'active_callback'       => [ $this, 'if_not_up_to_desktop' ],
				],
				'\Stax\Customizer\Core\Controls\React\Range'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_MEDIA_PANEL_FADE_TEXT,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_FADE_TEXT ]['default'],
				],
				[
					'label'    => __( 'Fade Text On Scroll', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 33,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TOP_READING_BAR,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TOP_READING_BAR ]['default'],
				],
				[
					'label'    => __( 'Top Reading Progress Bar', 'stax' ),
					'section'  => $this->section_id,
					'type'     => 'stax_toggle_control',
					'priority' => 34,
				]
			)
		);
	}

	/**
	 * Add single post breadcrumbs controls.
	 */
	private function controls_post_media_breadcrumbs() {
		$this->add_control(
			new Control(
				'stax_single_post_category_breadcrumbs_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Categories & Breadcrumbs', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 40,
					'class'            => esc_attr( 'advanced-post-accordion-2' ),
					'accordion'        => true,
					'controls_to_wrap' => 2,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_breadcrumbs_type' ],
				],
				[
					'label'    => __( 'Cateory & Breadcrumbs Style', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 41,
					'choices'  => [
						'none'                => [
							'name'  => __( 'None', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/no-category-breadcrumb.svg',
						],
						'cateory'             => [
							'name'  => __( 'Cateories', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/has-category.svg',
						],
						'breadcrumb'          => [
							'name'  => __( 'Breadcrumbs', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/has-breadcrumb.svg',
						],
						'category-breadcrumb' => [
							'name'  => __( 'Cateories & Breadcrumbs', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/has-category-breadcrumb.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION,
				[
					'sanitize_callback' => [ $this, 'sanitize_category_breadcrumbs_animation' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ]['default'],
				],
				[
					'label'       => __( 'Animation', 'stax' ),
					'description' => __( 'Choose your entry animation for breadcrumbs & categories (only when featured content)', 'stax' ),
					'priority'    => 42,
					'section'     => $this->section_id,
					'type'        => 'stax_inline_select',
					'options'     => [
						'none'       => __( 'None', 'stax' ),
						'fadeIn'     => __( 'FadeIn', 'stax' ),
						'fadeInUp'   => __( 'FadeInUp', 'stax' ),
						'fadeInDown' => __( 'FadeInDown', 'stax' ),
					],
					'default'     => Config::OPTIONS[ Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ]['default'],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);
	}

	/**
	 * Add single post title controls.
	 */
	private function controls_post_media_title() {
		$this->add_control(
			new Control(
				'stax_single_post_title_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Title', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 50,
					'class'            => esc_attr( 'advanced-post-accordion-3' ),
					'accordion'        => true,
					'controls_to_wrap' => 6,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TITLE_POSITION,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_title_position' ],
				],
				[
					'label'    => __( 'Position', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 51,
					'choices'  => [
						'title-over'  => [
							'name'  => __( 'Over Image', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-over-image.svg',
						],
						'half'        => [
							'name'  => __( 'Left Of Image', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-on-left-image.svg',
						],
						'title-above' => [
							'name'  => __( 'Above Image', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-above-image.svg',
						],
						'title-below' => [
							'name'  => __( 'Below Image', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-below-image.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TITLE_ALIGN,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_ALIGN ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_title_align' ],
				],
				[
					'label'           => __( 'Alignment', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 52,
					'choices'         => [
						'default' => [
							'name'  => __( 'Default', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-align-default.svg',
						],
						'center'  => [
							'name'  => __( 'Center Horizontally', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-center-horizontally.svg',
						],
					],
					'active_callback' => [ $this, 'if_title_above_or_below' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_title_extra_align' ],
				],
				[
					'label'           => __( 'Alignment', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 53,
					'choices'         => [
						'default'       => [
							'name'  => __( 'Default', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-align-default.svg',
						],
						'center'        => [
							'name'  => __( 'Center Horizontally', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-center-horizontally.svg',
						],
						'middle'        => [
							'name'  => __( 'Center Vertically', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-center-vertically.svg',
						],
						'middle-center' => [
							'name'  => __( 'Center Horizal & Vertical', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/title-center-vertically-horizontally.svg',
						],
					],
					'active_callback' => [ $this, 'if_title_half_or_title_over' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TITLE_COLOR,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_COLOR ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_text_type' ],
				],
				[
					'label'           => __( 'Color', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 54,
					'choices'         => [
						'dark'  => [
							'name'  => __( 'Light', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/mp-text-color-white.svg',
						],
						'light' => [
							'name'  => __( 'Dark', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/mp-text-color-black.svg',
						],
					],
					'active_callback' => [ $this, 'if_title_half_over' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_TITLE_SIZE,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_SIZE ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_title_size' ],
				],
				[
					'label'    => __( 'Size', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 55,
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
				Config::OPTION_SINGLE_POST_TITLE_ANIMTATION,
				[
					'sanitize_callback' => [ $this, 'sanitize_post_title_animation' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_ANIMTATION ]['default'],
				],
				[
					'label'       => __( 'Animation', 'stax' ),
					'description' => __( 'Choose your entry animation type (only when featured content)', 'stax' ),
					'priority'    => 56,
					'section'     => $this->section_id,
					'type'        => 'stax_inline_select',
					'options'     => [
						'none'       => __( 'None', 'stax' ),
						'fadeIn'     => __( 'FadeIn', 'stax' ),
						'fadeInUp'   => __( 'FadeInUp', 'stax' ),
						'fadeInDown' => __( 'FadeInDown', 'stax' ),
					],
					'default'     => Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_ANIMTATION ]['default'],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);
	}

	/**
	 * Add single post image controls.
	 */
	private function controls_post_image() {
		$this->add_control(
			new Control(
				'stax_single_post_image_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Image', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 60,
					'class'            => esc_attr( 'advanced-post-accordion-4' ),
					'accordion'        => true,
					'controls_to_wrap' => 5,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_IMAGE_FOR_STANDARD,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_FOR_STANDARD ]['default'],
				],
				[
					'label'       => __( 'Show Featured Image on Standard Post Format', 'stax' ),
					'description' => __( 'Choose either to show or not the featured image for standard post format.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 61,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_IMAGE_WIDTH,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_WIDTH ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_image_size' ],
				],
				[
					'label'           => __( 'Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 62,
					'choices'         => [
						'wide'       => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-wide.svg',
						],
						'full-width' => [
							'name'  => __( 'Full Width', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-full-width.svg',
						],
					],
					'active_callback' => [ $this, 'if_title_not_half' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_IMAGE_FORMAT,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_FORMAT ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_image_format' ],
				],
				[
					'label'    => __( 'Format', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 63,
					'choices'  => [
						'cover'   => [
							'name'  => __( 'Cover', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/bg-cover.svg',
						],
						'contain' => [
							'name'  => __( 'Contain', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/bg-contain.svg',
						],
					],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_IMAGE_OVERLAY,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_OVERLAY ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_image_overlay' ],
				],
				[
					'label'    => __( 'Color Overlay', 'stax' ),
					'section'  => $this->section_id,
					'priority' => 64,
					'type'     => 'select',
					'choices'  => [
						'none'       => __( 'None', 'stax' ),
						'primary'    => __( 'Primary color (as defined)', 'stax' ),
						'bottom'     => __( 'Bottom (Gradient)', 'stax' ),
						'top-bottom' => __( 'Top-bottom (Gradient)', 'stax' ),
						'red'        => __( 'Red', 'stax' ),
						'pink'       => __( 'Pink', 'stax' ),
						'purple'     => __( 'Purple', 'stax' ),
						'indigo'     => __( 'Indigo', 'stax' ),
						'blue'       => __( 'Blue', 'stax' ),
						'cyan'       => __( 'Cyan', 'stax' ),
						'teal'       => __( 'Teal', 'stax' ),
						'green'      => __( 'Green', 'stax' ),
						'lime'       => __( 'Lime', 'stax' ),
						'yellow'     => __( 'Yellow', 'stax' ),
						'orange'     => __( 'Orange', 'stax' ),
						'brown'      => __( 'Brown', 'stax' ),
						'black'      => __( 'Black', 'stax' ),
					],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_image_overlay_location' ],
				],
				[
					'label'           => __( 'Overlay Display On', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 65,
					'choices'         => [
						'both'    => [
							'name'  => __( 'Single Post & Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-both.svg',
						],
						'listing' => [
							'name'  => __( 'Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-listing.svg',
						],
						'single'  => [
							'name'  => __( 'Single Post', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-single.svg',
						],
					],
					'active_callback' => [ $this, 'if_image_has_overlay' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Add single post video controls.
	 */
	private function controls_post_video() {
		$this->add_control(
			new Control(
				'stax_single_post_video_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Video', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 70,
					'class'            => esc_attr( 'advanced-post-accordion-5' ),
					'accordion'        => true,
					'controls_to_wrap' => 4,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_VIDEO_PANEL,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_PANEL ]['default'],
				],
				[
					'label'       => __( 'Featured', 'stax' ),
					'description' => __( 'Automatically get the first video from the post\'s content and display it in the Media Panel.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 71,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_VIDEO_WIDTH,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_WIDTH ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_video_size' ],
				],
				[
					'label'           => __( 'Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 72,
					'choices'         => [
						'wide'       => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-wide.svg',
						],
						'full-width' => [
							'name'  => __( 'Full Width', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-full-width.svg',
						],
					],
					'active_callback' => [ $this, 'if_video_featured' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_VIDEO_OVERLAY,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_OVERLAY ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_video_overlay' ],
				],
				[
					'label'           => __( 'Color Overlay', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 73,
					'type'            => 'select',
					'choices'         => [
						'none'       => __( 'None', 'stax' ),
						'primary'    => __( 'Primary color (as defined)', 'stax' ),
						'bottom'     => __( 'Bottom (Gradient)', 'stax' ),
						'top-bottom' => __( 'Top-bottom (Gradient)', 'stax' ),
						'red'        => __( 'Red', 'stax' ),
						'pink'       => __( 'Pink', 'stax' ),
						'purple'     => __( 'Purple', 'stax' ),
						'indigo'     => __( 'Indigo', 'stax' ),
						'blue'       => __( 'Blue', 'stax' ),
						'cyan'       => __( 'Cyan', 'stax' ),
						'teal'       => __( 'Teal', 'stax' ),
						'green'      => __( 'Green', 'stax' ),
						'lime'       => __( 'Lime', 'stax' ),
						'yellow'     => __( 'Yellow', 'stax' ),
						'orange'     => __( 'Orange', 'stax' ),
						'brown'      => __( 'Brown', 'stax' ),
						'black'      => __( 'Black', 'stax' ),
					],
					'active_callback' => [ $this, 'if_video_featured' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_video_overlay_location' ],
				],
				[
					'label'           => __( 'Overlay Display On', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 74,
					'choices'         => [
						'both'    => [
							'name'  => __( 'Single Post & Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-both.svg',
						],
						'listing' => [
							'name'  => __( 'Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-listing.svg',
						],
						'single'  => [
							'name'  => __( 'Single Post', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-single.svg',
						],
					],
					'active_callback' => [ $this, 'if_video_has_overlay' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Add single post gallery controls.
	 */
	private function controls_post_gallery() {
		$this->add_control(
			new Control(
				'stax_single_post_gallery_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Gallery', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 80,
					'class'            => esc_attr( 'advanced-post-accordion-6' ),
					'accordion'        => true,
					'controls_to_wrap' => 5,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_GALLERY_PANEL,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_PANEL ]['default'],
				],
				[
					'label'       => __( 'Featured', 'stax' ),
					'description' => __( 'Automatically get the first gallery from the post\'s content and display it in the Media Panel.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 81,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_GALLERY_WIDTH,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_WIDTH ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_gallery_size' ],
				],
				[
					'label'           => __( 'Size', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 82,
					'choices'         => [
						'wide'       => [
							'name'  => __( 'Wide', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-wide.svg',
						],
						'full-width' => [
							'name'  => __( 'Full Width', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/cover-image-full-width.svg',
						],
					],
					'active_callback' => [ $this, 'if_gallery_featured' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_GALLERY_SLIDES,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_SLIDES ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_gallery_slides' ],
				],
				[
					'label'           => __( 'Slides', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 83,
					'choices'         => [
						'portrait' => [
							'name'  => __( 'Multiple', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/gallery-img-portrait.svg',
						],
						'wide'     => [
							'name'  => __( 'Single', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/gallery-img-wide.svg',
						],
					],
					'active_callback' => [ $this, 'if_gallery_featured' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_GALLERY_OVERLAY,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_OVERLAY ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_gallery_overlay' ],
				],
				[
					'label'           => __( 'Color Overlay', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 84,
					'type'            => 'select',
					'choices'         => [
						'none'       => __( 'None', 'stax' ),
						'primary'    => __( 'Primary color (as defined)', 'stax' ),
						'bottom'     => __( 'Bottom (Gradient)', 'stax' ),
						'top-bottom' => __( 'Top-bottom (Gradient)', 'stax' ),
						'red'        => __( 'Red', 'stax' ),
						'pink'       => __( 'Pink', 'stax' ),
						'purple'     => __( 'Purple', 'stax' ),
						'indigo'     => __( 'Indigo', 'stax' ),
						'blue'       => __( 'Blue', 'stax' ),
						'cyan'       => __( 'Cyan', 'stax' ),
						'teal'       => __( 'Teal', 'stax' ),
						'green'      => __( 'Green', 'stax' ),
						'lime'       => __( 'Lime', 'stax' ),
						'yellow'     => __( 'Yellow', 'stax' ),
						'orange'     => __( 'Orange', 'stax' ),
						'brown'      => __( 'Brown', 'stax' ),
						'black'      => __( 'Black', 'stax' ),
					],
					'active_callback' => [ $this, 'if_gallery_featured' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION,
				[
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION ]['default'],
					'sanitize_callback' => [ $this, 'sanitize_panel_gallery_overlay_location' ],
				],
				[
					'label'           => __( 'Overlay Display On', 'stax' ),
					'section'         => $this->section_id,
					'priority'        => 85,
					'choices'         => [
						'both'    => [
							'name'  => __( 'Single Post & Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-both.svg',
						],
						'listing' => [
							'name'  => __( 'Listings', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-listing.svg',
						],
						'single'  => [
							'name'  => __( 'Single Post', 'stax' ),
							'image' => get_template_directory_uri() . '/assets/img/wp-customizer/show-on-single.svg',
						],
					],
					'active_callback' => [ $this, 'if_gallery_has_overlay' ],
				],
				'\Stax\Customizer\Core\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Add single post audio controls.
	 */
	private function controls_post_audio() {
		$this->add_control(
			new Control(
				'stax_single_post_audio_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Audio', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 90,
					'class'            => esc_attr( 'advanced-post-accordion-7' ),
					'accordion'        => true,
					'controls_to_wrap' => 1,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_AUDIO_PANEL,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_AUDIO_PANEL ]['default'],
				],
				[
					'label'       => __( 'Featured', 'stax' ),
					'description' => __( 'Automatically get the first audio from the post\'s content and display it in the Media Panel.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 91,
				]
			)
		);
	}

	/**
	 * Add single post meta controls.
	 */
	private function controls_post_meta() {
		$this->add_control(
			new Control(
				'stax_single_post_meta_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Meta', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 100,
					'class'            => esc_attr( 'advanced-post-accordion-8' ),
					'accordion'        => true,
					'controls_to_wrap' => 6,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META ]['default'],
				],
				[
					'label'       => __( 'Show', 'stax' ),
					'description' => __( 'Display the post\'s meta and choose which of them should be visible.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 101,
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META_AUTHOR_AVATAR,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_AUTHOR_AVATAR ]['default'],
				],
				[
					'label'           => __( 'Author Avatar', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 102,
					'active_callback' => [ $this, 'if_meta_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META_AUTHOR_NAME,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_AUTHOR_NAME ]['default'],
				],
				[
					'label'           => __( 'Author Name', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 103,
					'active_callback' => [ $this, 'if_meta_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META_POST_DATE,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_POST_DATE ]['default'],
				],
				[
					'label'           => __( 'Post Date', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 104,
					'active_callback' => [ $this, 'if_meta_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META_READING_TIME,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_READING_TIME ]['default'],
				],
				[
					'label'           => __( 'Reading Time', 'stax' ),
					'section'         => $this->section_id,
					'type'            => 'stax_toggle_control',
					'priority'        => 105,
					'active_callback' => [ $this, 'if_meta_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_META_ANIMATION,
				[
					'sanitize_callback' => [ $this, 'sanitize_panel_meta_animation' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_ANIMATION ]['default'],
				],
				[
					'label'           => __( 'Animation', 'stax' ),
					'description'     => __( 'Choose your entry animation type (only when featured media)', 'stax' ),
					'priority'        => 106,
					'section'         => $this->section_id,
					'type'            => 'stax_inline_select',
					'options'         => [
						'none'       => __( 'None', 'stax' ),
						'fadeIn'     => __( 'FadeIn', 'stax' ),
						'fadeInUp'   => __( 'FadeInUp', 'stax' ),
						'fadeInDown' => __( 'FadeInDown', 'stax' ),
					],
					'default'         => Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_ANIMATION ]['default'],
					'active_callback' => [ $this, 'if_meta_enabled' ],
				],
				'\Stax\Customizer\Core\Controls\React\Inline_Select'
			)
		);
	}

	/**
	 * Add single post shapes controls.
	 */
	private function controls_post_shapes() {
		$this->add_control(
			new Control(
				'stax_single_post_shape_acc',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => __( 'Shapes', 'stax' ),
					'section'          => $this->section_id,
					'priority'         => 110,
					'class'            => esc_attr( 'advanced-post-accordion-9' ),
					'accordion'        => true,
					'controls_to_wrap' => 1,
					'expanded'         => false,
				],
				'\Stax\Customizer\Core\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				Config::OPTION_SINGLE_POST_SHAPES,
				[
					'sanitize_callback' => [ Sanitizer::instance(), 'sanitize_checkbox' ],
					'default'           => Config::OPTIONS[ Config::OPTION_SINGLE_POST_SHAPES ]['default'],
				],
				[
					'label'       => __( 'Show', 'stax' ),
					'description' => __( 'Displays predefined & animated shapes over the title section.', 'stax' ),
					'section'     => $this->section_id,
					'type'        => 'stax_toggle_control',
					'priority'    => 111,
				]
			)
		);

	}

	/**
	 * Sanitize panel height value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_height( $value ) {
		if ( ! in_array( $value, [ '1', '2', '3', '4' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize breadcrumbs & category animation value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_category_breadcrumbs_animation( $value ) {
		if ( ! in_array( $value, [ 'none', 'fadeIn', 'fadeInUp', 'fadeInDown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize title position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_title_position( $value ) {
		if ( ! in_array( $value, [ 'title-over', 'half', 'title-above', 'title-below' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'];
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
	public function sanitize_panel_title_align( $value ) {
		if ( ! in_array( $value, [ 'default', 'center' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_ALIGN ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize title extra align value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_title_extra_align( $value ) {
		if ( ! in_array( $value, [ 'default', 'center', 'middle', 'middle-center' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN ]['default'];
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
	public function sanitize_panel_title_size( $value ) {
		if ( ! in_array( $value, [ 'default', 'large' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_SIZE ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize title animation value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_post_title_animation( $value ) {
		if ( ! in_array( $value, [ 'none', 'fadeIn', 'fadeInUp', 'fadeInDown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_ANIMTATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize text type value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_text_type( $value ) {
		if ( ! in_array( $value, [ 'light', 'dark' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_COLOR ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize breadcrumbs value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_breadcrumbs_type( $value ) {
		if ( ! in_array( $value, [ 'none', 'category', 'breadcrumb', 'category-breadcrumb' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize meta animation value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_meta_animation( $value ) {
		if ( ! in_array( $value, [ 'none', 'fadeIn', 'fadeInUp', 'fadeInDown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_META_ANIMATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize image size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_image_size( $value ) {
		if ( ! in_array( $value, [ 'wide', 'full-width' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_WIDTH ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize video size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_video_size( $value ) {
		if ( ! in_array( $value, [ 'wide', 'full-width' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_WIDTH ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize video size value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_gallery_size( $value ) {
		if ( ! in_array( $value, [ 'wide', 'full-width' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_WIDTH ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize image format value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_image_format( $value ) {
		if ( ! in_array( $value, [ 'cover', 'contain' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_FORMAT ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize image overlay value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_image_overlay( $value ) {
		if ( ! in_array( $value, [ 'none', 'primary', 'bottom', 'top-bottom', 'red', 'pink', 'purple', 'indigo', 'blue', 'cyan', 'teal', 'green', 'lime', 'yellow', 'orange', 'black', 'brown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_OVERLAY ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize video overlay value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_video_overlay( $value ) {
		if ( ! in_array( $value, [ 'none', 'primary', 'bottom', 'top-bottom', 'red', 'pink', 'purple', 'indigo', 'blue', 'cyan', 'teal', 'green', 'lime', 'yellow', 'orange', 'black', 'brown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_OVERLAY ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize gallery overlay value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_gallery_overlay( $value ) {
		if ( ! in_array( $value, [ 'none', 'primary', 'bottom', 'top-bottom', 'red', 'pink', 'purple', 'indigo', 'blue', 'cyan', 'teal', 'green', 'lime', 'yellow', 'orange', 'black', 'brown' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_OVERLAY ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize gallery slides value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_gallery_slides( $value ) {
		if ( ! in_array( $value, [ 'portrait', 'wide' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_SLIDES ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize image overlay position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_image_overlay_location( $value ) {
		if ( ! in_array( $value, [ 'both', 'listing', 'single' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize video overlay position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_video_overlay_location( $value ) {
		if ( ! in_array( $value, [ 'both', 'listing', 'single' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize gallery overlay position value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_panel_gallery_overlay_location( $value ) {
		if ( ! in_array( $value, [ 'both', 'listing', 'single' ], true ) ) {
			return Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION ]['default'];
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Check if height is not up to desktop
	 *
	 * @return bool
	 */
	public function if_not_up_to_desktop() {
		return '4' !== get_theme_mod( Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT, 1 );
	}

	/**
	 * Check if title position is over or half
	 *
	 * @return bool
	 */
	public function if_title_half_or_title_over() {
		return 'title-over' === get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] ) ||
				'half' === get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] );
	}

	/**
	 * Check if title position is over
	 *
	 * @return bool
	 */
	public function if_title_half_over() {
		return 'title-over' === get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] );
	}

	/**
	 * Check if title position is above or below
	 *
	 * @return bool
	 */
	public function if_title_above_or_below() {
		return 'title-above' === get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] ) ||
				'title-below' === get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] );
	}

	/**
	 * Check if title position is not half
	 *
	 * @return bool
	 */
	public function if_title_not_half() {
		return 'half' !== get_theme_mod( Config::OPTION_SINGLE_POST_TITLE_POSITION, Config::OPTIONS[ Config::OPTION_SINGLE_POST_TITLE_POSITION ]['default'] );
	}

	/**
	 * Check if image has overlay
	 *
	 * @return bool
	 */
	public function if_image_has_overlay() {
		return 'none' !== get_theme_mod( Config::OPTION_SINGLE_POST_IMAGE_OVERLAY, Config::OPTIONS[ Config::OPTION_SINGLE_POST_IMAGE_OVERLAY ]['default'] );
	}

	/**
	 * Check if video is featured
	 *
	 * @return bool
	 */
	public function if_video_featured() {
		return true === get_theme_mod( Config::OPTION_SINGLE_POST_VIDEO_PANEL, Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_PANEL ]['default'] );
	}

	/**
	 * Check if video has overlay
	 *
	 * @return bool
	 */
	public function if_video_has_overlay() {
		return 'none' !== get_theme_mod( Config::OPTION_SINGLE_POST_VIDEO_OVERLAY, Config::OPTIONS[ Config::OPTION_SINGLE_POST_VIDEO_OVERLAY ]['default'] ) && $this->if_video_featured();
	}

	/**
	 * Check if gallery is featured
	 *
	 * @return bool
	 */
	public function if_gallery_featured() {
		return true === get_theme_mod( Config::OPTION_SINGLE_POST_GALLERY_PANEL, Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_PANEL ]['default'] );
	}

	/**
	 * Check if gallery has overlay
	 *
	 * @return bool
	 */
	public function if_gallery_has_overlay() {
		return 'none' !== get_theme_mod( Config::OPTION_SINGLE_POST_GALLERY_OVERLAY, Config::OPTIONS[ Config::OPTION_SINGLE_POST_GALLERY_OVERLAY ]['default'] ) && $this->if_gallery_featured();
	}

	/**
	 * Check if meta is enabled
	 *
	 * @return bool
	 */
	public function if_meta_enabled() {
		return true === get_theme_mod( Config::OPTION_SINGLE_POST_META, Config::OPTIONS[ Config::OPTION_SINGLE_POST_META ]['default'] );
	}

}
