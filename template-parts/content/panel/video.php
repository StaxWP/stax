<?php

namespace Stax;

use Stax\Customizer\Config;

if ( ! stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_PANEL ) ) {
	return;
}

$data = stax()->get_video_from_content();

if ( ! $data['video'] ) {
	return;
}

$video        = $data['video'];
$video_source = $data['source'];

$media_classes = [];
$embed_html    = '';

if ( $video_source === 'hosted' ) {
	$video = wp_get_attachment_url( $video );
} else {
	$embed_html = stax_get_video_embed_html( $video );
}

$media_classes[] = sprintf( 'svq--%s', stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_WIDTH ) );
$media_classes   = implode( ' ', $media_classes );

$overlay_classes  = [];
$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_OVERLAY );
$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION );

if ( $overlay_color !== 'none' && in_array( $overlay_location, [ 'single', 'both' ] ) ) {
	$overlay_classes[] = 'svq-overlay svq-overlay--' . $overlay_color;
}

$overlay_classes = implode( ' ', $overlay_classes );

?>

<?php if ( $video ) : ?>
	<figure class="svq-media-video video-reveal <?php echo esc_attr( $media_classes ); ?>">
		<div class="svq-progressive">
			<div class="aspect-ratio-1-1"></div>
			<div class="svq-progressive__placeholder-video <?php echo esc_attr( $overlay_classes ); ?>">
				<?php if ( $video_source === 'hosted' ) : ?>
					<video class="video-plyr" controls playsinline preload="metadata" src="<?php echo esc_url( $video ); ?>"></video>
				<?php elseif ( $embed_html ) : ?>
					<div class="video-plyr video-embed">
						<?php echo $embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oEmbed HTML from core, filtered by wp_filter_oembed_result(). ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</figure>
<?php endif; ?>
