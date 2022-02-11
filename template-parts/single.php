<?php
/**
 * The template for displaying single post/page main content
 *
 * @package stax
 */

namespace Stax;

?>

<?php
while ( have_posts() ) :
	the_post();
	?>

	<?php stax()->get_template_part( 'template-parts/content/panel', get_post_type() ); ?>

	<?php

	$atts       = apply_filters( 'stax_main_attributes', [] );
	$attributes = '';

	foreach ( $atts as $attr => $value ) {
		if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}
	}

	?>

	<main id="primary" class="svq-main-page" <?php echo $attributes; ?>>
		<?php stax()->get_template_part( 'template-parts/content/entry', get_post_type() ); ?>
	</main><!-- #primary -->

<?php endwhile; ?>

<?php
get_sidebar();
