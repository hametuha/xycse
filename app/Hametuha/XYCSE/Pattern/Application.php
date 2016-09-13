<?php

namespace Hametuha\XYCSE\Pattern;


use Hametuha\XYCSE\Utility\Input;

/**
 * Application base
 *
 * @package Hametuha\XYCSE\Pattern
 * @property-read Input $input
 */
abstract class Application extends Singleton {

	/**
	 * Getter
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'input':
				return Input::instance();
				break;
			default:
				return null;
				break;
		}
	}

}
