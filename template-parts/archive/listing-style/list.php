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

?>

<div class="svq-article--list">
	<?php

	if ( have_posts() ) {

		$k = 1;
		while ( have_posts() ) {
			the_post();

			$media_size = get_post_format() === 'gallery' ? 'auto' : 'stax-img-md';

			stax()->set_post_data( 'listing_type', 'list' );
			stax()->set_post_data( 'media_width', 'normal' );
			stax()->set_post_data( 'media_size', $media_size );
			stax()->set_post_data( 'media_position', stax()->get_option( Config::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS ) );

			do_action( 'stax_list_before_box', $k );

			stax()->get_template_part( 'template-parts/archive/article-box' );
			$k ++;
		}

		$nav = true;

		if ( isset( $show_navigation ) && is_bool( $show_navigation ) ) {
			$nav = $show_navigation;
		}

		if ( $nav ) {
			echo stax()->get_the_posts_navigation();
		}
	} else {
		stax()->get_template_part( 'template-parts/content/error/index' );
	}

	?>
</div>
