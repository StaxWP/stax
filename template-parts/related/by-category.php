<?php

namespace Stax;

use Stax\Customizer\Config;

global $post;
$args = [
	'post__not_in' => [ $post->ID ],
	'post_type'    => 'post',
	'showposts'    => 8,
	'post_status ' => 'publish',
	'orderby'      => 'rand',
	'order'        => 'ASC',
];

if ( is_singular( 'post' ) ) {
	$categories = get_the_category( $post->ID );

	if ( ! empty( $categories ) ) {
		$category_ids = [];
		foreach ( $categories as $rcat ) {
			$category_ids[] = $rcat->term_id;
		}

		$args['category__in'] = $category_ids;
	}
} else {
	$categories = get_object_taxonomies( $post );

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $tax ) {
			$terms = wp_get_object_terms( $post->ID, $tax, [ 'fields' => 'ids' ] );

			$args['tax_query'][] = [
				'taxonomy' => $tax,
				'field'    => 'id',
				'terms'    => $terms,
			];
		}
	}
}

if ( ! $categories ) {
	return;
}

$the_query = new \WP_Query( $args );

?>

	<?php if ( $the_query->have_posts() ) : ?>

		<?php \Stax_Assets::instance()->enqueue_swiper(); ?>

	<div class="svq-media-slider svq-post-related">
		<div class="heading-title">
			<div class="heading-title-content">
				<h4 class="heading-title-text">
					<?php esc_html_e( 'Related articles', 'stax' ); ?>
				</h4>
			</div>
		</div>

		<div class="svq-slider-articles svq-article--list-card">
			<div class="swiper-wrapper">
				<?php

				$media_width = stax()->get_option( Config::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL );
				$media_size  = $media_width === 'normal' ? 'stax-img-sm' : 'stax-img-lg';

				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					if ( $media_width === 'wide' && get_post_format() === 'gallery' ) {
						$media_size = 'auto';
					}

					stax()->set_post_data( 'listing_type', 'masonry' );
					stax()->set_post_data( 'media_size', $media_size );
					stax()->set_post_data( 'media_width', $media_width );
					stax()->set_post_data( 'media_aspect_ratio', $media_width !== 'normal' ? '16-9' : '1-1' );
					stax()->set_post_data( 'col_classes', 'swiper-slide' );

					stax()->get_template_part( 'template-parts/archive/article-box' );
				}

				?>
			</div>

			<?php if ( $the_query->post_count > 1 ) : ?>
				<div class="svq-article-btn">
					<a href="#" class="btn btn-light btn-sm button-ripple slide-to--back">
						<?php echo stax()->load_icon( 'long-arrow-left', 18 ); ?>
					</a>
					<a href="#" class="btn btn-light btn-sm button-ripple btn-icon--right slide-to--next">
						<?php esc_html_e( 'Next', 'stax' ); ?>
						<?php echo stax()->load_icon( 'long-arrow-right', 18 ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>
	</div>

<?php else : ?>

	<div class="svq-no-related"></div>

<?php endif; ?>

	<?php

	wp_reset_postdata();
