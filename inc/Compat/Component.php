<?php
/**
 * Stax\Compat\Component class
 *
 * Plugin and page builder integrations. Each integration only hooks in when its plugin is
 * active, so a site without them pays nothing.
 *
 * Covered here: WooCommerce, Beaver Themer, Header Footer Elementor (and other builders that
 * replace the header/footer through the stax_header/stax_footer hooks), LifterLMS,
 * BuddyPress/BuddyBoss, bbPress. Elementor Pro Theme Builder locations live in
 * Stax\Elementor\Component; SEO plugin breadcrumbs in Stax\Breadcrumb\Component.
 *
 * @package stax
 */

namespace Stax\Compat;

use Stax\Component_Interface;
use function Stax\stax;

/**
 * Class Component
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'compat';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_theme_support' ] );
		add_action( 'wp', [ $this, 'action_replace_header_footer' ] );

		if ( class_exists( 'WooCommerce' ) ) {
			$this->woocommerce();
		}

		if ( class_exists( 'LifterLMS' ) ) {
			$this->lifterlms();
		}
	}

	/**
	 * Theme supports that plugins look for.
	 */
	public function action_theme_support() {
		// WooCommerce: product gallery features and image sizes.
		add_theme_support(
			'woocommerce',
			[
				'thumbnail_image_width' => 450,
				'single_image_width'    => 800,
				'product_grid'          => [
					'default_rows'    => 4,
					'min_rows'        => 1,
					'default_columns' => 3,
					'min_columns'     => 1,
					'max_columns'     => 6,
				],
			]
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// BuddyPress: use the Nouveau template pack markup.
		add_theme_support( 'buddypress-use-nouveau' );

		// Beaver Themer: header, footer and parts layouts.
		add_theme_support( 'fl-theme-builder-headers' );
		add_theme_support( 'fl-theme-builder-footers' );
		add_theme_support( 'fl-theme-builder-parts' );

		// Header Footer Elementor (Ultimate Addons) and similar plugins.
		add_theme_support( 'header-footer-elementor' );

		// LifterLMS: let it use the theme sidebars.
		add_theme_support( 'lifterlms-sidebars' );
		add_theme_support( 'lifterlms-quizzes' );
	}

	/**
	 * When a builder plugin provides the header or footer for the current page, render it
	 * in place of the theme's header/footer builder.
	 */
	public function action_replace_header_footer() {
		$header = null;
		$footer = null;

		// Beaver Themer.
		if ( class_exists( '\FLThemeBuilderLayoutData' ) && class_exists( '\FLThemeBuilderLayoutRenderer' ) ) {
			if ( ! empty( \FLThemeBuilderLayoutData::get_current_page_header_ids() ) ) {
				$header = [ '\FLThemeBuilderLayoutRenderer', 'render_header' ];
			}
			if ( ! empty( \FLThemeBuilderLayoutData::get_current_page_footer_ids() ) ) {
				$footer = [ '\FLThemeBuilderLayoutRenderer', 'render_footer' ];
			}
		}

		// Header Footer Elementor.
		if ( function_exists( 'hfe_header_enabled' ) && hfe_header_enabled() && function_exists( 'hfe_render_header' ) ) {
			$header = 'hfe_render_header';
		}
		if ( function_exists( 'hfe_footer_enabled' ) && hfe_footer_enabled() && function_exists( 'hfe_render_footer' ) ) {
			$footer = 'hfe_render_footer';
		}

		/**
		 * Filters the callback that renders the header instead of the theme header builder.
		 *
		 * @param callable|null $header Callback, or null to keep the theme header.
		 */
		$header = apply_filters( 'stax_header_callback', $header );

		/**
		 * Filters the callback that renders the footer instead of the theme footer builder.
		 *
		 * @param callable|null $footer Callback, or null to keep the theme footer.
		 */
		$footer = apply_filters( 'stax_footer_callback', $footer );

		if ( $header && is_callable( $header ) ) {
			remove_action( 'stax_header', 'stax_header_builder_action' );
			add_action( 'stax_header', $header );
		}

		if ( $footer && is_callable( $footer ) ) {
			remove_action( 'stax_footer', 'stax_footer_builder_action' );
			add_action( 'stax_footer', $footer );
		}

		// Beaver Themer parts can be placed on the theme hooks.
		add_filter( 'fl_theme_builder_part_hooks', [ $this, 'filter_beaver_part_hooks' ] );
	}

	/**
	 * Theme hooks offered to Beaver Themer "Parts".
	 *
	 * @return array
	 */
	public function filter_beaver_part_hooks() {
		return [
			[
				'label' => __( 'Page', 'stax' ),
				'hooks' => [
					'stax_after_body' => __( 'After opening body tag', 'stax' ),
					'stax_header'     => __( 'Header', 'stax' ),
					'stax_footer'     => __( 'Footer', 'stax' ),
				],
			],
			[
				'label' => __( 'Content', 'stax' ),
				'hooks' => [
					'stax_single'  => __( 'Single content', 'stax' ),
					'stax_archive' => __( 'Archive content', 'stax' ),
				],
			],
		];
	}

	/**
	 * Opening markup shared by the WooCommerce and LifterLMS wrappers.
	 */
	public static function open_content_wrapper() {
		echo '<div ';
		stax()->main_section_class();
		echo '><div class="svq-site-content"><main id="primary" class="svq-main-page">';
	}

	/**
	 * Closing markup shared by the WooCommerce and LifterLMS wrappers (with the theme sidebar).
	 */
	public static function close_content_wrapper() {
		echo '</main>';
		get_sidebar();
		echo '</div></div>';
	}

	/**
	 * WooCommerce: theme wrappers instead of the default ones, theme sidebar.
	 */
	protected function woocommerce() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		add_action( 'woocommerce_before_main_content', [ __CLASS__, 'open_content_wrapper' ], 10 );
		add_action( 'woocommerce_after_main_content', [ __CLASS__, 'close_content_wrapper' ], 10 );

		// Shop pages use the wide container and, as in 1.x, no sidebar.
		add_action(
			'template_redirect',
			function () {
				if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
					stax()->force_main_container_size( 'large' );

					/**
					 * Filters the WooCommerce page layout: full (default), left, right, two-left, two-right or left-right.
					 *
					 * @param string $layout Layout name.
					 */
					stax()->force_main_layout( apply_filters( 'stax_woocommerce_layout', 'full' ) );
				}
			}
		);
	}

	/**
	 * LifterLMS: theme wrappers and the primary sidebar for courses and lessons.
	 */
	protected function lifterlms() {
		remove_action( 'lifterlms_before_main_content', 'lifterlms_output_content_wrapper', 10 );
		remove_action( 'lifterlms_after_main_content', 'lifterlms_output_content_wrapper_end', 10 );
		remove_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar', 10 );

		add_action( 'lifterlms_before_main_content', [ __CLASS__, 'open_content_wrapper' ], 10 );
		add_action( 'lifterlms_after_main_content', [ __CLASS__, 'close_content_wrapper' ], 10 );

		add_filter(
			'llms_get_theme_default_sidebar',
			function () {
				return 'sidebar-1';
			}
		);
	}
}
