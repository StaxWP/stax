<?php
/**
 * Stax\Editor\Component class
 *
 * Block editor integration: theme.json data from the Customizer, editor styles that match
 * the front end inside the editor iframe, block styles and block patterns.
 *
 * @package stax
 */

namespace Stax\Editor;

use Stax\Component_Interface;
use Stax\Customizer\Config;
use Stax\Customizer\Dynamic_Css;
use function add_action;
use function add_theme_support;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Customizer palette variables mapped to theme.json palette slugs.
	 */
	const PALETTE_SLUGS = [
		'primary'    => Config::VAR_PRIMARY_COLOR,
		'text'       => Config::VAR_TEXT_COLOR,
		'heading'    => Config::VAR_HEADING_COLOR,
		'background' => Config::VAR_BODY_BG,
		'light'      => Config::VAR_LIGHT_COLOR,
		'dark'       => Config::VAR_DARK_COLOR,
		'link'       => Config::VAR_LINK_COLOR,
		'border'     => Config::VAR_BORDER_COLOR,
		'meta'       => Config::VAR_TEXT_META_COLOR,
	];

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
		add_filter( 'wp_theme_json_data_theme', [ $this, 'filter_theme_json' ] );
		add_filter( 'block_editor_settings_all', [ $this, 'filter_editor_settings' ] );
		add_action( 'init', [ $this, 'action_register_block_styles' ] );
		add_action( 'init', [ $this, 'action_register_block_patterns' ] );
	}

	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {
		add_theme_support( 'editor-styles' );

		// Loaded inside the editor iframe and scoped to it by WordPress.
		add_editor_style( [ 'assets/css/root-vars.css', 'assets/css/style-editor.min.css' ] );

		// Add support for default block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for wide-aligned images.
		add_theme_support( 'align-wide' );

		add_theme_support( 'custom-spacing' );
	}

	/**
	 * Puts the Customizer colors and fonts into theme.json, so the editor palette, the
	 * has-*-color classes and the font family presets always match the Customizer.
	 *
	 * @param \WP_Theme_JSON_Data $theme_json Theme JSON data.
	 *
	 * @return \WP_Theme_JSON_Data
	 */
	public function filter_theme_json( $theme_json ) {
		$data    = $theme_json->get_data();
		$palette = $data['settings']['color']['palette']['theme'] ?? ( $data['settings']['color']['palette'] ?? [] );
		$colors  = self::get_customizer_colors();

		foreach ( $palette as $index => $entry ) {
			if ( isset( $entry['slug'], $colors[ $entry['slug'] ] ) ) {
				$palette[ $index ]['color'] = $colors[ $entry['slug'] ];
			}
		}

		$families = $data['settings']['typography']['fontFamilies']['theme'] ?? ( $data['settings']['typography']['fontFamilies'] ?? [] );

		foreach ( $families as $index => $family ) {
			if ( 'body' === ( $family['slug'] ?? '' ) ) {
				$families[ $index ]['fontFamily'] = (string) get_theme_mod( Config::MODS_FONT_GENERAL, 'Mukta, sans-serif' );
			} elseif ( 'heading' === ( $family['slug'] ?? '' ) ) {
				$families[ $index ]['fontFamily'] = (string) get_theme_mod( Config::MODS_FONT_HEADINGS, 'Heebo, sans-serif' );
			}
		}

		return $theme_json->update_with(
			[
				'version'  => 3,
				'settings' => [
					'color'      => [ 'palette' => $palette ],
					'typography' => [ 'fontFamilies' => $families ],
				],
			]
		);
	}

	/**
	 * Hex colors of the active Customizer palette, keyed by theme.json slug.
	 *
	 * @return array
	 */
	public static function get_customizer_colors() {
		$global = get_theme_mod( Config::MODS_GLOBAL_COLORS, \Stax_Assets::instance()->get_global_colors_default() );
		$active = $global['activePalette'] ?? 'light';
		$items  = $global['palettes'][ $active ]['colors'] ?? [];
		$colors = [];

		foreach ( $items as $item ) {
			$slug = array_search( $item['vars'] ?? null, self::PALETTE_SLUGS, true );
			$hex  = self::to_hex( $item['color'] ?? '' );

			if ( false !== $slug && $hex ) {
				$colors[ $slug ] = $hex;
			}
		}

		return $colors;
	}

	/**
	 * Design tokens saved in the site's global styles (the user layer of theme.json, what the Site Editor
	 * or a plugin such as Stax Builder writes to `wp_global_styles`) for the slugs the theme knows.
	 *
	 * Precedence: global styles > Customizer > theme defaults. theme.json already merges the user layer
	 * over the theme layer (which this class feeds from the Customizer); these values are also applied
	 * to the theme's own CSS variables (Stax\Customizer\Dynamic_Css), so the whole theme follows them.
	 *
	 * @return array {
	 *     @type string[] $colors   Hex colors keyed by palette slug (see PALETTE_SLUGS).
	 *     @type string[] $fonts    Font family stacks keyed by "body" / "heading".
	 *     @type string   $wideSize Layout wide size, or an empty string.
	 * }
	 */
	public static function get_global_styles_tokens() {
		static $tokens = null;

		if ( null !== $tokens && ! is_customize_preview() ) {
			return $tokens;
		}

		$tokens = [
			'colors'   => [],
			'fonts'    => [],
			'wideSize' => '',
		];

		if ( ! class_exists( '\WP_Theme_JSON_Resolver' ) || ! wp_theme_has_theme_json() ) {
			return $tokens;
		}

		$settings = \WP_Theme_JSON_Resolver::get_user_data()->get_settings();

		// User palettes: "theme" (the theme palette edited) then "custom" (added colors) win in that order.
		foreach ( [ 'theme', 'custom' ] as $origin ) {
			foreach ( (array) ( $settings['color']['palette'][ $origin ] ?? [] ) as $entry ) {
				$hex = self::to_hex( $entry['color'] ?? '' );

				if ( isset( $entry['slug'], self::PALETTE_SLUGS[ $entry['slug'] ] ) && $hex ) {
					$tokens['colors'][ $entry['slug'] ] = $hex;
				}
			}

			foreach ( (array) ( $settings['typography']['fontFamilies'][ $origin ] ?? [] ) as $entry ) {
				$family = trim( preg_replace( '/[^\w\s\-,\'"]/u', '', (string) ( $entry['fontFamily'] ?? '' ) ) );

				if ( in_array( $entry['slug'] ?? '', [ 'body', 'heading' ], true ) && '' !== $family ) {
					$tokens['fonts'][ $entry['slug'] ] = $family;
				}
			}
		}

		$wide = (string) ( $settings['layout']['wideSize'] ?? '' );

		if ( preg_match( '/^[\w.%()+\-*\/ ,]+$/', $wide ) ) {
			$tokens['wideSize'] = $wide;
		}

		return $tokens;
	}

	/**
	 * Normalizes #rgb, #rrggbb and rgb()/rgba() colors to #rrggbb.
	 *
	 * @param string $color Color value.
	 *
	 * @return string Hex color, or an empty string.
	 */
	public static function to_hex( $color ) {
		$color = trim( (string) $color );

		if ( preg_match( '/^#([0-9a-f]{3}){1,2}$/i', $color ) ) {
			return strtolower( $color );
		}

		if ( preg_match( '/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $color, $m ) ) {
			return sprintf( '#%02x%02x%02x', min( 255, (int) $m[1] ), min( 255, (int) $m[2] ), min( 255, (int) $m[3] ) );
		}

		return '';
	}

	/**
	 * Adds the Customizer CSS (colors, fonts, sizes) to the editor iframe.
	 *
	 * @param array $settings Editor settings.
	 *
	 * @return array
	 */
	public function filter_editor_settings( $settings ) {
		if ( class_exists( Dynamic_Css::class ) ) {
			$settings['styles'][] = [
				'css' => ( new Dynamic_Css() )->get_style(),
			];
		}

		return $settings;
	}

	/**
	 * Block styles: small, opt-in variations that fit the theme design.
	 */
	public function action_register_block_styles() {
		$styles = [
			'core/button'  => [
				'stax-pill' => __( 'Pill', 'stax' ),
			],
			'core/image'   => [
				'stax-shadow' => __( 'Shadow', 'stax' ),
			],
			'core/group'   => [
				'stax-card' => __( 'Card', 'stax' ),
			],
			'core/list'    => [
				'stax-checklist' => __( 'Checklist', 'stax' ),
			],
			'core/heading' => [
				'stax-eyebrow' => __( 'Eyebrow', 'stax' ),
			],
		];

		foreach ( $styles as $block => $variations ) {
			foreach ( $variations as $name => $label ) {
				register_block_style(
					$block,
					[
						'name'  => $name,
						'label' => $label,
					]
				);
			}
		}
	}

	/**
	 * Registers the theme's block patterns (files in /patterns are registered by core too;
	 * this adds the category).
	 */
	public function action_register_block_patterns() {
		register_block_pattern_category(
			'stax',
			[
				'label'       => __( 'Stax', 'stax' ),
				'description' => __( 'Sections designed for the Stax theme.', 'stax' ),
			]
		);
	}
}
