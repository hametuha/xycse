<?php

/**
 * Register assets.
 */
add_action( 'init', function(){
	$base = plugin_dir_url( __DIR__ ).'assets';
	// JS Libs
	wp_register_script( 'angular', "{$base}/js/angular.min.js", [], '1.5.8', true );
	wp_register_script( 'angular-ui-sortable', "{$base}/js/sortable.min.js", [ 'angular', 'jquery-ui-sortable' ], '0.14.3', true );
	// JS local
	wp_register_script( 'xycse-setting', "{$base}/js/option.js", [ 'angular-ui-sortable' ], '1.0.0', true );
	// CSS
	wp_register_style( 'xycse-admin', "{$base}/css/admin.css", [], '1.0.0' );

} );


/**
 * Load assets
 */
add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_style( 'xycse-admin' );
} );
