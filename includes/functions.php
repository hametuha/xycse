<?php

/**
 * Bootstrap
 */
function xycse_init() {
	static $init = false;
	if ( ! $init ) {
		// Bootstrap actions.
		\Hametuha\XYCSE\Option::instance();

		$init = true;
	}
}
