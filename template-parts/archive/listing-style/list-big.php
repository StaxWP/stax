<?php
/**
 * The template for displaying archive page content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

$site_layout  = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE );
$layout_width = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_CONTAINER );
$media_width  = stax()->get_option( Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE );

if ( $layout_width === 'large' && $site_layout === 'no-side' ) {
	$media_size = 'stax-img-wide-xxl';
} else {
	$media_size = $media_width === 'wide' ? 'stax-img-wide-xxl' : 'stax-img-wide-xl';
}

$components = [
	'categories',
	'title',
	'meta',
	'media',
	'excerpt',
];

?>
<div class="svq-article--big">
	<?php

	if ( have_posts() ) {
		$k = 1;
		while ( have_posts() ) {
			the_post();

			if ( get_post_format() === 'gallery' ) {
				$media_size_altered = 'auto';
			} else {
				$media_size_altered = $media_size;
			}

			stax()->set_post_data( 'listing_type', 'list-big' );
			stax()->set_post_data( 'media_width', $media_width );
			stax()->set_post_data( 'media_size', $media_size_altered );
			stax()->set_post_data( 'media_aspect_ratio', '16-9' );

			do_action( 'stax_list_before_box', $k );

			stax()->get_template_part( 'template-parts/archive/article-box', null, compact( 'components' ) );
			$k ++;
		}

		if ( isset( $show_navigation ) && is_bool( $show_navigation ) && $show_navigation ) {
			echo stax()->get_the_posts_navigation();
		}
	} else {
		stax()->get_template_part( 'template-parts/content/error/index' );
	}

	?>
</div>
