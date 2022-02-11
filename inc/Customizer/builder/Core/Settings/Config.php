<?php

namespace Stax\Builder\Core\Settings;

class Config {
	/**
	 * Get library url location.
	 *
	 * @return string Library url.
	 */
	public static function get_url() {
		return get_template_directory_uri() . '/inc/Customizer/builder';
	}

	/**
	 * Get theme support string.
	 *
	 * @return string Theme support string.
	 */
	public static function get_support() {
		return 'hfg_support';
	}

	/**
	 * Return library path.
	 *
	 * @return string Path.
	 */
	public static function get_path() {
		return get_template_directory() . '/inc/Customizer/builder';
	}

}
