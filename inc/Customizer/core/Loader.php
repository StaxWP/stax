<?php

namespace Stax\Customizer\Core;

use Stax_Assets;

use function Stax\stax;

class Loader {

	/**
	 * Customizer modules.
	 *
	 * @var array
	 */
	private $customizer_modules = [];

	/**
	 * Loader constructor.
	 */
	public function __construct() {
		add_action( 'customize_preview_init', [ $this, 'enqueue_customizer_preview' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'set_featured_image' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_customizer_controls' ] );
	}

	/**
	 * Initialize the customizer functionality
	 */
	public function init() {
		global $wp_customize;

		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$this->define_modules();
		$this->load_modules();
	}

	/**
	 * Define the modules that will be loaded.
	 */
	private function define_modules() {
		$this->customizer_modules = apply_filters(
			'stax_filter_customizer_modules',
			[
				[
					'class' => 'Stax\Customizer\Core\Options\Main',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Main.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Colors',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Colors.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Typography',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Typography.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Layout_Container',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Layout_Container.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Layout_General',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Layout_General.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Layout_Post',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Layout_Post.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Layout_Archive',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Layout_Archive.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Single_Post_General',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Single_Post_General.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Single_Post_Navigation',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Single_Post_Navigation.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Single_Post_Related',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Single_Post_Related.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Single_Page',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Single_Page.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Archive_List',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Archive_List.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Misc',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Misc.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Performance',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Performance.php',
				],
				[
					'class' => 'Stax\Customizer\Core\Options\Not_Found',
					'path'  => get_template_directory() . '/inc/Customizer/core/options/Not_Found.php',
				],
			]
		);
	}

	/**
	 * Enqueue customizer controls script.
	 */
	public function enqueue_customizer_controls() {
		wp_enqueue_style(
			'stax-customizer-style',
			get_theme_file_uri( 'assets/css/customizer/customizer-style.min.css' ),
			[],
			stax()->get_version()
		);

		wp_enqueue_script(
			'stax-customizer-controls',
			get_theme_file_uri( 'assets/js/customizer/customizer-controls.js' ),
			[ 'jquery', 'wp-color-picker' ],
			stax()->get_version(),
			true
		);

		$bundle_path = get_template_directory_uri() . '/inc/Customizer/core/controls/react/bundle/';

		wp_register_script(
			'react-controls',
			$bundle_path . 'controls.js',
			[ 'lodash', 'react', 'react-dom', 'wp-components', 'wp-element', 'wp-i18n', 'wp-media-utils', 'wp-polyfill', 'wp-primitives' ],
			stax()->get_version(),
			true
		);

		global $wp_version;

		wp_localize_script(
			'react-controls',
			'StaxReactCustomize',
			apply_filters(
				'stax_react_controls_localization',
				[
					'nonce'                         => wp_create_nonce( 'wp_rest' ),
					'headerControls'                => [],
					'instructionalVid'              => '',
					'dynamicTags'                   => [
						'controls' => [],
						'options'  => [],
					],
					'fonts'                         => [
						'System' => Stax_Assets::instance()->get_standard_fonts(),
						'Google' => Stax_Assets::instance()->get_google_fonts(),
					],
					'fontVariants'                  => Stax_Assets::instance()->get_google_fonts(),
					'hideConditionalHeaderSelector' => true,
					'dashUpdatesMessage'            => sprintf( 'Please %s to the latest version of Stax to manage the conditional headers.', '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">' . __( 'update', 'stax' ) . '</a>' ),
					'bundlePath'                    => $bundle_path,
					'shouldUseColorPickerFix'       => (int) ( ! version_compare( $wp_version, '5.8', '>=' ) ),
				]
			)
		);

		wp_enqueue_script( 'react-controls' );

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'react-controls', 'stax' );
		}

		wp_register_style( 'react-controls', $bundle_path . 'style-controls.css', [ 'wp-components' ], stax()->get_version() );
		wp_enqueue_style( 'react-controls' );

		wp_enqueue_style(
			'stax-fonts-control-google-fonts',
			'https://fonts.googleapis.com/css?family=' . join( '|', Stax_Assets::instance()->get_google_fonts() ) . '&text=Abc&display=swap"',
			[],
			stax()->get_version()
		);
	}

	/**
	 * Enqueue customizer preview script.
	 */
	public function enqueue_customizer_preview() {
		wp_enqueue_style(
			'stax-customizer-preview-style',
			get_theme_file_uri( 'assets/css/customizer/customizer-preview.min.css' ),
			[],
			stax()->get_version()
		);

		wp_register_script(
			'stax-customizer-preview',
			get_theme_file_uri( 'assets/js/customizer/customizer-preview.js' ),
			[],
			stax()->get_version(),
			true
		);

		wp_localize_script(
			'stax-customizer-preview',
			'staxCustomizePreview',
			apply_filters(
				'stax_customize_preview_localization',
				[
					'currentFeaturedImage' => '',
					'newBuilder'           => true,
					'newSkin'              => true,
				]
			)
		);

		wp_enqueue_script( 'stax-customizer-preview' );
	}

	/**
	 * Save featured image in previously localized object.
	 */
	public function set_featured_image() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$thumbnail = get_the_post_thumbnail_url();

		if ( false === $thumbnail ) {
			return;
		}

		wp_add_inline_script(
			'stax-customizer-preview',
			'staxCustomizePreview.currentFeaturedImage = "' . esc_url( get_the_post_thumbnail_url() ) . '";'
		);
	}

	/**
	 * Load the customizer modules.
	 *
	 * @return void
	 */
	private function load_modules() {
		foreach ( $this->customizer_modules as $module ) {
			require_once $module['path'];

			$class = new $module['class']();

			if ( null !== $class ) {
				$class->init();
			}
		}
	}
}
