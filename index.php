<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

get_header();
?>
	<div <?php stax()->main_section_class(); ?>>
		<div class="svq-site-content">

			<?php
			/**
			 * Renders the archive template
			 *
			 * @hooked stax_show_archive - 10
			 * @includes template-parts/archive/listing-style/{layout}
			 */
			do_action( 'stax_archive' );
			?>

		</div>
	</div>
	<?php
	get_footer();
