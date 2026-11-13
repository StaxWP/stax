<?php
/**
 * Google Fonts selected in the Customizer, served from the site itself.
 *
 * The first time a font combination is needed, the theme downloads the stylesheet and the
 * font files from Google once (server side) into the core fonts folder (uploads/fonts/stax/) and then serves them
 * locally. Visitors' browsers never contact Google. If the download fails, the stylesheet is
 * retried a day later and, meanwhile, the fallback system fonts from the font stack are used.
 *
 * @package stax
 */

namespace Stax\Customizer;

/**
 * Class Font_Manager
 */
class Font_Manager {

	/**
	 * Font families requested for the current page, as family => weights.
	 *
	 * @var array
	 */
	public static $font_families = [];

	/**
	 * Allowed font weights.
	 */
	const WEIGHTS = [ '100', '200', '300', '400', '500', '600', '700', '800', '900' ];

	/**
	 * Add a Google font for the current page.
	 *
	 * @param string $font_family Font family.
	 * @param string $font_weight Font weight.
	 */
	public static function add_google_font( $font_family, $font_weight = '400' ) {
		if ( empty( $font_family ) ) {
			$font_family = get_theme_mod( 'stax_body_font_family' );

			if ( empty( $font_family ) ) {
				return;
			}
		}

		$font_weight = in_array( (string) $font_weight, self::WEIGHTS, true ) ? (string) $font_weight : '400';

		if ( ! isset( self::$font_families[ $font_family ] ) ) {
			self::$font_families[ $font_family ] = [];
		}

		if ( ! in_array( $font_weight, self::$font_families[ $font_family ], true ) ) {
			self::$font_families[ $font_family ][] = $font_weight;
		}
	}

	/**
	 * Function that is run after instantiation.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_google_fonts' ], 200 );
		add_action( 'enqueue_block_assets', [ $this, 'register_editor_fonts' ], 200 );
		add_action( 'customize_save_after', [ __CLASS__, 'flush_failures' ] );
	}

	/**
	 * Fonts inside the block editor iframe (enqueue_block_assets also runs on the front end).
	 */
	public function register_editor_fonts() {
		if ( ! is_admin() ) {
			return;
		}

		// The editor collects iframe assets before the dynamic CSS ran; generate it to know the fonts.
		if ( empty( self::$font_families ) && class_exists( Dynamic_Css::class ) ) {
			( new Dynamic_Css() )->get_style();
		}

		$this->register_google_fonts();
	}

	/**
	 * Enqueue the fonts selected in the Customizer.
	 */
	public function register_google_fonts() {
		$google_fonts = \Stax_Assets::instance()->get_google_fonts();

		foreach ( self::$font_families as $font_family => $font_weights ) {
			if ( ! $google_fonts || ! in_array( $font_family, $google_fonts, true ) ) {
				continue;
			}

			// The Customizer preview needs every weight while the user experiments.
			$weights = is_customize_preview() ? self::WEIGHTS : array_unique( array_merge( $font_weights, [ '400' ] ) );
			sort( $weights );

			$url = self::get_stylesheet_url( $font_family, $weights );

			if ( $url ) {
				wp_enqueue_style( 'stax-google-font-' . sanitize_title( $font_family ), $url, [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- The URL is content addressed.
			}
		}
	}

	/**
	 * Google Fonts css2 URL for a family and weights.
	 *
	 * @param string $family  Font family.
	 * @param array  $weights Font weights.
	 *
	 * @return string
	 */
	public static function get_google_url( $family, $weights ) {
		return 'https://fonts.googleapis.com/css2?family=' . str_replace( '%20', '+', rawurlencode( trim( $family ) ) ) . ':wght@' . implode( ';', $weights ) . '&display=swap';
	}

	/**
	 * Local stylesheet URL for a family and weights, downloading the files on first use.
	 *
	 * @param string $family  Font family.
	 * @param array  $weights Font weights.
	 *
	 * @return string Local URL, or an empty string when the fonts are not available locally.
	 */
	public static function get_stylesheet_url( $family, $weights ) {
		/**
		 * Filters whether Google Fonts are stored and served locally (default true).
		 * Returning false loads them from fonts.googleapis.com, as Stax 1.x did.
		 *
		 * @param bool $local Whether to serve fonts locally.
		 */
		if ( ! apply_filters( 'stax_local_google_fonts', true ) ) {
			return self::get_google_url( $family, $weights );
		}

		$remote = self::get_google_url( $family, $weights );
		$dir    = self::get_dir();

		if ( ! $dir ) {
			return '';
		}

		$file = 'font-' . substr( md5( $remote ), 0, 12 ) . '.css';

		if ( file_exists( $dir['path'] . '/' . $file ) ) {
			return $dir['url'] . '/' . $file;
		}

		$failures = get_option( 'stax_font_failures', [] );
		$key      = md5( $remote );

		if ( ! empty( $failures[ $key ] ) && $failures[ $key ] > time() - DAY_IN_SECONDS ) {
			return '';
		}

		if ( ! self::download( $remote, $dir, $file ) ) {
			$failures         = is_array( $failures ) ? $failures : [];
			$failures[ $key ] = time();
			update_option( 'stax_font_failures', $failures, false );

			return '';
		}

		return $dir['url'] . '/' . $file;
	}

	/**
	 * Downloads a Google stylesheet and its font files and writes a local stylesheet.
	 *
	 * @param string $remote Google stylesheet URL.
	 * @param array  $dir    Target folder (path, url).
	 * @param string $file   Local stylesheet file name.
	 *
	 * @return bool
	 */
	protected static function download( $remote, $dir, $file ) {
		$filesystem = self::filesystem();

		if ( ! $filesystem || ! wp_mkdir_p( $dir['path'] ) ) {
			return false;
		}

		// A current browser user agent makes Google return WOFF2 files.
		$response = wp_safe_remote_get(
			$remote,
			[
				'timeout'    => 10,
				'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36',
			]
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$css = wp_remote_retrieve_body( $response );

		if ( ! preg_match_all( '#url\((https://fonts\.gstatic\.com/[^)\s]+\.woff2?)\)#', $css, $matches ) ) {
			return false;
		}

		foreach ( array_unique( $matches[1] ) as $font_url ) {
			$name = basename( wp_parse_url( $font_url, PHP_URL_PATH ) );
			$name = sanitize_file_name( substr( md5( $font_url ), 0, 8 ) . '-' . $name );

			if ( ! file_exists( $dir['path'] . '/' . $name ) ) {
				$font = wp_safe_remote_get( $font_url, [ 'timeout' => 10 ] );

				if ( is_wp_error( $font ) || 200 !== (int) wp_remote_retrieve_response_code( $font ) ) {
					return false;
				}

				if ( ! $filesystem->put_contents( $dir['path'] . '/' . $name, wp_remote_retrieve_body( $font ), FS_CHMOD_FILE ) ) {
					return false;
				}
			}

			$css = str_replace( $font_url, './' . $name, $css );
		}

		return (bool) $filesystem->put_contents( $dir['path'] . '/' . $file, "/* Google Fonts served locally by the Stax theme. */\n" . $css, FS_CHMOD_FILE );
	}

	/**
	 * Folder for local fonts: <fonts dir>/stax, where <fonts dir> is the core Font Library folder (wp_get_font_dir()).
	 *
	 * @return array|false Array with path and url, or false.
	 */
	public static function get_dir() {
		$fonts = wp_get_font_dir();

		if ( empty( $fonts['path'] ) ) {
			return false;
		}

		return [
			'path' => untrailingslashit( $fonts['path'] ) . '/stax',
			'url'  => set_url_scheme( untrailingslashit( $fonts['url'] ) . '/stax' ),
		];
	}

	/**
	 * Direct filesystem access (used only to store the font files).
	 *
	 * @return \WP_Filesystem_Base|false
	 */
	protected static function filesystem() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( 'direct' !== get_filesystem_method( [], WP_CONTENT_DIR ) || ! WP_Filesystem() ) {
			return false;
		}

		return $wp_filesystem;
	}

	/**
	 * After saving the Customizer, retry fonts that failed to download.
	 */
	public static function flush_failures() {
		delete_option( 'stax_font_failures' );
	}
}
