<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

if ( post_password_required() ) {
	return;
}

\Stax_Assets::instance()->enqueue_comments_reply();
\Stax_Assets::instance()->enqueue_lightbox();
?>

<div id="comments-post-<?php echo esc_attr( get_the_ID() ); ?>"
	 class="svq-section-comments svq-comments--default"
	 data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
	<div class="svq-status-comments <?php echo esc_attr( have_comments() ? 'has-comments' : '' ); ?>">

		<div class="svq-svg-icon-wrapp"
			 data-svg-notifier="<?php echo( ( have_comments() ) ? esc_attr( get_comments_number() ) : '' ); ?>">
			<span class="icon icon-comment-lines icon--x64"></span>
		</div>

		<?php if ( ! have_comments() ) : ?>
			<h5 class="comments-stat-info"
				data-first="<?php esc_attr_e( 'People reacted to this story.', 'stax' ); ?>">
				<?php esc_html_e( 'No Comments', 'stax' ); ?>
			</h5>
			<a class="btn btn-sm btn-primary btn-glow button-ripple"
				data-action="btn-comments-trigger" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
				<span class="btn--default-state"><?php esc_html_e( 'Leave a comment', 'stax' ); ?></span>
				<span class="btn--active-state"><?php esc_html_e( 'Cancel', 'stax' ); ?></span>
			</a>
		<?php else : ?>
			<h5 class="comments-stat-info"><?php esc_html_e( 'People reacted to this story.', 'stax' ); ?></h5>
			<a class="btn btn-sm btn-primary btn-glow button-ripple"
				data-action="btn-comments-trigger" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
				<span class="btn--default-state"><?php esc_html_e( 'Show comments', 'stax' ); ?></span>
				<span class="btn--active-state"><?php esc_html_e( 'Hide comments', 'stax' ); ?></span>
			</a>
		<?php endif; ?>
	</div>

	<?php

	stax()->get_template_part( 'template-parts/content/comment-list' );
	stax()->custom_comment_form();

	?>
</div>
