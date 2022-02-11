<?php
/**
 * Template part for displaying a post's content
 *
 * @package stax
 */

namespace Stax;

?>

<div class="entry-content">
	<?php
	do_action( 'stax_before_single_content' );

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

	do_action( 'stax_after_single_content' );
	?>
</div>
