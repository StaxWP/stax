<?php

namespace Stax\Builder;

$icon_type         = 'cart-icon-style1';
$cart_style        = 'dropdown';
$custom_html       = '';
$expand_enabled    = true;
$cart_label        = '';
$allowed_post_tags = wp_kses_allowed_html( 'header_footer_grid' );
$cart_is_empty     = WC()->cart->get_cart_contents_count() === 0;

$off_canvas_closing_button = '';
$mini_cart_classes         = [ 'stx-nav-cart', 'widget' ];
if ( $cart_style === 'off-canvas' ) {
	$mini_cart_classes         = [ 'stx-nav-cart', 'cart-off-canvas', 'widget' ];
	$off_canvas_closing_button = '<div class="cart-off-canvas-button-wrapper"><a href="#" class="stx-close-cart-sidebar button button-secondary secondary-default">' . __( 'Close', 'stax' ) . '</a></div>';
}
if ( (bool) $expand_enabled === false ) {
	$mini_cart_classes[] = 'expand-disable';
}
?>

<div class="component-wrap">
	<div class="responsive-nav-cart menu-item-nav-cart
	<?php
	echo esc_attr( $cart_style );
	echo $cart_is_empty ? ' cart-is-empty' : '';
	?>
	">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-icon-wrapper">
			<?php
			if ( ! empty( $cart_label ) ) {
				echo '<span class="cart-icon-label inherit-ff">';
				echo wp_kses_post( $cart_label );
				echo '</span>';
			}
			?>
			<span class="stx-icon stx-cart"><svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 1536q0 52-38 90t-90 38-90-38-38-90 38-90 90-38 90 38 38 90zm896 0q0 52-38 90t-90 38-90-38-38-90 38-90 90-38 90 38 38 90zm128-1088v512q0 24-16.5 42.5t-40.5 21.5l-1044 122q13 60 13 70 0 16-24 64h920q26 0 45 19t19 45-19 45-45 19h-1024q-26 0-45-19t-19-45q0-11 8-31.5t16-36 21.5-40 15.5-29.5l-177-823h-204q-26 0-45-19t-19-45 19-45 45-19h256q16 0 28.5 6.5t19.5 15.5 13 24.5 8 26 5.5 29.5 4.5 26h1201q26 0 45 19t19 45z"></path></svg>
			</span>
			<span class="screen-reader-text">
				<?php esc_html_e( 'Cart', 'stax' ); ?>
			</span>
			<span class="cart-count">
				<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
			</span>
			<?php do_action( 'stax_cart_icon_after_cart_total' ); ?>
		</a>
		<?php if ( $cart_style !== 'link' && ! is_cart() && ! is_checkout() ) { ?>
		<div class="<?php echo esc_attr( implode( ' ', $mini_cart_classes ) ); ?>">

			<?php
			/**
			 * Executes actions before the cart popup content.
			 *
			 * @since 2.9.3
			 */
			do_action( 'stax_before_cart_popup' );

			echo wp_kses_post( $off_canvas_closing_button );

			the_widget(
				'WC_Widget_Cart',
				[
					'title'         => ' ',
					'hide_if_empty' => true,
				],
				[
					'before_title' => '',
					'after_title'  => '',
				]
			);

			if ( ! empty( $custom_html ) ) {
				echo '<div class="after-cart-html">';
				echo wp_kses( balanceTags( apply_filters( 'stax_post_content', $custom_html ), true ), $allowed_post_tags );
				echo '</div>';
			}

			/**
			 * Executes actions after the cart popup content.
			 *
			 * @since 2.9.3
			 */
			do_action( 'stax_after_cart_popup' );
			?>
		</div>
		<?php } ?>
	</div>
</div>
