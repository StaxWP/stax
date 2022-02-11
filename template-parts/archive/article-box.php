<?php

namespace Stax;

use Stax\Customizer\Config;

global $post;

$col_classes          = [];
$show_thumbnail_block = false;

$post_format = get_post_format() ?: 'standard';

$article_classes = [ 'svq-article' ];
$has_media       = stax()->has_media();

$allowed_post_formats = [ 'image', 'video', 'gallery' ];

if ( stax()->get_option( Config::OPTION_ARCHIVE_LIST_STANDARD_IMAGE ) ) {
	$allowed_post_formats[] = 'standard';
}

if ( $has_media && in_array( $post_format, $allowed_post_formats, true ) ) {
	$article_classes[]    = 'has-post-thumbnail';
	$show_thumbnail_block = true;

	if ( stax()->get_post_data( 'media_position' ) ) {
		$article_classes[] = 'ty-thumb-' . stax()->get_post_data( 'media_position' );
	}
}

if ( $has_media && in_array( $post_format, $allowed_post_formats, true ) && stax()->get_post_data( 'media_width', 'normal' ) === 'wide' ) {
	$article_classes[] = 'post-thumbnail-wide';
}


$article_classes[] = ! get_the_title() ? 'svq-no-title' : '';

$show_categories   = (bool) stax()->get_post_data( 'show_category', stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_CATEGORY ) );
$article_classes[] = ! $show_categories ? 'svq-no-categories' : '';

$show_excerpt      = (bool) stax()->get_post_data( 'show_excerpt', $post_format !== 'audio' && stax()->get_excerpt() );
$article_classes[] = ! $show_excerpt ? 'svq-no-excerpt' : '';

$show_author_avatar = stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR );
$show_author_name   = stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME );
$show_post_date     = stax()->get_option( Config::OPTION_ARCHIVE_LIST_SHOW_POST_DATE );

$show_meta         = (bool) stax()->get_post_data( 'show_meta', $show_author_avatar || $show_author_name || $show_post_date );
$article_classes[] = ! $show_meta ? 'svq-no-meta' : '';

$has_grid_span = (bool) stax()->get_post_data( 'grid_span' );

$col_classes[] = stax()->get_post_data( 'col_classes', '' );
$col_classes[] = stax()->get_post_data( 'grid_span', '' );

$quote = '';
if ( $post_format === 'quote' ) {
	$quote = \Stax_Assets::instance()->get_content_quote( apply_filters( 'the_content', $post->post_content ) );
}

?>

<div class="svq-article-col <?php echo esc_attr( implode( ' ', $col_classes ) ); ?>">
	<article <?php stax_post_class( $article_classes ); ?>>

		<?php

		if ( ! isset( $components ) ) {
			$components = stax()->get_article_components();
		}

		if ( ! $show_categories ) {
			$key = array_search( 'categories', $components, true );
			unset( $components[ $key ] );
		}

		if ( ! $show_excerpt ) {
			$key = array_search( 'excerpt', $components, true );
			unset( $components[ $key ] );
		}

		if ( ! $show_meta ) {
			$key = array_search( 'meta', $components, true );
			unset( $components[ $key ] );
		}

		if ( $has_grid_span && ( $key = array_search( 'media', $components, true ) ) !== false ) {
			stax()->get_template_part( 'template-parts/archive/article-parts/media', null, compact( 'show_thumbnail_block' ) );
			unset( $components[ $key ] );
		}

		?>

		<?php if ( $has_grid_span ) : ?>
		<div class="svq-article-content">
			<?php endif; ?>

			<?php

			foreach ( $components as $component ) {
				$vars = [];

				if ( $component === 'media' ) {
					$vars = compact( 'show_thumbnail_block' );
				}

				if ( $component === 'meta' ) {
					$vars = compact( 'post' );
				}

				if ( $component === 'title' || $component === 'excerpt' ) {
					$vars = compact( 'quote' );
				}

				stax()->get_template_part( 'template-parts/archive/article-parts/' . $component, null, $vars );
			}

			?>

			<?php if ( $has_grid_span ) : ?>
		</div>
	<?php endif; ?>

	</article>
</div>
