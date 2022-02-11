<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\NavFooter;

$style = component_setting( NavFooter::STYLE_ID );

$container_classes = [ $style ];

$container_classes[] = 'nav-menu-footer';

?>
<div class="component-wrap">
	<div role="navigation" class="<?php echo esc_attr( join( ' ', $container_classes ) ); ?>"
		aria-label="<?php echo esc_html( __( 'Footer Menu', 'stax' ) ); ?>">

		<?php
		wp_nav_menu(
			[
				'theme_location' => 'footer',
				'depth'          => 1,
				'container'      => 'ul',
				'menu_class'     => 'footer-menu nav-ul',
				'menu_id'        => 'footer-menu',
			]
		);
		?>
	</div>
</div>
