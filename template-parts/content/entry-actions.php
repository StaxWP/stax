<?php
/**
 * Template part for displaying a post's comment and edit links
 *
 * @package stax
 */

namespace Stax;

edit_post_link(
	sprintf(
		wp_kses(
			/* translators: %s: post title */
			__( 'Edit article <span class="screen-reader-text">%s</span>', 'stax' ),
			[
				'span' => [
					'class' => [],
				],
			]
		),
		get_the_title()
	),
	'<div class="edit-link will-animate" data-cssanimate="fadeIn">',
	' </div>'
);
