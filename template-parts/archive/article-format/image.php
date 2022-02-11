<?php

namespace Stax;

if ( ! stax()->has_post_thumbnail() ) {
	return;
}

$post = get_post();

if ( ! isset( $show_thumbnail_block ) ) {
	$show_thumbnail_block = false;
}

$media_aspect_ratio = stax()->get_post_data( 'media_aspect_ratio', '1-1' );
$media_size         = stax()->get_post_data( 'media_size', 'stax-img-lg' );

if ( ! isset( $overlay_classes ) ) {
	$overlay_classes = '';
}
?>

<?php if ( $show_thumbnail_block ) : ?>
<div class="post-thumbnail">
	<?php endif; ?>

	<figure class="svq-media-image">
		<div class="svq-progressive">
			<div class="aspect-ratio-placeholder-fill aspect-ratio-<?php echo esc_attr( $media_aspect_ratio ); ?>"></div>
			<div class="svq-progressive__placeholder-image <?php echo esc_attr( $overlay_classes ); ?>">
				<a href="<?php the_permalink(); ?>">
					<?php

					$image_url  = get_the_post_thumbnail_url();
					$image_type = pathinfo( $image_url, PATHINFO_EXTENSION );
					$image_size = $image_type === 'gif' ? 'full' : $media_size;

					echo get_the_post_thumbnail(
						null,
						$image_size,
						[
							'class'           => 'svq-progressive__image',
							'data-object-fit' => 'cover',
						]
					);
					?>
					<span class="svq-img-loader"></span>
					<noscript>
						<?php echo get_the_post_thumbnail( null, 'thumbnail', [ 'class' => 'skip-lazy' ] ); ?>
					</noscript>
				</a>
			</div>
		</div>
		<figcaption class="svq-figcaption will-animate" data-cssanimate="fadeIn">
			<?php if ( $post && $post->post_excerpt ) : ?>
				<span class="media-caption--text"><?php wp_kses_post( $post->post_excerpt ); ?></span>
			<?php endif; ?>
		</figcaption>
	</figure>

	<?php if ( $show_thumbnail_block ) : ?>
</div>
<?php endif; ?>
