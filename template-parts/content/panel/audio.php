<?php

namespace Stax;

use Stax\Customizer\Config;

if ( ! stax()->get_option( Config::OPTION_SINGLE_POST_AUDIO_PANEL ) ) {
	return;
}

$audio = '';

if ( has_block( 'audio', get_the_content() ) ) {
	$post_blocks = parse_blocks( get_the_content() );

	foreach ( $post_blocks as $block ) {
		if ( ( $block['blockName'] ?? '' ) === 'core/audio' && ! empty( $block['attrs']['id'] ) ) {
			$audio = $block['attrs']['id'];
		}

		if ( $audio ) {
			break;
		}
	}
}

if ( ! $audio ) {
	return;
}

$audio_url = wp_get_attachment_url( $audio );

?>

<?php if ( $audio_url ) : ?>
	<figure class="svq-media-audio">
		<audio class="audio-plyr audio-plyr-alone" src="<?php echo esc_url( $audio_url ); ?>" controls preload="metadata"></audio>
	</figure>
<?php endif; ?>
