<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Core\Sanitizer;

class Nav extends Abstract_Component {

	const COMPONENT_ID             = 'primary-menu';
	const STYLE_ID                 = 'style';
	const COLOR_ID                 = 'color';
	const HOVER_COLOR_ID           = 'hover_color';
	const ACTIVE_COLOR_ID          = 'active_color';
	const LAST_ITEM_ID             = 'stax_last_menu_item';
	const NAV_MENU_ID              = 'stx-primary-navigation';
	const ITEM_HEIGHT              = 'item_height';
	const SPACING                  = 'spacing';
	const EXPAND_DROPDOWNS         = 'expand_dropdowns';
	const DROPDOWNS_EXPANDED_CLASS = 'dropdowns-expanded';

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
			'mobile'  => 14,
			'tablet'  => 14,
			'desktop' => 14,
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
	 * Nav constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Primary Menu', 'stax' ) );
		$this->set_property( 'component_slug', 'hfg-primary-menu' );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 6 );
		$this->set_property( 'icon', 'tagcloud' );
		$this->set_property( 'section', 'header_menu_primary' );
		$this->set_property( 'has_font_family_control', true );
		$this->set_property( 'has_typeface_control', true );
		$this->set_property( 'default_typography_selector', $this->default_typography_selector . '.builder-item--' . $this->get_id() );
		$this->default_align = 'right';

		add_filter(
			'stax_last_menu_setting_slug_' . $this->get_class_const( 'COMPONENT_ID' ),
			[
				$this,
				'filter_stax_last_menu_setting_slug',
			]
		);
	}

	/**
	 * Filter the setting slug for last menu.
	 *
	 * @param string $slug The setting slug.
	 *
	 * @return string
	 * @since   2.3.7
	 * @access  public
	 */
	public function filter_stax_last_menu_setting_slug( $slug ) {
		if ( $slug !== $this->get_class_const( 'LAST_ITEM_ID' ) ) {
			return $this->get_class_const( 'LAST_ITEM_ID' );
		}

		return $slug;
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {
		SettingsManager::get_instance()->add(
			[
				'id'                 => self::STYLE_ID,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'default'            => 'style-plain',
				'conditional_header' => true,
				'label'              => __( 'Hover Skin Mode', 'stax' ),
				'type'               => '\Stax\Customizer\Core\Controls\React\Radio_Buttons',
				'section'            => $this->section,
				'options'            => [
					'large_buttons' => true,
					'is_for'        => 'menu',
				],
			]
		);

		$selector = '.builder-item--' . $this->get_id() . ' .nav-menu-primary > .nav-ul ';

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::COLOR_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_colors' ],
				'default'               => '',
				'label'                 => __( 'Items Color', 'stax' ),
				'type'                  => 'stax_color_control',
				'section'               => $this->section,
				'conditional_header'    => true,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--color',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'template' =>
						$selector . ' li:not(.current_page_item):not(.current-menu-item):not(.woocommerce-mini-cart-item) > a,' . $selector . ' li.stax-mm-heading span {
						color: {{value}};
					}',
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::ACTIVE_COLOR_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_colors' ],
				'default'               => '#000000',
				'label'                 => __( 'Active Item Color', 'stax' ),
				'type'                  => 'stax_color_control',
				'section'               => $this->section,
				'conditional_header'    => true,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--activeColor',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'template' =>
						$selector . ' li.current_page_item > a,' . $selector . ' li.current-menu-item > a {
						color: {{value}} !important;
					}',
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::HOVER_COLOR_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_colors' ],
				'default'               => '#000000',
				'label'                 => __( 'Items Hover Color', 'stax' ),
				'type'                  => 'stax_color_control',
				'section'               => $this->section,
				'conditional_header'    => true,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--hoverColor',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'template' =>
						'.builder-item--' . $this->get_id() . ' .nav-menu-primary:not(.style-full-height) > .nav-ul li:not(.woocommerce-mini-cart-item):hover > a {
							 color: {{value}} !important;
						}' .
						$selector . ' li:not(.woocommerce-mini-cart-item) > a:after,' . $selector . ' li > .has-caret > a:after {
							background-color: {{value}} !important;
						}',
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                => 'shortcut',
				'group'             => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'esc_attr',
				'type'              => '\Stax\Customizer\Core\Controls\Button',
				'options'           => [
					'button_text'  => __( 'Primary Menu', 'stax' ),
					'button_class' => 'stx-top-bar-menu-shortcut',
					'icon_class'   => 'menu',
					'shortcut'     => true,
				],
				'section'           => $this->section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SPACING,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_LAYOUT,
				'section'            => $this->section,
				'label'              => __( 'Items Spacing (px)', 'stax' ),
				'type'               => 'Stax\Customizer\Core\Controls\React\Responsive_Range',
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => [ $this, 'sanitize_responsive_int_json' ],
				'default'            => $this->get_default_for_responsive_from_intval( self::SPACING ),
				'options'            => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => '20',
							'tablet'  => '20',
							'desktop' => '20',
						],
					],
				],
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::ITEM_HEIGHT,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_LAYOUT,
				'label'              => __( 'Items Min Height (px)', 'stax' ),
				'sanitize_callback'  => [ $this, 'sanitize_responsive_int_json' ],
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'default'            => $this->get_default_for_responsive_from_intval( self::ITEM_HEIGHT ),
				'type'               => 'Stax\Customizer\Core\Controls\React\Responsive_Range',
				'options'            => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => '25',
							'tablet'  => '25',
							'desktop' => '25',
						],
					],
				],
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::EXPAND_DROPDOWNS,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'absint',
				'default'            => 0,
				'label'              => __( 'Expand first level of dropdowns when menu is in mobile menu content.', 'stax' ),
				'type'               => 'stax_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);
	}

	/**
	 * Sanitize last menu item.
	 *
	 * @param string $value Json value of the control.
	 *
	 * @return string
	 */
	public function sanitize_last_menu_item( $value ) {
		$allowed = [
			'cart',
			'search',
			'wish_list',
		];

		if ( empty( $value ) ) {
			return wp_json_encode( $allowed );
		}

		$decoded = json_decode( $value, true );

		foreach ( $decoded as $val ) {
			if ( ! in_array( $val, $allowed, true ) ) {
				return wp_json_encode( $allowed );
			}
		}

		return $value;
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-nav' );
	}

	/**
	 * Add styles to the component.
	 *
	 * @param array $css_array rules array.
	 *
	 * @return array
	 */
	public function add_style( array $css_array = [] ) {
		$selector = '.builder-item--' . $this->get_id();

		$rules = [
			'--color'       => [
				Dynamic_Selector::META_KEY => $this->get_id() . '_' . self::COLOR_ID,
			],
			'--hoverColor'  => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::HOVER_COLOR_ID,
				Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::HOVER_COLOR_ID ),
			],
			'--activeColor' => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::ACTIVE_COLOR_ID,
				Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::ACTIVE_COLOR_ID ),
			],
			'--spacing'     => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::SPACING,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_SUFFIX        => 'px',
				Dynamic_Selector::META_DEFAULT       => $this->get_default_for_responsive_from_intval( self::SPACING, 20 ),
			],
			'--height'      => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::ITEM_HEIGHT,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_SUFFIX        => 'px',
				Dynamic_Selector::META_DEFAULT       => $this->get_default_for_responsive_from_intval( self::ITEM_HEIGHT, 25 ),
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => $selector,
			Dynamic_Selector::KEY_RULES    => $rules,
		];

		return parent::add_style( $css_array );
	}
}
