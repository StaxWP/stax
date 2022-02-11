<?php
/**
 * The template for displaying archive page content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

$grid_classes = [];
$site_layout  = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE );
$layout_width = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_CONTAINER );

if ( $site_layout !== 'no-side' || $layout_width === 'small' ) {
	$grid_classes[] = 'svq-grid-lg-12';
	$grid_classes[] = 'svq-grid-md-12';
	$grid_classes[] = 'svq-grid-sm-12';
} else {
	$grid_classes[] = 'svq-grid-lg-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP );
	$grid_classes[] = 'svq-grid-md-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET );
	$grid_classes[] = 'svq-grid-sm-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE );
}

$grid_classes = implode( ' ', $grid_classes );

?>

<?php if ( have_posts() ) : ?>
	<div class="svq-media-grid">
		<div class="svq-grid-articles svq-article--list-card <?php echo esc_attr( $grid_classes ); ?>">
			<?php

			$media_width = 'wide';
			$media_size  = 'stax-img-lg';

			$k = 1;
			while ( have_posts() ) {
				the_post();

				if ( $media_width === 'wide' && get_post_format() === 'gallery' ) {
					$media_size = 'auto';
				}

				if ( get_post_format() === 'video' ) {
					$media_aspect_ratio = '16-9';
				} else {
					$media_aspect_ratio = '1-1';
				}

				stax()->set_post_data( 'listing_type', 'grid' );
				stax()->set_post_data( 'grid_span', 'svq-grid-span-2' );
				stax()->set_post_data( 'media_size', $media_size );
				stax()->set_post_data( 'media_width', $media_width );
				stax()->set_post_data( 'media_aspect_ratio', $media_aspect_ratio );

				do_action( 'stax_masonry_grid_before_box', $k );

				stax()->get_template_part( 'template-parts/archive/article-box' );
				$k ++;
			}

			?>
		</div>

		<?php

		$nav = true;

		if ( isset( $show_navigation ) && is_bool( $show_navigation ) ) {
			$nav = $show_navigation;
		}

		if ( $nav ) {
			echo stax()->get_the_posts_navigation();
		}

		?>

	</div>
<?php else : ?>
	<?php stax()->get_template_part( 'template-parts/content/error/index' ); ?>
<?php endif; ?>
