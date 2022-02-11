<?php
/**
 * Template part for displaying a post
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'svq-article section-reveal' ); ?>>

	<div class="entry-content">
		<?php

		the_content(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'stax' ),
					[
						'span' => [
							'class' => [],
						],
					]
				),
				get_the_title()
			)
		);

		wp_link_pages(
			[
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'stax' ),
				'after'  => '</div>',
			]
		);

		// Show comments only when the post type supports it and when comments are open or at least one comment exists.
		if ( comments_open() && ! stax()->get_option( Config::OPTION_MISC_DISABLE_PAGE_COMMENTS ) && post_type_supports( get_post_type(), 'comments' ) ) {
			comments_template();
		}

		?>

	</div>
</article><!-- #post-<?php the_ID(); ?> -->

