<?php

namespace Stax;

use Stax\Customizer\Config;

if ( ! isset( $animation ) ) {
	$animation = true;
}

$categories_classes = '';

if ( stax()->has_media() && $animation ) {
	$categories_classes .= 'will-animate';
}

$categories = get_the_category();

?>

<?php if ( ! empty( $categories ) ) : ?>
	<ol class="meta-category <?php echo esc_attr( $categories_classes ); ?>"
		data-cssanimate="<?php echo esc_attr( stax()->get_option( Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ) ); ?>">
		<?php foreach ( $categories as $key => $category ) : ?>
			<li class="meta-category__item">
				<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="meta-category__link">
					<?php echo esc_html( $category->name ); ?>
				</a>
				<?php if ( count( $categories ) - ( $key + 1 ) > 0 ) : ?>
					<span></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
