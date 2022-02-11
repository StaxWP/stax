<?php

namespace Stax\Builder\Core\Builder;

use Stax\Builder\Main;
use Stax\Customizer\Core\Controls\React\Presets_Selector;
use WP_Customize_Manager;

class Header extends Abstract_Builder {

	/**
	 * Builder name.
	 */
	const BUILDER_NAME = 'header';

	/**
	 * Header init.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'title', __( 'Header', 'stax' ) );
		$this->set_property(
			'instructions_array',
			[
				'description'     => __( 'Build your own header or choose from preset options.', 'stax' ),
				'quickLinks'      => [
					'custom_logo'                       => [
						'label' => esc_html__( 'Change Logo', 'stax' ),
						'icon'  => 'dashicons-editor-customchar',
					],
					'hfg_header_layout_main_background' => [
						'label' => esc_html__( 'Change Header Color', 'stax' ),
						'icon'  => 'dashicons-admin-appearance',
					],
					'primary-menu_shortcut'             => [
						'label' => esc_html__( 'Change Menu', 'stax' ),
						'icon'  => 'dashicons-menu',
					],
				],
				'builderMigrated' => true,
				'hadOldBuilder'   => false,
			]
		);
	}

	/**
	 * Called to register component controls.
	 *
	 * @param WP_Customize_Manager $wp_customize The Customize Manager.
	 *
	 * @return WP_Customize_Manager
	 * @since   1.0.0
	 * @access  public
	 */
	public function customize_register( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'stax_header_presets',
			[
				'title'    => __( 'Header Presets', 'stax' ),
				'priority' => 200,
				'panel'    => 'hfg_header',
			]
		);

		$wp_customize->add_setting(
			'hfg_stax_header_presets',
			[
				'sanitize_callback' => 'sanitize_text_field',
				'label'             => __( 'Header Presets', 'stax' ),
			]
		);

		$wp_customize->add_control(
			new Presets_Selector(
				$wp_customize,
				'hfg_stax_header_presets',
				[
					'section'   => 'stax_header_presets',
					'transport' => 'postMessage',
					'priority'  => 30,
					'presets'   => $this->get_header_presets(),
					'builder'   => $this->layout_control_id,
				]
			)
		);

		return parent::customize_register( $wp_customize );
	}


	/**
	 * Method called via hook.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function load_template() {
		Main::get_instance()->load( 'header-wrapper' );
	}

	/**
	 * Get builder id.
	 *
	 * @return string Builder id.
	 */
	public function get_id() {
		return self::BUILDER_NAME;
	}

	/**
	 * Render builder row.
	 *
	 * @param string $device_id   The device id.
	 * @param string $row_id      The row id.
	 * @param array  $row_details Row data.
	 */
	public function render_row( $device_id, $row_id, $row_details ) {
		$name = $row_id;

		if ( 'sidebar' === $row_id && 'mobile' === $device_id ) {
			$name = 'mobile';
		}

		Main::get_instance()->load( 'row-wrapper', $name );
	}


	/**
	 * Return  the builder rows.
	 *
	 * @return array
	 * @since   1.0.0
	 * @updated 1.0.1
	 * @access  protected
	 */
	protected function get_rows() {
		return [
			'top'     => [
				'title'       => esc_html__( 'Header Top', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
			'main'    => [
				'title'       => esc_html__( 'Header Main', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
			'bottom'  => [
				'title'       => esc_html__( 'Header Bottom', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
			'sidebar' => [
				'title'       => esc_html__( 'Mobile Sidebar', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
		];
	}


	/**
	 * Get the header presets.
	 *
	 * @return array
	 */
	private function get_header_presets() {
		$assets_url = trailingslashit( get_template_directory_uri() ) . 'inc/Customizer/builder/assets/';

		return apply_filters(
			'stax_header_presets',
			[
				[
					'label' => 'Classic',
					'image' => $assets_url . 'images/header-presets/Classic.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"primary-menu\"},{\"id\":\"header_search_responsive\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"nav-icon\"},{\"id\":\"header_search_responsive\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}"}',
				],
				[
					'label' => 'Inverted',
					'image' => $assets_url . 'images/header-presets/Inverted.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"primary-menu\"},{\"id\":\"header_search_responsive\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"logo\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"nav-icon\"},{\"id\":\"header_search_responsive\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"logo\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}","logo_component_align":{"mobile":"right","tablet":"right","desktop":"right"}}',
				],
				[
					'label' => 'Centered',
					'image' => $assets_url . 'images/header-presets/Centered.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[],\"c-left\":[],\"center\":[{\"id\":\"logo\"}],\"c-right\":[],\"right\":[]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[{\"id\":\"primary-menu\"}],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[],\"c-left\":[],\"center\":[{\"id\":\"logo\"}],\"c-right\":[],\"right\":[]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[{\"id\":\"nav-icon\"}],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}","logo_component_align":{"mobile":"center","tablet":"center","desktop":"center"}}',
				],
				[
					'label' => 'Spaced',
					'image' => $assets_url . 'images/header-presets/Spaced.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"header_search_responsive\"}],\"c-left\":[],\"center\":[{\"id\":\"logo\"}],\"c-right\":[],\"right\":[{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"header_search_responsive\"}],\"c-left\":[],\"center\":[{\"id\":\"logo\"}],\"c-right\":[],\"right\":[{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}","logo_component_align":{"mobile":"center","tablet":"center","desktop":"center"}}',
				],
				[
					'label' => 'Collapsed',
					'image' => $assets_url . 'images/header-presets/ClassicCollapsed.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search_responsive\"},{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search_responsive\"},{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}"}',
				],
				[
					'label' => 'Search Field',
					'image' => $assets_url . 'images/header-presets/SearchField.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"},{\"id\":\"primary-menu\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"},{\"id\":\"nav-icon\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search_responsive\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}"}',
				],
				[
					'label' => 'Button Item',
					'image' => $assets_url . 'images/header-presets/ButtonItem.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"primary-menu\"},{\"id\":\"button_base\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"},{\"id\":\"button_base\"}]}}"}',
				],
			]
		);
	}
}
