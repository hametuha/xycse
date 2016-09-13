<?php
/*
Plugin Name: XYCSE - X belongs to Y as C from S till E
Plugin URI: http://wordpress.org/plugins/xycse/
Description: Next generation relation manager.
Author: hametuha
Version: 1.0.0
Author URI: https://hametuha.co.jp/
Text Domain: xycse
Domain Path: /languages/
License: GPL v2 or later
*/

defined( 'ABSPATH' ) or die( 'Do not load directly.' );

load_plugin_textdomain( 'xycse', false, 'xycse/languages' );

// Start
if ( version_compare( phpversion(), '5.4.*', '<' ) ) {
	add_action( 'admin_notices', '_xycse_admin_notice' );
} else {
	// Load all includes.
	foreach ( scandir( __DIR__.'/includes/' ) as $file ) {
		if ( preg_match( '#^[^.].*\.php#u', $file ) ) {
			require __DIR__.'/includes/'.$file;
		}
	}
	// Load autoload.php
	if ( ! file_exists( __DIR__.'/vendor/autoload.php' ) ) {
		add_action( 'admin_notices', '_xycse_should_composer' );
	} else {
		require __DIR__.'/vendor/autoload.php';
		// Initialize.
		xycse_init();
	}
}

/**
 * Show error message for PHP version.
 *
 * @ignore
 */
function _xycse_admin_notice() {
	printf(
		'<div class="error"><p><strong>[XYCSE]</strong> %s</p></div>',
		sprintf( esc_html__( 'This plugin requires PHP 5.4 and over. You PHP is %s.', 'sg' ), phpversion() )
	);
}

/**
 * Show error message for Composer
 *
 * @ignore
 */
function _xycse_should_composer() {
	printf(
		'<div class="error"><p><strong>[XYCSE]</strong> %s</p></div>',
		esc_html__( 'Auto loader is missing. You should run composer install.', 'sg' )
	);
}
