<?php
/**
 * Template part for displaying the page content when an error has occurred
 *
 * @package stax
 */

namespace Stax;

if ( is_home() && current_user_can( 'publish_posts' ) ) {
	?>
	<p>
		<?php
		printf(
			wp_kses(
			/* translators: 1: link to WP admin new post page. */
				__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'stax' ),
				[
					'a' => [
						'href' => [],
					],
				]
			),
			esc_url( admin_url( 'post-new.php' ) )
		);
		?>
	</p>
	<?php
} elseif ( is_search() ) {
	?>
	<p>
		<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'stax' ); ?>
	</p>
	<?php
} else {
	?>
	<p>
		<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'stax' ); ?>
	</p>
	<?php
}

get_search_form();
