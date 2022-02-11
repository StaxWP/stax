<?php

namespace Stax\Customizer\Core;

class Sanitizer {

	protected static $_instance = null;

	/**
	 * Instance
	 *
	 * @return null|Sanitizer
	 */
	public static function instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Function to sanitize alpha color.
	 *
	 * @param string $value Hex or RGBA color.
	 *
	 * @return string
	 */
	public function sanitize_colors( $value ) {
		$is_var = ( strpos( $value, 'var' ) !== false );

		if ( $is_var ) {
			return sanitize_text_field( $value );
		}

		// Is this an rgba color or a hex?
		$mode = ( false === strpos( $value, 'rgba' ) ) ? 'hex' : 'rgba';

		if ( 'rgba' === $mode ) {
			return $this->sanitize_rgba( $value );
		} else {
			return sanitize_hex_color( $value );
		}
	}

	/**
	 * Sanitize rgba color.
	 *
	 * @param string $value Color in rgba format.
	 *
	 * @return string
	 */
	public function sanitize_rgba( $value ) {
		$red   = 'rgba(0,0,0,0)';
		$green = 'rgba(0,0,0,0)';
		$blue  = 'rgba(0,0,0,0)';
		$alpha = 'rgba(0,0,0,0)';   // If empty or an array return transparent
		if ( empty( $value ) || is_array( $value ) ) {
			return '';
		}

		// By now we know the string is formatted as an rgba color so we need to further sanitize it.
		$value = str_replace( ' ', '', $value );
		sscanf( $value, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}

	/**
	 * Sanitize checkbox output.
	 *
	 * @param bool $value value to be sanitized.
	 *
	 * @return string
	 */
	public function sanitize_checkbox( $value ) {
		return true === (bool) $value;
	}

	/**
	 * Check if a string is in json format
	 *
	 * @param string $string Input.
	 *
	 * @return bool
	 * @since 1.1.38
	 */
	public function is_json( $string ) {
		return is_string( $string ) && is_array( json_decode( $string, true ) );
	}

	/**
	 * Sanitize values for range inputs.
	 *
	 * @param string $input Control input.
	 *
	 * @return float
	 */
	public function sanitize_range_value( $input ) {
		if ( ! $this->is_json( $input ) ) {
			return floatval( $input );
		}

		$range_value            = json_decode( $input, true );
		$range_value['desktop'] = isset( $range_value['desktop'] ) && is_numeric( $range_value['desktop'] ) ? floatval( $range_value['desktop'] ) : '';
		$range_value['tablet']  = isset( $range_value['tablet'] ) && is_numeric( $range_value['tablet'] ) ? floatval( $range_value['tablet'] ) : '';
		$range_value['mobile']  = isset( $range_value['mobile'] ) && is_numeric( $range_value['mobile'] ) ? floatval( $range_value['mobile'] ) : '';

		return wp_json_encode( $range_value );
	}

	/**
	 * Sanitize font weight values.
	 *
	 * @param string $value font-weight value.
	 *
	 * @return string
	 */
	public function sanitize_font_weight( $value ) {
		$allowed = [ '100', '200', '300', '400', '500', '600', '700', '800', '900' ];

		if ( ! in_array( (string) $value, $allowed, true ) ) {
			return '300';
		}

		return $value;
	}

	/**
	 * Sanitize font weight values.
	 *
	 * @param string $value font-weight value.
	 *
	 * @return string
	 */
	public function sanitize_text_transform( $value ) {
		$allowed = [ 'none', 'capitalize', 'uppercase', 'lowercase' ];

		if ( ! in_array( $value, $allowed, true ) ) {
			return 'none';
		}

		return $value;
	}

	/**
	 * Sanitize the background control.
	 *
	 * @param array $value input value.
	 *
	 * @return WP_Error | array
	 */
	public function sanitize_background( $value ) {
		if ( ! is_array( $value ) ) {
			return new \WP_Error();
		}

		if ( ! isset( $value['type'] ) || ! in_array( $value['type'], [ 'image', 'color' ], true ) ) {
			return new \WP_Error();
		}

		return $value;
	}

	/**
	 * Sanitize the button appearance control.
	 *
	 * @param array $value the control value.
	 *
	 * @return array
	 */
	public function sanitize_button_appearance( $value ) {
		return $value;
	}

	/**
	 * Sanitize the typography control.
	 *
	 * @param array $value the control value.
	 *
	 * @return array
	 */
	public function sanitize_typography_control( $value ) {
		$keys = [
			'lineHeight',
			'letterSpacing',
			'fontWeight',
			'fontSize',
			'textTransform',
		];

		// Approve Keys.
		foreach ( $value as $key => $values ) {
			if ( ! in_array( $key, $keys, true ) ) {
				unset( $value[ $key ] );
			}
		}

		// Font Weight.
		if ( ! in_array( $value['fontWeight'], [ '100', '200', '300', '400', '500', '600', '700', '800', '900' ], true ) ) {
			$value['fontWeight'] = '300';
		}
		// Text Transform.
		if ( ! in_array( $value['textTransform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ) {
			$value['textTransform'] = 'none';
		}

		// Make sure we deal with arrays.
		foreach ( [ 'letterSpacing', 'lineHeight', 'fontSize' ] as $value_type ) {
			if ( ! is_array( $value[ $value_type ] ) ) {
				$value[ $value_type ] = [];
			}
		}

		return $value;
	}

	/**
	 * Sanitize alignment.
	 *
	 * @param array $input alignment responsive array.
	 *
	 * @return array
	 */
	public function sanitize_alignment( $input ) {
		$default = [
			'mobile'  => 'left',
			'tablet'  => 'left',
			'desktop' => 'left',
		];
		$allowed = [ 'left', 'center', 'right', 'justify' ];

		if ( ! is_array( $input ) ) {
			return $default;
		}

		foreach ( $input as $device => $alignment ) {
			if ( ! in_array( $alignment, $allowed ) ) {
				$input[ $device ] = 'left';
			}
		}

		return $input;
	}

	/**
	 * Sanitize font variants.
	 *
	 * @param string[] $value the incoming value.
	 *
	 * @return string[]
	 */
	public function sanitize_font_variants( $value ) {
		$allowed = [
			'100',
			'200',
			'300',
			'400',
			'500',
			'600',
			'700',
			'800',
			'900',
			'100italic',
			'200italic',
			'300italic',
			'400italic',
			'500italic',
			'600italic',
			'700italic',
			'800italic',
			'900italic',
		];
		if ( ! is_array( $value ) ) {
			return [];
		}

		foreach ( $value as $variant ) {
			if ( in_array( $variant, $allowed, true ) ) {
				continue;
			}
			$key = array_search( $variant, $value );
			unset( $value[ $key ] );
		}

		return $value;
	}

}
