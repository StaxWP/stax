<?php

namespace Stax\Customizer;

class Dynamic_Css {

	/**
	 * Register actions.
	 */
	public function init() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'print_guntenberg_style' ], 100 );
		add_action( 'wp_enqueue_scripts', [ $this, 'print_builder_style' ], 100 );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'add_customize_vars_tag' ] );

		if ( is_customize_preview() ) {
			add_action( 'wp_head', [ $this, 'add_customize_vars_tag' ] );
		}
	}

	/**
	 * Legacy style
	 *
	 * @return void
	 */
	public function legacy_style() {
		$classes     = apply_filters( 'stax_filter_inline_style_classes', [], 'stax-generated-style' );
		$mobile_css  = '';
		$desktop_css = '';
		$tablet_css  = '';

		foreach ( $classes as $class ) {
			$object = new $class();
			$object->init();
			$mobile_css  .= $object->get_style( 'mobile' );
			$desktop_css .= $object->get_style( 'desktop' );
			$tablet_css  .= $object->get_style( 'tablet' );
		}

		$all_css = $mobile_css;

		if ( ! empty( $tablet_css ) ) {
			$all_css .= sprintf( '@media(min-width: 576px){ %s }', $tablet_css );
		}

		if ( ! empty( $desktop_css ) ) {
			$all_css .= sprintf( '@media(min-width: 960px){ %s }', $desktop_css );
		}

		add_filter(
			'stax_dynamic_style_output',
			function ( $css ) use ( $all_css ) {
				return $all_css . $css;
			}
		);
	}

	/**
	 * Get frontend style.
	 *
	 * @return string
	 */
	public function get_style() {
		$this->legacy_style();

		$generator    = new Frontend();
		$_subscribers = $generator->get();

		$_subscribers = array_merge( $_subscribers, apply_filters( 'stax_style_subscribers', [] ) );

		$generator->set( $_subscribers );

		$style = apply_filters( 'stax_dynamic_style_output', $generator->generate() );

		$style .= $this->get_root_css();

		return self::minify_css( $style );
	}

	/**
	 * Enqueue frontend style
	 */
	public function print_builder_style() {
		wp_add_inline_style( 'stax-header', $this->get_style() );
	}

	public function print_guntenberg_style() {
		wp_add_inline_style( 'stax-root', $this->get_style() );
	}

	/**
	 * Basic CSS minification.
	 *
	 * @param $css
	 *
	 * @return string
	 */
	public static function minify_css( $css ) {
		return preg_replace( '/\s+/', ' ', $css );
	}

	/**
	 * Adds customizer CSS tag for CSS vars.
	 */
	public function add_customize_vars_tag() {
		wp_register_style( 'stax-css-vars', false );
		wp_enqueue_style( 'stax-css-vars' );
		wp_add_inline_style( 'stax-css-vars', self::minify_css( ':root{' . $this->get_css_vars() . '}' ) );
	}

	/**
	 * Get root style (css variables)
	 *
	 * @return string
	 */
	public function get_root_css() {
		return self::minify_css( ':root{' . $this->get_css_vars() . '}' );
	}

	/**
	 * Returns CSS vars style.
	 *
	 * @return string
	 */
	private function get_css_vars() {
		$css = '';

		// Add color palette to css.
		$css .= $this->color_palette();

		// Check for other css vars that need to be added to css.
		foreach ( Config::OPTIONS as $mod_name => $data ) {
			if ( ! isset( $data['type'] ) || $data['type'] !== Config::OPTION_TYPE_VAR ) {
				continue;
			}

			$condition_results = [];
			$add_css           = false;

			// Check if option depends on other option.
			if ( isset( $data['condition'] ) ) {
				foreach ( $data['condition'] as $condition_mod => $condition_value ) {
					$condition_results[] = Mods::get( $condition_mod, Config::OPTIONS[ $condition_mod ]['default'] ) === $condition_value;
				}

				if ( count( array_unique( $condition_results ) ) === 1 ) {
					if ( current( $condition_results ) === true ) {
						$add_css = true;
					}
				}
			} else {
				$add_css = true;
			}

			// Add var to css.
			if ( $add_css ) {
				// For colors only
				$mod_value = Mods::get( $mod_name, $data['default'] );

				if ( isset( $data['input_attrs']['output'] ) && isset( $data['input_attrs']['var'] ) ) {
					if ( $data['input_attrs']['output'] === 'hsl' ) {
						$color = $this->rgb_to_hex( $mod_value );
						$color = $this->hex_to_hsl( $color );

						foreach ( $data['input_attrs']['var'] as $i => $var ) {
							if ( $color[ $i ] ) {
								$css .= '--' . $var . ':' . $color[ $i ] . ';';
							}
						}
					} elseif ( $data['input_attrs']['output'] === 'hex' ) {
						if ( is_array( $data['input_attrs']['var'] ) ) {
							foreach ( $data['input_attrs']['var'] as $var ) {
								if ( $mod_value ) {
									$css .= '--' . $var . ':' . $mod_value . ';';
								}
							}
						} else {
							if ( $mod_value ) {
								$css .= '--' . $data['input_attrs']['var'] . ':' . $mod_value . ';';
							}
						}
					}
				} else {
					$mod_value = Mods::get( $mod_name, $data['default'] );

					$var_value = $mod_value;

					if ( isset( $data['input_attrs']['unit'] ) ) {
						$var_value .= $data['input_attrs']['unit'];
					}

					if ( $var_value ) {
						$css .= '--' . $data['input_attrs']['var'] . ':' . $var_value . ';';
					}
				}
			}
		}

		$general_typography = get_theme_mod( 'stax_typeface_general', \Stax_Assets::instance()->get_body_typography_defaults() );

		foreach ( $general_typography as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['vars'] ) || empty( $item['vars'] ) ) {
				continue;
			}

			if ( is_array( $item['vars'] ) ) {
				if ( is_numeric( $item['mobile'] ) ) {
					$css .= '--' . $item['vars'][0] . ':' . $item['mobile'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['mobile'] ) ) ? $item['suffix']['mobile'] : '' ) . ';';
				}

				if ( is_numeric( $item['tablet'] ) ) {
					$css .= '--' . $item['vars'][1] . ':' . $item['tablet'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['tablet'] ) ) ? $item['suffix']['tablet'] : '' ) . ';';
				}

				if ( is_numeric( $item['desktop'] ) ) {
					$css .= '--' . $item['vars'][2] . ':' . $item['desktop'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['desktop'] ) ) ? $item['suffix']['desktop'] : '' ) . ';';
				}
			} else {
				if ( is_numeric( $item['value'] ) ) {
					$css .= '--' . $item['vars'] . ':' . $item['value'] . ( isset( $item['suffix'] ) ? $item['suffix'] : '' ) . ';';
				}
			}
		}

		foreach ( \Stax_Assets::instance()->get_headings_defaults() as $heading_id => $default_values ) {
			$heading_typography = get_theme_mod( 'stax_' . $heading_id . '_typeface_general', \Stax_Assets::instance()->get_headings_typography_defaults( $heading_id ) );

			foreach ( $heading_typography as $item ) {
				if ( ! is_array( $item ) || ! isset( $item['vars'] ) || empty( $item['vars'] ) ) {
					continue;
				}

				if ( is_array( $item['vars'] ) ) {
					if ( is_numeric( $item['mobile'] ) ) {
						$css .= '--' . $item['vars'][0] . ':' . $item['mobile'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['mobile'] ) ) ? $item['suffix']['mobile'] : '' ) . ';';
					}

					if ( is_numeric( $item['tablet'] ) ) {
						$css .= '--' . $item['vars'][1] . ':' . $item['tablet'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['tablet'] ) ) ? $item['suffix']['tablet'] : '' ) . ';';
					}

					if ( is_numeric( $item['desktop'] ) ) {
						$css .= '--' . $item['vars'][2] . ':' . $item['desktop'] . ( ( isset( $item['suffix'] ) && isset( $item['suffix']['desktop'] ) ) ? $item['suffix']['desktop'] : '' ) . ';';
					}
				} else {
					if ( is_numeric( $item['value'] ) ) {
						$css .= '--' . $item['vars'] . ':' . $item['value'] . ( isset( $item['suffix'] ) ? $item['suffix'] : '' ) . ';';
					}
				}
			}
		}

		$body_font_family = get_theme_mod( 'stax_body_font_family', 'Mukta, sans-serif' );
		$css             .= '--font-family:' . $body_font_family . ';';

		foreach ( explode( ',', $body_font_family ) as $font ) {
			Font_Manager::add_google_font( trim( $font ), '400' );
		}

		$heading_font_family = get_theme_mod( 'stax_headings_font_family', 'Heebo, sans-serif' );
		$css                .= '--heading-font-family:' . $heading_font_family . ';';

		foreach ( explode( ',', $heading_font_family ) as $font ) {
			foreach ( [ '100', '200', '300', '400', '500', '600', '700', '800', '900' ] as $weight ) {
				Font_Manager::add_google_font( trim( $font ), $weight );
			}
		}

		return $css;
	}

	public function color_palette() {
		$css = '';

		$global_colors = get_theme_mod( 'stax_global_colors', \Stax_Assets::instance()->get_global_colors_default() );

		if ( ! empty( $global_colors ) && isset( $global_colors['activePalette'] ) ) {
			$active = $global_colors['activePalette'];

			if ( isset( $global_colors['palettes'][ $active ] ) ) {
				$palette = $global_colors['palettes'][ $active ];

				if ( isset( $palette['colors'] ) ) {
					foreach ( $palette['colors'] as $data ) {
						if ( $data['output'] === 'hsl' ) {
							$hexColor = $this->rgb_to_hex( $data['color'] );
							$color    = $this->hex_to_hsl( $hexColor );

							foreach ( $data['vars'] as $index => $hsl_var ) {
								$css .= '--' . $hsl_var . ':' . $color[ $index ] . ';';
							}

							if ( isset( $data['contrast'] ) && $data['contrast'] ) {
								$css .= '--' . $data['contrast'] . ':' . $this->color_contrast( $hexColor ) . ';';
							}
						} elseif ( $data['output'] === 'hex' ) {
							$css .= '--' . $data['vars'] . ':' . $data['color'] . ';';

							if ( isset( $data['contrast'] ) && $data['contrast'] ) {
								$css .= '--' . $data['contrast'] . ':' . $this->color_contrast( $data['color'] ) . ';';
							}
						}
					}
				}
			}
		}

		return $css;
	}

	/**
	 * Hex to HSL
	 *
	 * @param string $hex
	 * @param string $string
	 *
	 * @return array|string
	 */
	private function hex_to_hsl( $hex, $string = false ) {
		$hex    = str_replace( '#', '', $hex );
		$values = str_split( $hex );

		$hex = [ $values[0] . $values[1], $values[2] . $values[3], $values[4] . $values[5] ];
		$rgb = array_map(
			function( $part ) {
				return hexdec( $part ) / 255;
			},
			$hex
		);

		$max = max( $rgb );
		$min = min( $rgb );

		$l = ( $max + $min ) / 2;

		if ( $max == $min ) {
			$h = $s = 0;
		} else {
			$diff = $max - $min;
			$s    = $l > 0.5 ? $diff / ( 2 - $max - $min ) : $diff / ( $max + $min );

			switch ( $max ) {
				case $rgb[0]:
					$h = ( $rgb[1] - $rgb[2] ) / $diff + ( $rgb[1] < $rgb[2] ? 6 : 0 );
					break;
				case $rgb[1]:
					$h = ( $rgb[2] - $rgb[0] ) / $diff + 2;
					break;
				case $rgb[2]:
					$h = ( $rgb[0] - $rgb[1] ) / $diff + 4;
					break;
			}

			$h /= 6;
		}

		$h = ceil( (float) $h * 360 );
		$s = round( (float) $s * 100 ) . '%';
		$l = round( (float) $l * 100 ) . '%';

		$result = [ $h, $s, $l ];

		if ( $string ) {
			return 'hsl(' . implode( ', ', $result ) . ')';
		}

		return $result;
	}

	/**
	 * Rgb to hex
	 *
	 * @param string $rgba
	 *
	 * @return string
	 */
	private function rgb_to_hex( $rgba ) {
		if ( strpos( $rgba, '#' ) === 0 ) {
			return $rgba;
		}

		preg_match( '/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i', $rgba, $by_color );

		return sprintf( '#%02x%02x%02x', $by_color[1], $by_color[2], $by_color[3] );
	}

	/**
	 * Color contrast
	 *
	 * @param string  $hex
	 * @param boolean $hsl
	 *
	 * @return string
	 */
	private function color_contrast( $hex, $hsl = false ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) === 3 ) {
			$values = str_split( $hex );

			$hex = $values[0] . $values[0] . $values[1] . $values[1] . $values[2] . $values[2];
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		if ( $hsl ) {
			return ( $yiq >= 154 ) ? '0, 0%, 0%' : '0, 0%, 100%';
		} else {
			return ( $yiq >= 154 ) ? '#000000' : '#ffffff';
		}
	}
}
