<?php
/**
 * Stax\Style_Switch\Migration class
 *
 * What switching a site from the 1.x stylesheet to the lean 2.0 one changes, and the switch itself.
 * Everything here works on the current site only, so each site of a network decides on its own.
 *
 * - Colors: the site's own Customizer colors are kept. A color is changed only when it fails WCAG AA
 *   contrast, and then only as much as needed (same hue and saturation, lightness moved step by step).
 * - Content: post content, widgets, menus, Elementor data, theme mods and Additional CSS are scanned for
 *   the 1.x classes the lean stylesheet left out; the matching 1.x rules are kept in a small inline style.
 * - Switching saves the previous values, so switching back restores them exactly.
 *
 * @package stax
 */

namespace Stax\Style_Switch;

use Stax\Customizer\Config;
use Stax\Editor\Component as Editor;

/**
 * Class Migration
 */
class Migration {

	/**
	 * Option holding the switch state of the site (not autoloaded).
	 */
	const OPTION = 'stax_style_switch';

	/**
	 * Option holding the 1.x compatibility CSS printed with the lean stylesheet.
	 */
	const CSS_OPTION = 'stax_style_compat_css';

	/**
	 * Option written by the first 2.0 builds; read once so those sites keep their offer.
	 */
	const OLD_NOTICE_OPTION = 'stax_show_2_0_notice';

	/**
	 * How long "Switch back" stays available, and when a dismissed notice comes back.
	 */
	const WINDOW = 30 * DAY_IN_SECONDS;

	/**
	 * Rules file written by bin/build.mjs.
	 */
	const RULES_FILE = 'assets/css/legacy/stax-1x-rules.json';

	/**
	 * Header menu color theme mods, with the minimum contrast they need against the header background.
	 */
	const HEADER_COLOR_MODS = [
		'primary-menu_color'                   => 4.5,
		'primary-menu_active_color'            => 4.5,
		'primary-menu_hover_color'             => 4.5,
		'header_search_responsive_hover_color' => 3,
	];

	/**
	 * Palette colors (theme.json slugs) with the minimum contrast they need against the background color.
	 * Headings are large text, so 3:1 is enough for them.
	 */
	const PALETTE_CONTRAST = [
		'text'    => 4.5,
		'link'    => 4.5,
		'meta'    => 4.5,
		'heading' => 3,
	];

	/**
	 * Post types that never hold front-end content.
	 */
	const SKIP_POST_TYPES = [ 'revision', 'attachment', 'nav_menu_item', 'customize_changeset', 'oembed_cache', 'custom_css', 'wp_global_styles', 'user_request', 'wp_navigation' ];

	/**
	 * Decoded rules file.
	 *
	 * @var array|null
	 */
	protected static $rules = null;

	/**
	 * State of the site, with defaults.
	 *
	 * @return array
	 */
	public static function get_state() {
		$state = get_option( self::OPTION, [] );
		$state = is_array( $state ) ? $state : [];

		// Sites that ran an early 2.0 build: their offer was recorded in another option.
		if ( empty( $state['offered'] ) && get_option( self::OLD_NOTICE_OPTION ) ) {
			$state['offered'] = time();
			delete_option( self::OLD_NOTICE_OPTION );
			self::save_state( $state );
		}

		return array_merge(
			[
				'offered'      => 0,
				'dismissed'    => 0,
				'dismissed_at' => 0,
				'switched_at'  => 0,
				'decided'      => false,
				'backup'       => null,
				'classes'      => null,
				'scanned_at'   => 0,
			],
			$state
		);
	}

	/**
	 * Saves the state.
	 *
	 * @param array $state State.
	 */
	public static function save_state( $state ) {
		update_option( self::OPTION, $state, false );
	}

	/**
	 * Called by the 2.0 upgrade routine on sites updated from 1.x.
	 */
	public static function mark_offered() {
		$state            = self::get_state();
		$state['offered'] = time();
		self::save_state( $state );
	}

	/**
	 * Whether the site came from 1.x (only those sites get the switch).
	 *
	 * @return bool
	 */
	public static function is_offered() {
		return (bool) self::get_state()['offered'];
	}

	/**
	 * Whether the site uses the 1.x stylesheet (the saved setting, not the preview).
	 *
	 * @return bool
	 */
	public static function is_legacy() {
		$mods = get_theme_mods();

		return ! empty( $mods[ Config::OPTION_PERF_LEGACY_CSS ] );
	}

	/**
	 * Time until which "Switch back" is available, or 0.
	 *
	 * @return int
	 */
	public static function switch_back_until() {
		$state = self::get_state();

		if ( self::is_legacy() || empty( $state['backup'] ) || ! $state['switched_at'] ) {
			return 0;
		}

		$until = (int) $state['switched_at'] + self::WINDOW;

		return $until > time() ? $until : 0;
	}

	/**
	 * Whether the "new style" notice should show now: offered, still on 1.x, not decided,
	 * and either never dismissed or dismissed once more than 30 days ago.
	 *
	 * @return bool
	 */
	public static function should_show_notice() {
		$state = self::get_state();

		if ( ! $state['offered'] || $state['decided'] || ! self::is_legacy() ) {
			return false;
		}

		if ( 0 === (int) $state['dismissed'] ) {
			return true;
		}

		return 1 === (int) $state['dismissed'] && time() >= (int) $state['dismissed_at'] + self::WINDOW;
	}

	/**
	 * "Not now": hides the notice; it comes back once after 30 days.
	 */
	public static function dismiss_notice() {
		$state                 = self::get_state();
		$state['dismissed']    = (int) $state['dismissed'] + 1;
		$state['dismissed_at'] = time();
		self::save_state( $state );
	}

	/**
	 * Switches the site to the lean stylesheet, with contrast-fixed colors and the compatibility CSS.
	 *
	 * @return bool False when the site is not on the 1.x stylesheet.
	 */
	public static function switch_to_new() {
		if ( ! self::is_legacy() ) {
			return false;
		}

		$state   = self::get_state();
		$classes = self::scan_content();
		$changes = self::get_new_values( $classes );
		$mods    = get_theme_mods();
		$backup  = [
			'mods' => [],
			'css'  => get_option( self::CSS_OPTION, null ),
		];

		foreach ( array_keys( $changes['mods'] ) as $name ) {
			$backup['mods'][ $name ] = array_key_exists( $name, $mods ) ? $mods[ $name ] : null;
		}

		foreach ( $changes['mods'] as $name => $value ) {
			set_theme_mod( $name, $value );
		}

		update_option( self::CSS_OPTION, $changes['css'], true );

		$state['backup']      = $backup;
		$state['switched_at'] = time();
		$state['decided']     = true;
		$state['classes']     = $classes;
		$state['scanned_at']  = time();
		self::save_state( $state );

		return true;
	}

	/**
	 * Restores exactly what the site had before the switch.
	 *
	 * @return bool False when there is nothing to restore.
	 */
	public static function switch_back() {
		if ( ! self::switch_back_until() ) {
			return false;
		}

		$state = self::get_state();

		foreach ( $state['backup']['mods'] as $name => $value ) {
			if ( null === $value ) {
				remove_theme_mod( $name );
			} else {
				set_theme_mod( $name, $value );
			}
		}

		if ( null === $state['backup']['css'] ) {
			delete_option( self::CSS_OPTION );
		} else {
			update_option( self::CSS_OPTION, $state['backup']['css'], true );
		}

		$state['backup']      = null;
		$state['switched_at'] = 0;
		self::save_state( $state );

		return true;
	}

	/**
	 * Drops the saved 1.x values once the switch back window has passed.
	 */
	public static function maybe_expire_backup() {
		$state = self::get_state();

		if ( ! empty( $state['backup'] ) && $state['switched_at'] && time() > (int) $state['switched_at'] + self::WINDOW ) {
			$state['backup'] = null;
			self::save_state( $state );
		}
	}

	/**
	 * Everything the switch would change: theme mods (stylesheet setting and contrast-fixed colors)
	 * and the compatibility CSS.
	 *
	 * @param string[] $classes 1.x classes used by the content.
	 * @param array    $mods    Theme mods to start from (defaults to the saved ones).
	 *
	 * @return array { mods: name => new value, colors: list of changed colors, css: string }
	 */
	public static function get_new_values( $classes, $mods = null ) {
		$mods    = null === $mods ? get_theme_mods() : $mods;
		$mods    = is_array( $mods ) ? $mods : [];
		$result  = [
			'mods'   => [ Config::OPTION_PERF_LEGACY_CSS => false ],
			'colors' => [],
			'css'    => self::build_compat_css( $classes ),
		];
		$palette = $mods[ Config::MODS_GLOBAL_COLORS ] ?? \Stax_Assets::instance()->get_global_colors_default();
		$fixed   = self::fix_palette( $palette, $result['colors'] );

		if ( $fixed !== $palette ) {
			$result['mods'][ Config::MODS_GLOBAL_COLORS ] = $fixed;
		}

		$mods[ Config::MODS_GLOBAL_COLORS ] = $fixed;
		$header_bg                          = self::get_header_background( $mods );

		foreach ( self::HEADER_COLOR_MODS as $name => $min ) {
			$value = $mods[ $name ] ?? \Stax\stax()->get_option( $name );
			$new   = self::fix_contrast( $value, $header_bg, $min );

			if ( $new !== $value ) {
				$result['mods'][ $name ] = $new;
				$result['colors'][]      = [
					'label' => self::get_mod_label( $name ),
					'from'  => Editor::to_hex( $value ),
					'to'    => $new,
				];
			}
		}

		return $result;
	}

	/**
	 * Fixes the contrast of the active palette's text colors against its background.
	 *
	 * @param array $palette Global colors theme mod.
	 * @param array $changes Changed colors are appended here.
	 *
	 * @return array
	 */
	public static function fix_palette( $palette, &$changes = [] ) {
		$active = $palette['activePalette'] ?? '';

		if ( empty( $palette['palettes'][ $active ]['colors'] ) || ! is_array( $palette['palettes'][ $active ]['colors'] ) ) {
			return $palette;
		}

		$background = self::get_palette_hex( $palette, 'background' );

		foreach ( $palette['palettes'][ $active ]['colors'] as $index => $item ) {
			$slug = array_search( $item['vars'] ?? null, Editor::PALETTE_SLUGS, true );

			if ( false === $slug || ! isset( self::PALETTE_CONTRAST[ $slug ] ) ) {
				continue;
			}

			$new = self::fix_contrast( $item['color'] ?? '', $background, self::PALETTE_CONTRAST[ $slug ] );

			if ( $new !== ( $item['color'] ?? '' ) ) {
				$palette['palettes'][ $active ]['colors'][ $index ]['color'] = $new;

				$changes[] = [
					'label' => $item['label'] ?? $slug,
					'from'  => Editor::to_hex( $item['color'] ),
					'to'    => $new,
				];
			}
		}

		return $palette;
	}

	/**
	 * Hex value of a palette color (active palette), by theme.json slug.
	 *
	 * @param array  $palette Global colors theme mod.
	 * @param string $slug    Slug.
	 *
	 * @return string
	 */
	protected static function get_palette_hex( $palette, $slug ) {
		$active = $palette['activePalette'] ?? '';

		foreach ( $palette['palettes'][ $active ]['colors'] ?? [] as $item ) {
			if ( ( $item['vars'] ?? null ) === Editor::PALETTE_SLUGS[ $slug ] ) {
				return Editor::to_hex( $item['color'] ?? '' );
			}
		}

		return '';
	}

	/**
	 * Header background color (main row), or the page background when it is not a plain color.
	 *
	 * @param array $mods Theme mods (global colors already contrast-fixed when that matters).
	 *
	 * @return string
	 */
	public static function get_header_background( $mods ) {
		$background = $mods['hfg_header_layout_main_background'] ?? \Stax\stax()->get_option( 'hfg_header_layout_main_background' );
		$hex        = is_array( $background ) && 'color' === ( $background['type'] ?? 'color' ) ? Editor::to_hex( $background['colorValue'] ?? '' ) : '';

		if ( $hex ) {
			return $hex;
		}

		return self::get_palette_hex( $mods[ Config::MODS_GLOBAL_COLORS ] ?? \Stax_Assets::instance()->get_global_colors_default(), 'background' );
	}

	/**
	 * Customizer label of a header color mod.
	 *
	 * @param string $name Theme mod.
	 *
	 * @return string
	 */
	protected static function get_mod_label( $name ) {
		$labels = [
			'primary-menu_color'                   => __( 'Menu items color', 'stax' ),
			'primary-menu_active_color'            => __( 'Menu active item color', 'stax' ),
			'primary-menu_hover_color'             => __( 'Menu hover color', 'stax' ),
			'header_search_responsive_hover_color' => __( 'Search icon hover color', 'stax' ),
		];

		return $labels[ $name ] ?? $name;
	}

	/**
	 * Relative luminance of a #rrggbb color (WCAG 2.x).
	 *
	 * @param string $hex Color.
	 *
	 * @return float
	 */
	public static function luminance( $hex ) {
		$channels = sscanf( $hex, '#%02x%02x%02x' );
		$linear   = array_map(
			function ( $value ) {
				$value = $value / 255;

				return $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
			},
			$channels
		);

		return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
	}

	/**
	 * Contrast ratio of two #rrggbb colors.
	 *
	 * @param string $a Color.
	 * @param string $b Color.
	 *
	 * @return float
	 */
	public static function contrast( $a, $b ) {
		$la = self::luminance( $a );
		$lb = self::luminance( $b );

		return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
	}

	/**
	 * Returns the color unchanged when it has enough contrast, otherwise the closest color with the same
	 * hue and saturation that has (lightness moved in 0.5% steps, darker on light backgrounds).
	 *
	 * @param string $color      Color (hex or rgb()).
	 * @param string $background Background (hex or rgb()).
	 * @param float  $min        Minimum contrast ratio.
	 *
	 * @return string
	 */
	public static function fix_contrast( $color, $background, $min ) {
		$hex = Editor::to_hex( (string) $color );
		$bg  = Editor::to_hex( (string) $background );

		if ( ! $hex || ! $bg || self::contrast( $hex, $bg ) >= $min ) {
			return $color;
		}

		list( $h, $s, $l ) = self::hex_to_hsl( $hex );

		$step = self::contrast( '#000000', $bg ) >= self::contrast( '#ffffff', $bg ) ? -0.005 : 0.005;

		for ( $l += $step; $l >= 0 && $l <= 1; $l += $step ) {
			$candidate = self::hsl_to_hex( $h, $s, $l );

			if ( self::contrast( $candidate, $bg ) >= $min ) {
				return $candidate;
			}
		}

		return $step < 0 ? '#000000' : '#ffffff';
	}

	/**
	 * Converts #rrggbb to HSL (all 0..1).
	 *
	 * @param string $hex Color.
	 *
	 * @return float[]
	 */
	protected static function hex_to_hsl( $hex ) {
		list( $r, $g, $b ) = array_map(
			function ( $v ) {
				return $v / 255;
			},
			sscanf( $hex, '#%02x%02x%02x' )
		);

		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );
		$l   = ( $max + $min ) / 2;

		if ( $max === $min ) {
			return [ 0, 0, $l ];
		}

		$d = $max - $min;
		$s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

		if ( $max === $r ) {
			$h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
		} elseif ( $max === $g ) {
			$h = ( $b - $r ) / $d + 2;
		} else {
			$h = ( $r - $g ) / $d + 4;
		}

		return [ $h / 6, $s, $l ];
	}

	/**
	 * Converts HSL (0..1) to #rrggbb.
	 *
	 * @param float $h Hue.
	 * @param float $s Saturation.
	 * @param float $l Lightness.
	 *
	 * @return string
	 */
	protected static function hsl_to_hex( $h, $s, $l ) {
		$l = max( 0, min( 1, $l ) );

		if ( 0.0 === (float) $s ) {
			$r = $g = $b = $l; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
		} else {
			$q   = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
			$p   = 2 * $l - $q;
			$hue = function ( $t ) use ( $p, $q ) {
				$t = $t < 0 ? $t + 1 : ( $t > 1 ? $t - 1 : $t );

				if ( $t < 1 / 6 ) {
					return $p + ( $q - $p ) * 6 * $t;
				}
				if ( $t < 1 / 2 ) {
					return $q;
				}
				if ( $t < 2 / 3 ) {
					return $p + ( $q - $p ) * ( 2 / 3 - $t ) * 6;
				}

				return $p;
			};
			$r   = $hue( $h + 1 / 3 );
			$g   = $hue( $h );
			$b   = $hue( $h - 1 / 3 );
		}

		return sprintf( '#%02x%02x%02x', round( $r * 255 ), round( $g * 255 ), round( $b * 255 ) );
	}

	/**
	 * The rules file (1.x rules missing from the lean stylesheet).
	 *
	 * @return array
	 */
	protected static function get_rules() {
		if ( null === self::$rules ) {
			$file        = get_theme_file_path( self::RULES_FILE );
			$data        = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.
			self::$rules = is_array( $data ) && isset( $data['index'], $data['rules'] ) ? $data : [
				'known'     => [],
				'media'     => [ '' ],
				'decls'     => [],
				'rules'     => [],
				'index'     => [],
				'keyframes' => [],
			];
		}

		return self::$rules;
	}

	/**
	 * Adds the class names found in a piece of content (HTML, serialized or JSON) to $found.
	 *
	 * @param string $content Content.
	 * @param array  $found   Class name => true.
	 */
	public static function collect_classes( $content, &$found ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return;
		}

		$patterns = [
			'/\bclass\s*=\s*\\\\*["\']([^"\'\\\\]*)/i',     // class="..." in HTML, also JSON or slashed.
			'/"(?:className|_?css_classes)"\s*:\s*"([^"]*)"/', // Block attributes, Elementor CSS classes.
		];

		foreach ( $patterns as $pattern ) {
			if ( preg_match_all( $pattern, $content, $matches ) ) {
				foreach ( $matches[1] as $value ) {
					foreach ( preg_split( '/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY ) as $class ) {
						if ( preg_match( '/^-?[_a-zA-Z][\w-]*$/', $class ) ) {
							$found[ $class ] = true;
						}
					}
				}
			}
		}
	}

	/**
	 * Scans the site for 1.x classes that the lean stylesheet does not have.
	 *
	 * @return string[] Sorted class names.
	 */
	public static function scan_content() {
		global $wpdb;

		$found = [];
		$skip  = implode( ',', array_fill( 0, count( self::SKIP_POST_TYPES ), '%s' ) );
		$last  = 0;

		// Post content (any post type with front-end content), 500 rows at a time.
		do {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- One-time scan; placeholders built above.
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content FROM {$wpdb->posts} WHERE ID > %d AND post_status IN ('publish','private','future','draft','pending') AND post_type NOT IN ($skip) AND post_content LIKE %s ORDER BY ID ASC LIMIT 500", array_merge( [ $last ], self::SKIP_POST_TYPES, [ '%' . $wpdb->esc_like( 'class' ) . '%' ] ) ) );

			foreach ( $rows as $row ) {
				$last = (int) $row->ID;
				self::collect_classes( $row->post_content, $found );
			}
		} while ( 500 === count( $rows ) );

		// Elementor data and menu item classes.
		$last = 0;

		do {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One-time scan.
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_id > %d AND meta_key IN ('_elementor_data','_menu_item_classes') ORDER BY meta_id ASC LIMIT 500", $last ) );

			foreach ( $rows as $row ) {
				$last = (int) $row->meta_id;

				if ( '_menu_item_classes' === $row->meta_key ) {
					foreach ( (array) maybe_unserialize( $row->meta_value ) as $class ) {
						self::collect_classes( 'class="' . $class . '"', $found );
					}
				} else {
					self::collect_classes( $row->meta_value, $found );
				}
			}
		} while ( 500 === count( $rows ) );

		// Widgets (text, HTML, block widgets) and theme mods (footer text, HTML elements).
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- One-time scan.
		$options = $wpdb->get_col( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name = %s", $wpdb->esc_like( 'widget_' ) . '%', 'theme_mods_' . get_stylesheet() ) );

		foreach ( $options as $value ) {
			self::collect_classes( $value, $found );
		}

		// Additional CSS: classes the site styles itself.
		$css = preg_replace( '#/\*.*?\*/|url\([^)]*\)#s', '', (string) wp_get_custom_css() );

		if ( preg_match_all( '/\.(-?[_a-zA-Z][\w-]*)/', $css, $matches ) ) {
			foreach ( $matches[1] as $class ) {
				$found[ $class ] = true;
			}
		}

		$classes = array_keys( array_intersect_key( $found, self::get_rules()['index'] ) );
		sort( $classes );

		return $classes;
	}

	/**
	 * The scan result, cached for ten minutes (the Customizer preview reuses it on every refresh).
	 *
	 * @param bool $fresh Scan again even when a recent result exists.
	 *
	 * @return string[]
	 */
	public static function get_used_classes( $fresh = false ) {
		$state = self::get_state();

		if ( $fresh || ! is_array( $state['classes'] ) || time() - (int) $state['scanned_at'] > 10 * MINUTE_IN_SECONDS ) {
			$state['classes']    = self::scan_content();
			$state['scanned_at'] = time();
			self::save_state( $state );
		}

		return $state['classes'];
	}

	/**
	 * Builds the compatibility CSS for the given 1.x classes: every left-out 1.x rule whose classes are
	 * all either used by the content or present in the lean stylesheet, in the original order.
	 *
	 * @param string[] $classes 1.x classes used by the content.
	 *
	 * @return string
	 */
	public static function build_compat_css( $classes ) {
		$data = self::get_rules();
		$used = array_flip( array_intersect( (array) $classes, array_keys( $data['index'] ) ) );

		if ( ! $used ) {
			return '';
		}

		$known = array_flip( $data['known'] ) + $used;
		$ids   = [];

		foreach ( array_keys( $used ) as $class ) {
			foreach ( $data['index'][ $class ] as $id ) {
				$ids[ $id ] = true;
			}
		}

		ksort( $ids );

		$css        = '';
		$open       = '';
		$animations = [];

		foreach ( array_keys( $ids ) as $id ) {
			list( $media, $selector, $decl ) = $data['rules'][ $id ];

			preg_match_all( '/\.(-?[_a-zA-Z][\w-]*)/', $selector, $matches );

			if ( array_diff_key( array_flip( $matches[1] ), $known ) ) {
				continue;
			}

			$wrap = $data['media'][ $media ];

			if ( $wrap !== $open ) {
				$css .= self::close_wrappers( $open ) . self::open_wrappers( $wrap );
				$open = $wrap;
			}

			$body = $data['decls'][ $decl ];
			$css .= $selector . '{' . $body . '}';

			if ( preg_match_all( '/animation(?:-name)?:\s*([\w-]+)/', $body, $names ) ) {
				$animations += array_flip( $names[1] );
			}
		}

		$css .= self::close_wrappers( $open );

		foreach ( array_keys( $animations ) as $name ) {
			$css .= $data['keyframes'][ $name ] ?? '';
		}

		return $css;
	}

	/**
	 * Opening of nested at-rules stored as "@media x|@supports y".
	 *
	 * @param string $wrap Wrappers.
	 *
	 * @return string
	 */
	protected static function open_wrappers( $wrap ) {
		return '' === $wrap ? '' : implode( '{', explode( '|', $wrap ) ) . '{';
	}

	/**
	 * Closing braces for open_wrappers().
	 *
	 * @param string $wrap Wrappers.
	 *
	 * @return string
	 */
	protected static function close_wrappers( $wrap ) {
		return '' === $wrap ? '' : str_repeat( '}', count( explode( '|', $wrap ) ) );
	}

	/**
	 * Adds the classes of one saved post to the scan result of a switched site and rebuilds the
	 * compatibility CSS when it uses a 1.x class that was not used before.
	 *
	 * @param string $content Post content.
	 */
	public static function add_content( $content ) {
		$state = self::get_state();

		if ( ! $state['offered'] || ! is_array( $state['classes'] ) || self::is_legacy() ) {
			return;
		}

		$found = array_flip( $state['classes'] );
		self::collect_classes( $content, $found );

		$classes = array_keys( array_intersect_key( $found, self::get_rules()['index'] ) );

		if ( count( $classes ) === count( $state['classes'] ) ) {
			return;
		}

		sort( $classes );
		$state['classes'] = $classes;
		self::save_state( $state );
		update_option( self::CSS_OPTION, self::build_compat_css( $classes ), true );
	}
}
