<?php
/**
 * Stax\Upgrades\Component class
 *
 * @package stax
 */

namespace Stax\Upgrades;

use Stax\Component_Interface;
use function Stax\stax;

/**
 * Class Component
 *
 * @package Stax\Upgrades
 */
class Component implements Component_Interface {

	private $option_name = 'stax-upgrades';

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

	public function do_upgrades() {
		$old_upgrades = get_option( $this->option_name, [] );
		$currentVersion = stax()->get_version();

		$upgrades = [
			$currentVersion => '_upgrade_current_version',
		];


		foreach ( $upgrades as $version => $function ) {
			if ( ! isset( $old_upgrades[ $version ] ) && version_compare( $currentVersion, $version, '>=' ) ) {

				// Run the upgrade
				$this->$function();
				$old_upgrades[ $version ] = true;
			}
		}
		update_option( $this->option_name, $old_upgrades );
	}

	private function _upgrade_current_version() {
		// Update db to the latest theme version
	}

}
