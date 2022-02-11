<?php

namespace Stax;

use Stax\Customizer\Config;

global $post;

/*
 * Panel classes
 */

$panel_classes = [];

$has_media = stax()->has_media();

if ( ! get_post_format() && ! stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_FOR_STANDARD ) ) {
	$has_media = false;
}

$full_screen_options = [
	2 => 'sm',
	3 => 'md',
	4 => 'lg',
];

$title_position = stax()->get_option( Config::OPTION_SINGLE_POST_TITLE_POSITION );

if ( $has_media ) {
	$panel_classes[] = 'has-featured-image';
	$panel_classes[] = 'svq-panel--' . $title_position;
	$panel_classes[] = 'svq-panel--' . stax()->get_option( Config::OPTION_SINGLE_POST_TITLE_COLOR );

	$full_screen = stax()->get_option( Config::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT );

	if ( (int) $full_screen && isset( $full_screen_options[ $full_screen ] ) ) {
		$panel_classes[] = 'svq-panel--full-height-' . $full_screen_options[ $full_screen ];
	} else {
		$panel_classes[] = 'svq-panel--off-height';
	}
} else {
	$panel_classes[] = 'svq-panel--light';
	$panel_classes[] = 'svq-panel--off-height';
}

$panel_classes = implode( ' ', $panel_classes );

/*
 * Header classes
 */

$header_classes   = [];
$header_classes[] = 'entry-header--' . stax()->get_option( Config::OPTION_SINGLE_POST_TITLE_SIZE );

if ( $title_position === 'title-over' || $title_position === 'half' ) {
	$alignment_slug = Config::OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN;
} else {
	$alignment_slug = Config::OPTION_SINGLE_POST_TITLE_ALIGN;
}

$title_alignment = stax()->get_option( $alignment_slug );

if ( $title_alignment === 'center' ) {
	$header_classes[] = 'entry-header--h-center';
} elseif ( $title_alignment === 'middle' ) {
	$header_classes[] = 'entry-header--v-center';
} elseif ( $title_alignment === 'middle-center' ) {
	$header_classes[] = 'entry-header--h-center';
	$header_classes[] = 'entry-header--v-center';
}

$header_classes = implode( ' ', $header_classes );

/**
 * Content classes
 */

$content_classes = [];

if ( $has_media && $title_position !== 'half' && stax()->get_option( Config::OPTION_SINGLE_POST_MEDIA_PANEL_FADE_TEXT ) ) {
	$content_classes[] = 'fade-on-scroll';
}

$content_classes = implode( ' ', $content_classes );

?>

<div class="svq-panel <?php echo esc_attr( $panel_classes ); ?>">
	<?php
	if ( ! get_post_format() && stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_FOR_STANDARD ) ) {
		stax()->get_template_part( 'template-parts/content/panel/image' );
	} elseif ( in_array( get_post_format(), [ 'quote', 'audio', 'image' ] ) ) {
		stax()->get_template_part( 'template-parts/content/panel/image' );
	} elseif ( get_post_format() === 'video' ) {
		stax()->get_template_part( 'template-parts/content/panel/video' );
	} elseif ( get_post_format() === 'gallery' ) {
		stax()->get_template_part( 'template-parts/content/panel/gallery' );
	}

	?>

	<header class="entry-header <?php echo esc_attr( $header_classes ); ?>">
		<div class="entry-header-content <?php echo esc_attr( $content_classes ); ?>">
			<?php

			if ( in_array(
				stax()->get_option( Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB ),
				[
					'category',
					'category-breadcrumb',
				]
			) ) {
				stax()->get_categories_nav();
			}

			if ( in_array(
				stax()->get_option( Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB ),
				[
					'breadcrumbv',
					'category-breadcrumb',
				]
			) ) {
				stax()->get_breadcrumbs();
			}

			$quote = '';
			if ( get_post_format() === 'quote' ) {
				$quote = \Stax_Assets::instance()->get_content_quote( apply_filters( 'the_content', $post->post_content ) );
				add_filter( 'the_content', [ \Stax_Assets::instance(), 'remove_first_content_quote' ] );
			}

			?>

			<?php if ( ! $quote ) : ?>
				<h1 class="entry-title">
					<?php if ( $has_media ) : ?>
						<span class="will-animate d-block" data-cssanimate="fadeIn">
							<?php the_title(); ?>
						</span>
					<?php else : ?>
						<?php the_title(); ?>
					<?php endif; ?>
				</h1>
			<?php else : ?>
				<?php stax()->get_template_part( 'template-parts/content/panel/quote', null, compact( 'quote' ) ); ?>
			<?php endif; ?>

			<?php if ( get_post_format() === 'audio' ) : ?>
				<?php stax()->get_template_part( 'template-parts/content/panel/audio' ); ?>
			<?php endif; ?>

			<?php if ( stax()->get_option( Config::OPTION_SINGLE_POST_META ) ) : ?>
				<?php stax()->get_template_part( 'template-parts/content/entry-meta' ); ?>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( stax()->get_option( Config::OPTION_SINGLE_POST_SHAPES ) ) : ?>
		<?php stax()->get_template_part( 'template-parts/content/panel/shapes' ); ?>
	<?php endif; ?>
</div>
