<?php

namespace Stax;

$data = stax()->get_video_from_content();

if ( ! $data['video'] ) {
	return;
}

$video        = $data['video'];
$video_source = $data['source'];

$external_video_img = '';

if ( $video_source === 'hosted' ) {
	$video = wp_get_attachment_url( $video );
} else {
	$external_video_img = stax_get_external_video_thumbnail( $video );
}

if ( ! isset( $show_thumbnail_block ) || ! $video ) {
	$show_thumbnail_block = false;
}

$media_aspect_ratio = stax()->get_post_data( 'media_aspect_ratio', '1-1' );

if ( ! isset( $overlay_classes ) ) {
	$overlay_classes = '';
}

$container_type = $video_source === 'hosted' ? 'svq-media-video' : 'svq-media-image';

?>

<?php if ( $video ) : ?>

	<?php if ( $show_thumbnail_block ) : ?>
		<div class="post-thumbnail">
	<?php endif; ?>

	<figure class="<?php echo esc_attr( $container_type ); ?>">
		<div class="svq-progressive">
			<div
				class="aspect-ratio-placeholder-fill aspect-ratio-<?php echo esc_attr( $media_aspect_ratio ); ?>"></div>
			<?php if ( $video_source === 'hosted' ) : ?>
				<div class="svq-progressive__placeholder-video <?php echo esc_attr( $overlay_classes ); ?>">
					<video class="video-plyr" controls playsinline preload="none" src="<?php echo esc_url( $video ); ?>"></video>
				</div>
			<?php elseif ( $video_source === 'other' ) : ?>
				<div class="svq-progressive__placeholder-image <?php echo esc_attr( $overlay_classes ); ?>">
					<a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
						<?php if ( $external_video_img ) : ?>
							<img src="<?php echo esc_url( $external_video_img ); ?>" loading="lazy" decoding="async"
								class="svq-progressive__image" alt="<?php the_title_attribute(); ?>">
						<?php else : ?>
							<span class="screen-reader-text"><?php the_title(); ?></span>
						<?php endif; ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<figcaption class="svq-figcaption will-animate" data-cssanimate="fadeIn">
		</figcaption>
	</figure>

	<?php if ( $show_thumbnail_block ) : ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
