<?php
/**
 * The template for displaying search
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
		 * @includes template-parts/archive/listing-stylev/{layout}
		 */
		do_action( 'stax_archive' );
		?>

	</div>
</div>

<?php
get_footer();
