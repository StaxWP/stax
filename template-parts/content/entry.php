<?php
/**
 * Template part for displaying a post
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'svq-article' ); ?>>
		<div class="entry-content-wrapper sticky-el-wrapper">
			<?php do_action( 'stax_before_entry_content' ); ?>

			<?php stax()->get_template_part( 'template-parts/content/entry-content' ); ?>
		</div>

		<?php stax()->get_template_part( 'template-parts/content/entry-footer' ); ?>
	</article><!-- #post-<?php the_ID(); ?> -->

	<?php
	// Show comments only when the post type supports it and when comments are open or at least one comment exists.
	if ( is_singular( get_post_type() ) && ! stax()->get_option( Config::OPTION_MISC_DISABLE_POST_COMMENTS ) && post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}
