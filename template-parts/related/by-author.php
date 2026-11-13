<?php

namespace Stax;

use Stax\Customizer\Config;

global $post;
$args = [
	'post__not_in' => [ $post->ID ],
	'post_type'    => 'post',
	'author__in'   => get_the_author_meta( 'ID' ),
	'showposts'    => 20,
	'post_status ' => 'publish',
	'orderby'      => 'rand',
	'order'        => 'ASC',
];

$the_query = new \WP_Query( $args );

$categories = [];

foreach ( $the_query->posts as $item ) {
	$post_categories = wp_get_post_categories( $item->ID );
	if ( is_array( $post_categories ) && isset( $post_categories[0] ) ) {
		$categories = array_unique( array_merge( $categories, [ $post_categories[0] ] ) );
	}
}

$swiper_slide_class = ! empty( $categories ) && count( $categories ) > 1 ? 'svq-slider-item-visible' : '';

?>

	<?php if ( $the_query->have_posts() ) : ?>


	<div class="svq-media-slider svq-author-related">
		<div class="heading-title">
			<div class="heading-title-content">
				<h4 class="heading-title-text">
					<?php
					/* translators: %s: author display name */
					printf( esc_html__( 'More from %s', 'stax' ), esc_html( get_the_author_meta( 'display_name' ) ) );
					?>
				</h4>
			</div>
		</div>

		<?php if ( ! empty( $categories ) && count( $categories ) > 1 ) : ?>
			<nav class="svq-slider-nav nav-button-style svq-master-carousel" aria-label="<?php esc_attr_e( 'Categories', 'stax' ); ?>">
				<ul class="svq-nav-list swiper-wrapper svq-nav-pagination" role="tablist">
					<?php foreach ( array_values( $categories ) as $stax_index => $stax_cat ) : ?>
						<li class="svq-list-item swiper-slide <?php echo 0 === $stax_index ? 'svq-list-item-active' : ''; ?>" role="presentation">
							<a href="#" class="svq-item-link svq-slider-nav-btn" role="tab" aria-selected="<?php echo 0 === $stax_index ? 'true' : 'false'; ?>" data-go-to-slide="<?php echo esc_attr( $stax_index ); ?>"><?php echo esc_html( get_cat_name( $stax_cat ) ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
		<div class="svq-slider-for svq-child-carousel">
			<?php

			$media_width = stax()->get_option( Config::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL );
			$media_size  = $media_width === 'normal' ? 'stax-img-sm' : 'stax-img-lg';

			?>

			<div class="swiper-wrapper">
				<?php for ( $i = 0, $iMax = count( $categories ); $i < $iMax; $i ++ ) : ?>
					<?php

					$extra_slider_class = $i == 0 ? ' swiper-slide-active' : '';

					?>
					<div class="svq-slider-item swiper-slide <?php echo esc_attr( $swiper_slide_class ); ?> <?php echo esc_attr( $extra_slider_class ); ?>">
						<div class="svq-slider-articles svq-article--list-card svq-nested swiper-container-horizontal">
							<div class="swiper-wrapper">
								<?php

								$category_posts = 0;

								while ( $the_query->have_posts() ) {
									$the_query->the_post();

									$post_cats = wp_get_post_categories( get_the_ID() );
									$post_cat  = null;
									if ( is_array( $post_cats ) && ! empty( $post_cats ) && isset( $post_cats[0] ) ) {
										$post_cat = $post_cats[0];
									}

									if ( isset( $categories[ $i ] ) && (int) $categories[ $i ] === (int) $post_cat ) {
										$category_posts ++;

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
								}

								?>

							</div>
						</div>
					</div>

				<?php endfor; ?>
			</div>

			<div class="svq-article-btn carousel-navigation">
				<button type="button" class="btn btn-light btn-sm button-ripple slide-to--back" aria-label="<?php esc_attr_e( 'Previous', 'stax' ); ?>">
					<?php echo stax()->load_icon( 'long-arrow-left', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG icon markup. ?>
				</button>
				<button type="button" class="btn btn-light btn-sm button-ripple btn-icon--right slide-to--next">
					<?php esc_html_e( 'Next', 'stax' ); ?>
					<?php echo stax()->load_icon( 'long-arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG icon markup. ?>
				</button>
			</div>
		</div>
	</div>

<?php else : ?>

	<div class="svq-no-related"></div>

<?php endif; ?>

	<?php

	wp_reset_postdata();
