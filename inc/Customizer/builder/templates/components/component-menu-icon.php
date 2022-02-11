<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\MenuIcon;

$item_attributes = apply_filters( 'stax_nav_toggle_data_attrs', '' );
$label           = component_setting( MenuIcon::TEXT_ID );


?>
<div class="menu-mobile-toggle item-button navbar-toggle-wrapper">
	<button type="button" class="navbar-toggle"
		<?php
		echo ( $item_attributes );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
			aria-label="
			<?php
			esc_html_e( 'Navigation Menu', 'stax' );
			?>
			">
		<?php
		if ( ! empty( $label ) ) {
			echo '<span class="nav-toggle-label">' . esc_html( $label ) . '</span>';
		}
		?>
		<span class="bars">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</span>
		<span class="screen-reader-text"><?php esc_html_e( 'Navigation Menu', 'stax' ); ?></span>
	</button>
</div> <!--.navbar-toggle-wrapper-->
