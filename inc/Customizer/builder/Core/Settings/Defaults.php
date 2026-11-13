<?php

namespace Stax\Builder\Core\Settings;

class Defaults implements \ArrayAccess {

	/**
	 * Holds default schema used for migration purposes or smart defaults.
	 *
	 * @var array|mixed|void Schema array.
	 */
	public static $defaults_schema = [];

	/**
	 * Defaults constructor.
	 */
	public function __construct() {
		self::$defaults_schema = apply_filters( 'hfg_settings_schema', [] );
	}

	/**
	 * Check if we have the setting.
	 *
	 * @param mixed $offset Offset key.
	 *
	 * @return bool Is set?
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( self::$defaults_schema[ $offset ] );
	}

	/**
	 * Return setting value.
	 *
	 * @param mixed $offset Offset id.
	 *
	 * @return mixed Value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return self::$defaults_schema[ $offset ];
	}

	/**
	 * Set value.
	 *
	 * @param mixed $offset Default key.
	 * @param mixed $value Default value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		self::$defaults_schema[ $offset ] = $value;
	}

	/**
	 * Remove default from collection.
	 *
	 * @param mixed $offset Offset key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( self::$defaults_schema[ $offset ] );
	}
}
