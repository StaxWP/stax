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
	 * Option set when the "updated to 2.0" notice should be shown.
	 */
	const NOTICE_OPTION = 'stax_show_2_0_notice';

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
		add_action( 'admin_init', [ $this, 'maybe_dismiss_notice' ] );
		add_action( 'admin_notices', [ $this, 'admin_notice_2_0' ] );
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
	 * 2.0.0: keep the 1.x look on updated sites and remove what the Appsero tracker stored.
	 */
	protected function upgrade_2_0_0() {
		// Keep the full 1.x stylesheet so nothing changes visually; the admin can switch to the lean one.
		if ( null === get_theme_mod( Config::OPTION_PERF_LEGACY_CSS, null ) ) {
			set_theme_mod( Config::OPTION_PERF_LEGACY_CSS, true );
			update_option( self::NOTICE_OPTION, 1, false );
		}

		// The Appsero usage tracker (bundled until 1.2.2) used the theme folder name as its prefix.
		$slug = get_template();

		foreach ( [ '_tracking_last_send', '_allow_tracking', '_tracking_notice', '_tracking_skipped' ] as $suffix ) {
			delete_option( $slug . $suffix );
		}

		wp_clear_scheduled_hook( $slug . '_tracker_send_event' );
	}

	/**
	 * Dismisses the 2.0 notice (link with a nonce).
	 */
	public function maybe_dismiss_notice() {
		if ( ! isset( $_GET['stax-dismiss-notice'], $_GET['_wpnonce'] ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stax-dismiss-notice' ) ) {
			delete_option( self::NOTICE_OPTION );
		}
	}

	/**
	 * One-time, dismissible notice after updating from 1.x.
	 */
	public function admin_notice_2_0() {
		if ( ! get_option( self::NOTICE_OPTION ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$customize = add_query_arg( 'autofocus[control]', Config::OPTION_PERF_LEGACY_CSS, admin_url( 'customize.php' ) );
		$dismiss   = wp_nonce_url( add_query_arg( 'stax-dismiss-notice', '1' ), 'stax-dismiss-notice' );

		printf(
			'<div class="notice notice-info"><p><strong>%1$s</strong> %2$s</p><p><a class="button button-primary" href="%3$s">%4$s</a> <a class="button" href="%5$s">%6$s</a></p></div>',
			esc_html__( 'Stax 2.0 is installed.', 'stax' ),
			esc_html__( 'Your site still uses the full 1.x stylesheet, so nothing changed visually. Stax 2.0 has a stylesheet about three times smaller; preview it in the Customizer and turn off "Load the full 1.x stylesheet" when you are happy with it.', 'stax' ),
			esc_url( $customize ),
			esc_html__( 'Preview the new stylesheet', 'stax' ),
			esc_url( $dismiss ),
			esc_html__( 'Dismiss', 'stax' )
		);
	}
}
