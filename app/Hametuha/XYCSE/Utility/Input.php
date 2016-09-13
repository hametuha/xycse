<?php

namespace Hametuha\XYCSE\Utility;

use Hametuha\XYCSE\Pattern\Singleton;

/**
 * Input helper
 *
 * @package Hametuha\XYCSE\Utility
 */
class Input extends Singleton {

	/**
	 * Short hand for $_GET
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		return isset( $_GET[ $key ] ) ? $_GET[ $key ] : null;
	}

	/**
	 * Short hand for $_POST
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function post( $key ) {
		return isset( $_POST[ $key ] ) ? $_POST[ $key ] : null;
	}

	/**
	 * Short hand for $_REQUEST
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function request( $key ) {
		return isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;
	}

	/**
	 * Get requested post body.
	 *
	 * @param bool $as_json
	 *
	 * @return string|array|null
	 */
	public function post_body( $as_json = false ) {
		$request = file_get_contents( 'php://input' );
		if ( $as_json ) {
			$request = json_decode( $request, true );
		}
		return $request;
	}

	/**
	 * Verify nonce
	 *
	 * @param string $action
	 * @param string $key
	 *
	 * @return bool
	 */
	public function verify_nonce ( $action, $key = '_wpnonce' ) {
		return (bool) wp_verify_nonce( $this->request( $key ), $action );
	}
}
