<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\SecondNav;

$style = component_setting( SecondNav::STYLE_ID );

$container_classes = [ $style ];

$container_classes[] = 'nav-menu-secondary';

?>
<div class="stx-top-bar">
	<div role="navigation" class="menu-content <?php echo esc_attr( join( ' ', $container_classes ) ); ?>"
		aria-label="<?php echo esc_html( __( 'Secondary Menu', 'stax' ) ); ?>">
		<?php
		wp_nav_menu(
			[
				'theme_location' => 'top-bar',
				'menu_class'     => 'nav-ul',
				'menu_id'        => 'secondary-menu',
				'container'      => 'ul',
				'depth'          => - 1,
				'fallback_cb'    => '__return_false',
			]
		);
		?>
	</div>
</div>
