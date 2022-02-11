<?php

namespace Stax;

use Stax\Customizer\Config;

if ( ! stax()->has_post_thumbnail() ) {
	return;
}

$thumb_post = get_post( get_post_thumbnail_id() );

$media_classes   = [];
$media_classes[] = sprintf( 'svq--%s', stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_WIDTH ) );

if ( stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_FORMAT ) !== 'cover' ) {
	$media_classes[] = 'svq-contain-bg';
}

$media_classes = implode( ' ', $media_classes );

$overlay_classes  = [];
$overlay_color    = stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_OVERLAY );
$overlay_location = stax()->get_option( Config::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION );

if ( $overlay_color !== 'none' && in_array( $overlay_location, [ 'single', 'both' ] ) ) {
	$overlay_classes[] = 'svq-overlay svq-overlay--' . $overlay_color;
}

$overlay_classes = implode( ' ', $overlay_classes );

?>

<figure class="svq-media-image <?php echo esc_attr( $media_classes ); ?>">
	<div class="svq-progressive">
		<div class="aspect-ratio-placeholder-fill aspect-ratio-1-1"></div>
		<div class="svq-progressive__placeholder-image <?php echo esc_attr( $overlay_classes ); ?>">
			<?php
			echo get_the_post_thumbnail(
				null,
				'full',
				[
					'class'           => 'svq-progressive__image',
					'data-object-fit' => 'cover',
				]
			)
			?>
			<span class="svq-img-loader"></span>
			<noscript>
				<?php echo get_the_post_thumbnail( null, 'full', [ 'class' => 'skip-lazy' ] ); ?>
			</noscript>
		</div>
	</div>
	<?php if ( $thumb_post && $thumb_post->post_excerpt ) : ?>
		<figcaption class="svq-figcaption will-animate" data-cssanimate="fadeIn">
			<span class="media-caption--text"><?php wp_kses_post( $thumb_post->post_excerpt ); ?></span>
		</figcaption>
	<?php endif; ?>
</figure>
