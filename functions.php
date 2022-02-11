<?php
/**
 * Stax functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package stax
 */

use function Stax\stax;
use Stax\Customizer\Config;

define( 'STAX_MINIMUM_WP_VERSION', '5.0' );
define( 'STAX_MINIMUM_PHP_VERSION', '7.0' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], STAX_MINIMUM_WP_VERSION, '<' ) || version_compare( PHP_VERSION, STAX_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';

	return;
}

// Setup autoloader (via Composer or custom).
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require get_template_directory() . '/vendor/autoload.php';
} else {
	/**
	 * Custom autoloader function for theme classes.
	 *
	 * @access private
	 *
	 * @param string $class_name Class name to load.
	 *
	 * @return bool True if the class was loaded, false otherwise.
	 */
	function _stax_autoload( $class_name ) {
		$namespace = 'Stax';

		if ( strpos( $class_name, $namespace . '\\' ) !== 0 ) {
			return false;
		}

		$parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

		$path = get_template_directory() . '/inc';
		foreach ( $parts as $part ) {
			$path .= '/' . $part;
		}
		$path .= '.php';

		if ( ! file_exists( $path ) ) {
			return false;
		}

		require_once $path;

		return true;
	}

	spl_autoload_register( '_stax_autoload' );
}

// Load the `stax()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'Stax\stax' );

if ( ! isset( $content_width ) ) {
	$content_width = 1140;
}

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
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	private function __construct() {
		if ( ! stax()->get_option( Config::OPTION_MISC_DEV_MODE ) ) {
			$this->min = '.min';
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_scripts' ], 11 );

		if ( stax()->get_option( Config::OPTION_PERF_CSS_PRELOAD ) ) {
			add_action( 'wp_head', [ $this, 'action_preload_styles' ] );
		}

		add_action( 'wp_body_open', [ $this, 'enqueue_single_post' ] );

		add_filter( 'the_content', [ $this, 'add_article_tags' ] );
		add_filter( 'the_password_form', [ $this, 'alter_password_form_btn' ] );
		add_filter( 'the_content', [ $this, 'add_lightbox_to_images' ], 10, 1 );
	}

	/**
	 * Enqueue single posts Javascript files
	 */
	public function enqueue_single_post() {
		if ( is_single() || is_singular( 'page' ) ) {
			$this->enqueue_hc_sticky();
			$this->enqueue_lightbox();
			$this->enqueue_plyr();
		}
	}

	/**
	 * Registers or enqueues scripts.
	 */
	public function action_enqueue_scripts() {
		/* Footer scripts */

		wp_register_script(
			'modernizr',
			get_theme_file_uri( 'assets/js/modernizr-custom' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'objectfit-pollyfill',
			get_theme_file_uri( 'assets/js/object-fit/objectFitPolyfill.basic' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'jquery-parallax-scroll',
			get_theme_file_uri( 'assets/js/jquery.parallax-scroll' . $this->min . '.js' ),
			[ 'jquery' ],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'swiper',
			get_theme_file_uri( 'assets/js/swiper/swiper' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'lightbox',
			get_theme_file_uri( 'assets/js/lightbox/lightbox' . $this->min . '.js' ),
			[ 'jquery' ],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'hc-sticky',
			get_theme_file_uri( 'assets/js/hc-sticky/hc-sticky-all' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'css-vars-ponyfill',
			get_theme_file_uri( 'assets/js/css-vars/css-vars-ponyfill' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'plyr-polyfill',
			get_theme_file_uri( 'assets/js/plyr/plyr.polyfilled' . $this->min . '.js' ),
			[ 'jquery' ],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'plyr-init',
			get_theme_file_uri( 'assets/js/plyr-init' . $this->min . '.js' ),
			[ 'jquery', 'plyr-polyfill' ],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'intersection-polyfill',
			get_theme_file_uri( 'assets/js/intersection-observer' . $this->min . '.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'stax-hfg',
			get_theme_file_uri( 'assets/js/hfg/app.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_register_script(
			'stax',
			get_theme_file_uri( 'assets/js/index' . $this->min . '.js' ),
			[
				'jquery',
				'objectfit-pollyfill',
				'intersection-polyfill',
				'stax-hfg',
			],
			stax()->get_version(),
			true
		);

		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'css-vars-ponyfill' );
		wp_enqueue_script( 'stax' );

		$objectFitData = 'jQuery(document).ready(function($) { objectFitPolyfill(); });';

		wp_add_inline_script( 'objectfit-pollyfill', $objectFitData );

		$cssVarsData = 'jQuery(document).ready(function($) {cssVars();});';

		wp_add_inline_script( 'css-vars-ponyfill', $cssVarsData );
	}

	/**
	 * Enqueue swiper style & script
	 */
	public function enqueue_swiper() {
		wp_enqueue_style( 'swiper' );
		wp_enqueue_script( 'swiper' );
	}

	/**
	 * Enqueue masonry
	 */
	public function enqueue_masonry() {
		wp_enqueue_script( 'masonry' );
	}

	/**
	 * Enqueue comments reply script
	 */
	public function enqueue_comments_reply() {
		wp_enqueue_script( 'comment-reply', 'wp-includes/js/comment-reply', [], false, true );
	}

	/**
	 * Enqueue lightbox
	 */
	public function enqueue_lightbox() {
		wp_print_styles( [ 'lightbox' ] );
		wp_enqueue_script( 'lightbox' );
	}

	/**
	 * Enqueue code hc-sticky
	 */
	public function enqueue_hc_sticky() {
		wp_enqueue_script( 'hc-sticky' );
	}

	/**
	 * Enqueue code plyr
	 */
	public function enqueue_plyr() {
		wp_enqueue_script( 'plyr-polyfill' );
		wp_enqueue_script( 'plyr-init' );
	}

	/**
	 * Enqueue jquery parallax
	 */
	public function enquque_jquery_parallax() {
		wp_enqueue_script( 'jquery-parallax-scroll' );
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
				'file'             => 'assets/css/index' . $this->min . '.css',
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
				'global' => true,
			],
			'swiper'          => [
				'file' => 'assets/js/swiper/swiper' . $this->min . '.css',
			],
			'lightbox'        => [
				'file' => 'assets/js/lightbox/lightbox' . $this->min . '.css',
			],
		];

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
	 * @param $content
	 *
	 * @return null|string|string[]
	 */
	public function add_lightbox_to_images( $content ) {
		$classes      = 'svq-lightbox-img';
		$lightbox_tag = 'svq-post-' . get_the_ID();

		$content = preg_replace_callback(
			'/<a(.*?)href=(?:"|\')(?:http(?:s?):)(?:[\/|.|\w|-])*\.(?:jpg|gif|png|jpeg|bmp)(?:"|\')(.*?)>/i',
			function ( array $matches ) use ( $classes, $lightbox_tag ) {
				if ( isset( $matches[0] ) ) {
					if ( strpos( $matches[0], 'svq-gallery__image-link' ) !== false ) {
						return $matches[0];
					} elseif ( strpos( $matches[0], 'class=' ) !== false ) {
						$matches[0] = str_replace( 'class="', '<a data-lightbox="' . esc_attr( $lightbox_tag ) . '" class="' . esc_attr( $classes ) . ' ', $matches[0] );

						return $matches[0];
					} else {
						$matches[0] = str_replace( '<a', '<a data-lightbox="' . esc_attr( $lightbox_tag ) . '" class="' . esc_attr( $classes ) . '"', $matches[0] );

						return $matches[0];
					}
				}
			},
			$content
		);

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
	public function get_global_colors_default() {
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
			'light'  => [ '#ffffff', '#4b4f56', '#242424', '#6acfc9', '#eef8f9', '#242424', '#1aaaa0', '#ddebed', '#90949c', '#6ACFC9', '#EEF8F9' ],
			'dark'   => [ '#121212', '#888888', '#d0d0d0', '#d86252', '#1a1a1a', '#e0e0e0', '#d86252', '#383838', '#888888', '#0c0b0b', '#0c0b0b' ],
			'purple' => [ '#ffffff', '#4b4f56', '#242424', '#621aa5', '#fafafa', '#242424', '#8734d4', '#e5e5e5', '#90949c', '#6ACFC9', '#EEF8F9' ],
		];

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


/**
 * WP 5.2 compatibility
 */
if ( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * Render single main content
 */
function stax_show_single() {
	stax()->get_template_part( 'template-parts/single' );
}

add_action( 'stax_single', 'stax_show_single' );

/**
 * Render archive main content
 */
function stax_show_archive() {

	// Include archive panel. Except blog home.
	if ( ! is_home() ) {
		stax()->get_template_part( 'template-parts/content/panel', 'page' );
	}

	// Include posts loop
	stax()->get_template_part( 'template-parts/archive/index' );
}
add_action( 'stax_archive', 'stax_show_archive' );


/**
 * Displays the classes for the post container element.
 *
 * @param array       $class One or more classes to add to the class list.
 * @param int|WP_Post $post_id Optional. Post ID or post object. Defaults to the global `$post`.
 *
 * @since 1.0.0
 */
function stax_post_class( $class = [], $post_id = null ) {
	$post_classes = get_post_class( '', $post_id );

	if ( ( $key = array_search( 'has-post-thumbnail', $post_classes ) ) !== false ) {
		unset( $post_classes[ $key ] );
	}

	if ( ( $key = array_search( 'sticky', $post_classes ) ) === false && is_sticky( $post_id ) ) {
		$post_classes[] = 'sticky';
	}

	$post_classes = array_merge( $post_classes, $class );

	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . implode( ' ', $post_classes ) . '"';
}

/**
 * Add placeholder tag when it is enabled.
 *
 * @return void
 */
function stax_add_page_preloader() {
	if ( stax()->get_option( Config::OPTION_MISC_PRELOADER ) ) {
		echo '<div class="svq-page-loader"></div>';
	}
}
add_action( 'wp_body_open', 'stax_add_page_preloader', 1 );


/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_stax() {

	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/vendor/appsero/src/Client.php';
	}

	$client = new Appsero\Client( 'b090e7cc-42a2-43c2-99a1-5c19b06b9661', 'Stax', __FILE__ );

	// Active insights.
	$client->insights()->init();

}

appsero_init_tracker_stax();
