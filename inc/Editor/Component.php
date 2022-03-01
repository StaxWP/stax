<?php

/**
 * Stax\Editor\Component class
 *
 * @package stax
 */

namespace Stax\Editor;

use Stax\Component_Interface;
use function add_action;
use function add_theme_support;
use function Stax\stax;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'editor';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_add_editor_support' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
	}

	public function enqueue_editor_assets() {
		wp_enqueue_style(
			'stax-root',
			get_theme_file_uri( 'assets/css/root-vars.css' ),
			[],
			stax()->get_version()
		);
	}

	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {
		add_theme_support( 'editor-styles' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style( [ 'assets/css/style-editor.css' ] );

		// Add support for default block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for wide-aligned images.
		add_theme_support( 'align-wide' );

		// Add support for color palettes.
		add_theme_support(
			'editor-color-palette',
			[
				[
					'name'  => esc_html__( 'Primary theme color', 'stax' ),
					'slug'  => 'primary',
					'color' => stax()->get_option( 'primary-color' ),
				],
				[
					'name'  => esc_html__( 'Pale pink', 'stax' ),
					'slug'  => 'pale-pink',
					'color' => '#f78da7',
				],
				[
					'name'  => esc_html__( 'Vivid red', 'stax' ),
					'slug'  => 'vivid-red',
					'color' => '#cf2e2e',
				],
				[
					'name'  => esc_html__( 'Luminous vivid orange', 'stax' ),
					'slug'  => 'luminous-vivid-orange',
					'color' => '#ff6900',
				],
				[
					'name'  => esc_html__( 'Luminous vivid amber', 'stax' ),
					'slug'  => 'luminous-vivid-amber',
					'color' => '#fcb900',
				],
				[
					'name'  => esc_html__( 'Light green cyan', 'stax' ),
					'slug'  => 'light-green-cyan',
					'color' => '#7bdcb5',
				],
				[
					'name'  => esc_html__( 'Vivid green cyan', 'stax' ),
					'slug'  => 'vivid-green-cyan',
					'color' => '#00d084',
				],
				[
					'name'  => esc_html__( 'Pale cyan blue', 'stax' ),
					'slug'  => 'pale-cyan-blue',
					'color' => '#8ed1fc',
				],
				[
					'name'  => esc_html__( 'Vivid cyan blue', 'stax' ),
					'slug'  => 'vivid-cyan-blue',
					'color' => '#0693e3',
				],
				[
					'name'  => esc_html__( 'Very light gray', 'stax' ),
					'slug'  => 'very-light-gray',
					'color' => '#eeeeee',
				],
				[
					'name'  => esc_html__( 'Cyan bluish gray', 'stax' ),
					'slug'  => 'cyan-bluish-gray',
					'color' => '#abb8c3',
				],
				[
					'name'  => esc_html__( 'Very dark gray', 'stax' ),
					'slug'  => 'very-dark-gray',
					'color' => '#313131',
				],
			]
		);

		/*
		* Optional: Disable custom colors in color palettes.
		*
		* Uncomment the line below to disable the custom color picker in the editor.
		*
		* add_theme_support( 'disable-custom-colors' );
		*/

		// Add support for font sizes.
		add_theme_support(
			'editor-font-sizes',
			[
				[
					'name'      => __( 'small', 'stax' ),
					'shortName' => __( 'S', 'stax' ),
					'size'      => 16,
					'slug'      => 'small',
				],
				[
					'name'      => __( 'regular', 'stax' ),
					'shortName' => __( 'M', 'stax' ),
					'size'      => 20,
					'slug'      => 'regular',
				],
				[
					'name'      => __( 'large', 'stax' ),
					'shortName' => __( 'L', 'stax' ),
					'size'      => 36,
					'slug'      => 'large',
				],
				[
					'name'      => __( 'larger', 'stax' ),
					'shortName' => __( 'XL', 'stax' ),
					'size'      => 48,
					'slug'      => 'larger',
				],
			]
		);

		/*
		* Optional: Disable custom font sizes in block text settings.
		*
		* Uncomment the line below to disable the custom custom font sizes in the editor.
		*
		* add_theme_support( 'disable-custom-font-sizes' );
		*/
	}
}
