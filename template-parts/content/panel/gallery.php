<?php

namespace Stax;

use Stax\Customizer\Config;

if ( ! stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_PANEL ) ) {
	return;
}

\Stax_Assets::instance()->enqueue_swiper();

$gallery = [];

if ( has_block( 'gallery', get_the_content() ) ) {
	$post_blocks = parse_blocks( get_the_content() );

	foreach ( $post_blocks as $block ) {
		if ( $block['blockName'] === 'core/gallery' ) {
			if ( empty( $block['innerBlocks'] ) && isset( $block['attrs']['ids'] ) ) {
				foreach ( $block['attrs']['ids'] as $id ) {
					$gallery[] = $id;
				}
			} else {
				foreach ( $block['innerBlocks'] as $inner_block ) {
					$gallery[] = $inner_block['attrs']['id'];
				}
			}
		}

		if ( ! empty( $gallery ) ) {
			break;
		}
	}
}

if ( empty( $gallery ) ) {
	return;
}

$overlay_classes  = [];
$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_OVERLAY );
$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION );

if ( $overlay_color !== 'none' && in_array( $overlay_location, [ 'single', 'both' ] ) ) {
	$overlay_classes[] = 'svq-overlay svq-overlay--' . $overlay_color;
}

$overlay_classes = implode( ' ', $overlay_classes );

$item_style = stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_SLIDES );

$item_class = '';

if ( $item_style === 'wide' ) {
	$item_class = 'svq-gallery__item-wide';
} elseif ( $item_style === 'portrait' ) {
	$item_class = 'svq-gallery__item-portrait';
}

$view  = 0;
$count = 0;
if ( ! empty( $gallery ) ) {
	$view = count( $gallery );
	if ( $view > 5 ) {
		$count = $view - 5;
		$view  = 'more';
	}
}

$slider_classes   = [];
$slider_classes[] = sprintf( 'svq--%s', stax()->get_option( Config::OPTION_SINGLE_POST_GALLERY_WIDTH ) );
$slider_classes[] = 'svq-gallery--view-' . $view;
$slider_classes   = implode( ' ', $slider_classes );

$nav_class      = '';
$title_position = stax()->get_option( Config::OPTION_SINGLE_POST_TITLE_POSITION );

if ( $title_position === 'title-over' && in_array( stax()->get_option( Config::OPTION_SINGLE_POST_TITLE_ALIGN ), [ 'middle', 'middle-center' ] ) ) {
	$nav_class = 'svq-control-bottom';
}

?>


<?php \Stax_Assets::instance()->enqueue_lightbox(); ?>

<div class="svq-gallery-slider <?php echo esc_attr( $slider_classes ); ?>" data-more="<?php echo esc_attr( $count ); ?>">
	<div class="swiper-wrapper">
		<?php foreach ( $gallery as $key ) : ?>
			<div class="svq-gallery__item swiper-slide <?php echo esc_attr( $item_class ); ?>">
				<div class="svq-gallery__image-wrapp <?php echo esc_attr( $overlay_classes ); ?>">
					<a href="<?php echo esc_url( wp_get_attachment_image_url( $key, 'large' ) ); ?>"
					   data-elementor-open-lightbox="no"
					   data-lightbox="gallery-<?php the_ID(); ?>"
					   data-caption="<?php echo esc_attr( wp_get_attachment_caption( $key ) ); ?>">
						<?php

						echo wp_get_attachment_image(
							$key,
							'full',
							false,
							[
								'class'           => 'svq-gallery__image',
								'data-object-fit' => 'cover',
							]
						)

						?>
						<span class="svq-img-loader"></span>
						<noscript>
							<?php

							echo wp_get_attachment_image(
								$key,
								'full',
								false,
								[ 'class' => 'skip-lazy' ]
							)

							?>
						</noscript>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="swiper-pagination"></div>

	<div class="svq-gallery-control svq-gallery-control-prev <?php echo esc_attr( $nav_class ); ?>"></div>
	<div class="svq-gallery-control svq-gallery-control-next <?php echo esc_attr( $nav_class ); ?>"></div>
</div>
