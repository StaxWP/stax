<?php

namespace Stax;

use Stax\Customizer\Config;

global $post;

$meta_classes = '';

if ( stax()->has_media() ) {
	$meta_classes .= 'will-animate';
}

$show_avatar  = stax()->get_post_data( 'show_meta_avatar', stax()->get_option( Config::OPTION_SINGLE_POST_META_AUTHOR_AVATAR ) );
$show_author  = stax()->get_post_data( 'show_meta_author', stax()->get_option( Config::OPTION_SINGLE_POST_META_AUTHOR_NAME ) );
$show_date    = stax()->get_post_data( 'show_meta_date', stax()->get_option( Config::OPTION_SINGLE_POST_META_POST_DATE ) );
$show_reading = stax()->get_post_data( 'show_meta_reading', stax()->get_option( Config::OPTION_SINGLE_POST_META_READING_TIME ) );

// Virtual pages (BuddyPress, bbPress directories) have no author or date.
if ( empty( $post->post_author ) ) {
	$show_avatar = false;
	$show_author = false;
}

if ( empty( $post->ID ) ) {
	$show_date = false;
}

?>

<div class="entry-meta <?php echo esc_attr( $meta_classes ); ?>"
	 data-cssanimate="<?php echo esc_attr( stax()->get_option( Config::OPTION_SINGLE_POST_META_ANIMATION ) ); ?>">

	<?php if ( $show_avatar ) : ?>
		<div class="author-avatar">
			<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>" rel="author">
				<?php echo get_avatar( $post->post_author, '60', '', get_the_author_meta( 'display_name', $post->post_author ), [ 'class' => 'avatar-img' ] ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-meta__content">
		<?php if ( $show_author ) : ?>
			<span class="by-line">
				<span class="author">
					 <?php
						$author_link = '<a href="' . esc_url( get_author_posts_url( $post->post_author ) ) . '" rel="author">' .
									esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</a>';
						/* translators: %s: author posts link. */
						printf( esc_html__( 'Written by %s', 'stax' ), $author_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $author_link is built from escaped parts.
						?>
				</span>
			</span>
		<?php endif; ?>

		<span class="posted-on">
			<?php if ( $show_date ) : ?>
				<?php esc_html_e( 'Posted on ', 'stax' ); ?>
				<?php echo stax()->get_the_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup built from escaped parts in Date\Component::get_the_date(). ?>
			<?php endif; ?>
			<?php if ( $show_reading ) : ?>
				<span class="entry-meta__text">
					<span class="no-reading-time"><?php esc_html_e( 'Less than', 'stax' ); ?></span>
					<span class="reading-time">0</span>
					<?php esc_html_e( 'min read', 'stax' ); ?>
				</span>
			<?php endif; ?>
		</span>
	</div>

	<?php do_action( 'stax_post_list_after_meta' ); ?>
</div>
