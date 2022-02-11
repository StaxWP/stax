<?php
/**
 * CSS Variables trait
 */

namespace Stax\Customizer;

use Stax\Customizer\Config;

/**
 * Trait Css_Vars
 *
 * @since 3.0.0
 */
trait Css_Vars {

	/**
	 * Get the common typography rules
	 *
	 * @retun array
	 */
	public function get_typography_rules() {
		$headings = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
		];
		$rules    = [];

		foreach ( $headings as $heading_selector ) {
			$rules[ '--' . $heading_selector . '-font-weight' ] = [
				Dynamic_Selector::META_KEY     => sprintf( 'stax_%s_typeface_general', $heading_selector ) . '.fontWeight',
				Dynamic_Selector::META_DEFAULT => '900',
				'font'                         => 'mods_' . Config::MODS_FONT_HEADINGS,
			];
		}

		return $rules;
	}

}
