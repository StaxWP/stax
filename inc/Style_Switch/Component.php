<?php
/**
 * Stax\Style_Switch\Component class
 *
 * Lets the administrator of a site updated from Stax 1.x move to the lean 2.0 stylesheet when they
 * choose to: a notice on the Dashboard, Appearance > Themes and in the Customizer, a permanent
 * "Stax style" screen (Appearance > Stax style and a Customizer section with a live before/after
 * preview), and "Switch back" for 30 days. Nothing changes on the front end until they switch.
 *
 * @package stax
 */

namespace Stax\Style_Switch;

use Stax\Component_Interface;
use Stax\Customizer\Config;
use function Stax\stax;

/**
 * Class Component
 */
class Component implements Component_Interface {

	/**
	 * Admin page, Customizer section and request action name.
	 */
	const SLUG = 'stax-style';

	/**
	 * Customizer section ID.
	 */
	const SECTION = 'stax_style';

	/**
	 * Nonce action.
	 */
	const NONCE = 'stax-style-switch';

	/**
	 * Cookie that shows the other style in the Customizer preview only.
	 */
	const COOKIE = 'stax_style_preview';

	/**
	 * Capability needed to see and use the switch.
	 */
	const CAPABILITY = 'edit_theme_options';

	/**
	 * Admin screens that show the notice.
	 */
	const NOTICE_SCREENS = [ 'dashboard', 'themes' ];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'style_switch';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_print_compat_css' ], 20 );
		add_action( 'customize_preview_init', [ $this, 'action_preview' ] );
		add_action( 'save_post', [ $this, 'action_save_post' ], 10, 2 );
		add_action( 'admin_init', [ Migration::class, 'maybe_expire_backup' ] );
		add_action( 'admin_menu', [ $this, 'action_admin_menu' ] );
		add_action( 'admin_notices', [ $this, 'action_admin_notices' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'action_admin_assets' ] );
		add_action( 'admin_post_' . self::SLUG, [ $this, 'handle_request' ] );
		add_action( 'wp_ajax_' . self::SLUG, [ $this, 'handle_request' ] );
		add_action( 'customize_register', [ $this, 'action_customize_register' ] );
		add_action( 'customize_controls_init', [ $this, 'action_customize_init' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'action_customize_assets' ] );
	}

	/**
	 * Whether the current user can use the switch and this site has it (updated from 1.x and either
	 * still on the 1.x stylesheet or inside the switch back window).
	 *
	 * @return bool
	 */
	public static function is_available() {
		return current_user_can( self::CAPABILITY ) && Migration::is_offered() && ( Migration::is_legacy() || Migration::switch_back_until() );
	}

	/**
	 * Prints the 1.x compatibility CSS after the lean stylesheet.
	 */
	public function action_print_compat_css() {
		if ( \Stax_Assets::instance()->use_legacy_css() ) {
			return;
		}

		$css = 'new' === $this->get_preview_mode() ? Migration::build_compat_css( Migration::get_used_classes() ) : get_option( Migration::CSS_OPTION, '' );

		if ( $css ) {
			wp_add_inline_style( 'stax-global', $css );
		}
	}

	/**
	 * Style requested for the Customizer preview ("new" or "old"), or an empty string.
	 *
	 * @return string
	 */
	protected function get_preview_mode() {
		if ( ! is_customize_preview() || ! isset( $_COOKIE[ self::COOKIE ] ) || ! current_user_can( self::CAPABILITY ) ) {
			return '';
		}

		$mode = sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE ] ) );

		if ( 'new' === $mode && Migration::is_legacy() ) {
			return 'new';
		}

		return 'old' === $mode && Migration::switch_back_until() ? 'old' : '';
	}

	/**
	 * Customizer preview: shows the other style without saving anything.
	 */
	public function action_preview() {
		$mode = $this->get_preview_mode();

		if ( 'new' === $mode ) {
			add_filter( 'stax_use_legacy_css', '__return_false', 20 );
			add_filter( 'theme_mod_' . Config::MODS_GLOBAL_COLORS, [ Migration::class, 'fix_palette' ], 20 );

			foreach ( Migration::HEADER_COLOR_MODS as $name => $min ) {
				add_filter(
					'theme_mod_' . $name,
					function ( $value ) use ( $min ) {
						return Migration::fix_contrast( $value, $this->get_preview_header_background(), $min );
					},
					20
				);
			}
		} elseif ( 'old' === $mode ) {
			$backup = Migration::get_state()['backup'];

			add_filter( 'stax_use_legacy_css', '__return_true', 20 );
			add_filter(
				'option_theme_mods_' . get_stylesheet(),
				function ( $mods ) use ( $backup ) {
					foreach ( $backup['mods'] as $name => $value ) {
						if ( null === $value ) {
							unset( $mods[ $name ] );
						} else {
							$mods[ $name ] = $value;
						}
					}

					return $mods;
				}
			);
		}
	}

	/**
	 * Header background in the preview (contrast-fixed palette, Customizer changes included).
	 *
	 * @return string
	 */
	protected function get_preview_header_background() {
		$mods = [
			'hfg_header_layout_main_background' => get_theme_mod( 'hfg_header_layout_main_background', stax()->get_option( 'hfg_header_layout_main_background' ) ),
			Config::MODS_GLOBAL_COLORS          => get_theme_mod( Config::MODS_GLOBAL_COLORS, \Stax_Assets::instance()->get_global_colors_default() ),
		];

		return Migration::get_header_background( $mods );
	}

	/**
	 * Keeps the compatibility CSS of a switched site up to date when content is saved.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post.
	 */
	public function action_save_post( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || in_array( $post->post_type, Migration::SKIP_POST_TYPES, true ) ) {
			return;
		}

		Migration::add_content( $post->post_content );
	}

	/**
	 * Appearance > Stax style.
	 */
	public function action_admin_menu() {
		if ( ! self::is_available() ) {
			return;
		}

		add_theme_page(
			__( 'Stax style', 'stax' ),
			__( 'Stax style', 'stax' ),
			self::CAPABILITY,
			self::SLUG,
			[ $this, 'render_admin_page' ]
		);
	}

	/**
	 * Admin page: the same panel as the Customizer section, with plain forms.
	 */
	public function render_admin_page() {
		echo '<div class="wrap stax-style-page"><h1>' . esc_html__( 'Stax style', 'stax' ) . '</h1>';
		$this->render_panel( 'page' );
		echo '</div>';
	}

	/**
	 * Notice on the Dashboard and Appearance > Themes, plus the result of an action.
	 */
	public function action_admin_notices() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		$this->render_result_notice();

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, self::NOTICE_SCREENS, true ) || ! Migration::should_show_notice() ) {
			return;
		}

		echo '<div class="notice notice-info is-dismissible stax-style-notice">';
		$this->render_notice_body( 'page' );
		echo '</div>';
	}

	/**
	 * Notice text and buttons ("page": a form posting to admin-post.php; "customizer": buttons for the script).
	 *
	 * @param string $context "page" or "customizer".
	 */
	protected function render_notice_body( $context ) {
		echo '<p><strong>' . esc_html__( 'Stax 2.0 has a faster, lighter style.', 'stax' ) . '</strong> ' . esc_html__( 'Preview it, then switch with one click.', 'stax' ) . '</p>';

		$this->open_form( $context );
		echo '<p class="stax-style-actions">';

		if ( 'page' === $context ) {
			printf( '<a class="button" href="%s">%s</a> ', esc_url( $this->get_customize_url( 'new' ) ), esc_html__( 'Preview', 'stax' ) );
		} else {
			$this->render_button( $context, 'preview', __( 'Preview', 'stax' ), 'button' );
		}

		$this->render_button( $context, 'switch', __( 'Switch now', 'stax' ), 'button button-primary' );
		$this->render_button( $context, 'dismiss', __( 'Not now', 'stax' ), 'button-link' );
		echo '</p>';
		$this->close_form( $context );
	}

	/**
	 * Success or error notice after an action (admin screens and the Customizer section).
	 */
	protected function render_result_notice( $dismissible = true ) {
		$messages = $this->get_result_messages();
		$result   = isset( $_GET[ self::SLUG ] ) ? sanitize_key( wp_unslash( $_GET[ self::SLUG ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only.

		if ( isset( $messages[ $result ] ) ) {
			printf(
				'<div class="notice notice-%1$s%2$s"><p>%3$s</p></div>',
				'failed' === $result ? 'error' : 'success',
				// "inline" keeps common.js from moving it out of the Customizer section.
				$dismissible ? ' is-dismissible' : ' inline',
				esc_html( $messages[ $result ] )
			);
		}
	}

	/**
	 * Messages shown after an action, by result.
	 *
	 * @return string[]
	 */
	protected function get_result_messages() {
		return [
			/* translators: %d: number of days. */
			'switched' => sprintf( __( 'Your site now uses the new Stax style. You can switch back for %d days in Appearance > Stax style.', 'stax' ), (int) ( Migration::WINDOW / DAY_IN_SECONDS ) ),
			'restored' => __( 'Your site uses the classic Stax style again.', 'stax' ),
			'failed'   => __( 'Nothing changed. Reload the page and try again.', 'stax' ),
		];
	}

	/**
	 * The "Stax style" panel: current style, live comparison, what changes, switch buttons.
	 *
	 * @param string $context "page" (Appearance > Stax style) or "customizer" (Customizer section).
	 */
	public function render_panel( $context ) {
		$legacy  = Migration::is_legacy();
		$until   = Migration::switch_back_until();
		$classes = $legacy ? Migration::get_used_classes() : (array) Migration::get_state()['classes'];
		$changes = $legacy ? Migration::get_new_values( $classes ) : null;

		echo '<div class="stax-style-panel" data-context="' . esc_attr( $context ) . '">';

		if ( 'customizer' === $context ) {
			echo '<div class="stax-style-message" role="status" tabindex="-1">';
			$this->render_result_notice( false );
			echo '</div>';
		}

		echo '<p class="stax-style-current">' . ( $legacy
			? esc_html__( 'Your site uses the classic Stax 1.x style.', 'stax' )
			: esc_html__( 'Your site uses the new Stax 2.0 style.', 'stax' ) ) . '</p>';

		echo '<p>' . esc_html__( 'The new style loads a much smaller stylesheet, so pages load faster. Your colors, fonts, layout, header and footer stay the same.', 'stax' ) . '</p>';

		$this->render_compare( $context, $legacy );

		if ( $legacy ) {
			echo '<h3>' . esc_html__( 'What changes when you switch', 'stax' ) . '</h3><ul class="stax-style-changes">';
			$this->render_size_change();
			$this->render_color_changes( $changes['colors'] );
			$this->render_class_changes( $classes );
			echo '</ul>';
		}

		$this->open_form( $context );
		echo '<p class="stax-style-actions">';

		if ( $legacy ) {
			$this->render_button( $context, 'switch', __( 'Switch to the new style', 'stax' ), 'button button-primary' );
		} elseif ( $until ) {
			$this->render_button( $context, 'switch_back', __( 'Switch back to the old style', 'stax' ), 'button' );
		}

		echo '</p>';
		$this->close_form( $context );

		if ( $legacy ) {
			echo '<p class="description">' . esc_html__( 'Nothing changes until you switch. You can switch back for 30 days.', 'stax' ) . '</p>';
		} elseif ( $until ) {
			/* translators: %s: date. */
			echo '<p class="description">' . esc_html( sprintf( __( 'Switching back restores your previous style settings. Available until %s.', 'stax' ), wp_date( get_option( 'date_format' ), $until ) ) ) . '</p>';
		}

		echo '</div>';
	}

	/**
	 * Before/after: preview toggle in the Customizer, a link to it on the admin page.
	 *
	 * @param string $context Context.
	 * @param bool   $legacy  Whether the site uses the 1.x stylesheet.
	 */
	protected function render_compare( $context, $legacy ) {
		if ( 'page' === $context ) {
			printf(
				'<p><a class="button" href="%1$s">%2$s</a></p>',
				esc_url( $this->get_customize_url( $legacy ? 'new' : 'old' ) ),
				$legacy ? esc_html__( 'Preview the new style', 'stax' ) : esc_html__( 'Preview the old style', 'stax' )
			);

			return;
		}

		$current = $legacy ? 'old' : 'new';
		$preview = isset( $_COOKIE[ self::COOKIE ] ) ? sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE ] ) ) : '';
		$checked = in_array( $preview, [ 'old', 'new' ], true ) ? $preview : $current;
		$options = [
			'old' => __( 'Classic style', 'stax' ),
			'new' => __( 'New style', 'stax' ),
		];

		echo '<fieldset class="stax-style-compare"><legend>' . esc_html__( 'Show in the preview', 'stax' ) . '</legend>';

		foreach ( $options as $value => $label ) {
			printf(
				'<label><input type="radio" name="stax-style-preview" value="%1$s"%2$s data-current="%3$s"> %4$s</label><br>',
				esc_attr( $value ),
				checked( $value, $checked, false ),
				$value === $current ? '1' : '0',
				esc_html( $value === $current ? sprintf( /* translators: %s: style name. */ __( '%s (current)', 'stax' ), $label ) : $label )
			);
		}

		echo '</fieldset>';
	}

	/**
	 * Stylesheet size before and after.
	 */
	protected function render_size_change() {
		$size = function ( $files ) {
			$bytes = 0;

			foreach ( $files as $file ) {
				$path   = get_theme_file_path( $file );
				$bytes += is_readable( $path ) ? (int) filesize( $path ) : 0;
			}

			return (int) round( $bytes / 1024 );
		};

		$lean = [ 'assets/css/index.min.css' ];

		foreach ( \Stax_Assets::get_active_compat_styles() as $name ) {
			$lean[] = 'assets/css/compat/' . $name . '.min.css';
		}

		printf(
			'<li>%s</li>',
			/* translators: 1: new size in KB, 2: old size in KB. */
			esc_html( sprintf( __( 'Theme stylesheet: %1$d KB instead of %2$d KB.', 'stax' ), $size( $lean ), $size( [ 'assets/css/legacy/stax-1x.min.css', 'assets/fonts/style.css' ] ) ) )
		);
	}

	/**
	 * Colors changed for contrast, with swatches.
	 *
	 * @param array $colors Changed colors (label, from, to).
	 */
	protected function render_color_changes( $colors ) {
		if ( ! $colors ) {
			echo '<li>' . esc_html__( 'Colors: no change needed, your colors are easy to read.', 'stax' ) . '</li>';

			return;
		}

		echo '<li>' . esc_html__( 'Colors that were too light to read easily become just dark enough:', 'stax' ) . '<ul>';

		foreach ( $colors as $color ) {
			printf(
				'<li>%1$s: <span class="stax-style-swatch" style="background:%2$s" aria-hidden="true"></span> %2$s %3$s <span class="stax-style-swatch" style="background:%4$s" aria-hidden="true"></span> %4$s</li>',
				esc_html( $color['label'] ),
				esc_attr( $color['from'] ),
				'<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . esc_html__( 'becomes', 'stax' ) . '</span>',
				esc_attr( $color['to'] )
			);
		}

		echo '</ul></li>';
	}

	/**
	 * 1.x classes found in the content.
	 *
	 * @param string[] $classes Classes.
	 */
	protected function render_class_changes( $classes ) {
		if ( ! $classes ) {
			echo '<li>' . esc_html__( 'Content: none of your content uses old Stax 1.x classes.', 'stax' ) . '</li>';

			return;
		}

		$shown = array_slice( $classes, 0, 12 );

		printf(
			'<li>%s <code>%s</code>%s</li>',
			esc_html(
				sprintf(
					/* translators: %d: number of CSS classes. */
					_n( 'Content: %d class from Stax 1.x is used, so its styles are kept:', 'Content: %d classes from Stax 1.x are used, so their styles are kept:', count( $classes ), 'stax' ),
					count( $classes )
				)
			),
			esc_html( implode( ' ', $shown ) ),
			count( $classes ) > count( $shown ) ? esc_html__( ' and more.', 'stax' ) : ''
		);
	}

	/**
	 * Opens the form of the admin page / notice (the Customizer uses the script instead, it is already in a form).
	 *
	 * @param string $context Context.
	 */
	protected function open_form( $context ) {
		if ( 'page' !== $context ) {
			return;
		}

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		echo '<input type="hidden" name="action" value="' . esc_attr( self::SLUG ) . '">';
		wp_nonce_field( self::NONCE );
	}

	/**
	 * Closes open_form().
	 *
	 * @param string $context Context.
	 */
	protected function close_form( $context ) {
		if ( 'page' === $context ) {
			echo '</form>';
		}
	}

	/**
	 * Action button: a submit button on admin pages, a script button in the Customizer.
	 *
	 * @param string $context Context.
	 * @param string $action  Action.
	 * @param string $label   Label.
	 * @param string $classes CSS classes.
	 */
	protected function render_button( $context, $action, $label, $classes ) {
		printf(
			'<button type="%1$s" name="do" value="%2$s" class="%3$s" data-stax-style="%2$s">%4$s</button> ',
			'page' === $context ? 'submit' : 'button',
			esc_attr( $action ),
			esc_attr( $classes ),
			esc_html( $label )
		);
	}

	/**
	 * Customizer URL that opens the Stax style section, optionally previewing a style.
	 *
	 * @param string $preview "new", "old" or empty.
	 *
	 * @return string
	 */
	protected function get_customize_url( $preview = '' ) {
		$args = [
			'autofocus[section]' => self::SECTION,
			'return'             => rawurlencode( remove_query_arg( [ self::SLUG, '_wpnonce' ] ) ),
		];

		if ( $preview ) {
			$args[ self::COOKIE ] = $preview;
		}

		return add_query_arg( $args, admin_url( 'customize.php' ) );
	}

	/**
	 * Handles switch, switch back and dismiss (admin-post.php form or admin-ajax.php from the scripts).
	 */
	public function handle_request() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to change the theme style.', 'stax' ), 403 );
		}

		check_admin_referer( self::NONCE );

		$action = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
		$result = 'failed';

		if ( 'switch' === $action && Migration::switch_to_new() ) {
			$result = 'switched';
		} elseif ( 'switch_back' === $action && Migration::switch_back() ) {
			$result = 'restored';
		} elseif ( 'dismiss' === $action && Migration::is_offered() ) {
			Migration::dismiss_notice();
			$result = 'dismissed';
		}

		// The preview cookie has no meaning after a switch.
		setcookie( self::COOKIE, '', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

		if ( wp_doing_ajax() ) {
			if ( 'failed' === $result ) {
				wp_send_json_error( [ 'message' => $this->get_result_messages()['failed'] ] );
			}

			wp_send_json_success( [ 'result' => $result ] );
		}

		$back = wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php' );

		wp_safe_redirect( add_query_arg( self::SLUG, $result, remove_query_arg( [ self::SLUG, self::COOKIE ], $back ) ) );
		exit;
	}

	/**
	 * Notice script (dismiss with the X) and panel styles on the screens that need them.
	 *
	 * @param string $hook_suffix Admin page.
	 */
	public function action_admin_assets( $hook_suffix ) {
		$screen = get_current_screen();
		$notice = $screen && in_array( $screen->id, self::NOTICE_SCREENS, true ) && Migration::should_show_notice() && current_user_can( self::CAPABILITY );

		if ( 'appearance_page_' . self::SLUG !== $hook_suffix && ! $notice ) {
			return;
		}

		$this->enqueue_assets( [] );
	}

	/**
	 * Enqueues the panel styles and script with its settings.
	 *
	 * @param array $deps Script dependencies.
	 * @param array $data Extra settings for the script.
	 */
	protected function enqueue_assets( $deps, $data = [] ) {
		wp_enqueue_style( 'stax-style-switch', get_theme_file_uri( 'assets/admin/css/style-switch.css' ), [], stax()->get_asset_version( get_theme_file_path( 'assets/admin/css/style-switch.css' ) ) );
		wp_enqueue_script( 'stax-style-switch', get_theme_file_uri( 'assets/admin/js/style-switch.js' ), $deps, stax()->get_asset_version( get_theme_file_path( 'assets/admin/js/style-switch.js' ) ), true );
		wp_localize_script(
			'stax-style-switch',
			'staxStyleSwitch',
			array_merge(
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'action'  => self::SLUG,
					'nonce'   => wp_create_nonce( self::NONCE ),
				],
				$data
			)
		);
	}

	/**
	 * Customizer section "Stax style" with the panel.
	 *
	 * @param \WP_Customize_Manager $wp_customize Customizer.
	 */
	public function action_customize_register( $wp_customize ) {
		if ( ! self::is_available() ) {
			return;
		}

		$wp_customize->add_section(
			self::SECTION,
			[
				'title'      => __( 'Stax style', 'stax' ),
				'priority'   => 1,
				'capability' => self::CAPABILITY,
			]
		);

		$wp_customize->add_control(
			new Control(
				$wp_customize,
				self::SECTION,
				[
					'section'  => self::SECTION,
					'settings' => [],
					'renderer' => $this,
				]
			)
		);
	}

	/**
	 * Opening the Customizer: show the requested style in the preview (link from the notice or the admin
	 * page), otherwise the saved one. Also refreshes the content scan for the panel.
	 */
	public function action_customize_init() {
		if ( ! self::is_available() ) {
			return;
		}

		$mode = isset( $_GET[ self::COOKIE ] ) ? sanitize_key( wp_unslash( $_GET[ self::COOKIE ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only changes what this user's preview shows.

		if ( in_array( $mode, [ 'new', 'old' ], true ) ) {
			setcookie( self::COOKIE, $mode, 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
			$_COOKIE[ self::COOKIE ] = $mode;
		} else {
			setcookie( self::COOKIE, '', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
			unset( $_COOKIE[ self::COOKIE ] );
		}
	}

	/**
	 * Customizer script: notification, preview toggle, switch buttons.
	 */
	public function action_customize_assets() {
		if ( ! self::is_available() ) {
			return;
		}

		ob_start();
		$this->render_notice_body( 'customizer' );
		$notice  = ob_get_clean();
		$preview = isset( $_COOKIE[ self::COOKIE ] ) ? sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE ] ) ) : '';

		$this->enqueue_assets(
			[ 'customize-controls' ],
			[
				'section'      => self::SECTION,
				'cookie'       => self::COOKIE,
				'cookiePath'   => COOKIEPATH,
				'preview'      => $preview,
				'result'       => isset( $_GET[ self::SLUG ] ) ? sanitize_key( wp_unslash( $_GET[ self::SLUG ] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only.
				// Opened with "Preview" from the notice: the section is already open, no need to repeat the notice.
				'showNotice'   => ! $preview && Migration::should_show_notice(),
				'noticeHtml'   => $notice,
				'unsaved'      => __( 'Publish or discard your Customizer changes first, then switch.', 'stax' ),
				'working'      => __( 'Working...', 'stax' ),
				'failed'       => $this->get_result_messages()['failed'],
			]
		);
	}
}
