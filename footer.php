<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package stax
 */

namespace Stax;

/**
 * Included Footer section using actions
 *
 * @hooked stax_footer_builder_action
 */

do_action( 'stax_footer' );
?>

</div><!-- .svq-page-wrapper -->

<?php wp_footer(); ?>

</body>
</html>
