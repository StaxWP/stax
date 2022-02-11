<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\Nav;
use Stax\Builder\Core\Builder\Header as HeaderBuilder;

$style                 = component_setting( Nav::STYLE_ID );
$dropdowns_expanded    = component_setting( Nav::EXPAND_DROPDOWNS );
$additional_menu_class = $dropdowns_expanded && current_row( HeaderBuilder::BUILDER_NAME ) === 'sidebar' ? ' ' . Nav::DROPDOWNS_EXPANDED_CLASS : '';
$container_classes     = [ $style ];

$container_classes[] = 'nav-menu-primary';

$menu_id = Nav::NAV_MENU_ID . '-' . current_row( HeaderBuilder::BUILDER_NAME );
?>
<div class="stx-nav-wrap">
	<div role="navigation" class="<?php echo esc_attr( join( ' ', $container_classes ) ); ?>"
			aria-label="<?php echo esc_html( __( 'Primary Menu', 'stax' ) ); ?>">

		<?php
		echo wp_nav_menu(
			[
				'theme_location' => 'primary',
				'menu_id'        => $menu_id,
				'menu_class'     => 'primary-menu-ul nav-ul' . $additional_menu_class,
				'container'      => 'ul',
				'walker'         => '\Stax\Nav_Menus\Nav_Walker',
				'fallback_cb'    => '\Stax\Nav_Menus\Nav_Walker::fallback',
				'echo'           => false,
			]
		);
		?>
	</div>
</div>
