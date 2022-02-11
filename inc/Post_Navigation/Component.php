<?php
/**
 * Stax\Post_Navigation\Component class
 *
 * @package stax
 */

namespace Stax\Post_Navigation;

use Stax\Customizer\Config;
use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function is_attachment;
use function get_post;
use function get_adjacent_post;
use function previous_post_link;
use function next_post_link;
use function Stax\stax;

/**
 * Class for managing post thumbnail support.
 *
 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'post_navigation';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callbaget_the_posts_navigationck_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() {
		return [
			'custom_post_nav' => [ $this, 'custom_post_nav' ],
		];
	}

	/**
	 * @param bool $same_cat
	 */
	public function custom_post_nav( $same_cat = false ) {
		if ( ! stax()->get_option( Config::OPTION_SINGLE_POST_NAVIGATION ) ) {
			return;
		}

		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( $same_cat, '', true );
		$next     = get_adjacent_post( $same_cat, '', false );

		if ( ! $next && stax()->get_option( Config::OPTION_SINGLE_POST_NAVIGATION_POSITION ) === 'sticky' ) {
			return;
		}

		$container_classes   = [];
		$container_classes[] = stax()->get_option( Config::OPTION_SINGLE_POST_NAVIGATION_POSITION ) === 'sticky' ? 'nav-article--sticky' : '';

		$container_classes = implode( ' ', $container_classes );

		$navigation_classes   = [];
		$navigation_classes[] = 'svq-content--' . stax()->get_option( Config::OPTION_LAYOUT_POST_CONTAINER );

		$navigation_classes = implode( ' ', $navigation_classes );
		?>

		<div class="nav-article-section nav-down <?php echo esc_attr( $container_classes ); ?>" role="navigation">
			<div class="nav-article-content <?php echo esc_attr( $navigation_classes ); ?>">
				<?php
				if ( is_attachment() ) :
					previous_post_link( '%link', '<span id="older-nav">' . esc_html__( 'Go to article', 'stax' ) . '</span>' );
				else :
					if ( $previous ) {
						$this->nav_item( $previous, 'previous', $same_cat );
					}
					if ( $next ) {
						$this->nav_item( $next, 'next', $same_cat );
					}
				endif;
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * @param $post
	 * @param $type
	 * @param $cat
	 */
	private function nav_item( $post, $type, $cat ) {
		$item_class = $type === 'previous' ? 'nav-article-prev' : 'nav-article-next';
		?>

		<div class="<?php echo esc_attr( $item_class ); ?>">
			<?php

			$image = get_the_post_thumbnail( $post->ID, 'thumbnail' );
			if ( $image ) {
				?>
				<div class="post-thumbnail">
					<?php

					if ( $type === 'previous' ) {
						previous_post_link( '%link', $image, $cat );
					} else {
						next_post_link( '%link', $image, $cat );
					}

					?>
				</div>
				<?php
			}

			$no_title_class = '';

			if ( ! $post->post_title ) {
				$no_title_class = 'no-title-next';
			}

			?>
			<div class="heading-title <?php echo esc_attr( $no_title_class ); ?>">
				<div class="heading-title-content">
					<?php

					$title = '';
					if ( $post->post_title ) {
						$title = '<span class="heading-title-text">' . $post->post_title . '</span>';
					}

					$label_ending = ':';
					$label_class  = '';

					if ( ! $post->post_title ) {
						$label_class = 'no-title-' . $type;

						if ( $type === 'previous' ) {
							$label_ending = stax()->load_icon( 'long-arrow-left', 18 );
						} else {
							$label_ending = stax()->load_icon( 'long-arrow-right', 18 );
						}
					}

					if ( $type === 'previous' ) {
						$item_label = sprintf( __( 'Previous story %s', 'stax' ), $label_ending );
						previous_post_link( '%link', '<span class="heading-title-label ' . esc_attr( $label_class ) . '">' . $item_label . '</span>' . $title, $cat );
					} else {
						$item_label = sprintf( __( 'Next story %s', 'stax' ), $label_ending );
						next_post_link( '%link', '<span class="heading-title-label ' . esc_attr( $label_class ) . '">' . $item_label . '</span>' . $title, $cat );
					}

					?>
				</div>
			</div>
		</div>

		<?php
	}
}
