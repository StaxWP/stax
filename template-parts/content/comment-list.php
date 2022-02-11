<?php

namespace Stax;

$comments_container_class = '';

if ( ! have_comments() ) {
	$comments_container_class .= 'comments-empty';
}

$comments_container_class .= ' for-post-id-' . get_the_ID();

?>

<div id="svq-comments" class="svq-comments <?php echo esc_attr( $comments_container_class ); ?>">
	<div class="comments-inner-content">
		<div class="comments-title comments-title--sticky">
			<div class="heading-title svq-content--small">
				<div class="heading-title-content">
					<span class="heading-title-label"><?php esc_html_e( 'Comments to:', 'stax' ); ?></span>
					<span class="heading-title-text"><?php the_title(); ?></span>
				</div>
				<div class="heading-title-action">
					<button type="button" class="btn btn-link close-responses">
						<span class="btn--default-state">
							<?php echo stax()->load_icon( 'close', 18 ); ?>
						</span>
					</button>
				</div>
			</div>
		</div>

		<ul class="comments-list svq-content--small section-reveal">
			<?php
			wp_list_comments(
				[
					'type'           => 'all',
					'callback'       => [ stax(), 'single_comment' ],
					'callback-after' => [ stax(), 'single_comment_after' ],
				]
			);
			?>
		</ul>

		<?php
		the_comments_navigation(
			[
				'prev_text' => '<span class="svq-icon icon-long-arrow-left icon--x24"></span>' . esc_html__( 'Older comments', 'stax' ),
				'next_text' => esc_html__( 'Newer comments', 'stax' ) . '<span class="svq-icon icon-long-arrow-right icon--x24"></span>',
			]
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<h5 class="no-comments"><?php esc_html_e( 'Comments are closed.', 'stax' ); ?></h5>
		<?php endif; ?>
	</div>
</div>
