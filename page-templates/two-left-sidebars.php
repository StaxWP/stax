<?php
/**
 * Template Name: Stax - Two Left Sidebars
 *
 * Two Left Sidebars
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

stax()->force_main_layout( 'two-left' );

get_header();
?>
	<div <?php stax()->main_section_class(); ?>>
		<div class="svq-site-content">

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<?php
				if ( 'yes' === stax()->get_option( Config::OPTION_SINGLE_SHOW_TITLE ) ) {
					stax()->get_template_part( 'template-parts/content/panel', get_post_type() );
				}
				?>

				<main id="primary" class="svq-main-page">
					<?php stax()->get_template_part( 'template-parts/content/entry', get_post_type() ); ?>
				</main><!-- #primary -->

			<?php endwhile; ?>

			<?php get_sidebar(); ?>

		</div>
	</div>

	<?php
	get_footer();
