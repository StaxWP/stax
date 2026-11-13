<?php
/**
 * Stax\Upgrades\Component class
 *
 * Runs one-time migrations when the theme is updated. Each migration runs once per site
 * and is recorded in the `stax-upgrades` option (which 1.x already wrote on every site).
 *
 * @package stax
 */

namespace Stax\Upgrades;

use Stax\Component_Interface;
use Stax\Customizer\Config;
use Stax\Style_Switch\Migration;

/**
 * Class Component
 *
 * @package Stax\Upgrades
 */
class Component implements Component_Interface {

	/**
	 * Option holding the migrations that already ran, as version => true.
	 */
	const OPTION = 'stax-upgrades';

	/**
	 * Customizer defaults that changed in 2.0 (for WCAG AA contrast), with their 1.x values.
	 */
	const CHANGED_DEFAULTS_2_0 = [
		'primary-menu_active_color'            => '#1aaaa0',
		'primary-menu_hover_color'             => '#1aaaa0',
		'header_search_responsive_hover_color' => '#1aaaa0',
	];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'upgrades';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'init', [ $this, 'do_upgrades' ] );
	}

	/**
	 * Migrations, as version => method. A site that never ran Stax gets none of them.
	 *
	 * @return array
	 */
	protected function get_upgrades() {
		return [
			'2.0.0' => 'upgrade_2_0_0',
		];
	}

	/**
	 * Runs the migrations this site has not run yet.
	 */
	public function do_upgrades() {
		$done = get_option( self::OPTION, null );

		// 1.x recorded its version here on every request, so an existing value means an update.
		$is_update = is_array( $done ) && ! empty( $done );
		$done      = is_array( $done ) ? $done : [];
		$changed   = false;

		foreach ( $this->get_upgrades() as $version => $method ) {
			if ( ! empty( $done[ $version ] ) ) {
				continue;
			}

			if ( $is_update ) {
				$this->$method();
			}

			$done[ $version ] = true;
			$changed          = true;
		}

		if ( $changed ) {
			update_option( self::OPTION, $done );
		}
	}

	/**
	 * 2.0.0: keep the 1.x look on updated sites (stylesheet and changed color defaults) and remove
	 * what the Appsero tracker stored.
	 */
	protected function upgrade_2_0_0() {
		// Keep the full 1.x stylesheet so nothing changes visually; the admin switches when they want
		// (notice, Stax Panel section, Customizer section; see Stax\Style_Switch).
		if ( null === get_theme_mod( Config::OPTION_PERF_LEGACY_CSS, null ) ) {
			set_theme_mod( Config::OPTION_PERF_LEGACY_CSS, true );
			Migration::mark_offered();
		}

		// Defaults changed in 2.0: store the 1.x values where the site relied on the default.
		if ( null === get_theme_mod( Config::MODS_GLOBAL_COLORS, null ) ) {
			set_theme_mod( Config::MODS_GLOBAL_COLORS, \Stax_Assets::instance()->get_global_colors_default( '1.x' ) );
		}

		foreach ( self::CHANGED_DEFAULTS_2_0 as $mod => $old_value ) {
			if ( null === get_theme_mod( $mod, null ) ) {
				set_theme_mod( $mod, $old_value );
			}
		}

		// The Appsero usage tracker (bundled until 1.2.2) used the theme folder name as its prefix.
		$slug = get_template();

		foreach ( [ '_tracking_last_send', '_allow_tracking', '_tracking_notice', '_tracking_skipped' ] as $suffix ) {
			delete_option( $slug . $suffix );
		}

		wp_clear_scheduled_hook( $slug . '_tracker_send_event' );
	}
}
