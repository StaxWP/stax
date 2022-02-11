<?php
/**
 * Template part for displaying a post's footer
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

?>

<div class="entry-footer entry-footer--default">
	<?php stax()->get_template_part( 'template-parts/content/entry-actions' ); ?>

	<?php
	if ( is_singular( get_post_type() ) ) {
		// Show post navigation only when the post type is 'post' or has an archive.
		if ( 'post' === get_post_type() || get_post_type_object( get_post_type() )->has_archive ) {
			stax()->custom_post_nav();
		}
	}
	?>

	<?php
	if ( is_single() && get_post_type() === 'post' &&
		stax()->get_option( Config::OPTION_SINGLE_POST_RELATED_POSTS ) ) {
		stax()->get_template_part( 'template-parts/related/by-' . stax()->get_option( Config::OPTION_SINGLE_POST_RELATED_POSTS_TYPE ) );
	}
	?>
</div><!-- .entry-footer -->
