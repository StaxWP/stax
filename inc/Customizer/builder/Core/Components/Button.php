<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Customizer\Config;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Core\Sanitizer;

class Button extends Abstract_Component {

	const COMPONENT_ID = 'button_base';
	const LINK_ID      = 'link_setting';
	const TEXT_ID      = 'text_setting';
	const STYLE_ID     = 'style_setting';

	/**
	 * Default spacing value
	 *
	 * @var array
	 */
	protected $default_padding_value = [
		'mobile'       => [
			'top'    => '12',
			'right'  => '12',
			'bottom' => '12',
			'left'   => '12',
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
	 * Button constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		parent::__construct( $panel );

		$this->default_selector = '.builder-item--' . $this->get_id();
	}

	/**
	 * Button constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Button', 'stax' ) );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'component_slug', 'hfg-button' );
		$this->set_property( 'width', 2 );
		$this->set_property( 'section', 'header_button' );
		$this->set_property( 'icon', 'admin-links' );
		$this->set_property( 'is_auto_width', true );
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
				'id'                 => self::LINK_ID,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'default'            => '#',
				'label'              => __( 'Link', 'stax' ),
				'type'               => 'text',
				'section'            => $this->section,
				'use_dynamic_fields' => [ 'url' ],
				'conditional_header' => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::TEXT_ID,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'default'            => __( 'Button', 'stax' ),
				'label'              => __( 'Text', 'stax' ),
				'type'               => 'text',
				'section'            => $this->section,
				'use_dynamic_fields' => [ 'string' ],
				'conditional_header' => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::STYLE_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_button_appearance' ],
				'label'                 => __( 'Appearance', 'stax' ),
				'type'                  => 'stax_button_appearance',
				'section'               => $this->section,
				'conditional_header'    => $this->get_builder_id() === 'header',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => [
							'--primaryBtnBg'           => 'background',
							'--primaryBtnColor'        => 'text',
							'--primaryBtnHoverBg'      => 'backgroundHover',
							'--primaryBtnHoverColor'   => 'textHover',
							'--primaryBtnBorderRadius' => [
								'key'    => 'borderRadius',
								'suffix' => 'px',
							],
							'--primaryBtnBorderWidth'  => [
								'key'    => 'borderWidth',
								'suffix' => 'px',
							],
						],
						'selector' => '.builder-item--' . $this->get_id(),
					],
				],
			]
		);
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
		$id    = $this->get_id() . '_' . self::STYLE_ID;
		$value = get_theme_mod( $id );

		$rules = [
			'--primaryBtnBg'           => [ Dynamic_Selector::META_KEY => $id . '.background' ],
			'--primaryBtnColor'        => [ Dynamic_Selector::META_KEY => $id . '.text' ],
			'--primaryBtnHoverBg'      => [ Dynamic_Selector::META_KEY => $id . '.backgroundHover' ],
			'--primaryBtnHoverColor'   => [ Dynamic_Selector::META_KEY => $id . '.textHover' ],
			'--primaryBtnBorderRadius' => [
				Dynamic_Selector::META_KEY => $id . '.borderRadius',
				'directional-prop'         => Config::CSS_PROP_BORDER_RADIUS,
			],
		];

		if ( isset( $value['type'] ) && 'outline' === $value['type'] ) {
			$rules['--primaryBtnBorderWidth'] = [
				Dynamic_Selector::META_KEY => $id . '.borderWidth',
				'directional-prop'         => Config::CSS_PROP_BORDER_WIDTH,
			];
		}

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => $this->default_selector,
			Dynamic_Selector::KEY_RULES    => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-button' );
	}

}
