<?php

use function Stax\stax;

use Stax\Customizer\Config;

$allowed_post_formats = [ 'image', 'video', 'gallery' ];

if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_STANDARD_IMAGE ) ) {
	$allowed_post_formats[] = 'standard';
}

$post_format = get_post_format() ?: 'standard';
$item_format = $post_format === 'standard' ? 'image' : $post_format;

if ( in_array( $post_format, $allowed_post_formats, true ) ) {
	$overlay_classes  = '';
	$overlay_color    = 'none';
	$overlay_location = 'none';

	if ( 'image' === $post_format ) {
		$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_OVERLAY );
		$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION );
	} elseif ( 'video' === $post_format ) {
		$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_OVERLAY );
		$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION );
	} elseif ( 'gallery' === $post_format ) {
		$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_OVERLAY );
		$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION );
	}

	if ( $overlay_color !== 'none' && in_array( $overlay_location, [ 'listing', 'both' ] ) ) {
		$overlay_classes .= 'svq-overlay svq-overlay--' . $overlay_color;
	}

	stax()->get_template_part(
		'template-parts/archive/article-format/' . $item_format,
		null,
		compact( 'show_thumbnail_block', 'overlay_classes' )
	);
}
