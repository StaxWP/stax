<?php

use function Stax\stax;

use Stax\Customizer\Config;

?>

<div class="entry-meta">
	<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR ) ) : ?>
		<div class="author-avatar">
			<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author">
				<?php echo get_avatar( $post->post_author, '60', '', '', [ 'class' => 'avatar-img' ] ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-meta__content">
		<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME ) ) : ?>
			<span class="by-line">
				<span class="author">
					<?php printf( esc_html__( 'Written by %s', 'stax' ), get_the_author_posts_link() ); ?>
				</span>
			</span>
		<?php endif; ?>
		<?php if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_POST_DATE ) ) : ?>
			<span class="posted-on">
				<?php echo stax()->get_the_date(); ?>
			</span>
		<?php endif; ?>
	</div>

	<?php do_action( 'stax_post_list_after_meta' ); ?>
</div>
