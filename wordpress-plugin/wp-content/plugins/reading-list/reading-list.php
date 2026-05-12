<?php
/**
 * Plugin Name: Reading List Manager
 * Description: A personal reading list for your WordPress site.
 * Version:     1.0.0
 * Author:      tamuliB0
 * Text Domain: reading-list
 *
 * @package Reading List
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'reading_list_activate' );

/**
 * Runs on plugin activation.
 */
function reading_list_activate() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'reading_list';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL,
	author varchar(255) NOT NULL,
	status varchar(20) NOT NULL DEFAULT 'to-read',
	notes text,
	created_at datetime NOT NULL,
	PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

register_deactivation_hook( __FILE__, 'reading_list_deactivate' );

/**
 * Runs on plugin deactivation.
 */
function reading_list_deactivate() {
}
