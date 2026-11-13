<?php
/**
 * Stax\Builder_Integration\Component class
 *
 * Lets a plugin (Stax Builder, or any site builder) take over the theme header or footer.
 * Contract: docs/theme-integration.md.
 *
 * @package stax
 */

namespace Stax\Builder_Integration;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use WP_Customize_Manager;
use function add_action;
use function add_filter;
use function add_theme_support;
use function apply_filters;

/**
 * Header and footer ownership by a plugin.
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Contract version, bumped only on breaking changes.
	 */
	const VERSION = 1;

	/**
	 * Areas a plugin can own, mapped to the action fired where the area renders.
	 */
	const AREAS = [
		'header' => 'stax_header',
		'footer' => 'stax_footer',
	];

	/**
	 * Theme callbacks that print each area.
	 */
	const THEME_CALLBACKS = [
		'header' => 'stax_header_builder_action',
		'footer' => 'stax_footer_builder_action',
	];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'builder_integration';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_add_theme_support' ] );

		foreach ( self::AREAS as $area => $hook ) {
			add_action(
				$hook,
				function () use ( $area ) {
					$this->action_yield_area( $area );
				},
				0
			);
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'action_drop_shared_assets' ], 20 );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs.
	 */
	public function template_tags() {
		return [
			'area_owner' => [ $this, 'area_owner' ],
			'skip_link'  => [ $this, 'skip_link' ],
		];
	}

	/**
	 * Declares the contract, so a plugin can detect it with get_theme_support( 'stax-builder' ).
	 */
	public function action_add_theme_support() {
		add_theme_support(
			'stax-builder',
			[
				'version' => self::VERSION,
				'header'  => self::AREAS['header'],
				'footer'  => self::AREAS['footer'],
			]
		);
	}

	/**
	 * Who owns an area: an owner id (for example "stax-builder"), or an empty string when the theme does.
	 *
	 * @param string $area    "header" or "footer".
	 * @param string $context "render" (front end, answered for the current request) or "customizer"
	 *                        (whether the plugin manages the area at all, used to replace the theme controls).
	 *
	 * @return string
	 */
	public function area_owner( $area, $context = 'render' ) {
		if ( ! isset( self::AREAS[ $area ] ) ) {
			return '';
		}

		/**
		 * Filters who owns a theme area. Return a non-empty owner id to take the area over.
		 *
		 * @param string $owner   Owner id, empty for the theme.
		 * @param string $area    "header" or "footer".
		 * @param string $context "render" or "customizer".
		 */
		$owner = apply_filters( 'stax_theme_area_owner', '', $area, $context );

		if ( true === $owner ) {
			return 'plugin';
		}

		return is_string( $owner ) ? sanitize_key( $owner ) : '';
	}

	/**
	 * Prints the theme skip link (same markup the theme header prints).
	 */
	public function skip_link() {
		echo '<a class="skip-link screen-reader-text" href="#primary">' . esc_html__( 'Skip to content', 'stax' ) . '</a>';
	}

	/**
	 * Runs first on the area action: when a plugin owns the area, removes the theme output and,
	 * for the header, keeps the skip link as the first focusable element.
	 *
	 * @param string $area Area.
	 */
	public function action_yield_area( $area ) {
		if ( ! $this->area_owner( $area ) ) {
			return;
		}

		remove_action( self::AREAS[ $area ], self::THEME_CALLBACKS[ $area ] );

		if ( 'header' === $area ) {
			$this->skip_link();
		}
	}

	/**
	 * The header/footer builder stylesheet and script serve both areas: when a plugin owns both,
	 * the files are not loaded (the handles stay, so inline CSS and dependencies keep working).
	 */
	public function action_drop_shared_assets() {
		if ( ! $this->area_owner( 'header' ) || ! $this->area_owner( 'footer' ) ) {
			return;
		}

		$styles  = wp_styles();
		$scripts = wp_scripts();

		if ( isset( $styles->registered['stax-header'] ) ) {
			$styles->registered['stax-header']->src = false;
		}

		if ( isset( $scripts->registered['stax-hfg'] ) ) {
			$scripts->registered['stax-hfg']->src = false;
		}
	}

	/**
	 * Name of the plugin that manages an area, for the Customizer note.
	 *
	 * @param string $area  Area.
	 * @param string $owner Owner id.
	 *
	 * @return string
	 */
	public static function owner_name( $area, $owner ) {
		/**
		 * Filters the name shown in the Customizer when a plugin manages an area.
		 *
		 * @param string $name  Plugin name.
		 * @param string $area  "header" or "footer".
		 * @param string $owner Owner id.
		 */
		return (string) apply_filters( 'stax_theme_area_owner_name', __( 'Stax Builder', 'stax' ), $area, $owner );
	}

	/**
	 * Where the area is edited, for the Customizer note.
	 *
	 * @param string $area  Area.
	 * @param string $owner Owner id.
	 *
	 * @return string
	 */
	public static function owner_url( $area, $owner ) {
		/**
		 * Filters the link shown in the Customizer when a plugin manages an area.
		 *
		 * @param string $url   Admin URL.
		 * @param string $area  "header" or "footer".
		 * @param string $owner Owner id.
		 */
		return (string) apply_filters( 'stax_theme_area_owner_url', admin_url( 'admin.php?page=stax' ), $area, $owner );
	}

	/**
	 * Registers the "managed by" note in place of the builder controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer.
	 * @param string               $area         Area.
	 * @param string               $owner        Owner id.
	 * @param string               $section      Section id.
	 */
	public static function add_managed_control( WP_Customize_Manager $wp_customize, $area, $owner, $section ) {
		$wp_customize->add_control(
			new Managed_Control(
				$wp_customize,
				'stax_' . $area . '_managed_by',
				[
					'section'  => $section,
					'settings' => [],
					'area'     => $area,
					'name'     => self::owner_name( $area, $owner ),
					'url'      => self::owner_url( $area, $owner ),
				]
			)
		);
	}
}
