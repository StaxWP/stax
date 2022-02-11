<?php

$header_components = [
	'Stax\Builder\Core\Components\Logo',
	'Stax\Builder\Core\Components\MenuIcon',
	'Stax\Builder\Core\Components\Nav',
	'Stax\Builder\Core\Components\Button',
	'Stax\Builder\Core\Components\CustomHtml',
	'Stax\Builder\Core\Components\Search',
	'Stax\Builder\Core\Components\SearchResponsive',
	'Stax\Builder\Core\Components\SecondNav',
];

if ( class_exists( 'WooCommerce', false ) ) {
	$header_components[] = 'Stax\Builder\Core\Components\CartIcon';
}

$footer_components = [
	'Stax\Builder\Core\Components\FooterWidgetOne',
	'Stax\Builder\Core\Components\FooterWidgetTwo',
	'Stax\Builder\Core\Components\FooterWidgetThree',
	'Stax\Builder\Core\Components\FooterWidgetFour',
	'Stax\Builder\Core\Components\NavFooter',
	'Stax\Builder\Core\Components\Copyright',
];

add_theme_support(
	'hfg_support',
	[
		'builders' => [
			'Stax\Builder\Core\Builder\Header' => $header_components,
			'Stax\Builder\Core\Builder\Footer' => $footer_components,
		],
	]
);

require_once get_template_directory() . '/inc/Customizer/builder/functions-template.php';


add_action( 'stax_header', 'stax_header_builder_action' );
function stax_header_builder_action() {
	do_action( 'hfg_header_render' );
}

add_action( 'stax_footer', 'stax_footer_builder_action' );
function stax_footer_builder_action() {
	do_action( 'hfg_footer_render' );
}

if ( version_compare( PHP_VERSION, '5.3.29' ) > 0 && class_exists( 'Stax\Builder\Main' ) ) {
	add_action( 'after_setup_theme', 'Stax\Builder\Main::get_instance' );
}
