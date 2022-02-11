<?php
/**
 * Template part for displaying the page content when a 404 error has occurred
 *
 * @package stax
 */

namespace Stax;

?>

<section class="error">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12 text-center">
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/404_page_not_found.png' ); ?>"
					class="placeholder-404-img"
					alt="404">
			</div>

			<div class="col-md-12 text-center">
				<h1 class="article-title font-weight-black mb-4 mt-8"><?php esc_html_e( 'Ooooops', 'stax' ); ?></h1>

				<h6 class="mb-8"><?php esc_html_e( 'The page your are looking for has not been found.', 'stax' ); ?></h6>
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>
