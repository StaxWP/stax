<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package stax
 */

namespace Stax;

stax()->force_main_layout( 'full' );
stax()->force_main_container_size( 'small' );

get_header();
?>
	<div <?php stax()->main_section_class(); ?>>
		<div class="svq-site-content">
			<main id="primary" class="svq-main-page">
				<?php
				stax()->get_template_part( 'template-parts/content/error/404' );
				?>
			</main>
			<?php get_sidebar(); ?>
		</div>
	</div>

	<?php
	get_footer();
