<?php
/**
 * Hooks and functions list for DB migration.
 *
 * This file manage DB migration.
 *
 * @package Hamtuha\XYCSE
 */


/**
 * DB version
 *
 * @return string
 */
function xycse_db_version() {
	return '1.0.0';
}

/**
 * Detect if DB is old.
 *
 * @return bool
 */
function xycse_db_old() {
	return version_compare( get_option( 'xycse_db', '0' ), xycse_db_version(), '<' );
}

/**
 * Return table name
 *
 * @return string
 */
function xycse_table_name() {
	global $wpdb;
	return "{$wpdb->prefix}xycse_relationships";
}

/**
 * Update DB
 */
function xycse_db_update() {
	global $wpdb;
	$charset = $wpdb->charset;
	$table = xycse_table_name();
	$query = <<<SQL
		CREATE TABLE `{$table}` (
			`ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`type` VARCHAR(24) NOT NULL,
			`subject_id` BIGINT UNSIGNED NOT NULL,
			`object_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
			`term_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
			`from` DATETIME NOT NULL,
			`till` DATETIME NOT NULL,
			INDEX type_subject ( `type`, `subject_id` ),
			INDEX type_object ( `type`, `object_id` ),
			INDEX type_date ( `type`, `from`, `till` ) 
		) ENGINE=InnoDB, CHARACTER SET = {$charset}
SQL;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $query );
	update_option( 'xycse_db', xycse_db_version(), false );
}

/**
 * Update db automatically
 */
add_action( 'admin_init', function(){
	if ( current_user_can( 'manage_options' ) && ! defined( 'DOING_AJAX' ) && xycse_db_old() ) {
		xycse_db_update();
		add_action( 'admin_notices', function(){
			printf( '<div class="updated"><p><strong>[XYCSE]</strong> %s</p></div>', sprintf( __( 'Database updated to %s', 'xycse' ), xycse_db_version() ) );
		} );
	}
} );

