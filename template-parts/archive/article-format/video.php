<?php

namespace Stax;

$data = stax()->get_video_from_content();

if ( ! $data['video'] ) {
	return;
}

$video        = $data['video'];
$video_source = $data['source'];

$external_video_img_placeholder = '';
$external_video_img             = '';

if ( $video_source === 'hosted' ) {
	$video = wp_get_attachment_url( $video );
} else {
	if ( strpos( $video, 'youtube.com' ) > 0 || strpos( $video, 'youtu.be' ) > 0 ) {
		preg_match( "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video, $matches );

		if ( isset( $matches[0] ) && $matches[0] ) {
			$external_video_img = 'https://img.youtube.com/vi/' . $matches[0] . '/sddefault.jpg';
		}
	} elseif ( strpos( $video, 'vimeo.com' ) > 0 ) {
		preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)([a-z0-9#=]+)(?:$|\/|\?)(?:[?]?.*)$%im', $video, $matches );

		if ( isset( $matches[3] ) && $matches[3] ) {
			$player_id = $matches[3];
			$player_id = explode( '#', $player_id );
			$player_id = $player_id[0];

			$api = wp_remote_get( 'https://vimeo.com/api/v2/video/' . $player_id . '.php' );

			if ( ! is_wp_error( $api ) ) {
				$body = unserialize( $api['body'] );

				if ( is_array( $body ) && ! empty( $body ) ) {
					$external_video_img = $body[0]['thumbnail_large'];
				}
			}
		}
	}
}

if ( ! isset( $show_thumbnail_block ) || ! $video ) {
	$show_thumbnail_block = false;
}

$media_aspect_ratio = stax()->get_post_data( 'media_aspect_ratio', '1-1' );

if ( ! isset( $overlay_classes ) ) {
	$overlay_classes = '';
}

$container_type = $video_source === 'hosted' ? 'svq-media-video' : 'svq-media-image';

$external_video_img_placeholder = $external_video_img;
$external_video_img_class       = '';

if ( 'no-lazyload' !== get_theme_mod( 'lazy_load_media' ) ) {
	$external_video_img_placeholder = get_theme_file_uri( '/assets/img/placeholder.png' );
	$external_video_img_class       = 'lazy';
}

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
					<?php \Stax_Assets::instance()->enqueue_plyr(); ?>
					<video class="video-plyr lazy-video">
						<source
							src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/img/placeholder.mp4' ) ); ?>"
							data-src="<?php echo esc_url( $video ); ?>" type="video/mp4">
					</video>
					<noscript>
						<video class="video-plyr">
							<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
						</video>
					</noscript>
				</div>
			<?php elseif ( $video_source === 'other' ) : ?>
				<div class="svq-progressive__placeholder-image <?php echo esc_attr( $overlay_classes ); ?>">
					<a href="<?php the_permalink(); ?>">
						<img src="<?php echo esc_url( $external_video_img_placeholder ); ?>"
							 data-src="<?php echo esc_url( $external_video_img ); ?>"
							 class="svq-progressive__image <?php echo esc_attr( $external_video_img_class ); ?>"
							 alt="<?php the_title(); ?>" data-object-fit="cover">
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
