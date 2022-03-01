<?php

namespace Stax\Metabox;

use function Stax\stax;

final class Manager {

	/**
	 * Control instances.
	 *
	 * @var array
	 */
	private $controls = [];

	/**
	 * Control classes to get controls from.
	 *
	 * @var array
	 */
	private $control_classes;

	/**
	 * Init function
	 */
	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add' ] );
		add_action( 'admin_init', [ $this, 'define_controls' ] );
		add_action( 'admin_init', [ $this, 'load_controls' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'save_post', [ $this, 'save' ] );

		add_action( 'init', [ $this, 'register_meta' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'meta_sidebar_script_enqueue' ] );
	}

	/**
	 * Define the controls.
	 */
	public function define_controls() {
		$this->control_classes = [
			'Stax\\Metabox\\Main',
		];

		$this->control_classes = apply_filters( 'stax_filter_metabox_controls', $this->control_classes );
	}

	/**
	 * Instantiate the controls and actually load them into the control manager.
	 */
	public function load_controls() {
		if ( empty( $this->control_classes ) ) {
			return;
		}

		foreach ( $this->control_classes as $control_manager ) {
			$control_instance = new $control_manager();
			if ( ! $control_instance instanceof Controls_Base ) {
				continue;
			}

			$control_instance->init();

			$this->controls = array_merge( $this->controls, $control_instance->get_controls() );
		}

		$this->order_by_priority();
	}

	/**
	 * The metabox content.
	 */
	public function render_controls() {
		global $post;

		foreach ( $this->controls as $control ) {
			if ( method_exists( $control, 'render' ) ) {
				$control->render( $post->ID );
			}
		}
	}

	/**
	 * Save metabox content.
	 *
	 * @param int $post_id the post id.
	 */
	public function save( $post_id ) {
		foreach ( $this->controls as $control ) {
			if ( method_exists( $control, 'save' ) ) {
				$control->save( $post_id );
			}
		}
	}

	/**
	 * Register meta box to control layout on pages and posts.
	 */
	public function add() {
		$post_type         = 'Stax';
		$post_type_from_db = get_post_type();

		if ( $post_type_from_db ) {
			$post_type = ucfirst( $post_type_from_db );
		}

		add_meta_box(
			'stax-page-settings',
			sprintf(
			/* translators: %s - post type */
				__( '%s Settings', 'stax' ),
				$post_type
			),
			[ $this, 'render_metabox' ],
			[ 'post', 'page', 'product' ],
			'side',
			'default',
			[
				'__back_compat_meta_box' => true,
			]
		);

		if ( $this->is_gutenberg_active() ) {
			add_meta_box(
				'stax-page-settings-notice',
				sprintf(
				/* translators: %s - post type */
					__( '%s Settings', 'stax' ),
					$post_type
				),
				[ $this, 'render_metabox_notice' ],
				[ 'post', 'page' ],
				'side',
				'default',
				[
					'__back_compat_meta_box' => false,
				]
			);
		}
	}

	/**
	 * Detect if is gutenberg editor.
	 *
	 * @return bool
	 */
	private function is_gutenberg_active() {
		global $current_screen;

		if ( method_exists( $current_screen, 'is_block_editor' ) ) {
			return $current_screen->is_block_editor();
		}

		return false;
	}

	/**
	 * The metabox content.
	 */
	public function render_metabox() {
		$this->render_controls();
	}

	/**
	 * Render the metabox notice.
	 */
	public function render_metabox_notice() {
		echo '<div class="stx-meta-notice-wrapper">';
		echo '<h4>' . esc_html__( 'Page Settings are now accessible from the top bar', 'stax' ) . '</h4>';
		printf(
			/* translators: %1$s - Keyboard shortcut.   %2&s - svg icon */
			esc_html__( 'Click the %1$s icon in the top bar or use the keyboard shortcut ( %2$s ) to customise the layout settings for this page', 'stax' ),
			apply_filters( 'ti_wl_theme_is_localized', false ) ?
			'<span class="dashicons dashicons-hammer"></span>' :
			'<svg width="17" height="24" viewBox="0 0 58 72" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path d="M40.0227 22.1136C39.7955 19.6136 38.7841 17.6705 36.9886 16.2841C35.2159 14.875 32.6818 14.1705 29.3864 14.1705C27.2045 14.1705 25.3864 14.4545 23.9318 15.0227C22.4773 15.5909 21.3864 16.375 20.6591 17.375C19.9318 18.3523 19.5568 19.4773 19.5341 20.75C19.4886 21.7955 19.6932 22.7159 20.1477 23.5114C20.625 24.3068 21.3068 25.0114 22.1932 25.625C23.1023 26.2159 24.1932 26.7386 25.4659 27.1932C26.7386 27.6477 28.1705 28.0455 29.7614 28.3864L35.7614 29.75C39.2159 30.5 42.2614 31.5 44.8977 32.75C47.5568 34 49.7841 35.4886 51.5795 37.2159C53.3977 38.9432 54.7727 40.9318 55.7045 43.1818C56.6364 45.4318 57.1136 47.9545 57.1364 50.75C57.1136 55.1591 56 58.9432 53.7955 62.1023C51.5909 65.2614 48.4205 67.6818 44.2841 69.3636C40.1705 71.0455 35.2045 71.8864 29.3864 71.8864C23.5455 71.8864 18.4545 71.0114 14.1136 69.2614C9.77273 67.5114 6.39773 64.8523 3.98864 61.2841C1.57955 57.7159 0.340909 53.2045 0.272727 47.75H16.4318C16.5682 50 17.1705 51.875 18.2386 53.375C19.3068 54.875 20.7727 56.0114 22.6364 56.7841C24.5227 57.5568 26.7045 57.9432 29.1818 57.9432C31.4545 57.9432 33.3864 57.6364 34.9773 57.0227C36.5909 56.4091 37.8295 55.5568 38.6932 54.4659C39.5568 53.375 40 52.125 40.0227 50.7159C40 49.3977 39.5909 48.2727 38.7955 47.3409C38 46.3864 36.7727 45.5682 35.1136 44.8864C33.4773 44.1818 31.3864 43.5341 28.8409 42.9432L21.5455 41.2386C15.5 39.8523 10.7386 37.6136 7.26136 34.5227C3.78409 31.4091 2.05682 27.2045 2.07955 21.9091C2.05682 17.5909 3.21591 13.8068 5.55682 10.5568C7.89773 7.30682 11.1364 4.77273 15.2727 2.95455C19.4091 1.13636 24.125 0.22727 29.4205 0.22727C34.8295 0.22727 39.5227 1.14772 43.5 2.98863C47.5 4.80682 50.6023 7.36364 52.8068 10.6591C55.0114 13.9545 56.1364 17.7727 56.1818 22.1136H40.0227Z" fill="white"/>
             </svg>',
			'<strong>SHIFT + ALT + S</strong> ' . esc_html__( 'or', 'stax' ) . ' <strong>control + option + S</strong>'
		);
		echo '</div>';
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return bool
	 */
	public function enqueue() {
		if ( $this->is_gutenberg_active() ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! is_object( $screen ) ) {
			return false;
		}

		if ( $screen->base !== 'post' ) {
			return false;
		}

		wp_register_script(
			'stax-metabox',
			get_theme_file_uri( 'assets/js/metabox/metabox.js' ),
			[ 'jquery' ],
			stax()->get_version(),
			true
		);

		wp_localize_script(
			'stax-metabox',
			'staxMetabox',
			$this->get_localization()
		);

		wp_enqueue_script( 'stax-metabox' );

		return true;
	}

	/**
	 * Localize the Metabox script.
	 *
	 * @return array
	 */
	private function get_localization() {
		return [];
	}

	/**
	 * Order the controls by given priority.
	 */
	private function order_by_priority() {
		$order = [];

		foreach ( $this->controls as $key => $control_object ) {
			$order[ $key ] = $control_object->priority;
		}

		array_multisort( $order, SORT_ASC, $this->controls );
	}

	/**
	 * Register meta
	 */
	public function register_meta() {
		$meta_sidebar_controls = apply_filters(
			'stax_sidebar_meta_controls',
			[
				[
					'id'   => 'stax_show_title_section',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_media_panel_height',
					'type' => 'select',
				],
				[
					'id'   => 'stax_single_post_media_panel_text',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_cateory_breadcrumb',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_title_position',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_title_align',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_title_extra_align',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_title_size',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_image_width',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_image_format',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_audio_panel',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_video_panel',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_video_width',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_gallery_panel',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_gallery_width',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_gallery_slides',
					'type' => 'radio',
				],
				[
					'id'   => 'stax_single_post_meta_author_avatar',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_meta_author_name',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_meta_post_date',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_meta_reading_time',
					'type' => 'checkbox',
				],
				[
					'id'   => 'stax_single_post_shapes',
					'type' => 'checkbox',
				],
			]
		);

		foreach ( $meta_sidebar_controls as $control ) {
			$type = 'string';

			if ( $control['type'] === 'range' ) {
				$type = 'integer';
			}

			$post_type = '';

			if ( array_key_exists( 'post_type', $control ) ) {
				$post_type = $control['post_type'];
			}

			$meta_settings = [
				'show_in_rest'      => true,
				'type'              => $type,
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			];

			register_post_meta(
				$post_type,
				$control['id'],
				$meta_settings
			);
		}
	}

	/**
	 * Register the metabox sidebar.
	 */
	public function meta_sidebar_script_enqueue() {
		global $post_type;

		if ( 'post' !== $post_type && 'page' !== $post_type ) {
			return false;
		}

		wp_enqueue_script(
			'stax-meta-sidebar',
			trailingslashit( get_template_directory_uri() ) . 'inc/Customizer/metabox/build/index.js',
			[ 'react', 'wp-components', 'wp-compose', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-keyboard-shortcuts', 'wp-plugins', 'wp-polyfill' ],
			stax()->get_version(),
			false
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'stax-meta-sidebar', 'stax' );
		}

		wp_enqueue_style(
			'stax-meta-sidebar-css',
			trailingslashit( get_template_directory_uri() ) . 'inc/Customizer/metabox/build/index.css',
			[ 'wp-edit-blocks' ],
			stax()->get_version()
		);
	}

}
