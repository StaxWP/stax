<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Core\Sanitizer;

class Copyright extends Abstract_Component {

	const COMPONENT_ID = 'footer_copyright';
	const CONTENT_ID   = 'content';
	const COLOR_ID     = 'color';

	/**
	 * Copyright constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Copyright', 'stax' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 2 );
		$this->set_property( 'icon', 'nametag' );
		$this->set_property( 'has_typeface_control', true );
		$this->set_property( 'default_typography_selector', $this->default_typography_selector . '.builder-item--' . $this->get_id() );
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
				'id'                 => self::CONTENT_ID,
				'group'              => self::COMPONENT_ID,
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . self::COMPONENT_ID,
				'sanitize_callback'  => 'wp_kses_post',
				'default'            => get_theme_mod(
					'stax_footer_text',
					apply_filters(
						'stax_copyright',
						sprintf(
							esc_html__( '%1$s | Made with ❤️ by %2$s', 'stax' ),
							wp_kses_post( '<p><a href="https://staxwp.com/stax-theme/" rel="nofollow">Stax Theme</a>' ),
							wp_kses_post( '<a href="https://staxwp.com/" rel="nofollow">StaxWP</a></p>' )
						)
					)
				),
				'label'              => __( 'Copyright', 'stax' ),
				'type'               => 'textarea',
				'section'            => $this->section,
				'use_dynamic_fields' => [ 'url', 'string' ],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::COLOR_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_colors' ],
				'label'                 => __( 'Text Color', 'stax' ),
				'type'                  => 'stax_color_control',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--color',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					[
						'selector' => $this->default_typography_selector . ', ' . $this->default_typography_selector . ' *:not(a)',
						'prop'     => 'color',
						'fallback' => 'inherit',
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
		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id(),
			Dynamic_Selector::KEY_RULES    => [
				'--color' => [
					Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::COLOR_ID,
					Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::COLOR_ID ),
				],
			],
		];

		return parent::add_style( $css_array );
	}

	/**
	 * Render method for component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-copyright' );
	}
}
