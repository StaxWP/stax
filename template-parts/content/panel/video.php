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

$player_id     = '';
$player_type   = '';
$media_classes = [];

if ( $video_source === 'hosted' ) {
	$video = wp_get_attachment_url( $video );
} else {
	if ( strpos( $video, 'youtube.com' ) > 0 || strpos( $video, 'youtu.be' ) > 0 ) {
		preg_match( "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video, $matches );
		if ( isset( $matches[0] ) && $matches[0] ) {
			$player_id = $matches[0];
		}

		$player_type = 'youtube';
	} elseif ( strpos( $video, 'vimeo.com' ) > 0 ) {
		preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)([a-z0-9#=]+)(?:$|\/|\?)(?:[?]?.*)$%im', $video, $matches );

		if ( isset( $matches[3] ) && $matches[3] ) {
			$player_id = $matches[3];
			$player_id = explode( '#', $player_id );
			$player_id = $player_id[0];
		}

		$player_type = 'vimeo';
	}
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
	<?php \Stax_Assets::instance()->enqueue_plyr(); ?>

	<figure class="svq-media-video video-reveal <?php echo esc_attr( $media_classes ); ?>">
		<div class="svq-progressive">
			<div class="aspect-ratio-1-1"></div>
			<div class="svq-progressive__placeholder-video <?php echo esc_attr( $overlay_classes ); ?>">
				<?php if ( $video_source === 'hosted' ) : ?>
					<video class="video-plyr lazy-video">
						<source src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/img/placeholder.mp4' ) ); ?>"
								data-src="<?php echo esc_url( $video ); ?>" type="video/mp4">
					</video>
					<noscript>
						<video class="video-plyr">
							<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
						</video>
					</noscript>
				<?php elseif ( $video_source === 'other' && $player_id && $player_type ) : ?>
					<div class="video-plyr video-plyr-alone"
						 data-plyr-provider="<?php echo esc_attr( $player_type ); ?>"
						 data-plyr-embed-id="<?php echo esc_attr( $player_id ); ?>"></div>
				<?php endif; ?>
			</div>
		</div>
	</figure>
<?php endif; ?>
