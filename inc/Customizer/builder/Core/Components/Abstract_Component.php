<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Builder\Abstract_Builder;
use Stax\Builder\Core\Css_Generator;
use Stax\Builder\Core\Interfaces\Component;
use Stax\Builder\Core\Settings;
use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Builder\Traits\Core;
use Stax\Customizer\Config;
use Stax\Customizer\Core\Sanitizer;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Font_Manager;
use WP_Customize_Manager;

abstract class Abstract_Component implements Component {
	use Core;

	const ALIGNMENT_ID      = 'component_align';
	const VERTICAL_ALIGN_ID = 'component_vertical_align';
	const PADDING_ID        = 'component_padding';
	const MARGIN_ID         = 'component_margin';
	const FONT_FAMILY_ID    = 'component_font_family';
	const TYPEFACE_ID       = 'component_typeface';

	/**
	 * Current id of the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var null|string
	 */
	public static $current_component = null;

	/**
	 * Check if current component should show CSS when rendered inside customizer.
	 *
	 * @var bool
	 */
	public static $should_show_css = true;

	/**
	 * Default alignament value for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var null|string
	 */
	public $default_align = 'left';

	/**
	 * Current X pos of the component if set.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var mixed|null $current_x
	 */
	public $current_x = null;

	/**
	 * Current Width of the component if set.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var mixed|null $current_width
	 */
	public $current_width = null;

	/**
	 * The ID of component.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string $id
	 */
	protected $id;

	/**
	 * The section name for the component
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string $section
	 */
	protected $section;

	/**
	 * The component slug.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string $section
	 */
	protected $component_slug = 'hfg-generic-component';

	/**
	 * Should component merge?
	 *
	 * @since  2.5.2
	 * @access protected
	 * @var bool $is_auto_width
	 */
	protected $is_auto_width = false;

	/**
	 * The section icon.
	 *
	 * @access protected
	 * @var string $icon
	 */
	protected $icon = 'welcome-widgets-menus';

	/**
	 * The component preview image.
	 *
	 * @access protected
	 * @var string $preview_image
	 */
	protected $preview_image = null;

	/**
	 * The component default width.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int $width
	 */
	protected $width = 1;

	/**
	 * The component label
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string $label
	 */
	protected $label;

	/**
	 * The component description
	 *
	 * @since   1.0.1
	 * @access  protected
	 * @var string $description
	 */
	protected $description;

	/**
	 * The component priority in customizer
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int $priority
	 */
	protected $priority = 30;

	/**
	 * The name of the component panel
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var string $panel
	 */
	protected $panel;

	/**
	 * Holds component builder id.
	 *
	 * @var string $builder_id Builder id.
	 */
	private $builder_id;

	/**
	 * Can override the default css selector.
	 * Allows child components to specify their own selector for inherited style rules.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var null|string $default_selector
	 */
	protected $default_selector = null;

	/**
	 * Default media selectors for device.
	 *
	 * @since   1.0.1
	 * @access  protected
	 * @var array $media_selectors The device and css media selector pairing.
	 */
	protected $media_selectors = [
		'mobile'  => ' @media (max-width: 576px)',
		'tablet'  => ' @media (min-width: 576px)',
		'desktop' => ' @media (min-width: 961px)',
	];

	/**
	 * The default typography selector on which to apply the settings.
	 *
	 * @var null
	 */
	protected $default_typography_selector = null;

	/**
	 * Padding settings default values.
	 *
	 * @var array
	 */
	protected $default_padding_value = [
		'mobile'       => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'tablet'       => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'desktop'      => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'mobile-unit'  => 'px',
		'tablet-unit'  => 'px',
		'desktop-unit' => 'px',
	];

	/**
	 * Margin settings default values.
	 *
	 * @var array
	 */
	protected $default_margin_value = [
		'mobile'       => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'tablet'       => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'desktop'      => [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		],
		'mobile-unit'  => 'px',
		'tablet-unit'  => 'px',
		'desktop-unit' => 'px',
	];

	/**
	 * Typography control default values.
	 *
	 * @var array
	 */
	protected $typography_default = [
		'fontSize'      => [
			'suffix'  => [
				'mobile'  => 'px',
				'tablet'  => 'px',
				'desktop' => 'px',
			],
			'vars'    => [],
			'mobile'  => '',
			'tablet'  => '',
			'desktop' => '',
		],
		'lineHeight'    => [
			'vars'    => [],
			'mobile'  => '',
			'tablet'  => '',
			'desktop' => '',
		],
		'letterSpacing' => [
			'vars'    => [],
			'mobile'  => '',
			'tablet'  => '',
			'desktop' => '',
		],
		'fontWeight'    => '',
		'textTransform' => 'none',
	];

	/**
	 * Should have font family control.
	 *
	 * @var bool
	 */
	public $has_font_family_control = false;

	/**
	 * Should have typeface control.
	 *
	 * @var bool
	 */
	public $has_typeface_control = false;

	/**
	 * Should have horizontal alignment.
	 *
	 * @var bool
	 */
	public $has_horizontal_alignment = false;

	/**
	 * Abstract_Component constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		if ( ! $this->is_active() ) {
			return;
		}

		$this->init();
		$this->maybe_enqueue_fonts();
		$this->set_property( 'panel', $panel );
		$this->set_property( 'icon', $this->icon );

		if ( null === $this->preview_image ) {
			$this->set_property( 'preview_image', $this->preview_image );
		}

		if ( null === $this->section ) {
			$this->set_property( 'section', $this->get_id() );
		}

		add_action( 'init', [ $this, 'define_settings' ] );
	}

	/**
	 * Render CSS code for component.
	 */
	public function render_css() {
		if ( ! self::$should_show_css ) {
			return;
		}

		if ( ! is_customize_preview() ) {
			return;
		}

		$css_array = [];

		$css_array = $this->add_style( $css_array );
		$generator = new Css_Generator();
		$generator->set( $css_array );
		$style = $generator->generate();

		echo '<style type="text/css" id="' . esc_attr( $this->get_id() ) . '-style">' . $style . '</style>';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		self::$should_show_css = false;
	}

	/**
	 * Allow for constant changes in pro.
	 *
	 * @param string $const Name of the constant.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function get_class_const( $const ) {
		if ( defined( 'static::' . $const ) ) {
			return constant( 'static::' . $const );
		}

		return '';
	}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @return bool
	 * @since   1.0.1
	 * @access  public
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Method to set protected properties for class.
	 *
	 * @param string $key The property key name.
	 * @param string $value The property value.
	 *
	 * @return bool
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function set_property( $key = '', $value = '' ) {
		if ( ! property_exists( $this, $key ) ) {
			return false;
		}

		$this->$key = $value;

		return true;
	}

	/**
	 * Utility method to return the component ID.
	 *
	 * @return string
	 * @since   1.0.0
	 * @access  public
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Return the settings for the component.
	 *
	 * @return array
	 * @since   1.0.0
	 * @updated 1.0.1
	 * @access  public
	 */
	public function get_settings() {
		return [
			'name'          => $this->label,
			'description'   => $this->description,
			'id'            => $this->id,
			'width'         => $this->width,
			'section'       => $this->section,
			'icon'          => $this->icon,
			'previewImage'  => $this->preview_image,
			'componentSlug' => $this->component_slug,
		];
	}

	/**
	 * Get the section id.
	 *
	 * @return string
	 * @since   1.0.0
	 * @access  public
	 */
	public function get_section_id() {
		return $this->section;
	}

	/**
	 * Method to get protected properties for class.
	 *
	 * @param string $key The property key name.
	 *
	 * @return bool
	 * @since   1.0.0
	 * @access  protected
	 */
	public function get_property( $key = '' ) {
		if ( ! property_exists( $this, $key ) ) {
			return false;
		}

		return $this->$key;
	}

	/**
	 * Define global settings.
	 */
	public function define_settings() {
		$this->add_settings();
		$padding_selector = '.builder-item--' . $this->get_id() . ' > :not(.customize-partial-edit-shortcut):not(.item--preview-name):first-of-type';

		if ( null !== $this->default_selector ) {
			$padding_selector = $this->default_selector;
		}

		$this->add_horizontal_alignment_control();

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::PADDING_ID,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
				'label'                 => __( 'Padding', 'stax' ),
				'type'                  => '\Stax\Customizer\Core\Controls\React\Spacing',
				'default'               => $this->default_padding_value,
				'options'               => [
					'input_attrs' => [
						'min'     => 0,
						'default' => $this->default_padding_value,
					],
				],
				'live_refresh_selector' => $padding_selector,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--padding',
						'responsive' => true,
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'prop'   => 'padding',
				],
				'section'               => $this->section,
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::MARGIN_ID,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
				'label'                 => __( 'Margin', 'stax' ),
				'type'                  => '\Stax\Customizer\Core\Controls\React\Spacing',
				'default'               => $this->default_padding_value,
				'live_refresh_selector' => '.builder-item--' . $this->get_id(),
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--margin',
						'responsive' => true,
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'prop'   => 'margin',
				],
				'section'               => $this->section,
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);

		$this->add_typography_controls();

		do_action( 'hfg_component_settings', $this->get_id() );
	}

	/**
	 * Get builder where component can be used.
	 *
	 * @return string Assigned builder.
	 */
	public function get_builder_id() {
		return $this->builder_id;
	}

	/**
	 * Called to register component controls.
	 *
	 * @param WP_Customize_Manager $wp_customize The Customize Manager.
	 *
	 * @return WP_Customize_Manager
	 * @since   1.0.0
	 * @updated 1.0.1
	 * @access  public
	 */
	public function customize_register( WP_Customize_Manager $wp_customize ) {
		$description = ( isset( $this->description ) && ! empty( $this->description ) )
			? $this->description
			: '';

		$wp_customize->add_section(
			$this->section,
			[
				'title'              => $this->label,
				'description'        => $description,
				'description_hidden' => '' !== $description,
				'priority'           => $this->priority,
				'panel'              => $this->panel,
			]
		);

		Settings\Manager::get_instance()->load( $this->get_id(), $wp_customize );

		$wp_customize->selective_refresh->add_partial(
			$this->get_id() . '_partial',
			[
				'selector'            => '.builder-item--' . $this->get_id() . ':parent',
				'settings'            => Settings\Manager::get_instance()->get_transport_group( $this->get_id() ),
				'render_callback'     => [ $this, 'render' ],
				'container_inclusive' => true,
			]
		);

		return $wp_customize;
	}

	/**
	 * Render component markup.
	 */
	public function render() {
		self::$current_component           = $this->get_id();
		Abstract_Builder::$current_builder = $this->get_builder_id();
		Main::get_instance()->load( 'component-wrapper' );
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_style( array $css_array = [] ) {
		$rules = [
			'--padding' => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::PADDING_ID,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::PADDING_ID ),
				'directional-prop'                   => Config::CSS_PROP_PADDING,
			],
			'--margin'  => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::MARGIN_ID,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::MARGIN_ID ),
				'directional-prop'                   => Config::CSS_PROP_MARGIN,
			],
		];

		if ( $this->has_font_family_control || $this->has_typeface_control ) {
			$rules = array_merge(
				$rules,
				[
					'--fontFamily'    => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::FONT_FAMILY_ID,
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FONT_FAMILY_ID ),
					],
					'--fontSize'      => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.fontSize',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX  => 'em',
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'fontSize' ),
					],
					'--lineHeight'    => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.lineHeight',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'lineHeight' ),
					],
					'--letterSpacing' => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.letterSpacing',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX  => 'px',
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'letterSpacing' ),
					],
					'--fontWeight'    => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.fontWeight',
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'fontWeight' ),
						'font'                         => 'mods_' . $this->get_id() . '_' . self::FONT_FAMILY_ID,
					],
					'--textTransform' => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.textTransform',
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'textTransform' ),
					],
					'--iconSize'      => [
						Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.fontSize',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX  => 'responsive_suffix',
						Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'fontSize' ),
					],
				]
			);

			if ( strpos( $this->get_id(), Nav::COMPONENT_ID ) !== false || strpos( $this->get_id(), SecondNav::COMPONENT_ID ) !== false ) {
				$css_array[] = [
					Dynamic_Selector::KEY_SELECTOR => '.hfg-is-group.has-' . $this->get_id() . ' .inherit-ff',
					Dynamic_Selector::KEY_RULES    => [
						'--inheritedFF' => [
							Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::FONT_FAMILY_ID,
							Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FONT_FAMILY_ID ),
						],
						'--inheritedFW' => [
							Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::TYPEFACE_ID . '.fontWeight',
							Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::TYPEFACE_ID, 'fontWeight' ),
						],
					],
				];
			}
		}

		if ( $this->has_horizontal_alignment ) {
			$rules['--textAlign'] = [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::ALIGNMENT_ID,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::ALIGNMENT_ID ),
			];

			$justify_map = [
				'left'   => 'flex-start',
				'center' => 'center',
				'right'  => 'flex-end',
			];

			$rules['--justify'] = [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::ALIGNMENT_ID,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) use ( $justify_map ) {
					return sprintf( '%s: %s;', $css_prop, $justify_map[ $value ] );
				},
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::ALIGNMENT_ID ),
			];
		}

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id(),
			Dynamic_Selector::KEY_RULES    => $rules,
		];

		return $css_array;
	}

	/**
	 * Assign component to builder.
	 *
	 * @param string $builder_id Builder unique id.
	 */
	public function assign_builder( $builder_id ) {
		$this->builder_id = $builder_id;

		if ( 'footer' === $this->builder_id ) {
			$this->has_horizontal_alignment = true;
		}
	}

	/**
	 * Add typography controls.
	 */
	private function add_typography_controls() {
		if ( ! $this->has_font_family_control && ! $this->has_typeface_control ) {
			return;
		}

		$accordion_wrap = 0;
		$priority       = 2000;

		if ( $this->has_typeface_control ) {
			$accordion_wrap ++;
		}

		if ( $this->has_font_family_control ) {
			$accordion_wrap ++;
		}

		SettingsManager::get_instance()->add(
			[
				'id'                => $this->get_id() . '_typography_wrap',
				'group'             => $this->get_id(),
				'tab'               => SettingsManager::TAB_STYLE,
				'transport'         => 'postMessage',
				'priority'          => $priority,
				'type'              => 'Stax\Customizer\Core\Controls\Heading',
				'sanitize_callback' => 'sanitize_text_field',
				'label'             => __( 'Typography', 'stax' ),
				'section'           => $this->section,
				'options'           => [
					'accordion'        => true,
					'controls_to_wrap' => $accordion_wrap,
					'expanded'         => false,
					'class'            => esc_attr( 'typography-accordion-' . $this->get_id() ),
				],
			]
		);

		if ( $this->has_font_family_control ) {
			SettingsManager::get_instance()->add(
				[
					'id'                    => self::FONT_FAMILY_ID,
					'group'                 => $this->get_id(),
					'tab'                   => SettingsManager::TAB_STYLE,
					'transport'             => 'postMessage',
					'priority'              => $priority + 1,
					'type'                  => '\Stax\Customizer\Core\Controls\React\Font_Family',
					'live_refresh_selector' => $this->default_typography_selector,
					'live_refresh_selector' => $this->default_typography_selector,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--fontFamily',
							'selector' => '.builder-item--' . $this->get_id(),
						],
					],
					'section'               => $this->section,
					'options'               => [
						'input_attrs' => [
							'default_is_inherit' => true,
						],
					],
				]
			);
		}

		if ( $this->has_typeface_control ) {
			SettingsManager::get_instance()->add(
				[
					'id'                    => self::TYPEFACE_ID,
					'group'                 => $this->get_id(),
					'tab'                   => SettingsManager::TAB_STYLE,
					'transport'             => 'postMessage',
					'priority'              => $priority + 2,
					'type'                  => '\Stax\Customizer\Core\Controls\React\Typography',
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--textTransform' => 'textTransform',
								'--fontWeight'    => 'fontWeight',
								'--fontSize'      => [
									'key'        => 'fontSize',
									'responsive' => true,
								],
								'--lineHeight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--letterSpacing' => [
									'key'        => 'letterSpacing',
									'suffix'     => 'px',
									'responsive' => true,
								],
							],
							'selector' => '.builder-item--' . $this->get_id(),
						],
					],
					'section'               => $this->section,
					'default'               => $this->typography_default,
					'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_typography_control' ],
					'options'               => [
						'input_attrs' => [
							'size_units'             => [ 'px', 'rem', 'em' ],
							'font_weight_default'    => $this->typography_default['fontWeight'],
							'size_default'           => $this->typography_default['fontSize'],
							'line_height_default'    => $this->typography_default['lineHeight'],
							'letter_spacing_default' => $this->typography_default['letterSpacing'],

						],
					],
				]
			);
		}
	}

	/**
	 * Maybe enqueue google fonts.
	 */
	private function maybe_enqueue_fonts() {
		if ( ! $this->has_font_family_control ) {
			return;
		}

		$font = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::FONT_FAMILY_ID );
		if ( empty( $font ) ) {
			return;
		}

		$typography = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::TYPEFACE_ID );
		$weight     = ! isset( $typography['fontWeight'] ) ? [ '300' ] : $typography['fontWeight'];

		Font_Manager::add_google_font( $font, $weight );
	}

	/**
	 * Get the item height default.
	 *
	 * @return array
	 */
	protected function get_default_for_responsive_from_intval( $old_val_const, $default_int_val = 0 ) {
		$old = get_theme_mod( $this->get_id() . '_' . $old_val_const );
		if ( $old === false ) {
			return [
				'mobile'  => $default_int_val,
				'tablet'  => $default_int_val,
				'desktop' => $default_int_val,
			];
		}

		return [
			'mobile'  => $old,
			'tablet'  => $old,
			'desktop' => $old,
		];
	}

	/**
	 * Add horizontal alignment to component.
	 */
	private function add_horizontal_alignment_control() {
		if ( strpos( $this->get_id(), Search::COMPONENT_ID ) !== false ) {
			return;
		}

		if ( ! $this->has_horizontal_alignment ) {
			return;
		}

		$align_choices = [
			'left'   => [
				'tooltip' => __( 'Left', 'stax' ),
				'icon'    => 'editor-alignleft',
			],
			'center' => [
				'tooltip' => __( 'Center', 'stax' ),
				'icon'    => 'editor-aligncenter',
			],
			'right'  => [
				'tooltip' => __( 'Right', 'stax' ),
				'icon'    => 'editor-alignright',
			],
		];
		if ( strpos( $this->get_id(), Button::COMPONENT_ID ) !== false ) {
			$align_choices['justify'] = [
				'tooltip' => __( 'Justify', 'stax' ),
				'icon'    => 'editor-justify',
			];
		}

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::ALIGNMENT_ID,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'transport'             => $this->is_auto_width ? 'post' . $this->get_builder_id() : 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_alignment' ],
				'label'                 => __( 'Alignment', 'stax' ),
				'type'                  => '\Stax\Customizer\Core\Controls\React\Responsive_Radio_Buttons',
				'live_refresh_selector' => $this->is_auto_width ? null : '.builder-item--' . $this->get_id(),
				'live_refresh_css_prop' => [
					'cssVar'         => [
						'vars'       => [
							'--textAlign',
							'--justify',
						],
						'valueRemap' => [
							'--justify' => [
								'left'   => 'flex-start',
								'center' => 'center',
								'right'  => 'flex-end',
							],
						],
						'responsive' => true,
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'remove_classes' => [
						'mobile-left',
						'mobile-right',
						'mobile-center',
						'tablet-left',
						'tablet-right',
						'tablet-center',
						'desktop-left',
						'desktop-right',
						'desktop-center',
					],
					'is_for'         => 'horizontal',
				],
				'options'               => [
					'choices' => $align_choices,
				],
				'section'               => $this->section,
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);
	}

}
