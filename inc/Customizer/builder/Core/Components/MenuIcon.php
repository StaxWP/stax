<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Customizer\Config;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Core\Sanitizer;

class MenuIcon extends Abstract_Component {

	const COMPONENT_ID      = 'header_menu_icon';
	const SIDEBAR_TOGGLE    = 'sidebar';
	const TEXT_ID           = 'menu_label';
	const BUTTON_APPEARANCE = 'button_appearance';
	const COMPONENT_SLUG    = 'nav-icon';
	const QUICK_LINKS_ID    = 'quick-links';

	/**
	 * Padding settings default values.
	 *
	 * @var array
	 */
	protected $default_padding_value = [
		'mobile'       => [
			'top'    => 10,
			'right'  => 15,
			'bottom' => 10,
			'left'   => 15,
		],
		'tablet'       => [
			'top'    => 10,
			'right'  => 15,
			'bottom' => 10,
			'left'   => 15,
		],
		'desktop'      => [
			'top'    => 10,
			'right'  => 15,
			'bottom' => 10,
			'left'   => 15,
		],
		'mobile-unit'  => 'px',
		'tablet-unit'  => 'px',
		'desktop-unit' => 'px',
	];

	/**
	 * Close button target CSS selector.
	 *
	 * @var string
	 */
	private $close_button = '.header-menu-sidebar .close-sidebar-panel .navbar-toggle';

	/**
	 * MenuIcon constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Menu Icon', 'stax' ) );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_SLUG' ) );
		$this->set_property( 'component_slug', self::COMPONENT_SLUG );
		$this->set_property( 'width', 1 );
		$this->set_property( 'icon', 'menu' );
		$this->set_property( 'section', self::COMPONENT_ID );
		$this->set_property( 'default_selector', '.builder-item--' . $this->get_id() . ' .navbar-toggle' );
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
				'id'                    => self::TEXT_ID,
				'group'                 => $this->get_id(),
				'transport'             => 'postMessage',
				'tab'                   => SettingsManager::TAB_GENERAL,
				'sanitize_callback'     => 'wp_filter_nohtml_kses',
				'default'               => '',
				'label'                 => __( 'Menu label', 'stax' ),
				'type'                  => 'text',
				'section'               => $this->section,
				'live_refresh_selector' => $this->default_selector . ' .nav-toggle-label',
				'live_refresh_css_prop' => [
					'parent'     => $this->default_selector,
					'wrap_class' => 'nav-toggle-label',
					'html_tag'   => 'span',
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                => self::QUICK_LINKS_ID,
				'group'             => $this->get_id(),
				'transport'         => 'postMessage',
				'tab'               => SettingsManager::TAB_GENERAL,
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				'type'              => '\Stax\Customizer\Core\Controls\React\Instructions_Control',
				'section'           => $this->section,
				'options'           => [
					'options' => [
						'quickLinks' => [
							'toggle_sidebar' => [
								'label' => esc_html__( 'Show Mobile Menu', 'stax' ),
								'icon'  => 'dashicons-nametag',
							],
							'hfg_header_layout_sidebar_layout' => [
								'label' => esc_html__( 'Mobile Menu Options', 'stax' ),
								'icon'  => 'dashicons-admin-appearance',
							],
						],
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::BUTTON_APPEARANCE,
				'group'                 => $this->get_id(),
				'transport'             => 'postMessage',
				'tab'                   => SettingsManager::TAB_STYLE,
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_button_appearance' ],
				'default'               => [
					'type'         => 'outline',
					'borderRadius' => [
						'top'    => 0,
						'left'   => 0,
						'bottom' => 0,
						'right'  => 0,
					],
				],
				'label'                 => __( 'Appearance', 'stax' ),
				'type'                  => '\Stax\Customizer\Core\Controls\React\Button_Appearance',
				'section'               => $this->section,
				'options'               => [
					'no_hover'     => true,
					'default_vals' => [
						'type'         => 'outline',
						'borderRadius' => [
							'top'    => 0,
							'left'   => 0,
							'bottom' => 0,
							'right'  => 0,
						],
					],
				],
				'live_refresh_selector' => $this->default_selector,
				'live_refresh_css_prop' => [
					'cssVar'             => [
						'vars'     => [
							'--bgColor'      => 'background',
							'--color'        => 'text',
							'--borderRadius' => [
								'key'    => 'borderRadius',
								'suffix' => 'px',
							],
							'--borderWidth'  => [
								'key'    => 'borderWidth',
								'suffix' => 'px',
							],
						],
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'additional_buttons' => $this->get_class_const( 'COMPONENT_ID' ) !== 'header_menu_icon' ? [] : [
						[
							'button' => $this->close_button,
							'text'   => '.icon-bar',
						],
					],
				],
				'conditional_header'    => true,
			]
		);
	}

	/**
	 * Add CSS style for the component.
	 *
	 * @param array $css_array the css style array.
	 *
	 * @return array
	 */
	public function add_style( array $css_array = [] ) {
		$id = $this->get_id() . '_' . self::BUTTON_APPEARANCE;

		$rules = [
			'--bgColor'      => $id . '.background',
			'--color'        => $id . '.text',
			'--borderRadius' => [
				Dynamic_Selector::META_KEY => $id . '.borderRadius',
				'directional-prop'         => Config::CSS_PROP_BORDER_RADIUS,
			],
			'--borderWidth'  => [
				Dynamic_Selector::META_KEY => $id . '.borderWidth',
				'directional-prop'         => Config::CSS_PROP_BORDER_WIDTH,
			],
		];

		$value = SettingsManager::get_instance()->get( $id );

		if ( isset( $value['type'] ) && 'outline' !== $value['type'] ) {
			$rules ['--borderWidth']['override'] = 0;
		}

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id() . ',' . $this->close_button,
			Dynamic_Selector::KEY_RULES    => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * Render method.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-menu-icon' );
	}

}
