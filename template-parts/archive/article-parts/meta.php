<?php

use function Stax\stax;

use Stax\Customizer\Config;

?>

<div class="entry-meta">
	<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR ) ) : ?>
		<div class="author-avatar">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php echo get_avatar( $post->post_author, '60', '', get_the_author_meta( 'display_name', $post->post_author ), [ 'class' => 'avatar-img' ] ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-meta__content">
		<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME ) ) : ?>
			<span class="by-line">
				<span class="author">
					<?php
				/* translators: %s: author posts link. */
				printf( esc_html__( 'Written by %s', 'stax' ), get_the_author_posts_link() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_author_posts_link() returns core-escaped markup.
				?>
				</span>
			</span>
		<?php endif; ?>
		<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_POST_DATE ) ) : ?>
			<span class="posted-on">
				<?php echo stax()->get_the_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup built from escaped parts in Date\Component::get_the_date(). ?>
			</span>
		<?php endif; ?>
	</div>

	<?php do_action( 'stax_post_list_after_meta' ); ?>
</div>
