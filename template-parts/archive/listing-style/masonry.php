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

$masonry_classes = [];
$site_layout     = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE );
$layout_width    = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_CONTAINER );

if ( $site_layout !== 'no-side' || $layout_width === 'small' ) {
	$masonry_classes[] = 'svq-masonry-lg-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP );
	$masonry_classes[] = 'svq-masonry-md-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET );
	$masonry_classes[] = 'svq-masonry-sm-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE );
} else {
	$masonry_classes[] = 'svq-masonry-lg-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP );
	$masonry_classes[] = 'svq-masonry-md-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET );
	$masonry_classes[] = 'svq-masonry-sm-' . 12 / (int) stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE );
}

$masonry_classes = implode( ' ', $masonry_classes );
\Stax_Assets::instance()->enqueue_masonry();

?>

<?php if ( have_posts() ) : ?>
	<div class="svq-media-masonry">
		<div class="svq-masonry-articles svq-article--list-card <?php echo esc_attr( $masonry_classes ); ?>">
			<div class="svq-grid-sizer"></div>
			<?php

			$media_width = stax()->get_option( Config::OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE );
			$media_size  = $media_width === 'normal' ? 'stax-img-sm' : 'stax-img-lg';

			$k = 1;
			while ( have_posts() ) {
				the_post();

				if ( $media_width === 'wide' && get_post_format() === 'gallery' ) {
					$media_size = 'auto';
				}

				if ( $media_width !== 'normal' && get_post_format() === 'video' ) {
					$media_aspect_ratio = '16-9';
				} else {
					$media_aspect_ratio = '1-1';
				}

				if ( $k === 1 && stax()->get_option( Config::OPTION_ARCHIVE_LIST_MASONRY_FIRST_BIG ) ) {
					stax()->set_post_data( 'grid_span', 'svq-masonry-span' );
				}

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
