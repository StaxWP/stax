<?php
/**
 * Front-end assets and content helpers (class kept global as Stax_Assets for child themes).
 *
 * @package stax
 */

use function Stax\stax;
use Stax\Customizer\Config;

/**
 * Class for managing theme stylesheets and scripts.
 */
class Stax_Assets {

	/**
	 * Stores the class instance
	 *
	 * @var Stax_Assets The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @var string
	 */
	protected $min = '';

	/**
	 * Associative array of CSS files, as $handle => $data pairs.
	 * Do not access this property directly, instead use the `get_css_files()` method.
	 *
	 * @var array
	 */
	protected $css_files;

	/**
	 * Class Instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @return Stax_Assets
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Script handles removed in 2.0. They stay registered without a file so child themes
	 * and plugins that list them as dependencies keep working.
	 *
	 * @var string[]
	 */
	const LEGACY_SCRIPT_HANDLES = [
		'modernizr',
		'objectfit-pollyfill',
		'jquery-parallax-scroll',
		'hc-sticky',
		'css-vars-ponyfill',
		'plyr-polyfill',
		'plyr-init',
		'intersection-polyfill',
	];

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	private function __construct() {
		if ( ! stax()->get_option( Config::OPTION_MISC_DEV_MODE ) ) {
			$this->min = '.min';
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_scripts' ], 11 );
		add_action( 'wp_head', [ $this, 'action_print_js_class_script' ], 1 );

		if ( stax()->get_option( Config::OPTION_PERF_CSS_PRELOAD ) ) {
			add_action( 'wp_head', [ $this, 'action_preload_styles' ] );
		}

		add_filter( 'the_content', [ $this, 'add_article_tags' ] );
		add_filter( 'the_password_form', [ $this, 'alter_password_form_btn' ] );
		add_filter( 'the_content', [ $this, 'add_lightbox_to_images' ], 10, 1 );
	}

	/**
	 * Swaps the `no-js` class for `js` and flags touch devices, before the first paint.
	 * Replaces the Modernizr build used until 1.2.2.
	 */
	public function action_print_js_class_script() {
		if ( stax()->is_amp() ) {
			return;
		}

		wp_print_inline_script_tag(
			"(function(c){c.remove('no-js');c.add('js');c.add(('ontouchstart' in window||navigator.maxTouchPoints>0)?'touchevents':'no-touchevents');})(document.documentElement.classList);"
		);
	}

	/**
	 * Kept for 1.x child themes. Nothing is needed on single posts any more.
	 */
	public function enqueue_single_post() {}

	/**
	 * Registers a theme script that loads deferred in the footer.
	 *
	 * @param string $handle Script handle.
	 * @param string $file   Path relative to the theme, without the `.js` / `.min.js` suffix.
	 * @param array  $deps   Dependencies.
	 */
	protected function register_script( $handle, $file, $deps = [] ) {
		wp_register_script(
			$handle,
			get_theme_file_uri( $file . $this->min . '.js' ),
			$deps,
			stax()->get_asset_version( get_theme_file_path( $file . $this->min . '.js' ) ),
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);
	}

	/**
	 * Registers and enqueues scripts. No jQuery on the front end.
	 */
	public function action_enqueue_scripts() {
		foreach ( self::LEGACY_SCRIPT_HANDLES as $handle ) {
			wp_register_script( $handle, false, [], stax()->get_version(), true );
		}

		$this->register_script( 'swiper', 'assets/js/swiper/swiper' );
		$this->register_script( 'stax-lightbox', 'assets/js/lightbox' );
		$this->register_script( 'stax-hfg', 'assets/js/hfg/app' );
		$this->register_script( 'stax', 'assets/js/index', [ 'stax-hfg' ] );

		// 1.x handle name, now the jQuery-free lightbox.
		wp_register_script( 'lightbox', false, [ 'stax-lightbox' ], stax()->get_version(), true );

		wp_add_inline_script(
			'stax',
			'var staxSettings = ' . wp_json_encode( [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] ) . ';',
			'before'
		);

		wp_localize_script(
			'stax-lightbox',
			'staxLightbox',
			[
				'label'    => __( 'Image viewer', 'stax' ),
				'close'    => __( 'Close', 'stax' ),
				'previous' => __( 'Previous image', 'stax' ),
				'next'     => __( 'Next image', 'stax' ),
			]
		);

		wp_enqueue_script( 'stax' );
	}

	/**
	 * Enqueue swiper style & script
	 */
	public function enqueue_swiper() {
		wp_enqueue_style( 'swiper' );
		wp_enqueue_script( 'swiper' );
	}

	/**
	 * Enqueue masonry (core script, no jQuery).
	 */
	public function enqueue_masonry() {
		wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'imagesloaded' );
	}

	/**
	 * Enqueue comments reply script
	 */
	public function enqueue_comments_reply() {
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue the lightbox.
	 */
	public function enqueue_lightbox() {
		wp_enqueue_style( 'stax-lightbox' );
		wp_enqueue_script( 'stax-lightbox' );
	}

	/**
	 * Kept for 1.x child themes. Sticky sidebars use CSS `position: sticky` now.
	 */
	public function enqueue_hc_sticky() {}

	/**
	 * Kept for 1.x child themes. Post format audio and video use the native players now.
	 */
	public function enqueue_plyr() {}

	/**
	 * Kept for 1.x child themes. The 404 shapes parallax is part of the main script now.
	 */
	public function enquque_jquery_parallax() {}

	/**
	 * Whether to load the full 1.x stylesheet instead of the lean 2.0 one
	 * (Customizer > Performance > "Load the full 1.x stylesheet").
	 *
	 * @return bool
	 */
	public function use_legacy_css() {
		return (bool) apply_filters( 'stax_use_legacy_css', (bool) stax()->get_option( Config::OPTION_PERF_LEGACY_CSS ) );
	}

	/**
	 * Compatibility stylesheets (assets/css/compat/<name>.css) for the active plugins.
	 *
	 * @return string[]
	 */
	public static function get_active_compat_styles() {
		$styles = [
			'woocommerce' => class_exists( 'WooCommerce' ),
			'buddypress'  => function_exists( 'buddypress' ),
			'bbpress'     => class_exists( 'bbPress' ),
			'elementor'   => defined( 'ELEMENTOR_VERSION' ),
			'lms'         => defined( 'LEARNDASH_VERSION' ) || class_exists( 'LifterLMS' ) || function_exists( 'tutor' ),
			'edd'         => class_exists( 'Easy_Digital_Downloads' ),
		];

		/**
		 * Filters which compatibility stylesheets load.
		 *
		 * @param bool[] $styles Map of compat stylesheet name => whether to load it.
		 */
		$styles = apply_filters( 'stax_compat_styles', $styles );

		return array_keys( array_filter( $styles ) );
	}

	/**
	 * Gets all CSS files.
	 *
	 * @return array Associative array of $handle => $data pairs.
	 */
	protected function get_css_files() {
		if ( is_array( $this->css_files ) ) {
			return $this->css_files;
		}

		$css_files = [
			'stax-global'     => [
				'file'             => $this->use_legacy_css() ? 'assets/css/legacy/stax-1x.min.css' : 'assets/css/index' . $this->min . '.css',
				'global'           => true,
				'preload_callback' => '__return_true',
			],
			'stax-header'     => [
				'file'   => 'inc/Customizer/builder/assets/css/style' . $this->min . '.css',
				'deps'   => [ 'stax-global' ],
				'global' => true,
			],
			'stax-icon-fonts' => [
				'file'   => 'assets/fonts/style.css',
				// Bundled into the lean stylesheet; a separate file only with the 1.x stylesheet.
				'global' => $this->use_legacy_css(),
			],
			'swiper'          => [
				'file' => 'assets/js/swiper/swiper' . $this->min . '.css',
			],
			'stax-lightbox'   => [
				'file' => 'assets/css/lightbox' . $this->min . '.css',
			],
		];

		// Plugin compatibility styles load only when the plugin is active.
		// The 1.x stylesheet already contains them.
		if ( ! $this->use_legacy_css() ) {
			foreach ( self::get_active_compat_styles() as $name ) {
				$file = 'assets/css/compat/' . $name . $this->min . '.css';

				if ( ! is_readable( get_theme_file_path( $file ) ) || ! filesize( get_theme_file_path( $file ) ) ) {
					continue;
				}

				$css_files[ 'stax-' . $name ] = [
					'file'   => $file,
					'deps'   => [ 'stax-global' ],
					'global' => true,
				];
			}
		}

		/**
		 * Filters default CSS files.
		 *
		 * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
		 *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
		 *                         directory), and optionally 'global' (whether the file should immediately be
		 *                         enqueued instead of just being registered) and 'preload_callback' (callback)
		 *                         function determining whether the file should be preloaded for the current request).
		 */
		$css_files = apply_filters( 'stax_css_files', $css_files );

		$this->css_files = [];
		foreach ( $css_files as $handle => $data ) {
			if ( is_string( $data ) ) {
				$data = [ 'file' => $data ];
			}

			if ( empty( $data['file'] ) ) {
				continue;
			}

			$this->css_files[ $handle ] = array_merge(
				[
					'deps'             => [],
					'global'           => false,
					'preload_callback' => null,
				],
				$data
			);
		}

		return $this->css_files;
	}

	/**
	 * Registers or enqueues stylesheets.
	 *
	 * Stylesheets that are global are enqueued. All other stylesheets are only registered, to be enqueued later.
	 */
	public function action_enqueue_styles() {
		$css_files = $this->get_css_files();

		foreach ( $css_files as $handle => $data ) {
			$src     = get_theme_file_uri( $data['file'] );
			$version = Stax\stax()->get_asset_version( get_theme_file_path( $data['file'] ) );

			// Enqueue global stylesheets immediately, register the ones for later use.
			if ( $data['global'] ) {
				wp_enqueue_style( $handle, $src, $data['deps'], $version );
			} else {
				wp_register_style( $handle, $src, $data['deps'], $version );
			}

			wp_style_add_data( $handle, 'precache', true );
		}
	}


	/**
	 * Preloads in-body stylesheets depending on what templates are being used.
	 *
	 * Only stylesheets that have a 'preload_callback' provided will be considered. If that callback evaluates to true
	 * for the current request, the stylesheet will be preloaded.
	 *
	 * Preloading is disabled when AMP is active, as AMP injects the stylesheets inline.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
	 */
	public function action_preload_styles() {
		// If the AMP plugin is active, return early.
		if ( Stax\stax()->is_amp() ) {
			return;
		}

		$wp_styles = wp_styles();
		$css_files = $this->get_css_files();

		foreach ( $css_files as $handle => $data ) {

			// Skip if stylesheet not registered.
			if ( ! isset( $wp_styles->registered[ $handle ] ) ) {
				continue;
			}

			// Skip if no preload callback provided.
			if ( ! is_callable( $data['preload_callback'] ) ) {
				continue;
			}

			// Skip if preloading is not necessary for this request.
			if ( ! call_user_func( $data['preload_callback'] ) ) {
				continue;
			}

			$preload_uri = $wp_styles->registered[ $handle ]->src . '?ver=' . $wp_styles->registered[ $handle ]->ver;

			echo '<link rel="preload" id="' . esc_attr( $handle ) . '-preload" href="' . esc_url( $preload_uri ) . '" as="style">';
			echo "\n";
		}
	}

	/**
	 * Add tags
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public function add_article_tags( $content ) {
		if ( is_single() ) {
			ob_start();
			get_template_part( 'template-parts/content/entry-taxonomies', get_post_type() );
			$tags     = ob_get_clean();
			$content .= $tags;

			remove_filter( 'the_content', [ $this, 'add_article_tags' ], 20 );
		}

		return $content;
	}

	/**
	 * Get the first quote from a post
	 *
	 * @param $content
	 *
	 * @return mixed|string
	 */
	public function get_content_quote( $content ) {
		preg_match( '/<blockquote(.*?)>(.|\n)*?<\/blockquote>/i', $content, $matches );
		if ( isset( $matches[0] ) ) {
			if ( strpos( $matches[0], 'class=' ) !== false ) {
				$matches[0] = str_replace( 'is-style-large', '', $matches[0] );
				$matches[0] = str_replace( 'wp-block-quote', 'wp-block-quote is-style-large', $matches[0] );

				return $matches[0];
			} else {
				return str_replace( '<blockquote', '<blockquote class="wp-block-quote is-style-large"', $matches[0] );
			}
		}

		return '';
	}

	/**
	 * Remove the first quote from single post format quote
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public function remove_first_content_quote( $content ) {
		if ( get_post_format() === 'quote' && is_main_query() ) {
			$content = preg_replace( '/<blockquote(.*?)>(.|\n)*?<\/blockquote>/i', '', $content, 1 );
			remove_filter( 'the_content', [ $this, 'remove_first_content_quote' ] );
		}

		return $content;
	}

	/**
	 * Add custom button class to the password form
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function alter_password_form_btn( $form ) {
		$form = str_replace( 'name="Submit"', 'name="Submit" class="btn btn-dark"', $form );

		return $form;
	}

	/**
	 * Opens image links in post content in the theme lightbox (grouped per post).
	 * The lightbox script only loads when a page has such links.
	 *
	 * @param string $content Post content.
	 *
	 * @return string
	 */
	public function add_lightbox_to_images( $content ) {
		if ( ! is_string( $content ) || false === stripos( $content, '<a' ) ) {
			return $content;
		}

		$group = 'svq-post-' . get_the_ID();
		$count = 0;

		$content = preg_replace_callback(
			'/<a\s[^>]*href=(["\'])https?:[^"\']+\.(?:jpe?g|gif|png|bmp|webp|avif)\1[^>]*>/i',
			function ( array $matches ) use ( $group ) {
				$tag = $matches[0];

				if ( false !== strpos( $tag, 'svq-gallery__image-link' ) || false !== strpos( $tag, 'data-lightbox' ) ) {
					return $tag;
				}

				if ( preg_match( '/\sclass=(["\'])/', $tag ) ) {
					$tag = preg_replace( '/\sclass=(["\'])/', ' class=$1svq-lightbox-img ', $tag, 1 );
				} else {
					$tag = substr_replace( $tag, ' class="svq-lightbox-img"', 2, 0 );
				}

				return substr_replace( $tag, ' data-lightbox="' . esc_attr( $group ) . '"', 2, 0 );
			},
			$content,
			-1,
			$count
		);

		if ( $count ) {
			$this->enqueue_lightbox();
		}

		return $content;
	}

	/**
	 * Get standard fonts
	 *
	 * @return array
	 */
	public function get_standard_fonts() {
		return apply_filters(
			'stax_standard_fonts_array',
			Config::STANDARD_FONTS
		);
	}

	/**
	 * Get all google fonts.
	 *
	 * @return array
	 */
	public function get_google_fonts() {
		return apply_filters(
			'stax_google_fonts_array',
			Config::GOOGLE_FONTS
		);
	}

	/**
	 * Get Global Colors Default
	 *
	 * @return array
	 */
	public function get_global_colors_default( $version = '2.0' ) {
		$vars = [
			[
				'label'    => __( 'Body Background', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_BODY_BG,
				'contrast' => Config::VAR_BODY_BG_CONTRAST,
				'preview'  => true,
			],
			[
				'label'    => __( 'Text Color', 'stax' ),
				'output'   => 'hex',
				'vars'     => Config::VAR_TEXT_COLOR,
				'contrast' => false,
				'preview'  => true,
			],
			[
				'label'    => __( 'Heading Color', 'stax' ),
				'output'   => 'hex',
				'vars'     => Config::VAR_HEADING_COLOR,
				'contrast' => false,
				'preview'  => false,
			],
			[
				'label'    => __( 'Primary Color', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_PRIMARY_COLOR,
				'contrast' => Config::VAR_PRIMARY_COLOR_CONTRAST,
				'preview'  => true,
			],
			[
				'label'    => __( 'Light Color', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_LIGHT_COLOR,
				'contrast' => Config::VAR_LIGHT_COLOR_CONTRAST,
				'preview'  => false,
			],
			[
				'label'    => __( 'Dark Color', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_DARK_COLOR,
				'contrast' => Config::VAR_DARK_COLOR_CONTRAST,
				'preview'  => false,
			],
			[
				'label'    => __( 'Link Color', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_LINK_COLOR,
				'contrast' => false,
				'preview'  => true,
			],
			[
				'label'    => __( 'Border Color', 'stax' ),
				'output'   => 'hex',
				'vars'     => Config::VAR_BORDER_COLOR,
				'contrast' => false,
				'preview'  => false,
			],
			[
				'label'    => __( 'Meta Color', 'stax' ),
				'output'   => 'hex',
				'vars'     => Config::VAR_TEXT_META_COLOR,
				'contrast' => false,
				'preview'  => false,
			],
			[
				'label'    => __( 'Archive Title Background', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_ARCHIVE_TITLE_COLOR,
				'contrast' => Config::VAR_ARCHIVE_TITLE_COLOR_CONTRAST,
				'preview'  => false,
			],
			[
				'label'    => __( 'Related Section Background', 'stax' ),
				'output'   => 'hsl',
				'vars'     => Config::VAR_RELATED_COLOR,
				'contrast' => Config::VAR_RELATED_COLOR_CONTRAST,
				'preview'  => false,
			],
		];

		$colors = [
			// 2.0: link and meta colors darkened to reach WCAG AA contrast (4.5:1) on white.
			'light'  => [ '#ffffff', '#4b4f56', '#242424', '#6acfc9', '#eef8f9', '#242424', '#0e7c75', '#ddebed', '#6a6e75', '#6ACFC9', '#EEF8F9' ],
			'dark'   => [ '#121212', '#888888', '#d0d0d0', '#d86252', '#1a1a1a', '#e0e0e0', '#d86252', '#383838', '#888888', '#0c0b0b', '#0c0b0b' ],
			'purple' => [ '#ffffff', '#4b4f56', '#242424', '#621aa5', '#fafafa', '#242424', '#8734d4', '#e5e5e5', '#6a6e75', '#6ACFC9', '#EEF8F9' ],
		];

		// The 1.x defaults, saved on sites updated from 1.x so their colors do not change.
		if ( '1.x' === $version ) {
			$colors['light'][6]  = '#1aaaa0';
			$colors['light'][8]  = '#90949c';
			$colors['purple'][8] = '#90949c';
		}

		$palettes = [
			'activePalette' => 'light',
			'palettes'      => [
				'light'  => [
					'name'          => __( 'Light', 'stax' ),
					'allowDeletion' => false,
					'colors'        => [],
				],
				'dark'   => [
					'name'          => __( 'Dark', 'stax' ),
					'allowDeletion' => false,
					'colors'        => [],
				],
				'purple' => [
					'name'          => __( 'Purple Accent', 'stax' ),
					'allowDeletion' => false,
					'colors'        => [],
				],
			],
		];

		foreach ( $palettes['palettes'] as $type => &$palette ) {
			foreach ( $vars as $key => $var ) {
				$var['color']        = $colors[ $type ][ $key ];
				$palette['colors'][] = $var;
			}
		}

		return $palettes;
	}

	/**
	 * Get the body typography defaults.
	 *
	 * @return array
	 */
	public function get_body_typography_defaults() {
		return [
			'fontSize'      => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'font-size-sm',
					'font-size-md',
					'font-size-lg',
				],
				'mobile'  => 1,
				'tablet'  => 1.13,
				'desktop' => 1.19,
			],
			'lineHeight'    => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'line-height-sm',
					'line-height-md',
					'line-height-lg',
				],
				'mobile'  => 1.5,
				'tablet'  => 1.6,
				'desktop' => 1.7,
			],
			'letterSpacing' => [
				'suffix'  => [
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				],
				'vars'    => [
					'letter-spacing-sm',
					'letter-spacing-md',
					'letter-spacing-lg',
				],
				'mobile'  => 0.03,
				'tablet'  => 0.03,
				'desktop' => 0.03,
			],
		];
	}

	/**
	 * Get headings defaults
	 *
	 * @return array
	 */
	public function get_headings_defaults() {
		return [
			'h1' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h1-font-size-sm',
					'h1-font-size-md',
					'h1-font-size-lg',
				],
				'mobile'  => 1.88,
				'tablet'  => 2.63,
				'desktop' => 3,
			],
			'h2' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h2-font-size-sm',
					'h2-font-size-md',
					'h2-font-size-lg',
				],
				'mobile'  => 1.5,
				'tablet'  => 2.13,
				'desktop' => 2.63,
			],
			'h3' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h3-font-size-sm',
					'h3-font-size-md',
					'h3-font-size-lg',
				],
				'mobile'  => 1.31,
				'tablet'  => 1.75,
				'desktop' => 2.13,
			],
			'h4' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h4-font-size-sm',
					'h4-font-size-md',
					'h4-font-size-lg',
				],
				'mobile'  => 1.25,
				'tablet'  => 1.5,
				'desktop' => 1.75,
			],
			'h5' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h5-font-size-sm',
					'h5-font-size-md',
					'h5-font-size-lg',
				],
				'mobile'  => 1.13,
				'tablet'  => 1.25,
				'desktop' => 1.5,
			],
			'h6' => [
				'suffix'  => [
					'mobile'  => 'rem',
					'tablet'  => 'rem',
					'desktop' => 'rem',
				],
				'vars'    => [
					'h6-font-size-sm',
					'h6-font-size-md',
					'h6-font-size-lg',
				],
				'mobile'  => 1,
				'tablet'  => 1,
				'desktop' => 1,
			],
		];
	}

	/**
	 * Get default value for headings typography.
	 *
	 * @param string $heading_type the heading type [h1,h2,...h6].
	 *
	 * @return array
	 */
	public function get_headings_typography_defaults( $heading_type ) {
		$default_value = [
			'lineHeight'    => [
				'suffix'  => [
					'mobile'  => 'em',
					'tablet'  => 'em',
					'desktop' => 'em',
				],
				'vars'    => [
					$heading_type . '-line-height-sm',
					$heading_type . '-line-height-md',
					$heading_type . '-line-height-lg',
				],
				'mobile'  => 1.2,
				'tablet'  => 1.2,
				'desktop' => 1.2,
			],
			'letterSpacing' => [
				'suffix'  => [
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				],
				'vars'    => [
					$heading_type . '-letter-spacing-sm',
					$heading_type . '-letter-spacing-md',
					$heading_type . '-letter-spacing-lg',
				],
				'mobile'  => 0,
				'tablet'  => 0,
				'desktop' => 0,
			],
			'fontWeight'    => '900',
			'fontSize'      => self::instance()->get_headings_defaults()[ $heading_type ],
		];

		return $default_value;
	}

	/**
	 * Get the grid typography defaults.
	 *
	 * @return array
	 */
	public function get_grid_typography_defaults( $section ) {
		$data = [
			'title' => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-card-title-sm',
						'fs-article-card-title-md',
						'fs-article-card-title-lg',
					],
					'mobile'  => 1.31,
					'tablet'  => 1.5,
					'desktop' => 1.75,
				],
			],
			'text'  => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-list-card-sm',
						'fs-article-list-card-md',
						'fs-article-list-card-lg',
					],
					'mobile'  => 1,
					'tablet'  => 1,
					'desktop' => 1.15,
				],
			],
		];

		return isset( $data[ $section ] ) ? $data[ $section ] : [];
	}

	/**
	 * Get the normal list typography defaults.
	 *
	 * @return array
	 */
	public function get_normal_list_typography_defaults( $section ) {
		$data = [
			'title' => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-list-title-sm',
						'fs-article-list-title-md',
						'fs-article-list-title-lg',
					],
					'mobile'  => 1.31,
					'tablet'  => 1.5,
					'desktop' => 1.75,
				],
			],
			'text'  => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-list-sm',
						'fs-article-list-md',
						'fs-article-list-lg',
					],
					'mobile'  => 1,
					'tablet'  => 1,
					'desktop' => 1.15,
				],
			],
		];

		return isset( $data[ $section ] ) ? $data[ $section ] : [];
	}

	/**
	 * Get the big list typography defaults.
	 *
	 * @return array
	 */
	public function get_big_list_typography_defaults( $section ) {
		$data = [
			'title' => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-big-title-sm',
						'fs-article-big-title-md',
						'fs-article-big-title-lg',
					],
					'mobile'  => 1.5,
					'tablet'  => 1.75,
					'desktop' => 2.25,
				],
			],
			'text'  => [
				'fontSize' => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
					'vars'    => [
						'fs-article-big-sm',
						'fs-article-big-md',
						'fs-article-big-lg',
					],
					'mobile'  => 1,
					'tablet'  => 1,
					'desktop' => 1.25,
				],
			],
		];

		return isset( $data[ $section ] ) ? $data[ $section ] : [];
	}
}

Stax_Assets::instance();
