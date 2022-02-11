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
		if ( $block['blockName'] === 'core/audio' ) {
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

$link = wp_get_attachment_url( $audio );

?>

<?php if ( $link ) : ?>
	<?php \Stax_Assets::instance()->enqueue_plyr(); ?>

	<figure class="svq-media-audio">
		<audio class="audio-plyr audio-plyr-alone" src="<?php echo esc_url( $link ); ?>" controls></audio>
	</figure>
<?php endif; ?>
