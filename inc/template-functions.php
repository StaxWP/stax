<?php
/**
 * Global template functions and hooks.
 *
 * @package stax
 */

use function Stax\stax;
use Stax\Customizer\Config;

/**
 * Render single main content
 */
function stax_show_single() {
	stax()->get_template_part( 'template-parts/single' );
}

add_action( 'stax_single', 'stax_show_single' );

/**
 * Render archive main content
 */
function stax_show_archive() {

	// Include archive panel. Except blog home.
	if ( ! is_home() ) {
		stax()->get_template_part( 'template-parts/content/panel', 'page' );
	}

	// Include posts loop
	stax()->get_template_part( 'template-parts/archive/index' );
}
add_action( 'stax_archive', 'stax_show_archive' );


/**
 * Displays the classes for the post container element.
 *
 * @param array       $class One or more classes to add to the class list.
 * @param int|WP_Post $post_id Optional. Post ID or post object. Defaults to the global `$post`.
 *
 * @since 1.0.0
 */
function stax_post_class( $class = [], $post_id = null ) {
	$post_classes = array_diff( get_post_class( '', $post_id ), [ 'has-post-thumbnail' ] );

	if ( is_sticky( $post_id ) ) {
		$post_classes[] = 'sticky';
	}

	$post_classes = array_unique( array_merge( $post_classes, (array) $class ) );

	echo 'class="' . esc_attr( implode( ' ', $post_classes ) ) . '"';
}

/**
 * Add placeholder tag when it is enabled.
 *
 * @return void
 */
function stax_add_page_preloader() {
	if ( stax()->get_option( Config::OPTION_MISC_PRELOADER ) ) {
		echo '<div class="svq-page-loader"></div>';
	}
}
add_action( 'wp_body_open', 'stax_add_page_preloader', 1 );

/**
 * Adds the Customizer block gap (Single Page > Block gap) to the dynamic CSS.
 *
 * @param string $style Dynamic CSS.
 *
 * @return string
 */
function stax_block_gap_style( $style ) {
	$block_gap = (string) stax()->get_option( Config::OPTION_SINGLE_BLOCK_GAP );

	// Accept a single CSS length only (for example 24px, 1.5rem or 0).
	if ( '' !== $block_gap && preg_match( '/^\d*\.?\d+(px|rem|em|%|vh|vw)?$/', trim( $block_gap ) ) ) {
		$style .= '.has-blocks .svq-main-page .entry-content > * + * { margin-top: ' . trim( $block_gap ) . '; }';
	}

	return $style;
}
add_filter( 'stax_dynamic_style_output', 'stax_block_gap_style' );


/**
 * Embed HTML for an external (YouTube, Vimeo, ...) video URL, using core oEmbed.
 * WP_Embed caches the result in post meta.
 *
 * @param string $url Video URL.
 *
 * @return string
 */
function stax_get_video_embed_html( $url ) {
	if ( empty( $url ) || empty( $GLOBALS['wp_embed'] ) ) {
		return '';
	}

	$html = $GLOBALS['wp_embed']->shortcode( [], $url );

	// WP_Embed returns a plain link when the provider is unknown.
	return ( is_string( $html ) && false !== strpos( $html, '<iframe' ) ) ? $html : '';
}

/**
 * Thumbnail URL for an external video, from the provider's oEmbed data (cached for a week).
 * Replaces the uncached Vimeo API call (and unserialize of its response) used until 1.2.2.
 *
 * @param string $url Video URL.
 *
 * @return string
 */
function stax_get_external_video_thumbnail( $url ) {
	if ( empty( $url ) ) {
		return '';
	}

	$key   = 'stax_video_thumb_' . md5( $url );
	$thumb = get_transient( $key );

	if ( false === $thumb ) {
		$data  = _wp_oembed_get_object()->get_data( $url, [ 'width' => 800 ] );
		$thumb = ( $data && ! empty( $data->thumbnail_url ) ) ? esc_url_raw( $data->thumbnail_url ) : '';
		set_transient( $key, $thumb, WEEK_IN_SECONDS );
	}

	return $thumb;
}
