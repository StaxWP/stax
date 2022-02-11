<?php

namespace Stax;

$gallery = [];

foreach ( stax()->get_gallery_from_content() as $id ) {
	$gallery[] = [
		'size' => '',
		'img'  => $id,
	];
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

if ( ! isset( $show_thumbnail_block ) ) {
	$show_thumbnail_block = false;
}

if ( ! isset( $overlay_classes ) ) {
	$overlay_classes = '';
}

$media_size = stax()->get_post_data( 'media_size', 'stax-img-lg' );

if ( $media_size === 'auto' ) {
	if ( count( $gallery ) === 1 ) {
		foreach ( $gallery as $key => $item ) {
			$gallery[ $key ]['size'] = 'stax-img-lg';
		}
	} elseif ( count( $gallery ) === 2 ) {
		foreach ( $gallery as $key => $item ) {
			$gallery[ $key ]['size'] = 'stax-img-md';
		}
	} else {
		foreach ( $gallery as $key => $item ) {
			if ( $key === 0 ) {
				$gallery[ $key ]['size'] = 'stax-img-md';
			} else {
				$gallery[ $key ]['size'] = 'stax-img-xs';
			}
		}
	}
} else {
	foreach ( $gallery as $key => $item ) {
		$gallery[ $key ]['size'] = $media_size;
	}
}

?>

<?php if ( ! empty( $gallery ) ) : ?>

	<?php if ( $show_thumbnail_block ) : ?>
		<div class="post-thumbnail">
	<?php endif; ?>

	<div class="svq-gallery-grid svq-gallery--view-<?php echo esc_attr( $view ); ?>">
		<?php foreach ( $gallery as $item ) : ?>
			<div class="svq-gallery__item">
				<div class="svq-gallery__image-wrapp <?php echo esc_attr( $overlay_classes ); ?>"
					 data-more="<?php echo esc_attr( $count ); ?>">
					<a href="<?php echo esc_url( wp_get_attachment_image_url( $item['img'], 'large' ) ); ?>"
					   class="svq-gallery__image-link"
					   data-elementor-open-lightbox="no"
					   data-lightbox="gallery-<?php the_ID(); ?>"
					   data-caption="<?php echo esc_attr( wp_get_attachment_caption( $item['img'] ) ); ?>">
						<?php

						echo wp_get_attachment_image(
							$item['img'],
							$item['size'],
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
								$item['img'],
								$item['size'],
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

	<?php if ( $show_thumbnail_block ) : ?>
		</div>
	<?php endif; ?>

	<?php \Stax_Assets::instance()->enqueue_lightbox(); ?>

<?php endif; ?>
