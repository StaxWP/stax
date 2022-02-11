<?php
/**
 * The template for displaying user interest content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

// Include posts loop
$show_navigation = false;
$title           = __( 'New from your network', 'stax' );

?>

<h3 class="title-section" data-first-letter="<?php echo substr( esc_attr( $title ), 0, 1 ); ?>">
	<?php echo esc_html( $title ); ?>
</h3>

<?php

if ( have_posts() ) {
	stax()->get_template_part(
		'template-parts/archive/listing-style/' . stax()->get_option( Config::OPTION_ARCHIVE_LIST_TYPE ),
		null,
		compact( 'show_navigation' )
	);
} else {
	stax()->get_template_part( 'template-parts/content/error/feed' );
}

$title = __( 'Latest articles', 'stax' );

?>

<h3 class="title-section" data-first-letter="<?php echo substr( esc_attr( $title ), 0, 1 ); ?>">
	<?php echo esc_html( $title ); ?>
</h3>
