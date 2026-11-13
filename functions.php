<?php
/**
 * Stax functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package stax
 */

define( 'STAX_MINIMUM_WP_VERSION', '6.6' );
define( 'STAX_MINIMUM_PHP_VERSION', '7.4' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], STAX_MINIMUM_WP_VERSION, '<' ) || version_compare( PHP_VERSION, STAX_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';

	return;
}

/**
 * Autoloads Stax\ classes from the inc/ folder (Stax\Foo\Component => inc/Foo/Component.php).
 *
 * @param string $class_name Class name to load.
 *
 * @return bool True if the class was loaded.
 */
function stax_autoload( $class_name ) {
	if ( 0 !== strpos( $class_name, 'Stax\\' ) ) {
		return false;
	}

	$path = get_template_directory() . '/inc/' . str_replace( '\\', '/', substr( $class_name, 5 ) ) . '.php';

	if ( ! file_exists( $path ) ) {
		return false;
	}

	require_once $path;

	return true;
}

spl_autoload_register( 'stax_autoload' );

// Load the `stax()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'Stax\stax' );

if ( ! isset( $content_width ) ) {
	$content_width = 1140; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Core global meant to be set by themes.
}

require get_template_directory() . '/inc/class-stax-assets.php';
require get_template_directory() . '/inc/template-functions.php';
