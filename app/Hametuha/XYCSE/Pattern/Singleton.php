<?php

namespace Hametuha\XYCSE\Pattern;

/**
 * Singleton
 *
 * @package Hametuha\XYCSE\Pattern
 */
abstract class Singleton {

	private static $instances = [];

	/**
	 * Singleton constructor.
	 *
	 * @param array $arguments
	 */
	final protected function __construct( array $arguments = [] ) {
		$this->on_construct( $arguments );
	}

	/**
	 * Executed on constructor
	 *
	 * @param array $arguments
	 */
	protected function on_construct( array $arguments = [] ) {
		// Override this if required.
	}

	/**
	 * Get instance
	 *
	 * @param array $arguments
	 * @return static
	 */
	public static function instance( array $arguments = [] ) {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name( $arguments );
		}
		return self::$instances[ $class_name ];
	}
}
