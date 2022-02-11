<?php

namespace Stax;

use Stax\Customizer\Config;

/*
 * Panel classes
 */

$render_panel = true;

$render_panel = apply_filters( 'stax_render_general_panel', $render_panel );

if ( ! $render_panel ) {
	return;
}

/*
 * Title classes
 */

$title       = '';
$description = '';

if ( is_404() ) {
	$title = __( 'Oops! That page can&rsquo;t be found.', 'stax' );
} elseif ( is_home() ) {
	if ( ! have_posts() ) {
		$title = __( 'Nothing Found', 'stax' );
	} elseif ( ! is_front_page() ) {
		$title = single_post_title();
	}
} elseif ( is_search() ) {
	$title = sprintf(
		esc_html__( 'Search results for: "%s"', 'stax' ),
		get_search_query()
	);
} elseif ( is_archive() ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} else {
		$title = get_the_archive_title();
	}

	$description = get_the_archive_description();
} elseif ( is_page() ) {
	$title = get_the_title();
}

$header_classes = 'entry-header--' . stax()->get_option( Config::OPTION_SINGLE_PAGE_TITLE_SIZE );

if ( stax()->get_option( Config::OPTION_SINGLE_PAGE_TITLE_ALIGN ) === 'center' ) {
	$header_classes .= ' entry-header--h-center';
}

?>

<div class="svq-panel svq-panel--light">
	<header class="entry-header <?php echo esc_attr( $header_classes ); ?>">
		<div class="entry-header-content">
			<?php
			if ( stax()->get_option( Config::OPTION_SINGLE_PAGE_BREADCRUMBS ) ) {
				stax()->get_breadcrumbs();
			}
			?>

			<h1 class="entry-title">
				<?php echo wp_kses_post( $title ); ?>
			</h1>
			<?php if ( $description ) : ?>
				<div class="archive-description"><?php echo wp_kses_post( $description ); ?></div>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( stax()->get_option( Config::OPTION_SINGLE_PAGE_SHAPES ) ) : ?>
		<?php stax()->get_template_part( 'template-parts/content/panel/shapes' ); ?>
	<?php endif; ?>

</div>
