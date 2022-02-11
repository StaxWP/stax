<?php
/**
 * Template Name: Full Width - Small container
 *
 * Full width, small container
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<?php stax()->get_template_part( 'template-parts/content/panel', get_post_type() ); ?>

				<main id="primary" class="svq-main-page">
					<?php stax()->get_template_part( 'template-parts/content/entry', get_post_type() ); ?>
				</main><!-- #primary -->

			<?php endwhile; ?>

		</div>
	</div>

	<?php
	get_footer();
