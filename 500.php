<?php
/**
 * The template for displaying 500 pages (internal server errors)
 *
 * @link https://github.com/xwp/pwa-wp#offline--500-error-handling
 *
 * @package stax
 */

namespace Stax;

get_header();

stax()->force_main_layout( 'full' );
?>
	<div <?php stax()->main_section_class(); ?>>
		<div class="svq-site-content">

			<main id="primary" class="svq-main-page">
				<?php

				while ( have_posts() ) {
					the_post();

					stax()->get_template_part( 'template-parts/content/error/500' );
				}
				?>
			</main><!-- #primary -->

		</div>
	</div>

	<?php
	get_footer();
