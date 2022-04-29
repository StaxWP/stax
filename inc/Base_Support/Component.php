<?php
/**
 * Stax\Base_Support\Component class
 *
 * @package stax
 */

namespace Stax\Base_Support;

use Stax\Customizer\Config;
use Stax\Component_Interface;
use Stax\Templating_Component_Interface;

use function add_action;
use function add_filter;
use function add_theme_support;
use function is_singular;
use function pings_open;
use function esc_url;
use function get_bloginfo;
use function wp_get_theme;
use function get_template;
use function Stax\stax;

/**
 * Class Component
 *
 * @package Stax\Base_Support
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'base_support';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_essential_theme_support' ] );
		add_action( 'wp_head', [ $this, 'action_add_pingback_header' ] );
		add_filter( 'body_class', [ $this, 'extra_body_classes' ] );
		add_filter( 'embed_defaults', [ $this, 'filter_embed_dimensions' ] );
		add_filter( 'theme_scandir_exclusions', [ $this, 'filter_scandir_exclusions_for_optional_templates' ] );

	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() {
		return [
			'get_version'              => [ $this, 'get_version' ],
			'get_asset_version'        => [ $this, 'get_asset_version' ],
			'get_breadcrumbs'          => [ $this, 'get_breadcrumbs' ],
			'get_categories_nav'       => [ $this, 'get_categories_nav' ],
			'get_excerpt'              => [ $this, 'get_excerpt' ],
			'get_the_posts_navigation' => [ $this, 'get_the_posts_navigation' ],
			'get_html_class'           => [ $this, 'get_html_class' ],
		];
	}

	/**
	 * Adds theme support for essential features.
	 */
	public function action_essential_theme_support() {
		// Add default RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Ensure WordPress manages the document title.
		add_theme_support( 'title-tag' );

		$args = [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		];

		// Ensure WordPress theme features render in HTML5 markup.
		add_theme_support( 'html5', $args );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			[
				'image',
				'gallery',
				'quote',
				'video',
				'audio',
			]
		);

		// Add support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}

	/**
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 */
	public function action_add_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}

	/**
	 * @param array $classes
	 *
	 * @return array
	 */
	public function extra_body_classes( array $classes ) {
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		if ( is_singular( get_post_type() ) ) {
			if ( ! post_password_required() && post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
				$classes[] = 'page-has-comments';
			}
		}

		if ( stax()->get_option( Config::OPTION_GENERAL_CONTAINER_BOXED ) ) {
			$classes[] = 'layout-boxed';
		}

		if ( is_single() && get_post_type() === 'post' && stax()->get_option( Config::OPTION_SINGLE_POST_RELATED_POSTS ) ) {
			$classes[] = 'page-has-related';
		}

		if ( is_singular() && 'no' === stax()->get_option( Config::OPTION_SINGLE_SHOW_TITLE ) ) {
			$classes[] = 'has-no-media-panel';
		}

		$show_footer = true;

		if ( is_404() ) {
			$show_footer = (bool) stax()->get_option( Config::OPTION_404_FOOTER );
		}

		if ( ! $show_footer || ! stax()->is_any_footer_column_active() ) {
			$classes[] = 'no-footer-widgets';
		}

		if (function_exists('has_blocks') && has_blocks() ) {
			$classes[] = 'has-blocks';
		}

		return $classes;
	}

	/**
	 * Sets the embed width in pixels, based on the theme's design and stylesheet.
	 *
	 * @param array $dimensions An array of embed width and height values in pixels (in that order).
	 *
	 * @return array Filtered dimensions array.
	 */
	public function filter_embed_dimensions( array $dimensions ) {
		$dimensions['width'] = 720;

		return $dimensions;
	}

	/**
	 * Excludes any directory named 'optional' from being scanned for theme template files.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/theme_scandir_exclusions/
	 *
	 * @param array $exclusions the default directories to exclude.
	 *
	 * @return array Filtered exclusions.
	 */
	public function filter_scandir_exclusions_for_optional_templates( array $exclusions ) {
		return array_merge(
			$exclusions,
			[ 'optional' ]
		);
	}


	/**
	 * Gets the theme version.
	 *
	 * @return string Theme version number.
	 */
	public function get_version() {
		static $theme_version = null;

		if ( null === $theme_version ) {
			$theme_version = wp_get_theme( get_template() )->get( 'Version' );
		}

		return $theme_version;
	}

	/**
	 * Gets the version for a given asset.
	 *
	 * Returns filemtime when WP_DEBUG is true, otherwise the theme version.
	 *
	 * @param string $filepath Asset file path.
	 *
	 * @return string Asset version number.
	 */
	public function get_asset_version( $filepath ) {
		if ( WP_DEBUG ) {
			return (string) filemtime( $filepath );
		}

		return $this->get_version();
	}

	/**
	 * Returns breadcrumbs template
	 *
	 * @param bool $animation
	 */
	public function get_breadcrumbs( $animation = true ) {
		stax()->get_template_part( 'template-parts/content/breadcrumbs', null, compact( 'animation' ) );
	}

	/**
	 * Returns categories nav template
	 *
	 * @param bool $animation
	 */
	public function get_categories_nav( $animation = true ) {
		stax()->get_template_part( 'template-parts/content/categories-nav', null, compact( 'animation' ) );
	}

	/**
	 * @return string
	 */
	public function get_excerpt() {
		return get_the_excerpt();
	}

	/**
	 * Custom posts pagination
	 *
	 * @return string
	 */
	public function get_the_posts_navigation() {
		global $paged, $wp_query;
		$max_page = (int) $wp_query->max_num_pages;

		if ( $max_page === 1 ) {
			return '';
		}

		if ( $max_page > 1 ) {
			if ( ! $paged ) {
				$paged = 1;
			}
		}

		$nav = '';

		if ( $paged > 1 ) {
			$nav .= '<div class="nav-previous">';
			$nav .= '<a class="btn btn-light btn-sm button-ripple btn-icon--left nav-previous" href="' . get_pagenum_link( $paged - 1 ) . '">' . stax()->load_icon( 'long-arrow-left' ) . esc_html__( 'Newer posts', 'stax' ) . ' </a>';
			$nav .= '</div>';
		}

		if ( $paged + 1 <= $max_page ) {
			$nav .= '<div class="nav-next">';
			$nav .= '<a class="btn btn-light btn-sm button-ripple btn-icon--right nav-next" href="' . get_pagenum_link( $paged + 1 ) . '">' . stax()->load_icon( 'long-arrow-right' ) . esc_html__( 'Older posts', 'stax' ) . ' </a>';
			$nav .= '</div>';
		}

		if ( $nav ) {
			return '<nav class="navigation posts-navigation"><h2 class="screen-reader-text">' .
				   esc_html__( 'Posts navigation', 'stax' ) . '</h2>' .
				   '<div class="nav-links">' .
				   $nav .
				   '</div>' .
				   '</nav>';
		}

		return '';
	}

	/**
	 * @param $classes
	 *
	 * @return string
	 */
	public function get_html_class( $classes ) {
		if ( is_user_logged_in() && ! is_customize_preview() ) {
			if ( get_user_meta( get_current_user_id(), 'show_admin_bar_front', true ) !== 'false' ) {
				$classes .= ' admin-bar';
			}
		}

		apply_filters( 'stax_html_classes', $classes );

		return $classes;
	}

}
