<?php

namespace Stax\Builder\Core;

use Stax\Builder\Core\Builder\Abstract_Builder;
use Stax\Builder\Core\Interfaces\Builder;
use Stax\Builder\Core\Settings\Config;
use Stax\Builder\Main;
use Stax\Builder\Traits\Core;
use WP_Customize_Manager;

class Customizer {
	use Core;

	/**
	 * Holds the builders to register.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var array $builders
	 */
	private $builders = [];

	/**
	 * Customizer constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct() {
		$theme_support = get_theme_support( 'hfg_support' );
		$theme_support = apply_filters( 'hfg_theme_support_filter', $theme_support );

		if ( empty( $theme_support ) ) {
			return;
		}

		$theme_support = reset( $theme_support );
		$theme_support = apply_filters( 'hfg_support_components_filter', $theme_support );

		foreach ( $theme_support['builders'] as $builder => $components ) {
			if ( class_exists( $builder ) && in_array( 'Stax\Builder\Core\Interfaces\Builder', class_implements( $builder ), true ) ) {
				/**
				 * A new builder instance.
				 *
				 * @var Abstract_Builder $new_builder
				 */
				$new_builder = new $builder();

				foreach ( $components as $component ) {
					$new_builder->register_component( $component );
				}

				$this->builders[ $new_builder->get_id() ] = $new_builder;
			}
		}

		if ( is_admin() || is_customize_preview() ) {
			add_action( 'customize_register', [ $this, 'register' ] );
			add_action( 'customize_preview_init', [ $this, 'preview_js' ] );
		}

		add_filter( 'body_class', [ $this, 'hfg_body_classes' ] );
		add_filter( 'stax_react_controls_localization', [ $this, 'add_dynamic_tags_options' ] );
	}

	/**
	 * Add the dynamic tags options.
	 *
	 * @param array $array the localized array.
	 *
	 * @return mixed
	 */
	public function add_dynamic_tags_options( $array ) {
		$array['HFG']                    = $this->get_builders_data();
		$array['dynamicTags']['options'] = Magic_Tags::get_instance()->get_options();

		return $array;
	}

	/**
	 * Classes for the body tag.
	 *
	 * @param array $classes List of body classes.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function hfg_body_classes( $classes ) {
		if ( is_customize_preview() ) {
			$classes[] = 'customize-previewing';
		}

		$sidebar_class = 'menu_sidebar_' . get_theme_mod( 'hfg_header_layout_sidebar_layout', 'slide_left' );

		$classes[] = $sidebar_class;

		return $classes;
	}

	/**
	 * Returns list of builders.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function get_builders_data() {
		$builders_list = [];

		/**
		 * A Builder Class instance.
		 *
		 * @var Builder $builder
		 */
		foreach ( $this->builders as $key => $builder ) {
			$builders_list[ $key ] = $builder->get_builder();
		}

		return $builders_list;
	}

	/**
	 * Return builder object or whole list.
	 *
	 * @param string $name Builder id.
	 *
	 * @return Abstract_Builder[]|Abstract_Builder Builder object or list.
	 */
	public function get_builders( $name = '' ) {
		if ( isset( $this->builders[ $name ] ) ) {
			return $this->builders[ $name ];
		}

		return $this->builders;
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function preview_js() {
		if ( ! is_customize_preview() ) {
			return;
		}

		wp_enqueue_script(
			'hfg-customizer',
			esc_url( Config::get_url() ) . '/assets/js/customizer/customizer.min.js',
			[
				'customize-preview',
				'customize-selective-refresh',
			],
			Main::VERSION,
			true
		);

	}

	/**
	 * Register builder controls and settings.
	 *
	 * @param WP_Customize_Manager $wp_customize The Customize Manager.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function register( WP_Customize_Manager $wp_customize ) {
		/**
		 * A Builder Class instance.
		 *
		 * @var Builder $builder
		 */
		foreach ( $this->builders as $builder ) {
			$builder->customize_register( $wp_customize );
		}

		$wp_customize->register_section_type( '\Stax\Customizer\Core\Controls\React\Instructions_Section' );
	}

}
