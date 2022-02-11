<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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
			 * Renders the single blog post template.
			 *
			 * @hooked stax_show_single - 10
			 * @includes template-parts/single.php
			 */
			do_action( 'stax_single' );
			?>

		</div>
	</div>
	<?php
	get_footer();
