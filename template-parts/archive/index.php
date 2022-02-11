<?php
/**
 * The template for displaying archive page content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

?>

	<?php do_action( 'stax_before_archive_main' ); ?>

	<main id="primary" class="svq-main-page">
		<div class="svq-page type-page">
			<div class="entry-content">
				<?php

				do_action( 'stax_before_archive_content' );

				stax()->get_template_part( 'template-parts/archive/listing-style/' . stax()->get_option( Config::OPTION_ARCHIVE_LIST_TYPE ) );

				do_action( 'stax_after_archive_content' );

				?>
			</div>
		</div>
	</main><!-- #primary -->

	<?php do_action( 'stax_after_archive_main' ); ?>

	<?php
	get_sidebar();
