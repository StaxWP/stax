<?php

namespace Stax\Customizer;

class Mods {

	/**
	 * Cached values.
	 *
	 * @var array Values cached.
	 */
	private static $_cached = [];
	/**
	 * No cache mode.
	 *
	 * @var bool Should we avoid cache.
	 */
	public static $no_cache = false;

	/**
	 * Get theme mod.
	 *
	 * @param string $key Key value.
	 * @param string $default Default value.
	 *
	 * @return mixed Mod value.
	 */
	public static function get( $key, $default = false ) {
		$master_default = $default;
		$subkey         = null;

		if ( strpos( $key, '.' ) !== false ) {
			$key_parts      = explode( '.', $key );
			$key            = $key_parts[0];
			$subkey         = $key_parts[1];
			$master_default = false;
		}

		if ( ! isset( self::$_cached[ $key ] ) || self::$no_cache ) {
			self::$_cached[ $key ] =
				( $master_default === false ) ?
					get_theme_mod( $key ) :
					get_theme_mod( $key, $master_default );
		}

		if ( $subkey === null ) {
			return self::$_cached[ $key ];
		}

		$value = is_string( self::$_cached[ $key ] ) ? json_decode( self::$_cached[ $key ], true ) : self::$_cached[ $key ];

		return isset( $value[ $subkey ] ) ? $value[ $subkey ] : $default;
	}

	/**
	 * Setter for the manager.
	 *
	 * @param string $key Key.
	 * @param mixed  $value Value.
	 */
	public static function set( $key, $value ) {
		self::$_cached[ $key ] = $value;
	}

	/**
	 * Get and transform setting to json.
	 *
	 * @param string $key Key name.
	 * @param string $default Default value.
	 * @param bool   $as_array As array or Object.
	 *
	 * @return mixed
	 */
	public static function to_json( $key, $default = false, $as_array = true ) {
		return json_decode( self::get( $key, $default ), $as_array );
	}

}
